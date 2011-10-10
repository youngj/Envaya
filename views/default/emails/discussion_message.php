<?php
    $message = $vars['message'];
    $user = $vars['user'];    

    ob_start();

    $topic = $message->get_container_entity();
    
    echo "<b>".escape($topic->subject)."</b><br />";    
    echo $message->render_content();
    echo "<div><a href='{$message->get_url()}'>{$message->get_url()}</a></div>";
    
    echo view('emails/notification_footer', array(
        'user' => $user,
        'notification_type' => Notification::Discussion
    ));
    
    $content = ob_get_clean();
    
    echo view('emails/html_layout', array('body' => $content));
