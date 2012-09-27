<?php

class Action_Registration_RegisterPerson extends Action
{
    function before()
    {
        Permission_Public::require_any();
        $user = Session::get_logged_in_user();
        if ($user)
        {
            throw new RedirectException('', "/pg/register_logged_in");            
        }
    }

    function process_input()
    {
        $name = trim(Input::get_string('name'));
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }

        $username = trim(Input::get_string('username'));

        User::validate_username($username, 6);

        $password = Input::get_string('password');
        $password2 = Input::get_string('password2');

        $email = EmailAddress::validate(trim(Input::get_string('email')));
        $phone_number = Input::get_string('phone');
        
        User::validate_password($password, $password2, array($name, $username, $email, $phone_number));
        
        if (User::get_by_username($username, true))
        {
            throw new ValidationException(__('register:username_exists'));
        }

        if (!$this->check_captcha())
        {
            return $this->render_captcha();
        }
        
        $user = new Person();
        $user->username = $username;
        $user->set_phone_number($phone_number);
        $user->set_email($email);
        $user->name = $name;
        $user->set_password($password);
        $user->language = Language::get_current_code();
        $user->setup_state = User::SetupComplete;
        $user->set_defaults();
        $user->save();
        
        $user->update_scope();
        $user->save();

        
        $user->init_default_widgets();        
        
        EmailSubscription_Registration::send_notifications(Person::Registered, $user);
                
        SessionMessages::add(__('register:created_ok'));                
        Session::login($user);
        $this->redirect_next($user);
    }    
    
    function redirect_next($user)
    {
        $next = Input::get_string('next');
        throw new RedirectException('', $next ?: $user->get_url());
    }
    
    function render()
    {
        $this->page_draw(array(
            'title' => __('register:title'),
            'content' => view("account/register"),
        ));        
    }
}    