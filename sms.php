<?php

require "start.php";
require "scripts/cmdline.php";

$from = "+1234567890";
$to = Config::get('news_phone_number');

echo "Simulating SMS $from -> $to\n";

while (true)
{
    $msg = _readline("> ");
    try
    {
        $res = file_get_contents(abs_url("/pg/receive_sms?From=".urlencode($from)
            ."&To=".urlencode($to)
            ."&Body=".urlencode($msg)));
        $dom = new DOMDocument();
        $dom->loadXML($res);                    
    }
    catch (ErrorException $ex)
    {
        continue;
    }
    
    $smses = $dom->getElementsByTagName('Sms');
    for ($i = 0; $i < $smses->length; $i++)
    {
        echo ($i + 1).". ".$smses->item($i)->textContent. "\n";
    }
}
