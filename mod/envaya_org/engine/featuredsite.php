<?php

/* 
 * A site that is chosen by an administrator to be featured on the home page 
 * or the /org/featured page. Each featured site has an image and summary text.
 */
class FeaturedSite extends Entity
{
    static $table_name = 'featured_sites';

    static $table_attributes = array(
        'image_url' => '',
        'active' => 0,
    );
    static $mixin_classes = array(
        'Mixin_Content'
    );    
    
    static function active_cache_key()
    {
        return Cache::make_key("featuredsite:active");
    }
    
    function save()
    {
        Cache::get_instance()->delete(static::active_cache_key());
        parent::save();
    }
    
    static function get_active()
    {           
        return Cache::get_instance()->cache_result(function() {    
            return FeaturedSite::query()->where('active=1')->get();
        }, static::active_cache_key());
    }
}