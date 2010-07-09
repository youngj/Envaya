<?php

    $widget = $vars['widget'];

    $content = elgg_view('output/longtext', array('value' => translate_field($widget, 'content')));

    echo elgg_view_layout('section', elgg_echo("org:mission"), $content);

    $org = $vars['widget']->getContainerEntity();

    echo "<div class='section_header'>".elgg_echo("widget:news:latest")."</div>";

    $feedName = get_feed_name(array('user' => $org->guid));

    $items = FeedItem::filterByFeedName($feedName, $limit = 6);

    echo "<div class='section_content'>";

    echo elgg_view('feed/self_list', array('items' => $items));

    if (sizeof($org->getNewsUpdates(1)))
    {
        echo "<div class='padded'><a class='float_right' href='".$org->getUrl()."/news'>".elgg_echo('blog:view_all')."</a><div style='clear:both'></div></div>";
    }

    echo "</div>";

    $sectors = $org->getSectors();

    if (!empty($sectors))
    {
        echo elgg_view_layout('section', elgg_echo("org:sectors"),
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
        echo elgg_echo('org:see_nearby');
        echo "</a>";
        echo "</div>";
    $map = ob_get_clean();
    echo elgg_view_layout('section', elgg_echo("org:location"), $map);

?>
