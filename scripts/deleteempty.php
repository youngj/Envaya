<?php

include("scripts/cmdline.php");
include("engine/start.php");

foreach (Widget::query()->where("(widget_name='history' OR widget_name='team' or widget_name='projects')")->where("content=''")->filter() as $widget)
{
    echo "{$widget->guid}\n";
    $widget->disable();
    $widget->save();
}

foreach (Widget::query()->where("(widget_name='partnerships')")->where('NOT EXISTS (select * from partnerships where partner_guid = e.container_guid)')->filter() as $widget)
{
    echo "{$widget->guid}\n";
    $widget->disable();
    $widget->save();
    
}