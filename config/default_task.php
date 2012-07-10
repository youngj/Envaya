<?php 

return array(    
    // crontab intervals (minutes)
    'task:notify_status_interval' => 1440,
    'task:notify_stuck_mail_interval' => 120,
    'task:check_system_interval' => 720,
    'task:backup_interval' => 720,
    'task:backup_s3_interval' => 720,
    'task:send_waiting_sms_interval' => 60,
    'task:check_external_feeds_interval' => 2,
    'task:check_sms_app_interval' => 60,
    'task:scheduled_events_interval' => 720,
    
    'task:max_disk_pct' => 85,
    
    'task:db_backup_user' => 'backup',
    'task:db_backup_password' => '',   
    'task:db_backup_host' => 'localhost',
    'task:s3_backup_bucket' => '',
    
);