<?php

/*
 * Represents a user account on Envaya, each with a unique username.
 *
 * (The Organization subclass is the most common type of user.)
 */
class User extends Entity
{
    static $table_name = 'users';
    static $query_class = 'Query_SelectUser';

    static $table_attributes = array(        
        'subtype_id' => '',
        'name' => '',
        'username' => '',
        'password' => '',
        'salt' => '',
        'email' => '',
        'phone_number' => '',
        'language' => '',
        'admin' => 0,
        'approval' => 0,
        'latitude' => null,
        'longitude' => null,
        'timezone_id' => '',
        'city' => '',
        'region' => '',
        'country' => '',
        'email_code' => null,
        'setup_state' => 5,
        'icons_json' => null,
        'design_json' => null,
        'last_notify_time' => null,
        'last_action' => 0,
        'notifications' => 15,
    );
    
    public function get_feed_names()
    {
        return array(
            FeedItem::make_feed_name(array()), // global feed
            FeedItem::make_feed_name(array('user' => $this->guid))
        );
    }
    
    public function is_setup_complete()
    {
        return true;
    }

    public function get_name_for_email($email = null)
    {
        $name = mb_encode_mimeheader($this->name, "UTF-8", "B");
        if (!$email)
        {
            $email = $this->email ?: Config::get('email_from');
        }
        return "\"$name\" <$email>";
    }

    public function get_title()
    {
        return $this->name;
    }

    public function get_url()
    {
        return abs_url("/{$this->username}");
    }

    public function get_icon_props($size = '')
    {
        $props = $this->get_icon_props_raw($size);        
        $sizes = $this->get_icon_sizes();
        $max_size = explode('x', @$sizes[$size] ?: $size);    
        $new_size = constrain_size(array($props['width'], $props['height']), $max_size);
        
        return array(
            'url' => $props['url'],
            'width' => $new_size[0],
            'height' => $new_size[1],
        );
    }
    
    private function get_icon_props_raw($size = '')
    {        
        if ($this->icons_json)
        {
            $all_sizes = json_decode($this->icons_json, true);
            foreach ($all_sizes as $props)
            {
                if ($props['size'] == $size)
                {
                    return $props;
                }
            }
            return $all_sizes[0];
        }
        else if ($this->has_lat_long())
        {   
            return array(
                'url' => Geography::get_static_map_url(array(
                    'lat' => $this->latitude, 
                    'long' => $this->longitude, 
                    'zoom' => 6, 
                    'width' => 100, 
                    'height' => 100,
                    'pin' => true
                )),
                'width' => 100,
                'height' => 100
            );
        } 
        else
        {
            return array(
                'url' => abs_url("/_media/images/defaultmedium.gif"),
                'width' => 100,
                'height' => 100
            );
        }    
    }

    public function has_custom_icon()
    {
        return !!$this->icons_json;
    }        
    
    public function get_icon($size = 'medium')
    {
        $props = $this->get_icon_props_raw($size);
        return $props['url'];
    }

    static function get_icon_sizes()
    {
        return array(
            'small' => '60x40',
            'medium' => '150x100',
            'large' => '540x540',
        );
    }

    public function set_icon($imageFiles)
    {
        if (!$imageFiles)
        {
            $this->icons_json = null;
        }
        else
        {
            $this->icons_json = UploadedFile::json_encode_array($imageFiles);
        }
    }

    private $design_settings;
    
    public function get_design_settings()
    {
        if (!$this->design_settings)
        {
            $this->design_settings = json_decode($this->design_json, true) ?: array();
        }
        return $this->design_settings;
    }
    
    public function get_design_setting($name)
    {
        $settings = $this->get_design_settings();
        return @$settings[$name];
    }
    
    public function set_design_setting($name, $value)
    {
        $settings = $this->get_design_settings();
        $settings[$name] = $value;
        $this->design_settings = $settings;
        $this->design_json = json_encode($settings);
    }        

    function has_lat_long()
    {
        return $this->latitude || $this->longitude;
    }
        
    /**
     * Set latitude and longitude tags for a given entity.
     *
     * @param float $lat
     * @param float $long
     */
    public function set_lat_long($lat, $long)
    {
        if ($lat != $this->latitude || $long != $this->longitude)
        {
            $this->latitude = $lat;
            $this->longitude = $long;
            $this->timezone_id = '';
        }
        return true;
    }

    public function get_latitude() { return $this->latitude; }
    public function get_longitude() { return $this->longitude; }

    function query_feed_items()
    {
        $feedName = FeedItem::make_feed_name(array('user' => $this->guid));
        return FeedItem::query_by_feed_name($feedName);
    }      

    public function is_approved()
    {
        return $this->approval > 0 || $this->admin;
    }

    public function set_password($password)
    {
        $this->password = generate_password_hash($password);
    }
    
    public function js_properties()
    {
        return array(
            'guid' => $this->guid,
            'username' => $this->username,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'url' => $this->get_url()
        );
    }
    
    function reset_login_failure_count()
    {
        $fails = (int)$this->get_metadata('login_failures');

        if ($fails) 
        {
            for ($n=1; $n <= $fails; $n++)
                $this->set_metadata("login_failure_$n", null);

            $this->set_metadata('login_failures', null);
        }
    }
    
    function check_rate_limit_exceeded()
    {
        $limit = 50;
        $fails = (int)$this->get_metadata('login_failures');
        if ($fails >= $limit)
        {
            $cnt = 0;
            $time = time();
            for ($n=$fails; $n>0; $n--)
            {
                $f = $this->get_metadata("login_failure_$n");
                if ($f > $time - (60*5))
                    $cnt++;

                if ($cnt==$limit) return true; // Limit reached
            }
        }

        return false;
    }
    
    function log_login_failure()
    {
        $fails = (int)$this->get_metadata('login_failures');
        $fails++;

        $this->set_metadata('login_failures', $fails);
        $this->set_metadata("login_failure_$fails", time());
    }           
    
    function has_password($password)
    {
        if ($this->password[0] != '$') // migrate old md5 password+salt from elgg to new bcrypt passwords
        {
            if ($this->password == md5($password . $this->salt))
            {
                $this->password = generate_password_hash($password);
                $this->salt = '';
                $this->save();
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            return ($this->password == crypt($password, $this->password));
        }
    }
    	
	function is_notification_enabled($notification)
	{
		return ($this->notifications & $notification) != 0;
	}
    
    function set_notification_enabled($notification, $enabled = true)
    {
        if ($enabled)
        {
            $this->notifications |= $notification;
        }
        else
        {
            $this->notifications &= ~$notification;
        }   
    }
	
	function get_notifications()
	{
		$notifications = array();
		
		foreach (Notification::all() as $n)
		{
			if ($this->is_notification_enabled($n))
			{
				$notifications[] = $n;
			}
		}		
		return $notifications;
	}
    
    function get_timezone_id()
    {
        if (!$this->timezone_id) 
        {
            if (!$this->has_lat_long())
            {
                return Config::get('default_timezone');
            }
        
            try 
            {
                $geonames = Zend::geonames();
                $res = $geonames->timezone(array(
                    'lat' => $this->latitude,
                    'lng' => $this->longitude
                ));
                
                $this->timezone_id = @$res['timezoneId'] ?: Config::get('default_timezone');
            }
            catch (Zend_Http_Client_Adapter_Exception $ex)
            {
                $this->timezone_id = Config::get('default_timezone');            
            }
            catch (Bgy_Service_Geonames_Exception $ex)
            {
                $this->timezone_id = Config::get('default_timezone');
            }
            
            $this->save();
        }
        return $this->timezone_id;
    }
    
    function get_email_settings_url()
    {
        $code = User::get_email_fingerprint($this->email);
        return abs_url("/pg/email_settings?e=".urlencode($this->email)."&c={$code}");    
    }
    
    static function get_cache_key_for_username($username)
    {
        return make_cache_key("guid_for_username", $username);
    }

    static function get_by_username($username, $show_disabled = false)
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
        $cacheKey = User::get_cache_key_for_username($username);

        $guid = $cache->get($cacheKey);
        if (!$guid)
        {
            $guidRow = Database::get_row("SELECT guid from users where username=?", array($username));
            if (!$guidRow)
            {
                return null;
            }

            $guid = $guidRow->guid;
            $cache->set($cacheKey, $guid);
        }

        return static::get_by_guid($guid, $show_disabled);
    }    

    static function get_email_fingerprint($email)
    {
        return substr(md5($email . Config::get('site_secret') . "-email"), 0,15);
    }        
    
    static function validate_username($username, $min_length = 3)
    {
        if (strlen($username) < $min_length)
        {
            throw new ValidationException(strtr(__('register:usernametooshort'), array('{min}' => $min_length)));
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

    static function validate_password($password, $password2, $name, $username, $min_length = 6)
    {
        if (strlen($password) < $min_length)
        {
            throw new ValidationException(strtr(__('register:passwordtooshort'), array('{min}' => $min_length)));
        }

        $lpassword = strtolower($password);
        $lusername = strtolower($username);
        $lname = strtolower($name);
                
        if (strpos($lname, $lpassword) !== FALSE || strpos($lusername, $lpassword) !== FALSE)
        {
            throw new ValidationException(__('register:password_too_easy'));
        }

        if (strcmp($password, $password2) != 0)
        {
            throw new ValidationException(__('register:passwords_differ'));
        }        
     
        return $password;
    }    
    
    public function get_country_text()
    {
        if ($this->country)
        {
            return __("country:{$this->country}");
        }
        else
        {
            return '';
        }
    }    
    
    public function get_location_text($includeRegion = true)
    {
        $res = '';

        if ($this->city)
        {
            $res .= "{$this->city}, ";
        }
        if ($this->region && $includeRegion)
        {
            $regionText = __($this->region);

            if ($regionText != $this->city)
            {
                $res .= "$regionText, ";
            }
        }
        $res .= $this->get_country_text();

        return $res;
    }
        
    function geocode_lat_long()
    {
        $latlong = Geography::geocode($this->get_location_text());
        if ($latlong)
        {
            $this->set_lat_long($latlong['lat'], $latlong['long']);
            return true;
        }    
        return false;
    }
    
    protected $phone_numbers;
    protected $phone_numbers_dirty = false;

    function query_phone_numbers()
    {
        return UserPhoneNumber::query()->where('user_guid = ?', $this->guid);
    }
        
    function set_phone_number($phone_number_str)
    {
        $phone_numbers = PhoneNumber::canonicalize_multi($phone_number_str, $this->country);

        $this->phone_number = $phone_number_str;
        $this->phone_numbers = array();        
        foreach ($phone_numbers as $phone_number)
        {
            if ($this->guid)
            {   
                $user_phone_number = $this->query_phone_numbers()
                    ->where('phone_number = ?', $phone_number)
                    ->get();
            }
            else
            {
                $user_phone_number = null;
            }
            if (!$user_phone_number)
            {
                $user_phone_number = new UserPhoneNumber();
                $user_phone_number->user_guid = $this->guid;
                $user_phone_number->phone_number = $phone_number;
            }            
            $this->phone_numbers[] = $user_phone_number;
        }        
        $this->phone_numbers_dirty = true;
    }
    
    function save()
    {
        $res = parent::save();
        
        if ($this->phone_numbers_dirty)
        {            
            $newIds = array_map(function($op) { return $op->id; }, $this->phone_numbers);        
            
            foreach ($this->query_phone_numbers()
                ->where('confirmed = 0')
                ->where_not_in('id', $newIds)
                ->filter() 
                    as $oldPhoneNumber)
            {
                $oldPhoneNumber->delete();
            }
            
            foreach ($this->phone_numbers as $phone_number)
            {
                $phone_number->user_guid = $this->guid;
                $phone_number->save();
            }        
            $this->phone_numbers_dirty = false;
        }    
        return $res;
    }

    function set_password_reset_code($code)
    {
        if ($code != null)
        {
            $this->set_metadata('password_reset_code', generate_password_hash($code));
            $this->set_metadata('password_reset_time', time());        
        }
        else
        {
            $this->set_metadata('password_reset_code', null);
            $this->set_metadata('password_reset_time', null);                
        }        
    }    
    
    function has_password_reset_code($code)
    {        
        $code_time = $this->get_metadata('password_reset_time');    
        if (!$code_time || time() - $code_time > 60*60*24*7)
        {
            return false;
        }
        
        $code_hash = $this->get_metadata('password_reset_code');    
        
        return ($code_hash && $code && $code_hash == crypt(strtoupper($code), $code_hash));
    }    
}
