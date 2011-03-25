<?php
    $message = $vars['entity'];
    
    $name = escape($message->from_name);
    
    $owner = $message->get_owner_entity();
    
    if ($owner && $owner instanceof Organization)
    {
        $name = "<a href='{$owner->get_url()}'>$name</a>";
    }
        
    echo "<strong>$name</strong>";
    echo " <span class='blog_date'>". date('r', $message->time_posted) . "</span>";
    echo $message->render_content();
?>
