<?php

require_once 'start.php';
require_once 'scripts/cmdline.php';

$time = timestamp();

$waiting_smses = OutgoingSMS::query()
    ->where('time_sendable <= ?', $time)
    ->where('status = ?', OutgoingSMS::Waiting)
    ->filter();

foreach ($waiting_smses as $waiting_sms)
{
    $waiting_sms->enqueue();
    error_log("enqueued SMS {$waiting_sms->id}");
}
