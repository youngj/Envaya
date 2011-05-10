<?php

/*
 * Represents a language of Envaya's user interface that is available for users to translate.
 * The language may not necessarily be in the languages/ directory or in Config::get('languages')
 * (in which case people can't yet actually use this language for Envaya's interface).
 */
class InterfaceLanguage extends Entity
{
    static $table_name = 'interface_languages';
    static $table_attributes = array(
        'code' => '',
        'name' => '',
    );   
    
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
        return InterfaceGroup::query()->where('container_guid = ?', $this->guid);
    }
    
    function query_comments()
    {
        return InterfaceKeyComment::query()
            ->where('language_guid = ? OR language_guid = 0', $this->guid);
    }
    
    function query_keys()
    {
        return InterfaceKey::query()->where('language_guid = ?', $this->guid);
    }    
    
    function query_translations()
    {
        return InterfaceTranslation::query()->where('language_guid = ?', $this->guid);
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
        
        $groups = $this->query_groups()
            ->show_disabled(true)
            ->filter();
        
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