<?php

class Action_User_CustomDesign extends Action
{
    static $customizable_views = array(
        'layouts/default' => 'Layout',
        'css/custom' => 'CSS'
    );
        
    private $current_view;
        
    function before()
    {
        Permission_EditUserSite::require_for_entity($this->get_user());
        
        $this->current_view = get_input('current_view');
        
        $allowed_views = array_keys(static::$customizable_views);        
        if (!in_array($this->current_view, $allowed_views))
        {
            $this->current_view = $allowed_views[0];
        }
    }

    function process_input()
    {
        $this->set_content_type('text/javascript');
    
        $user = $this->get_user();
        $custom_views = $user->get_design_setting('custom_views');
        $template = get_input('template');
        
        $template = Markup::sanitize_html($template, array(
            'Attr.EnableID' => true,
            'AutoFormat.Linkify' => false,
        ));
        
        $current_view = get_input('current_view');        
        $custom_views[$current_view] = $template;
        $user->set_design_setting('theme_id', Theme_Custom::get_subtype_id());
        $user->set_design_setting('custom_views', $custom_views);
        $user->save();
        
        $this->set_content(json_encode('ok'));
    }
    
    function render()
    {
        return $this->page_draw(array(
            'css_name' => 'editor_wide',
            'content' => view('account/custom_design', array(
                'user' => $this->get_user(),
                'customizable_views' => static::$customizable_views,
                'current_view' => $this->current_view,
            ))
        ));
    }
}