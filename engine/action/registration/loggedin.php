<?php

class Action_Registration_LoggedIn extends Action
{    
    function before()
    {
        Permission_Public::require_any();
        
        if (!Session::is_logged_in())
        {
            throw new RedirectException('', $this->get_redirect_url());
        }
    }

    function get_redirect_url()
    {
        return secure_url(get_input('next') ?: "/pg/register", Request::get_host());
    }
    
    function render()
    {        
        $this->allow_view_types(null);
        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("account/register_logged_in", array(
                'prev' => get_input('prev'),
                'next' => get_input('next')
            )),
        ));
    }    

    function process_input()
    {            
        Session::logout();
        $this->redirect($this->get_redirect_url());            
    }
}
