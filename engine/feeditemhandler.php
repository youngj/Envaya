<?php

/* 
 * Base class for rendering a view of a FeedItem;
 * Subclasses for each action_name are defined in engine/feeditemhandler
 */
class FeedItemHandler
{
    function is_valid($item)
    {
        return !!$item->get_subject_entity();
    }

    function render_heading($item, $mode)
    {
        return '';
    }

    function render_content($item, $mode)
    {
        return '';
    }

    function render_thumbnail($item, $mode)
    {
        return '';
    }
    
    function get_link($item, $title)
    {
        return "<a href='{$this->get_url($item)}'>".escape($title)."</a>";
    }
    
    function get_org_link($item, $mode)
    {
        $org = $item->get_user_entity();
        if ($mode == 'self')
        {
            return escape($org->name);
        }
        else
        {
            return "<a class='feed_org_name' href='{$org->get_url()}'>".escape($org->name)."</a>";
        }
    }
    
    function get_url($item)
    {
        return rewrite_to_current_domain($item->get_subject_entity()->get_url());
    }    
}