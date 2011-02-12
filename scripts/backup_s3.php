<?php

global $BACKUP_DIR;
$BACKUP_DIR = "/etc/dropbox/Dropbox/s3_backup";

require_once("scripts/cmdline.php");
require_once("engine/start.php");

require_once("vendors/s3.php");

global $s3;
$s3 = new S3(Config::get('s3_key'), Config::get('s3_private'));

function enumerate_bucket($s3, $callback)
{
    $bucketName = Config::get('s3_bucket');
    $marker = null;
    do
    {
        $q = '?max-keys=500';
        if(!is_null($marker))
        {
            $q .= '&marker=' . urlencode($marker);
        }

        $request = array('verb' => 'GET', 'resource' => "/$bucketName/$q");
        $result = $s3->sendRequest($request);
        $xml = simplexml_load_string($result);

        if($xml === false)
            return false;

        foreach($xml->Contents as $item)
        {
            $callback($item);

            $key = $item->Key;
            if ($marker == null || strcmp($key, $marker) > 0)
            {
                $marker = $key;
            }
        }
    }
    while((string) $xml->IsTruncated == 'true');
}

global $n;

function handle_item($item)
{
    global $s3, $n, $BACKUP_DIR;
    $key = $item->Key;

    $localPath = "$BACKUP_DIR/$key";
    $dir = dirname($localPath);

    if (!is_dir($dir))
    {
        mkdir($dir, 0777, true);
    }
    if (!is_file($localPath))
    {
        echo "$localPath\n";
        $s3->downloadFile(Config::get('s3_bucket'), $key, $localPath);
        $mtime = strtotime($item->LastModified);
        touch($localPath, $mtime);
    }
    else
    {
        echo "exists: $localPath\n";
    }
}

enumerate_bucket($s3, 'handle_item');