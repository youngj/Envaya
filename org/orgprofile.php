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

        $viewOrg = $org->canView();
                
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
        
        if ($viewOrg)
        {
            $body = elgg_view_layout('one_column', org_title($org, $subtitle), $viewOrg ? $widget->renderView() : '', 
                    (isadminloggedin() ? elgg_view("org/admin_box", array('entity' => $org)) : '')
            );
        }
        else
        {
            $org->showCantViewMessage();
            $body = '';
        }


        page_draw($title, $body);        
	} 
    else 
    {    
        forward("");
		not_found();
	}
?>