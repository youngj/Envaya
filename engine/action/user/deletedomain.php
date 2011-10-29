<?php

class Action_User_DeleteDomain extends Action
{
    function before()
    {
        Permission_UseAdminTools::require_for_entity($this->get_user());
    }
     
    function process_input()
    {       
        $user_domain_name = UserDomainName::query()
            ->where('guid = ?', $this->get_user()->guid)
            ->where('id = ?', (int)get_input('id'))->get();
            
        if (!$user_domain_name)
        {
            throw new RedirectException(__('domains:not_found'));
        }
        $user_domain_name->delete();
        SessionMessages::add(__('domains:deleted'));
        $this->redirect();
    }    
}    