<?php

class SMS_Controller_News extends SMS_Controller
{
    static $routes = array(
        array(
            'regex' => '(post|p)\s+(?P<message>.*)',
            'action' => 'action_post',
        ),               
        array(
            'regex' => '(post|p)\b',
            'action' => 'action_index',
        ),     
        array(
            'regex' => 'user\b',
            'action' => 'action_user',
        ),
        array(
            'regex' => '(delete)\s+(?P<post_guid>\d+)',
            'action' => 'action_delete_post',
        ),                   
        array(
            'regex' => '(y|yes)\b',
            'action' => 'action_confirm_post',
        ),                   
        array(
            'regex' => '(help|menu|info)\b',
            'action' => 'action_help',
        ),
        array(
            'regex' => '((log in)|login)\s+(?P<username>[\w\-]+)\s+(?P<password>.*)',
            'action' => 'action_login',
        ),            
        array(
            'regex' => '((log in)|login)',
            'action' => 'action_login_format',
        ),                    
        array(
            'regex' => '((log out)|logout)\b',
            'action' => 'action_logout',
        ),            
        array(
            'regex' => '(?P<message>.*)',
            'action' => 'action_default',
        ),         
    );
    
    protected $user;
    
    function before()
    {
        $user_guid = $this->get_state('user_guid');
        if ($user_guid)
        {
            $this->user = User::get_by_guid($user_guid);
        }
    }
    
    function action_post()
    {    
        $this->post_message($this->param('message'));
    }
        
    function post_message($message)
    {        
        $user = $this->user;
        if (!$user)
        {
            $this->set_state('message', $message);
            $this->set_state('login_prompt', true);
            $this->reply("To post your message, you need to log in to Envaya. Txt LOGIN then your Envaya username then your password");      
            return;
        }        
        
        $this->set_state('login_prompt', false);
        
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
        
        $this->reply("Your news update has been published at {$post->get_url()} !
To delete this news update, txt DELETE {$post->guid}.");
        $this->set_state('message', null);
    }
    
    function action_delete_post()
    {
        $user = $this->user;
        $post_guid = $this->param('post_guid');
        
        $post = Widget_Post::get_by_guid($post_guid);
        
        if (!$post)
        {
            $this->reply("News update {$post_guid} was not found.");
        }        
        else if (!$post->can_user_edit($user))
        {
            $this->reply("You do not have access to delete this news update.");
        }
        else
        {
            $post->disable();
            $post->save();
            $this->reply("News update deleted successfully.");
        }
    }    
    
    function action_user()
    {
        $user = $this->user;
        if ($user)
        {
            $this->reply("You are logged in as {$user->username} ({$user->name}). Txt LOGOUT to log out.");    
        }
        else
        {
            $this->reply("You are logged out. To log in, txt LOGIN then your Envaya username then your password");    
            $this->set_state('login_prompt', true);
        }
    }
    
    function action_index()
    {
        $user = $this->user;
        if ($user)
        {
            $this->reply("You are currently logged in as {$user->username}. 
To publish a message to your News page on Envaya, txt P + your message.");
        }
        else        
        {
            $this->action_login_format();
        }
    }
    
    function action_logout()
    {
        $this->set_state('login_prompt', false);
        $this->set_state('user_guid', null);
        $this->reply("Successfully logged out.");
    }
    
    function action_login_format()
    {
        if (!$this->user)
        {
            $this->reply("To log in to Envaya, txt \"LOGIN [your Envaya username] [your password]\".");            
            $this->set_state('login_prompt', true);
        }
        else
        {
            $this->action_user();
        }
    }
    
    function action_login()
    {
        $username = $this->param('username');
        $password = $this->param('password');
        
        $this->try_login($username, $password);        
    }
    
    function try_login($username, $password)
    {
        $this->set_state('login_prompt', true);
        
        $user = User::get_by_username($username);
        
        if (!$user)
        {
            $this->reply("The username '$username' does not exist on Envaya. Please correct the username, then txt \"LOGIN [your username] [your password]\"");
            return;
        }
        else if (!($user instanceof Organization))
        {
            $this->reply("The username '$username' cannot access this system because it is not registered as an Organization.");
            return;
        }
        else if (!$user->has_password($password))
        {
            $this->reply("The password '$password' was incorrect for username '$username'. Please correct the password, then txt \"LOGIN [your username] [your password]\".");            
            return;
        }
        else
        {
            $this->user = $user;            
            
            $this->set_state('login_prompt', false);
            $this->set_state('user_guid', $user->guid);
            
            $message = $this->get_state('message');
            
            if ($message)
            {
                $this->post_message($message);
            }
            else
            {                        
                $this->action_index();
            }
        }                
    }
    
    function action_default()
    {
        $message = $this->param('message');
    
        if ($this->get_state('login_prompt'))
        {
            list($username, $password) = explode(" ", $message, 2);
            $this->try_login($username, $password);
        }
        else if (strlen($message) > 20)
        {    
            $snippet = substr($message, 0, 20);
            $this->reply("To publish your last message (\"$snippet...\") on your News page, reply with txt YES. Or, txt HELP for other options.");
            $this->set_state('message', $message);
        }
        else
        {
            throw new NotFoundException();
        }
    }       
    
    function action_confirm_post()
    {
        $message = $this->get_state('message');
        if ($message)
        {
            $this->post_message($message);
        }
        else
        {
            $this->reply("No message to publish.");
        }
    }
    
    function action_help()
    {    
        $this->set_state('login_prompt', false);
    
        ob_start();
        echo "Send SMS to publish updates on your News page on Envaya.\n";
        echo "To publish news, txt P + your message.\n";
        echo "Other commands: LOGIN, LOGOUT\n";
        echo "Msg&Data Rates May Apply.";
    
        $this->reply(ob_get_clean());        
//More at http://envaya.org/sms
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
            $this->reply($msg ?: "Unknown command. Txt HELP for a list of commands.");
            return $this;
        }
    }
}
