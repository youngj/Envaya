<?php

/* 
 * A widget that displays links to an organization's external websites, social networking profiles, etc.
 */
class Widget_Links extends Widget
{        
    static $default_menu_order = 115;
    static $default_widget_name = 'links';    
    
    function get_default_title()
    {
        return __("widget:links");
    }

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
        switch (Input::get_string('action'))
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
        $guid = Input::get_string('guid');
        
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
        $url = Input::get_string('url'); 
        $title = Input::get_string('title');
        $include_feed = Input::get_string('include_feed') == '1';
        $feed_subtype = Input::get_string('feed_subtype');
        $feed_url = Input::get_string('feed_url');    
        $site_subtype = Input::get_string('site_subtype');

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
            $news = Widget_News::get_for_entity($org);
            if ($news && $news->can_add_feed())
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
        $url = Input::get_string('url');
        
        $org = $this->get_container_user();                
        $news = Widget_News::get_for_entity($org);
        
        $link_info = ExternalSite::get_linkinfo($url, $news ? $news->can_add_feed() : false);
        
        if ($news)
        {
            $link_info['has_feed'] = $news->query_external_feeds()
                ->where('feed_url = ? OR url = ?', $url, $url)
                ->exists();        
        }
        
        $action->set_content(json_encode($link_info));
    }            
}
