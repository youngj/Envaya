<?php

class FeaturedSite extends Entity
{
    static $table_name = 'featured_sites';

    static $table_attributes = array(
        'user_guid' => 0,
        'content' => '',
        'image_url' => '',
        'data_types' => 0,
        'language' => '',
        'active' => 0,
    );
    
    static function active_cache_key()
    {
        return make_cache_key("featuredsite:active");
    }
    
    function save()
    {
        get_cache()->delete(static::active_cache_key());
        parent::save();
    }
    
    static function get_active()
    {           
        return cache_result(function() {    
            return FeaturedSite::query()->where('active=1')->get();
        }, static::active_cache_key());
    }
}