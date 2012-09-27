<?php

class Action_User_Design extends Action
{
    function before()
    {
        Permission_EditUserSite::require_for_entity($this->get_user());
    }
     
    function process_input()
    {
        $user = $this->get_user();

        $theme_id = Input::get_string('theme_id');

        $theme = ClassRegistry::get_class($theme_id);        
        if ($theme && is_subclass_of($theme, 'Theme'))
        {
            $user->set_design_setting('theme_id', $theme_id);
        }

        //$user->set_design_setting('theme_options', Input::get_array('theme_options'));        
        
        $iconFiles = UploadedFile::json_decode_array($_POST['icon']);

        if (Input::get_string('deleteicon'))
        {
            $user->set_icon(null);
        }
        else if ($iconFiles)
        {
            $user->set_icon($iconFiles);
        }

        $custom_header = Input::get_int('custom_header');
        
        if (!$custom_header)
        {
            $user->set_design_setting('tagline', Input::get_string('tagline'));            
            //$user->set_design_setting('share_links', Input::get_array('share_links'));                        
            $user->set_design_setting('custom_header', false);
        }
        else 
        {
            $header_image = json_decode(Input::get_string('header_image'), true);
            if ($header_image)
            {            
                $user->set_design_setting('header_image', isset($header_image[1]) ? $header_image[1] : $header_image[0]);
            }
            
            $user->set_design_setting('custom_header', !!$user->get_design_setting('header_image'));
        }
        
        SessionMessages::add(__("design:saved"));
        $user->save();
        
        $this->redirect($user->get_url());
    }

    function render()
    {
        $user = $this->get_user();

        $cancelUrl = Input::get_string('from') ?: $user->get_url();
        PageContext::get_submenu('top')->add_link(__("canceledit"), $cancelUrl);

        $this->page_draw(array(
            'title' => __("design:edit"),
            'theme' => 'Theme_Editor',
            'content' => view("account/design", array('user' => $user))
        ));        
    }
    
}    