<?php

class FeedItem_EditHome extends FeedItem
{
    function render_heading($mode)
    {
        return sprintf(__('feed:edit_home'), 
            $this->get_org_link($mode)
        );    
    }
    
    function render_thumbnail($mode)
    {
        return '';      
    }
        
    function render_content($mode)
    {
        if ($mode != 'self')
        {
            $org = $this->get_user_entity();
            return view('feed/home', array('org' => $org, 'home_widget' => $org->get_widget_by_name('home')));
        }        
        return '';
    }
}