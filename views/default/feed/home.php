<?php
    $org = $vars['org'];
    $home = $vars['home_widget'];

    $maxLength = 500;

    echo "<div class='feed_snippet'>";
    echo "<em>".__('org:mission')."</em>: ";

    $content = $home->render_content();
    $snippet = Markup::get_snippet($content, $maxLength);
    echo $snippet;

    if (strlen($content) > $maxLength)
    {
        echo " <a class='feed_more' href='{$home->get_url()}'>".__('feed:more')."</a>";
    }
    echo "</div>";

    echo "<div class='feed_snippet'>";
    echo "<em>".__('org:sectors')."</em>: ";
    echo view("org/sectors", array('sectors' => $org->get_sectors(), 'sector_other' => $org->sector_other));
    echo "</div>";

    echo "<div class='feed_snippet'>";
    echo "<em>".__('org:location')."</em>: ";
    echo "<a href='org/browse/?lat={$org->get_latitude()}&long={$org->get_longitude()}&zoom=10'>";
    echo $org->get_location_text(false);
    echo "</a>";
    echo "</div>";