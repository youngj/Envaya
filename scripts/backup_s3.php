<?php

    require_once("scripts/cmdline.php");
    require_once("engine/start.php");
   
function enumerate_bucket($s3, $bucketName, $callback)
{  
    $marker = null;
    do
    {
        $q = '?max-keys=250';
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
    global $s3, $bucketName, $n;
    $key = $item->Key;
    
    $localPath = "../envayadata/$key";
    $dir = dirname($localPath);    
    
    if (!is_dir($dir))
    {
        mkdir($dir, 0777, true);
    }
    if (!is_file($localPath))
    {
        echo "$localPath\n";
        $s3->downloadFile($bucketName, $key, $localPath);
        $mtime = strtotime($item->LastModified);
        touch($localPath, $mtime);
    }    
    else
    {
        echo "exists: $localPath\n";
    }
}

global $s3;
global $bucketName;

$s3 = get_s3();
$bucketName = 'envayadata';
enumerate_bucket($s3, $bucketName, 'handle_item');