<?php

class FeedItem_NewsMulti extends FeedItem_News
{
    function render_heading($mode)
    {
        $count = @$this->args['count'] ?: 1;
    
        return sprintf(__('feed:news_multi'), 
            $this->get_org_link($mode),                
            $this->get_link(__('widget:news:items')),
            $count
        );
    }
    
    function get_url()
    {
        $user = $this->get_user_entity();
        $update = $this->get_subject_entity();        
        return $user->get_url() . "/news?end={$update->guid}";
    }               
}