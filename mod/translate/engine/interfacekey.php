<?php

/*
 * Represents one of Envaya's localized language strings. Each key name is 
 * a string used in the __() function, e.g. 'discussions:title'.
 */
class InterfaceKey extends Entity
{
    static $table_name = 'interface_keys';
    static $table_attributes = array(
        'name' => '',
        'language_guid' => 0,
        'num_translations' => 0,
        'best_translation' => '',
        'best_translation_guid' => 0,
    );    
    
    function update($recursive = false)
    {
        $this->num_translations = $this->query_translations()->count();

        $best = $this->query_translations()
            ->where('score >= 0')
            ->order_by('score desc, guid desc')
            ->get();
            
        if ($best)
        {
            $this->best_translation = $best->value;
            $this->best_translation_guid = $best->guid;
        }
        else
        {
            $this->best_translation = '';
            $this->best_translation_guid = 0;
        }
        $this->save();
        if ($recursive)
        {
            $this->get_container_entity()->update();
        }
    }
    
    function get_language()
    {
        return InterfaceLanguage::get_by_guid($this->language_guid);
    }
    
    function save()
    {
        if (!$this->language_guid)
        {
            $this->language_guid = $this->get_container_entity()->container_guid;
        }
        parent::save();
    }
    
    public function init_defined_translation($update_recursive = false)
    {
        $group = $this->get_container_entity();
        $defined_group = $group->get_defined_group();
        if ($defined_group)
        {
            $defined_value = @$defined_group[$this->name];
            if ($defined_value && $this->query_translations()->where('value = ?', $defined_value)->is_empty())
            {
                $translation = new InterfaceTranslation();
                $translation->value = $defined_value;
                $translation->container_guid = $this->guid;
                $translation->save();
                $this->update($update_recursive);
            }
        }
    }
        
    function get_defined_language()
    {
        return $this->get_container_entity()->get_defined_language();
    }    
    
    function get_title()
    {
        return $this->name;
    }
    
    function get_url()
    {
        return $this->get_container_entity()->get_url()."/".urlencode($this->name);
    }
    
    function get_best_translation()
    {
        return InterfaceTranslation::get_by_guid($this->best_translation_guid);
    }
        
    function get_placeholders()
    {
        return Language::get_placeholders($this->get_default_value());
    }
    
    function query_translations()
    {
        return InterfaceTranslation::query()->where('container_guid = ?', $this->guid);
    }

    function query_comments()
    {
        return InterfaceKeyComment::query()
            ->where('container_guid = ? OR (key_name = ? AND language_guid = 0)', $this->guid, $this->name);
    }

    
    function get_default_value()
    {
        return @__($this->name, Config::get('language'));
    }
    
    function get_output_view()
    {
        if (strpos($this->get_default_value(), "\n") !== false)
        {
            return 'output/longtext';
        }
        else
        {       
            return 'output/text';
        }
    }
}