<?php

    admin_gatekeeper();
    
    set_context('editor');
    set_theme('editor');
    
    $org = get_entity(get_input('org_guid'));
    
    $title = elgg_echo('username:title');
        
    $area1 = elgg_view('org/changeUsername', array('org' => $org));
    
    $body = elgg_view_layout("one_column", elgg_view_title($title), $area1);        
            
    page_draw($title,$body);          
