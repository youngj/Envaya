<?php
    $org = $vars['org'];
    $home = $vars['home_widget'];

    echo view('feed/snippet', array(
        'max_length' => 50,
        'content' => "<em>".__('org:mission')."</em>: " . $home->render_content(),
        'link_url' => $home->get_url()
    ));

    echo "<div class='feed_snippet'>";
    echo "<em>".__('org:sectors')."</em>: ";
    echo view("org/sectors", array('sectors' => $org->get_sectors(), 'sector_other' => $org->sector_other), 'default');
    echo "</div>";

    echo "<div class='feed_snippet'>";
    echo "<em>".__('org:location')."</em>: ";
    echo "<a href='/org/browse/?lat={$org->get_latitude()}&long={$org->get_longitude()}&zoom=10'>";
    echo $org->get_location_text(false);
    echo "</a>";
    echo "</div>";