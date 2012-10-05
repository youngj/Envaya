<?php

class Action_User_Delete extends Action
{  
    function before()
    {
        $user = $this->get_user();        
    
        Permission_EditUserSettings::require_for_entity($user);
        Permission_UseAdminTools::require_for_entity($user);
    }

    function process_input()
    {
        $user = $this->get_user();        
                                
        if (!Input::get_string('delete'))
        {
            throw new ValidationException("Please click the delete button.");
        }
        
        if (!Session::get_logged_in_user()->has_password(Input::get_string('password')))
        {
            throw new ValidationException("Invalid password.");
        }
        
        $user->delete();
            
        LogEntry::create('user:delete', $user);
            
        SessionMessages::add(__('user:deleted'));
        return $this->redirect('/admin/entities');
    }

    function render()
    {
        $user = $this->get_user();
    
        Permission_ViewUserSettings::require_for_entity($user);
    
        $this->use_editor_layout();
    
        $this->page_draw(array(
            'title' => "Delete user",
            'content' => view("account/delete", array('user' => $user)),
        ));                
    }    
}    