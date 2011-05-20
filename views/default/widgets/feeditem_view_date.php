<?php
    $widget = $vars['widget'];
    $is_primary = $vars['is_primary'];

    echo "<div class='blog_date'>";            
    $date_text = strtr(__('date:date_feed'), array(
        '{date}' => $widget->get_date_text(),
        '{feed}' => escape($widget->get_feed_name()),
    ));    
    $link = escape($widget->get_metadata('link') ?: $widget->get_url());
    echo "<a href='{$link}'>{$date_text}</a>";
    echo "</div>";        
    