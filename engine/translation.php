<?php

class Translation extends Model
{
    static $table_name = 'translations';
    static $table_attributes = array(
        'container_guid' => 0,
        'owner_guid' => 0,
        'time_updated' => 0,
        'property' => '',
        'hash' => '',
        'lang' => '',
        'value' => '',
        'html' => 0
    );

    public function save()
    {
        $this->hash = $this->calculate_hash();
        $this->time_updated = time();
        return parent::save();
    }
    
    public function get_container_entity()
    {
        return get_entity($this->container_guid);
    }

    public function get_original_text()
    {
        $obj = $this->get_container_entity();
        $property = $this->property;
        return trim($obj->$property);
    }
    
    public function get_original_language()
    {
        $obj = $this->get_container_entity();
        return $obj->get_language();
    }

    public function calculate_hash()
    {
        return $this->get_container_entity()->get_language() . ":" . sha1($this->get_original_text());
    }

    public function is_stale()
    {
        return $this->calculate_hash() != $this->hash;
    }

    public static function query_by_language_and_owner($lang, $owner_guid)
    {
        return static::query()->where('lang = ?',$lang)->where('owner_guid = ?',$owner_guid)->order_by('time_updated asc');
    }
}