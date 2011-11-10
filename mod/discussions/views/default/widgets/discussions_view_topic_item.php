<?php
    $topic = $vars['topic'];
        
    $url = $topic->get_url();        
    echo "<div><a class='discussionTopic' href='$url'>";
    echo "<span style='font-weight:bold'>".escape($topic->render_property('subject'))."</span>";
    
    if ($topic->num_messages > 1)
    {
        echo " ({$topic->num_messages})";
    }
    echo "<br /><span class='feed_snippet'>{$topic->snippet}</span>";    
    echo "<br />";
    
    if ($topic->num_messages)
    {
        echo "<span class='blog_date'>";
        
        $date = friendly_time($topic->last_time_posted);
        
        if ($topic->last_from_name)
        {
            echo strtr(__('date:date_name'), array(
                '{date}' => $date, 
                '{name}' => escape($topic->last_from_name)
            ));
        }
        else
        {
            echo $date;
        }
        echo "</span>";
    }    
            
    echo "</a></div>";