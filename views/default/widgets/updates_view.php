<div class='section_content'>
<?php
    $widget = $vars['widget'];
    $user = $widget->get_container_user();       
    
    $items = $user->query_feed_items()->limit(6)->filter();   

    echo view('feed/list', array('items' => $items, 'mode' => 'self'));    
?>
</div>