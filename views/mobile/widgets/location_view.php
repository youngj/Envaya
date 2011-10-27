<div class='section_content padded'>
<?php

$widget = $vars['widget'];
$user = $widget->get_container_user();
$zoom = $widget->get_metadata('zoom') ?: 10;
$region = $user->region;

echo "<em>";
echo escape($user->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/pg/browse/?list=1&region=".escape($region)."'>";
echo __('widget:location:see_nearby');
echo "</a>";
?>
</div>