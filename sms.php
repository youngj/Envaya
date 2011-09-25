<?php

require "start.php";
require "scripts/cmdline.php";

$from = SMS_Provider_Twilio::format_number("115152453453");
//$to = SMS_Provider_Twilio::format_number("14847722224");
$to = "16505551212";

echo "Simulating SMS $from -> $to\n";

while (true)
{
    $msg = readline("> ");
    try
    {
        $res = file_get_contents(abs_url("/sg/incoming?provider=Mock&from=".urlencode($from)
            ."&to=".urlencode($to)
            ."&msg=".urlencode($msg)));
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
