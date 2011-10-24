<?php
    $message = $vars['message'];

    $topic = $message->get_container_entity();
    
    echo "<b>".escape($topic->subject)."</b><br />";    
    echo $message->render_content();
    echo "<div><a href='{$message->get_url()}'>{$message->get_url()}</a></div>";
    
