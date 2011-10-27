<div class='padded section_content'>
<?php
    $widget = $vars['widget'];
    
    ob_start();
    
    $is_custom_widget = !in_array($widget->widget_name, Widget::get_default_names());
    
    if ($widget->title || $is_custom_widget)
    {    
        if ($widget->is_section())
        {
            echo view('widgets/edit_section_title', array('value' => $widget->title));            
        }
        else if ($widget->is_page())
        {
            echo view('widgets/edit_page_title', array('value' => $widget->title));
        }
        else
        {
            echo view('widgets/edit_title', array('value' => $widget->title));
        }
    }
    if ($is_custom_widget && $widget->is_page())
    {
        echo view('widgets/edit_page_address', array(
            'org' => $widget->get_container_user(),
            'value' => $widget->widget_name,
        ));
    }
    
    echo view('widgets/edit_content', array('widget' => $widget));
    
    $content = ob_get_clean();

    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));

?>
</div>