<?php
    $widget = $vars['widget'];

    $form_body = "<p>" . 
        elgg_view('input/hidden', array('internalname' => 'org_guid', 'value' => $widget->getContainerEntity()->guid)) . 
        elgg_view('input/hidden', array('internalname' => 'widget_name', 'value' => $widget->widget_name)) . 
        elgg_view('input/submit', array('internalname' => "submit", 'value' => elgg_echo('widget:save'))) .        
        "</p>";

    echo elgg_view('input/form', array('body' => $vars['body'] . $form_body, 'action' => "action/org/saveWidget"));

?>