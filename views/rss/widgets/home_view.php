<?php	
    $user = $vars['widget']->get_container_user();
    $items = $user->query_feed_items()->limit(10)->filter();
    echo view('feed/list', array('items' => $items, 'mode' => 'self'));    
?>