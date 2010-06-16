<?php

	$org = $vars['org'];

	$sectors = $org->getSectors();

	foreach ($sectors as $sector)
	{
		$feedNames[] = get_feed_name(array('sector' => $sector));
	}

	/*
	if ($org->region)
	{
		$feedNames[] = get_feed_name(array('region' => $org->region));
	}
	*/

	foreach ($org->getPartnerships($limit = 10) as $partnership)
	{
		$feedNames[] = get_feed_name(array('user' => $partnerhip->partner_guid));
	}

	echo "<div class='padded'>";

	echo sprintf(
		elgg_echo('feed:org:about'), "<a href='{$org->getURL()}/partnerships/edit'>".elgg_echo('widget:partnerships')."</a>"
	);

	echo " <strong><a href='{$org->getURL()}/related'>".elgg_echo('feed:org:included')."</a></strong>";
	echo "</div>";

	echo "<hr>";

	echo elgg_view('feed/list',
		array('items' => FeedItem::filterByFeedNames($feedNames, $limit = 20)));
