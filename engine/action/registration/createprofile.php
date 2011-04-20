<?php    

class Action_Registration_CreateProfile extends Action_Registration_CreateProfileBase
{
    protected function post_process_input()
    {
        forward(Session::get_loggedin_user()->get_url());            
    }
    
    protected function handle_validation_exception($ex)
    {
        return redirect_back_error($ex->getMessage());    
    }
}