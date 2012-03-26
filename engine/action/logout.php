<?php

class Action_Logout extends Action
{
    function before()
    {
        Permission_Public::require_any();
    }
    
    function process_input()
    {
        Session::logout();
        $this->redirect(get_input('next') ?: '/');
    }
    
    function render()
    {
        if (!Session::is_logged_in())
        {
            throw new RedirectException('', get_input('next') ?: '/');
        }

        $this->page_draw(array(
            'title' => __("logout"), 
            'header' => '&nbsp;',
            'theme_name' => 'editor',
            'content' => view("account/logout", array('next' => get_input('next'))),
        ));
    }
}    
