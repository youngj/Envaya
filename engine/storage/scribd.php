<?php

class Storage_Scribd extends Storage
{
	public function get_url($key)
    {
        return "//www.scribd.com/doc/{$key['docid']}";
    }
	public function upload_file($key /* ignored */, $fs_path, $web_accessible = false, $mime = null)
    {        
        throw new NotImplementedException();
    }
	public function get_object_info($key)
    {
        throw new NotImplementedException();
    }
	public function download_file($key, $fs_path)
    {
        throw new NotImplementedException();
    }
	public function delete_object($key)
    {
        throw new NotImplementedException();
    }
	public function copy_object($key, $dest_key, $web_accessible = false)
    {
        throw new NotImplementedException();
    }    
}
