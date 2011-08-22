<div class='padded'>
<?php  
    $limit = 20;
    $offset = (int)get_input('offset');
    
    $sector = (int)get_input('sector');
    $region = get_input('region');
    $country = get_input('country');
    
    $query = DiscussionTopic::query();    
    
    $query->from('discussion_topics d');
    
    $subquery = new Query_SelectUser('users u');
    $subquery->columns('u.guid');
    $subquery->where('u.guid = d.container_guid');
    $subquery->where_visible_to_user();   
    $subquery->with_sector($sector);
    $subquery->with_region($region);
    $subquery->with_country($country);
    
    $query->where("exists ({$subquery->get_sql()})");
    $query->args($subquery->get_args());
    
    $query->order_by('last_time_posted desc');
    
    $query->limit($limit, $offset);
    
    $topics = $query->filter();

    echo view('org/filter_controls', array('baseurl' => '/pg/discussions'));
    
    echo "<div style='height:10px'></div>";
    
    $elements = array_map('view_entity', $topics);    
        
    echo implode('', $elements);
    
    echo view('pagination', array(
        'offset' => $offset,
        'limit' => $limit,
        'count_displayed' => sizeof($topics),
    ));
    
?>
<br />
</div>