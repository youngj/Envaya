<?php
    class QueryString
    {
        static $used_parameters = array();
        
        static function is_used_param($param)
        {
            return isset(static::$used_parameters[$param]);
        }
        
        static function set_used_param($param)
        {
            static::$used_parameters[$param] = true;
        }
    }

    function get_input($variable, $default = "")
    {
        QueryString::$used_parameters[$variable] = true;
    
        return (isset($_REQUEST[$variable])) ? $_REQUEST[$variable] : $default;
    }

    function get_input_array($variable)
    {
        $res = get_input($variable);
        if (is_array($res))
        {
            return $res;
        }
        else if ($res != null)
        {
            return array($res);
        }
        else
        {
            return array();
        }
    }

    function get_bit_field_from_options($options)
    {
        $field = 0;
        foreach ($options as $item)
        {
            $field |= (int)$item;
        }
        return $field;
    }    

    function restore_input($name, $value, $track_dirty = false)
    {
        if (isset($_POST[$name]))
        {
            return $_POST[$name];
        }

        $prevInput = Session::get('input');
        
        if ($prevInput)
        {
            if (isset($prevInput[$name]))
            {
                $val = $prevInput[$name];
                unset($prevInput[$name]);
                Session::set('input', $prevInput);
                
                if ($track_dirty && $val != $value)
                {
                    PageContext::set_dirty(true);
                }
                
                return $val;
            }
        }
        return $value;
    }

    function yes_no_options()
    {
        return array(
            'yes' => __('yes'),
            'no' => __('no'),
        );
    }
    
    /**
     * Validate an CSRF token, returning true if valid and false if not
     *
     * @return unknown
     */
    function validate_security_token($require_session = false)
    {
        $token = get_input('__token');
        $ts = get_input('__ts');
        $session_id = Session::id();
        
        if (!$require_session && !$token && $ts && !$session_id)
        {
            // user does not have a session; expect an empty token
            return;
        }

        if ($token && $ts && $session_id)
        {
            // generate token, check with input and forward if invalid
            $generated_token = generate_security_token($ts);

            // Validate token
            if (strcmp($token, $generated_token)==0)
            {
                $day = 60*60*24;
                $now = timestamp();

                // Validate time to ensure its not crazy
                if (($ts>$now-$day) && ($ts<$now+$day))
                {
                    return;
                }
            }
        }
        throw new ValidationException(__('page:expired'));        
    }

    /**
     * Generate a CSRF token for the current user suitable for being placed in a hidden field in action forms.
     *
     * @param int $timestamp Unix timestamp
     */
    function generate_security_token($timestamp)
    {
        // Get input values
        $site_secret = Config::get('site_secret');

        // Current session id
        $session_id = Session::id();

        // Get user agent
        $ua = $_SERVER['HTTP_USER_AGENT'];

        // Session token
        $st = Session::get('__elgg_session');

        if (($site_secret) && ($session_id))
        {
            return md5($site_secret.$timestamp.$session_id.$ua.$st);
        }

        return false;
    }            