<?php
    $widget = $vars['widget'];

    $news = $widget->get_container_entity();
    $url = rewrite_to_current_domain($widget->get_url());
           
    $has_prev = $news->query_widgets()
        ->where('status = ?', EntityStatus::Enabled)
        ->where('guid < ?', $widget->guid)
        ->exists();    
        
    $has_next = $news->query_widgets()
        ->where('status = ?', EntityStatus::Enabled)
        ->where('guid > ?', $widget->guid)
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