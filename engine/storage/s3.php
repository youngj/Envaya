<?php

/*
 * Stores uploaded files on Amazon S3
 */
class Storage_S3 implements Storage
{
    public function get_url($path)
    {
        $s3_bucket = Config::get('s3_bucket');
        return "http://{$s3_bucket}.s3.amazonaws.com/{$path}";
    }    
    public function upload_file($path, $fs_path, $web_accessible = false, $mime = null)
    {
        $headers = array();
        if ($mime)
        {
            $headers['Content-Type'] = $mime;
        }
        
        $headers['Cache-Control'] = 'max-age=10000000';
        
        return $this->get_s3()->uploadFile(Config::get('s3_bucket'), $path, $fs_path, $web_accessible, $headers);      
    }
    public function delete_object($path)
    {
        return $this->get_s3()->deleteObject(Config::get('s3_bucket'), $path);
    }
    public function copy_object($path, $dest_path, $web_accessible = false)
    {
        return $this->get_s3()->copyObject(Config::get('s3_bucket'), $path, 
                    Config::get('s3_bucket'), $dest_path, $web_accessible);        
    }
    public function get_object_info($path)
    {
        return $this->get_s3()->getObjectInfo(Config::get('s3_bucket'), $path);             
    }        
    public function download_file($path, $fs_path)
    {   
        return $this->get_s3()->downloadFile(Config::get('s3_bucket'), $path, $fs_path);
    }
    
    private function get_s3()
    {
        require_once(Config::get('path')."vendors/s3.php");        
        return new S3(Config::get('s3_key'), Config::get('s3_private'));
    }    
}
 