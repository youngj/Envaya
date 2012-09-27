<?php

class Input
{
    static $used_parameters = array();
    
    static function is_used_param($param)
    {
        return isset(self::$used_parameters[$param]);
    }
    
    static function set_used_param($param)
    {
        self::$used_parameters[$param] = true;
    }
    
    static function _get($variable, $default)
    {
        self::$used_parameters[$variable] = true;    
        return (isset($_REQUEST[$variable])) ? $_REQUEST[$variable] : $default;
    }
    
    static function get_int($variable, $default = 0)
    {
        $res = self::_get($variable, $default);
        if ($res !== null)
        {
            return (int)$res;
        }
        else
        {
            return $default;
        }
    }
    
    static function get_string($variable, $default = "")
    {
        $res = self::_get($variable, $default);
        if (is_string($res))
        {
            return $res;
        }
        else if ($res !== null)
        {
            return "$res";
        }
        else
        {
            return null;
        }
    }
    
    static function get_array($variable)
    {
        $res = self::_get($variable, null);
        
        if (is_array($res))
        {
            return $res;
        }
        else if ($res !== null)
        {
            return array($res);
        }
        else
        {
            return array();
        }
    }
    
    static function validate_strlen($str, $desc, $min, $max)
    {       
        if (!is_string($str))
        {
            throw new ValidationException("The $desc is invalid.");
        }
    
        $len = strlen($str);
    
        if ($min > 0 && !$len)
        {
            throw new ValidationException("Please enter the $desc.");
        }
        
        if ($len < $min)
        {
            throw new ValidationException("The $desc is too short (minimum length $min).");
        }

        if ($len > $max)
        {
            throw new ValidationException("The $desc is too long (maximum length $max).");
        }
        return $str;
    }
    
    static function get_bit_field_from_options($options)
    {
        $field = 0;
        foreach ($options as $item)
        {
            $field |= (int)$item;
        }
        return $field;
    }    

    static function yes_no_options()
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
    static function validate_security_token($require_session = false)
    {
        $token = Input::get_string('__token');
        $ts = Input::get_string('__ts');
        $session_id = Session::id();
        
        if (!$require_session && !$token && $ts && !$session_id)
        {
            // user does not have a session; expect an empty token
            return;
        }

        if ($token && $ts && $session_id)
        {
            // generate token, check with input and forward if invalid
            $generated_token = self::generate_security_token($ts);

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
    static function generate_security_token($timestamp)
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
    
    static function restore_value($name, $value, $track_dirty = false)
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
}