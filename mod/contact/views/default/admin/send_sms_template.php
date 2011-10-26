<?php 
    $template = $vars['template'];
    $subscriptions = $vars['subscriptions'];
    
    ob_start();
    
    echo view('admin/preview_sms_template', array(
        'template' => $template,
        'subscription' => $subscriptions[0]));
    
    $content = ob_get_clean();
    
    echo view('admin/send_template', array(
        'template' => $template,
        'subscriptions' => $subscriptions,
        'content' => $content        
    ));