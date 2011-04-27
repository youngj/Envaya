<?php

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
            throw new ValidationException(__('register:usernametooshort'));
        }

        if (preg_match('/[^\w\-]/', $username, $matches))
        {
            throw new ValidationException(sprintf(__('register:invalidchars'), $username, $matches[0]));
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
            throw new ValidationException(sprintf(__('register:usernamenotvalid'), $username));
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
            throw new ValidationException(__('register:passwordtooshort'));

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
        if ($address !== "" && !preg_match('/^[A-Z0-9\._\%\+\-]+@[A-Z0-9\.\-]+$/i', $address))
            throw new ValidationException(__('register:notemail'));

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
            throw new ValidationException(__('register:bad'));
        }				       

        validate_email_address($email);
        validate_password($password);
        validate_username($username);

        // Check to see if $username exists already
        if ($user = User::get_by_username($username)) {
            throw new ValidationException(__('register:userexists'));
        }

        $user = new User();
        $user->username = $username;
        $user->email = $email;
        $user->name = $name;
        $user->set_password($password);
        $user->save();

        return $user;
    }
    
    function get_email_fingerprint($email)
    {
        return substr(md5($email . Config::get('site_secret') . "-email"), 0,15);
    }    
