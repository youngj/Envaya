<?php

/*
 * The main controller that handles incoming web requests via /index.php
 * and forwards to child controllers.
 */
class Controller_Default extends Controller
{
    static $routes = array(
        array(
            'regex' => '/(?P<controller>pg|admin)\b',
        ),
        array(
            'regex' => '/sg\b',
            'controller' => 'Controller_SMSGateway',
        ),
        array(
            'regex' => '/robots.txt\b',
            'action' => 'action_robots_txt',
        ),        
        array(
            'regex' => '/(?P<username>[\w\-]{3,})\b', 
            'controller' => 'Controller_UserSite',
            'before' => 'init_user_by_username',
        ),
    );      
    
    protected function execute_routes($uri)
    {
        if (!mb_check_encoding(Request::full_original_url()))
        {
            $this->set_status(400);
            $this->set_content_type('text/plain');
            $this->set_content("Invalid URL");
            return;
        }
    
        // Reduce multiple slashes to a single slash
        $uri = preg_replace('#//+#', '/', $uri);

        // Remove all dot-paths from the URI, they are not valid
        $uri = preg_replace('#\.[\s./]*/#', '', $uri);
        
        // map custom domain names to the appropriate user site			
        $host = Request::get_host();
        if ($host != Config::get('domain'))
        {
            if (!preg_match('#^[a-z0-9\-\.]+(:\d+)?$#', $host))
            {
                $this->set_status(400);
                $this->set_content_type('text/plain');
                $this->set_content("Invalid hostname");
                return;
            }
                    
            $username = UserDomainName::get_username_for_host($host);            
            if ($username)
            {
                $this->redirect(abs_url("/{$username}{$uri}"), 302);
                throw new RequestAbortedException();
            }
            else if ($host === Config::get('redirect_domain') && !Request::is_post())
            {
                $this->redirect(abs_url($uri), 301);
                throw new RequestAbortedException();
            }
        }
        
        $this->params['rewritten_uri'] = $uri;
        
        $user = Session::get_logged_in_user();
        if ($user && $user->timezone_id)
        {
            date_default_timezone_set($user->timezone_id);
        }                    
        
        // 'login' query parameter forces user to log in
        if (isset($_GET['login']) && !Session::is_logged_in())
        {
            throw new RedirectException('', $this->get_login_url());
        }

        if (isset($_COOKIE['https']))
        {
            $this->prefer_https();
        }

        // workaround https://bugs.php.net/bug.php?id=60761
        ini_set('zlib.output_compression', true);
                
        // 'lang' query parameter permanently changes interface language via cookie
        if (isset($_GET['lang']))
        {                
            $this->change_viewer_language($_GET['lang']);
        }
                    
        // 'view' query parameter permanently changes interface viewtype via cookie
        $viewtype = isset($_GET['view']) ? $_GET['view'] : null;
        if ($viewtype && Views::is_browsable_type($viewtype))
        {                
            $this->set_cookie('view', $viewtype);
        }
        
        // set viewtype for current request
        $viewtype = $viewtype ?: @$_COOKIE['view'] ?: 
            (Request::is_mobile_browser() ? 'mobile' : 'default');            
        
        if (preg_match('/[^\w]/', $viewtype))
        {            
            $viewtype = 'default';
        }
        Views::set_request_type($viewtype);
        
        // work around flash uploader cookie bug, where the session cookie is sent as a POST field
        // instead of as a cookie
        if (isset($_POST['session_id']))
        {
            $_COOKIE[Config::get('session_cookie_name')] = $_POST['session_id'];
        }
        
        parent::execute_routes($uri);
                    
        if (!Permission::require_passed())
        {
            throw new SecurityException("No permission check");
        }
    }    
    
    function init_user_by_username()
    {    
        $user = User::get_by_username($this->param('username'));
        
        $this->params['user_uri'] = $this->param('rest');
                
        if ($user)
        {
            $this->params['user'] = $user;
        }
        else
        {
            throw new NotFoundException();
        }
    }
    
    function action_robots_txt()
    {
        Permission_Public::require_any();
    
        $this->set_content_type('text/plain');
        
        if (!Config::get('allow_robots'))
        {
            $this->set_content("User-agent: *\nDisallow: /\n");
        }
    }
    
    function after()
    {
        foreach (PageContext::get_http_headers() as $name => $value)
        {
            $this->response->headers[$name] = $value;
        }
    }
}
