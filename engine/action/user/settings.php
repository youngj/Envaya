<?php

class Action_User_Settings extends Action
{  
    function process_input()
    {
        $user = $this->get_user();
        
        Permission_EditUserSettings::require_for_entity($user);
                                
        if (Input::get_string('delete'))
        {
            Permission_UseAdminTools::require_for_entity($user);
        
            $user->disable();
            $user->save();
            
            LogEntry::create('user:delete', $user);
            
            SessionMessages::add(__('user:deleted'));
            return $this->redirect('/admin/entities');
        }

        $name = Input::get_string('name');

        if ($name)
        {
            if ($name != $user->name)
            {
                $user->name = $name;
                SessionMessages::add(__('user:name:success'));
            }
        }
        else
        {
            throw new ValidationException(__('register:no_name'));
        }

        $language = Input::get_string('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            $this->change_viewer_language($user->language);
            SessionMessages::add(__('user:language:success'));
        }

        $email = trim(Input::get_string('email'));
        if ($email != $user->email)
        {
            $user->set_email(EmailAddress::validate($email));
            SessionMessages::add(__('user:email:success'));
        }

        $phone = Input::get_string('phone');
        if ($phone != $user->phone_number)
        {
            $user->set_phone_number($phone);
            SessionMessages::add(__('user:phone:success'));
        }

        if ($user->country)
        {
            $city = Input::get_string('city');
            $region = Input::get_string('region');
            if ($city != $user->city || $region != $user->region)
            {
                $old_location_text = $user->get_location_text(false);
                        
                $user->city = $city;
                $user->region = $region;
                $user->geocode_lat_long();
                
                if ($user->get_design_setting('tagline') == $old_location_text)
                {
                    $user->set_design_setting('tagline', $user->get_location_text(false));
                }
                
                SessionMessages::add(__('user:location:success'));
            }
        }
        
        $user->save();
        $this->redirect(Input::get_string('from') ?: $user->get_url());
    }

    function render()
    {
        $user = $this->get_user();
    
        Permission_ViewUserSettings::require_for_entity($user);
    
        $this->use_editor_layout();
    
        $this->page_draw(array(
            'title' => __('user:settings'),
            'content' => view("account/settings", array('user' => $user)),
        ));                
    }    
}    