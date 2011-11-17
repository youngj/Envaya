<?php

require_once "scripts/cmdline.php";

// can also define crontab.php in modules, with same format as this file

return array(
    array(
        'interval' => Config::get('task:backup_interval'), // minutes
        'cmd' => "php scripts/backup.php"
    ),
    array(
        'interval' => Config::get('task:notify_stuck_mail_interval'),
        'cmd' => "php scripts/notify_stuck_mail.php"
    ),   
    array(
        'interval' => Config::get('task:backup_s3_interval'),
        'cmd' => "php scripts/backup_s3.php"
    ),
    array(
        'interval' => Config::get('task:send_waiting_sms_interval'),
        'cmd' => "php scripts/send_waiting_sms.php"
    ),
    array(
        'interval' => Config::get('task:check_sms_app_interval'),
        'cmd' => "php scripts/check_sms_app.php"
    ),    
    array(
        'interval' => Config::get('task:check_system_interval'),
        'cmd' => "php scripts/check_system.php"
    ),
    array(
        'interval' => Config::get('task:notify_status_interval'),
        'cmd' => "php scripts/notify_status.php"
    ),    
    array(
        'interval' => Config::get('task:scheduled_events_interval'),
        'cmd' => "php scripts/scheduled_events.php"
    ),
    array(
        'interval' => Config::get('task:check_external_feeds_interval'),
        'cmd' => "php scripts/check_external_feeds.php"
    )
);