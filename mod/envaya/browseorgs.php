<?php

    global $CONFIG;
     
    $body = list_entities("user","organization",0,10,false);
   
    page_draw(elgg_echo("org:list_all"), elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("org:list_all")), $body));
     
    
?>