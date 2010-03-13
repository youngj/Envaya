<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
ob_start();
?>
<div class='input'>
    <label><?php echo elgg_echo('setup:mission') ?></label>
<div class='help'>
<?php echo elgg_echo('setup:mission:help') ?>
</div>
<?php echo elgg_view("input/longtext", array('internalname' => 'content', 
        'value' => $widget->content)) ?>    
</div>    

<div class='input'>
    <label><?php echo elgg_echo("setup:sector"); ?><br /></label>
    <?php
        echo elgg_view("input/checkboxes",array(
            'internalname' => 'sector', 
            'options' => Organization::getSectorOptions(), 
            'value' => $org->getSectors()));
    ?>
    <?php echo elgg_echo('setup:sector:other_specify') ?> <?php echo elgg_view('input/text', array(
    'internalname' => 'sector_other',
    'value' => $org->sector_other,
    'js' => 'style="width:200px"'
)) ?>    
</div>
<?php
    $content = ob_get_clean();
   
    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
    
?>
</div>