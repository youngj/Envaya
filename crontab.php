<?php

require_once "scripts/cmdline.php";

// can also define crontab.php in modules, with same format as this file

return array(
    array(
        'interval' => Config::get('backup_interval'), // minutes
        'cmd' => "php scripts/backup.php"
    ),
    array(
        'interval' => Config::get('notify_stuck_mail_interval'),
        'cmd' => "php scripts/notify_stuck_mail.php"
    ),   
    array(
        'interval' => Config::get('backup_s3_interval'),
        'cmd' => "php scripts/backup_s3.php"
    ),
    array(
        'interval' => Config::get('send_waiting_sms_interval'),
        'cmd' => "php scripts/send_waiting_sms.php"
    ),
    array(
        'interval' => Config::get('check_sms_app_interval'),
        'cmd' => "php scripts/check_sms_app.php"
    ),    
    array(
        'interval' => Config::get('check_system_interval'),
        'cmd' => "php scripts/check_system.php"
    ),
    array(
        'interval' => Config::get('notify_status_interval'),
        'cmd' => "php scripts/notify_status.php"
    ),    
    array(
        'interval' => Config::get('scheduled_events_interval'),
        'cmd' => "php scripts/scheduled_events.php"
    ),
    array(
        'interval' => Config::get('check_external_feeds_interval'),
        'cmd' => "php scripts/check_external_feeds.php"
    )
);