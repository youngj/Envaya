<?php

    $context = $vars['context'];
    $offset = $vars['offset'];
    $entities = $vars['entities'];
    $limit = $vars['limit'];
    $count = $vars['count'];
    $baseurl = $vars['baseurl'];
    $context = $vars['context'];
    $pagination = $vars['pagination'];
    $fullview = $vars['fullview']; 
    
    $html = "";
    $nav = "";
    
    if ($pagination)
        $nav .= elgg_view('navigation/pagination',array(			
            'baseurl' => $baseurl,
            'offset' => $offset,
            'count' => $count,
            'limit' => $limit,
        ));
        
    $html .= $nav;

    if (is_array($entities) && sizeof($entities) > 0) 
    {
        foreach($entities as $entity) 
        {
            $html .= view_entity($entity, $fullview);
        }
    }
    
    if ($count)
        $html .= $nav;
        
    echo $html;

?>