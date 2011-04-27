<?php
    $topic = $vars['entity'];
        
    $url = $topic->get_url();        
    echo "<div><a class='discussionTopic' href='$url'>";
    
    echo escape($topic->subject);
    if ($topic->num_messages > 1)
    {
        echo " ({$topic->num_messages})";
    }
    
    echo " <span class='feed_snippet'>{$topic->snippet}</span>";    
    echo "<br />";
    
    if ($topic->num_messages)
    {
        echo "<span class='blog_date'>";
        echo strtr(__('discussions:topic_time_name'), array(
            '{time}' => friendly_time($topic->last_time_posted), 
            '{name}' => escape($topic->last_from_name)
        ));
        echo "</span>";
    }    
            
    echo "</a></div>";