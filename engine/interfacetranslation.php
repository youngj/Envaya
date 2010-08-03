<?php

class InterfaceTranslation extends ElggObject
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
        return static::getByCondition(array('`key` = ?', 'lang = ?'), array($key, $lang));
    }

    static function filterByLang($lang)
    {
        return static::filterByCondition(array('lang = ?'), array($lang), '', 10000);
    }
}
