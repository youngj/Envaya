<?php

    if (get_input('login'))
    {
        gatekeeper();
    }

	$org_guid = get_input('org_guid');
    set_context('orgprofile');

	global $autofeed;
	$autofeed = false;
   
	$org = get_entity($org_guid);
	if ($org && ($org instanceof Organization)) 
    {
        global $CONFIG;
        $CONFIG->sitename = $org->name;        
    
		set_page_owner($org_guid);
        set_theme(get_input("__theme") ?: $org->theme ?: 'green');

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
            add_submenu_item(elgg_echo("widget:edit"), $widget->getEditURL(), 'edit');
        }    

        if (get_input("__topbar") != "0")
        {                                
            $org->showCantViewMessage();        

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
        }    
        
        if ($viewOrg)
        {        
            $body = elgg_view_layout('one_column', org_title($org, $subtitle), ($viewOrg ? $widget->renderView() : ''), $preBody);
        }
        else
        {            
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