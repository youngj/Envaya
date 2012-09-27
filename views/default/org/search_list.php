<?php    
    $fulltext = null;
    $filters = null;
    $limit = 10;
    extract($vars);
    
    $offset = Input::get_int('offset');

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
       
    $results = $query->filter();
       
    echo view('org/search_results_list', array(
        'items' => array_map(function($org) { 
            return view('org/search_result', array('org' => $org)); 
        }, $results),
        'count' => $query->count(),
        'offset' => $offset,
        'limit' => $limit,
    ));
