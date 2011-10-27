<?php
    $message = $vars['message'];
    
    echo "<div id='msg{$message->guid}'>";
    
    if ($message->is_enabled())
    {   
        echo "<strong>";
        echo $message->get_from_link();
        if ($message->from_location)
        {
            echo " (".escape($message->from_location).")";        
        }
        echo "</strong>";    
        echo "<div class='blog_date'>". $message->get_date_text();

        if ($message->time_updated > $message->time_posted)
        {
            echo " ".strtr(__('date:edited'), array('{date}' => $message->get_date_text($message->time_updated)));
        }
        
        echo "</div>";    
        echo "<div class='message_content'>";
        echo $message->render_content();
        echo "</div>";
    }
    else
    {
        echo "<div class='comment_deleted'>[".__('discussions:message_deleted_marker')."]</div>";
    }
    echo "</div>";
?>
