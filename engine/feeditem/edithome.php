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
            return view('feed/home', array(
                'user' => $user, 
                'home_widget' => Widget_Home::get_for_entity($user)
            ));
        }        
        return '';
    }
    
    function get_sms_description()
    {
        $username = $this->get_user_entity()->username;          
        return "I $username";
    }    
}