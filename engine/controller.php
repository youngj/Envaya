<?php
/**
 * Abstract controller class. Controllers should only be created using a [Request].
 *
 * Controllers methods will be automatically called in the following order by
 * the request:
 *
 *     $controller = new Controller_Foo($request);
 *     $controller->before();
 *     $controller->action_bar();
 *     $controller->after();
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

    /**
     * @var  object  Request that created the controller
     */
    public $request;
    
    protected $page_draw_vars = array();

    /**
     * Creates a new controller instance. Each controller must be constructed
     * with the request object that created it.
     *
     * @param   object  Request that created the controller
     * @return  void
     */
    public function __construct(Request $request)
    {
        // Assign the request to the controller
        $this->request = $request;
    }

    public function get_request()
    {
        return $this->request;
    }
        
    public function page_draw($vars)
    {        
        if (get_input('__topbar') == '0')
        {
            $vars['no_top_bar'] = true;
        }
                      
        foreach ($this->page_draw_vars as $k => $v)
        {
            if (!isset($vars[$k]))
            {
                $vars[$k] = $v;
            }
        }
        
        if (!isset($vars['header']) && @$vars['title'])
        {
            $vars['header'] = view('page_elements/title', $vars);
        }
                                
        if (!isset($vars['full_title']))
        {
            $sitename = @$vars['sitename'] ?: Config::get('sitename');     
            if (empty($vars['title'])) 
            {
                $vars['full_title'] = $sitename;
            } 
            else 
            {
                $vars['full_title'] = $sitename . ": " . $vars['title'];
            }
        }
        
        if (Views::get_current_type() == 'default')
        {
            $theme = Theme::get(@$vars['theme_name'] ?: 'simple');
            $vars['css_name'] = $theme->get_css_name();        
            if (!isset($vars['layout']))
            {
                $vars['layout'] = $theme->get_layout();
            }
        }

        $this->request->response = view(@$vars['layout'] ?: 'layouts/default', $vars);
    }

    /*
     * Displays a friendly 404 page and ends the request.
     */            
    public function not_found()
    {
        header("HTTP/1.1 404 Not Found");        
        $this->page_draw(array(
            'title' => __('page:notfound'),
            'content' => view('section', array('content' => __('page:notfound:details')."<br/><br/><br/>"))
        ));        
        echo $this->request->response;
        exit;    
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
            redirect_back_error($ex->getMessage());
        }        
    }

    /*
     * If the client is using a secure HTTPS connection, 
     * redirects to the same page on an insecure connection when possible.
     */    
    public function prefer_http()
    {
        if (!Request::is_post() && Request::$protocol == 'https')
        {
            $url = Request::full_original_url();
            $url = str_replace("https://", "http://", $url);
            forward($url);
        }
    }
    
    /*
     * If the client is using an insecure HTTP connection, 
     * redirects to the same page on https when possible.
     */
    public function prefer_https()
    {
        if (!Request::is_post() && Request::$protocol == 'http' && Config::get('ssl_enabled') && !is_mobile_browser())
        {
            $url = secure_url(Request::full_original_url());
            forward($url);
        }
    }
    
    /*
     * Redirects to the login page if the client is not logged in.
     */    
    public function require_login()
    {
        if (!Session::isloggedin())
        {
            force_login();
        }
    }

    /*
     * Redirects to the login page if the client is not an administrator.
     */        
    public function require_admin()
    {
        if (!Session::isadminloggedin())
        {
            if (Session::isloggedin())
            {
                SessionMessages::add_error(__('noaccess'));
            }
        
            force_login();
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
        
        $view_type = Views::get_current_type();
        
        if ($view_type == 'default' || $view_type == 'mobile' || in_array($view_type, $allowed_view_types))
            return;
        
        Views::set_current_type('default');
    }
    
    function allow_content_translation($allow = true)
    {
        $this->page_draw_vars['show_translate_bar'] = $allow;
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
