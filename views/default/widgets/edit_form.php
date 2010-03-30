<?php
    $widget = $vars['widget'];
    
    $saveText = ($widget->guid && $widget->isEnabled()) ? elgg_echo('widget:save') : elgg_echo('widget:save:new');

    $form_body = "<div class='padded'>" . 
        elgg_view('input/hidden', array('internalname' => 'org_guid', 'value' => $widget->getContainerEntity()->guid)) . 
        elgg_view('input/hidden', array('internalname' => 'widget_name', 'value' => $widget->widget_name)) . 
        elgg_view('input/submit', array('internalname' => "submit", 'trackDirty' => true, 'value' => $saveText)) ;
    
    if ($widget->guid && $widget->isEnabled() && $widget->widget_name != 'home')
    {    
        $form_body .= elgg_view('input/submit', array(
            'internalname' => "delete", 
            'internalid' => 'widget_delete', 
            'js' => "onclick='return confirm(".json_encode(elgg_echo('widget:delete:confirm')).")'",
            'value' => elgg_echo('widget:delete')
        ));
    }    
        
    $form_body .= "</div>";

    echo elgg_view('input/form', array(
        'body' => $vars['body'] . $form_body, 
        'action' => "action/org/saveWidget",
        'enctype' => 'multipart/form-data'
    ));

?>