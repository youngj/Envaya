<?php

function get_viewtype()
{
    static $VIEWTYPE;
    
    if (!isset($VIEWTYPE))
    {
        $VIEWTYPE = get_input('view') ?: @$_COOKIE['view'] ?: '';        
        
        if (!$VIEWTYPE && is_mobile_browser())
        {
            $VIEWTYPE = 'mobile';
        }
        
        if (preg_match('/[^\w]/', $VIEWTYPE))
        {            
            $VIEWTYPE = '';
        }
    }
    return $VIEWTYPE;
}

/**
 * Handles templating views.
 * 
 * Adds special variable 'include_count', which increments each time a given view is rendered.
 *   include_count is 0 the first time a view is rendered for a given script execution.
 *    This allows views to do one-time setup or generate unique DOM ids.
 *
 * @param string $view The name and location of the view to use
 * @param array $vars Any variables that the view requires, passed as an array
 * @return string The HTML content
 */
function view($view, $vars = null, $viewtype = null)
{
    // basic checking for bad paths
    if (strpos($view, '..') !== false)
    {
        return false;
    }

    if (empty($vars))
    {
        $vars = array();
    }

    if (!$viewtype)
    {
        $viewtype = get_viewtype();
    }
           
    ob_start();

    include_view($view, $viewtype, $vars);
    foreach (Views::get_extensions($view) as $extension_view)
    {
        include_view($extension_view, $viewtype, $vars);
    }    
    
    return ob_get_clean();
}

function get_view_path($view, $viewtype = '', $fallback = true)
{
    if (empty($viewtype))
        $viewtype = get_viewtype();

    $viewPath = null;
    
    if ($viewtype)
    {
        $viewPath = get_real_path("views/{$viewtype}/{$view}.php");
        if (!$viewPath && !$fallback)
        {
            return null;
        }
    }
    
    if (!$viewPath)
    {
        $viewPath = get_real_path("views/default/{$view}.php");
    }
    
    return $viewPath;
}

function include_view($view, $viewtype, $vars)
{
    static $INCLUDE_COUNTS = array();

    $viewPath = get_view_path($view, $viewtype);
        
    if (!isset($INCLUDE_COUNTS[$viewPath]))
    {
        $INCLUDE_COUNTS[$viewPath] = 0;
    }
    $include_count = $INCLUDE_COUNTS[$viewPath];
    $INCLUDE_COUNTS[$viewPath] = $include_count + 1;
    
    $vars['include_count'] = $include_count;       

    if (include_view_file($viewPath, $vars))
    {
        // success
    }
    else if (Config::get('debug'))
    {
        error_log(" [This view ({$view}) could not be included] ");
    }
}

function include_view_file($viewFile, $vars)
{
    return include $viewFile;
}
/**
 * Returns whether the specified view exists
 *
 * @param string $view The view name
 * @param string $viewtype If set, forces the viewtype
 * @return true|false Depending on success
 */
function view_exists($view, $viewtype = '', $fallback = true)
{
    return get_view_path($view, $viewtype, $fallback) != null; 
}

/**
 * @param Entity $entity The entity to display

 * @return string HTML (etc) to display
 */
function view_entity($entity, $args = null) 
{
    if (!$args)
    {
        $args = array();
    }
    $args['entity'] = $entity;
    
    return $entity 
        ? view($entity->get_default_view_name(), $args)
        : '';
}

class Views
{
    static $extensions_map = array();
    static function extend($base_view, $extend_view, $priority = 1)
    {
        $extensions = @static::$extensions_map[$base_view];    
        if (!$extensions)
        {
            $extensions = array();
            static::$extensions_map[$base_view] =& $extensions;
        }        
        while (isset($extensions[$priority])) 
        {
            $priority++;
        }        
        
        $extensions[$priority] = $extend_view;
    }
    
    static function get_extensions($base_view)
    {
        $extensions = @static::$extensions_map[$base_view];
        if ($extensions)
        {
            ksort($extensions);
            return array_values($extensions);
        }
        else
        {
            return array();
        }
    }
}