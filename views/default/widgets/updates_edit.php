<div class='section_content'>
<?php
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();       
    
    $items = $org->query_feed_items()->limit(6)->filter();   
    
    echo view("widgets/edit_form", array(
            'widget' => $widget,
            'noSave' => true,
            'body' => view('feed/list', array('items' => $items, 'mode' => 'self', 'show_edit_controls' => true))
    ));    
?>
</div>