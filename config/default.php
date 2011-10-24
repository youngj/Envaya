<?php

$root = dirname(__DIR__);

return array(
    'debug' => true,
    
    'dbuser' => '',
    'dbpass' => '',
    'dbname' => '',
    'dbport' => 3306,
    'dbhost' => 'localhost',

    'db_backup_user' => 'backup',
    'db_backup_password' => '',   
    
    'queue_host' => "localhost",
    'queue_port' => 22133,
       
    'cache_backend' => "Cache_Database",

    'admin_email' => "admin@localhost",    
    'reply_email' => "reply@localhost",
    'email_from' => "web@localhost",
    'email_pass' => "",    
    
    'sphinx_host' => 'localhost',  
    'sphinx_port' => 9312,    
    'sphinx_conf_dir' => '/usr/local/etc',
    'sphinx_bin_dir' => '/usr/local/bin',
    'sphinx_log_dir' => '/var/log/sphinx',
    'sphinx_pid_dir' => '/var/run/sphinx',
        
    'google_analytics_id' => "",
    'analytics_enabled' => false,
    
    'error_emails_enabled' => false,
    'ssl_enabled' => false,

    'storage_backend' => 'Storage_Local',

    'root' => $root,
    'dataroot' => dirname($root). "/envayadata",
        
    'languages' => array(
        'en' => 'English',
        'sw' => 'Kiswahili',
    ),
    'language' => "en",
    
    'sms_routes' => array(),
    
    'mock_sms_file' => null,
    
    'mail_backend' => 'Mail_SMTP',	
    'mock_mail_file' => null,
    
    'smtp_host' => "localhost",
    'smtp_port' => 25,
    'smtp_user' => '',
    'smtp_pass' => "",

    'cookie_domain' => null,
    'domain' => "localhost",
    'site_name' => "Envaya",

    'captcha_enabled' => false,
    
    'scribd_key' => '',
    'scribd_private' => '',

    'extract_images_from_docs' => false,
    
    'modules' => array('envaya_org','translate','contact','discussions','network'),
    
    'discussion_list_suffix' => '-list',
    'apps_domain' => '',
    'apps_admin' => '',
    'apps_password' => '',
    
    'geonames_user' => '',
    'default_timezone' => 'Africa/Dar_es_Salaam',
    
    'site_secret' => 'default_secret',
    'fallback_theme' => 'simple',
    
    'session_cookie_name' => 'envaya',
    
    'selenium_jar' => "selenium-server-standalone-2.0.0.jar",
    
    'cache_version' => 195,  // increment when all cached objects need to be invalidated (rare)
    
    'allow_robots' => true,    
    
    'subtype_aliases' => null, // map of old subtype_id => class name for database migrations
    
    'db_profile' => false,
    
    'twilio_account_sid' => '',
    'twilio_auth_token' => '',

    'contact_phone_number' => '',
    'news_phone_number' => '',
    
    'sendgrid_secret' => '',
    
    'available_countries' => array('tz','rw','lr'),
    'visible_countries' => array('tz','rw'),
    
    'mock_time_file' => null,
    
    'plupload_runtimes' => 'flash,html5,html4',
    'feed_page_size' => 20,
    
    'max_disk_pct' => 85,
    'status_email' => null,
    
    // crontab intervals (minutes)
    'notify_status_interval' => 1440,
    'notify_stuck_mail_interval' => 120,
    'check_system_interval' => 720,
    'backup_interval' => 720,
    'backup_s3_interval' => 720,
    'send_waiting_sms_interval' => 60,
    'check_external_feeds_interval' => 2,
    'check_sms_app_interval' => 60,
);