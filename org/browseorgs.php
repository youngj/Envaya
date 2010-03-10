<?php
                      
        add_submenu_item(elgg_echo('browse:map'), $CONFIG->wwwroot. "pg/org/browse");
        add_submenu_item(elgg_echo('browse:list'), $CONFIG->wwwroot. "pg/org/browse?list=1");                      
                      
        $title = elgg_echo("browse:title");
        
        if (get_input("list"))
        {
            $area = elgg_view("org/browseList", array('lat' => $lat, 'long' => $long));
        }
        else
        {        
            $lat = get_input('lat');
            $long = get_input('long');

            $area = elgg_view("org/browseMap", array('lat' => $lat, 'long' => $long));
        }    
               
        
        $body = elgg_view_layout('one_column_padded', elgg_view_title($title), $area);    
        
        page_draw($title, $body);

?>