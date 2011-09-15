<?php

abstract class SMS_Provider
{   
    // incoming sms
    abstract function is_validated_request();   
    abstract function get_request_from();
    abstract function get_request_to();
    abstract function get_request_message();
    abstract function render_response($replies, $controller);    
    
    // outgoing sms
    abstract function can_send_sms();
    function send_sms($sms) { throw new NotImplementedException(); }
}