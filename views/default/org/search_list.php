<?php    
    $fulltext = null;
    $filters = null;
    $limit = 10;
    extract($vars);
    
    $offset = (int) get_input('offset');

    $query = Organization::query()
        ->where_visible_to_user()
        ->apply_filters($filters);
     
    if ($fulltext)
    {
        $query->fulltext($fulltext);
    }
    else
    {
        $query->order_by('name');                
    }
                
    $query->limit($limit, $offset);
       
    echo view('search/results_list', array(
        'entities' => $query->filter(),
        'count' => $query->count(),
        'offset' => $offset,
        'limit' => $limit,
    ));
