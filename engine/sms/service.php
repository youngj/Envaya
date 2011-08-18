<?php

abstract class SMS_Service
{
    abstract function get_id();
    abstract function get_default_controller();
        
    static function is_match($number, $to_number)
    {
        return strpos(PhoneNumber::canonicalize($number), $to_number) !== false;
    }        

    static function route($to_number)
    {
        if (static::is_match(Config::get('news_phone_number'), $to_number))
        {
            return new SMS_Service_News();
        }
        else if (static::is_match(Config::get('contact_phone_number'), $to_number))
        {
            return new SMS_Service_Contact();
        }
        else
        {
            return new SMS_Service_Unknown();
        }
    }
}