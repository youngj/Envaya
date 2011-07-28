<div class='section_content padded'>
<?php

$widget = $vars['widget'];
$org = $widget->get_root_container_entity();

ob_start();
?>
<label><?php echo __('register:location') ?></label>
<div>
<?php echo __('register:city') ?> <?php echo view('input/text', array(
    'name' => 'city',
    'style' => 'width:200px',
    'track_dirty' => true,
    'value' => $org->city
)) ?>, <?php echo escape($org->get_country_text()); ?>
</div>
<div>
<?php echo __('register:region') ?> <?php echo view('input/pulldown', array(
    'name' => 'region',
    'options' => Geography::get_region_options($org->country),
    'empty_option' => __('register:region:blank'),
    'value' => $org->region
)) ?>
<br />
<br />
<label id="pinDragInstr">
<?php echo __("widget:location:drag_pin"); ?>
</label>
<script type='text/javascript'>

function initMap(map)
{
    function updateHiddenFields()
    {
        var pos = marker.getPosition();
        var form = document.forms[0];
        form.lat.value = pos.lat();
        form.long.value = pos.lng();
        form.zoom.value = map.getZoom();
    }

    var marker = new google.maps.Marker({
        position: map.getCenter(), 
        draggable: true
    });

    google.maps.event.addListener(marker, "dragend", function(e) {
        map.setCenter(e.latLng);
        updateHiddenFields();
    });

    google.maps.event.addListener(map, "zoom_changed", function() {
        updateHiddenFields();
    });

    marker.setMap(map);
}

</script>

<?php

    $lat = $org->get_latitude() ?: 0.0;
    $long = $org->get_longitude() ?: 0.0;
    $zoom = $widget->get_metadata('zoom') ?: (($lat || $long) ? 11 : 1);

    echo view('input/hidden', array('name' => 'lat', 'value' => $lat));
    echo view('input/hidden', array('name' => 'long', 'value' => $long));
    echo view('input/hidden', array('name' => 'zoom', 'value' => $zoom));

    echo view("output/map", array(
        'lat' => $lat,
        'long' => $long,
        'zoom' => $zoom,
        'width' => 560,
        'height' => 350,        
        'onload' => 'initMap',
    ));

    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    )); 
?>

</div>
</div>
