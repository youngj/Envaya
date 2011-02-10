<?php
class Storage_S3 implements Storage
{
    public function get_url($path)
    {
        global $CONFIG;
        return "http://{$CONFIG->s3_bucket}.s3.amazonaws.com/{$path}";
    }    
    public function upload_file($path, $fs_path, $web_accessible = false, $mime = null)
    {
        global $CONFIG;
        
        $headers = array();
        if ($mime)
        {
            $headers['Content-Type'] = $mime;
        }
        
        return $this->get_s3()->uploadFile($CONFIG->s3_bucket, $path, $fs_path, $web_accessible, $headers);      
    }
    public function delete_object($path)
    {
        global $CONFIG;
        return $this->get_s3()->deleteObject($CONFIG->s3_bucket, $path);
    }
    public function copy_object($path, $dest_path, $web_accessible = false)
    {
        global $CONFIG;
        return $this->get_s3()->copyObject($CONFIG->s3_bucket, $path, 
                    $CONFIG->s3_bucket, $dest_path, $web_accessible);        
    }
    public function get_object_info($path)
    {
        global $CONFIG;
        return $this->get_s3()->getObjectInfo($CONFIG->s3_bucket, $path);             
    }        
    public function download_file($path, $fs_path)
    {   
        global $CONFIG;
        return $this->get_s3()->downloadFile($CONFIG->s3_bucket, $path, $fs_path);
    }
    
    private function get_s3()
    {
        global $CONFIG;
        require_once("{$CONFIG->path}vendors/s3.php");        
        return new S3($CONFIG->s3_key, $CONFIG->s3_private);
    }    
}
 