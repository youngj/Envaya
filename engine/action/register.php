<?php

class Action_Register extends Action
{
    function before()
    {
        $user = Session::get_loggedin_user();
        if ($user && !$user->admin)
        {
            SessionMessages::add(__('register:already_registered'));
            $this->redirect_next($user);
        }
    }

    function process_input()
    {
        $name = trim(get_input('name'));
        if (!$name)
        {
            throw new ValidationException(__('register:user:no_name'));
        }

        $username = trim(get_input('username'));

        User::validate_username($username, 6);

        $password = get_input('password');
        $password2 = get_input('password2');

        User::validate_password($password, $password2, $name, $username);

        $email = EmailAddress::validate(trim(get_input('email')));
        
        if (User::get_by_username($username, true))
        {
            throw new ValidationException(__('register:username_exists'));
        }

        if (!$this->check_captcha())
        {
            return $this->render_captcha();
        }
        
        $user = new User();
        $user->username = $username;
        $user->set_phone_number(get_input('phone'));
        $user->set_email($email);
        $user->name = $name;
        $user->set_password($password);
        $user->language = Language::get_current_code();
        $user->setup_state = SetupState::CreatedAccount;
        $user->save();

        $mail = OutgoingMail::create(
            sprintf(__('register:notification_subject'), $user->name),
            "{$user->get_url()}\n"
        );
        $mail->send_to_admin();
                
        SessionMessages::add(__('register:created_ok'));                
        if (Session::isadminloggedin())
        {            
            $this->redirect('/admin/user');
        }
        else
        {
            Session::login($user, false);
            $this->redirect_next($user);
        }
    }    
    
    function redirect_next($user)
    {
        $next = get_input('next');
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