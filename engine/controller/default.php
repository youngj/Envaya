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
            'regex' => '/(?P<username>[\w\-]{3,})\b', 
            'defaults' => array('controller' => 'usersite'), 
            'before' => 'init_user_by_username',
        ),
    );      
    
    public function execute($uri)
    {
        try
        {
            // Reduce multiple slashes to a single slash
            $uri = preg_replace('#//+#', '/', $uri);

            // Remove all dot-paths from the URI, they are not valid
            $uri = preg_replace('#\.[\s./]*/#', '', $uri);

            // map custom domain names to the appropriate user site
            $username = OrgDomainName::get_username_for_host($request->host);            
            if ($username)
            {
                $uri = "/{$username}{$uri}";
            }
            
            $this->params['rewritten_uri'] = $uri;
            
            // 'login' query parameter forces user to log in
            if (@$_GET['login'] && !Session::isloggedin())
            {
                $this->force_login();
            }

            // 'lang' query parameter permanently changes interface language via cookie
            if (@$_GET['lang'])
            {
                $this->change_viewer_language($_GET['lang']);
            }    
            
            // 'view' query parameter permanently changes interface viewtype via cookie
            $viewtype = @$_GET['view'];
            if ($viewtype && Views::is_browsable_type($viewtype))
            {
                set_cookie('view', $viewtype);
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
            if (@$_POST['session_id'])
            {
                $_COOKIE['envaya'] = $_POST['session_id'];
            }
            
            parent::execute($uri);
        }
        catch (NotFoundException $ex)
        {
            $this->not_found();
        }
        catch (RedirectException $ex)
        {
            $msg = $ex->getMessage();
            if ($msg)
            {
                SessionMessages::add_error($msg);
            }
            if (Request::is_post())
            {
                Session::save_input();
            }
            $this->redirect($ex->url, $ex->status);
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

            if ($user instanceof Organization)
            {
                $this->params['org'] = $user;
            }
        }
        else
        {
            throw new NotFoundException();
        }
    }
}