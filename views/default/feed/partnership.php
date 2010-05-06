<?php

	$item = $vars['item'];
    $org = $item->getUserEntity();
	$orgUrl = $org->getURL();

	$partner = $item->getSubjectEntity();
	$partnerUrl = $partner->getURL();

	echo sprintf(elgg_echo('feed:partnership'),
		"<a class='feed_org_name' href='$orgUrl'>".escape($org->name)."</a>",
		"<a class='feed_org_name' href='$partnerUrl'>".escape($partner->name)."</a>"
	);

