<?php

    $nav = view('navigation/pagination',array(
        'offset' => $vars['offset'],
        'count' => $vars['count'],
        'limit' => $vars['limit'],
    ));
        
    echo $nav;
      
    $entities = $vars['entities'];       
    
    if (is_array($entities) && sizeof($entities) > 0) 
    {
        foreach($entities as $entity) 
        {
            echo view_entity($entity);
        }      
        echo $nav;
    }
?>