<?php

    $widget = $vars['widget'];
    $org = $vars['widget']->getContainerEntity();
       
    $items = $org->queryFeedItems()->limit(6)->filter();

    echo "<div class='section_content'>";

    echo view('feed/self_list', array('items' => $items));

    echo "</div>";
    
?>
