<?php    

class Action_Registration_CreateProfile extends Action_Registration_CreateProfileBase
{
    protected function post_process_input()
    {
        $this->redirect(Session::get_loggedin_user()->get_url());            
    }    
}