<?php
    $user = $vars['user'];
    
    if (!$user)
    {
        $user = new User();        
        $user->name = "{{name}}";
        $user->username = "{{username}}";
        $user->email = "{{email}}";
    }
    
    $email = $vars['email'];
    $head = '';
    
    if (@$vars['base'])
    {
        $head .= "<base href='{$vars['base']}' />";
    }
    
    $body = $email->render_content($user).
            view('emails/notification_footer', array(
                'user' => $user,
                'notification_type' => Notification::Batch
            )); 
    
    echo view('emails/html_layout', array('head' => $head, 'body' => $body));    