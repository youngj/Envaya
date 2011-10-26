<?php 
    $template = $vars['template'];
    $subscriptions = $vars['subscriptions'];
    
    ob_start();
    
    echo view('admin/preview_email_template', array(
        'template' => $template, 
        'subscription' => $subscriptions[0]
    ));
    echo view('admin/email_template_statistics', array('template' => $template));
    
    $content = ob_get_clean();
    
    echo view('admin/send_template', array(
        'template' => $template,
        'subscriptions' => $subscriptions,
        'content' => $content        
    ));