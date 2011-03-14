<?php

require_once("scripts/cmdline.php");
require_once("engine/start.php");

$widgets = Widget::query()->where('widget_name = ?', 'partnerships')->filter();

foreach ($widgets as $widget)
{
    echo "{$widget->get_url()}\n";
    $widget->widget_name = 'network';
    $widget->save();
}

$partnerships = Partnership::query()->filter();
foreach ($partnerships as $partnership)
{
    echo "{$partnership->get_container_entity()->get_url()} + {$partnership->get_partner()->get_url()}\n";

    $relationship = new OrgRelationship();
    
    $relationship->container_guid = $partnership->container_guid;    
    $relationship->type = OrgRelationship::Partnership;
    $relationship->content = $partnership->description;
    $relationship->language = $partnership->language;    
    $relationship->subject_guid = $partnership->partner_guid;
    $relationship->subject_name = $partnership->get_partner()->name;
    $relationship->approval = $partnership->approval;    
    
    $relationship->save();    
    
    $feedItems = FeedItem::query()
        ->where('action_name = ?', 'partnership')
        ->where('user_guid = ?', $partnership->container_guid)
        ->where('subject_guid = ?', $partnership->partner_guid)
        ->filter();
    foreach ($feedItems as $feedItem)
    {
        echo "   feed item {$feedItem->id}\n";
    
        $feedItem->action_name = 'relationship';
        $feedItem->subject_guid = $relationship->guid;
        $feedItem->save();
    }
    
    echo "Deleting partnership {$partnership->guid}\n";
    $partnership->delete();
}