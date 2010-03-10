<?php

    $org_guid = get_input("org_guid");
    $org = get_entity($org_guid);
    set_page_owner($org_guid);

    $area2 = $org->listNewsUpdates(10, true);

    $title = elgg_echo('org:updates');

    $body = elgg_view_layout("one_column_padded", org_title($org, $title), $area2);

    page_draw($title,$body);
        
?>