<div class='section_content padded'>
<?php

$widget = $vars['widget'];
$org = $widget->get_container_user();

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
<?php
    
    echo view('input/map_pin', array(
        'lat_name' => 'lat',
        'long_name' => 'long',
        'zoom_name' => 'zoom',
        'lat' => $org->get_latitude(),
        'long' => $org->get_longitude(),
        'zoom' => $widget->get_metadata('zoom'),
    ));
    
    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    )); 
?>

</div>
</div>
