<?php

    function is_email_address($address)
    {
        return preg_match('/^[A-Z0-9\._\%\+\-]+@[A-Z0-9\.\-]+$/i', $address, $matches);
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
     * @throws ValidationException on invalid
     */
    function validate_username($username)
    {
        if (strlen($username) < 3)
        {
            throw new ValidationException(__('registration:usernametooshort'));
        }

        if (preg_match('/[^a-zA-Z0-9\-\_]/', $username, $matches))
        {
            throw new ValidationException(sprintf(__('registration:invalidchars'), $username, $matches[0]));
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
            throw new ValidationException(sprintf(__('registration:usernamenotvalid'), $username));
        }

        return $username;
    }

    /**
     * Simple validation of a password.
     *
     * @param string $password
     * @throws ValidationException on invalid
     */
    function validate_password($password)
    {
        if (strlen($password)<6)
            throw new ValidationException(__('registration:passwordtooshort'));

        return $password;
    }

    /**
     * Simple validation of a email.
     *
     * @param string $address
     * @throws ValidationException on invalid
     * @return bool
     */
    function validate_email_address($address)
    {
        if ($address !== "" && !is_email_address($address))
            throw new ValidationException(__('registration:notemail'));

        return $address;
    }

    /**
     * Registers a user
     *
     * @param string $username The username of the new user
     * @param string $password The password
     * @param string $name The user's display name
     * @param string $email Their email address
     * @return int|false The new user's GUID; false on failure
     */
    function register_user($username, $password, $name, $email) 
    {
        $username = trim($username);
        $password = trim($password);
        $name = trim($name);

        if (empty($username) || empty($password) || empty($name)) 
        {
            throw new ValidationException(__('registerbad'));
        }				       

        validate_email_address($email);
        validate_password($password);
        validate_username($username);

        // Check to see if $username exists already
        if ($user = User::get_by_username($username)) {
            throw new ValidationException(__('registration:userexists'));
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->set_password($password);
        $user->save();

        return $user;
    }
    