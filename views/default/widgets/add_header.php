<div id='heading'><h1>
<?php
    $widget = $vars['widget'];

    if ($widget->is_section_container())
    {
        $sections = array(
            array('url' => $widget->get_edit_url(), 'title' => sprintf(__('edit_item'), $widget->get_title())),
            array('title' => __('widget:add_section')),
        );
    }
    else   
    {    
        $sections = array(
            array('title' => __('widget:add')),
        );
    }            
    echo view('breadcrumb', array('items' => $sections));
?>
</h1></div>