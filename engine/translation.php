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

    public function calculateHash()
    {
        return $this->getContainerEntity()->getLanguage() . ":" . sha1($this->getOriginalText());
    }

    public function isStale()
    {
        return $this->calculateHash() != $this->hash;
    }

    public static function filterByLanguageAndOwner($lang, $owner_guid, $limit = 10, $offset = 0, $count = false)
    {
        return static::filterByCondition(array('lang = ?', 'owner_guid = ?'), array($lang, $owner_guid), 'time_created asc', $limit, $offset, $count);
    }
}