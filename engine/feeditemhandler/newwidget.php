<?php

class FeedItemHandler_NewWidget extends FeedItemHandler_EditWidget
{
    function render_heading($item, $mode)
    {
        $widget = $item->get_subject_entity();
        return sprintf($widget->is_page() ? __('feed:new_page') : __('feed:new_section'), 
            $this->get_org_link($item, $mode),
            $this->get_link($item, $widget->get_title())
        );    
    }
}