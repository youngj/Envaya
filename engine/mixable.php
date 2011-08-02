<?php

/*
 * Mixable is the base class for any class that can be extended with mixins
 * (subclasses of Mixin). 
 *
 * Mixins allow reusing functionality across multiple classes without needing to
 * define the functionality in a common base class (i.e. inheritance) or in 
 * a separate object (i.e. composition).  
 *
 * Mixins can also be added to a Mixable class at runtime, which allows modules 
 * (in mod/.../start.php) to extend the functionality of core classes in engine/
 *
 */
abstract class Mixable
{
    static $mixin_classes = array(); /* array of Mixin class names; subclasses can override */
    
    private static $_mixin_classes_map = array();
    private static $mixin_classes_map = array();

    private static function _get_mixin_classes($cls)
    {
        if (isset(Mixable::$_mixin_classes_map[$cls]))
        {
            return Mixable::$_mixin_classes_map[$cls];
        }
        else
        {
            $mixin_classes = static::$mixin_classes;        
            
            $parent = get_parent_class($cls);
            $parent_classes = $parent ? $parent::$mixin_classes : null;
            
            if ($parent_classes === $mixin_classes)
            {            
                $mixin_classes = array();
            }            
            Mixable::$_mixin_classes_map[$cls] = $mixin_classes;
            return $mixin_classes;
        }        
    }

    static function get_mixin_classes()
    {
        $cls = get_called_class();
        
        if (isset(Mixable::$mixin_classes_map[$cls]))
        {
            return Mixable::$mixin_classes_map[$cls];
        }
        else
        {    
            $mixin_classes = static::_get_mixin_classes($cls);
            
            $parent = get_parent_class($cls);
            if ($parent != 'Mixable')
            {
                $parent_mixin_classes = $parent::get_mixin_classes();
                if ($parent_mixin_classes)
                {
                    $mixin_classes = array_merge($mixin_classes, $parent_mixin_classes);
                }
            }        
            Mixable::$mixin_classes_map[$cls] = $mixin_classes;
            return $mixin_classes;
        }
    }
    
    function __call($fn, $args)
    {    
        foreach (static::get_mixin_classes() as $mixin_class)
        {        
            $mixin = $this->get_mixin($mixin_class);
            
            // avoid infinite recursion, since Mixin calls this if $fn doesn't exist
            if (method_exists($mixin, $fn)) 
            {
                return call_user_func_array(array($mixin, $fn), $args);
            }
        }
        $cls = get_class($this);
        throw new CallException("method $fn does not exist in $cls");
    }
    
    static function __callStatic($fn, $args)
    {
        foreach (static::get_mixin_classes() as $mixin_class)
        {        
            if (method_exists($mixin_class, $fn)) 
            {
                return call_user_func_array(array($mixin_class, $fn), $args);
            }
        }
        $cls = get_called_class();
        throw new CallException("method $fn does not exist in $cls");        
    }

    static function add_mixin_class($mixin_class)
    {
        $cls = get_called_class();
        $mixin_classes = static::_get_mixin_classes($cls);
        $mixin_classes[] = $mixin_class;
        
        // don't modify static::$mixin_classes directly because
        // it may actually modify a base class
        Mixable::$_mixin_classes_map[$cls] = $mixin_classes;
    }
    
    protected $mixins = array();
    protected function get_mixin($mixin_class)
    {
        $mixin = @$this->mixins[$mixin_class];
        if (!$mixin)
        {
            $mixin = new $mixin_class($this);
            $this->mixins[$mixin_class] = $mixin;
        }
        return $mixin;
    }
}