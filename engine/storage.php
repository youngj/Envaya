<?php
interface Storage
{
	public function get_url($path);
	public function upload_file($path, $fs_path, $web_accessible = false, $headers = null);
	public function delete_object($path);
	public function copy_object($path, $dest_path, $web_accessible = false);
	public function get_object_info($path);
	public function download_file($path, $fs_path);
}