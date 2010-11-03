<?php

class PageContext
{
    private static $translatable = true;
    private static $translations_available = array();
    private static $theme = 'simple';
    private static $rss = false;
    private static $site_org = null;
    
    static function set_translatable($translatable)
    {
        static::$translatable = $translatable;
    }
    
    static function is_translatable($mode=TranslateMode::All)
    {
        if (!static::$translatable)
        {
            return false;
        }

        foreach (static::$translations_available as $translation)
        {
            if ($mode == TranslateMode::All || $mode == TranslateMode::ManualOnly && $translation->owner_guid)
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
        if (!empty(static::$translations_available))
        {
            return static::$translations_available[0]->get_original_language();
        }
        return get_language();
    }
    
    static function get_available_translations()
    {
        return static::$translations_available;
    }
    
    static function add_available_translation($translation)
    {
        static::$translations_available[] = $translation;
    }

    static function get_theme()
    {
        return static::$theme;
    }
        
    static function set_theme($theme)
    {
        static::$theme = $theme;
    }    
    
    static function has_rss()
    {
        return static::$rss;
    }    
    
    static function set_rss($rss)
    {
        static::$rss = $rss;
    }
   
    static function set_site_org($org)
    {
        static::$site_org = $org;
    }
    
    static function get_site_org()
    {
        return static::$site_org;
    }
}
