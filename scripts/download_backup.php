<?php
    require_once("scripts/cmdline.php");
    require_once("vendors/s3.php");
    require_once("start.php");

    $s3_path = $argv[1];
    $fs_path = Config::get('dataroot')."/$s3_path";
    
    $s3 = new S3(Config::get('storage:s3_key'), Config::get('storage:s3_private'));    
    
    error_log("downloading $s3_path...");
    
    if ($s3->downloadFile(Config::get('task:s3_backup_bucket'), $s3_path, $fs_path))
    {
        error_log($fs_path);
    }
    else
    {
        error_log("error downloading file");
    }

    