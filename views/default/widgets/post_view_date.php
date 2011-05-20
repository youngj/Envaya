<?php
    $widget = $vars['widget'];
    $is_primary = @$vars['is_primary'];        

    echo "<div class='blog_date'>";        
    $date_text = $widget->get_date_text();    
    
    if (!$is_primary)
    {
        echo "<a href='{$widget->get_url()}'>{$date_text}</a>";
    }
    else
    {
        echo $date_text;
    }
    
    if ($widget->publish_status == Widget::Draft)
    {
        echo " (".__('widget:draft').")";
    }
    
    echo "</div>";    
