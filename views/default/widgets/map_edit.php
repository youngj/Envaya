<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
    $lat = $org->getLatitude() ?: 0.0;
    $long = $org->getLongitude() ?: 0.0;
    
    $zoom = ($lat || $long) ? 11 : 1;    
   
    ob_start();
   
?>

<div class='input'>
<label><?php echo __('setup:location') ?></label>
<div>
<?php echo __('setup:city') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'city',
    'js' => 'style="width:200px"',
    'value' => $org->city
)) ?>, <?php echo escape($org->getCountryText()); ?>   
</div>
</div>
<div>
<?php echo __('setup:region') ?> <?php echo elgg_view('input/pulldown', array(
    'internalname' => 'region',
    'options_values' => regions_in_country($org->country),
    'empty_option' => __('setup:region:blank'),
    'value' => $org->region
)) ?>    
<br />
<br />
<?php
    echo elgg_view("org/map", array(
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
   
    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
?>
</div>