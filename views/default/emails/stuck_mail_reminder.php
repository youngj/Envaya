<?php

$stuck_mails = $vars['stuck_mails'];

foreach ($stuck_mails as $stuck_mail)
{
    echo "{$stuck_mail->to_address} - {$stuck_mail->subject} - {$stuck_mail->get_status_text()}\n\n";
}
echo Config::get('url')."admin/outgoing_mail\n";