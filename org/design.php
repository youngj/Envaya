<?php
    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);

    set_theme('editor');
    set_context('editor');

    if ($org && $org->canEdit())
    {
        $cancelUrl = get_input('from') ?: $org->getUrl();

        add_submenu_item(elgg_echo("canceledit"), $cancelUrl, 'edit');

        $title = elgg_echo("design:edit");
        $area1 = elgg_view("org/design", array('entity' => $org));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);

        page_draw($title,$body);
    }
    else if ($org)
    {
    	force_login();
    }
    else
    {
		not_found();
    }