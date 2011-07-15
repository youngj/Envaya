<?php    

class Action_Registration_CreateProfile extends Action_Registration_CreateProfileBase
{
    function before()
    {
        $this->require_login();
        
        if (!(Session::get_loggedin_user() instanceof Organization))
        {
            throw new RedirectException('', "/org/register_logged_in");
        }
    }
    
    function render()
    {        
        $this->allow_view_types(null);        
        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("org/create_profile"),
            'org_only' => true
        ));
    }    

    protected function post_process_input()
    {
        $this->redirect(Session::get_loggedin_user()->get_url());            
    }    
}