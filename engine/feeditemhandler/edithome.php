<?php

class FeedItemHandler_EditHome extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        return sprintf(__('feed:edit_home'), 
            $this->get_org_link($item, $mode)
        );    
    }
    
    function render_thumbnail($item, $mode)
    {
        return '';      
    }
        
    function render_content($item, $mode)
    {
        if ($mode != 'self')
        {
            $org = $item->get_user_entity();
            return view('feed/home', array('org' => $org, 'home_widget' => $org->get_widget_by_name('home')));
        }        
        return '';
    }
}