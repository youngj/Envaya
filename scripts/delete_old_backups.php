<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    require_once("vendors/s3.php");

	$s3 = new S3(Config::get('s3_key'), Config::get('s3_private'));    
    
	$bucket_name = Config::get('s3_backup_bucket');
	
	$contents = $s3->getBucketContents($bucket_name);
	
	$time = timestamp();
	
	$backup_interval = 60 * 720;
	
	foreach ($contents as $key => $props)
	{	
		$backup_time = strtotime($props['LastModified']);
		
		$backup_age = $time - $backup_time;
				
		$backup_index = (int)($backup_time / $backup_interval);
		
		if (($backup_age > 86400 * 3 && $backup_index % 2 != 0)
			|| ($backup_age > 86400 * 7 && $backup_index % 8 != 0)
			|| ($backup_age > 86400 * 30 && $backup_index % 32 != 0)
			|| ($backup_age > 86400 * 120 && $backup_index % 128 != 0))
		{
			error_log("deleting old backup $key");
			$s3->deleteObject($bucket_name, $key);
		}
	}