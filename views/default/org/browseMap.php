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
    <strong><?php echo __('browse:map') ?></strong> &middot;
    <a id='browseLink' href='/org/browse?list=1&sector=<?php echo escape($sector) ?>'><?php echo __('list') ?></a>
</div>

<?php echo view('input/pulldown', array(
    'internalname' => 'sector',
    'internalid' => 'sectorList',
    'options_values' => Organization::get_sector_options(),
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,
    'js' => "onchange='sectorChanged()' onkeypress='sectorChanged()'"
))
?>

<div class='instructions' style='clear:both'><?php echo __("browse:instructions") ?></div>
<?php
    $lat = $vars['lat'] ?: -6.6;
    $long = $vars['long'] ?: 36;
    $zoom = $vars['zoom'] ?: 5;
    $sector = @$vars['sector'] ?: 0;

    echo view("org/map", array('lat' => $lat, 'long' => $long,  'height' => 350, 'zoom' => $zoom, 'sector' => $sector, 'nearby' => true));
?>
</div>
