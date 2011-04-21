<?php
class Widget_Generic extends Widget
{
    function render_view()
    {
        return view("widgets/generic_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/generic_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $prevContent = $this->content;
        $lastUpdated = $this->time_updated;

        $title = get_input('title');
        if ($title)
        {
            $this->title = $title;
        }
        
        $content = get_input('content');
                
        $this->set_content($content);
        $this->save();        
        
        $revision = ContentRevision::get_recent_draft($this);
        $revision->time_updated = time();
        $revision->status = ContentRevision::Published;
        $revision->content = $content;
        $revision->save();                
        
        if ($this->content)
        {
            if (!$prevContent)
            {
                $this->post_feed_items_new();
            }
            else if (!Session::isadminloggedin() && time() - $lastUpdated > 86400)
            {
                $this->post_feed_items();
            }
        }     
    }
}