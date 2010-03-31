<?php

gatekeeper();
set_theme('editor');
set_context('editor');

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
    $cancelUrl = get_input('from') ?: $widget->getUrl();

    add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');

    $body = elgg_view_layout('one_column', 
        elgg_view_title($title), $widget->renderEdit());    
}
else 
{
    register_error(elgg_echo("org:cantedit"));
    forward_to_referrer();
}

page_draw($title, $body);