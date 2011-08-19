<?php

class Action_Registration_LoggedIn extends Action
{    
    function before()
    {
        if (!Session::isloggedin())
        {
            throw new RedirectException('', "/org/new");
        }
    }

    function render()
    {        
        $this->allow_view_types(null);
        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("org/register_logged_in"),
            'org_only' => true
        ));
    }    

    function process_input()
    {            
        Session::logout();
        $this->redirect("/org/new");            
    }
}
