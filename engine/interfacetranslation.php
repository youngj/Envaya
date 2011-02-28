<?php

/*
 * Represents a user-contributed translation (or correction to existing translation)
 * of a piece of text from Envaya's user interface into a particular language.
 *
 * InterfaceTranslations are not directly used for the user interface translations; 
 * first they must be exported and saved as PHP files in languages/
 * 
 * (Contrast with Translation class, which represents a translation of user-generated
 * content.)
 */
class InterfaceTranslation extends Model
{
    static $table_name = 'interface_translations';
    static $table_attributes = array(
        'key' => '',
        'lang' => '',
        'value' => '',
        'owner_guid' => 0,
        'approval' => 0
    );

    static function get_by_key_and_lang($key, $lang)
    {        
        return static::query()->where('`key` = ?', $key)->where('lang = ?', $lang)->get();
    }

    static function filter_by_lang($lang)
    {
        return static::query()->where('lang = ?', $lang)->filter();
    }
}
