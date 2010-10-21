<div class='padded'>
<?php

$sector = $vars['sector'];
$region = $vars['region'];
$items = $vars['items'];

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

<form method='GET' action='/org/feed'>
<?php

echo view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::get_sector_options(),
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
<noscript>
<?php echo view('input/submit', array('internalname' => 'submit', 'value' => __('go'))); ?>
</noscript>

</form>
</div>

<?php	
	echo view('feed/list', array('items' => $items));
?>