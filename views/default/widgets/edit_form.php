<?php
    $widget = $vars['widget'];

    $noSave = @$vars['noSave'];
        
    $form_body = $vars['body'];
    
    $is_section = $widget->is_section();
    
    if ($widget->is_enabled())
    {
        $form_body .= view('input/alt_submit', array(
            'name' => "delete",
            'trackDirty' => true,
            'confirm' => $is_section ? __('widget:delete_section:confirm') : __('widget:delete:confirm'),
            'id' => 'widget_delete',
            'value' => $is_section ? __('widget:delete_section') : __('widget:delete')
        ));
    }
    
    if (!$noSave)
    {
        $saveText = __('widget:publish');
    }
    else
    {
        $saveText = $widget->is_enabled() ? __('widget:view') : 
            ($is_section ? __('widget:create_section') : __('widget:create'));        
    }
    $form_body .= view('input/submit', array('value' => $saveText));
    

    echo view('input/form', array(
        'body' => $form_body,
        'action' => "{$widget->get_base_url()}/edit",
        'enctype' => 'multipart/form-data'
    ));

?>