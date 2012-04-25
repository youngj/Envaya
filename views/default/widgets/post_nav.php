<?php
    $widget = $vars['widget'];

    $news = $widget->get_container_entity();
    $url = $widget->get_url();
    
    $has_prev = $news->query_published_widgets()
        ->where('tid < ?', $widget->tid)
        ->exists();    
        
    $has_next = $news->query_published_widgets()
        ->where('tid > ?', $widget->tid)
        ->exists();
    
    if ($has_prev || $has_next)
    {        
        echo "<div class='post_nav'>";
    
        if ($has_prev)
        {
            echo "<a href='{$url}/prev' title='".__('previous')."' class='post_nav_prev'><span>&#xab; ".__('previous')."</span></a> ";
        }
        
        if ($has_next)
        {
            echo "<a href='{$url}/next' title='".__('next')."' class='post_nav_next'><span>".__('next')." &#xbb;</span></a>";
        }    
        
        echo "</div>";
    }