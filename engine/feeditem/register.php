<?php

class FeedItem_Register extends FeedItem_EditHome
{
    function render_heading($mode)
    {
        return strtr(__('feed:registered'), 
            array('{name}' => $this->get_user_link($mode))
        );           
    }
}