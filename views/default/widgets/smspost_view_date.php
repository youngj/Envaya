<?php
    $widget = $vars['widget'];
    echo "<div class='blog_date'>";            
    $date_text = strtr(__('date:date_feed'), array(
        '{date}' => $widget->get_date_text(),
        '{feed}' => 'SMS',
    ));    
    $link = $widget->get_url();
    echo "<a href='{$link}'>{$date_text}</a>";
    echo "</div>";        
    