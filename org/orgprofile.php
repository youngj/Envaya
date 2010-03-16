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
            $viewOrg = true;
        }
	    else if ($org->approval < 0)
	    {
            system_message(elgg_echo('org:rejected'));
            $viewOrg = $org->canEdit();
        }
        else
        {
            system_message(elgg_echo('org:waitingapproval'));
            $viewOrg = $org->canEdit();
        }
        
        if (!$widget)
        {
            $widget = $org->getWidgetByName('home');
            $subtitle = $org->getLocationText(false);
            $title = '';    
        }
        else
        {
            $subtitle = elgg_echo("widget:{$widget->widget_name}");
            $title = $subtitle;    
        }
        
        if ($org->canEdit())
        {
            add_submenu_item(elgg_echo("widget:edit"), "{$widget->getUrl()}/edit", 'b');                
        }    

        $body = elgg_view_layout('one_column', org_title($org, $subtitle), $viewOrg ? $widget->renderView() : '', 
                (isadminloggedin() ? elgg_view("org/admin_box", array('entity' => $org)) : '')
        );


        page_draw($title, $body);        
	} 
    else 
    {    
        forward("");
		not_found();
	}
?>