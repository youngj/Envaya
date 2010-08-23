<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->get_container_entity();
    
    $lat = $org->get_latitude() ?: 0.0;
    $long = $org->get_longitude() ?: 0.0;
    
    $zoom = ($lat || $long) ? 11 : 1;    
   
    ob_start();
   
?>

<div class='input'>
<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->get_country_text()); ?>   
</div>
</div>
<div>
<?php echo __('setup:region') ?> <?php echo view('input/pulldown', array(
    'internalname' => 'region',
    'options_values' => regions_in_country($org->country),
    'empty_option' => __('setup:region:blank'),
    'value' => $org->region
)) ?>    
<br />
<br />
<?php
    echo view("org/map", array(
        'lat' => $lat, 
        'long' => $long,
        'zoom' => $zoom,
        'pin' => true,
        'org' => $group,
        'edit' => true
    ));   
?>    


</div>
<?php    
    
    $content = ob_get_clean();
   
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
?>
</div>