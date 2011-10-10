<?php

$sector = get_input('sector') ?: 0;
$lat = get_input('lat') ?: -6.6;
$long = get_input('long') ?: 36;
$zoom = get_input('zoom') ?: 5;

?>

<div class='section_content padded'>
<script type='text/javascript'>
<?php
    echo view('js/google_map');
?>  

function sectorChanged()
{
    setTimeout(function() {
        $('browseLink').href = "/pg/browse?list=1&sector=" + $('sectorList').value;
        orgLoader.reset();
        orgLoader.load();
    }, 1);
}

var orgLoader = new OrgMapLoader();

orgLoader.getURLParams = function() {
    return {
        sector: $('sectorList').value
    };
};

</script>

<div class='view_toggle'>
    <strong><?php echo __('browse:map') ?></strong> &middot;
    <a id='browseLink' href='/pg/browse?list=1&sector=<?php echo escape($sector) ?>'><?php echo __('browse:list') ?></a>
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
        'zoom' => $zoom, 
        'onload' => 'orgLoader.setMap',
    ));
?>
</div>
