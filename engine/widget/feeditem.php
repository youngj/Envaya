<?php

class Widget_FeedItem extends Widget_Post
{
    function render_edit()
    {
        return view("widgets/feeditem_edit", array(
            'widget' => $this,
            'is_primary' => true,
        ));
    }        
    
    function get_date_view()
    {
        return 'widgets/feeditem_view_date';
    }
    
    function get_title_view()
    {
        return 'widgets/post_view_title';
    }
    
    function get_feed_name()
    {
        return $this->get_metadata('feed_name') ?: 'RSS';
    }

    function process_input($action)
    {
        $this->save();
    }        
}
