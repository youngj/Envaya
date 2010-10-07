<?php

class FeedItemHandler_NewsMulti extends FeedItemHandler_News
{
    function render_heading($item, $mode)
    {
        $count = @$item->args['count'] ?: 1;
    
        return sprintf(__('feed:news_multi'), 
            $this->get_org_link($item, $mode),                
            $this->get_link($item, __('widget:news:items')),
            $count
        );
    }
    
    function get_url($item)
    {
        $user = $item->get_user_entity();
        $update = $item->get_subject_entity();
        
        return rewrite_to_current_domain($user->get_url() . "/news?end={$update->guid}");
    }               
}