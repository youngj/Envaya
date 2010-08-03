<?php

    /**
     * Elgg users
     * Functions to manage multiple or single users in an Elgg install
     *
     * @package Elgg
     * @subpackage Core

     * @author Curverider Ltd

     * @link http://elgg.org/
     */

    $CODE_TO_GUID_MAP_CACHE = array();

    /**
     * Get a user object from a GUID.
     *
     * This function returns an ElggUser from a given GUID.
     * @param int $guid The GUID
     * @return ElggUser|false
     */
    function get_user($guid)
    {
        if (!empty($guid)) // Fixes "Exception thrown without stack frame" when db_select fails
            $result = get_entity($guid);

        if ((!empty($result)) && (!($result instanceof ElggUser)))
            //throw new InvalidClassException(sprintf(__('InvalidClassException:NotValidElggStar'), $guid, 'ElggUser'));
            return false;

        if (!empty($result))
            return $result;

        return false;
    }

    function get_cache_key_for_username($username)
    {
		global $CONFIG;
        return make_cache_key("guid_for_username", $username);
    }

    /**
     * Get user by username
     *
     * @param string $username The user's username
     * @return ElggUser|false Depending on success
     */
    function get_user_by_username($username)
    {
        $cache = get_cache();
        $cacheKey = get_cache_key_for_username($username);

        $guid = $cache->get($cacheKey);
        if (!$guid)
        {
            $guidRow = get_data_row("SELECT guid from users_entity where username=?", array($username));
            if (!$guidRow)
            {
                return null;
            }

            $guid = $guidRow->guid;
            $cache->set($cacheKey, $guid);
        }

        return get_entity($guid);
    }

    /**
     * Get an array of users from their
     *
     * @param string $email Email address.
     * @return Array of users
     */
    function get_users_by_email($email)
    {
        $access = get_access_sql_suffix('e');

        return array_map('entity_row_to_elggstar', get_data(
            "SELECT e.* from entities e join users_entity u on e.guid=u.guid where email=? and $access",
            array($email)
        ));
    }

    /**
     * A function that returns a maximum of $limit users who have done something within the last
     * $seconds seconds.
     *
     * @param int $seconds Number of seconds (default 600 = 10min)
     * @param int $limit Limit, default 10.
     * @param int $offset Offset, defualt 0.
     */
    function find_active_users($seconds = 600, $limit = 10, $offset = 0)
    {
        global $CONFIG;

        $seconds = (int)$seconds;
        $limit = (int)$limit;
        $offset = (int)$offset;

        $time = time() - $seconds;

        $access = get_access_sql_suffix("e");

        $query = "SELECT distinct e.* from entities e join users_entity u on e.guid = u.guid where u.last_action >= ? and $access order by u.last_action desc limit {$offset},{$limit}";

        return array_map('entity_row_to_elggstar', get_data($query, array($time)));
    }

    /**
     * Generate and send a password request email to a given user's registered email address.
     *
     * @param int $user_guid
     */
    function send_new_password_request($user_guid)
    {
        global $CONFIG;

        $user_guid = (int)$user_guid;

        $user = get_entity($user_guid);
        if ($user && $user->email)
        {
            // generate code
            $code = generate_random_cleartext_password();
            set_private_setting($user_guid, 'passwd_conf_code', $code);

            // generate link
            $link = $CONFIG->url . "pg/password_reset?u=$user_guid&c=$code";

            // generate email
            $email = sprintf(__('email:resetreq:body',$user->language), $user->name, $link);

            return notify_user($user->guid, $CONFIG->site_guid,
                __('email:resetreq:subject',$user->language), $email, NULL, 'email');

        }

        return false;
    }

    /**
     * Validate and execute a password reset for a user.
     *
     * @param int $user_guid The user id
     * @param string $conf_code Confirmation code as sent in the request email.
     */
    function execute_new_password_request($user_guid, $conf_code)
    {
        global $CONFIG;

        $user_guid = (int)$user_guid;

        $user = get_entity($user_guid);

        if (($user) && (get_private_setting($user_guid, 'passwd_conf_code') == $conf_code))
        {
            $password = generate_random_cleartext_password();

            $user->setPassword($password);
            $user->save();

            remove_private_setting($user_guid, 'passwd_conf_code');

            $email = sprintf(__('email:resetpassword:body',$user->language), $user->name, $password);

            notify_user($user->guid, $CONFIG->site_guid, __('email:resetpassword:subject',$user->language), $email, NULL, 'email');
            return true;
        }

        return false;
    }

    /**
     * Validates an email address.
     *
     * @param string $address Email address.
     * @return bool
     */
    function is_email_address($address)
    {
        return preg_match('/^[A-Z0-9\._\%\+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,4}$/i', $address, $matches);
    }

	
	function get_username_for_host($host)
	{
		$cacheKey = make_cache_key('username_for_host', $host);
        $cache = get_cache();
        $cachedUsername = $cache->get($cacheKey);
        
        if ($cachedUsername !== null)
        {
            return $cachedUsername;
        }
        else
        {
            $row = get_data_row('SELECT * FROM org_domain_names WHERE domain_name = ?', array($host));
            if ($row)
            {
                $user = get_entity($row->guid);
                if ($user)
                {
                    $cache->set($cacheKey, $user->username);
                    return $user->username;
                }
            }
            $cache->set($cacheKey, '');
            return '';
        }
	}	
	
    /**
     * Simple function that will generate a random clear text password suitable for feeding into generate_user_password().
     *
     * @see generate_user_password
     * @return string
     */
    function generate_random_cleartext_password()
    {
        return substr(md5(microtime() . rand()), 0, 8);
    }

    /**
     * Generate a password for a user, currently uses MD5.
     *
     * Later may introduce salting etc.
     *
     * @param ElggUser $user The user this is being generated for.
     * @param string $password Password in clear text
     */
    function generate_user_password(ElggUser $user, $password)
    {
        return md5($password . $user->salt);
    }

    /**
     * Simple function which ensures that a username contains only valid characters.
     *
     * This should only permit chars that are valid on the file system as well.
     *
     * @param string $username
     * @throws RegistrationException on invalid
     */
    function validate_username($username)
    {
        global $CONFIG;

        if (strlen($username) < 3)
        {
            throw new RegistrationException(__('registration:usernametooshort'));
        }

        if (preg_match('/[^a-zA-Z0-9\-\_]/', $username, $matches))
        {
            throw new RegistrationException(sprintf(__('registration:invalidchars'), $username, $matches[0]));
        }

        $lower = strtolower($username);

        $badUsernames = array(
            'pg',
            'org',
            'page',
            'action',
            'account',
            'mod',
            'search',
            'admin',
            'dashboard',
            'engine'
        );

        if (in_array($lower, $badUsernames) || $username[0] == "_")
        {
            throw new RegistrationException(sprintf(__('registration:usernamenotvalid'), $username));
        }

        return true;
    }

    /**
     * Simple validation of a password.
     *
     * @param string $password
     * @throws RegistrationException on invalid
     */
    function validate_password($password)
    {
        if (strlen($password)<6)
            throw new RegistrationException(__('registration:passwordtooshort'));

        return true;
    }

    /**
     * Simple validation of a email.
     *
     * @param string $address
     * @throws RegistrationException on invalid
     * @return bool
     */
    function validate_email_address($address)
    {
        if ($address !== "" && !is_email_address($address))
            throw new RegistrationException(__('registration:notemail'));

        return true;
    }

    /**
     * Registers a user, returning false if the username already exists
     *
     * @param string $username The username of the new user
     * @param string $password The password
     * @param string $name The user's display name
     * @param string $email Their email address
     * @param bool $allow_multiple_emails Allow the same email address to be registered multiple times?
     * @param int $friend_guid Optionally, GUID of a user this user will friend once fully registered
     * @return int|false The new user's GUID; false on failure
     */
    function register_user($username, $password, $name, $email, $allow_multiple_emails = false, $friend_guid = 0, $invitecode = '') 
    {
        // Load the configuration
        global $CONFIG;

        $username = trim($username);
        $password = trim($password);
        $name = trim($name);
        $email = trim($email);

        // A little sanity checking
        if (empty($username) || empty($password) || empty($name) || empty($email)) 
        {
            throw new RegistrationException(__('registerbad'));
        }				
        
        // See if it exists and is disabled
        $access_status = access_get_show_hidden_status();
        access_show_hidden_entities(true);

        validate_email_address($email);
        validate_password($password);
        validate_username($username);

        // Check to see if $username exists already
        if ($user = get_user_by_username($username)) {
            throw new RegistrationException(__('registration:userexists'));
        }

        // If we're not allowed multiple emails then see if this address has been used before
        if ((!$allow_multiple_emails) && (sizeof(get_users_by_email($email)) > 0))
        {
            throw new RegistrationException(__('registration:dupeemail'));
        }

        access_show_hidden_entities($access_status);

        // Check to see if we've registered the first admin yet.
        // If not, this is the first admin user!
        $admin = datalist_get('admin_registered');

        // Otherwise ...
        $user = new ElggUser();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->salt = generate_random_cleartext_password(); // Note salt generated before password!
        $user->password = generate_user_password($user, $password);
        $user->owner_guid = 0; // Users aren't owned by anyone, even if they are admin created.
        $user->container_guid = 0; // Users aren't contained by anyone, even if they are admin created.
        $user->save();

        global $registering_admin;
        if (!$admin) {
            $user->admin = true;
            datalist_set('admin_registered',1);
            $registering_admin = true;
        } else {
            $registering_admin = false;
        }

        // Turn on email notifications by default
        set_user_notification_setting($user->getGUID(), 'email', true);

        return $user;
    }

    /**
     * Sets the last action time of the given user to right now.
     *
     * @param int $user_guid The user GUID
     */
    function set_last_action($user_guid)
    {
        execute_delayed_write_query("UPDATE users_entity set prev_last_action = last_action, last_action = ? where guid = ?", array(time(), $user_guid));
    }

    /**
     * Sets the last logon time of the given user to right now.
     *
     * @param int $user_guid The user GUID
     */
    function set_last_login($user_guid)
    {
        execute_delayed_write_query("UPDATE users_entity set prev_last_login = last_login, last_login = ? where guid = ?", array(time(), $user_guid));
    }

