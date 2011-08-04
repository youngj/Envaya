<?php

/*
 * Extension for PageContext that collects information about the translations used on the current page.
 */
class Mixin_TranslateContext extends Mixin
{
    private static $translations_available = array();
    private static $orig_lang = null;

    /*
     * Notes that a particular piece of translatable content appears on the page, 
     * which the user may wish to translate into their own language.
     */    
    static function add_available_translation($translation)
    {
        static::$translations_available[] = $translation;
    }

    static function get_translation_url($include_hardcoded = false)
    {
        $lang = Language::get_current_code();

        $keys = array();
        foreach (static::get_available_translations() as $trans)
        {
             $keys[] = $trans->get_container_entity()->name;
        }

        if ($include_hardcoded && $lang != Config::get('language'))
        {    
            $keys = array_merge($keys, Language::current()->get_requested_keys());            
        }
        
        if ($keys)
        {
            // compress keys in URL to try to stay under the 2083 byte maximum length for internet explorer under most circumstances        
            $b64 = base64_encode(gzcompress(
                Request::get_uri() . ' ' .
                implode(',', $keys), 4));
                
            $b64 = rtrim($b64, '='); // trailing = signs not necessary for php base64_decode
            
            $url = "/tr/page/".urlencode_alpha($b64);    
            
            if (sizeof($keys) == 1)
            {
                $url .= "/".urlencode_alpha($keys[0]);
            }
            
            return $url;
        }
        return null;
    }

    static function has_translation($mode=TranslateMode::All)
    {
        foreach (static::$translations_available as $translation)
        {
            if ($mode == TranslateMode::All
                || $mode == TranslateMode::Approved && $translation->is_approved()
                || $mode == TranslateMode::Automatic && !$translation->owner_guid && !$translation->is_approved())
            {
                return true;
            }
        }
        return false;
    }
    
    static function has_stale_translation()
    {
        foreach (static::$translations_available as $translation)
        {
            if ($translation->is_stale())
            {
                return true;
            }
        }
        return false;
    }
    
    static function get_original_language()
    {        
        return static::$orig_lang ?: Language::get_current_code();
    }
    
    static function set_original_language($lang)
    {
        static::$orig_lang = $lang;
    }
    
    static function get_available_translations()
    {
        return static::$translations_available;
    }    
}