<div class='padded'>
<?php

$sector = get_input('sector');
$region = get_input('region');

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

echo view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(),
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"
));

echo view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',
    'options_values' => regions_in_country('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"
));

?>

</div>

<?php
	$feedName = get_feed_name(array('sector' => $sector, 'region' => $region));
	echo view('feed/list', array('items' => FeedItem::queryByFeedName($feedName)->limit(20)->filter()));
?>