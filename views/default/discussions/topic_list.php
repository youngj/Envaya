<div class='padded'>
<?php  
    $limit = 20;
    $offset = (int)get_input('offset');
        
    $query = DiscussionTopic::query();    
    
    $query->from('discussion_topics d');
    
    $filters = Query_Filter::filters_from_input(array('Sector','Country','Region'));
    
    $subquery = new Query_SelectUser('users u');
    $subquery->columns('u.guid');
    $subquery->where('u.guid = d.container_guid');
    $subquery->where_visible_to_user();   
    $subquery->apply_filters($filters);
    
    $query->where("exists ({$subquery->get_sql()})");
    $query->args($subquery->get_args());
    
    $query->order_by('last_time_posted desc');
    
    $query->limit($limit, $offset);
    
    $topics = $query->filter();

    echo view('org/filter_controls', array(
        'baseurl' => '/pg/discussions',
        'filters' => $filters,
    ));
    
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