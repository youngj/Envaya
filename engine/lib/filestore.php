<?php

    include_once("objects.php");

    function get_s3()
    {
        global $CONFIG;
        require_once("{$CONFIG->path}engine/lib/Net/s3.php");
        return new S3($CONFIG->s3_key, $CONFIG->s3_private);
    }

    class ElggFile extends ElggObject
    {
        static $subtype_id = T_file;

        static $table_name = 'files_entity';
        static $table_attributes = array(
            'group_name' => '',
            'filename' => '',
            'size' => '',
            'width' => null,
            'height' => null,
            'mime' => '',
        );

        public function setFilename($name)
        {
            $this->filename = $name;
        }

        public function getFilename()
        {
            return $this->filename;
        }

        public function getFilesInGroup()
        {
            return ElggFile::filterByCondition(
                array('owner_guid = ?', 'group_name = ?'),
                array($this->owner_guid, $this->group_name)
            );
        }

        public function jsProperties()
        {
            return array(
                'guid' => $this->guid,
                'size' => $this->size,
                'group_name' => $this->group_name,
                'filename' => $this->filename,
                'mime' => $this->mime,
                'width' => $this->width,
                'height' => $this->height,
                'url' => $this->getURL(),
            );
        }

        public function getPath()
        {
            if ($this->group_name)
            {
                return "{$this->owner_guid}/{$this->group_name}/{$this->filename}";
            }
            else
            {
                return "{$this->owner_guid}/{$this->filename}";
            }
        }

        public function getURL()
        {
            global $CONFIG;
            return "http://{$CONFIG->s3_bucket}.s3.amazonaws.com/{$this->getPath()}";
        }

        public function delete()
        {
            global $CONFIG;
            $res = get_s3()->deleteObject($CONFIG->s3_bucket, $this->getPath());

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
            global $CONFIG;
            $info = get_s3()->getObjectInfo($CONFIG->s3_bucket, $this->getPath());
            if ($info)
            {
                return $info['Content-Length'];
            }
            return -1;
        }

        public function uploadFile($filePath, $mime = null)
        {
            global $CONFIG;

            $headers = array();
            if ($mime)
            {
                $headers['Content-Type'] = $mime;
            }

            return get_s3()->uploadFile($CONFIG->s3_bucket, $this->getPath(), $filePath, true, $headers);
        }

        public function copyTo($destFile)
        {
            global $CONFIG;
            $res = get_s3()->copyObject($CONFIG->s3_bucket, $this->getPath(), $CONFIG->s3_bucket, $destFile->getPath(), true);
            return $res;
        }

        public function exists()
        {
            global $CONFIG;
            $info = get_s3()->getObjectInfo($CONFIG->s3_bucket, $this->getPath());
            return ($info) ? true : false;
        }
    }

    function get_uploaded_filename($input_name)
    {
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] == 0)
        {
            return $_FILES[$input_name]['tmp_name'];
        }
        return false;
    }

    function has_uploaded_file($input_name)
    {
        return isset($_FILES[$input_name]) && $_FILES[$input_name]['size'];
    }

    function is_image_upload($input_name)
    {
        return substr_count($_FILES[$input_name]['type'],'image/');
    }

    function get_thumbnail_src($html)
    {
        if (preg_match('/src="http:\/\/(\w+)\.s3\.amazonaws\.com\/(\d+)\/([\w\.]+)\//', $html, $matches))
        {
            $ownerGuid = $matches[2];
            $groupName = $matches[3];

            $files = ElggFile::filterByCondition(
                array('owner_guid = ?', 'group_name = ?','size = ?'),
                array($ownerGuid, $groupName, 'small')
            );
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

            $files = ElggFile::filterByCondition(
                array('owner_guid = ?', 'group_name = ?','filename = ?'),
                array($ownerGuid, $groupName, $fileName)
            );
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
                    $file->setFilename("$sizeName.jpg");
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
