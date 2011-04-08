<?php
    $message = $vars['entity'];
    
    echo "<div id='msg{$message->guid}'>";
    
    if ($message->status == EntityStatus::Enabled)
    {   
        echo "<strong>";
        echo $message->get_from_link();
        if ($message->from_location)
        {
            echo " (".escape($message->from_location).")";        
        }
        echo "</strong>";    
        echo "<div class='blog_date'>". $message->get_date_text().    "</div>";    
        echo $message->render_content();
    }
    else
    {
        echo "<div class='comment_deleted'>".__('discussions:message_deleted_marker')."</div>";
    }
    echo "</div>";
?>
