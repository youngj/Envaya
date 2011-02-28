<?php

/* 
 * An interface for storing and retrieving uploaded files;
 * see implementations in storage/ directory.
 */
interface Storage
{
	public function get_url($key);
	public function upload_file($key, $fs_path, $web_accessible = false, $mime = null);
	public function delete_object($key);
	public function copy_object($key, $dest_key, $web_accessible = false);
	public function get_object_info($key);
	public function download_file($key, $fs_path);
}