<?php

class FeedItem_EditHome extends FeedItem
{
    function render_heading($mode)
    {
        return strtr(__('feed:edit_home'), 
            array('{name}' => $this->get_user_link($mode))
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
            $user = $this->get_user_entity();
            return view('feed/home', array('user' => $user, 'home_widget' => $user->get_widget_by_name('home')));
        }        
        return '';
    }
}