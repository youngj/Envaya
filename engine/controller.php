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
 * `$this->request->response`, typically in the form of a [View], during the
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

    public $request;    
    protected $parent_controller;
    protected $params;    
    
    protected $page_draw_vars = array();

    /**
     * Creates a new controller instance. Each controller must be constructed
     * with the request object that created it.
     *
     * @param   object  Request that created the controller
     * @return  void
     */
    public function __construct(Request $request, $parent_controller = null)
    {
        // Assign the request to the controller
        $this->request = $request;
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
        $this->params = $params;
        
        $before = @$route['before'];
        if ($before)
        {
            $this->$before();
        }
        $this->before();                               
                
        $controller = @$params['controller'];
        if ($controller)
        {
            $cls = "Controller_{$controller}";
            if (!class_exists($cls))
            {
                throw new NotFoundException();
            }
            $controller = new $cls($this->request, $this);
            $controller->execute($params['rest']);
        }
        else
        {
            $action = @$params['action'] ?: 'index';                
            $method = "action_{$action}";
            if (!method_exists($this, $method))
            {
                throw new NotFoundException();
            }
            $this->$method();                       
        }
        $this->after();    
    }
    
    /*
     * Tests if the beginning of the URI component matches a given route regex.
     *
     * If the regex matched, returns an associative array of route parameters taken from the
     * matched values of the named regex parameters, merged with the array of defaults,
     * with the special parameter 'rest' which is the remainder of the URI after the
     * part that matched the regex.
     *
     * If the route regex did not match, returns false.
     */    
    protected function match_route($route, $uri)
    {
        $regex = @$route['regex'];
        
        if ($regex)
        {    
            if (!preg_match('#^'.$regex.'#i', $uri, $matches))
                return false;

            $params = array('rest' => 
                substr($uri, strlen($matches[0])) ?: ''
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
            }                       
        }
        else
        {
            $params = array('rest' => $uri);
        }

        $defaults = @$route['defaults'];
        if ($defaults)
        {
            foreach ($defaults as $key => $value)
            {
                if (!isset($params[$key]) OR $params[$key] === '')
                {
                    // Set default values for any key that was not matched
                    $params[$key] = $value;
                }
            }
        }
   
        return $params;                
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

    public function get_request()
    {
        return $this->request;
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

        $vars['canonical_url'] = $this->get_canonical_url();
        $vars['original_url'] = $this->request->full_original_url();
        $vars['css_url'] = css_url(@$vars['css_name'] ?: 'simple');
        $vars['is_secure'] = $this->request->is_secure();                    
        $vars['base_url'] = $vars['is_secure'] ? Config::get('secure_url') : Config::get('url');
    }
        
    public function page_draw($vars)
    {                                     
        $this->prepare_page_draw_vars(/* & */ $vars);        
        $this->request->response = view('layouts/base', $vars);
    }

    protected function get_canonical_url()
    {
        $canonical_url = $this->request->full_original_url();
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
    
        if ($url && $url[0] == '/')
        {
            $url = substr($url, 1);
        }

        if (!preg_match('#^http(s)?://#', $url))
        {
            $url = Config::get('url').$url;
        }

        SessionMessages::save();

        $request = $this->request;    
        $request->status = $status;
        $request->headers['Location'] = $url;               
    }
    
    /*
     * Displays a friendly 404 page, unless the url matches a global NotFoundRedirect pattern,
     * in which case it redirects the user to another page.
     */            
    function not_found()
    {
        $request = $this->request;
        $redirect_url = NotFoundRedirect::get_redirect_url($request->uri);
        if ($redirect_url)
        {
            $this->redirect($redirect_url);
        }
        else
        {        
            $request->status = 404;        
            $this->page_draw(array(
                'title' => __('page:notfound'),
                'content' => view('section', array('content' => __('page:notfound:details')."<br/><br/><br/>"))
            ));                
        }
    }
            
    public function add_generic_footer()
    {
        $footer = PageContext::get_submenu('footer');
    
        $footer->add_item(__('about'), "/envaya");
        $footer->add_item(__('contact'), "/envaya/contact");
        $footer->add_item(__('donate'), "/envaya/page/contribute");    
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
        $request = $this->request;
        if (!$request->is_post() && $request->is_secure())
        {
            $url = $request->full_original_url();
            $url = str_replace("https://", "http://", $url);
            throw new RedirectException('', $url);
        }
    }
    
    /*
     * If the client is using an insecure HTTP connection, 
     * redirects to the same page on https when possible.
     */
    public function prefer_https()
    {
        $request = $this->request;
        if (!$request->is_post() && !$request->is_secure() && Config::get('ssl_enabled') && !is_mobile_browser())
        {
            $url = secure_url($request->full_original_url());
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
            $this->force_login();
        }
    }

    function force_login($msg = '')
    {
        $next = $this->request->full_rewritten_url();
        
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
            $this->page_draw_vars['rss_url'] = url_with_param($this->request->full_original_url(), 'view', 'rss');
        }
        
        $view_type = Views::get_request_type();
        
        if (Views::is_browsable_type($view_type) || in_array($view_type, $allowed_view_types))
            return;
        
        Views::set_request_type('default');
    }   
    
    function allow_content_translation($allow = true)
    {
        $this->page_draw_vars['show_translate_bar'] = $allow;
        if ($allow)
        {
            EventRegister::register_handler('translate','all', array('PageContext','translate_listener'));
        }
    }
    
    function change_viewer_language($newLanguage)
    {
        set_cookie('lang', $newLanguage);
    }    
    
    function set_content_type($content_type)
    {
        $this->request->headers['Content-Type'] = $content_type;
    }
    
    function set_response($response)
    {
        $this->request->response = $response;
    }
} // End Controller
