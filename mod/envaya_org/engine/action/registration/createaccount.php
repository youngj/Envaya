<?php

class Action_Registration_CreateAccount extends Action_Registration_CreateAccountBase
{
    function before()
    {        
        if (Session::isloggedin())
        {
            throw new RedirectException('', "/org/create_profile");
        }

        if (!Session::get('registration'))
        {
            throw new RedirectException(__('register:qualify_missing'), "/org/new");
        }    
    }

    function render()
    {        
        $this->allow_view_types(null);        
        $this->page_draw(array(
            'title' => __("register:title"),
            'content' => view("org/create_account"),
            'org_only' => true
        ));
    }    

    function post_process_input()
    {        
        Session::set('registration', null);                
                
        $invite_code = Session::get('invite_code');
        Session::set('invite_code', null);
        if ($invite_code)
        {
            $this->update_existing_relationships($invite_code);
        }        
        
        $this->redirect("/org/create_profile");    
    }
    
    protected function get_country()
    {
        $prevInfo = Session::get('registration');            
        return @$prevInfo['country'];
    }
    
    private function update_existing_relationships($invite_code)
    {
        $org = $this->org;
    
        $invitedEmail = InvitedEmail::query()
            ->where('invite_code = ?', $invite_code)
            ->where('registered_guid = 0')
            ->get();
        
        if (!$invitedEmail)
        {
            return;
        }
        
        /*
         * only update existing relationships if we're fairly confident they refer to 
         * the newly registered organization.
         */
        $invitedAddress = $invitedEmail->email;                            
        if ($invitedAddress == $org->email)
        {
            $relationships = Relationship::query()
                ->where('subject_guid = 0')
                ->where('subject_email = ?', $invitedAddress)
                ->filter();
                
            foreach ($relationships as $relationship)
            {
                $relationship->subject_guid = $org->guid;
                $relationship->save();
                
                $reverse = $relationship->make_reverse_relationship();
                $reverse->set_subject_approved();
                $reverse->set_self_approved(); // not really, but they can always delete it before creating their network page
                $reverse->save();                                                                            
            }        
        }
        
        $invitedEmail->registered_guid = $org->guid;
        $invitedEmail->save();                
    }    
}