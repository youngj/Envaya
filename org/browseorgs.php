<?php
                                            
        $title = elgg_echo("browse:title");       
        $sector = get_input('sector');
        
        if (get_input("list"))
        {
            $area = elgg_view("org/browseList", array('lat' => $lat, 'long' => $long, 'sector' => $sector));
        }
        else
        {        
            $lat = get_input('lat');
            $long = get_input('long');
            $zoom = get_input('zoom');

            $area = elgg_view("org/browseMap", array('lat' => $lat, 'long' => $long, 'zoom' => $zoom, 'sector' => $sector));
        }    
                       
        $body = elgg_view_layout('one_column', elgg_view_title($title), $area);    
        
        page_draw($title, $body);

?>