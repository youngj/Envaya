<?php 

$sector = @$vars['sector'];

?>

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


<div class='view_toggle'>
    <a href='org/browse?sector=<?php echo escape($sector); ?>'><?php echo __('browse:map') ?></a> &middot;
    <strong><?php echo __('list') ?></strong>
</div>

<?php 

echo view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::get_sector_options(), 
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

echo "<div style='height:5px'></div>";

$region = get_input('region');

echo view('input/pulldown', array(
    'internalname' => 'region',
    'internalid' => 'regionList',    
    'options_values' => regions_in_country('tz'),
    'empty_option' => __('region:empty_option'),
    'value' => $region,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
));

echo "<br /><br />";

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