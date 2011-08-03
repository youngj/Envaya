<?php

class EntityTranslationKey extends TranslationKey
{    
    static $query_subtype_ids = array('translate.entity.key');

    function get_url()
    {
        return $this->get_language()->get_url()."/content/".urlencode_alpha($this->name);
    }
        
    protected function get_entity_property()
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
        if ($entity_prop)
        {
            $prop = $entity_prop[1];
            return $entity_prop[0]->$prop;
        }
        else
        {
            return null;
        }
        
    }
    
    private function call_entity_method($format, $args)
    {
        $entity_prop = $this->get_entity_property();
        if (!$entity_prop)
        {
            throw new CallException("get_entity_property");
        }
        
        $entity = $entity_prop[0];
        $property = $entity_prop[1];
        
        $method = sprintf($format, $property);
        return call_user_func_array(array($entity,$method), $args);
    }
    
    function view_value($value, $snippet_len = null)
    {
        try
        {
            return $this->call_entity_method("view_%s_value", array($value, $snippet_len));
        }
        catch (CallException $ex)
        {
            return parent::view_value($value, $snippet_len);
        }            
    }
    
    function view_input($initial_value)
    {
        try
        {
            return $this->call_entity_method("view_%s_input", array($initial_value));
        }
        catch (CallException $ex)
        {
            return parent::view_input($initial_value);
        }            
    }    
    
    function sanitize_value($value)
    {
        try
        {
            return $this->call_entity_method("sanitize_%s_value", array($value));
        }
        catch (CallException $ex)
        {
            return Markup::sanitize_html($value, array(
                'AutoFormat.Linkify' => false,
                'HTML.AllowedElements' => ''
            ));
        }
    }
    
    function get_current_base_lang()
    {
        return $this->get_default_value_lang();
    }
    
    function get_default_value_lang()
    {
        $entity = $this->get_container_entity();
        return $entity->get_language();        
    }
}