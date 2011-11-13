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

        $theme = get_input('theme');

        if ($theme != $user->get_design_setting('theme_name'))
        {
            $user->set_design_setting('theme_name', $theme);
        }

        $iconFiles = UploadedFile::json_decode_array($_POST['icon']);

        if (get_input('deleteicon'))
        {
            $user->set_icon(null);
        }
        else if ($iconFiles)
        {
            $user->set_icon($iconFiles);
        }

        $custom_header = (int)get_input('custom_header');
        
        if (!$custom_header)
        {
            $user->set_design_setting('tagline', get_input('tagline'));            
            //$user->set_design_setting('share_links', get_input_array('share_links'));                        
            $user->set_design_setting('custom_header', false);
        }
        else 
        {
            $header_image = json_decode(get_input('header_image'), true);
            if ($header_image)
            {
                $user->set_design_setting('header_image', $header_image[0]);
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

        $cancelUrl = get_input('from') ?: $user->get_url();
        PageContext::get_submenu('edit')->add_link(__("canceledit"), $cancelUrl);

        $this->page_draw(array(
            'title' => __("design:edit"),
            'theme_name' => 'editor',
            'content' => view("account/design", array('user' => $user))
        ));        
    }
    
}    