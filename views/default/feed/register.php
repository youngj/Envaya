<?php

    $item = $vars['item'];
    $mode = $vars['mode'];
    $org = $item->getUserEntity();
    $orgUrl = $org->getURL();

    $home = $org->getWidgetByName('home');

    echo "<div style='padding-bottom:5px'>";
    echo sprintf(__('feed:registered'),
        $mode == 'self' ? escape($org->name) : "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>");
    echo "</div>";

    if ($mode != 'self')
    {
        $maxLength = 500;

        echo "<div class='feed_snippet'>";
        echo "<em>".__('org:mission')."</em>: ";

        $content = $home->renderContent();
        $snippet = get_snippet($content, $maxLength);
        echo $snippet;

        if (strlen($content) > $maxLength)
        {
            echo " <a class='feed_more' href='$orgUrl'>".__('feed:more')."</a>";
        }
        echo "</div>";

        echo "<div class='feed_snippet'>";
        echo "<em>".__('org:sectors')."</em>: ";
        echo view("org/sectors", array('sectors' => $org->getSectors(), 'sector_other' => $org->sector_other));
        echo "</div>";

        echo "<div class='feed_snippet'>";
        echo "<em>".__('org:location')."</em>: ";
        echo "<a href='org/browse/?lat={$org->getLatitude()}&long={$org->getLongitude()}&zoom=10'>";
        echo $org->getLocationText(false);
        echo "</a>";
        echo "</div>";
    }
