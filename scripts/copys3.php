<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");

    global $CONFIG;

    $s3 = get_storage();

    foreach ($s3->get_bucket_contents('envaya_data') as $key => $keyInfo)
    {
        //var_dump($keyInfo);
        if (strpos($key,'temp') == false)
        {
            $s3->copy_object('envaya_data', $key, 'envayadata', $key, true);
            echo "$key\n";
        }
        else
        {
        }
    }