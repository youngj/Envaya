<?php

    $item = $vars['item'];
    $org = $item->getUserEntity();
    $orgUrl = $org->getURL();

    $widget = $item->getSubjectEntity();
    $widgetUrl = $widget->getURL();

    echo "<div style='padding-bottom:5px'>";
    echo sprintf(elgg_echo('feed:new_widget'),
        "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
        "<a href='$widgetUrl'>{$widget->getTitle()}</a>"
    );
    echo "</div>";

