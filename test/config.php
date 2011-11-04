<?php

/* 
 * Overridden values of config settings for selenium tests.
 * Selenium tests use a separate domain and data store from the normal development environment.
 */

require_once dirname(__DIR__)."/engine/config.php";
Config::load();
 
$dataroot = Config::get('dataroot').'/test_data';

return array(        
    'plupload_runtimes' => 'html4',
    
    'sendgrid_secret' => 'flksflk312',
    'admin_email' => 'nobody@envaya.org',
    
    'notify_status_interval' => 0,
    'notify_stuck_mail_interval' => 0,
    'check_system_interval' => 0,
    'backup_interval' => 0,
    'backup_s3_interval' => 0,    
    'check_external_feeds_interval' => 0,
    'check_sms_app_interval' => 0,
    'send_waiting_sms_interval' => 0,
    
    'captcha_enabled' => false,
    'ssl_enabled' => false,    
    
    'sms_routes' => array(
        array(
            'service' => 'SMS_Service_News',
            'remote_numbers' => '.*',
            'self_number' => '14845551213',
            'provider' => 'SMS_Provider_Mock',
        ),
        array(
            'service' => 'SMS_Service_Contact',
            'remote_numbers' => '.*',
            'self_number' => '14845551212',
            'provider' => 'SMS_Provider_Mock',
        ),
    ),    
    
    'mock_time_file' => "$dataroot/time.txt",
    'mock_sms_file' => "$dataroot/sms.out",
    'contact_phone_number' => '14845551212',
    'news_phone_number' => '14845551213',    
    'mail_backend' => "Mail_Mock",
    'mock_mail_file' => "$dataroot/mail.out",
    'domain' => 'localhost:3001',
    'queue_host' => 'localhost',
    'queue_port' => 22134,
    'sphinx_port' => 9313,    
    'dataroot' => $dataroot,        
    'sphinx_conf_dir' => $dataroot,
    'sphinx_log_dir' => $dataroot,
    'sphinx_pid_dir' => $dataroot,
    'dbname' => 'envaya_test',
    'feed_page_size' => 6,
    'debug_media' => false,
    'debug' => true,
);