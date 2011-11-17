<?php

Zend::load('Zend_Mail_Transport_Smtp');

class Mail_SMTP extends Zend_Mail_Transport_Smtp
{
    function __construct()
    {
        parent::__construct(Config::get('mail:smtp_host'),
            array(
                'port' => Config::get('mail:smtp_port'),
                'username' => Config::get('mail:smtp_user'),                        
                'password' => Config::get('mail:smtp_pass'),
                'auth' => 'Login',
            )                                                
        );    
    }
}