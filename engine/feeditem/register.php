<?php

class FeedItem_Register extends FeedItem_EditHome
{
    function render_heading($mode)
    {
        return sprintf(__('feed:registered'), 
            $this->get_org_link($mode)
        );    
    }
}