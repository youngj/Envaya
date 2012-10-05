<?php

/*
 * Represents a language of Envaya's user interface that is available for users to translate.
 * The language may not necessarily be in the languages/ directory or in Config::get('languages')
 * (in which case people can't yet actually use this language for Envaya's interface).
 */
class TranslationLanguage extends Entity
{
    static $table_name = 'translation_languages';
    static $table_attributes = array(
        'code' => '',
        'name' => '',
    );   
    
    private static $code_map;
        
    static function get_by_code($code)
    {
        if (!isset(static::$code_map))
        {
			$cache_key = Cache::make_key('language_code_map');
		
            $code_map = Cache::get_instance()->get($cache_key);
            if (!$code_map)
            {
                static::update_code_map();
            }
            else
            {
                static::$code_map = $code_map;
            }
        }
    
        if (!isset(static::$code_map[$code]))
        {
            $language = new TranslationLanguage();
            $language->code = $code;
            $language->name = @__("lang:$code", $code);
            $language->save();

            static::$code_map[$code] = $language;
        }
        return static::$code_map[$code];
    }
    
    static function update_code_map()
    {        
        $all_languages = static::query()->filter();
        $code_map = array();
        foreach ($all_languages as $language)
        {
            $code_map[$language->code] = $language;
        }        
        static::$code_map = $code_map;
		
		$cache_key = Cache::make_key('language_code_map');		
		Cache::get_instance()->set($cache_key, $code_map);
    }
    
    function save()
    {
        if (!$this->container_guid)
        {
            $this->set_container_entity(UserScope::get_root());
        }
    
        parent::save();        
        static::update_code_map();
    }
    
    function get_current_base_code()
    {
        $base_lang = Language::get_current_code();
        if ($base_lang == $this->code) // no sense translating from one language to itself
        {
            return Config::get('language');
        }
        return $base_lang;
    }
    
    function get_defined_language()
    {
        return Language::get($this->code);
    }
    
    function get_title()
    {
        return "{$this->name} ({$this->code})";
    }
    
    function get_url()
    {
        return "/tr/{$this->code}";
    }
    
    function get_admin_url()
    {
        return "/tr/admin/{$this->code}";
    }
    
    function query_groups()
    {
        return InterfaceGroup::query()
            ->where('container_guid = ?', $this->guid);
    }
    
    function query_comments()
    {
        return TranslationKeyComment::query()
            ->where('language_guid = ? OR language_guid is null', $this->guid);
    }
    
    function query_keys()
    {
        return TranslationKey::query()->where('language_guid = ?', $this->guid);
    }    
    
    function query_translations()
    {
        return Translation::query()->where('language_guid = ?', $this->guid);
    }    

    function query_votes()
    {
        return TranslationVote::query()->where('language_guid = ?', $this->guid);
    }
    
    function new_group_by_name($group_name)
    {
        $group = new InterfaceGroup();
        $group->name = $group_name;
        $group->container_guid = $this->guid;
        return $group;
    }
    
    function get_available_groups()
    {
        $en = Language::get('en');
        $group_names = $en->get_all_group_names();
        sort($group_names);
        
        $groups = $this->query_groups()->where('status = ?', InterfaceGroup::Enabled)->filter();
        
        $groups_map = array();
        foreach ($groups as $group)
        {
            $groups_map[$group->name] = $group;
        }
        
        $available_groups = array();
        foreach ($group_names as $group_name)
        {
            $available_groups[] = @$groups_map[$group_name] ?: $this->new_group_by_name($group_name);
        }
        return $available_groups;
    }
    
    function query_translator_stats()
    {
        return TranslatorStats::query()->where('container_guid = ?', $this->guid);
    }
    
    function get_stats_for_user($user)
    {
        $stats = $this->query_translator_stats()->where('owner_guid = ?', $user->guid)->get();
        if (!$stats)
        {
            $stats = new TranslatorStats();
            $stats->container_guid = $this->guid;
            $stats->owner_guid = $user->guid;            
        }
        return $stats;
    }
}