<?php
    /**
     * Action to request a new password.
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    require_once(dirname(dirname(__DIR__)) . "/engine/start.php");
    global $CONFIG;

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
    exit;
?>