<?php

class PageContext
{
    private static $translatable = true;
    private static $translations_available = array();
    private static $theme = 'simple';
    private static $rss = false;
    private static $site_org = null;
    private static $header_html = array();
    private static $submenu = array();    
    
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
    
    static function add_header_html($key, $html)
    {
        if (!isset(static::$header_html[$key]))
        {
            static::$header_html[$key] = $html;
        }
    }
    
    static function get_header_html()
    {
        $res = '';
        foreach (static::$header_html as $key => $html)
        {
            $res .= $html;
        }
        return $res;
    }   
    
    /**
     * Adds an item to the submenu
     *
     * @param string $label The human-readable label
     * @param string $link The URL of the submenu item
     */
    static function add_submenu_item($label, $link, $group = 'topnav') {

        if (!isset(static::$submenu[$group])) 
            static::$submenu[$group] = array();
        
        $item = new stdClass;
        $item->value = $link;
        $item->name = $label;
        static::$submenu[$group][] = $item;
    }

    static function get_submenu_group($groupname, $itemTemplate = 'canvas_header/submenu_template', $groupTemplate = 'canvas_header/submenu_group')
    {
        $submenu_register = static::$submenu;
        if (!isset($submenu_register[$groupname]))
        {
            return '';
        }

        $submenu = array();
        $submenu_register_group = static::$submenu[$groupname];

        $parsedUrl = parse_url($_SERVER['REQUEST_URI']);

        foreach($submenu_register_group as $key => $item)
        {
            $selected = endswith($item->value, $parsedUrl['path']);

            $submenu[] = view($itemTemplate,
                array(
                        'href' => $item->value,
                        'label' => $item->name,
                        'selected' => $selected,
                    ));
        }

        return view($groupTemplate, array(
            'submenu' => $submenu,
            'group_name' => $groupname
        ));
    }    
}
