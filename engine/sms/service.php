<?php

abstract class SMS_Service
{
    abstract function get_id();
    abstract function get_default_controller();
        
    static function route($to_number)
    {
        if ($to_number == PhoneNumber::canonicalize(Config::get('news_phone_number')))
        {
            return new SMS_Service_News();
        }
        else if ($to_number == PhoneNumber::canonicalize(Config::get('contact_phone_number')))
        {
            return new SMS_Service_Contact();
        }
        else
        {
            return new SMS_Service_Unknown();
        }
    }
}