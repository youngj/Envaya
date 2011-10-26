<?php

class Action_User_AddDomain extends Action
{
    function before()
    {
        $this->require_admin();
    }
     
    function process_input()
    {       
        $domain_name = get_input('domain_name');
        if (UserDomainName::query()->where('domain_name = ?', $domain_name)->exists())
        {
            throw new RedirectException(__('domains:duplicate'));
        }
        if (preg_match('/[^\w\.\-]/', $domain_name))
        {
            throw new RedirectException(__('domains:invalid'));
        }
        
        $org_domain_name = new UserDomainName();
        $org_domain_name->domain_name = $domain_name;
        $org_domain_name->guid = $this->get_user()->guid;
        $org_domain_name->save();
        SessionMessages::add(__('domains:added'));
        $this->redirect();
    }
}