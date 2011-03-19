<?php

    function get_user($guid)
    {
        $result = get_entity($guid);

        if (!$result || !$result instanceof User)
            return null;

        return $result;
    }

    function get_cache_key_for_username($username)
    {
        return make_cache_key("guid_for_username", $username);
    }

    function get_user_by_username($username)
    {
        if (!$username)
            return null;
    
        /*
         * some people might try entering http://envaya.org/foo as the username when logging in,
         * so we just ignore everything before the last slash (/ is not allowed in usernames)
         */
        $last_slash = strrpos($username, '/');
        if ($last_slash !== FALSE)
        {
            $username = substr($username, $last_slash + 1);
        }
    
        $cache = get_cache();
        $cacheKey = get_cache_key_for_username($username);

        $guid = $cache->get($cacheKey);
        if (!$guid)
        {
            $guidRow = Database::get_row("SELECT guid from users_entity where username=?", array($username));
            if (!$guidRow)
            {
                return null;
            }

            $guid = $guidRow->guid;
            $cache->set($cacheKey, $guid);
        }

        return get_entity($guid);
    }

    function is_email_address($address)
    {
        return preg_match('/^[A-Z0-9\._\%\+\-]+@[A-Z0-9\.\-]+\.[A-Z]{2,4}$/i', $address, $matches);
    }
	
    /**
     * Simple function that will generate a random clear text password suitable for feeding into generate_user_password().
     *
     * @see generate_user_password
     * @return string
     */
    function generate_random_cleartext_password()
    {
        return md5(microtime() . rand());
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

        return $address;
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
        $username = trim($username);
        $password = trim($password);
        $name = trim($name);

        if (empty($username) || empty($password) || empty($name)) 
        {
            throw new RegistrationException(__('registerbad'));
        }				       

        validate_email_address($email);
        validate_password($password);
        validate_username($username);

        // Check to see if $username exists already
        if ($user = get_user_by_username($username)) {
            throw new RegistrationException(__('registration:userexists'));
        }

        // If we're not allowed multiple emails then see if this address has been used before
        if ((!$allow_multiple_emails) && (User::query(true)->where('email = ?', $email)->count() > 0))
        {
            throw new RegistrationException(__('registration:dupeemail'));
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->set_password($password);
        $user->owner_guid = 0; // Users aren't owned by anyone, even if they are admin created.
        $user->container_guid = 0; // Users aren't contained by anyone, even if they are admin created.
        $user->save();

        return $user;
    }

