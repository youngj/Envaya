<?php

class FeedItemHandler_Partnership extends FeedItemHandler
{
    function render_heading($item, $mode)
    {
        $partner = $item->get_subject_entity();
        $partnerUrl = $partner->get_url();
            
        return sprintf(__('feed:partnership'), 
            $this->get_org_link($item, $mode),
            "<a class='feed_org_name' href='$partnerUrl'>".escape($partner->name)."</a>"
        );            
    }
}