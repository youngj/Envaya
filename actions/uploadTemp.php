<?php

gatekeeper();

$sizes = json_decode(get_input('sizes'));

$filename = get_uploaded_filename('file');

$json = upload_temp_images($filename, $sizes);

if (get_input('iframe'))
{    
    Session::set('lastUpload', $json);
    forward("upload.php?swfupload=".urlencode(get_input('swfupload'))."&sizes=".urlencode(get_input('sizes')));
}
else
{    
    header("Content-Type: text/javascript");
    echo $json;
    exit();
}    
