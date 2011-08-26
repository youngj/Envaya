<?php

/* 
 * A widget that implements a single blog post (news update), typically
 * as a child of a Widget_News container.
 */
class Widget_Post extends Widget_Generic
{
    public function get_default_title()
    {
        return __("widget:news");
    }

    function render_view($vars = null)
    {
        return view('widgets/post_view', array(
            'widget' => $this,
            'is_primary' => @$vars['is_primary'],
        ));
    }

    function get_content_view()
    {
        return 'widgets/post_view_content';
    }
    
    function get_date_view()
    {
        return 'widgets/post_view_date';
    }
    
    function get_title_view()
    {
        return 'widgets/post_view_title';
    }        
        
    function process_input($action)
    {
        $content = get_input('content');
        if (empty($content))
        {
            throw new ValidationException(__("widget:post:blank"));
        }
        parent::process_input($action);
    }
    
    public function get_url()
    {
        $org = $this->get_root_container_entity();
        if ($org)
        {
            return $org->get_url() . "/post/" . $this->get_url_slug();
        }
        return '';
    }    
    
    public function get_base_url()
    {
        return $this->get_url();
    }
       
    function post_feed_items_edit() {}
    
    function post_feed_items()
    {
        $org = $this->get_root_container_entity();
        $recent = timestamp() - 60*60*6;
        
        $recent_update = $org->query_feed_items()
            ->where("action_name in ('news','newsmulti')")
            ->where('time_posted > ?', $recent)
            ->order_by('id desc')
            ->get();
        
        if ($recent_update)
        {
            $time = timestamp();
        
            foreach ($recent_update->query_items_in_group()->filter() as $r)
            {
                $r->action_name = 'newsmulti';
                $r->subject_guid = $this->guid;
                $r->time_posted = $time;
                $prev_count = @$r->args['count'] ?: 1;
                $r->args = array('count' => $prev_count + 1);
                $r->save();
            }
        }
        else
        {                
            FeedItem_News::post($org, $this);
        }
    }
}