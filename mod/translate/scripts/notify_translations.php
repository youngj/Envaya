<?php

require_once 'start.php';
require_once 'scripts/cmdline.php';

$time = timestamp();
$last_notify = (int)State::get('notify_translations_time');

State::set('notify_translations_time', $time);

$translations = Translation::query()
    ->where('time_created >= ?', $last_notify)
    ->where('approval = 0')
    ->where('status = ?', Translation::Published)
    ->where('source = ?', Translation::Human)
    ->filter();

if ($translations)
{
    $mail = OutgoingMail::create(__('itrans:notify_translations_subject'),
        view('emails/notify_translations', array(
            'translations' => $translations,
        ))
    );
    $mail->send_to_admin();
    echo "sent translations notification\n";
}    
