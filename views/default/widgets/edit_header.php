<div id='heading'><h1>
<?php
    $widget = $vars['widget'];

    if ($widget->is_section())
    {
        $container = $widget->get_container_entity();
        $sections = array(
            array('url' => $container->get_edit_url(), 'title' => sprintf(__('edit_item'), $container->get_title())),
            array('title' => $widget->get_title()),
        );
    }
    else   
    {    
        $sections = array(
            array('title' => sprintf(__('edit_item'), $widget->get_title())),
        );
    }            
    echo view('breadcrumb', array('items' => $sections));
?>
</h1></div>