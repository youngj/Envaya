<?php

    global $CONFIG;
     
    $users = get_entities("user","organization",0,10,false);
   
    $body = elgg_view('extensions/entity_list',array(
        'entities' => $users
    ));

    page_draw(elgg_echo("org:browse"), elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("org:browse")), $body));
     
    
?>