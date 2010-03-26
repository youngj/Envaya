<?php

    if (get_input('login'))
    {
        gatekeeper();
    }

	$org_guid = get_input('org_guid');
	set_context('org');

	global $autofeed;
	$autofeed = false;
   
	$org = get_entity($org_guid);
	if ($org && ($org instanceof Organization)) 
    {
        global $CONFIG;
        $CONFIG->sitename = $org->name;        
    
		set_page_owner($org_guid);
        set_theme($org->theme);

        $viewOrg = $org->canView();
                
        if (!$widget)
        {
            $widget = $org->getWidgetByName('home');
            $subtitle = $org->getLocationText(false);
            $title = '';    
        }
        else if (!$widget->isActive())
        {
            not_found();
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
        
        $org->showCantViewMessage();
        
        if ($viewOrg)
        {        
            $body = elgg_view_layout('one_column', org_title($org, $subtitle), $viewOrg ? $widget->renderView() : '');
        }
        else
        {            
            $body = '';
        }
        
        $preBody = '';
        
        if (isadminloggedin())
        {
            $preBody .= elgg_view("org/admin_box", array('entity' => $org));
        }
        
        if ($org->canCommunicateWith())
        {
            $preBody .= elgg_view("org/comm_box", array('entity' => $org));
        }
                
        if ($org->guid == get_loggedin_userid() && $org->approval == 0)
        {
            $preBody .= elgg_view("org/setupNextStep");
        }


        page_draw($title, $body, $preBody);        
	} 
    else 
    {    
        forward("");
		not_found();
	}
?>