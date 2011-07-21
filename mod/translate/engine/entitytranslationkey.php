<?php

class EntityTranslationKey extends TranslationKey
{    
    function get_url()
    {
        return $this->get_language()->get_url()."/entity/".urlencode_alpha($this->name);
    }
    
    function get_entity_property()
    {
        $name_parts = explode(':', $this->name);
        
        $entity = Entity::get_by_guid($name_parts[1]);
        if ($entity)
        {
            return array($entity, $name_parts[2]);
        }
        else
        {
            return null;
        }
    }
    
    function get_default_value()
    {
        $entity_prop = $this->get_entity_property();
        $prop = $entity_prop[1];
        return $entity_prop[0]->$prop;
    }
    
    function get_value_in_lang($lang)
    {
        $entity_prop = $this->get_entity_property();
    
        if ($entity_prop)
        {
            return $entity_prop[0]->translate_field($entity_prop[1], $lang);
        }
        return null;
    }
    
    function view_input($initial_value)
    {
        $entity_prop = $this->get_entity_property();
        
        $entity = $entity_prop[0];
        $property = $entity_prop[1];
        
        $view_method = "view_{$property}_input";
        
        try
        {
            return $entity->$view_method($initial_value);
        }
        catch (CallException $ex)
        {
            return parent::view_input($initial_value);
        }            
    }
    
    function view_value($value)
    {
        $entity_prop = $this->get_entity_property();
        
        $entity = $entity_prop[0];
        $property = $entity_prop[1];
        
        $view_method = "view_{$property}_value";
        
        try
        {
            return $entity->$view_method($value);
        }
        catch (CallException $ex)
        {
            return parent::view_value($value);
        }            
    }
    
    function sanitize_value($value)
    {
        $entity_prop = $this->get_entity_property();
        
        $entity = $entity_prop[0];
        $property = $entity_prop[1];    
    
        $sanitize_method = "sanitize_{$property}_value";
    
        try
        {
            return $entity->$sanitize_method($value);
        }
        catch (CallException $ex)
        {
            return Markup::sanitize_html($value, array(
                'AutoFormat.Linkify' => false,
                'HTML.AllowedElements' => ''
            ));
        }
    }
}