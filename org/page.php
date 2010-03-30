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
            $body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);        
            page_draw($title, $body);
        }    
    }    