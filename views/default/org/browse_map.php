<?php

$sector = get_input('sector') ?: 0;
$lat = get_input('lat') ?: -6.6;
$long = get_input('long') ?: 36;
$zoom = get_input('zoom') ?: 5;

?>

<div class='padded'>
<script type='text/javascript'>
<?php
    echo view('js/google_map');
?>  

function sectorChanged()
{
    setTimeout(function() {
        $('browseLink').href = "org/browse?list=1&sector=" + $('sectorList').value;
        mapLoader.reset();
        mapLoader.load();
    }, 1);
}

var mapLoader = new MapLoader(function ($bounds) 
{
    var $sw = $bounds.getSouthWest();
    var $ne = $bounds.getNorthEast(); 

    return "/org/searchArea?latMin="+$sw.lat()+"&latMax="+$ne.lat()+
        "&longMin="+$sw.lng()+"&longMax="+$ne.lng()+
        "&sector=" + $('sectorList').value;
});
</script>

<div class='view_toggle'>
    <strong><?php echo __('browse:map') ?></strong> &middot;
    <a id='browseLink' href='/org/browse?list=1&sector=<?php echo escape($sector) ?>'><?php echo __('browse:list') ?></a>
</div>

<?php echo view('input/pulldown', array(
    'name' => 'sector',
    'id' => 'sectorList',
    'options' => OrgSectors::get_options(),
    'empty_option' => __('sector:empty_option'),
    'value' => $sector,    
    'attrs' => array('onchange' => 'sectorChanged()', 'onkeypress' => 'sectorChanged()'),
))
?>

<div class='instructions' style='clear:both'><?php echo __("browse:instructions") ?></div>
<?php
    echo view("output/map", array(
        'lat' => $lat, 
        'long' => $long,  
        'height' => 350, 
        'zoom' => $zoom, 
        'onload' => 'mapLoader.setMap',
    ));
?>
</div>
