<?php

class Action_CustomDesign extends Action
{
    static $customizable_views = array(
        'layouts/default' => 'Layout',
        'css/custom' => 'CSS'
    );
        
    private $current_view;
        
    function before()
    {
        $this->require_editor();
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
    
        $org = $this->get_org();
        $custom_views = $org->get_design_setting('custom_views');
        $template = get_input('template');
        
        $template = Markup::sanitize_html($template, array(
            'Attr.EnableID' => true,
            'AutoFormat.Linkify' => false,
        ));
        
        $current_view = get_input('current_view');        
        $custom_views[$current_view] = $template;
        $org->set_design_setting('theme_name', 'custom');
        $org->set_design_setting('custom_views', $custom_views);
        $org->save();
        
        $this->set_content(json_encode('ok'));
    }
    
    function render()
    {
        return $this->page_draw(array(
            'css_name' => 'editor_wide',
            'content' => view('org/custom_design', array(
                'org' => $this->get_org(),
                'customizable_views' => static::$customizable_views,
                'current_view' => $this->current_view,
            ))
        ));
    }
}