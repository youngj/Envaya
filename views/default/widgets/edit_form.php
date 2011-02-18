<?php
    $widget = $vars['widget'];

    $noSave = @$vars['noSave'];
        
    $form_body =  $vars['body'];
    
    $form_body .= view('input/hidden', array('name' => 'widget_name', 'value' => $widget->widget_name));
    
    if ($widget->guid && $widget->is_enabled() && $widget->widget_name != 'home')
    {
        $form_body .= view('input/alt_submit', array(
            'name' => "delete",
            'trackDirty' => true,
            'confirmMessage' => __('widget:delete:confirm'),
            'id' => 'widget_delete',
            'value' => __('widget:delete')
        ));
    }
    if (!$noSave)
    {
        $saveText = $widget->is_active() ? __('savechanges') : __('widget:save:new');
        $form_body .= view('input/submit', array('name' => "submit", 'trackDirty' => true, 'value' => $saveText)) ;
    }
    else
    {
        $saveText = $widget->is_active() ? __('widget:view') : __('widget:save:new');
        $form_body .= view('input/submit', array('name' => "submit", 'value' => $saveText)) ;
    }


    echo view('input/form', array(
        'body' => $form_body,
        'action' => "{$widget->get_base_url()}/edit",
        'enctype' => 'multipart/form-data'
    ));

?>