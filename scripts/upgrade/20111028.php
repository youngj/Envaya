<?php
    require_once "start.php";      
    
    $root_scope = UserScope::get_root();
    if (!$root_scope)
    {    
        $root_scope = new UserScope();
        $root_scope->save();
    }
    
    $scopes = array(
        'Tanzania Organizations' => array(
            new Query_Filter_Country(array('value' => 'tz')),
            new Query_Filter_UserType(array('value' => 'core.user.org')),
        ),
        'Rwanda Organizations' => array(
            new Query_Filter_Country(array('value' => 'rw')),
            new Query_Filter_UserType(array('value' => 'core.user.org')),
        ),
        'Other Organizations' => array(
            new Query_Filter_UserType(array('value' => 'core.user.org')),
        ),
        'People' => array(
            new Query_Filter_UserType(array('value' => 'core.user.person')),
        )
    );
    
    $i = 0;
    
    foreach ($scopes as $description => $filters)
    {
        $i++;
        $scope = $root_scope->query_scopes()->where('description = ?', $description)->get();
        if (!$scope)
        {
            $scope = new UserScope();
            $scope->description = $description;
            $scope->order = $i;
            $scope->set_container_entity($root_scope);
            $scope->set_filters($filters);        
            $scope->save();
        }
    }        
    