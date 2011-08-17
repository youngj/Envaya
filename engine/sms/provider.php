<?php

abstract class SMS_Provider
{   
    abstract function is_validated_request();   
    abstract function send_sms($from, $to, $msg);    
}