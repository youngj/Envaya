<?php

/*
 * Represents a grouping of Envaya's localized language strings. Each group has one file
 * in a language -- languages/en/en_discussions.php corresponds to a group named 'discussions'.
 */
class InterfaceGroup extends Entity
{
    static $table_name = 'interface_groups';
    static $table_attributes = array(
        'name' => '',
        'num_keys' => 0,
    );      
    
    function get_defined_language()
    {
        return $this->get_container_entity()->get_defined_language();
    }
    
    private $defined_group;
    private $defined_group_loaded = false;
    
    function get_defined_group()
    {
        if (!$this->defined_group_loaded)
        {
            $language = $this->get_defined_language();
            $this->defined_group = $language ? $language->get_group($this->name) : null;        
            $this->defined_group_loaded = true;
        }
        return $this->defined_group;            
    }
    
    function get_defined_module_name()
    {
        $default_lang = Config::get('language');
        
        foreach (Config::get('modules') as $module_name)
        {
            $module_root = Engine::get_module_root($module_name);
        
            if (file_exists("{$module_root}/languages/{$default_lang}/{$default_lang}_{$this->name}.php"))
            {
                return $module_name;
            }
        }
        return null;
    }
    
    private $defined_default_group;
        
    function get_defined_default_group()
    {
        if (!isset($this->defined_default_group))
        {
            $default_language = Language::get(Config::get('language'));        
            $this->defined_default_group = @$default_language->get_group($this->name) ?: array();    
        }
        return $this->defined_default_group;
    }
    
    function is_valid_key_name($key_name)
    {
        return array_key_exists($key_name, $this->get_defined_default_group());
    }    
            
    function get_title()
    {
        return $this->name;
    }
    
    function get_url()
    {
        return $this->get_container_entity()->get_url() . "/module/{$this->name}";
    }
    
    function update()
    {
        $all_key_names = array_keys($this->get_defined_default_group());
        $deleted_keys = $this->query_keys()->where_not_in('name', $all_key_names)->filter();
        foreach ($deleted_keys as $deleted_key)
        {
            $deleted_key->disable();
            $deleted_key->save();
        }
    
        $this->num_keys = $this->query_keys()->where("best_translation <> ''")->count();
        $this->save();
    }       
    
    function query_keys()
    {
        return InterfaceKey::query()->where('container_guid = ?', $this->guid);
    }        

    function get_key_by_name($key_name)
    {
        $key = $this->query_keys()->where('name = ?', $key_name)->get();
        if (!$key && $this->is_valid_key_name($key_name))
        {
            $key = $this->new_key_by_name($key_name);    
        }
        return $key;
    }
    
    function new_key_by_name($name)
    {
        $key = $this->get_container_entity()->query_keys()
            ->where('name = ?', $name)
            ->show_disabled(true)
            ->get();

        if ($key)
        {
            $key->enable();
        }
        else
        {
            $key = new InterfaceKey();
            $key->name = $name;
        }
        
        // perhaps the key was moved between groups 
        // (e.g. 'foo:bar' could be moved between 'default' and 'foo' groups)
        $key->container_guid = $this->guid;
        
        $defined_group = $this->get_defined_group();        
        if ($defined_group && !$key->best_translation)
        {
            $key->best_translation = $defined_group[$key->name];
        }
        $key->save();
                
        return $key;
    }
    
    function get_available_keys()
    {
        $group = $this->get_defined_default_group();
        
        $keys = $this->query_keys()
            ->show_disabled(true)
            ->filter();
        
        $keys_map = array();
        foreach ($keys as $key)
        {
            $keys_map[$key->name] = $key;
        }
        
        $available_keys = array();
        $needs_update = false;
        foreach ($group as $key_name => $value)
        { 
            $existing_key = @$keys_map[$key_name];
            if ($existing_key)
            {
                $available_keys[] = $existing_key;
            }
            else
            {        
                $available_keys[] = $this->new_key_by_name($key_name);
                $needs_update = true;
            }
        }
        if ($needs_update)
        {
            $this->update();
        }
        
        return $available_keys;    
    }
}