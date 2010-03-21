<div class='padded'>
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
<?php 

$sector = get_input('sector');

echo elgg_view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(), 
    'empty_option' => elgg_echo('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

$region = get_input('region');

echo elgg_view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',    
    'options_values' => regions_in_country('tz'),
    'empty_option' => elgg_echo('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

echo "<br /><br />";

$res = Organization::listSearch($name=null, $sector, $region, $limit = 10);   
if ($res)
{
    echo $res;
}
else
{
    echo elgg_echo("search:noresults");
}

?>
</div>