<?php

	$item = $vars['item'];
    $org = $item->getUserEntity();
	$orgUrl = $org->getURL();

	$home = $org->getWidgetByName('home');

	echo "<div style='padding-bottom:5px'>";
	echo sprintf(elgg_echo('feed:registered'), "<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>");
	echo "</div>";

	$maxLength = 500;

	$content = translate_field($home,'content');

	echo "<div>";
	echo "<em>".elgg_echo('org:mission')."</em>: ";

	echo elgg_view('output/longtext',
		array('value' => get_snippet($content, $maxLength))
	);

	if (strlen($content) > $maxLength)
	{
		echo " <a class='feed_more' href='$orgUrl'>".elgg_echo('feed:more')."</a>";
	}
	echo "</div>";

	echo "<div>";
	echo "<em>".elgg_echo('org:sectors')."</em>: ";
	echo elgg_view("org/sectors", array('sectors' => $org->getSectors(), 'sector_other' => $org->sector_other));
	echo "</div>";

	echo "<div>";
	echo "<em>".elgg_echo('org:location')."</em>: ";

	echo "<a href='org/browse/?lat={$org->getLatitude()}&long={$org->getLongitude()}&zoom=10'>";
	echo $org->getLocationText(false);
	echo "</a>";
	echo "</div>";
