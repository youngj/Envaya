<div class='padded'>
<?php

    $widget = $vars['widget'];
    $org = $widget->getContainerEntity();
    
    $content = elgg_view("input/longtext", array('internalname' => 'content', 
        'value' => $widget->content));
   
    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));
    
    
?>
</div>