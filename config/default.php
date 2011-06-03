<?php
return array(
    'cache_version' => 187,  // increment when css, or external js (tinymce/swfupload) changes
    'debug' => true,
    
    'dbuser' => '',
    'dbpass' => '',
    'dbname' => '',
    'dbhost' => 'localhost',

    'queue_host' => "localhost",
    'queue_port' => 22133,
    
    'sphinx_host' => 'localhost',  
    'sphinx_port' => 9312,
   
    'cache_backend' => "Cache_Database",

    'admin_email' => "admin@localhost",
    'post_email' => "post@localhost",
    'email_from' => "web@localhost",
    'email_pass' => "",    
    
    'sphinx_conf_dir' => '/usr/local/etc',
    'sphinx_bin_dir' => '/usr/local/bin',
    'sphinx_log_dir' => '/var/log/sphinx',
    'sphinx_pid_dir' => '/var/run/sphinx',
        
    'google_analytics_id' => "",
    'analytics_enabled' => false,
    
    'error_emails_enabled' => false,
    'ssl_enabled' => false,

    'storage_backend' => 'Storage_Local',

    'root' => dirname(__DIR__),
    'dataroot' => dirname(dirname(__DIR__)). "/envayadata",

    'languages' => array(
        'en' => 'English',
        'sw' => 'Kiswahili'
    ),
    'language' => "en",
	
    'mock_mail_file' => null,

    'smtp_host' => "localhost",
    'smtp_port' => 25,
    'smtp_user' => '',
    'smtp_pass' => "",

    'cookie_domain' => null,
    'domain' => "localhost",
    'site_name' => "Envaya",

    'recaptcha_enabled' => false,
    'recaptcha_key' => '',
    'recaptcha_private' => '',
    
    'scribd_key' => '',
    'scribd_private' => '',

    'extract_images_from_docs' => false,
    
    'modules' => array('envaya_org','translate','contact'),
    
    'discussion_list_suffix' => '-list',
    'apps_domain' => '',
    'apps_admin' => '',
    'apps_password' => '',
    
    'geonames_user' => '',
    'default_timezone' => 'Africa/Dar_es_Salaam',
    
    'site_secret' => 'default_secret',
    'fallback_theme' => 'simple',
);