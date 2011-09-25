<?php

abstract class SMS_Provider
{
    // incoming sms
    abstract function is_validated_request();
    abstract function get_request_from();
    abstract function get_request_to();    
    abstract function get_request_text();    
    abstract function render_response($replies, $controller);    
    
    function render_message_html()
    {
        return escape($this->get_request_text());
    }
    
    // outgoing sms
    abstract function can_send_sms();
    function send_sms($sms) { throw new NotImplementedException(); }
}