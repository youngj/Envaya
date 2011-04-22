<?php

/* 
 * A widget that displays the latest activity by an organization (FeedItem objects).
 */
class Widget_Updates extends Widget
{
    function render_view($args = null)
    {
        PageContext::set_rss(true);
        return view("widgets/updates_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/updates_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $this->save();
    }    
}
