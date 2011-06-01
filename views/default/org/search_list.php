<?php    
    $sector = null;
    $region = null;
    $fulltext = null;
    $limit = 10;
    extract($vars);
    
    $offset = (int) get_input('offset');

    $query = Organization::query()->where_visible_to_user();
    
    if ($sector)
    {
        $query->with_sector($sector);
    }
        
    if ($region)
    {
        $query->with_region($region);
    }
     
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
