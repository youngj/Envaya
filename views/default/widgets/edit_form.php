<?php
    $widget = $vars['widget'];

    $noSave = @$vars['noSave'];
        
    $form_body =  $vars['body'];
    
    $form_body .= view('input/hidden', array('name' => 'widget_name', 'value' => $widget->widget_name));
    
    $is_page = $widget->is_page();
    
    if ($widget->guid && ($widget->status != EntityStatus::Disabled))
    {
        $form_body .= view('input/alt_submit', array(
            'name' => "delete",
            'trackDirty' => true,
            'confirmMessage' => $is_page ? __('widget:delete:confirm') : __('widget:delete_section:confirm'),
            'id' => 'widget_delete',
            'value' => $is_page ? __('widget:delete') : __('widget:delete_section')
        ));
    }
    
    if (!$noSave)
    {
        $saveText = __('widget:publish');
    }
    else
    {
        $saveText = $widget->is_active() ? __('widget:view') : 
            ($is_page ? __('widget:create') : __('widget:create_section'));        
    }
    $form_body .= view('input/submit', array('value' => $saveText));
    

    echo view('input/form', array(
        'body' => $form_body,
        'action' => "{$widget->get_base_url()}/edit",
        'enctype' => 'multipart/form-data'
    ));

?>