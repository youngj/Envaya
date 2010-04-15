<?php

gatekeeper();

$sizes = json_decode(get_input('sizes'));

$res = array();

$filename = get_uploaded_filename('file');

foreach($sizes as $sizeName => $size)
{
    $file = new ElggFile();
    $file->owner_guid = get_loggedin_userid();
   
    $sizeArray = explode("x", $size);
   
    $resizedImage = resize_image_file($filename, $sizeArray[0], $sizeArray[1]); 

    $tempFilename = "temp/".mt_rand().".jpg";

    $file->setFilename($tempFilename);
    $file->uploadFile($resizedImage);
    
    $res[$sizeName] = array(
        'filename' => $tempFilename,
        'url' => $file->getURL(),
    );
}

$json = json_encode($res);

if (get_input('iframe'))
{    
    Session::set('lastUpload', $json);
    forward("upload.php?swfupload=".urlencode(get_input('swfupload'))."&sizes=".urlencode(get_input('sizes')));
}
else
{
    header("Content-Type: text/javascript");
    echo $json;
}    
