<?php

$org = $vars['org'];
$zoom = $vars['zoom'] ?: 10;

$lat = $org->get_latitude();
$long = $org->get_longitude();
echo view("org/map", array(
    'lat' => $lat,
    'long' => $long,
    'zoom' => $zoom,
    'pin' => true,
    'static' => true
));
echo "<div style='text-align:center'>";
echo "<em>";
echo escape($org->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/org/browse/?lat=$lat&long=$long&zoom=10'>";
echo __('org:see_nearby');
echo "</a>";
echo "</div>";
