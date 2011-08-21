<?php

require_once 'start.php';
require_once 'scripts/cmdline.php';

$time = time();
$last_reminder = (int)State::get('last_stuck_mail_reminder');

State::set('last_stuck_mail_reminder', $time);

$stuck_mails = OutgoingMail::query()
    ->where('time_created >= ?', $last_reminder)
    ->where('status = ? OR status = ? OR status = ? OR (status = ? AND time_queued < ?)',
        OutgoingMail::Held, OutgoingMail::Failed, OutgoingMail::Bounced, OutgoingMail::Queued, $time - 60
    )->filter();

if ($stuck_mails)
{
    $mail = OutgoingMail::create(__('email:stuck_reminder'),
        view('emails/stuck_mail_reminder', array(
            'stuck_mails' => $stuck_mails,
        ))
    );
    $mail->send_to_admin();
    echo "sent stuck mail reminder\n";
}    
