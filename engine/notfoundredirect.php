<?php

/*
 * A model representing a regex-based HTTP redirect. 
 * 
 * If a request would normally result in a 404 and a NotFoundRedirect matches the current URL, 
 * then the user is forwarded to another URL instead of showing a 404 not found page.
 *
 * This allows merging organizations, changing usernames, and changing site URL patterns 
 * without breaking existing URLs (or having to maintain old urls in the code).
 */
class NotFoundRedirect extends Model
{
    static $table_name = 'not_found_redirects';
    static $table_attributes = array(
        'container_guid' => 0,
        'pattern' => '',
        'replacement' => '',
        'order' => 1000,
    );
    
    function __toString()
    {
        return "id={$this->id} container_guid={$this->container_guid} order={$this->order}: {$this->get_preg_pattern()} -> {$this->replacement}";
    }
    
    static function new_simple_redirect($old_path, $new_path)
    {
        $redirect = new NotFoundRedirect();
        $redirect->pattern = '^'.preg_replace('/[^\w]/', '\\\$0', $old_path).'\b';
        $redirect->replacement = $new_path;
        $redirect->validate();
        return $redirect;
    }

    function validate_error_handler($errno, $errmsg, $filename, $linenum, $vars)
    {            
        if (error_reporting() == 0) // @ sign
            return true; 
            
        throw new ValidationException($errmsg);
    }

    function validate()
    {
        set_error_handler(array('NotFoundRedirect','validate_error_handler'));    
        $this->try_get_redirect_url("/");
        restore_error_handler();    
    }    
    
    function try_get_redirect_url($uri)
    {
        $redirect_url = preg_replace($this->get_preg_pattern(), $this->replacement, $uri, 1, $count);
        if ($count > 0)
        {
            return $redirect_url;
        }    
        return null;
    }
    
    function get_preg_pattern()
    {
        return "#{$this->pattern}#i";
    }
    
    static function query_by_user($user)
    {        
        $query = static::query()->order_by('`order`');
    
        if ($user)
        {
            $query->where('container_guid = ?', $user->guid);
        }
        else
        {
            $query->where('container_guid = 0');
        }
        return $query;
    }
    
    static function get_redirect_url($uri, $user = null)
    {        
        $query = static::query_by_user($user);

        foreach ($query->filter() as $redirect)
        {
            $redirect_url = $redirect->try_get_redirect_url($uri);
            if ($redirect_url)
            {            
                return $redirect_url;
            }
        }
        return null;
    }
}