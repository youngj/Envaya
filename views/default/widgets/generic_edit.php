<div class='padded section_content'>
<?php

    $widget = $vars['widget'];
    
    $content = view('widgets/edit_title', array('widget' => $widget));
    
    $content .= view('widgets/edit_content', array('widget' => $widget));

    echo view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));

?>
</div>