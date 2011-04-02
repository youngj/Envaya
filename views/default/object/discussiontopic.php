<?php
    $topic = $vars['entity'];
        
    $url = $topic->get_url();        
    echo "<a class='discussionTopic' href='$url'>";
    
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
        $format = __('discussions:topic_time_name');
        echo sprintf($format, friendly_time($topic->last_time_posted), escape($topic->last_from_name));
        echo "</span>";
    }    
            
    echo "</a>";