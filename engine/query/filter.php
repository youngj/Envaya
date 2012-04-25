<?php

abstract class Query_Filter
{
    public $value;

    function __construct($args = null)
    {
        $this->args = $args;
        if ($args)
        {
            foreach ($args as $name => $value)
            {
                $this->$name = $value;
            }
        }
    }    
    
    function is_empty_value()
    {
        return $this->value === '' || $this->value === null;
    }
    
    static function get_all_text()
    {
        return 'All';
    }    
    
    abstract function render_view();
    abstract function render_input($vars);
    
    static function get_param_name()
    {
        $cls = get_called_class();
        $parts = explode('_', $cls);
        return strtolower($parts[sizeof($parts) - 1]);
    }
    
    static function new_from_input()
    {
        $cls = get_called_class();
        $param = $cls::get_param_name();
        return new $cls(array('value' => get_input($param)));
    }
    
    static function filters_from_input($subclasses)
    {
        $res = array();
        foreach ($subclasses as $cls)
        {
            $res[] = $cls::new_from_input();
        }
        return $res;
    }
    
    static function get_subtype_id()
    {
        return ClassRegistry::get_subtype_id(get_called_class());
    }
    
    static function json_encode_filters($filters)
    {
        $filters_args = array();
        foreach ($filters as $filter)
        {
            $subtype_id = $filter->get_subtype_id();            
            if (!$subtype_id)
            {
                throw new InvalidParameterException(get_class($filter) . " is not in class registry");
            }
        
            $filters_args[] = array(
                'subtype_id' => $subtype_id,
                'args' => $filter->args
            );
        }
        return json_encode($filters_args);        
    }
    
    static function json_decode_filters($filters_json)
    {
        $filters = array();
        $filters_args = json_decode($filters_json, true);
        if ($filters_args)
        {
            foreach ($filters_args as $filter_args)
            {
                $subtype_id = $filter_args['subtype_id'];
                $args = $filter_args['args'];
                
                $filter_class = ClassRegistry::get_class($subtype_id);
                
                if (!$filter_class)
                {
                    throw new ValidationException("Invalid filter type: $subtype_id");
                }
                
                $filters[] = new $filter_class($args);                    
            }
        }
        return $filters;    
    }        
    
    static function get_name() { return "Filter"; }
        
    function apply($query)
    {
        if (!$this->is_empty_value())
        {
            return $this->_apply($query);
        }
        else
        {
            return $query;
        }
    }
        
    abstract function _apply($query);

    function is_valid()
    {
        return true;
    }    
}