<?php

class FeedItemHandler_Report extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        $report = $item->get_subject_entity();        
    
        return sprintf(__('feed:report'), 
            $this->get_org_link($item, $mode),                
            $this->get_link($item, $report->get_title())
        );    
    }
    
    function render_content($item, $mode)
    {
        return '';
    }
}