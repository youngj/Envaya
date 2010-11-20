<?php
class Storage_Local implements Storage
{
	public function get_url($bucket_name, $s3_path)
	{	
		global $CONFIG;
		return "http://{$CONFIG->domain}/pg/local_store?bucket={$bucket_name}&path={$s3_path}";
	}
	
	public function upload_file($bucket_name, $s3_path, $fs_path, $web_accessible = false, $headers = null)
	{
		$file_path = $this->get_file_path($bucket_name, $s3_path);
		$dir = dirname($file_path);
		if (!is_dir($dir))
		{
			mkdir($dir, 0777, true);
		}
		
		file_put_contents($file_path, file_get_contents($fs_path));
	}

	public function copy_object($bucket_name, $s3_path, $dest_bucket_name, $dest_s3_path, $web_accessible = false)
	{
		$old_path = $this->get_file_path($bucket_name, $s3_path);
		return $this->upload_file($dest_bucket_name, $dest_s3_path, $old_path);
	}
	
	public function get_object_info($bucket_name, $s3_path)
	{
		$file_path = $this->get_file_path($bucket_name, $s3_path);
		if (is_file($file_path))
		{
			return array(
				'todo'
			);
		}
	}
	
	public function delete_object($bucket_name, $s3_path)
	{
		// todo
	}
	
	public function download_file($bucket_name, $s3_path, $fs_path)
	{
		// todo
	}

	public function get_file_path($bucket_name, $path)
	{
		global $CONFIG;
		return "{$CONFIG->dataroot}s3/{$bucket_name}/{$path}";	
	}
}
 