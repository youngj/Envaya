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

    function get_users_by_email($email)
    {
        return User::query()->where('email = ?', $email)->filter();
    }

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

        // Check to see if we've registered the first admin yet.
        // If not, this is the first admin user!
        $admin = Datalist::get('admin_registered');

        // Otherwise ...
        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->salt = generate_random_cleartext_password(); // Note salt generated before password!
        $user->password = $user->generate_password($password);
        $user->owner_guid = 0; // Users aren't owned by anyone, even if they are admin created.
        $user->container_guid = 0; // Users aren't contained by anyone, even if they are admin created.
        $user->save();

        if (!$admin) 
        {
            $user->admin = true;
            Datalist::set('admin_registered',1);
        }

        return $user;
    }

