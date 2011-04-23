<?php 

$sector = get_input('sector');     
$region = get_input('region');

?>

<div class='padded'>
<form method='GET' action='/org/browse'>
<input type='hidden' name='list' value='1' />
<script type='text/javascript'>
function sectorChanged()
{
    setTimeout(function() {
        var sectorList = document.getElementById('sectorList');
        
        var regionList = document.getElementById('regionList');

        var sector = sectorList.options[sectorList.selectedIndex].value;
        var region = regionList.options[regionList.selectedIndex].value;

        window.location.href = "/org/browse?list=1&sector=" + sector + "&region=" + region;
    }, 1);    
}
</script>

<div class='view_toggle'>
    <a href='/org/browse?list=0&sector=<?php echo escape($sector); ?>'><?php echo __('browse:map') ?></a> &middot;
    <strong><?php echo __('list') ?></strong>
</div>

<?php 

echo view('input/pulldown', array(
    'name' => 'sector',
    'id' => 'sectorList',
    'options' => OrgSectors::get_options(), 
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));
echo "<div style='height:5px'></div>";

echo view('input/pulldown', array(
    'name' => 'region',
    'id' => 'regionList',    
    'options' => Geography::get_region_options('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

?>
<noscript>
<?php echo view('input/submit', array('value' => __('go'))); ?>
</noscript>
</form>
<br />

<?php

$res = view('org/search_list', array(
    'sector' => $sector,
    'region' => $region
));

if ($res)
{
    echo $res;
}
else
{
    echo __("search:noresults");
}

?>
</div>