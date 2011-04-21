<?php

class FeedItem_NewWidget extends FeedItem_EditWidget
{
    function render_heading($mode)
    {
        $widget = $this->get_subject_entity();
        return sprintf(!$widget->is_section() ? __('feed:new_page') : __('feed:new_section'), 
            $this->get_org_link($mode),
            $this->get_link($widget->get_title())
        );    
    }
}