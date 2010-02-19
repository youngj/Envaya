<?php
	$title = elgg_echo("org:new");	
    $body = elgg_view_layout('one_column', elgg_view_title($title), elgg_view("org/addOrg"));	
	page_draw($title, $body);
?>