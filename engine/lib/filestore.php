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
				
		public function setFilename($name) 
        { 
            $this->filename = $name; 
        }
        
		public function getFilename() 
        { 
            return $this->filename; 
        }       
        
        public function getPath()
        {
            return "{$this->owner_guid}/{$this->filename}";
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
        
        public function uploadFile($filePath)
        {
            global $CONFIG;
            return get_s3()->uploadFile($CONFIG->s3_bucket, $this->getPath(), $filePath, true);
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
    
    function upload_temp_images($filename, $sizes)
    {
        $res = array();

        foreach($sizes as $sizeName => $size)
        {
            $file = new ElggFile();
            $file->owner_guid = get_loggedin_userid();

            $sizeArray = explode("x", $size);

            $resizedImage = resize_image_file($filename, $sizeArray[0], $sizeArray[1]); 
            if ($resizedImage)
            {
                $tempFilename = "temp/".mt_rand().".jpg";

                $file->setFilename($tempFilename);
                $file->uploadFile($resizedImage);

                $res[$sizeName] = array(
                    'filename' => $tempFilename,
                    'url' => $file->getURL(),
                );
            }    
            else
            {
                return json_encode(null);
            }
        }
        return json_encode($res);
    }    
    
    function get_uploaded_files($json)
    {
        $filedata = json_decode($json);
               
        if (!$filedata)
        {
            return null;
        }
        
        $res = array();
        foreach ($filedata as $size => $value)
        {
            $file = new ElggFile();
            $file->owner_guid = get_loggedin_userid();
            $file->setFilename($value->filename);
        
            $res[$size] = $file;
        }
        return $res;
    }    
    
	/**
	 * Gets the jpeg contents of the resized version of an already uploaded image 
	 * (Returns false if the uploaded file was not an image)
	 *
	 * @param string $input_name The name of the file input field on the submission form
	 * @param int $maxwidth The maximum width of the resized image
	 * @param int $maxheight The maximum height of the resized image
	 * @param true|false $square If set to true, will take the smallest of maxwidth and maxheight and use it to set the dimensions on all size; the image will be cropped.
	 * @return false|mixed The contents of the resized image, or false on failure
	 */
    function resize_image_file($input_name, $maxwidth, $maxheight, $square = false, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) 
    {
		
		// Get the size information from the image
		if ($imgsizearray = getimagesize($input_name))
        {		
			// Get width and height
			$width = $imgsizearray[0];
			$height = $imgsizearray[1];
			$newwidth = $width;
			$newheight = $height;
			
			// Square the image dimensions if we're wanting a square image
			if ($square) {
				if ($width < $height) {
					$height = $width;
				} else {
					$width = $height;
				}
				
				$newwidth = $width;
				$newheight = $height;
				
			}
			
			if ($width > $maxwidth) {
				$newheight = floor($height * ($maxwidth / $width));
				$newwidth = $maxwidth;
			}
			if ($newheight > $maxheight) {
				$newwidth = floor($newwidth * ($maxheight / $newheight));
				$newheight = $maxheight; 
			}
			
			$accepted_formats = array(
                'image/jpeg' => 'jpeg',
                'image/png' => 'png',
                'image/gif' => 'gif'
            );
			
            if ($imgsizearray['mime'] == 'image/jpeg' && $width == $newwidth && $height == $newheight)
            {
                // avoid re-encoding file if it's already the correct size and file type
            
                return $input_name;
            }
            
			// If it's a file we can manipulate ...
			if (array_key_exists($imgsizearray['mime'],$accepted_formats)) {

				$function = "imagecreatefrom" . $accepted_formats[$imgsizearray['mime']];
				$newimage = imagecreatetruecolor($newwidth,$newheight);
				
				if (is_callable($function) && $oldimage = $function($input_name)) {
 				
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
                    
                    if (imagejpeg($newimage, $tempFileName, 90))
                    {
                        return $tempFileName;
                    }										
				}
				
			}
			
		}
			
		return false;
	}	
