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
abstract class Controller extends Router {

    static $SIMPLE_ROUTES = array(
        array(
            'regex' => '/(?P<action>\w+)\b',
        ),
    );
    
    protected $parent_controller;    
    
    protected $response = null;
    
    protected $page_draw_vars = array();

    /**
     * Creates a new controller instance. 
     */
    public function __construct($parent_controller = null)
    {
        parent::__construct($parent_controller);
        
        if (!$parent_controller)
        {
            $this->response = new Response();
        }
        else
        {
            $this->response = $parent_controller->response;
        }
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
            if (!isset($vars['css_name']))
            {
                $vars['css_name'] = $theme->get_css_name();
            }
            
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
        $vars['original_url'] = Request::full_original_url();
        $vars['css_url'] = css_url(@$vars['css_name'] ?: 'simple');        
        $vars['base_url'] = abs_url('/', (Request::is_secure() ? 'https' : 'http'));

        $vars['content'] = view('page_elements/content_wrapper', $vars);
        
        if (!isset($vars['messages']))
        {
            $vars['messages'] = view('page_elements/messages', $vars);
        }
        
        if (!isset($vars['header']))
        {
            $vars['header'] = view('page_elements/header', $vars);
        }
        
        if (!isset($vars['footer'])) 
        {
            $vars['footer'] = view('page_elements/footer', $vars); 
        }
        
        if (!isset($vars['site_menu']))
        {
            $vars['site_menu'] = view('page_elements/site_menu', $vars);
        }        
    }
    
    public function page_draw($vars)
    {                                     
        $this->prepare_page_draw_vars(/* & */ $vars);                
        $this->response->content = view('layouts/base', $vars);
    }

    protected function get_canonical_url()
    {
        $canonical_url = abs_url(strtolower($this->get_matched_uri()), Request::get_protocol());
        
        foreach ($_GET as $param => $value)
        {
            if (QueryString::is_used_param($param))
            {
                $canonical_url = url_with_param($canonical_url, $param, $value);
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
        if ($allow)
        {
            TranslateMode::set_current(((int)get_input("trans")) ?: TranslateMode::Approved);
        }
        else        
        {
            TranslateMode::set_current(TranslateMode::Disabled);
        }
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
