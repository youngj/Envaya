<?php

class FeedItemHandler_EditWidget extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        $widget = $item->get_subject_entity();
        
        return sprintf($widget->is_page() ? __('feed:edit_page') : __('feed:edit_section'), 
            $this->get_org_link($item, $mode),
            $this->get_link($item, $widget->get_title())
        );    
    }
    
    function render_thumbnail($item, $mode)
    {
        $widget = $item->get_subject_entity();
        return view('feed/thumbnail', array(
            'link_url' => $this->get_url($item),
            'thumbnail_url' => $widget->thumbnail_url
        ));
    }
        
    function render_content($item, $mode)
    {
        $widget = $item->get_subject_entity();
        
        if ($mode == 'self' && !$widget->is_page())
        {
            return '';
        }
        
        return view('feed/snippet', array(            
            'link_url' => $this->get_url($item),
            'content' => $widget->render_content(Markup::Feed)
        ));    
    }
}