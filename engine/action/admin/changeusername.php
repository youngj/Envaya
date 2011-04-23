<?php

class Action_Admin_ChangeUsername extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();
        $this->require_admin();
    }
     
    function process_input()
    {
        $org = $this->get_org();

        $username = get_input('username');

        $oldUsername = $org->username;

        if ($username && $username != $oldUsername)
        {
            validate_username($username);

            if (User::get_by_username($username))
            {
                throw new ValidationException(__('registration:userexists'));
            }

            $org->username = $username;
            $org->save();

            get_cache()->delete(User::get_cache_key_for_username($username));
            get_cache()->delete(User::get_cache_key_for_username($oldUsername));

            $redirect = NotFoundRedirect::new_simple_redirect("/{$oldUsername}","/{$username}");
            $redirect->save();
            
            SessionMessages::add(__('username:changed'));
        }
        forward($org->get_url());
    }

    function render()
    {
        $this->page_draw(array(
            'title' => __('username:title'),
            'content' => view('org/change_username', array('org' => $this->get_org()))
        ));
    }    
}    