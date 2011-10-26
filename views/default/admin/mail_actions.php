<?php
    $mail = $vars['mail'];

    if ($mail->status == OutgoingMail::Held)
    {
        echo view('input/post_link', array(
            'href' => "/admin/resend_mail?id={$mail->id}",
            'confirm' => __('areyousure'),
            'text' => __('send'),
        ));        
        echo " &middot; ";
        echo view('input/post_link', array(
            'href' => "/admin/set_mail_status?id={$mail->id}&status=".OutgoingMail::Rejected,
            'text' => __('email:reject'),
        ));
    }
    else if ($mail->status == OutgoingMail::Rejected)
    {
        echo view('input/post_link', array(
            'href' => "/admin/set_mail_status?id={$mail->id}&status=".OutgoingMail::Held,
            'text' => __('email:unreject'),
        ));        
    }
    else
    {
        echo view('input/post_link', array(
            'href' => "/admin/resend_mail?id={$mail->id}",
            'confirm' => __('areyousure'),
            'text' => __('email:resend'),
        ));
    }
