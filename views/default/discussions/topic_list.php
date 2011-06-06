<?php  
    $limit = 2;
    $offset = (int)get_input('offset');
    
    $query = DiscussionTopic::query();    
    
    $query->from('discussion_topics d');
    
    if (!Session::isadminloggedin())
    {
        $query->where("exists (select u.guid from users u where u.guid = d.container_guid and u.status <> 0 and (u.approval > 0 OR u.guid = ?))", Session::get_loggedin_userid());
    }
    
    $query->order_by('last_time_posted desc');
    
    $query->limit($limit, $offset);
    
    $topics = $query->filter();

    echo "<div style='height:1px'></div>";
    
    $elements = array_map('view_entity', $topics);
        
    echo implode('', $elements);
    
    echo view('pagination', array(
        'offset' => $offset,
        'limit' => $limit,
        'count' => $count,
        'count_displayed' => sizeof($topics),
    ));
    
    echo "<br />";