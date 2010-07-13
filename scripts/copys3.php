<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    global $CONFIG;

    $s3 = get_s3();

    foreach ($s3->getBucketContents('envaya_data') as $key => $keyInfo)
    {
        //var_dump($keyInfo);
        if (strpos($key,'temp') == false)
        {
            $s3->copyObject('envaya_data', $key, 'envayadata', $key, true);
            echo "$key\n";
        }
        else
        {
        }
    }