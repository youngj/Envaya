<?php

/* 
 * A widget that displays links to an organization's external websites, social networking profiles, etc.
 */
class Widget_Links extends Widget
{        
    function render_view($args = null)
    {
        return view("widgets/links_view", array('widget' => $this));
    }

    function render_edit()
    {
        return view("widgets/links_edit", array('widget' => $this));
    }
 
    function process_input($action)
    {    
        switch (get_input('action'))
        {
            case 'add':
                return $this->add_link($action);
            case 'linkinfo_js':
                return $this->get_linkinfo_js($action);
            case 'remove':
                return $this->remove_link($action);
            default:
                $this->save();
        }            
    } 
    
    function remove_link($action)
    {
        $guid = get_input('guid');
        
        $org = $this->get_container_user();        
        $site = $org->query_external_sites()->guid($guid)->get();
        if ($site)
        {
            $site->delete();
            SessionMessages::add(__('widget:links:deleted'));
        }        
        
        $this->save();
        $action->redirect($this->get_edit_url());        
    }
    
    function add_link($action)
    {
        $url = get_input('url'); 
        $title = get_input('title');
        $include_feed = get_input('include_feed') == '1';
        $feed_subtype = get_input('feed_subtype');
        $feed_url = get_input('feed_url');    
        $site_subtype = get_input('site_subtype');

        $site_cls = ClassRegistry::get_class($site_subtype);
        ExternalSite::validate_subclass($site_cls);
        ExternalSite::validate_url($url);        
        
        if ($include_feed)
        {
            $feed_cls = ClassRegistry::get_class($feed_subtype);
            ExternalFeed::validate_subclass($feed_cls);
            ExternalFeed::validate_url($feed_url);
        }
        
        $org = $this->get_container_user();                
        $site = $org->query_external_sites()->where('url = ?', $url)->get();
        if (!$site)
        {
            $site = new $site_cls();
            $site->container_guid = $org->guid;
        }
        $site->url = $url;
        $site->title = $title;        
        $site->save();
        
        if ($include_feed)
        {        
            $news = $org->get_widget_by_class('News');
            if ($news->can_add_feed())
            {                
                $news->add_feed_by_url($feed_url, $url, $feed_cls);
            }
        }

        $this->save();
        SessionMessages::add(__('widget:links:added'));
        $action->redirect($this->get_edit_url());        
    }    
    
    function get_linkinfo_js($action)
    {
        $action->set_content_type('text/javascript');                
        $url = get_input('url');
        
        $org = $this->get_container_user();                
        $news = $org->get_widget_by_class('News');        
        
        $link_info = ExternalSite::get_linkinfo($url, $news->can_add_feed());        
        
        $link_info['has_feed'] = $news->query_external_feeds()
            ->where('feed_url = ? OR url = ?', $url, $url)
            ->exists();        
        
        $action->set_content(json_encode($link_info));
    }            
}
