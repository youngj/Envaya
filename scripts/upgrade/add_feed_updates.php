<?php

    require_once("scripts/cmdline.php"); 
    require_once("engine/start.php");

    $widgets = Widget::query()->where("content <> ''")->where("widget_name <> 'home'")->where('time_updated > time_created')->filter();

    foreach ($widgets as $widget)
    {
        $numFeedItems = FeedItem::query()->where('subject_guid = ?', $widget->guid)->where('time_posted > ?', $widget->time_updated - 86400)->count();
        if ($numFeedItems == 0)
        {
            echo "{$widget->get_url()}\n";
            FeedItem::post($widget->get_container_entity(), 'edit_widget', $widget, null, $widget->time_updated);
        }
    }

    $newsUpdates = NewsUpdate::query()->where('guid > 3240')->filter();
    foreach ($newsUpdates as $newsUpdate)
    {
        $numFeedItems = FeedItem::query()->where('subject_guid = ?', $newsUpdate->guid)->count();
        if ($numFeedItems == 0)
        {
            echo "{$newsUpdate->get_url()}\n";            
            FeedItem::post($newsUpdate->get_container_entity(), 'news', $newsUpdate, null, $newsUpdate->time_updated);
        }    
    }