<?php

class FeedItemHandler_EditWidget extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        $widget = $item->get_subject_entity();
        return sprintf(__('feed:edit_widget'), 
            $this->get_org_link($item, $mode),
            $this->get_link($item, $widget->get_title())
        );    
    }
    
    function render_thumbnail($item, $mode)
    {
        $widget = $item->get_subject_entity();
        return view('feed/thumbnail', array(
            'link_url' => $this->get_url($item),
            'thumbnail_url' => $widget->has_image() ? $widget->thumbnail_url : ''
        ));
    }
        
    function render_content($item, $mode)
    {
        $widget = $item->get_subject_entity();
        return view('feed/snippet', array(            
            'link_url' => $this->get_url($item),
            'content' => $widget->render_content(Markup::Feed)
        ));    
    }
}