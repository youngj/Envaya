<?php
    $message = $vars['message'];

    $topic = $message->get_container_entity();
    
    echo "<b>".escape($topic->subject)."</b><br />";    
    echo $message->render_content();

    $message_url = secure_url($message->get_url());

    echo "<div><a href='{$message_url}'>{$message_url}</a></div>";
    
