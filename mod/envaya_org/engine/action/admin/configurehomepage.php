<?php

class Action_Admin_ConfigureHomePage extends Action
{
    function before()
    {
        Permission_EditMainSite::require_for_root();
    }
        
    function process_input()
    {
        $guid = (int)get_input('home_bottom_left_guid');
    
        State::set('home_bottom_left_guid', $guid);
        SessionMessages::add("Changes saved.");
        $this->redirect();
    
    }
    
    function render()
    {
        $this->page_draw(array(
            'title' => 'Configure Home Page',
            'content' =>  view('admin/configure_home_page'),
            'theme_name' => 'editor'
        ));    
    }
}