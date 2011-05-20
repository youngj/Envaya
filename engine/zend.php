<?php

class Zend
{      
    static $loaded = false;
    static $mailer = null;

    static function load($cls)
    {
        if (!static::$loaded)
        {
            static::$loaded = true;
            set_include_path(get_include_path() . PATH_SEPARATOR . Config::get('path').'vendors/zend');
            require_once 'Zend/Loader.php';            
        }
        Zend_Loader::loadClass($cls);
    }    
    
    static function mail()
    {
        static::load('Zend_Mail');
        return new Zend_Mail('UTF-8');    
    }
    
    static function mail_transport()
    {
        if (!static::$mailer)
        {
            if (Config::get('mock_mail_file'))
            {
                static::$mailer = new Mail_Mock();
            }
            else
            {                
                static::load('Zend_Mail_Transport_Smtp');
            
                static::$mailer = new Zend_Mail_Transport_Smtp(Config::get('smtp_host'),
                    array(
                        'port' => Config::get('smtp_port'),
                        'username' => Config::get('smtp_user'),                        
                        'password' => Config::get('smtp_pass'),
                        'auth' => 'Login',
                    )                                                
                );
            }
        }
        return static::$mailer;    
    }
    
    static function imap()
    {        
        static::load('Zend_Mail_Protocol_Imap');       
        return new Zend_Mail_Protocol_Imap(Config::get('imap_host'),Config::get('imap_port'),'SSL');
    }
    
    static $gapps = null;
    
    static function google_apps()
    {
        if (!static::$gapps)
        {
            Zend::load('Zend_Gdata_ClientLogin');
            Zend::load('Zend_Gdata_Gapps');
        
            $client = Zend_Gdata_ClientLogin::getHttpClient(
                Config::get('apps_admin'),
                Config::get('apps_password'), 
                Zend_Gdata_Gapps::AUTH_SERVICE_NAME);
            
            static::$gapps = new Zend_Gdata_Gapps($client, Config::get('apps_domain'));
        }
        return static::$gapps;
    }    
    
    static function geonames()
    {
        Zend::load('Bgy_Service_Geonames');
        return new Bgy_Service_Geonames(array('username' => Config::get('geonames_user')));
    }
        
}