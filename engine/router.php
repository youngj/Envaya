<?php

abstract class Router
{
    static $routes = array();
    protected $params = array();    
    protected $parent_controller = null;
    
    /**
     * Creates a new controller instance. 
     */
    public function __construct($parent_controller = null)
    {
        $this->parent_controller = $parent_controller;
    }
    
    /**
     * Retrieves a value from the route parameters.
     *
     *     $id = $controller->param('id');
     *
     * @param   string   key of the value
     * @param   mixed    default value if the key is not set
     * @return  mixed
     */
    public function param($key, $default = NULL)
    {
        if (isset($this->params[$key]))        
        {
            return $this->params[$key];
        }
        if ($this->parent_controller)
        {
            return $this->parent_controller->param($key, $default);
        }
        return $default;
    }

    /*
     * Returns the controller and action that the given URI would be routed to,
     * without actually executing the action or any before/after hooks.
     *
     * The returned action is null if the URI would not be routed to any action.
     */
    public function get_controller_action($uri)
    {
        foreach (static::$routes as $route)
        {
            $params = $this->match_route($route, $uri);
            if ($params)
            {       
                $cls = @$params['controller'];
                if ($cls)
                {
                    $controller = new $cls($this);
                    return $controller->get_controller_action($params['rest']);
                }
                else
                {
                    return array($this, $params['action']);
                }
            }
        }
        return array($this, null);
    }

    /*
     * Tries all the route regexes for this controller, 
     * executing the first one that matches the beginning of the uri.
     * Displays a 404 page if there is no valid route.
     */
    public function execute($uri)
    {
        $cls = get_class($this);
        
        while ($cls != null)
        {    
            $routes = $cls::$routes;
        
            foreach ($routes as $route)
            {        
                $params = $this->match_route($route, $uri);
                if ($params)
                {       
                    return $this->execute_route($route, $params);                
                }
            }
            
            $cls = get_parent_class($cls);
        }
        throw new NotFoundException();
    }    

    /*
     * Performs the action for the route that matched the request URI.
     *
     *  - if the 'before' key is set on the route, calls it as a method on this controller.
     *  - calls before()
     *  - if 'controller' is set (by a match with the named regex  parameters or defaults):
     *      - instantiates that controller and calls execute(), passing the remainder (unmatched part) of the URI
     *  - otherwise, if the method named 'action_<action>' exists, where <action> is taken from the named 
     *    regex parameters or defaults, with a default value of 'index':
     *      - calls 'action_<action>()'
     *  - calls after()
     */
    protected function execute_route($route, $params)
    {        
        foreach ($params as $k => $v)
        {        
            $this->params[$k] = $v;
        }
        
        $before = @$route['before'];
        if ($before)
        {
            $this->$before();
        }
        $this->before();                               
                
        $cls = @$params['controller'];
        if ($cls)
        {
            $controller = new $cls($this);
            $res = $controller->execute($params['rest']);
        }
        else
        {
            $action = $params['action'];
            $this->$action();
            $res = $this;
        }
        $this->after();    
        
        return $res;
    }
    
    /*
     * Tests if the beginning of the URI component matches a given route regex.
     *
     * If the regex matched, returns an associative array of route parameters taken from the
     * matched values of the named regex parameters, merged with the array of defaults.
     * 
     * special keys of return value:
     * 'rest' : the remainder of the URI after the part that matched the regex.
     * 'controller' : the name of the matched controller class (only if 'action' is not set)
     * 'action' : the name of the matched action function in the current class (only if 'controller' is not set)
     *
     * If the route regex did not match, returns false.
     */    
    protected function match_route($route, $uri)
    {
        $regex = @$route['regex'];        
        
        $tr_params = array();
        
        if ($regex)
        {    
            if (!preg_match('#^'.$regex.'#is', $uri, $matches))
                return false;

            $params = array(
                'match' => $matches[0],
                'rest' => substr($uri, strlen($matches[0])) ?: ''
            );
            
            foreach ($matches as $key => $value)
            {
                if (is_int($key))
                {
                    // Skip all unnamed keys
                    continue;
                }
                // Set the value for all matched keys
                $params[$key] = $value;
                $tr_params["<$key>"] = $value;
            }                       
        }
        else
        {
            $params = array(
                'match' => '',
                'rest' => $uri
            );
        }

        if (isset($route['defaults']))
        {
            foreach ($route['defaults'] as $key => $value)
            {
                if (!isset($params[$key]) OR $params[$key] === '')
                {
                    // Set default values for any key that was not matched
                    $params[$key] = $value;
                    $tr_params["<$key>"] = $value;
                }
            }
        }

        $controller_format = isset($route['controller']) ? $route['controller'] : 'Controller_<controller>';        
        $controller = strtr($controller_format, $tr_params);        
        
        if (strpos($controller, '<') === false) // current route refers to another controller
        {
            if (!class_exists($controller))
            {
                return false;
            }
            $params['controller'] = $controller;
            return $params;
        }
        
        $action_format = isset($route['action']) ? $route['action'] : 'action_<action>';        
        $action = strtr($action_format, $tr_params);
        
        if (strpos($action, '<') === false) // current route is a method in the current class
        {
            if (!method_exists($this, $action))
            {
                return false;
            }
            $params['action'] = $action;
            return $params;
        }

        return false;
    }
    
    static function add_route($route, $index = null)
    {
        if ($index === null)
        {
            static::$routes[] = $route;
        }
        else
        {
            array_splice(static::$routes, $index, 0, array($route));
        }
    }    
    
    /**
     * Automatically executed before the controller action. Can be used to set
     * class properties, do authorization checks, and execute other custom code.
     *
     * @return  void
     */
    public function before()
    {
        // Nothing by default
    }

    /**
     * Automatically executed after the controller action. Can be used to apply
     * transformation to the request response, add extra output, and execute
     * other custom code.
     *
     * @return  void
     */
    public function after()
    {
    }
    
}