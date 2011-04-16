<?php

/*
 * Forwards inaccessible properties and methods to an object instance.
 * 
 * This allows methods in Mixin subclasses to be written as if they were defined directly in the
 * the (Mixable) class that the Mixin is mixed into. In other words, methods can be refactored 
 * into Mixins without needing to rewrite them.
 *
 * However any undefined functions called via static:: in Mixins is not forwarded to the Mixable class.
 */
abstract class Mixin
{
    protected $instance;

    function __construct($instance)
    {
        $this->instance = $instance;
    }
    
    function __get($name)
    {
        return $this->instance->$name;
    }
    
    function __set($name, $value)
    {
        $this->instance->$name = $value;
    }
    
    function __call($fn, $args)
    {
        return call_user_func_array(array($this->instance, $fn), $args);
    }
    
    // __callStatic not defined because Mixin doesn't know what class to call
}