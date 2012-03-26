<?php

class Controller_Pg_Discussions extends Controller
{
    static $routes = array(
        array(
            'regex' => '',
            'action' => 'action_index',
        )
    );

    function action_index()
    {
        Permission_Public::require_any();
    
        $this->allow_view_types(null);
        
        $this->page_draw(array(
            'title' => __('discussions:latest'),
            'content' => view('discussions/topic_list')
        ));                    
    }    
}