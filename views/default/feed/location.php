<?php
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();

    echo "<a href='/pg/browse/?lat={$org->get_latitude()}&long={$org->get_longitude()}&zoom=10'>";
    echo $org->get_location_text(false);
    echo "</a>";
