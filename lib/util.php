<?php

    /**
    * Given a message shortcode, returns an appropriately translated full-text string
    *
    * @param string $message_key The short message code
    * @param string $language Optionally, the standard language code (defaults to the site default, then English)
    * @return string Either the translated string, or the original English string, or the message key
    */
    function __($message_key, $language_code = "") {

        if (!$language_code)
        {
            $language_code = Language::get_current_code();
        }
       
        return Language::get($language_code)->get_translation($message_key) 
            ?: Language::get('en')->get_translation($message_key) 
            ?: $message_key;
    }

    function url_with_param($url, $param, $value)
    {
        $parsed = parse_url($url);
        parse_str(@$parsed['query'],$query);
        if (is_null($value))
        {
            unset($query[$param]);
        }
        else
        {
            $query[$param] = $value;
        }

        $prefix = @$parsed['scheme'] ? $parsed['scheme']."://".$parsed['host'] : '';

        if (isset($parsed['port']))
        {
            $prefix .= ":".$parsed['port'];
        }
        
        $url = $prefix.$parsed['path'];
        if (sizeof($query) > 0)
        {
            return $url."?".http_build_query($query);
        }
        return $url;
    }
        
    /* 
     * Returns an absolute URL for a relative URI, using a given scheme.
     *
     * If $url is a relative URI, returns the corresponding absolute URL on this domain.
     * If $url is already an absolute URL, returns the URL converted to the given scheme.
     */
    function abs_url($url, $scheme = 'http')        
    {
        $scheme_end = strpos($url, "://");
        if ($scheme_end === false)
        {
            if ($url[0] != '/')
            {
                throw new InvalidParameterException("Invalid relative URI '$url'");
            }
            $domain = Config::get('domain');
            return "$scheme://$domain$url";
        }        
        else // convert URL to requested scheme
        {
            return $scheme.substr($url, $scheme_end);
        }
    }

    /* 
     * Returns absolute URL for a given URL/URI, using https if SSL is enabled on this server.
     */    
    function secure_url($url)
    {
        return abs_url($url, Config::get('ssl_enabled') ? 'https' : 'http');
    }
    
    function css_url($css_name)
    {
        if (Config::get('debug'))
        {
            return "/pg/css?name=$css_name&hash=" . md5(view("css/$css_name", 'default'));
        }
        else
        {
            return "/_media/css/$css_name.css?".Config::get("hash:css:$css_name");
        }        
    }     
                    
    function escape($val)
    {
        return htmlspecialchars($val, ENT_QUOTES, 'UTF-8');
    }
                    
    function get_constant_name($val, $prefix)
    {
        foreach (get_defined_constants() as $name => $value) 
        {
            if ($value == $val && strpos($name, $prefix) === 0)
            {
                return $name;
            }            
        }
        return null;
    }
    
    function constrain_size($size_arr, $max_arr)
    {
        $width = $size_arr[0];
        $height = $size_arr[1];

        $maxwidth = $max_arr[0];
        $maxheight = $max_arr[1];
     
        $newwidth = $width;
        $newheight = $height;     
     
        if ($width > $maxwidth)
        {
            $newheight = floor($height * ($maxwidth / $width));
            $newwidth = $maxwidth;
        }
        if ($newheight > $maxheight)
        {
            $newwidth = floor($newwidth * ($maxheight / $newheight));
            $newheight = $maxheight;
        }     
        
        return array($newwidth, $newheight);
    }
        
    /*
     * Generates a random string with hexadecimal characters of length $len <= 32
     */
    function generate_random_code($len = 32)
    {
        return substr(md5(microtime() . rand()), 0, $len);
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
            throw new ValidationException(sprintf(__('register:notemail'), $address));

        return $address;
    }

    function include_js($js_path)
    {
        if (Config::get('debug'))
        {
            $path = Engine::get_real_path("js/$js_path");
            if (!$path)
            {
                throw new InvalidArgumentException("js/$js_path does not exist");
            }
        }
        else
        {
            $path = Config::get('root') . "/www/_media/$js_path";
        }
        readfile($path);
    }