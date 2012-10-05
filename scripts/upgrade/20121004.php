<?php
    require_once "start.php";      
   
    foreach (Widget_Home::query()->filter() as $home)
    {
        $first_widget = $home->query_menu_widgets()->get();    
        
        if ($first_widget instanceof Widget_Mission)
        {
            error_log($home->guid);
            
            $home->content = $first_widget->content;
            $home->language = $first_widget->language;
            $home->thumbnail_url = $first_widget->thumbnail_url;
            $home->time_updated = max($home->time_updated, $first_widget->time_updated);
            $home->save();
            
            $count = Database::update("DELETE FROM translation_keys WHERE container_guid = ?", [$home->guid]);            
            
            $count = Database::update("UPDATE translation_keys SET container_guid = ?, name = ? WHERE name = ?", 
                [$home->guid, "{$home->guid}:content", "{$first_widget->guid}:content"]);            
                
            if ($count > 0)
            {
                error_log("$count translations updated");
            }
            
            $first_widget->delete();
        }
        else
        {
            error_log("{$home->get_url()} does not have mission first");
        }
    }
    