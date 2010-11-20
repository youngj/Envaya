<?php
interface Storage
{
	public function get_url($bucket_name, $s3_path);
	public function upload_file($bucket_name, $s3_path, $fs_path, $web_accessible = false, $headers = null);
	public function delete_object($bucket_name, $s3_path);
	public function copy_object($bucket_name, $s3_path, $dest_bucket_name, $dest_s3_path, $web_accessible = false);
	public function get_object_info($bucket_name, $s3_path);
	public function download_file($bucket_name, $s3_path, $fs_path);
 }