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
        $this->validate_security_token();
        
        $org = $this->get_org();

        $username = get_input('username');

        $oldUsername = $org->username;

        if ($username && $username != $oldUsername)
        {
            try
            {
                validate_username($username);
            }
            catch (RegistrationException $ex)
            {
                register_error($ex->getMessage());
                return $this->render();
            }

            if (get_user_by_username($username))
            {
                register_error(__('registration:userexists'));
                return $this->render();
            }

            $org->username = $username;
            $org->save();

            get_cache()->delete(get_cache_key_for_username($username));
            get_cache()->delete(get_cache_key_for_username($oldUsername));

            system_message(__('username:changed'));
        }
        forward($org->get_url());
    }

    function render()
    {
        $title = __('username:title');
        $area1 = view('org/changeUsername', array('org' => $this->get_org()));
        $body = view_layout("one_column", view_title($title), $area1);

        $this->page_draw($title,$body);
    }    
}    