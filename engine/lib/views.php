<?php

/**
 * Templating and visual functionality
 */

$CURRENT_SYSTEM_VIEWTYPE = "";

/**
 * Override the view mode detection for the elgg view system.
 *
 * This function will force any further views to be rendered using $viewtype. Remember to call elgg_set_viewtype() with
 * no parameters to reset.
 *
 * @param string $viewtype The view type, e.g. 'rss', or 'default'.
 * @return bool
 */
function elgg_set_viewtype($viewtype = "")
{
    global $CURRENT_SYSTEM_VIEWTYPE;

    $CURRENT_SYSTEM_VIEWTYPE = $viewtype;

    return true;
}

/**
 * Return the current view type used by the elgg view system.
 *
 * By default, this function will return a value based on the default for your system or from the command line
 * view parameter. However, you may force a given view type by calling elgg_set_viewtype()
 *
 * @return string The view.
 */
function elgg_get_viewtype()
{
    global $CURRENT_SYSTEM_VIEWTYPE;

    return $CURRENT_SYSTEM_VIEWTYPE ?: get_input('view') ?: 'default';
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
function view($view, $vars = null)
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
    $viewtype = elgg_get_viewtype();
    $viewDir = dirname(dirname(__DIR__)) . "/views/";
    $viewFile = $viewDir . "{$viewtype}/{$view}.php";

    $exists = file_exists($viewFile);

    ob_start();

    if ($exists && include_view($viewFile, $vars))
    {
        // success
    }
    else if (@$CONFIG->debug)
    {
        error_log(" [This view ({$view}) could not be included] ");
    }

    return ob_get_clean();
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
function view_exists($view, $viewtype = '')
{
    if (empty($viewtype))
        $viewtype = elgg_get_viewtype();

    return file_exists(dirname(dirname(__DIR__)) . "/views/{$viewtype}/{$view}.php");

}

/**
 * When given an entity, views it intelligently.
 *
 * Expects a view to exist called entity-type/subtype, or for the entity to have a parameter
 * 'view' which lists a different view to display.  In both cases, view will be called with
 * array('entity' => $entity) as its parameters, and therefore this is what the view should expect
 * to receive.
 *
 * @param ElggEntity $entity The entity to display
 * @param boolean $full Determines whether or not to display the full version of an object, or a smaller version for use in aggregators etc
 * @return string HTML (etc) to display
 */
function view_entity($entity, $full = false) 
{
    // No point continuing if entity is null.
    if (!$entity) return '';

    $classes = array(
        'ElggUser' => 'user',
        'ElggObject' => 'object',
    );

    $entity_class = get_class($entity);

    if (isset($classes[$entity_class])) 
    {
        $entity_type = $classes[$entity_class];
    } 
    else 
    {
        foreach($classes as $class => $type) 
        {
            if ($entity instanceof $class) {
                $entity_type = $type;
                break;
            }
        }
    }
    if (!isset($entity_class)) 
        return false;

    $subtype = $entity->getSubtypeName();
    if (empty($subtype)) 
    { 
        $subtype = $entity_type; 
    }

    $args = array('entity' => $entity,'full' => $full);
    
    if (view_exists("{$entity_type}/{$subtype}")) 
    {
        return view("{$entity_type}/{$subtype}", $args);
    }
    else
    {
        return view("{$entity_type}/default", $args);
    }
}

function view_entity_list($entities, $count, $offset, $limit, $fullview = false, $pagination = true) {

    $count = (int) $count;
    $offset = (int) $offset;
    $limit = (int) $limit;

    return view('search/entity_list',array(
        'entities' => $entities,
        'count' => $count,
        'offset' => $offset,
        'limit' => $limit,
        'baseurl' => $_SERVER['REQUEST_URI'],
        'fullview' => $fullview,
        'viewtypetoggle' => false,
        'pagination' => $pagination
      ));
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
    if (view_exists("canvas/layouts/{$layout}")) {
        return view("canvas/layouts/{$layout}",$param_array);
    } else {
        return view("canvas/default",$param_array);
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
 * Wrapper function to display search listings.
 *
 * @param string $icon The icon for the listing
 * @param string $info Any information that needs to be displayed.
 * @return string The HTML (etc) representing the listing
 */
function elgg_view_listing($icon, $info) {
    return view('search/listing',array('icon' => $icon, 'info' => $info));
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
