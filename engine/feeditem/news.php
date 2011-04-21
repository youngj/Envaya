<?php

class FeedItem_News extends FeedItem
{
    function render_heading($mode)
    {
        return sprintf(__('feed:news'), 
            $this->get_org_link($mode),                
            $this->get_link(__('widget:news:item'))
        );    
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
            'content' => $update->render_content(Markup::Feed)
        ));
    }
}