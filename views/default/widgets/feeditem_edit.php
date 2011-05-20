<div class='padded section_content'>
<?php

    $widget = $vars['widget'];

    ob_start();

    echo view($widget->get_title_view(), $vars);
    echo view($widget->get_content_view(), $vars);        
    echo view($widget->get_date_view(), $vars);
    
    $content = ob_get_clean();
    
    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));    
?>
</div>