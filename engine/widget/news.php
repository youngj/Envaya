<?php

/* 
 * A container widget that displays child widgets in reverse chronological order,
 * like a blog. Normally child widgets are of type Widget_Post.
 */
class Widget_News extends Widget
{
    function get_view_types()
    {
        return array('rss');
    }    

    function render_view($args = null)
    {
        $end_guid = (int)get_input('end');    
        return view("widgets/news_view", array('widget' => $this, 'end_guid' => $end_guid));
    }

    function render_edit()
    {
        return view("widgets/news_edit", array('widget' => $this));
    }

    function process_input($action)
    {
        $this->save();
    }    
    
    function render_add_child()
    {
        return view("news/add_post", array('widget' => $this));
    }
    
    function render_add_child_title()
    {
        return sprintf(__('widget:edit_title'), $this->get_title());
    }
    
    function new_child_widget_from_input()
    {           
        return $this->get_widget_by_name(get_input('uniqid'), 'Post');
    }    
}