<?php

class FeedItemHandler_News extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        return sprintf(__('feed:news'), 
            $this->get_org_link($item, $mode),                
            $this->get_link($item, __('widget:news:item'))
        );    
    }
    
    function render_thumbnail($item, $mode)
    {
        $update = $item->get_subject_entity();
        return view('feed/thumbnail', array(
            'link_url' => $this->get_url($item),
            'thumbnail_url' => $update->thumbnail_url
        ));        
    }
        
    function render_content($item, $mode)
    {
        $update = $item->get_subject_entity();

        return view('feed/snippet', array(            
            'link_url' => $this->get_url($item),
            'content' => $update->render_content(Markup::Feed)
        ));
    }
}