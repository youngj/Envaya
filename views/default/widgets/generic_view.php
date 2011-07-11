<?php
    $widget = $vars['widget'];
    
    if (!$widget->content && $widget->is_page())
    {    
        $content = sprintf(__('widget:empty'), escape($widget->get_title()));
    }
    else
    {
        $content = $widget->render_content();
    }
        
    echo view('section', array(
        'content' => $content
    ));
?>
