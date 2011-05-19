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
        
        $org = $this->get_root_container_entity();        
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

        if (!$this->is_valid_url($url))
        {
            throw new ValidationException(strtr(__('widget:links:invalid'), array('{url}' => $url)));
        }       
        
        $title = get_input('title');
        $include_feed = get_input('include_feed') == 'yes';
        
        $feed_subtype = get_input('feed_subtype');
        $feed_url = get_input('feed_url');
        
        $org = $this->get_root_container_entity();        
        
        $site = $org->query_external_sites()->where('url = ?', $url)->get();
        if (!$site)
        {
            $site = new ExternalSite();
            $site->container_guid = $org->guid;
        }
        $site->url = $url;
        $site->title = $title;        
        $site->save();
        
        if ($include_feed && $feed_url)
        {
            $feed_cls = EntityRegistry::get_subtype_class($feed_subtype);
            $news = $org->get_widget_by_class('News');

            if ($news->guid && $feed_cls && preg_match('/^ExternalFeed/', $feed_cls))
            {
                $feed = new $feed_cls();
                $feed->url = $feed_url;            
                $feed->container_guid = $news->guid;
                $feed->save();
            }
        }

        $this->save();
        SessionMessages::add(__('widget:links:added'));
        $action->redirect($this->get_edit_url());        
    }
    
    function is_valid_url($url)
    {
        $parsed = parse_url($url);        
        
        $host = @$parsed['host'];
        $scheme = @$parsed['scheme'];
        
        // don't let people make us fetch suspicious URLs.
        // (they could possibly point to our own servers, and the request would be made behind the firewall)
        // only allow http or https schemes on standard ports, with no username/password or IP addresses.
               
        if (strlen($url) > 100
            || isset($parsed['port'])      
            || isset($parsed['user']) 
            || isset($parsed['pass']) 
            || !$host
            || ($scheme != 'http' && $scheme != 'https')
            || !preg_match('/^([a-z0-9]([a-z0-9\-]*[a-z0-9])?\.)+([a-z0-9][a-z0-9\-]*[a-z0-9])$/i', $host) 
                    // valid hostnames that have at least one dot (not local network hostnames)
            || preg_match('/(\]|\d|arpa|localhost)$/i', $host)     // ip addresses
            )
        {
            return false;
        }               
        
        return true;
    }
    
    function get_linkinfo_js($action)
    {
        $action->set_content_type('text/javascript');
        
        $url = trim(get_input('url'));
        if (!preg_match('#://#', $url))
        {
            $url = "http://{$url}";
        }       
                
        $cur_url = $url;
                
        for ($redirect = 0; $redirect < 3; $redirect++)
        {            
            if (!$this->is_valid_url($cur_url))
            {
                throw new ValidationException(strtr(__('widget:links:invalid'), array('{url}' => $url)));
            }                       
        
            $ch = curl_init();
            
            curl_setopt($ch, CURLOPT_URL, $cur_url);
            curl_setopt($ch, CURLOPT_REFERER, $action->get_request()->referrer ?: Config::get('url'));
            
            // put magic words in the user agent string so they give us the version normal people see
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 EnvayaBot/0.1");
            curl_setopt($ch, CURLOPT_TIMEOUT, 6);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false); // Location header could point to unsafe URL; need to check each one
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $res = curl_exec($ch);                   
            $info = curl_getinfo($ch);    
                
            curl_close($ch);
            
            if ($info['http_code'] == 302 || $info['http_code'] == 301)
            {            
                if (preg_match('/\nLocation: ([^\s]+)/', $res, $match))
                {
                    $cur_url = $match[1];
                    continue;
                }
            }     
            break;
        }
        
        if (!$res || $info['http_code'] != 200)
        {
            throw new ValidationException(strtr(__('widget:links:broken'), array('{url}' => $url)));
        }
        
        $action->set_response(json_encode(array(
            'url' => $url,
            'feed_url' => '',
            'feed_subtype' => '',
        )));
    }
}
