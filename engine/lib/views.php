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
    static $INCLUDE_COUNTS = array();

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
       
    $viewPath = get_view_path($view, $viewtype);
        
    if (!isset($INCLUDE_COUNTS[$viewPath]))
    {
        $INCLUDE_COUNTS[$viewPath] = 0;
    }
    $include_count = $INCLUDE_COUNTS[$viewPath];
    $INCLUDE_COUNTS[$viewPath] = $include_count + 1;
    
    $vars['include_count'] = $include_count;       

    ob_start();

    if (include_view($viewPath, $vars))
    {
        // success
    }
    else if (Config::get('debug'))
    {
        error_log(" [This view ({$view}) could not be included] ");
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

function include_view($viewFile, $vars)
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

/**
 * Displays an internal layout for the use of a plugin canvas.
 * Takes a variable number of parameters, which are made available
 * in the views as $vars['area1'] .. $vars['areaN'].
 *
 * @param string $layout The name of the views in canvas/layouts/.
 * @return string The layout
 */
function view_layout($layout) 
{
    $arg = 1;
    $param_array = array();
    while ($arg < func_num_args()) {
        $param_array['area' . $arg] = func_get_arg($arg);
        $arg++;
    }
    if (view_exists("canvas/default", false)) 
    {
        return view("canvas/default",$param_array);               
    } 
    else 
    {
        return view("canvas/layouts/{$layout}", $param_array);
    }

}

/**
 * Returns a view for the page title
 *
 * @param string $title The page title
 * @return string The HTML (etc)
 */
function view_title($title, $args = null)
{
    return view('page_elements/title', array('title' => $title, 'args' => $args));
}        

/**
 * Returns a representation of a full 'page' (which might be an HTML page, RSS file, etc, depending on the current view)
 *
 * @param unknown_type $title
 * @param unknown_type $body
 * @return unknown
 */
function page_draw($title, $body, $vars = null)
{
    if ($vars == null)
    {
        $vars = array();
    }
    $vars['title'] = $title;
    $vars['body'] = $body;

    return view('pageshells/pageshell', $vars);
}
