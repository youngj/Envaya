<?php

abstract class SMS_Provider
{   
    abstract function init_request();
    abstract function is_validated_request();   
    abstract function send_sms($from, $to, $msg);    
}