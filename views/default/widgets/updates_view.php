<?php

    $widget = $vars['widget'];
    $org = $vars['widget']->get_container_entity();
       
    $items = $org->query_feed_items()->limit(6)->filter();

    echo "<div class='section_content'>";

    echo view('feed/self_list', array('items' => $items, 'mode' => 'self'));

    echo "</div>";
    
?>
