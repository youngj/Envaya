<?php    

class Action_Registration_CreateProfile extends Action_Registration_CreateProfileBase
{
    function process_input()
    {
        try
        {
            $this->_process_input(); 
            forward(Session::get_loggedin_user()->get_url());            
        }
        catch (ValidationException $r)
        {
            return redirect_back_error($r->getMessage());
        }
    }
}