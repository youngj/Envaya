<?php

class SMS_Controller_News extends SMS_Controller
{
    static $routes = array(
        array(
            'regex' => '(p|post)\s+(?P<message>.+)',
            'action' => 'action_post',
        ),               
        array(
            'regex' => '(p|post)\b',
            'action' => 'action_post_help',
        ),     
        array(
            'regex' => '(d|delete)\s+(?P<guid>\d+)',
            'action' => 'action_delete',
        ),       
        array(
            'regex' => '(d|delete)\b',
            'action' => 'action_delete_help',
        ),       
        array(
            'regex' => '(l|lang|language)\s+(?P<lang>\w+)',
            'action' => 'action_language',
        ),        
        array(
            'regex' => '(l|lang|language)\b',
            'action' => 'action_language_help',
        ),
        array(
            'regex' => 'user\b',
            'action' => 'action_user',
        ),        
        array(
            'regex' => '(in|(log in)|login)\s+(?P<username>[\w\-]+)\s+(?P<password>.*)',
            'action' => 'action_login',
        ),            
        array(
            'regex' => '(in|(log in)|login)\b',
            'action' => 'action_login_help',
        ),                    
        array(
            'regex' => '(out|(log out)|logout)\b',
            'action' => 'action_logout',
        ),    
        array(
            'regex' => '(h|help|menu|info)\b',
            'action' => 'action_help',
        ),        
        array(
            'regex' => '(?P<message>.*)',
            'action' => 'action_default',
        ),         
    );
        
    function action_post()
    {    
        $this->post_message($this->param('message'));
    }
    
    function action_post_help()
    {
        // if user has possible message saved from before, "P" alone will post it
        $message = $this->get_state('message');
        if ($message)
        {
            $this->post_message($message);
        }
        else
        {
            $this->reply(__('sms:post_help'));   
        }        
    }    
        
    function post_message($message)
    {        
        $user = Session::get_loggedin_user();
        if (!$user)
        {
            $this->set_state('message', $message);            
            $this->set_default_action('login');
            $this->reply(__('sms:login_to_post'));      
            return;
        }
        
        $this->set_default_action(null);
        
        $news = $user->get_widget_by_class('News');
        if (!$news->guid)
        {
            $news->save();
        }
        
        $post = $news->new_widget_by_class('SMSPost');
        $post->owner_guid = $user->guid;
        $post->set_content($message);
        $post->save();
        $post->post_feed_items();
        
        $this->reply(strtr(__('sms:post_published'),
            array(
                '{url}' => $post->get_url(),
                '{id}' => $post->guid
            )
        ));            
        $this->set_state('message', null);
    }
    
    function action_delete()
    {
        $guid = $this->param('guid');
        
        $post = Widget_Post::get_by_guid($guid);
        
        if (!$post)
        {
            $this->reply(strtr(__('sms:post_not_found'), array('{id}' => $guid)));
        }        
        else if (!$post->can_edit())
        {
            $this->reply(__('sms:cant_delete_post'));
        }
        else
        {
            $post->disable();
            $post->save();
            $this->reply(__('sms:post_deleted'));
        }
    }    
    
    function action_delete_help()
    {
        $this->reply(__('sms:delete_help'));
    }
    
    function action_language()
    {
        $lang = strtolower($this->param('lang'));
        
        $languages = Config::get('languages');
        
        if (isset($languages[$lang]))
        {
            $this->set_state('lang', $lang);
            Language::set_current_code($lang);
            $this->reply(__('sms:language_changed'));
        }
        else
        {
            $this->reply(strtr(__('sms:bad_language'), array('lang' => $lang)));
        }
    }
    
    function action_language_help()
    {
        $this->reply(__('sms:language_help'));
    }
    
    function action_user()
    {
        $user = Session::get_loggedin_user();
        if ($user)
        {
            $this->reply(strtr(__('sms:logged_in'), array(
                '{username}' => $user->username,
                '{name}' => $user->name,
            )));
        }
        else
        {
            $this->reply(__('sms:logged_out'));    
            $this->set_default_action('login');
        }
    }   
    
    function action_logout()
    {        
        $this->set_default_action(null);
        $this->logout();
        $this->reply(__('sms:logout_success'));
    }    
    
    function action_login()
    {
        $username = $this->param('username');
        $password = $this->param('password');
        
        $this->try_login($username, $password);        
    }
    
    function action_login_help()
    {
        if (!Session::get_loggedin_user())
        {
            $this->reply(__('sms:login_help'));            
            $this->set_default_action('login');            
        }
        else
        {
            $this->action_user();
        }
    }    
    
    function try_login($username, $password)
    {
        $this->set_default_action('login');
        
        $user = User::get_by_username($username);
        
        if (!$user)
        {
            $this->reply(strtr(__('sms:login_unknown_user'), array('{username}' => $username)));
            return;
        }
        else if (!($user instanceof Organization))
        {
            $this->reply(strtr(__('sms:login_not_org'), array('{username}' => $username)));
            return;
        }
        else if (!$user->has_password($password))
        {
            $this->reply(strtr(__('sms:login_bad_password'), array('{username}' => $username, '{password}' => $password)));            
            return;
        }
        else
        {
            $this->set_default_action(null);
            $this->login($user);
            
            $message = $this->get_state('message');
            
            if ($message)
            {
                $this->post_message($message);
            }
            else
            {                        
                $this->reply(__('sms:login_success').' '.__('sms:post_help'));
            }
        }
    }
    
    function action_default()
    {
        $message = $this->param('message');
        
        // heuristics to guess possible intent (possibly based on previous state)
        // if the message doesn't match any explicit rules
    
        $default_action = $this->get_default_action();
        
        if ($default_action == 'login')
        {
            list($username, $password) = explode(" ", $message, 2);
            $this->try_login($username, $password);
        }
        else if (strlen($message) > 20)
        {    
            $snippet = substr($message, 0, 20);
            $this->reply(strtr(__('sms:publish_last_help'), array('snippet' => $snippet)));
            $this->set_state('message', $message);
        }
        else
        {
            throw new NotFoundException();
        }
    }
    
    function action_help()
    {    
        $this->set_default_action(null);
        $this->reply(__('sms:help'));
    }    
    
    public function execute($message)
    {       
        try
        {
            return parent::execute($message);
        }
        catch (NotFoundException $ex)
        {
            $msg = $ex->getMessage();
            $this->reply($msg ?: __('sms:bad_command'));
            return $this;
        }
    }
}
