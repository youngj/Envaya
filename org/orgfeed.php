<?php

    $title = elgg_echo("feed:org");

	set_context('editor');
	set_theme('editor');

    page_set_translatable(false);

	$org = page_owner_entity();

	$feedNames = array();

	$area = elgg_view('org/orgfeed', array('org' => $org));

    $body = elgg_view_layout('one_column', elgg_view_title($title), $area);

    page_draw($title, $body);

