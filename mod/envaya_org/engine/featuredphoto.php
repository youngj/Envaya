<?php

/* 
 * A photo that is chosen by an administrator to be featured on the home page slideshow.
 */
class FeaturedPhoto extends Entity
{
    static $table_name = 'featured_photos';

    static $table_attributes = array(
        'user_guid' => null,
        'image_url' => '',        
        'x_offset' => 0,
        'y_offset' => 0,
        'weight' => 1,
        'href' => '',
        'caption' => '',
        'org_name' => '',
        'language' => '',
        'active' => 1
    );

    static function json_cache_key()
    {
        return Cache::make_key("featuredphoto:json");
    }    
    
    function save()
    {
        Cache::get_instance()->delete(static::json_cache_key());
        parent::save();
    }    
    
    function delete()
    {
        Cache::get_instance()->delete(static::json_cache_key());
        parent::delete();
    }        
    
    static function get_json_array()
    {
        return Cache::get_instance()->cache_result(function() {    
            return json_encode(array_map(
                function($p) { return $p->js_properties(); }, 
                FeaturedPhoto::query()->where('active=1')->filter()
            ));   
        }, static::json_cache_key());
    }
    
    public function js_properties()
    {
        return array(
            'url' => $this->image_url,
            'x' => $this->x_offset,
            'y' => $this->y_offset,
            'weight' => $this->weight,
            'href' => $this->href,
            'caption' => $this->caption,
            'org' => $this->org_name
        );
    }    
}