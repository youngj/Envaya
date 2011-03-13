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
