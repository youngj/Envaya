<?php
    $items = @$vars['items'] ?: array();    

    $nav = view('pagination', $vars);        
    echo $nav;      
    
    if (sizeof($items) > 0)
    {
        if (isset($vars['before']))
        {
            echo $vars['before'];
        }
    
        echo implode(@$vars['separator'] ?: '', $items);
        
        if (isset($vars['after']))
        {
            echo $vars['after'];
        }        
        
        echo $nav;        
    }