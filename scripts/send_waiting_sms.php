<?php

require_once 'start.php';
require_once 'scripts/cmdline.php';

$time = timestamp();

$waiting_smses = SMS::query()
    ->where('time_sendable <= ?', $time)
    ->where('status = ?', SMS::Waiting)
    ->filter();

foreach ($waiting_smses as $waiting_sms)
{
    $waiting_sms->enqueue();
    error_log("enqueued SMS {$waiting_sms->id}");
}
