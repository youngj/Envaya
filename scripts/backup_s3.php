<?php

    /*
    * Copies new files from a s3 bucket, and saves them to a local directory.
    *
    * Doesn't update previously backed-up files that have changed on s3.
    */

    require_once("scripts/cmdline.php");
    require_once("start.php");

    require_once("vendors/s3.php");

    umask(0);

    $max_guid = (int)State::get('s3_backup_guid');

    $new_files = 0;

    $start = microtime(true);

    $files = UploadedFile::query()
        ->where('guid > ?', $max_guid)
        ->where("storage = '' or storage is null or storage=?", 's3')
        ->limit(500)
        ->order_by('guid')
        ->filter();
        
    echo "Processing ".sizeof($files)." files with guid > $max_guid\n";        
        
    foreach ($files as $file)
    {    
        $storage = $file->get_storage();
        
        if ($storage instanceof Storage_S3)
        {
            $key = $file->get_storage_key();
            $localPath = Config::get('dataroot') . "/s3_backup/$key";
        
            $dir = dirname($localPath);

            if (!is_dir($dir))
            {
                mkdir($dir, 0777, true);
            }
            
            if (!is_file($localPath))
            {
                echo "$key\n";
            
                if (!$storage->download_file($key, $localPath))
                {
                    error_log("Error downloading $key");
                }
                
                $mtime = $file->time_created;
                touch($localPath, $mtime);            
                $new_files++;
            }
        }
        State::set('s3_backup_guid', $file->guid);
    }

    $end = microtime(true);
    $elapsed = $end - $start;

    State::set('s3_backup_time', timestamp());
    State::set('s3_backup_info', "$new_files new files / $elapsed sec");