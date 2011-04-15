<?php
    $nav = view('pagination',array(
        'offset' => $vars['offset'],
        'count' => $vars['count'],
        'limit' => $vars['limit'],
    ));
        
    echo $nav;

    $entities = $vars['entities'];
    if (is_array($entities) && sizeof($entities) > 0) 
    {
        echo "<ul>";
        foreach($entities as $entity) 
        {
            echo "<li>".view_entity($entity)."</li>";
        }      
        echo "</ul>";
        echo $nav;
    }
?>