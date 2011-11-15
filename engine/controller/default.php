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
            'regex' => '/(?P<guid>\d+)',
            'action' => 'action_guid_redirect',
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
    
    public function execute($uri)
    {
        if (!mb_check_encoding(Request::full_original_url()))
        {
            $this->set_status(400);
            $this->set_content_type('text/plain');
            $this->set_content("Invalid URL");
            return;
        }
    
        try
        {
            // Reduce multiple slashes to a single slash
            $uri = preg_replace('#//+#', '/', $uri);

            // Remove all dot-paths from the URI, they are not valid
            $uri = preg_replace('#\.[\s./]*/#', '', $uri);
            
            // map custom domain names to the appropriate user site			
			$host = Request::get_host();
			if ($host != Config::get('domain'))
			{
				$username = UserDomainName::get_username_for_host($host);            
				if ($username)
				{
					$uri = "/{$username}{$uri}";
				}
			}
            
            $this->params['rewritten_uri'] = $uri;
            
            // 'login' query parameter forces user to log in
            if (isset($_GET['login']) && !Session::is_logged_in())
            {
                throw new RedirectException('', $this->get_login_url());
            }

            if (isset($_COOKIE['https']))
            {
                $this->prefer_https();
            }

            QueryString::set_used_param('lang');            
            
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
                $_COOKIE['envaya'] = $_POST['session_id'];
            }
            
            parent::execute($uri);
        }
        catch (NotFoundException $ex)
        {
            $this->not_found();
        }
        catch (PermissionDeniedException $ex)
        {
            $this->exception_redirect($ex, $this->get_login_url());
        }
        catch (RedirectException $ex)
        {
            $this->exception_redirect($ex, $ex->url, $ex->status);
        }
        catch (RequestAbortedException $ex)
        {
            // nothing to do, move along
        }
        catch (Exception $ex)
        {
            $this->server_error($ex);
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
    
    function action_guid_redirect()
    {
        try
        {
            $entity = Entity::get_by_guid($this->param('guid'));
        }
        catch (InvalidParameterException $ex)
        {
            $entity = null;
        }
        
        if (!$entity)
        {
            throw new NotFoundException();
        }
        
        $url = $entity->get_url();
            
        if (!$url)
        {
            throw new NotFoundException();
        }
        
        $this->redirect($url . $this->param('rest'));        
    }
}
