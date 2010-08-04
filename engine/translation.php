<?php

class Translation extends ElggObject
{
    static $subtype_id = T_translation;
    static $table_name = 'translations';
    static $table_attributes = array(
        'hash' => '',
        'property' => '',
        'lang' => '',
        'value' => '',
        'html' => 0
    );

    public function save()
    {
        $this->hash = $this->calculateHash();
        return parent::save();
    }

    public function getOriginalText()
    {
        $obj = $this->getContainerEntity();
        $property = $this->property;
        return trim($obj->$property);
    }
    
    public function getOriginalLanguage()
    {
        $obj = $this->getContainerEntity();
        return $obj->getLanguage();
    }

    public function calculateHash()
    {
        return $this->getContainerEntity()->getLanguage() . ":" . sha1($this->getOriginalText());
    }

    public function isStale()
    {
        return $this->calculateHash() != $this->hash;
    }

    public static function queryByLanguageAndOwner($lang, $owner_guid)
    {
        return static::query()->where('lang = ?',$lang)->where('owner_guid = ?',$owner_guid)->order_by('time_created asc');
    }
}