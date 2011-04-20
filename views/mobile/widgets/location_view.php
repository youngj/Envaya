<div class='section_content padded'>
<?php

$widget = $vars['widget'];
$org = $widget->get_root_container_entity();
$zoom = $widget->zoom ?: 10;
$region = $org->region;

echo "<em>";
echo escape($org->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/org/browse/?list=1&region=".escape($region)."'>";
echo __('org:see_nearby');
echo "</a>";
?>
</div>