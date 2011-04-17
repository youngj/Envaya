<?php    
    $offset = (int) get_input('offset');

    $query = Organization::query()->where_visible_to_user();
    
    $sector = @$vars['sector'];
    $region = @$vars['region'];
    
    if ($sector)
    {
        $query->with_sector($sector);
    }
        
    if ($region)
    {
        $query->with_region($region);
    }
     
    $fulltext = $vars['fulltext'];
    if ($fulltext)
    {
        $query->fulltext($fulltext);
    }
    else
    {
        $query->order_by('name');                
    }
                
    $query->limit($vars['limit'] ?: 10, $offset);
       
    echo view('search/results_list', array(
        'entities' => $query->filter(),
        'count' => $query->count(),
        'offset' => $offset,
        'limit' => $limit,
    ));
