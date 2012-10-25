<?php

class FeedItem_News extends FeedItem
{
    static $query_subtypes = array('FeedItem_NewsMulti');

    function is_valid()
    {
        return parent::is_valid() && $this->get_subject_entity()->publish_status == Widget::Published;
    }
    
    function render_heading($mode)
    {
        return strtr(__('feed:news'), array(
            '{name}' => $this->get_user_link($mode),
            '{title}' => $this->get_link(__('widget:news:item')),
        ));         
    }
    
    function render_thumbnail($mode)
    {
        $update = $this->get_subject_entity();
        return view('feed/thumbnail', array(
            'link_url' => $this->get_url(),
            'thumbnail_url' => $update->thumbnail_url
        ));        
    }
        
    function render_content($mode)
    {
        $update = $this->get_subject_entity();
        return view('feed/snippet', array(            
            'link_url' => $this->get_url(),
            'max_length' => $mode == static::ModeFeatured ? 150 : 350,
            'content' => $update->render_content(Markup::Feed)
        ));
    }
    
    function get_sms_description()
    {
        $username = $this->get_user_entity()->username;                       
        return "N $username";
    }
}