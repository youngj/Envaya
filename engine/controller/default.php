<?php

/*
 * The main controller that handles incoming web requests and forwards to child controllers.
 */
class Controller_Default extends Controller
{
    static $routes = array(
        array(
            'regex' => '/$', 
            'defaults' => array('controller' => 'home'), 
        ),
        array(
            'regex' => '/(?P<controller>pg|home|org|admin)\b',
        ),
        array(
            'regex' => '/(?P<username>[\w\-]{3,})\b', 
            'defaults' => array('controller' => 'usersite'), 
            'before' => 'init_user_by_username',
        ),
    );      
    
    public function execute($uri)
    {
        if (get_input('login') && !Session::isloggedin())
        {
            $this->force_login();
        }
        parent::execute($uri);
    }
    
    function init_user_by_username()
    {    
        $user = User::get_by_username($this->param('username'));
                
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
            $this->not_found();
        }
    }
}