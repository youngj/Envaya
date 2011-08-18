<?php

require "start.php";
require "scripts/cmdline.php";

$from = SMS_Provider_Twilio::format_number("115152453453");
//$to = SMS_Provider_Twilio::format_number("14847722224");
$to = SMS_Provider_Twilio::format_number(Config::get('news_phone_number'));

echo "Simulating SMS $from -> $to\n";

while (true)
{
    $msg = readline("> ");
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
