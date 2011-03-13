<?php

/*
 * Base class of data objects that correspond to a row in a database table.
 * Defines save() and delete() methods which perform insert/update/delete SQL commands
 *
 * Also has a 'query' interface for selecting objects from a database, e.g.:
 *  ModelSubclass::query()->where('foo > ?', 10)->filter();
 */
class Model
{       
    // subclasses should override these static properties
    static $primary_key = 'id';
    static $table_name;
    static $table_attributes;
    
    /**
     * The main attributes of a model.
     * Blank entries for all database fields should be created by the constructor.     
     * For Entity subclasses, any field not appearing in this will be viewed as metadata
     */    
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

    public function serialize()
    {
        return serialize($this->attributes);
    }

    public function unserialize($data)
    {
        $this->initialize_attributes();
        $this->attributes = unserialize($data);
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
    
    protected function get_table_attributes()
    {
        $tableAttributes = array();
        foreach (static::$table_attributes as $name => $default)
        {
            $tableAttributes[$name] = $this->attributes[$name];
        }
        return $tableAttributes;
    }
    
    public function save()
    {
        $attributes = $this->get_table_attributes();
    
        Database::save_row(static::$table_name, static::$primary_key, $this->attributes[static::$primary_key], $attributes);
        $this->dirty = false;
    }    
    
    public function delete()
    {
        $this->dirty = false;
        return Database::delete("DELETE from ".static::$table_name." where ".static::$primary_key."=?", array($this->attributes[static::$primary_key]));
    }
    
    function get_default_view_name()
    {
        $view_name = "object/".strtolower(get_class($this));
        
        if (view_exists($view_name)) 
        {
            return $view_name;
        }
        else
        {
            return 'object/default';
        }
    }    
}