<?php
    gatekeeper();
    set_theme('editor');
    set_context('editor');

    $org = page_owner_entity() ?: get_loggedin_user();

	if (!($org instanceof Organization))
	{
		not_found();
	}
	if ($org->canEdit())
	{
		$title = elgg_echo("help:title");
		$area = elgg_view("org/help", array('org' => $org));
		$body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);
		page_draw($title, $body);
	}
	else
	{
		force_login();
	}
