<div class='padded'>
<div class='instructions'>
<?php echo elgg_echo('widget:contact:instructions') ?></label>
</div>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
ob_start();
?>
<div class='input'>
    <label><?php echo elgg_echo('widget:contact:name:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'contact_name', 
    'value' => $org->contact_name
));    
?>
</div>    

<div class='input'>
    <label><?php echo elgg_echo('widget:contact:title:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'contact_title', 
    'value' => $org->contact_title
));    
?>
</div>    


<div class='input'>
    <label><?php echo sprintf(elgg_echo('widget:contact:public_email'), escape($org->email)) ?></label><br />
<?php echo elgg_view("input/radio", array('internalname' => 'public_email', 
    'options' => yes_no_options(),
    'value' => $widget->public_email
));    
?>
</div>    

<div class='input'>
    <label><?php echo elgg_echo('widget:contact:phone_number:edit') ?></label><br />
<?php echo elgg_view("input/text", array('internalname' => 'phone_number', 
    'value' => $org->phone_number,
    'js' => "style='width:200px'"
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