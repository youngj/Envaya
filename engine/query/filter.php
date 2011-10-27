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

    static function get_class_name($subclass)
    {
        return "Query_Filter_{$subclass}";
    }
    
    static function get_param_name()
    {
        return strtolower(static::get_subclass());
    }
    
    static function get_subclass()
    {
        return substr(get_called_class(), strlen("Query_Filter_"));
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
        foreach ($subclasses as $subclass)
        {
            $cls = static::get_class_name($subclass);
            $res[] = $cls::new_from_input();
        }
        return $res;
    }

    static function json_encode_filters($filters)
    {
        $filters_args = array();
        foreach ($filters as $filter)
        {
            $filters_args[] = array(
                'subclass' => $filter->get_subclass(),
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
                $subclass = $filter_args['subclass'];
                $args = $filter_args['args'];
                
                $filter_class = static::get_class_name($subclass);
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