<?php
    $sms = $vars['sms'];
    
    echo view('input/post_link', array(
        'href' => "/admin/resend_sms?id={$sms->id}",
        'confirm' => __('areyousure'),
        'text' => ($sms->status == OutgoingSMS::Waiting) ? __('send') : __('resend'),
    ));
