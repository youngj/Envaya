<?php

/*
 * Base class of data objects that correspond to a row in a database table.
 * Defines save() and delete() methods which perform insert/update/delete SQL commands
 *
 * If a model subclass defines a subtype_id attribute, it will be interpreted
 * as the subtype_id of the PHP class to instantiate when initializing the model from
 * a database row (see ClassRegistry).
 *
 * Also has a 'query' interface for selecting objects from a database, e.g.:
 *  ModelSubclass::query()->where('foo > ?', 10)->filter();
 */
abstract class Model extends Mixable
{       
    // subclasses should override these static properties
    static $table_name;
    static $table_attributes = array();
    
    // subclasses may override these static properties
    static $primary_key = 'id';
    static $query_class = 'Query_Select';
    static $table_base_class = null; 
    static $query_subtypes = null;    
    
    /**
     * The main attributes of a model.
     */    
    protected $attributes = array();
    
    protected $attribute_defaults;
       
    protected $dirty_attributes = null;

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
            if (property_exists($mixin_class, 'table_attributes'))
            {
                $attributes = array_merge($attributes, $mixin_class::$table_attributes);
            }
        }
        
        if (isset($attributes['subtype_id']))
        {
            $attributes['subtype_id'] = static::get_subtype_id();
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
        if (isset($this->attributes[$name]))
        {
            return $this->attributes[$name];
        }
        else if (array_key_exists($name, $this->attributes))
        {
            return null;
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
        $this->dirty_attributes[$name] = true;
    }    
    
    static function query()
    {
        $query_class = static::$query_class;
        
        $cls = get_called_class();    
        $query = new $query_class(static::$table_name, $cls);        
        
        /*
         * If a base class shares a table with derived classes,
         * BaseClass::query() should query all entities of base and derived classes,
         * while DerivedClass1::query() should return only entities of a particular
         * derived class.
         *
         * To enable this behavior, set static::$table_base_class in the base class
         * to the name of the base class (e.g. static $table_base_class = 'BaseClass';).
         *
         * For situations where there are >= 3 levels of inheritance represented in one table,
         * set static::$query_subtypes to an array containing all the class names of subclasses.
         */
        $table_base_class = static::$table_base_class;         
        if ($table_base_class && $table_base_class != $cls)
        {            
            $subtype_ids = array($cls::get_subtype_id());            
            if (static::$query_subtypes)
            {
                foreach (static::$query_subtypes as $subtype)
                {
                    $subtype_ids[] = $subtype::get_subtype_id();
                }
            }        
            $query->subtype_ids($subtype_ids);
        }
        
        return $query;
    }
        
    static function new_from_row($row)
    {
        if (isset(static::$table_attributes['subtype_id']))
        {    
            $cls = ClassRegistry::get_class($row->subtype_id);
            if (!$cls)
            {   
                throw new InvalidParameterException("Model subtype {$row->subtype_id} is not defined");
            }
        }
        else
        {
            $cls = get_called_class();
        }
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
            if (isset($this->attributes[$name]) || array_key_exists($name, $this->attributes))
            {            
                $tableAttributes[$name] = $this->attributes[$name];
            }
        }
        return $tableAttributes;
    }
    
    protected function get_dirty_attribute_values()
    {
        $dirtyAttributes = array();        
        foreach (static::get_table_attributes() as $name => $default)
        {
            if (isset($this->dirty_attributes[$name]))
            {            
                $dirtyAttributes[$name] = $this->attributes[$name];
            }
        }
        return $dirtyAttributes;
    }    
    
    public function save()
    {
        $pk = static::$primary_key;
        
        if ($this->$pk)
        {
            $values = $this->get_dirty_attribute_values();            
        }
        else
        {
            $values = $this->get_table_attribute_values();
        }
        
        Database::save_row(static::$table_name, $pk, 
            /* reference */ $this->attributes[$pk], $values);    
        
        $this->dirty_attributes = null;
    }    
    
    function is_dirty()
    {
        return isset($this->dirty_attributes);
    }
        
    public function delete()
    {
        $this->dirty_attributes = null;
        $table = static::$table_name;
        $pk = static::$primary_key;    
        return Database::delete("DELETE from `{$table}` WHERE `{$pk}`=?", 
            array($this->$pk)
        );
    }    
    
    function get_primary_key()
    {
        $pk = static::$primary_key;    
        return $this->attributes[$pk];
    }
    
    static function get_subtype_id()
    {
        return ClassRegistry::get_subtype_id(get_called_class());
    }
}