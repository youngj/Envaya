<?php

/**
 * view - Renders a view (a normal PHP file that outputs content), captures the output content
 * and returns it as a string. An associative array of parameters is passed into the view
 * with the variable name $vars.
 *
 * View files are allowed to call methods defined in the engine/ directory to query the database
 * and access properties of models. They can also render other views.
 * 
 * Views are not allowed to use controllers or actions, perform access control, throw exceptions,
 * or end/forward the request, and are generally not allowed to modify state, 
 * but they are allowed to call PageContext methods to modify state for the current request.
 * 
 * view() adds a special variable $INCLUDE_COUNT, which increments each time a given view is rendered.
 *   $INCLUDE_COUNT is 0 the first time a view is rendered for a given script execution.
 *   This allows views to do one-time setup or generate unique DOM ids.
 *
 * @param string $view The name and location of the view to use
 * @param array $vars Any variables that the view requires, passed as an array
 * @return string The HTML content
 */
function view($view, $vars = null, $viewtype = null)
{
    if ($vars === null)
    {
        $vars = array();
    }

    if (!$viewtype)
    {
        $viewtype = Views::$current_type ?: Views::$request_type;
    }
           
    ob_start();
    
    if (!isset(Engine::$view_patch[$view]))
    {
        include_view($view, $viewtype, $vars);
    }          
    else
    {
        $views = array($view);
        $patch_fn = 'patch_view_'.str_replace('/', '_', $view);

        foreach (Engine::$view_patch[$view] as $module_class)
        {
            $module_class::$patch_fn($views);
        }

        foreach ($views as $extension_view)
        {
            include_view($extension_view, $viewtype, $vars);
        }
    }
    
    return ob_get_clean();
}

function include_view($view, $viewtype, $vars)
{
    static $INCLUDE_COUNTS = array();

    $view_path = Views::get_path($view, $viewtype);
    if ($view_path == null)
    {
        ob_get_clean(); // hack so that exceptions can be caught with correct output buffer nesting level
        throw new InvalidParameterException("view $view does not exist");
    }
        
    if (!isset($INCLUDE_COUNTS[$view_path]))
    {
        $INCLUDE_COUNTS[$view_path] = 0;
    }
    
    $INCLUDE_COUNT = $INCLUDE_COUNTS[$view_path];
    $INCLUDE_COUNTS[$view_path] = $INCLUDE_COUNT + 1;
    
    include_view_file($view_path, $vars, $INCLUDE_COUNT);
}

function include_view_file($VIEW_PATH, $vars, $INCLUDE_COUNT)
{
    return include $VIEW_PATH;
}

/**
 * Returns whether the specified view exists
 *
 * @param string $view The view name
 * @param string $viewtype If set, forces the viewtype
 * @return true|false Depending on success
 */
function view_exists($view, $viewtype = null, $fallback = true)
{
    return Views::get_path($view, $viewtype, $fallback) != null; 
}

function render_custom_view($view, $vars, $template_vars=null)
{
    $template = @$vars['design']['custom_views'][$view] ?: view("templates/$view", null, 'custom');
    
    $replacements = array();
    
    if ($template_vars)
    {
        foreach ($template_vars as $template_var)
        {
            $replacements["{{".$template_var."}}"] = $vars[$template_var];
        }   
    }
    return strtr($template, $replacements);    
}

class Views
{
    static $request_type = 'default';
    static $current_type = null;
    private static $browsable_types = array('mobile','default');
       
    static function get_request_type()
    {
        return static::$request_type;
    }
    
    static function set_request_type($type)
    {
        static::$request_type = $type;
    }
    
    static function get_current_type()
    {
        return static::$current_type ?: static::get_request_type();
    }
    
    static function set_current_type($type)
    {
        static::$current_type = $type;
    }
    
    static function is_browsable_type($type)
    {
        return in_array($type, static::$browsable_types);
    }
        
    static function get_path($view, $viewtype = null, $fallback = true)
    {
        if (!$viewtype)
        {
            $viewtype = Views::get_current_type();
        }

        $viewPath = null;
        
        if ($viewtype != 'default')
        {
            $viewPath = Engine::get_real_path("views/{$viewtype}/{$view}.php");
            if (!$viewPath && !$fallback)
            {
                return null;
            }
        }
        
        if (!$viewPath)
        {
            $viewPath = Engine::get_real_path("views/default/{$view}.php");
        }
        
        return $viewPath;
    }      
}
