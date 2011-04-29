<?php
    $widget = $vars['widget'];
    $is_primary = @$vars['is_primary'];        

    if (!$is_primary && $widget->title)
	{
        echo "<h3><a href='{$widget->get_url()}'>".escape($widget->title)."</a></h3>";
	}

    echo $widget->render_content();
    
    echo "<div style='clear:both'></div>";
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
    echo "</div>";    

    if (!$is_primary)
    {
        echo view('widgets/comment_link', $vars);
    }
    else
    {
        echo view('widgets/post_nav', $vars);    
    }