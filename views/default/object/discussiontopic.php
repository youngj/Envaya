<?php
    $topic = $vars['entity'];
        
    $url = $topic->get_url();        
    echo "<div><a class='discussionTopic' href='$url'>";
    
    echo "<span style='font-weight:bold'>".escape($topic->translate_field('subject'))."</span>";
    
    if ($topic->num_messages > 1)
    {
        echo " ({$topic->num_messages})";
    }
    
    echo " <span class='feed_snippet'>{$topic->snippet}</span>";    
    echo "<br />";
    
    if ($topic->num_messages)
    {
        echo "<span class='blog_date'>";
        echo strtr(__('date:date_name'), array(
            '{date}' => friendly_time($topic->last_time_posted), 
            '{name}' => escape($topic->last_from_name)
        ));
        echo "</span>";
    }    
            
    echo "</a></div>";