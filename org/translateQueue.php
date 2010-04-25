<?php

admin_gatekeeper();

$title = elgg_echo('translate:queue');

set_theme('editor');

$body = elgg_view_layout("one_column_padded", elgg_view_title($title), 
    elgg_view('org/translateQueue', array('lang' => get_language()))
);            

page_draw($title,$body);

?>