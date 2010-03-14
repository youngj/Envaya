<?php

gatekeeper();

$widgetTitle = elgg_echo("widget:{$widget->widget_name}");

if ($widget->guid && $widget->isEnabled())
{
    $title = sprintf(elgg_echo("widget:edittitle"), $widgetTitle);
}
else
{
    $title = sprintf(elgg_echo("widget:edittitle:new"), $widgetTitle);
}

if ($org->canEdit())
{
    $body = elgg_view_layout('one_column', 
        elgg_view_title($title), $widget->renderEdit());    
}
else 
{
    $body = elgg_view('org/contentwrapper',array('body' => elgg_echo('org:noaccess')));
}

page_draw($title, $body);