<?php

    $widget = $vars['widget'];

    echo elgg_view_layout('section', __("org:mission"), $widget->renderContent());

    $org = $vars['widget']->getContainerEntity();

    echo "<div class='section_header'>".__("widget:news:latest")."</div>";

    $items = $org->getFeedItems(6);

    echo "<div class='section_content'>";

    echo elgg_view('feed/self_list', array('items' => $items));

    echo "</div>";

    $sectors = $org->getSectors();

    if (!empty($sectors))
    {
        echo elgg_view_layout('section', __("org:sectors"),
            elgg_view("org/sectors", array('sectors' => $sectors, 'sector_other' => $org->sector_other))
        );
    }

    ob_start();
        $zoom = $widget->zoom ?: 10;

        $lat = $org->getLatitude();
        $long = $org->getLongitude();
        echo elgg_view("org/map", array(
            'lat' => $lat,
            'long' => $long,
            'zoom' => $zoom,
            'pin' => true,
            'static' => true
        ));
        echo "<div style='text-align:center'>";
        echo "<em>";
        echo escape($org->getLocationText());
        echo "</em>";
        echo "<br />";
        echo "<a href='org/browse/?lat=$lat&long=$long&zoom=10'>";
        echo __('org:see_nearby');
        echo "</a>";
        echo "</div>";
    $map = ob_get_clean();
    echo elgg_view_layout('section', __("org:location"), $map);

?>
