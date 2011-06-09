<div class='section_content padded'>
<?php

$widget = $vars['widget'];
$org = $widget->get_root_container_entity();
$zoom = $widget->get_metadata('zoom') ?: 10;
$region = $org->region;

echo "<em>";
echo escape($org->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/pg/browse/?list=1&region=".escape($region)."'>";
echo __('widget:location:see_nearby');
echo "</a>";
?>
</div>