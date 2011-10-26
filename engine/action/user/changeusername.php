<?php

class Action_User_ChangeUsername extends Action
{
    function before()
    {
        $this->require_site_editor();
    }
     
    function process_input()
    {
        $user = $this->get_user();

        $username = get_input('username');
        $oldUsername = $user->username;

        if ($username && $username != $oldUsername)
        {
            User::validate_username($username);

            $isCaseChange = strtolower($username) == strtolower($oldUsername);
            
            if ($isCaseChange)
            {
                $user->username = $username;
                $user->save();
            }
            else
            {            
                if (User::get_by_username($username))
                {
                    throw new ValidationException(__('register:userexists'));
                }

                $user->username = $username;
                $user->save();
                
                if ($user->is_approved())
                {
                    // each user gets one courtesy redirect to avoid breaking existing links.
                    // delete redirects to the old username to prevent spamming and circular redirects from multiple username changes. 
                    $oldRedirects = NotFoundRedirect::query()
                        ->where('container_guid = 0')
                        ->where('replacement = ?', "/{$oldUsername}")
                        ->filter();
                    foreach ($oldRedirects as $oldRedirect)
                    {
                        $oldRedirect->delete();
                    }
                
                    $redirect = NotFoundRedirect::new_simple_redirect("/{$oldUsername}","/{$username}");
                    $redirect->save();
                }
            }
            
            get_cache()->delete(User::get_cache_key_for_username($username));
            get_cache()->delete(User::get_cache_key_for_username($oldUsername));
            
            SessionMessages::add(__('user:username:changed'));
        }
        $this->redirect($user->get_url()."/settings");
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('user:username:change'),
            'content' => view('account/change_username', array('user' => $this->get_user()))
        ));
    }    
}    