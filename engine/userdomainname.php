<?php

/*
 * A custom domain name for an user or organization using Envaya; 
 * allows them to have a URL like http://www.mywebsite.com 
 * instead of http://envaya.org/myusername
 */
class UserDomainName extends Model
{
    static $table_name = 'org_domain_names';
    static $table_attributes = array(
        'guid' => null,
        'domain_name' => ''
    );       
    
    function invalidate_cache()
    {
        Cache::get_instance()->delete(static::cache_key_for_host($this->domain_name));
    }
    
    function save()
    {
        parent::save();
        $this->invalidate_cache();
    }
    
    function delete()
    {
        parent::delete();
        $this->invalidate_cache();
    }
    
    static function cache_key_for_host($host)
    {
        return Cache::make_key('username_for_host', $host);
    }
    
    static function get_username_for_host($host)
    {    
		$cacheKey = static::cache_key_for_host($host);
        $cache = Cache::get_instance();
        $cachedUsername = $cache->get($cacheKey);
        
        if ($cachedUsername !== null)
        {
            return $cachedUsername;
        }
        else
        {
            $row = static::query()->where('domain_name = ?', $host)->get();
            if ($row)
            {
                $user = User::get_by_guid($row->guid);
                if ($user)
                {
                    $cache->set($cacheKey, $user->username);
                    return $user->username;
                }
            }
            $cache->set($cacheKey, '');
            return '';
        }    
    }
}