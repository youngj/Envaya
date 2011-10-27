<div class='section_content padded'>
<?php

$widget = $vars['widget'];

$org = $widget->get_container_user();

$lat = $org->get_latitude();
$long = $org->get_longitude();

echo "<a href='/pg/browse/?lat=$lat&long=$long&zoom=10'>";

echo view("output/map", array(
    'lat' => $lat,
    'width' => 560,
    'height' => 350,
    'long' => $long,
    'zoom' => $widget->get_metadata('zoom') ?: 10,
    'static' => true,
    'pin' => true
));
echo "</a>";


echo "<div style='text-align:center'>";
echo "<em>";
echo escape($org->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/pg/browse/?lat=$lat&long=$long&zoom=10'>";
echo __('widget:location:see_nearby');
echo "</a>";
echo "</div>";
?>
</div>