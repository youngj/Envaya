<?php

/*
 * Stores uploaded files on the local computer
 */
class Storage_Local implements Storage
{
	public function get_url($path)
	{	
		return "http://".Config::get('domain')."/pg/local_store?path={$path}";
	}
	
	public function upload_file($path, $fs_path, $web_accessible = false, $mime = null)
	{
		$file_path = $this->get_file_path($path);
		$dir = dirname($file_path);
		if (!is_dir($dir))
		{
			mkdir($dir, 0777, true);
		}
		
		file_put_contents($file_path, file_get_contents($fs_path));
	}

	public function copy_object($path, $dest_path, $web_accessible = false)
	{
		$old_path = $this->get_file_path($path);
		return $this->upload_file($dest_path, $old_path);
	}
	
	public function get_object_info($path)
	{
		$file_path = $this->get_file_path($path);
		if (is_file($file_path))
		{
			return array(
				'todo'
			);
		}
	}
	
	public function delete_object($path)
	{
		throw new NotImplementedException();
	}
	
	public function download_file($path, $fs_path)
	{
		throw new NotImplementedException();
	}

	public function get_file_path($path)
	{
		return Config::get('dataroot')."/local_store/{$path}";	
	}
}
