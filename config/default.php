<?php
return array(
    'cache_version' => 122,  // increment when css, or external js (tinymce/swfupload) changes
    
    'dbuser' => '',
    'dbpass' => '',
    'dbname' => '',
    'dbhost' => 'localhost',

    'queue_host' => "localhost",
    'queue_port' => 22133,
   
    'cache_backend' => "DatabaseCache",

    'admin_email' => "admin@localhost",
    'post_email' => "post@localhost",
    'email_from' => "web@localhost",
    'email_pass' => "",
    
    'google_api_key' => "ABQIAAAAHy69XWEjciJIVElz0OYMsRR3-IOatrPZ1tLat998tYHgwqPnkhTKyWcq8ytRPMx3RyxFjK0O7WSCHA",
    
    'google_analytics_id' => "",
    'analytics_enabled' => false,
    
    'error_emails_enabled' => false,
    'ssl_enabled' => false,

    'storage_backend' => 'Storage_Local',

    'path' => dirname(__DIR__) . "/",
    
    'languages' => array(
        'en' => 'English',
        'sw' => 'Kiswahili'
    ),
    'language' => "en",

    'dataroot' => dirname(dirname(__DIR__)). "/envayadata/",
	
    'simplecache_enabled' => false,
    
    'mock_mail_file' => null,

    'smtp_host' => "localhost",
    'smtp_port' => 25,
    'smtp_user' => '',
    'smtp_pass' => "",

    'cookie_domain' => null,
    'domain' => "localhost",
    'debug' => true,
    'sitename' => "Envaya",

    'recaptcha_enabled' => false,
    'recaptcha_key' => '',
    'recaptcha_private' => '',
    
    'scribd_key' => '',
    'scribd_private' => '',

    'extract_images_from_docs' => false,
);