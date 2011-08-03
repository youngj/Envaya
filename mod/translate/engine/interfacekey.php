<?php

/*
 * Represents one of Envaya's localized language strings. Each key name is 
 * a string used in the __() function, e.g. 'discussions:title'.
 */
class InterfaceKey extends TranslationKey
{
    static $query_subtype_ids = array('translate.interface.key');

    function update($recursive = false)
    {
        parent::update();
        
        if ($recursive)
        {
            $this->get_container_entity()->update();
        }
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
                $translation = $this->new_translation();
                $translation->value = $defined_value;
                $translation->set_approved(true);
                $translation->save();
                $this->update($update_recursive);
            }
        }
    }
        
    function get_defined_language()
    {
        return $this->get_container_entity()->get_defined_language();
    }    
    
    function get_url()
    {
        return $this->get_container_entity()->get_url()."/".urlencode_alpha($this->name);
    }
    
    function get_default_value()
    {
        return $this->get_value_in_lang(Config::get('language'));
    }
    
    function get_default_value_lang()
    {
        return Config::get('language');
    }
    
    function get_placeholders()
    {
        return Language::get_placeholders($this->get_default_value());
    }
    
    function get_value_in_lang($lang)
    {
        return @__($this->name, $lang);
    }    
    
    function sanitize_value($value)
    {        
        // don't allow people to sneak in bad HTML into translations
        $value = Markup::sanitize_html($value, array(
            'AutoFormat.Linkify' => false,
            'HTML.AllowedElements' => 'em,strong,br'
        ));                
        
        $placeholders = Language::get_placeholders($value);
        $correct_placeholders = $this->get_placeholders();
        sort($correct_placeholders);
        sort($placeholders);
        if ($correct_placeholders != $placeholders)
        {
            throw new ValidationException(__('itrans:placeholder_error'));
        }
        
        return $value;
    }

    function get_current_base_value()
    {
        $base_lang = $this->get_current_base_lang();
        return $this->get_value_in_lang($base_lang);
    }        
}