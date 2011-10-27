<?php
    $widget = $vars['widget'];
    $org = $widget->get_container_user();

    echo "<a href='/pg/browse/?lat={$org->get_latitude()}&long={$org->get_longitude()}&zoom=10'>";
    echo $org->get_location_text(false);
    echo "</a>";
