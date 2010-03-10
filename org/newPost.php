<?php

    gatekeeper();
        
    $org_guid = get_input('org_guid');
    $org = get_entity($org_guid);
    set_page_owner($org_guid);

    $area1 = elgg_view("org/editPost", array('container_guid' => $org_guid));
    $title = elgg_echo('blog:addpost');
            
    page_draw($title,
        elgg_view_layout("one_column", 
            org_title($org, $title),
            $area1
        )
    );

?>