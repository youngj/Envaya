<?php
    require_once "start.php";      
    
    $widgets = Widget::query()
        ->where('content like ? or thumbnail_url like ?', '%src="http://%', 'http://%')
        ->filter();
    
    foreach ($widgets as $widget)
    {
        error_log($widget->get_url());
        $widget->content = Markup::remove_image_scheme($widget->content);
        $widget->thumbnail_url = str_replace("http://", "//", $widget->thumbnail_url);
        $widget->save();
    }
    