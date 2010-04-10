<?php

include(dirname(__FILE__)."/engine/start.php");


$lang = 'sw';

load_translation($lang);

$key = get_input('key');

if ($key)
{
    $title = elgg_echo("trans:item_title");    
    $body = elgg_view_layout("one_column_padded", elgg_view_title($title), elgg_view("translation/interface_item", array('lang' => $lang, 'key' => $key)));
    page_draw($title, $body);
}
else if (get_input('export'))
{
    header("Content-type: text/plain");
    echo elgg_view("translation/interface_export", array('lang' => $lang));
}
else
{
    $title = elgg_echo("trans:list_title");
    $body = elgg_view_layout("one_column_wide", elgg_view_title($title), elgg_view("translation/interface_list", array('lang' => $lang)));
    page_draw($title, $body);
}