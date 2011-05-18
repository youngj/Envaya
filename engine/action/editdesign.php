<?php

class Action_EditDesign extends Action
{
    function before()
    {
        $this->require_editor();
        $this->require_org();        
    }
     
    function process_input()
    {
        $org = $this->get_org();

        $theme = get_input('theme');

        if ($theme != $org->get_design_setting('theme_name'))
        {
            $org->set_design_setting('theme_name', $theme);
        }

        $iconFiles = UploadedFile::json_decode_array($_POST['icon']);

        if (get_input('deleteicon'))
        {
            $org->set_icon(null);
        }
        else if ($iconFiles)
        {
            $org->set_icon($iconFiles);
        }

        $custom_header = (int)get_input('custom_header');
        
        $org->set_design_setting('custom_header', $custom_header);

        if (!$custom_header)
        {
            $org->set_design_setting('tagline', get_input('tagline'));            
            $org->set_design_setting('share_links', get_input_array('share_links'));                        
        }
        else 
        {
            $header_image = json_decode(get_input('header_image'), true);
            if ($header_image)
            {
                $org->set_design_setting('header_image', $header_image[0]);
            }
        }
        
        SessionMessages::add(__("design:saved"));
        $org->save();
        
        $this->redirect($org->get_url());
    }

    function render()
    {
        $org = $this->get_org();

        $cancelUrl = get_input('from') ?: $org->get_url();
        PageContext::get_submenu('edit')->add_item(__("canceledit"), $cancelUrl);

        $this->page_draw(array(
            'title' => __("design:edit"),
            'theme_name' => 'editor',
            'content' => view("org/design", array('org' => $org))
        ));        
    }
    
}    