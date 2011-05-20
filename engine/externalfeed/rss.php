<?php

class ExternalFeed_RSS extends ExternalFeed
{
    function get_widget_subclass()
    {
        return 'RSSItem';
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
    
    static function absolutize_url($rel, $base)
    {
        // from http://nashruddin.com/PHP_Script_for_Converting_Relative_to_Absolute_URL,
        // no license information found
       
        if (parse_url($rel, PHP_URL_SCHEME) != '') 
            return $rel;
       
        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') 
            return $base.$rel;
       
        $parsed_base = parse_url($base);
        
        $path = @$parsed_base['path'];
        $scheme = @$parsed_base['scheme'];
        $host = @$parsed_base['host'];
                    
        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);
     
        /* destroy path if relative url points to root */
        if ($rel[0] == '/') 
            $path = '';
       
        /* dirty absolute URL */
        $abs = "$host$path/$rel";
     
        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}
       
        return $scheme.'://'.$abs;
    }    
    
    protected function _update()
    {
        Zend::load('Zend_Feed_Reader');         

        $request = new Web_Request($this->feed_url);
        $response = $request->get_response();
        
        if ($response->status != 200)
            throw new IOException("Error {$response->status} retrieving feed");
        
        try
        {
            $feed = Zend_Feed_Reader::importString($response->content);        
            $feed->setOriginalSourceUri($this->feed_url);
        
            $language = null;
            $changed = false;            

            foreach ($feed as $entry)
            {            
                $external_id = $entry->getId();
                $widget = $this->get_widget_by_external_id($external_id);
                    
                echo "  $external_id => $widget->widget_name ($widget->guid)\n";                
                    
                if ($widget->guid)
                    continue;                
                    
                $content = $entry->getContent();
                    
                // assume items in a feed are all in the same language to avoid checking Google Translate for each one                
                if (!$language && $content)
                {
                    $language = GoogleTranslate::guess_language($this->content);
                }
                    
                $widget->enable();
                $widget->language = $language;
                $widget->title = $entry->getTitle();
                $widget->time_published = $entry->getDateCreated()->getTimestamp();
                $widget->set_content($content);
                                                    
                $link = static::absolutize_url($entry->getLink(), $this->url);
           
                $widget->set_metadata('link', $link);
                $widget->set_metadata('feed_guid', $this->guid);
                $widget->set_metadata('feed_name', $this->title);
                $widget->save();
                
                $changed = true;
            }
            
            return $changed;
        }
        catch (Zend_Feed_Exception $ex)
        {
            throw new DataFormatException($ex->getMessage());
        }
    }
}