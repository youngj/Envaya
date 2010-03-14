<?php

	$org_guid = get_input('org_guid');
	set_context('org');

	global $autofeed;
	$autofeed = false;
   
	$org = get_entity($org_guid);
	if ($org) 
    {
        global $CONFIG;
        $CONFIG->sitename = $org->name;        
    
		set_page_owner($org_guid);                        

        $viewOrg = false;
        
        if ($org->approval > 0)
        {
            //organization approved
        }
	    else if ($org->approval < 0)
	    {
            system_message(elgg_echo('org:rejected'));
        }
        else
        {
            system_message(elgg_echo('org:waitingapproval'));            
        }
        
        if (!$widget)
        {
            $widget = $org->getWidgetByName('home');
            $subtitle = $org->getLocationText();
            $title = '';    
        }
        else
        {
            $subtitle = elgg_echo("widget:{$widget->widget_name}");
            $title = $subtitle;    
        }
        
        add_submenu_item(elgg_echo("widget:edit"), "{$widget->getUrl()}/edit", 'b');                

        $body = elgg_view_layout('one_column', org_title($org, $subtitle), $widget->renderView());

        
	} else {
        forward("");
		$title = elgg_echo('org:notfound');
        $body = elgg_view_layout('one_column_padded', elgg_view_title($title), elgg_echo('org:notfound:details'));
	}

	page_draw($title, $body);
?>