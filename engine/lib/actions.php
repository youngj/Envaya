<?php

    /**
     * Elgg actions
     * Allows system modules to specify actions
     *
     * @package Elgg
     * @subpackage Core
     * @author Curverider Ltd
     * @link http://elgg.org/
     */

    function record_user_action()
    {
        $userId = get_loggedin_userid();
        if ($userId)
        {
            set_last_action($userId);
        }
    }

    /**
     * Validate an action token, returning true if valid and false if not
     *
     * @return unknown
     */
    function validate_action_token($visibleerrors = true)
    {
        $token = get_input('__elgg_token');
        $ts = get_input('__elgg_ts');
        $session_id = Session::id();

        if (($token) && ($ts) && ($session_id))
        {
            // generate token, check with input and forward if invalid
            $generated_token = generate_action_token($ts);

            // Validate token
            if (strcmp($token, $generated_token)==0)
            {
                $hour = 60*60*24;
                $now = time();

                // Validate time to ensure its not crazy
                if (($ts>$now-$hour) && ($ts<$now+$hour))
                {
                    return true;
                }
                else if ($visibleerrors)
                    register_error(elgg_echo('actiongatekeeper:timeerror'));
            }
            else if ($visibleerrors)
            {
                register_error(elgg_echo('actiongatekeeper:timeerror'));
            }
        }
        else if ($visibleerrors)
        {
            register_error(elgg_echo('actiongatekeeper:missingfields'));
        }

        return false;
    }

    /**
     * Action gatekeeper.
     * This function verifies form input for security features (like a generated token), and forwards
     * the page if they are invalid.
     *
     * Place at the head of actions.
     */
    function action_gatekeeper()
    {
        if (validate_action_token())
            return true;

        forward();
        exit;
    }

    function action_error($msg)
    {
        register_error($msg);
        Session::saveInput();
        forward_to_referrer();
    }

    /**
     * Generate a token for the current user suitable for being placed in a hidden field in action forms.
     *
     * @param int $timestamp Unix timestamp
     */
    function generate_action_token($timestamp)
    {
        // Get input values
        $site_secret = get_site_secret();

        // Current session id
        $session_id = session_id();

        // Get user agent
        $ua = $_SERVER['HTTP_USER_AGENT'];

        // Session token
        $st = Session::get('__elgg_session');

        if (($site_secret) && ($session_id))
            return md5($site_secret.$timestamp.$session_id.$ua.$st);

        return false;
    }

    /**
     * Initialise the site secret.
     *
     */
    function init_site_secret()
    {
        $secret = md5(rand().microtime());
        if (datalist_set('__site_secret__', $secret))
            return $secret;

        return false;
    }

    /**
     * Retrieve the site secret.
     *
     */
    function get_site_secret()
    {
        $secret = datalist_get('__site_secret__');
        if (!$secret) $secret = init_site_secret();

        return $secret;
    }
