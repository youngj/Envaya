<?php
        
/**
 * Sets the functional context of a page
 *
 * @param string $context The context of the page
 * @return string|false Either the context string, or false on failure
 */
function set_context($context) 
{    
    global $CONFIG;
    if (!empty($context)) 
    {
        $context = trim($context);
        $context = strtolower($context);
        $CONFIG->context = $context;
        return $context;
    } 
    else 
    {
        return false;
    }
}
        
/**
 * Returns the functional context of a page
 *
 * @return string The context, or 'main' if no context has been provided
 */
function get_context() {
    
    global $CONFIG;
    if (isset($CONFIG->context) && !empty($CONFIG->context)) {
        return $CONFIG->context;
    }
    return "main";
    
}
