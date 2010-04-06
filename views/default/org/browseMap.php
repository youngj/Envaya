<?php 

$sector = @$vars['sector'];

?>

<div class='padded'>
<script type='text/javascript'>
function sectorChanged()
{
    setTimeout(function() {
        var sectorList = document.getElementById('sectorList');

        var val = sectorList.options[sectorList.selectedIndex].value;        
        
        var browseLink = document.getElementById('browseLink');
        browseLink.href = "org/browse?list=1&sector=" + val;
        
        setMapSector(val);
    }, 1);    
}

</script>

<div class='view_toggle'>
    <strong><?php echo elgg_echo('browse:map') ?></strong> &middot;
    <a id='browseLink' href='org/browse?list=1&sector=<?php echo escape($sector) ?>'><?php echo elgg_echo('list') ?></a>
</div>

<?php echo elgg_view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::getSectorOptions(), 
    'empty_option' => elgg_echo('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"        
))    
?>

<div class='instructions'><?php echo elgg_echo("browse:instructions") ?></div>
<?php     
    $lat = $vars['lat'] ?: -6.6;
    $long = $vars['long'] ?: 36;
    $zoom = $vars['zoom'] ?: 5;
    $sector = @$vars['sector'] ?: 0;
        
    echo elgg_view("org/map", array('lat' => $lat, 'long' => $long,  'height' => 350, 'zoom' => $zoom, 'sector' => $sector, 'nearby' => true));
?>    
</div>
    