<?php
    $message = $vars['message'];

    $topic = $message->get_container_entity();
    echo "<b>".escape($topic->subject)."</b><br /><br />";
    
    echo $message->render_content();
    echo "<br /><br />";
    echo "<a href='{$message->get_url()}'>{$message->get_url()}</a>";
    echo "<br />";