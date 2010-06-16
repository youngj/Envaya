<?php
    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);

    set_theme('editor');
    set_context('editor');

    if ($org && $org->canEdit())
    {
    	if ($org->guid == get_loggedin_userid())
    	{
	        $title = elgg_echo("dashboard");
	    }
	    else
	    {
	    	$title = sprintf(elgg_echo("dashboard:other_user"), $org->name);
	    }

        $area1 = elgg_view("org/dashboard", array('org' => $org));
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