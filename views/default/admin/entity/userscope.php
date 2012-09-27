<?php
    $scope = $vars['entity'];   
        
    foreach ($scope->get_filters() as $filter)
    {            
        echo "<div>".escape($filter->get_name()) . ": " . $filter->render_view()."</div>";
    }
        
    $child_scopes = $scope->query_scopes()->filter();    
    if ($child_scopes)
    {
        echo "<h4>Categories</h4>";
        
        foreach ($child_scopes as $child_scope)
        {
            echo "<a href='{$child_scope->get_admin_url()}'>".escape($child_scope->get_title())."</a><br />";
        }
        echo "<br />";
    }
    
    $users_query = $scope->query_users();    
        
    $q = Input::get_string('q');
    
    if ($q)
    {
        $users_query->fulltext($q);
    }
        
    $limit = 20;
    $offset = Input::get_int('offset');
    $num_users = $users_query->count();        
    
    if ($num_users || $q)
    {    
        echo "<div style='float:right'>";
        echo "<form method='GET' action='{$scope->get_admin_url()}'>";
        echo view('input/text', array('name' => 'q', 'value' => $q, 'style' => 'width:150px'));
        echo view('input/submit', array('name' => '', 'style' => 'margin:0px', 'value' => __('search')));
        echo "</form>";
        echo "</div>";
        
        echo "<h4>Users ($num_users)</h4>";       

    }
    
    if ($num_users)
    {
        $users = $users_query
            ->limit($limit, $offset)
            ->order_by('time_created desc')
            ->filter();

        $items = array();
        foreach ($users as $user)
        {
            $approval = $user->is_approved() ? "" : 
                ($user->approval == User::Rejected ? "(rejected)" : 
                    ($user->is_setup_complete() ? "(not yet approved)" : "(setup incomplete)"));
        
            $items[] = "<a href='{$user->get_admin_url()}'>".escape($user->get_title())."</a> $approval";
        }
        
        echo view('paged_list', array(
            'items' => $items,
            'separator' => '<br />',
            'limit' => $limit,
            'offset' => $offset,
            'count' => $num_users
        ));
        echo "<br />";
    }