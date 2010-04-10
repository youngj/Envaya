<?php
    $pageName = get_input('page_name');       
       
    if (preg_match('/[^\w]/', $pageName))
    {
        not_found();        
    }
    else
    {
        $area = elgg_view("page/$pageName");    
        if (!$area)
        {            
            not_found();            
        }
        else
        {
            $title = elgg_echo("$pageName:title");            
            $args = array('org_only' => (in_array($pageName, array('why'))));
            
            $body = elgg_view_layout('one_column_padded', elgg_view_title($title, $args), $area);        
            page_draw($title, $body);
        }    
    }    