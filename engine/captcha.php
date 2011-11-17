<?php

abstract class Captcha
{
	abstract function get_html();
    abstract function show();
    abstract function check_answer($response);    
    
    static function get_instance()
    {
        $cls = Config::get('captcha:backend');
        return new $cls();
    }    
}   