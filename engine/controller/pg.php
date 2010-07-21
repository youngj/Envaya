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
}