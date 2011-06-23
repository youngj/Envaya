<?php

/* 
 * A class that represents one controller action, which can process input and render output.
 * Subclasses (defined in engine/action/) should override process_input() and render().
 * 
 * Can be used like so:
 *   $action = new Action_Subclass($controller)
 *   $action->esecute();
 *
 * Simple controller actions can just be defined as controller methods, without necessarily 
 * creating an Action subclass in a new file. But creating a Action subclass allows the logic
 * to be more easily reused elsewhere (and prevents controller files from getting very large).
 */
abstract class Action
{    
    protected $controller;
    static $view_types = array();
    
    function __construct($controller)
    {
        $this->controller = $controller;
        if (!$controller) { throw new Exception("controller is null"); }
    }
        
    public function __call($name, $arguments)
    {
        return call_user_func_array(array($this->controller, $name), $arguments);
    }
    
    function execute()
    {
        $this->before();
        $this->allow_view_types();
    
        $request_method = @$_SERVER['REQUEST_METHOD'];    
        $fn = "do_{$request_method}";
        if (method_exists($this, $fn))
        {
            $this->$fn();
        }
        else
        {
            $this->set_status(405);
            $this->set_content_type('text/plain');
            $this->set_content("Invalid request method $request_method");
        }
            
        $this->after();
    }        
    
    function do_POST()
    {
        try
        {
            $this->validate_security_token();
            $this->process_input();
        }
        catch (ValidationException $ex)
        {
            $this->handle_validation_exception($ex);
        }
        
        $this->record_user_action();
    }
    
    function do_GET()
    {
        $this->render();
    }
    
    function do_HEAD()
    {
        $this->render();
        $this->set_header('Content-Length', strlen($this->get_response()->content));
        $this->set_content('');
    }    
    
    protected function handle_validation_exception($ex)
    {       
        $response = $this->get_response();
        if (@$response->headers['Content-Type'] == 'text/javascript')
        {                    
            $this->render_error_js($ex);
        }
        else
        {            
            if ($ex->is_html())
            {
                SessionMessages::add_error_html($ex->getMessage());
            }
            else
            {
                SessionMessages::add_error($ex->getMessage());
            }                
            $this->render();
            if (!$response->content)
            {
                throw new RedirectException();
            }    
        }
    }
    
    protected function validate_security_token()
    {
        validate_security_token();    
    }
    
    protected function record_user_action()
    {
        $user = Session::get_loggedin_user();
        if ($user)
        {
            $user->last_action = time();
            $user->save();
        }                
    }

    /*
     * Subclasses should override to process the input from a POST request.
     * This function may forward the user to another page, or call render() on errors.
     */
    function process_input() 
    {
    }
    
    /*
     * Subclasses should override to render the page for a GET request (or POST request with errors)
     */    
    function render()
    {    
    }

    function before()
    {
    }

    function after()
    {
    }

    function allow_view_types()
    {
        $this->controller->allow_view_types(static::$view_types);
    }
    
    function render_captcha($vars = null)
    {
        Session::start(); // make sure that securitytoken is correct
        
        if (method_exists($this->controller, 'use_public_layout'))
        {        
            $this->controller->use_public_layout();        
        }
        $this->page_draw(array(
            'title' => __('captcha:title'),
            'content' => view("captcha/captcha_form", $vars),
        ));
    }
    
    function needs_captcha()
    {
        $user = Session::get_loggedin_user();
        
        // show captcha if not logged in
        if (!$user)
            return true;
                
        // show captcha if not approved and using someone else's site.
        if (!$user->is_approved())
        {
            $controller = $this->controller;
            if (method_exists($controller, 'get_org'))
            {
                $site_org = $controller->get_org();            
                if ($site_org && $user->guid != $site_org->guid)
                    return true;
            }
        }
        
        return false;
    }
    
    function check_captcha()
    {       
        if (!$this->needs_captcha())
        {
            return true;
        }
    
        // after entering a correct captcha, we avoid prompting the user for a captcha again 
        // the next few times during the same session
        $free_captchas = Session::get('free_captchas');
        if ($free_captchas > 0)
        {
            Session::set('free_captchas', $free_captchas - 1);
            return true;
        }
    
        if (get_input('captcha'))
        {
            if (Captcha::check_answer($_POST['captcha_response']))
            {
                Session::set('free_captchas', 3);
                return true;
            }
            else
            {
                SessionMessages::add_error(__('captcha:invalid'));
            }
        }
        return false;
    }
}