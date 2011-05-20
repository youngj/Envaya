<?php

class ExternalFeed extends Entity
{
    static $table_name = 'external_feeds';
    static $table_attributes = array(
        'subtype_id' => '',
        'url' => '',
        'feed_url' => '',
        'title' => '',
    );
    
    static $regexes = array(
        '#://[^/]*facebook\.com#i' => 'ExternalFeed_Facebook',
        '#://[^/]*twitter\.com#i' => 'ExternalFeed_Twitter',
    );
    
    function queue_update()
    {
        return FunctionQueue::queue_call(array('ExternalFeed', 'update_by_guid'), array($this->guid));    
    }

    function get_widget_subclass() { throw new NotImplementedException("ExternalFeed::get_widget_subclass"); }
    protected function _update() { throw new NotImplementedException("ExternalFeed::_update"); }

    function get_widget_by_external_id($id)
    {
        // ensure widget_name is less than size of database column        
        $widget_name = "{$this->subtype_id}:" . ((strlen($id) < 90) ? $id : md5($id));
        $container = $this->get_container_entity();
                
        return $container->get_widget_by_name(
            $widget_name, 
            $this->get_widget_subclass()
        );
    }
    
    static function update_by_guid($guid)
    {
        $feed = ExternalFeed::query()->guid($guid)->get();
        if ($feed)
        {
            $feed->update();
        }
    }
    
    function update()
    {
        if (!$this->is_enabled())
            return;
    
        $container = $this->get_container_entity();
        if (!$container || !$container->is_enabled())
            return;

        $root = $this->get_root_container_entity();
        if (!$root || !$root->is_enabled())
            return;
            
        // avoid rechecking the same feed too frequently
        $time_checked = $this->get_metadata('time_checked');
        $time = time();
        if ($time - $time_checked < 60 * 5)
        {
            echo "ignoring {$this->feed_url}, too recent\n";
            return;
        }
        
        echo "updating {$this->feed_url}...\n";
            
        $this->set_metadata('time_checked', $time);
        $this->save();

        $this->_update();
    }
    
    static function validate_url($url)
    {
        Web_Request::validate_url($url);            
        if (static::is_local_url($url))
        {
            throw new ValidationException(
                strtr(__('web:invalid_url'), array('{url}' => $url))."\n".
                __('web:try_again')
            );
        }            
    }
    
    static function validate_subclass($cls)
    {
        if (!$cls || !preg_match('/^ExternalFeed_/', $cls))
        {
            throw new ValidationException("Invalid feed class: " . $cls);           
        }
        return $cls;        
    }       
    
    static function is_local_url($url)
    {
        $parsed = parse_url($url);
        $host = strtolower($parsed['host']);
        if (strpos($host, Config::get('domain')) !== false
            || OrgDomainName::get_username_for_host($host))
        {
            return true;
        }
        else
        {
            return false;
        }
    }               
    
    static function try_new_from_web_response($response)
    {
        $headers = $response->headers;
        $url = $response->url;
        
        if (static::is_local_url($url))
        {
            return null;
        }
         
        $feed_cls = 'ExternalFeed_RSS';
        foreach (static::$regexes as $regex => $cls)
        {
            if (preg_match($regex, $url))
            {
                $feed_cls = $cls;
                break;
            }
        }
               
        if (isset($headers['Content-Type']))
        {            
            $feed = $feed_cls::try_new_from_content_type($headers['Content-Type']);            
            if ($feed)
            {    
                Zend::load('Zend_Feed_Reader');
                $feed = Zend_Feed_Reader::importString($res);
                
                $feed->url = $feed->getLink();
                $feed->feed_url = $url;
            }
            else if (strpos($headers['Content-Type'], 'text/html') !== false)
            {
                $feed = $feed_cls::try_new_from_html($response->content, $url);            
            }
        }
        
        return $feed;
    }
    
    static function try_new_from_content_type($content_type)
    {
        $feed_content_types = array('application/atom+xml', 'application/rss+xml');
        foreach ($feed_content_types as $feed_content_type)
        {
            if (strpos($content_type, $feed_content_type) !== false)
            {
                $cls = get_called_class();
                return new $cls();
            }
        }
        return null;
    }
        
    static function try_new_from_html($html, $url)
    {
        Zend::load('Zend_Feed_Reader_FeedSet');
        
        $dom = new DOMDocument;
        $status = @$dom->loadHTML($html);                
        if ($status)
        {
            $feedSet = new Zend_Feed_Reader_FeedSet();
            $links = $dom->getElementsByTagName('link');
            $feedSet->addLinks($links, $url);
            foreach ($feedSet as $feedItem)
            {
                $feed = static::try_new_from_content_type($feedItem['type']);
                if ($feed) 
                {
                    $feed->url = $url;
                    $feed->feed_url = $feedItem['href'];
                    return $feed;
                }
            }
        }
        return null;
    }    
    
}