<?php

function get_s3()
{
    global $CONFIG;
    return new S3($CONFIG->s3_key, $CONFIG->s3_private);
}

function get_uploaded_filename($input_name)
{
    if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0)
    {
        return $_FILES[$input_name]['tmp_name'];
    }
    return false;
}

function get_thumbnail_src($html)
{
    if (preg_match('/src="http:\/\/(\w+)\.s3\.amazonaws\.com\/(\d+)\/([\w\.]+)\//', $html, $matches))
    {
        $ownerGuid = $matches[2];
        $groupName = $matches[3];

        $files = ElggFile::query()->where('owner_guid = ?', $ownerGuid)->
                    where('group_name = ?', $groupName)->
                    where('size=?', 'small')->filter();

        if (sizeof($files) > 0)
        {
            return $files[0]->getURL();
        }
    }
    return null;
}

function get_file_from_url($url)
{
    if (preg_match('/\/(\d+)\/([\w\.]+)\/([^\/]+)$/', $url, $matches))
    {
        $ownerGuid = $matches[1];
        $groupName = $matches[2];
        $fileName = $matches[3];

        $files = ElggFile::query()->where('owner_guid = ?', $ownerGuid)->
            where('group_name = ?', $groupName)->where('filename = ?', $fileName)->filter();
       
        if (sizeof($files) > 0)
        {
            return $files[0];
        }
    }
    return null;
}

function upload_temp_images($filename, $sizes)
{
    $files = array();
    $groupName = uniqid("",true);
    $lastFile = null;

    foreach($sizes as $sizeName => $size)
    {
        $sizeArray = explode("x", $size);

        $resizedImage = resize_image_file($filename, $sizeArray[0], $sizeArray[1]);

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
                $file = new ElggFile();
                $file->owner_guid = get_loggedin_userid();
                $file->group_name = $groupName;
                $file->size = $sizeName;
                $file->width = $resizedImage['width'];
                $file->height = $resizedImage['height'];
                $file->mime = $resizedImage['mime'];
                $file->filename = "$sizeName.jpg";
                $file->uploadFile($resizedImage['filename'], $resizedImage['mime']);
                $file->save();
                $lastFile = $file;

                $files[] = $file;
            }
        }
        else
        {
            return json_encode(null);
        }
    }
    return get_file_group_json($files);
}

function get_file_group_json($files)
{
    $res = array();

    foreach ($files as $file)
    {
        $res[$file->size] = $file->jsProperties();
    }

    return json_encode($res);
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
        if ($file instanceof ElggFile)
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
