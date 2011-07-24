<?php
/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * URLs are routed to controller methods by the execute($uri) method. 
 * Controller classes may define a static $routes array. Each element of the $routes 
 * array describes a path regex, and what action to take when the regex matches the
 * next component of the $uri.
 *
 * The controller action should add the output it creates to
 * `$this->response->content`, typically in the form of a [View], during the
 * "action" part of execution.
 *
 * @package    Kohana
 * @category   Controller
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
abstract class Controller {

    static $routes = array();

    static $SIMPLE_ROUTES = array(
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),
    );
    
    protected $parent_controller;
    protected $params = array();    
    
    protected $response = null;
    
    protected $page_draw_vars = array();

    /**
     * Creates a new controller instance. 
     */
    public function __construct($parent_controller = null)
    {
        $this->parent_controller = $parent_controller;
        
        if (!$parent_controller)
        {
            $this->response = new Response();
        }
        else
        {
            $this->response = $parent_controller->response;
        }
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
        foreach (static::$routes as $route)
        {
            $params = $this->match_route($route, $uri);
            if ($params)
            {       
                return $this->execute_route($route, $params);                
            }
        }
        throw new NotFoundException();
    }
    
    public function full_rewritten_url()
    {
        $domain = Config::get('domain');        
        $protocol = Request::get_protocol();
        $base_uri = $this->param('rewritten_uri');
        $query = Request::get_query();        
        return "{$protocol}://{$domain}{$base_uri}{$query}";
    }
    
    public function get_matched_uri()
    {
        $match = $this->param('match');
    
        if ($this->parent_controller)
        {
            return $this->parent_controller->get_matched_uri() . $match;
        }
        else
        {
            return $match;
        }
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
            $controller->execute($params['rest']);
        }
        else
        {
            $action = $params['action'];
            $this->$action();
        }
        $this->after();    
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
            if (!preg_match('#^'.$regex.'#i', $uri, $matches))
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

    protected function prepare_page_draw_vars(&$vars)
    {
        foreach ($this->page_draw_vars as $k => $v)
        {
            if (!isset($vars[$k]))
            {
                $vars[$k] = $v;
            }
        }    
    
        if (get_input('__topbar') == '0')
        {
            $vars['no_top_bar'] = true;
        }        
        
        $viewtype = Views::get_request_type();
        if ($viewtype == 'default')
        {
            $theme = Theme::get(@$vars['theme_name'] ?: Config::get('fallback_theme'));
            $vars['css_name'] = $theme->get_css_name();
            
            if (!isset($vars['layout']))
            {
                $vars['layout'] = $theme->get_layout();
            }
            
            Views::set_current_type($theme->get_viewtype());            
        }
        else
        {
            $vars['layout'] = 'layouts/default';
        }
        
        if (!isset($vars['header']) && @$vars['title'])
        {
            $vars['header'] = view('page_elements/content_header', $vars);
        }
                                
        if (!isset($vars['full_title']))
        {
            $full_title = @$vars['site_name'] ?: Config::get('site_name');                 
            if (!@$vars['is_site_home'])
            {                
                $full_title .= ": " . @$vars['title'];
            }                        
            $vars['full_title'] = $full_title;
        }

        $vars['translate_url'] = PageContext::get_translation_url();
        $vars['canonical_url'] = $this->get_canonical_url();
        $vars['original_url'] = Request::full_original_url();
        $vars['css_url'] = css_url(@$vars['css_name'] ?: 'simple');        
        $vars['base_url'] = abs_url('/', (Request::is_secure() ? 'https' : 'http'));
    }
    
    public function page_draw($vars)
    {                                     
        $this->prepare_page_draw_vars(/* & */ $vars);        
        $this->response->content = view('layouts/base', $vars);
    }

    protected function get_canonical_url()
    {
        $canonical_url = Request::full_original_url();
        $ignored_params = array('view','login','_lt','__topbar');
        
        foreach ($ignored_params as $ignored_param)
        {
            if (@$_GET[$ignored_param])
            {
                $canonical_url = url_with_param($canonical_url, $ignored_param, null);
            }
        }
        
        return $canonical_url;        
    }
    
    /**
     * Adds messages to the session so they'll be carried over, and forwards the browser.
     */    
    function redirect($url = null, $status = 302)
    {
        if (!$url)
        {
            $url = @$_SERVER['HTTP_REFERER'] ?: "/";
        }
    
        SessionMessages::save();

        $this->set_status($status);
        $this->set_header('Location', abs_url($url));
    }
    
    /*
     * Displays a friendly 404 page, unless the url matches a global NotFoundRedirect pattern,
     * in which case it redirects the user to another page.
     */            
    function not_found()
    {
        $redirect_url = NotFoundRedirect::get_redirect_url(Request::get_uri());
        if ($redirect_url)
        {
            $this->redirect($redirect_url);
        }
        else
        {        
            $this->set_status(404);
            $this->page_draw(array(
                'title' => __('page:notfound'),
                'content' => view('section', array('content' => __('page:notfound:details')."<br/><br/><br/>"))
            ));                
        }
    }
    
    function render_error_js($exception)
    {
        $res = array(
            'error' => $exception->getMessage(), 
            'errorClass' => get_class($exception)
        );
        
        $this->response->content = json_encode($res);    
    }
    
    function server_error($exception)
    {
        ob_discard_all();
    
        $this->set_status(500);
    
        $content_type = @$this->response->headers['Content-Type'];
        if ($content_type == 'text/javascript')
        {
            $this->render_error_js($exception);
        }
        else
        {
            $this->page_draw(array(
                'title' => __('exception_title'),
                'theme_name' => Config::get('debug') ? 'simple_wide' : 'simple',
                'hide_login' => true,
                'content' => view("messages/exception", array('object' => $exception))
            ));
        }    
        
        notify_exception($exception);
    }            

    /*
     * Redirects to the previous page if the submitted security token 
     * (rendered by view input/securitytoken) is incorrect.
     * Should be called at the beginning of a POST request to protect against CSRF attacks.
     */
    public function validate_security_token()
    {
        try
        {
            validate_security_token();
        }
        catch (ValidationException $ex)
        {
            throw new RedirectException($ex->getMessage());
        }        
    }

    /*
     * If the client is using a secure HTTPS connection, 
     * redirects to the same page on an insecure connection when possible.
     */    
    public function prefer_http()
    {
        if (!Request::is_post() && Request::is_secure())
        {
            $url = abs_url(Request::full_original_url(), 'http');
            throw new RedirectException('', $url);
        }
    }
    
    /*
     * If the client is using an insecure HTTP connection, 
     * redirects to the same page on https when possible.
     */
    public function prefer_https()
    {
        if (!Request::is_post() && !Request::is_secure() 
            && Config::get('ssl_enabled') && !Request::is_mobile_browser())
        {
            $url = secure_url(Request::full_original_url());
            throw new RedirectException('', $url);
        }
    }
    
    /*
     * Redirects to the login page if the client is not logged in.
     */    
    public function require_login()
    {
        if (!Session::isloggedin())
        {
            if (@$this->response->headers['Content-Type'] == 'text/javascript')
            {
                $this->set_status(403);
                throw new RequestAbortedException();
            }
            else
            {
                $this->force_login();
            }
        }
    }

    function force_login($msg = '')
    {
        $next = $this->full_rewritten_url();
        
        $args = array();
        
        $arg_names = array('username','_lt','__topbar');
        foreach ($arg_names as $arg_name)
        {        
            $arg = get_input($arg_name);
            if ($arg !== '')
            {
                $args[$arg_name] = $arg;
            }
        }
        
        if ($next)
        {
            $args['next'] = $next;
        }
        
        $query = $args ? ("?".http_build_query($args)) : "";
        
        throw new RedirectException($msg, "/pg/login{$query}");      
    }
    
    /*
     * Redirects to the login page if the client is not an administrator.
     */        
    public function require_admin()
    {
        if (!Session::isadminloggedin())
        {
            $this->force_login(Session::isloggedin() ? __('page:noaccess') : '');
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
    
    public function allow_view_types($allowed_view_types = null /* array, or variable arguments */)
    {
        if (!is_array($allowed_view_types))
        {
            $allowed_view_types = func_get_args();
        }
    
        if (in_array('rss', $allowed_view_types))
        {
            $this->page_draw_vars['rss_url'] = url_with_param(Request::full_original_url(), 'view', 'rss');
        }
        
        $view_type = Views::get_request_type();
        
        if (Views::is_browsable_type($view_type) || in_array($view_type, $allowed_view_types))
            return;
        
        Views::set_request_type('default');
    }   
    
    function allow_content_translation($allow = true)
    {
        $this->page_draw_vars['show_translate_bar'] = $allow;
    }
    
    function change_viewer_language($new_language)
    {
        $this->set_cookie('lang', $new_language);
    }    
    
    function set_cookie($name, $val, $expireTime = 0)
    {
        $cookie_domain = Config::get('cookie_domain');
        if ($cookie_domain)
        {
            setcookie($name, $val, $expireTime, '/', $cookie_domain);
        }
        setcookie($name, $val, $expireTime, '/');    
    }
        
    private function _set_cookie($name, $value = null, 
        $expire = null, $path = null, $domain = null, 
        $secure = null, $httponly = null)
    {        
		// TODO: Handle $secure and $httponly		    
    
        $cookie_str = urlencode($name)."=".urlencode($value).";";    
    
		if ($expire)
        {
            $datetime = new DateTime(date("Y-m-d H:i:s", $expire));
            $cookie_time = $datetime->format(DATE_COOKIE);
            
            $cookie_str .= " expires=".$cookie_time.";";
        }
		
        if ($path != null)
        {
            $cookie_str .= " path=".$path.";";
        }
        
        if ($domain != null)
        {
            $domain = $_SERVER['HTTP_HOST'];
            
            $cookie_str .= " domain=".$domain.";";
        }
        
        $this->set_header('Set-Cookie', $cookie_str);		
    }
        
    function set_header($name, $value)
    {
        $this->response->headers[$name] = $value;
    }
    
    function set_content_type($content_type)
    {
        $this->set_header('Content-Type', $content_type);
    }
    
    function set_status($status)
    {
        $this->response->status = $status;
    }
    
    function set_content($content)
    {
        $this->response->content = $content;
    }
    
    function get_response()
    {
        return $this->response;
    }
    
    function get_parent_controller()
    {
        return $this->parent_controller;
    }
} // End Controller
