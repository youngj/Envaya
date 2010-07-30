<div class='padded section_content'>
<?php

    $widget = $vars['widget'];
    
    $content = elgg_view('widgets/edit_title', array('widget' => $widget));
    
    $content .= elgg_view('widgets/edit_content', array('widget' => $widget));

    echo elgg_view("widgets/edit_form", array(
        'widget' => $widget,
        'body' => $content
    ));

?>
</div>