<div class='section_content'>
<?php
    $widget = $vars['widget'];
    $org = $widget->get_root_container_entity();       
    
    $items = $org->query_feed_items()->limit(6)->filter();   

    echo view('feed/list', array('items' => $items, 'mode' => 'self'));    
?>
</div>