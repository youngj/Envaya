<?php

gatekeeper();

$title = sprintf(elgg_echo("widget:edittitle"), elgg_echo("widget:{$widget->widget_name}"));
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