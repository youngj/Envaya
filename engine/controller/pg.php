<?php

class Controller_Pg extends Controller {

    function action_login()
    {
        set_context('login');

        $username = get_input('username');

        $title = elgg_echo("login");
        $body = elgg_view_layout('one_column_padded',
            elgg_view_title($title, array('org_only' => true)),
            elgg_view("account/forms/login", array('username' => $username)));

        $this->page_draw($title, $body);
    }

    function action_submit_login()
    {
        record_user_action();

        $username = get_input('username');
        $password = get_input("password");
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
            system_message(sprintf(elgg_echo('loginok'), $user->name));

            $forward_url = Session::get('last_forward_from');
            if ($forward_url)
            {
                Session::set('last_forward_from', null);
                forward($forward_url);
            }
            else
            {
                if (get_input('returntoreferer'))
                {
                    forward($_SERVER['HTTP_REFERER']);
                }
                else if (!$user->isSetupComplete())
                {
                    forward("org/new?step={$user->setup_state}");
                }
                else
                {
                    forward("pg/dashboard/");
                }
            }
        }
        else
        {
            Session::saveInput();

            $error_msg = elgg_echo('loginerror');
            // figure out why the login failed
            if (!empty($username) && !empty($password)) {
                // See if it exists and is disabled
                $access_status = access_get_show_hidden_status();
                access_show_hidden_entities(true);

                register_error(elgg_echo('loginerror'));
                forward("pg/login");

                access_show_hidden_entities($access_status);
            } else {
                register_error(elgg_echo('loginerror'));
            }
        }
    }

    function action_logout()
    {
        logout();
        forward();
    }

    function action_dashboard()
    {
        gatekeeper();

        set_theme('editor');
        set_context('editor');
        set_page_owner(get_loggedin_userid());

        $title = elgg_echo('dashboard');

        $intro_message = elgg_view('dashboard/blurb');

        $body = elgg_view_layout('one_column',
            elgg_view_title(elgg_echo("dashboard")),
            $intro_message
        );

        $this->page_draw($title, $body);
    }

    function action_forgot_password()
    {
        if (!isloggedin())
        {
            $body = elgg_view("account/forms/forgotten_password");

            $this->page_draw(elgg_echo('user:password:lost'), elgg_view_layout("one_column_padded",
                elgg_view_title(elgg_echo('user:password:lost'), array('org_only' => true)), $body));
        }
        else
        {
            forward();
        }
    }

    function action_request_new_password()
    {
        $username = get_input('username');

        $access_status = access_get_show_hidden_status();
        access_show_hidden_entities(true);
        $user = get_user_by_username($username);
        if ($user)
        {
            if (!$user->email)
            {
                register_error(elgg_echo('user:password:resetreq:no_email'));
                forward("page/contact");
            }
            if (send_new_password_request($user->guid))
            {
                system_message(elgg_echo('user:password:resetreq:success'));
            }
            else
            {
                register_error(elgg_echo('user:password:resetreq:fail'));
            }
        }
        else
            register_error(sprintf(elgg_echo('user:username:notfound'), $username));

        access_show_hidden_entities($access_status);
        forward();
    }

    function action_password_reset()
    {
        global $CONFIG;

        $user_guid = get_input('u');
        $code = get_input('c');

        access_show_hidden_entities(true);

        if (execute_new_password_request($user_guid, $code))
            system_message(elgg_echo('user:password:reset'));
        else
            register_error(elgg_echo('user:password:fail'));

        forward("pg/login");
        exit;
    }


    function action_register()
    {
        $friend_guid = (int) get_input('friend_guid',0);
        $invitecode = get_input('invitecode');

        if (!isloggedin())
        {
            $body = elgg_view_layout('one_column_padded', elgg_view_title(elgg_echo("register")), elgg_view("account/forms/register", array('friend_guid' => $friend_guid, 'invitecode' => $invitecode)));
            $this->page_draw(elgg_echo('register'), $body);
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
            action_error(elgg_echo('create:passwords_differ'));
        }

        try
        {
            $new_user = register_user($username, $password, $name, $email);
            login($new_user, false);
            system_message(elgg_echo("registerok"));
            forward("pg/dashboard/");
        }
        catch (RegistrationException $r)
        {
            action_error($r->getMessage());
        }
    }

    function action_upload_frame()
    {
        $this->request->response = elgg_view('upload_frame');
    }

    function action_upload()
    {
        gatekeeper();

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
            action_error(elgg_echo('feedback:empty'));
        }

        $from = get_input('name');
        $email = get_input('email');

        $headers = array();

        if ($email && is_email_address($email))
        {
            $headers['Reply-To'] = mb_encode_mimeheader($email, "UTF-8", "B");
        }

        send_admin_mail("User feedback", "From: $from\n\nEmail: $email\n\n$message", $headers);
        system_message(elgg_echo('feedback:sent'));
        forward("page/contact");
    }
}