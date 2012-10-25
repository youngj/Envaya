<?php

class FeedItem_EditWidget extends FeedItem
{
    function is_valid()
    {
        return parent::is_valid() && $this->get_subject_entity()->publish_status == Widget::Published;
    }

    function render_heading($mode)
    {
        $widget = $this->get_subject_entity();

        return strtr(!$widget->is_section() ? __('feed:edit_page') : __('feed:edit_section'), array(
            '{name}' => $this->get_user_link($mode),
            '{title}' => $this->get_link($widget->get_title()),
        )); 
    }
    
    function render_thumbnail($mode)
    {
        $widget = $this->get_subject_entity();
        return view('feed/thumbnail', array(
            'link_url' => $this->get_url(),
            'thumbnail_url' => $widget->thumbnail_url
        ));
    }
        
    function render_content($mode)
    {
        $widget = $this->get_subject_entity();
        
        if ($mode == 'self' && $widget->is_section())
        {
            return '';
        }
        
        return view('feed/snippet', array(            
            'link_url' => $this->get_url(),
            'max_length' => $mode == static::ModeFeatured ? 150 : 350,
            'content' => $widget->render_content(Markup::Feed)
        ));    
    }
}