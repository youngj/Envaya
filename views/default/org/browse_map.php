<?php

    $sector = Input::get_string('sector') ?: 0;
    $lat = Input::get_string('lat') ?: -6.6;
    $long = Input::get_string('long') ?: 36;
    $zoom = Input::get_string('zoom') ?: 5;

    echo "<div class='section_content padded'>";
    
    echo view('js/google_map');
?>  
<script type='text/javascript'>
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
<?php 
    echo view('input/pulldown', array(
        'name' => 'sector',
        'id' => 'sectorList',
        'options' => OrgSectors::get_options(),
        'empty_option' => __('sector:empty_option'),
        'value' => $sector,    
        'attrs' => array('onchange' => 'sectorChanged()', 'onkeypress' => 'sectorChanged()'),
    ));
    
    echo "<div class='instructions' style='clear:both;padding-bottom:10px'>";
    echo __("browse:instructions");
    echo "</div>";
    
    
    echo view("output/map", array(
        'id' => 'mapDiv',
        'width' => 650,
        'height' => 450,
        'lat' => $lat, 
        'long' => $long,  
        'zoom' => $zoom, 
        'onload' => 'orgLoader.setMap',
    ));
    
    echo "</div>";

