<?php

class Controller_Pg extends Controller {

    function action_login()
    {
        $username = get_input('username');
        $next = get_input('next');
        $error = get_input('error');

        $title = __("login");
        
        $error_msg = $error ? view('account/login_error') : '';
        
        $body = view_layout('one_column_padded',
            view_title($title, array('org_only' => true)),
            view("account/forms/login", array('username' => $username, 'next' => $next)),
            $error_msg
        );

        $this->page_draw($title, $body, array('hideLogin' => true));
    }

    function action_tci_donate_frame()
    {
        echo view("page/tci_donate_frame", $values);    
    }
    
    function action_submit_donate_form()
    {    
        $values = $_POST;
        $amount = (int)$values['_amount'] ?: (int)$values['_other_amount'];
        $values['donation'] = $amount;
        
        $emailBody = "";
        
        foreach ($values as $k => $v)
        {
            $emailBody .= "$k = $v\n\n";
        }

        send_admin_mail("Donation form started", $emailBody);
        
        if (!$amount)
        {
            action_error("Please select a donation amount.");
        }        
        if (!$values['Name'])
        {
            action_error("Please enter your Full Name.");
        }
        if (!$values['phone'])
        {
            action_error("Please enter your Phone Number.");
        }
        if (!$values['Email'])
        {
            action_error("Please enter your Email Address.");
        }

        unset($values['_amount']);
        unset($values['_other_amount']);
        unset($values['Submit']);       

        echo view("page/submit_tci_donate_form", $values);    
    }
    
    function action_submit_login()
    {
        $username = get_input('username');
        $password = get_input("password");
        $next = get_input('next');
        $persistent = get_input("persistent", false);

        $result = false;
        if (!empty($username) && !empty($password))
        {
            if ($user = authenticate($username,$password))
            {
                $result = login($user, $persistent);
            }
        }

        if ($result)
        {
            system_message(sprintf(__('loginok'), $user->name));
            
            if ($next)
            {
                forward($next);
            }
            else
            {
                if (!$user->is_setup_complete())
                {
                    forward("org/new?step={$user->setup_state}");
                }
                else
                {
                    forward("{$user->get_url()}/dashboard");
                }
            }
        }
        else
        {
            Session::save_input();
            forward("pg/login?error=1&next=".urlencode($next));
        }
    }

    function action_logout()
    {
        logout();
        forward();
    }

    function action_dashboard()
    {
        $this->require_login();                
        forward(Session::get_loggedin_user()->get_url()."/dashboard");
    }

    function action_forgot_password()
    {
        $body = view("account/forms/forgotten_password",
            array('username' => get_input('username'))
        );

        $title = __('user:password:reset');
        $this->page_draw($title, view_layout("one_column_padded",
            view_title($title, array('org_only' => true)), $body));
    }

    function action_request_new_password()
    {
        $username = get_input('username');

        $user = get_user_by_username($username);
        if (!$user)
        {
            $user = User::query(true)->where('email = ?', $username)->get();
        }        
        
        if ($user)
        {
            if (!$user->email)
            {
                register_error(__('user:password:resetreq:no_email'));
                forward("page/contact");
            }
            
            $user->passwd_conf_code = substr(generate_random_cleartext_password(), 0, 24); // avoid making url too long for 1 line in email
            $user->save();

            global $CONFIG;
            $link = $CONFIG->url . "pg/password_reset?u={$user->guid}&c={$user->passwd_conf_code}";

            $email = sprintf(__('email:resetreq:body',$user->language), $user->name, $link);

            if ($user->notify(__('email:resetreq:subject',$user->language), $email))
            {
                system_message(__('user:password:resetreq:success'));
            }
            else
            {
                register_error(__('user:password:resetreq:fail'));
            }
        }
        else
        {
            action_error(sprintf(__('user:username:notfound'), $username));            
        }

        forward();
    }

    function action_password_reset()
    {
        global $CONFIG;

        $user_guid = get_input('u');
        $conf_code = get_input('c');
        
        $user = get_user($user_guid);

        if ($user && $user->passwd_conf_code && $user->passwd_conf_code == $conf_code)
        {                  
            $title = __("user:password:choose_new");
            $body = view_layout('one_column_padded', 
                view_title($title, array('org_only' => true)), 
                view("account/forms/reset_password", array('entity' => $user)));
            $this->page_draw($title, $body);
        }
        else
        {
            register_error(__('user:password:fail'));
            forward("pg/login");
        }        
    }
    
    function action_submit_password_reset()
    {
        $user_guid = get_input('u');
        $conf_code = get_input('c');        
        $user = get_user($user_guid);

        if ($user && $user->passwd_conf_code && $user->passwd_conf_code == $conf_code)
        {   
            $password = get_input('password');
            $password2 = get_input('password2');
            if ($password!="")
            {
                try
                {
                    validate_password($password);
                }
                catch (RegistrationException $ex)
                {
                    action_error($ex->getMessage());
                }

                if ($password == $password2)
                {
                    $user->set_password($password);
                    $user->passwd_conf_code = null;
                    $user->save();
                    system_message(__('user:password:success'));
                    login($user);
                    forward("pg/dashboard");
                }
                else
                {
                    action_error(__('user:password:fail:notsame'));
                }
            }
        }
        else
        {
            register_error(__('user:password:fail'));
            forward("pg/login");
        }            
    }


    function action_register()
    {
        $friend_guid = (int) get_input('friend_guid',0);
        $invitecode = get_input('invitecode');

        if (!Session::isloggedin())
        {
            $body = view_layout('one_column_padded', view_title(__("register")), view("account/forms/register", array('friend_guid' => $friend_guid, 'invitecode' => $invitecode)));
            $this->page_draw(__('register'), $body);
        }
        else
        {
            forward();
        }
    }

    function action_submit_registration()
    {
        $username = get_input('username');
        $password = get_input('password');
        $password2 = get_input('password2');
        $email = get_input('email');
        $name = get_input('name');

        if ($password != $password2)
        {
            action_error(__('create:passwords_differ'));
        }

        try
        {
            $new_user = register_user($username, $password, $name, $email);
            login($new_user, false);
            system_message(__("registerok"));
            forward("pg/dashboard/");
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_upload_frame()
    {
        $this->request->response = view('upload_frame');
    }

    function action_upload()
    {
        $this->require_login();

        $sizes = json_decode(get_input('sizes'));

        $filename = get_uploaded_filename('file');

        $json = upload_temp_images($filename, $sizes);

        if (get_input('iframe'))
        {
            Session::set('lastUpload', $json);
            forward("pg/upload_frame?swfupload=".urlencode(get_input('swfupload'))."&sizes=".urlencode(get_input('sizes')));
        }
        else
        {
            header("Content-Type: text/javascript");
            echo $json;
            exit();
        }
    }

    function action_send_feedback()
    {
        $message = get_input('message');

        if (!$message)
        {
            action_error(__('feedback:empty'));
        }

        $from = get_input('name');
        $email = get_input('email');

        $headers = array();

        if ($email && is_email_address($email))
        {
            $headers['Reply-To'] = mb_encode_mimeheader($email, "UTF-8", "B");
        }

        send_admin_mail("User feedback", "From: $from\n\nEmail: $email\n\n$message", $headers);
        system_message(__('feedback:sent'));
        forward("page/contact");
    }
    
    function action_large_img()
    {
        $owner_guid = get_input('owner');
        $group_name = get_input('group');
        
        $largeFile = UploadedFile::query()->where('owner_guid = ?', $owner_guid)->where('group_name = ?', $group_name)
            ->order_by('width desc')->get();            
            
        if ($largeFile)
        {
            echo "<html><body><img src='{$largeFile->get_url()}' width='{$largeFile->width}' height='{$largeFile->height}' /></body></html>";
        }
        else
        {
            not_found();
        }
    }
}