<?php

class InterfaceTranslation extends Entity
{
    static $subtype_id = T_interface_translation;
    static $table_name = 'interface_translations';
    static $table_attributes = array(
        'key' => '',
        'lang' => '',
        'value' => '',
        'approval' => 0
    );

    static function getByKeyAndLang($key, $lang)
    {        
        return static::query()->where('`key` = ?', $key)->where('lang = ?', $lang)->get();
    }

    static function filterByLang($lang)
    {
        return static::query()->where('lang = ?', $lang)->filter();
    }
}
