<?php

function get_storage()
{
    global $CONFIG;
	$storage_backend = $CONFIG->storage_backend;
	return new $storage_backend();
}

function get_thumbnail_src($html)
{
    if (preg_match('/src=".*[\/\=](\d+)\/([\w\.]+)\//', $html, $matches))
    {
        $ownerGuid = $matches[1];
        $groupName = $matches[2];
		
        $files = UploadedFile::query()->where('owner_guid = ?', $ownerGuid)->
                    where('group_name = ?', $groupName)->
                    where('size=?', 'small')->filter();

        if (sizeof($files) > 0)
        {
            return $files[0]->get_url();
        }
    }
    return null;
}

function get_file_from_url($url)
{
    if (preg_match('/(\d+)\/([\w\.]+)\/([^\/]+)$/', $url, $matches))
    {
        $ownerGuid = $matches[1];
        $groupName = $matches[2];
        $fileName = $matches[3];

        $files = UploadedFile::query()->where('owner_guid = ?', $ownerGuid)->
            where('group_name = ?', $groupName)->where('filename = ?', $fileName)->filter();
       
        if (sizeof($files) > 0)
        {
            return $files[0];
        }
    }
    return null;
}

function upload_file($file_input)
{
    if (!$file_input || $file_input['error'] != 0)
        return null;
        
    $tmp_file = $file_input['tmp_name'];

    $file = new UploadedFile();
    $file->owner_guid = Session::get_loggedin_userid();
    $file->group_name = uniqid("",true);
    $file->size = 'original';
    $file->mime = UploadedFile::get_mime_type($file_input['name']);
    $file->filename = preg_replace('/[^\w\.\-]|(\.\.)/', '_', basename($file_input['name']));
    $file->upload_file($tmp_file);
    $file->save();
    
    return get_file_group_array(array($file));
}

function convert_to_pdf($filename, $extension)
{
   require_once("vendors/scribd.php");

   global $CONFIG;
   $scribd = new Scribd($CONFIG->scribd_key, $CONFIG->scribd_private);

   $res = $scribd->upload($filename, $extension, 'private');
   
   $doc_id = @$res['doc_id'];
   if (!$doc_id)
   {
      error_log($res);
      return null;
   }

   for ($i = 0; $i < 15; $i++)
   {
      $status = $scribd->getConversionStatus($doc_id);
      if ($status == 'PROCESSING' || $status == 'DISPLAYABLE') 
      {
         sleep(1);
      }
      else
      {
         break;
      }
  }
  try 
  {
     $pdf_url = $scribd->getDownloadUrl($doc_id, 'pdf');
     return $pdf_url;
  }
  catch (Exception $ex)
  {
     error_log("error in pdf conversion: ".$ex->getMessage());
     return null;
  }
}

function download_file($url, $filename)
{
  $fh = fopen($filename, 'w');
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_FILE, $fh);
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
  $res = curl_exec($curl);
  if (!$res)
  {
    error_log(curl_error($curl));
  }
  curl_close($curl);

  fclose($fh);
  return $res;
}

function create_temp_dir($prefix)
{
   $temp_dir = sys_get_temp_dir();
   for ($i = 0; $i < 10; $i++)
   {
      $output_dir = "$temp_dir/$prefix".mt_rand(0,10000000);
      if (mkdir($output_dir, 0700))
      {
         return $output_dir;
      }
   }
   return false;
}

function extract_image_from_pdf($filename)
{
    $temp_dir = create_temp_dir("pdfimages");

    // pdfimages extracts images from .pdf file
    // in either .jpg or .ppm format
    system("pdfimages -j $filename $temp_dir/img"); 

    $ppm_file = null;
    $jpg_file = null;
    $res_file = null;

   if ($handle = opendir($temp_dir))
   {
      while($file = readdir($handle))
      {
         $path = "$temp_dir/$file";
         if (endswith($file, '.jpg'))
         {
            $jpg_file = $path;
            break; // only need 1 image
         }
         else if (endswith($file, '.ppm'))
         {
            $ppm_file = $path;
            break; 
         } 
      }
      closedir($handle);
   }
  
    // php gd doesn't understand .ppm files, so convert them to .jpg
    if ($ppm_file)
    {
        $jpg_file = "$ppm_file.jpg";
        system("pnmtojpeg $ppm_file > $jpg_file");
    }

    // clean up
    if ($jpg_file)
    {
       // move result file out of temporary directory
       // so we can delete the temporary directory
       for ($i = 0; $i < 10; $i++)
       {
           $res_file = sys_get_temp_dir(). '/pdfimages_jpg' . mt_rand(0,100000000);
           if (rename($jpg_file, $res_file)) break;
       }
    }

    system("rm -rf \"$temp_dir\"");

    return $res_file;
}

function get_image_document_extensions()
{
   // file types that we can extract images from, and accept as image uploads
   return array('pdf', 'rtf', 'odt', 'odg', 'doc', 'ppt', 'docx', 'pptx'); 
}

function store_image_from_doc($tmp_file, $ext, $sizes)
{
    $res = null;
    if ($ext != 'pdf')
    {
        $pdf_url = convert_to_pdf($tmp_file, $ext);
        $pdf_filename = tempnam(sys_get_temp_dir(), 'pdfimages_pdf');
        if (!download_file($pdf_url, $pdf_filename))
        {
            $pdf_filename = null;
        }
    } 
    else
    {
        $pdf_filename = $tmp_file;
        $pdf_url = null;
    }

    if ($pdf_filename)
    {
        $pdf_image = extract_image_from_pdf($pdf_filename);
        if ($pdf_image)
        {
            $res = store_image($pdf_image, $sizes);
            @unlink($pdf_image); 
        }
        if ($pdf_url)
        {
            @unlink($pdf_filename);
        }
    }

    return $res;
}

function upload_image($file_input, $sizes)
{
    if (!$file_input || $file_input['error'] != 0)
        return null;

    $tmp_file = $file_input['tmp_name'];        

    global $CONFIG;

    $orig_name = $file_input['name'];
    $pathinfo = pathinfo($orig_name);
    $ext = strtolower($pathinfo['extension']);
    if (in_array($ext, get_image_document_extensions()))
    {
        if ($CONFIG->extract_images_from_docs)
        {
            return store_image_from_doc($tmp_file, $ext, $sizes);
        }
        else
        {
            return null;
        }
    } 
    else
    {
        return store_image($tmp_file, $sizes);
    }
}

function store_image($tmp_file, $sizes)
{
    $files = array();
    $groupName = uniqid("",true);
    $lastFile = null;

    foreach($sizes as $sizeName => $size)
    {
        $sizeArray = explode("x", $size);

        $resizedImage = resize_image_file($tmp_file, $sizeArray[0], $sizeArray[1]);

        if ($resizedImage)
        {
            if ($lastFile != null &&
                $resizedImage['width'] == $lastFile->width &&
                $resizedImage['height'] == $lastFile->height)
            {
                // skip
            }
            else
            {
                $file = new UploadedFile();
                $file->owner_guid = Session::get_loggedin_userid();
                $file->group_name = $groupName;
                $file->size = $sizeName;
                $file->width = $resizedImage['width'];
                $file->height = $resizedImage['height'];
                $file->mime = $resizedImage['mime'];
                $file->filename = "$sizeName.jpg";
                $file->upload_file($resizedImage['filename']);
                $file->save();
                $lastFile = $file;

                $files[] = $file;
            }

            if ($resizedImage['filename'] != $tmp_file)
            {
                @unlink($resizedImage['filename']);
            }
        }
        else
        {
            return null;
        }
    }
    return get_file_group_array($files);
}

function get_file_group_array($files)
{
    $res = array();

    foreach ($files as $file)
    {
        $res[$file->size] = $file->js_properties();
    }

    return $res;
}

function get_uploaded_files($json)
{
    $filedata = json_decode($json, true);

    if (!$filedata)
    {
        return null;
    }

    foreach ($filedata as $size => $value)
    {
        $file = get_entity($value['guid']);
        if ($file instanceof UploadedFile)
        {
            $filedata[$size]['file'] = $file;
        }
    }
    return $filedata;
}

/**
 * Gets the resized version of an already uploaded image
 * (Returns false if the uploaded file was not an image)
 *
 * @param string $imageFileName Filename of the original image
 * @param int $maxwidth The maximum width of the resized image
 * @param int $maxheight The maximum height of the resized image
 * @param true|false $square If set to true, will take the smallest of maxwidth and maxheight and use it to set the dimensions on all size; the image will be cropped.
 * @return false|mixed array with keys 'filename','width','height','mime' or false on failure
 */
function resize_image_file($imageFileName, $maxwidth, $maxheight, $square = false, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0)
{
    if ($imgsizearray = getimagesize($imageFileName))
    {
        $width = $imgsizearray[0];
        $height = $imgsizearray[1];
        $mime = $imgsizearray['mime'];
        $newwidth = $width;
        $newheight = $height;

        if ($square)
        {
            if ($width < $height)
            {
                $height = $width;
            }
            else
            {
                $width = $height;
            }

            $newwidth = $width;
            $newheight = $height;
        }

        if ($width > $maxwidth)
        {
            $newheight = floor($height * ($maxwidth / $width));
            $newwidth = $maxwidth;
        }
        if ($newheight > $maxheight)
        {
            $newwidth = floor($newwidth * ($maxheight / $newheight));
            $newheight = $maxheight;
        }

        $accepted_formats = array(
            'image/jpeg' => 'jpeg',
            'image/png' => 'png',
            'image/gif' => 'gif'
        );

        if ($width == $newwidth && $height == $newheight)
        {
            // avoid re-encoding file if it's already the correct size
            return array(
                'filename' => $imageFileName,
                'width' => $width,
                'height' => $height,
                'mime' => $mime
            );
        }

        // If it's a file we can manipulate ...
        if (array_key_exists($mime,$accepted_formats))
        {
            $ext = $accepted_formats[$mime];

            $function = "imagecreatefrom$ext";
            $newimage = imagecreatetruecolor($newwidth,$newheight);

            if (is_callable($function) && $oldimage = $function($imageFileName)) {

                // Crop the image if we need a square
                if ($square) {
                    if ($x1 == 0 && $y1 == 0 && $x2 == 0 && $y2 ==0) {
                        $widthoffset = floor(($imgsizearray[0] - $width) / 2);
                        $heightoffset = floor(($imgsizearray[1] - $height) / 2);
                    } else {
                        $widthoffset = $x1;
                        $heightoffset = $y1;
                        $width = ($x2 - $x1);
                        $height = $width;
                    }
                } else {
                    if ($x1 == 0 && $y1 == 0 && $x2 == 0 && $y2 ==0) {
                        $widthoffset = 0;
                        $heightoffset = 0;
                    } else {
                        $widthoffset = $x1;
                        $heightoffset = $y1;
                        $width = ($x2 - $x1);
                        $height = ($y2 - $y1);
                    }
                }
                if ($square) {
                    $newheight = $maxheight;
                    $newwidth = $maxwidth;
                }
                imagecopyresampled($newimage, $oldimage, 0,0,$widthoffset,$heightoffset,$newwidth,$newheight,$width,$height);

                $tempFileName = tempnam(sys_get_temp_dir(), 'img');

                /* make output image same type as input */
                switch ($ext)
                {
                    case 'gif':
                        $res = imagegif($newimage, $tempFileName);
                        break;
                    case 'png':
                        $res = imagepng($newimage, $tempFileName);
                        break;
                    default:
                        $res = imagejpeg($newimage, $tempFileName, 90);
                        break;
                }

                if ($res)
                {
                    return array(
                        'filename' => $tempFileName,
                        'width' => $newwidth,
                        'height' => $newheight,
                        'mime' => $mime
                    );
                }
            }

        }

    }

    return false;
}
