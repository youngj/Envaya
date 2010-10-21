<?php

$org = $vars['org'];
$zoom = $vars['zoom'] ?: 10;
$region = $org->region;

echo "<em>";
echo escape($org->get_location_text());
echo "</em>";
echo "<br />";
echo "<a href='/org/browse/?list=1&region=".escape($region)."'>";
echo __('org:see_nearby');
echo "</a>";
