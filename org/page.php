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
            $heading = elgg_view('page/simpleheading', array(
                'org_only' => (in_array($pageName, array('why'))), 
                'title' => $title
            ));
            $body = elgg_view_layout('one_column_padded', $heading, $area);        
            page_draw($title, $body);
        }    
    }    