<?php

class ExternalFeed_RSS extends ExternalFeed
{
    function update()
    {
        Zend::load('Zend_Feed');
        
        $container = $this->get_container_entity();
        if (!$container || !$container->is_enabled())
            return;
        
        $feed = Zend_Feed::import($this->url);
        
        foreach ($feed as $entry)
        {
            $widget_name = substr($entry->guid, 0, 127);
        
            $rss_item = $container->get_widget_by_name($widget_name, 'RSSItem');
            echo "{$entry->guid}; {$rss_item->guid}\n";
            if ($rss_item->guid)
                continue;
                
            
            $rss_item->title = $entry->title;
            $rss_item->time_published = strtotime($entry->pubDate);
            $rss_item->set_content($entry->description);
            $rss_item->set_metadata('link', $entry->link);
            $rss_item->save();
        }
    }
}