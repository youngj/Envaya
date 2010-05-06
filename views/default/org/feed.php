<div class='padded'>
<?php

$sector = get_input('sector');
$region = get_input('region');

$feedName = get_feed_name(array('sector' => $sector, 'region' => $region));

$feedItems = FeedItem::filterByFeedName($feedName, $limit = 20);

?>

<script type='text/javascript'>
function sectorChanged()
{
    var sectorList = document.getElementById('sectorList');
    var regionList = document.getElementById('regionList');
    var sector = sectorList.options[sectorList.selectedIndex].value;
    var region = regionList.options[regionList.selectedIndex].value;
    window.location.href = "/org/feed?sector=" + sector + "&region=" + region;
}
</script>

<?php

echo elgg_view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(),
    'empty_option' => elgg_echo('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"
));

echo elgg_view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',
    'options_values' => regions_in_country('tz'),
    'empty_option' => elgg_echo('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"
));

?>

</div>

<?php

if (empty($feedItems))
{
    echo "<div class='padded'>".elgg_echo("search:noresults")."</div>";
}

foreach ($feedItems as $feedItem)
{
	$org = $feedItem->getUserEntity();
	$subject = $feedItem->getSubjectEntity();
	if ($org && $subject)
	{
		$orgIcon = $org->getIcon('small');
		$orgUrl = $org->getURL();

	?>

	<div class='blog_post_wrapper padded'>
	<div class="feed_post">
    	<a class='feed_org_icon' href='<?php echo $orgUrl ?>'><img src='<?php echo $orgIcon ?>' /></a>
		<div class='feed_content'><?php echo $feedItem->renderView() ?>
		<div class='blog_date'><?php echo $feedItem->getDateText() ?></div>
		</div>
		<div style='clear:both'></div>
	</div>
	</div>
	<?php
	}
}

?>