<?php
    $offset = $vars['offset'];
    $entities = $vars['entities'];
    $limit = $vars['limit'];
    $count = $vars['count'];
    
    $nav = view('navigation/pagination',array(
        'offset' => $offset,
        'count' => $count,
        'limit' => $limit,
    ));
        
    echo $nav;

    if (is_array($entities) && sizeof($entities) > 0) 
    {
        foreach($entities as $entity) 
        {
            echo view_entity($entity);
        }      
        echo $nav;
    }
?>