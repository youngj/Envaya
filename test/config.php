<?php

/* 
 * Overridden values of config settings for selenium tests.
 * Selenium tests use a separate domain and data store from the normal development environment.
 */

require_once dirname(__DIR__)."/start.php";
 
$dataroot = Config::get('dataroot').'/test_data';

return array(        
    'storage:plupload_runtimes' => 'html4',
    
    'mail:sendgrid_secret' => 'flksflk312',
    'mail:admin_email' => 'nobody@envaya.org',
    
    'task:notify_status_interval' => 0,
    'task:notify_stuck_mail_interval' => 0,
    'task:check_system_interval' => 0,
    'task:backup_interval' => 0,
    'task:backup_s3_interval' => 0,    
    'task:check_external_feeds_interval' => 0,
    'task:check_sms_app_interval' => 0,
    'task:send_waiting_sms_interval' => 0,
    
    'captcha:backend' => 'Captcha_Mock',
    'ssl_enabled' => false,    
    
    'sms:routes' => array(
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
    
    'time:mock_file' => "$dataroot/time.txt",
    'sms:mock_file' => "$dataroot/sms.out",
    'test:contact_phone_number' => '14845551212',
    'test:news_phone_number' => '14845551213',    
    'mail:backend' => "Mail_Mock",
    'mail:mock_file' => "$dataroot/mail.out",
    'domain' => '127.0.0.1:3001',
    'dataroot' => $dataroot,        
    'amqp:vhost' => '/envaya-test',
    'sphinx:port' => 9313,    
    'sphinx:conf_dir' => $dataroot,
    'sphinx:log_dir' => $dataroot,
    'sphinx:pid_dir' => $dataroot,
    'db:name' => 'envaya_test',
    'feed:page_size' => 6,
    'debug:media' => false,
    'debug' => true,
	'debug:db_profile' => false,
);