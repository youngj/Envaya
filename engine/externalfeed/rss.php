<?php

/* 
 * Represents an external RSS or Atom feed. Creates RSSItem widgets for
 * imported feed items.
 */
class ExternalFeed_RSS extends ExternalFeed
{
    function get_widget_subclass()
    {
        return 'RSSItem';
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

    static function try_new_from_document($dom, $url)
    {
        Zend::load('Zend_Feed_Reader_FeedSet');
                
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
        return null;
    }                
    
    // default title is hostname of website, minus www.
    function get_default_title()
    {
        $title = parse_url($this->url, PHP_URL_HOST);
        return preg_replace('#^www\.#', '', $title);                
    }
    
    function save()
    {
        if (!$this->title)
        {               
            $this->title = $this->get_default_title();
        }    
        parent::save();
    }
    
    protected function get_entry_external_id($entry)
    {
        return $entry->getId();
    }
    
    protected function get_entry_title($entry)
    {
        return $entry->getTitle();
    }
    
    protected function get_entry_time($entry)
    {
        return $entry->getDateCreated()->getTimestamp(); 
    }
    
    protected function get_entry_link($entry)
    {
        return $entry->getLink();
    }
    
    protected function get_entry_content($entry)
    {
        return $entry->getContent();
    }
            
    protected function _update()
    {
        Zend::load('Zend_Feed_Reader');         
        
        $response = $this->load_feed();
        
        try
        {
            $feed = Zend_Feed_Reader::importString($response->content);        
            $feed->setOriginalSourceUri($this->feed_url);        
            return $this->update_entries($feed);        
        }
        catch (Zend_Feed_Exception $ex)
        {
            throw new DataFormatException($ex->getMessage());
        }
    }
}