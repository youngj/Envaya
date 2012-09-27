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
        $this->redirect(Input::get_string('next') ?: '/');
    }
    
    function render()
    {
        if (!Session::is_logged_in())
        {
            throw new RedirectException('', Input::get_string('next') ?: '/');
        }

        $this->page_draw(array(
            'title' => __("logout"), 
            'header' => '&nbsp;',
            'theme' => 'Theme_Editor',
            'content' => view("account/logout", array('next' => Input::get_string('next'))),
        ));
    }
}    
