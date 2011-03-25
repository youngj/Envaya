<?php
    $topic = $vars['entity'];
        
    echo "<div>";
               
    echo "<a href='{$topic->get_url()}'>".escape($topic->subject)." ({$topic->num_messages})</a>";
       
    if ($topic->num_messages)
    {
        echo " <span class='blog_date'>";
        echo friendly_time($topic->last_time_posted)." by ".escape($topic->last_from_name);
        echo "</span>";
    }
    
    echo "<div class='feed_snippet'>{$topic->snippet}</div>";
    
            
    echo "</div>";