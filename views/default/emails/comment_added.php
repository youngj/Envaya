<?php
    $user = @$vars['user'];
    $comment = $vars['comment'];
    
    $widget = $comment->get_container_entity();
    $comments_url = $widget->get_url()."?comments=1#comments";                        
    
    ob_start();   
    
    echo $comment->render_content();
    echo "<div><a href='$comments_url'>$comments_url</a></div>";
    echo view('emails/notification_footer', array(
        'user' => $user, 
        'notification_type' => Notification::Comments
    ));    
    
    $content = ob_get_clean();
  
    echo view('emails/html_layout', array('body' => $content));