<?php	
    $org = $vars['widget']->get_root_container_entity();
    $items = $org->query_feed_items()->limit(10)->filter();
    echo view('feed/list', array('items' => $items, 'mode' => 'self'));    
?>