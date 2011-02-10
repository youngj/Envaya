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
 * Handles templating views
 *
 * @see set_template_handler
 *
 * @param string $view The name and location of the view to use
 * @param array $vars Any variables that the view requires, passed as an array
 * @return string The HTML content
 */
function view($view, $vars = null, $viewtype = null)
{
    global $CONFIG;

    // basic checking for bad paths
    if (strpos($view, '..') !== false)
    {
        return false;
    }

    if (empty($vars))
    {
        $vars = array();
    }

    $vars['user'] = Session::get_loggedin_user();
    $vars['config'] = $CONFIG;
    $vars['url'] = $CONFIG->url;    
    
    if (!$viewtype)
    {
        $viewtype = get_viewtype();
    }
       
    $viewPath = get_view_path($view, $viewtype);

    ob_start();

    if (include_view($viewPath, $vars))
    {
        // success
    }
    else if (@$CONFIG->debug)
    {
        error_log(" [This view ({$view}) could not be included] ");
    }

    return ob_get_clean();
}

function get_view_path($view, $viewtype = '', $fallback = true)
{
    if (empty($viewtype))
        $viewtype = get_viewtype();

    $viewDir = dirname(dirname(__DIR__)) . "/views/";
    $exists = false;
    
    if ($viewtype)
    {
        $viewPath  = $viewDir . "{$viewtype}/{$view}.php";
        $exists = file_exists($viewPath);
        if (!$exists && !$fallback)
        {
            return null;
        }
    }
    
    if (!$exists)
    {
        $viewPath = $viewDir . "default/{$view}.php";
        $exists = file_exists($viewPath);
    }
    
    return ($exists) ? $viewPath : null;
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
 * @param string $submenu Should a submenu be displayed? (default false, use not recommended)
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
