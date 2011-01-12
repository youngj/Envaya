<?php 

$sector = @$vars['sector'];
$region = @$vars['region'];

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

<?php echo view('org/browseToggle', $vars); ?>

<?php 

echo view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options' => Organization::get_sector_options(), 
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));
echo "<div style='height:5px'></div>";

echo view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',    
    'options' => regions_in_country('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

?>
<noscript>
<?php echo view('input/submit', array('internalname' => 'submit', 'value' => __('go'))); ?>
</noscript>
</form>
<br />

<?php

$res = Organization::list_search($name=null, $sector, $region, $limit = 10);   
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