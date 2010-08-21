<?php

class Model
{       
    static $primary_key = 'id';
    static $table_name;
    static $table_attributes;
    
    protected $attributes = array();
    
    public $dirty = false;

    function __construct($row = null)
    {
        $this->initialize_attributes();
        if ($row)
        {
            $this->init_from_row($row);        
        }
    }
    
    function get($name)
    {
        return @$this->attributes[$name];
    }

    function set($name, $value)
    {
        $this->attributes[$name] = $value;
        $this->dirty = true;
    }
    
    function __get($name) {
        return $this->get($name);
    }

    function __set($name, $value) {
        $this->set($name, $value);
    }    
    
    protected function initialize_attributes()
    {
        $this->attributes[static::$primary_key] = 0;
        
        foreach (static::$table_attributes as $name => $default)
        {
            $this->attributes[$name] = $default;
        }
    }
        
    static function query()
    {       
        $query = new Query_Select(static::$table_name);
        $query->set_row_function(array(get_called_class(), '_new'));
        return $query;
    }    
    
    static function _new($row)
    {
        $cls = get_called_class();
        return new $cls($row);
    }

    protected function init_from_row($row)
    {
        foreach ((array)$row as $k => $v)
        {
            $this->attributes[$k] = $v;
        }
    }
    
    public function save()
    {
        $attributes = array();
        
        foreach (static::$table_attributes as $k => $v)
        {
            $attributes[$k] = $this->attributes[$k];
        }
    
        save_db_row(static::$table_name, static::$primary_key, $this->attributes[static::$primary_key], $attributes);
        $this->dirty = false;
    }    
    
    public function delete()
    {
        $this->dirty = false;
        return delete_data("DELETE from ".static::$table_name." where id=?", array($this->attributes[static::$primary_key]));
    }
}