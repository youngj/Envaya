<?php

    $widget = $vars['widget'];

    echo view('section', array('header' => __("org:mission"), 'content' => $widget->render_content()));

    $org = $vars['widget']->get_container_entity();

    echo "<div class='section_header'>".__("widget:news:latest")."</div>";

    $items = $org->query_feed_items()->limit(6)->filter();

    echo "<div class='section_content'>";
    echo view('feed/list', array('items' => $items, 'mode' => 'self'));
    echo "</div>";

    $sectors = $org->get_sectors();

    if (!empty($sectors))
    {
        echo view('section', array(
            'header' => __("org:sectors"), 
            'content' => view("org/sectors", array('sectors' => $sectors, 'sector_other' => $org->sector_other)))
        );    
    }
    
    echo view('section', array('header' => __("org:location"), 
        'content' => view('org/location', array('org' => $org, 'zoom' => $widget->zoom))
    ));
?>
