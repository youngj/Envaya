<?php
    $org = $vars['org'];

    echo view('search/listing', array(
        'icon' => view('org/icon', array('org' => $org)),
        'info' => escape($org->name).
            "<br />".
            "<span style='font-size:10px'>".
            escape($org->get_location_text()).
            "<br />".
            "<span class='search_url'>".$org->get_url()."</span>".
            "</span>"
            
    ));