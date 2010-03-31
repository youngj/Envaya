<?php
    gatekeeper();
    
    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);
    $partner_guid = get_input('partner_guid');
    $partner = get_entity($partner_guid);
        
    set_theme('editor');
    set_context('editor');
    
    if ($org && $partner) 
    {                   
        if (!$org->canEdit())
        {            
            logout();
            forward($_SERVER['REQUEST_URI']);
        }
    
        $partnership = $org->getPartnership($partner);
        if ($partnership->isSelfApproved() || !$partnership->isPartnerApproved())
        {
            not_found();
        }
    
        $title = elgg_echo("partner:confirm");
        $area1 = elgg_view("org/confirmPartner", array('entity' => $org, 'partner' => $partner));
        $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);                
        page_draw($title,$body);
    }
    else 
    {
        not_found();
    }
        