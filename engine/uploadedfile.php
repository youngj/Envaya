<?php

/*
 * Represents one version of a user-uploaded file.
 * Provides methods for handling file uploads and saving them
 * to a data store.
 *
 * Each uploaded file may have different versions saved
 * (e.g. different image sizes, different formats, different
 * data stores). These correspond to multiple UploadedFile
 * objects which each share the same group_name.
 */
class UploadedFile extends Entity
{
    static $table_name = 'files_entity';
    static $table_attributes = array(
        'group_name' => '',
        'filename' => '',
        'storage' => '',
        'size' => '', 
            /* sizes:
                large (image)
                medium (image)
                small (image)
                tiny (image)
                original
                
              NOTE: doesn't refer to file size or pixel dimensions
              there may be several different versions of file with same group_name
            */
        'mime' => '',

        // only used for images
        'width' => null,
        'height' => null,        
    );

    // file types that we can extract images from, and accept as image uploads
    static $image_document_extensions = array('pdf', 'rtf', 'odt', 'odg', 'doc', 'ppt', 'docx', 'pptx'); 
    static $scribd_document_extensions = array('doc', 'docx', 'pdf', 'txt', 'rtf', 'ppt', 'pptx', 
                                                'xls', 'xlsx', 'pps', 'ppsx', 'odt', 'odg', 'odp');

    public function get_files_in_group()
    {
        return UploadedFile::query()->where('owner_guid = ?', $this->owner_guid)->
            where('group_name = ?', $this->group_name)->filter();            
    }

    public function js_properties()
    {
        $props = array(
            'guid' => $this->guid,
            'size' => $this->size,
            'storage' => $this->storage,
            'group_name' => $this->group_name,
            'filename' => $this->filename,
            'mime' => $this->mime,
            'width' => $this->width,
            'height' => $this->height,
            'url' => $this->get_url(),
        );
        
        if ($this->storage == 'scribd')
        {
            $props['docid'] = $this->docid;
            $props['accesskey'] = $this->accesskey;
        }
        
        return $props;
    }

    public function get_storage_key()
    {
        switch ($this->storage)
        {
            case 'scribd':
                return array('docid' => $this->docid, 'accesskey' => $this->accesskey);
            default:
                if ($this->group_name)
                {
                    return "{$this->owner_guid}/{$this->group_name}/{$this->filename}";
                }
                else
                {
                    return "{$this->owner_guid}/{$this->filename}";
                }
        }
    }
    
    public function get_storage()
    {
        if ($this->storage)
        {
            $storage_class = "Storage_{$this->storage}";
            return new $storage_class();
        }
        else
        {
            return get_storage();
        }
    }

    public function get_url()
    {
        return $this->get_storage()->get_url($this->get_storage_key());        
    }

    public function delete()
    {
        $res = $this->get_storage()->delete_object($this->get_storage_key());

        if ($res && $this->guid)
        {
            return parent::delete();
        }
        else
        {
            return $res;
        }
    }

    public function size()
    {
        $info = $this->get_storage()->get_object_info($this->get_storage_key());
        if ($info)
        {
            return $info['Content-Length'];
        }
        return -1;
    }

    public function upload_file($filePath)
    {
        return $this->get_storage()->upload_file($this->get_storage_key(), $filePath, true, $this->mime);
    }

    public function copy_to($destFile)
    {
        return $this->get_storage()->copy_object($this->get_storage_key(), $destFile->get_storage_key(), true);
    }

    public function exists()
    {
        $info = $this->get_storage()->get_object_info($this->get_storage_key());
        return ($info) ? true : false;
    }

    private static function get_extension($filename)
    {
        $pathinfo = pathinfo($filename);
        return strtolower($pathinfo['extension']);
    }
	
    static function get_mime_type($filename)
    {
        return @static::$mime_types[static::get_extension($filename)];
    }
    
    static function get_thumbnail_url_from_html($html)
    {
        if (preg_match('/src=".*[\/\=](\d+)\/([\w\.]+)\//', $html, $matches))
        {
            $ownerGuid = $matches[1];
            $groupName = $matches[2];
            
            $file = UploadedFile::query()->where('owner_guid = ?', $ownerGuid)->
                        where('group_name = ?', $groupName)->
                        where('size=?', 'small')->get();

            if ($file)
            {
                return $file->get_url();
            }
        }
        return null;
    }    
    
    static function get_from_url($url)
    {
        if (preg_match('/(\d+)\/([\w\.]+)\/([^\/]+)$/', $url, $matches))
        {
            $ownerGuid = $matches[1];
            $groupName = $matches[2];
            $fileName = $matches[3];

            return UploadedFile::query()->where('owner_guid = ?', $ownerGuid)->
                where('group_name = ?', $groupName)->where('filename = ?', $fileName)->get();
        }
        return null;
    }
    
    /* 
     * Gets a javascript encoding for an array of UploadedFile objects
     * in the format required by the image upload javascript.
     */
    static function json_encode_array($files)
    {
        if (!is_array($files))
        {
            $files = array($files);
        }
    
        $res = array();

        foreach ($files as $file)
        {
            $res[] = $file->js_properties();
        }

        return json_encode($res);
    }
    
    /* 
     * Gets a javascript encoding for an array of UploadedFile objects
     * in the format required by the image upload javascript.
     */    
    static function json_decode_array($json)
    {
        $values = json_decode($json, true);
        if (!$values)
        {
            return null;
        }

        $files = array();        
        foreach ($values as $value)
        {
            $file = get_entity($value['guid']);
            if ($file instanceof UploadedFile)
            {
                $files[] = $file;
            }
        }        
        return $files;
    }
    
    private static function new_from_file_input($file_input)
    {
        $file = new UploadedFile();
        $file->owner_guid = Session::get_loggedin_userid();
        $file->group_name = uniqid("",true);
        $file->mime = UploadedFile::get_mime_type($file_input['name']);        
        $file->filename = static::sanitize_file_name(basename($file_input['name']));                    
        $file->size = 'original';
        return $file;
    }
    
    static function sanitize_file_name($file_name)
    {
        return preg_replace('/[^\w\.\-]|(\.\.)/', '_', $file_name); 
    }
            
    static function upload_scribd_from_input($file_input)
    {
        $filename = $file_input['name'];
        $extension = static::get_extension($filename);
    
        if (!in_array($extension, static::$scribd_document_extensions))
        {
            throw new InvalidParameterException(
                sprintf(__("upload:invalid_document_format"),
                    implode(", ", static::$scribd_document_extensions)
                )
            );
        }
    
        $file = static::new_from_file_input($file_input);
        $file->storage = 'scribd';
        
        $scribd = get_scribd();
        
        $res = $scribd->upload(
            $file_input['tmp_name'], 
            $extension, 
            'private'
        );
        if (!@$res['doc_id'])
        {   
            throw new IOException(__('upload:storage_error'));
        }           
        $file->docid = $res['doc_id'];
        $file->accesskey = $res['access_key'];
        
        $file->save();
        
        $scribd->changeSettings(
            $file->docid,
            $file->filename,
            Session::get_loggedin_user()->get_url()
        );
        
        /* save a copy on default storage (e.g. s3) */
        $backup_file = static::new_from_file_input($file_input);
        $backup_file->group_name = $file->group_name;
        $backup_file->upload_file($file_input['tmp_name']);
        $backup_file->save();        
        
        return array($file, $backup_file);
    }
    
    /*
     * Uploads a file from user input 
     *  $file_input: an element from $_FILES corresponding to the uploaded file
     *  returns: a new UploadedFile object
     */
    static function upload_from_input($file_input)
    {          
        $file = static::new_from_file_input($file_input);
        $file->upload_file($file_input['tmp_name']);
        $file->save();
        
        return $file;
    }
    
    /*
     * Uploads an image file from user input and saves versions in multiple sizes
     *  $file_input: an element from $_FILES corresponding to the uploaded file
     *  returns: an array of new UploadedFile objects corresponding to the different image sizes saved.
     */    
    static function upload_images_from_input($file_input, $sizes)
    {
        $tmp_file = $file_input['tmp_name'];        

        $orig_name = $file_input['name'];
        $ext = static::get_extension($orig_name);
        
        if (in_array($ext, static::$image_document_extensions))
        {
            if (Config::get('extract_images_from_docs'))
            {
                return static::store_image_from_doc($tmp_file, $ext, $sizes);
            }
            else
            {
                throw new InvalidParameterException(__("upload:invalid_image_format"));
            }
        } 
        else
        {
            return static::store_image($tmp_file, $sizes);
        }
    }    
  
    static function print_to_pdf($temp_path)
    {
        // output pdf file is printed to /tmp/PDF directory 
        // with filename as $temp_path but .pdf extension,
        // as specified in /etc/cups/cups-pdf.conf
        $args = "-norestore -nofirststartwizard -nologo -headless -pt Cups-PDF";
        system("/usr/bin/openoffice.org $args ".escapeshellarg($temp_path));
    }
 
    private static function convert_to_pdf($path, $ext)
    {
        $temp_name = uniqid("upload",false);
        $temp_path = "/tmp/$temp_name";
       
        copy($path, $temp_path);
        $output_path = "/tmp/PDF/$temp_name.pdf";

        FunctionQueue::queue_call(
                array("UploadedFile", "print_to_pdf"), 
                array($temp_path)
        );

        for ($i = 0; $i < 30; $i++)
        {
            sleep(1);
            if (is_file($output_path))
            {
                break;
            }
        }
        if (!is_file($output_path))
        {
            throw new IOException(__('upload:image_extract_failed'));
        }
        @unlink($temp_path);

        return $output_path;
    }

    private static function create_temp_dir($prefix)
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

    private static function extract_image_from_pdf($filename)
    {
        $temp_dir = static::create_temp_dir("pdfimages");

        // pdfimages extracts images from .pdf file
        // in either .jpg or .ppm format
        system("pdfimages -j ".escapeshellarg($filename)." ".escapeshellarg("$temp_dir/img")); 

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
            system("pnmtojpeg ".escapeshellarg($ppm_file)." > ".escapeshellarg($jpg_file));
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

        system("rm -rf ".escapeshellarg($temp_dir));

        return $res_file;
    }

    private static function store_image_from_doc($tmp_file, $ext, $sizes)
    {
        $res = null;
        $converted_pdf = false;
        if ($ext != 'pdf')
        {
            $pdf_filename = static::convert_to_pdf($tmp_file, $ext);
            $converted_pdf = true;
        } 
        else
        {
            $pdf_filename = $tmp_file;
        }

        if ($pdf_filename)
        {
            $pdf_image = static::extract_image_from_pdf($pdf_filename);
            if ($pdf_image)
            {
                $res = static::store_image($pdf_image, $sizes);
                @unlink($pdf_image); 
            }
            else
            {
                throw new DataFormatException(__("upload:no_image_in_doc"));
            }
            
            if ($converted_pdf)
            {
                @unlink($pdf_filename);
            }
        }

        return $res;
    }

    private static function store_image($tmp_file, $sizes)
    {
        $files = array();
        $groupName = uniqid("",true);
        $lastFile = null;

        foreach($sizes as $sizeName => $size)
        {
            $sizeArray = explode("x", $size);
            $resizedImage = static::resize_image_file($tmp_file, $sizeArray);

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
                throw new DataFormatException(__("upload:image_bad"));
            }
        }
        return $files;
    }    
        
    /**
     * Gets the resized version of an already uploaded image
     * (Returns false if the uploaded file was not an image)
     *
     * @param string $imageFileName Filename of the original image
     * @param int $maxsize The maximum width of the resized image, as array(width,height)
     * @return false|mixed array with keys 'filename','width','height','mime' or false on failure
     */
    private static function resize_image_file($imageFileName, $maxsize)
    {
        if ($imgsizearray = getimagesize($imageFileName))
        {
            $width = $imgsizearray[0];
            $height = $imgsizearray[1];
            $mime = $imgsizearray['mime'];
            
            $newsize = constrain_size($imgsizearray, $maxsize);
            
            $newwidth = $newsize[0];
            $newheight = $newsize[1];

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

                if (is_callable($function) && $oldimage = $function($imageFileName)) 
                {
                    imagecopyresampled($newimage, $oldimage, 0,0,0,0, $newwidth,$newheight,$width,$height);

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
    
    
	static $mime_types = array("323" => "text/h323", "acx" => "application/internet-property-stream", "ai" => "application/postscript", "aif" => "audio/x-aiff", "aifc" => "audio/x-aiff", "aiff" => "audio/x-aiff",
        "asf" => "video/x-ms-asf", "asr" => "video/x-ms-asf", "asx" => "video/x-ms-asf", "au" => "audio/basic", "avi" => "video/quicktime", "axs" => "application/olescript", "bas" => "text/plain", "bcpio" => "application/x-bcpio", "bin" => "application/octet-stream", "bmp" => "image/bmp",
        "c" => "text/plain", "cat" => "application/vnd.ms-pkiseccat", "cdf" => "application/x-cdf", "cer" => "application/x-x509-ca-cert", "class" => "application/octet-stream", "clp" => "application/x-msclip", "cmx" => "image/x-cmx", "cod" => "image/cis-cod", "cpio" => "application/x-cpio", "crd" => "application/x-mscardfile",
        "crl" => "application/pkix-crl", "crt" => "application/x-x509-ca-cert", "csh" => "application/x-csh", "css" => "text/css", "dcr" => "application/x-director", "der" => "application/x-x509-ca-cert", "dir" => "application/x-director", "dll" => "application/x-msdownload", "dms" => "application/octet-stream", "doc" => "application/msword",
        "dot" => "application/msword", "dvi" => "application/x-dvi", "dxr" => "application/x-director", "eps" => "application/postscript", "etx" => "text/x-setext", "evy" => "application/envoy", "exe" => "application/octet-stream", "fif" => "application/fractals", "flr" => "x-world/x-vrml", "gif" => "image/gif",
        "gtar" => "application/x-gtar", "gz" => "application/x-gzip", "h" => "text/plain", "hdf" => "application/x-hdf", "hlp" => "application/winhlp", "hqx" => "application/mac-binhex40", "hta" => "application/hta", "htc" => "text/x-component", "htm" => "text/html", "html" => "text/html",
        "htt" => "text/webviewhtml", "ico" => "image/x-icon", "ief" => "image/ief", "iii" => "application/x-iphone", "ins" => "application/x-internet-signup", "isp" => "application/x-internet-signup", "jfif" => "image/pipeg", "jpe" => "image/jpeg", "jpeg" => "image/jpeg", "jpg" => "image/jpeg",
        "js" => "application/x-javascript", "latex" => "application/x-latex", "lha" => "application/octet-stream", "lsf" => "video/x-la-asf", "lsx" => "video/x-la-asf", "lzh" => "application/octet-stream", "m13" => "application/x-msmediaview", "m14" => "application/x-msmediaview", "m3u" => "audio/x-mpegurl", "man" => "application/x-troff-man",
        "mdb" => "application/x-msaccess", "me" => "application/x-troff-me", "mht" => "message/rfc822", "mhtml" => "message/rfc822", "mid" => "audio/mid", "mny" => "application/x-msmoney", "mov" => "video/quicktime", "movie" => "video/x-sgi-movie", "mp2" => "video/mpeg", "mp3" => "audio/mpeg",
        "mpa" => "video/mpeg", "mpe" => "video/mpeg", "mpeg" => "video/mpeg", "mpg" => "video/mpeg", "mpp" => "application/vnd.ms-project", "mpv2" => "video/mpeg", "ms" => "application/x-troff-ms", "mvb" => "application/x-msmediaview", "nws" => "message/rfc822", "oda" => "application/oda",
        "p10" => "application/pkcs10", "p12" => "application/x-pkcs12", "p7b" => "application/x-pkcs7-certificates", "p7c" => "application/x-pkcs7-mime", "p7m" => "application/x-pkcs7-mime", "p7r" => "application/x-pkcs7-certreqresp", "p7s" => "application/x-pkcs7-signature", "pbm" => "image/x-portable-bitmap", "pdf" => "application/pdf", "pfx" => "application/x-pkcs12",
        "pgm" => "image/x-portable-graymap", "pko" => "application/ynd.ms-pkipko", "pma" => "application/x-perfmon", "pmc" => "application/x-perfmon", "pml" => "application/x-perfmon", "pmr" => "application/x-perfmon", "pmw" => "application/x-perfmon", "png" => "image/png", "pnm" => "image/x-portable-anymap", "pot" => "application/vnd.ms-powerpoint", "ppm" => "image/x-portable-pixmap",
        "pps" => "application/vnd.ms-powerpoint", "ppt" => "application/vnd.ms-powerpoint", "prf" => "application/pics-rules", "ps" => "application/postscript", "pub" => "application/x-mspublisher", "qt" => "video/quicktime", "ra" => "audio/x-pn-realaudio", "ram" => "audio/x-pn-realaudio", "ras" => "image/x-cmu-raster", "rgb" => "image/x-rgb",
        "rmi" => "audio/mid", "roff" => "application/x-troff", "rtf" => "application/rtf", "rtx" => "text/richtext", "scd" => "application/x-msschedule", "sct" => "text/scriptlet", "setpay" => "application/set-payment-initiation", "setreg" => "application/set-registration-initiation", "sh" => "application/x-sh", "shar" => "application/x-shar",
        "sit" => "application/x-stuffit", "snd" => "audio/basic", "spc" => "application/x-pkcs7-certificates", "spl" => "application/futuresplash", "src" => "application/x-wais-source", "sst" => "application/vnd.ms-pkicertstore", "stl" => "application/vnd.ms-pkistl", "stm" => "text/html", "svg" => "image/svg+xml", "sv4cpio" => "application/x-sv4cpio",
        "sv4crc" => "application/x-sv4crc", "t" => "application/x-troff", "tar" => "application/x-tar", "tcl" => "application/x-tcl", "tex" => "application/x-tex", "texi" => "application/x-texinfo", "texinfo" => "application/x-texinfo", "tgz" => "application/x-compressed", "tif" => "image/tiff", "tiff" => "image/tiff",
        "tr" => "application/x-troff", "trm" => "application/x-msterminal", "tsv" => "text/tab-separated-values", "txt" => "text/plain", "uls" => "text/iuls", "ustar" => "application/x-ustar", "vcf" => "text/x-vcard", "vrml" => "x-world/x-vrml", "wav" => "audio/x-wav", "wcm" => "application/vnd.ms-works",
        "wdb" => "application/vnd.ms-works", "wks" => "application/vnd.ms-works", "wmf" => "application/x-msmetafile", "wps" => "application/vnd.ms-works", "wri" => "application/x-mswrite", "wrl" => "x-world/x-vrml", "wrz" => "x-world/x-vrml", "xaf" => "x-world/x-vrml", "xbm" => "image/x-xbitmap", "xla" => "application/vnd.ms-excel",
        "xlc" => "application/vnd.ms-excel", "xlm" => "application/vnd.ms-excel", "xls" => "application/vnd.ms-excel", "xlt" => "application/vnd.ms-excel", "xlw" => "application/vnd.ms-excel", "xof" => "x-world/x-vrml", "xpm" => "image/x-xpixmap", "xwd" => "image/x-xwindowdump", "z" => "application/x-compress", "zip" => "application/zip");
}
