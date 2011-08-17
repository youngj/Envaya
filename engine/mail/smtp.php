<?php

Zend::load('Zend_Mail_Transport_Smtp');

class Mail_SMTP extends Zend_Mail_Transport_Smtp
{
    function __construct()
    {
        parent::__construct(Config::get('smtp_host'),
            array(
                'port' => Config::get('smtp_port'),
                'username' => Config::get('smtp_user'),                        
                'password' => Config::get('smtp_pass'),
                'auth' => 'Login',
            )                                                
        );    
    }
}