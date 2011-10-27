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

    function query_external_feeds()
    {
        return ExternalFeed::query()->where('container_guid = ?', $this->guid);
    }
    
    function can_add_feed()
    {
        return $this->guid && $this->query_external_feeds()->count() < 3;
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

    function render_add_child()
    {
        return view("news/add_post", array('widget' => $this));
    }
        
    function new_child_widget_from_input()
    {           
        return $this->get_widget_by_name(get_input('uniqid'), 'Post');
    }    
    
    function process_input($action)
    {    
        switch (get_input('action'))
        {
            case 'add_feed':
                return $this->add_feed($action);
            case 'add_link':
                return $this->add_link($action);                
            case 'linkinfo_js':
                return $this->get_linkinfo_js($action);
            case 'remove_feed':
                return $this->remove_feed($action);
            default:
                $this->save();
        }            
    }    
    
    function add_link($action)
    {
        $org = $this->get_container_user();
        $home = $org->get_widget_by_class('Home');
        $links = $home->get_widget_by_class('Links');
        
        $action->redirect("{$links->get_edit_url()}?url=" . urlencode(get_input('url')));
    }
    
    function remove_feed($action)
    {
        $guid = get_input('guid');
        $remove_posts = get_input('remove_posts') == '1';
        
        $feed = $this->query_external_feeds()->guid($guid)->get();
        if ($feed)
        {
            if ($remove_posts)
            {
                $posts = $this->query_widgets()
                    ->with_metadata('feed_guid', $feed->guid)
                    ->filter();
                    
                // delete rather than disabling posts so that 
                // we can create posts again if this feed is later reenabled
                // (if the post is disabled, it may have been deleted explicitly by the user)
                foreach ($posts as $post)
                {
                    $post->delete();
                }
            }        
        
            $feed->delete();            
            SessionMessages::add(__('widget:links:deleted'));
        }
        
        $this->save();
        $action->redirect($this->get_edit_url());        
    }
        
    function add_feed_by_url($feed_url, $url, $feed_cls)
    {               
        $feed = $this->query_external_feeds()->where('feed_url = ? OR url = ?', $feed_url, $url)->get();            
        if (!$feed)
        {
            $feed = new $feed_cls();
            $feed->container_guid = $this->guid;
        }
        $feed->url = $url;            
        $feed->feed_url = $feed_url;                
        $feed->save();
        $feed->queue_update();
        return $feed;
    }
    
    function add_feed($action)
    {
        $url = get_input('url'); 
        $feed_url = get_input('feed_url'); 
        $feed_subtype = get_input('feed_subtype'); 
        
        ExternalSite::validate_url($url);
        ExternalFeed::validate_url($feed_url);
        $feed_cls = EntityRegistry::get_subtype_class($feed_subtype);
        ExternalFeed::validate_subclass($feed_cls);
        
        if (!$this->can_add_feed())
        {
            throw new ValidationException("cannot add any more feeds");
        }        
        
        $this->add_feed_by_url($feed_url, $url, $feed_cls);
        
        $this->save();
        SessionMessages::add(__('widget:links:added').' '.strtr(__('widget:news:feeds:delay'), array('{url}' => $url)));
        
        if (get_input('add_link'))
        {
            $this->add_link($action);
        }
        else
        {
            $action->redirect($this->get_edit_url());     
        }
    }    
    
    function get_linkinfo_js($action)
    {
        $action->set_content_type('text/javascript');                
        $url = get_input('url');
        $link_info = ExternalSite::get_linkinfo($url);     
        
        $action->set_content(json_encode($link_info));
    }
}