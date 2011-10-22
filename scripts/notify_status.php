<?php

require_once 'start.php';
require_once 'scripts/cmdline.php';

$email = Config::get('status_email');

if ($email)
{
    $mail = OutgoingMail::create("Envaya Status",
        "Last Database Backup: " . friendly_time(State::get('backup_time')) . "\n" .
            "     ". State::get('backup_info'). "\n\n" .
        "Last S3 Backup: " .         friendly_time(State::get('s3_backup_time')) . "\n" .
            "     ". State::get('s3_backup_info') . "\n\n"
    );
    
    $mail->add_to($email);    
    $mail->send();
}
