<?php

require "start.php";
require "scripts/cmdline.php";

$from = SMS_Provider_Twilio::format_number("16507993371");
//$to = SMS_Provider_Twilio::format_number("14847722224");
$to = "16505551212";

echo "Simulating SMS $from -> $to\n";

while (true)
{
    $msg = readline("> ");
    try
    {
        $url = abs_url("/sg/incoming?provider=Mock&from=".urlencode($from)
            ."&to=".urlencode($to)
            ."&msg=".urlencode($msg));
    
        $res = file_get_contents($url);
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
        $text = $smses->item($i)->textContent;
        $len = strlen($text);
    
    
        echo "$text\n";
        echo "($len chars)\n";
    }
}
