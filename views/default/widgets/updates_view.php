<?php

    $widget = $vars['widget'];
    $org = $vars['widget']->getContainerEntity();
       
    $items = $org->getFeedItems(6);

    echo "<div class='section_content'>";

    echo elgg_view('feed/self_list', array('items' => $items));

    echo "</div>";
    
?>
