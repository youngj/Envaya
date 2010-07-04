<?php
    /**
     * Aggregate action for saving settings
     *
     * @package Elgg
     * @subpackage Core


     * @link http://elgg.org/
     */

    require_once(dirname(dirname(__DIR__)) . "/engine/start.php");
    global $CONFIG;

    gatekeeper();
    action_gatekeeper();

    $user_id = get_input('guid');
    $user = $user_id ?  get_entity($user_id) : get_loggedin_user();

    if (!$user || !$user->canEdit())
    {
        action_error(elgg_echo('org:cantedit'));
    }
    else
    {
        $name = get_input('name');

        // name
        if ($name)
        {
            if (strcmp($name, $user->name)!=0)
            {
                $user->name = $name;
                $user->save();
                system_message(elgg_echo('user:name:success'));
            }
        }
        else
        {
            action_error(elgg_echo('create:no_name'));
        }

        // password
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
                $user->salt = generate_random_cleartext_password(); // Reset the salt
                $user->password = generate_user_password($user, $password);
                $user->save();
                system_message(elgg_echo('user:password:success'));
            }
            else
            {
                action_error(elgg_echo('user:password:fail:notsame'));
            }
        }

        // language
        $language = get_input('language');
        if ($language && $language != $user->language)
        {
            $user->language = $language;
            $user->save();
            change_viewer_language($user->language);
            system_message(elgg_echo('user:language:success'));
        }

        // email
        $email = get_input('email');
        if ($email != $user->email)
        {
            $user->email = $email;
            $user->save();
            system_message(elgg_echo('user:email:success'));
        }

        forward($user->getURL());
    }

?>
