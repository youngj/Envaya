<?php
    $widget = $vars['widget'];
    $is_primary = @$vars['is_primary'];        

    if ($widget->title)
	{
        if ($is_primary)
        {
            echo "<h3>".escape($widget->title)."</h3>";
        }
        else
        {
            echo "<h3><a href='{$widget->get_url()}'>".escape($widget->title)."</a></h3>";
        }
	}
