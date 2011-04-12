<?php
    $widget = $vars['widget'];

    $noSave = @$vars['noSave'];
        
    $form_body =  $vars['body'];
    
    $form_body .= view('input/hidden', array('name' => 'widget_name', 'value' => $widget->widget_name));
    
    if ($widget->guid && ($widget->status != EntityStatus::Disabled))
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
        $saveText = __('widget:save');       
    }
    else
    {
        $saveText = $widget->is_active() ? __('widget:view') : __('widget:create');        
    }
    $form_body .= view('input/submit', array('value' => $saveText));
    

    echo view('input/form', array(
        'body' => $form_body,
        'action' => "{$widget->get_base_url()}/edit",
        'enctype' => 'multipart/form-data'
    ));

?>