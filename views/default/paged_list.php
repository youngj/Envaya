<?php

    $nav = view('navigation/pagination',array(
        'offset' => $vars['offset'],
        'count' => $vars['count'],
        'limit' => $vars['limit'],
    ));
        
    echo $nav;
      
    $entities = @$vars['entities'];       
    if (is_array($entities)) 
    {
        $elements = array_map('view_entity', $entities);
    }
    else
    {    
        $elements = @$vars['elements'] ?: array();
    }
    
    if (sizeof($elements) > 0)
    {
        echo implode(@$vars['separator'] ?: '', $elements);
        echo $nav;        
    }       