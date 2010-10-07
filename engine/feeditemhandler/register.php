<?php

class FeedItemHandler_Register extends FeedItemHandler_EditHome
{
    function render_heading($item, $mode)
    {
        return sprintf(__('feed:registered'), 
            $this->get_org_link($item, $mode)
        );    
    }
}