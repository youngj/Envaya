<?php
    $widget = $vars['widget'];

    $noSave = @$vars['noSave'];
        
    $form_body =  $vars['body'];
    
    $form_body .= elgg_view('input/hidden', array('internalname' => 'widget_name', 'value' => $widget->widget_name));
    

    if ($widget->guid && $widget->isEnabled() && $widget->widget_name != 'home')
    {
        $form_body .= elgg_view('input/alt_submit', array(
            'internalname' => "delete",
            'trackDirty' => true,
            'confirmMessage' => __('widget:delete:confirm'),
            'internalid' => 'widget_delete',
            'value' => __('widget:delete')
        ));
    }

    if (!$noSave)
    {
        $saveText = $widget->isActive() ? __('savechanges') : __('widget:save:new');
        $form_body .= elgg_view('input/submit', array('internalname' => "submit", 'trackDirty' => true, 'value' => $saveText)) ;
    }
    else
    {
        $saveText = $widget->isActive() ? __('widget:view') : __('widget:save:new');
        $form_body .= elgg_view('input/submit', array('internalname' => "submit", 'value' => $saveText)) ;
    }


    echo elgg_view('input/form', array(
        'body' => $form_body,
        'action' => "{$widget->getContainerEntity()->getURL()}/{$widget->widget_name}/save",
        'enctype' => 'multipart/form-data'
    ));

?>