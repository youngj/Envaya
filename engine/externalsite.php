<?php

/*
 * Represents a link to a user's external website.
 * The container entity (container_guid) for an ExternalSite is an Organization.
 *
 * Different subclasses of ExternalSite could potentially have different user
 * interfaces; e.g. a link to a Facebook page could have a Facebook logo / like button.
 */
class ExternalSite extends Entity
{
    static $table_name = 'external_sites';
    static $table_attributes = array(
        'subtype_id' => '',
        'url' => '',
        'title' => '',
    );
    
    static $regexes = array(
        '#://[^/]*facebook\.com#i' => 'ExternalSite_Facebook',
        '#://[^/]*twitter\.com#i' => 'ExternalSite_Twitter',
    );
    
    function get_default_view_name()
    {
        return 'object/externalsite';
    }
    
    static function validate_url($url)
    {
        Web_Request::validate_url($url);
    }
        
    
    static function validate_subclass($cls)
    {
        if (!$cls || !preg_match('/^ExternalSite/', $cls))
        {
            throw new ValidationException("Invalid site class: " . $cls);           
        }
        return $cls;        
    }
    
    static function new_from_url($url)
    {
        $site = null;
        
        foreach (ExternalSite::$regexes as $regex => $cls)
        {
            if (preg_match($regex, $url))
            {
                $site = new $cls;
                break;
            }
        }
        if (!$site)
        {
            $site = new ExternalSite();
        }
        $site->url = $url;
        return $site;
    }    
    
    /*
     * Retrieves a URL and returns various information about
     * the page which could be used to create ExternalSite
     * and ExternalFeed entities for that URL.
     */
    static function get_linkinfo($url, $check_feed = true)
    {
        $url = trim($url);
        if (!preg_match('#://#', $url))
        {
            $url = "http://{$url}";
        }                       
         
        $request = new Web_Request($url);
        
        $response = $request->get_response();
        
        if (!$response->content || $response->status != 200)
        {
            throw new ValidationException(
                strtr(__('web:url_error'), array('{url}' => $url))."\n".
                __('web:try_again')
            );
        }        
       
        if ($check_feed)
        {       
            $feed = ExternalFeed::try_new_from_web_response($response);        
        }
        else
        {
            $feed = null;
        }
        $site = static::new_from_url($url);
        
        
        return array(
            'url' => $feed ? $feed->url : $url,
            'title' => $response->get_title() ?: '',
            'site_subtype' => $site->subtype_id,
            'feed_url' => $feed ? $feed->feed_url : null,
            'feed_subtype' => $feed ? $feed->subtype_id : null,
        );
    }        
}