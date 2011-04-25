<?php

/*
 * Base class of data objects that correspond to a row in a database table.
 * Defines save() and delete() methods which perform insert/update/delete SQL commands
 *
 * Also has a 'query' interface for selecting objects from a database, e.g.:
 *  ModelSubclass::query()->where('foo > ?', 10)->filter();
 */
class Model extends Mixable
{       
    // subclasses should override these static properties
    static $primary_key = 'id';
    static $table_name;
    static $table_attributes = array();
    
    /**
     * The main attributes of a model.
     */    
    protected $attributes = array();
    
    protected $attribute_defaults;
       
    public $dirty = false;

    function __construct($row = null)
    {
        if ($row)
        {
            $this->init_from_row($row);        
        }
        else
        {
            $this->attributes = static::get_table_attributes();
            $this->attributes[static::$primary_key] = 0;
        }
    }
    
    static function get_table_attributes()
    {
        $attributes = static::$table_attributes;
        
        foreach (static::get_mixin_classes() as $mixin_class)
        {
            if (method_exists($mixin_class, 'mixin_table_attributes'))
            {
                $attributes = array_merge($attributes, $mixin_class::mixin_table_attributes());
            }
        }
        
        return $attributes;
    }    
    
    public function serialize()
    {
        return serialize($this->attributes);
    }

    public function unserialize($data)
    {
        $this->attributes = unserialize($data);
    }
        
    function __get($name)
    {
        $val = @$this->attributes[$name];    
        if ($val !== null || array_key_exists($name, $this->attributes))
        {
            return $val;
        }
        
        /* 
         * Store default values of attributes separately (so that
         * when save() is called we only save the attributes that are
         * already loaded from the database).
         */
        if (!isset($this->attribute_defaults))
        {
            $this->attribute_defaults = static::get_table_attributes();
        }        
        
        /* 
         * lazy-load attribute from database if it is defined as an attribute
         * and not loaded already (e.g. if loaded by a query with a subset of columns)
         */
        if (array_key_exists($name, $this->attribute_defaults))
        {
            $pk = static::$primary_key;    
            $pk_val = $this->$pk;
            
            if ($pk_val)
            {
                $table = static::$table_name;
                $row = Database::get_row("select `$name` as val from `$table` where `$pk` = ?", array($pk_val));
                if ($row)
                {
                    $val = $row->val;
                    $this->attributes[$name] = $val;
                    return $val;
                }
            }
        }
        
        return @$this->attribute_defaults[$name];
    }

    function __set($name, $value)
    {
        $this->attributes[$name] = $value;
        $this->dirty = true;
    }    
    
    static function query()
    {       
        return new Query_Select(static::$table_name, get_called_class());
    }    
    
    static function new_from_row($row)
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
    
    protected function get_table_attribute_values()
    {
        $tableAttributes = array();
        foreach (static::get_table_attributes() as $name => $default)
        {
            if (array_key_exists($name, $this->attributes))
            {            
                $tableAttributes[$name] = $this->attributes[$name];
            }
        }
        return $tableAttributes;
    }
    
    public function save()
    {
        $values = $this->get_table_attribute_values();
        $pk = static::$primary_key;    
        Database::save_row(static::$table_name, $pk, 
            /* reference */ $this->attributes[$pk], $values);
        $this->dirty = false;
    }    
    
    public function delete()
    {
        $this->dirty = false;
        $table = static::$table_name;
        $pk = static::$primary_key;    
        return Database::delete("DELETE from `{$table}` WHERE `{$pk}`=?", 
            array($this->$pk)
        );
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