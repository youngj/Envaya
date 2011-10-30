<?php

$stuck_mails = $vars['stuck_mails'];

foreach ($stuck_mails as $stuck_mail)
{
    echo "{$stuck_mail->to_address} - {$stuck_mail->subject} - {$stuck_mail->get_status_text()}";
    
    if ($stuck_mail->has_error())
    {
        echo ": {$stuck_mail->error_message}";
    }
    echo "\n\n";
}
echo secure_url("/admin/outgoing_mail");
echo "\n";
