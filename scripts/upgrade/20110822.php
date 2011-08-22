<?php

    require_once("scripts/cmdline.php");
    require_once("start.php");
    
    function is_country_feed($name)
    {
        return strpos($name, 'country') !== false;
    }
    
    $items = FeedItem::query()
        ->where('feed_name = ?', '')
        ->order_by('id asc')
        //->limit(10)
        ->filter();
        
    foreach ($items as $item)
    {
        $user = $item->get_user_entity();
        if (!$user)
        {
            continue;
        }
        $country_feed_names = array_filter($user->get_feed_names(), is_country_feed);
        
        foreach ($country_feed_names as $country_feed_name)
        {
            if (FeedItem::query()->where('feed_name = ?', $country_feed_name)
                ->where('user_guid = ?', $user->guid)
                ->where('time_posted = ?', $item->time_posted)
                ->exists())
            {
                continue;
            }
                
            $newitem = new FeedItem();
            $newitem->feed_name = $country_feed_name;
            $newitem->action_name = $item->action_name;
            $newitem->subject_guid = $item->subject_guid;
            $newitem->user_guid = $item->user_guid;
            $newitem->args = $item->args;
            $newitem->time_posted = $item->time_posted;
            $newitem->save();
            echo "{$item->id} => {$newitem->id} {$country_feed_name}\n";
        }        
    }
        
        