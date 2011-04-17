<?php

/*
 * Represents a user account on Envaya, each with a unique username.
 *
 * (The Organization subclass is the most common type of user.)
 */
class User extends Entity
{
    static $table_name = 'users';

    static $table_attributes = array(        
        'subtype' => 0,
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
        'theme' => '',
        'email_code' => null,
        'setup_state' => 5,
        'icons_json' => null,
        'header_json' => null,
        'last_notify_time' => null,
        'last_action' => 0,
        'notifications' => 15,
    );

    protected function get_table_attributes()
    {
        return array_merge(parent::get_table_attributes(), array(
            'subtype' => static::get_subtype_id(),
        ));
    }    
        
    
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
        return Config::get('url') . $this->username;
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
                'url' => view('output/static_map_url', array(
                    'lat' => $this->latitude, 
                    'long' => $this->longitude, 
                    'zoom' => 6, 
                    'width' => 100, 
                    'height' => 100
                )),
                'width' => 100,
                'height' => 100
            );
        } 
        else
        {
            return array(
                'url' => Config::get('url')."_graphics/defaultmedium.gif",
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
        $this->save();
    }

    public function has_custom_header()
    {
        return !!$this->header_json;
    }
    
    public function get_header_props()
    {
        return json_decode($this->header_json, true);
    }    
    
    public function set_header($imageFiles)
    {
        if (!$imageFiles)
        {
            $this->header_json = null;
        }
        else
        {               
            $this->header_json = json_encode($imageFiles[0]->js_properties());
        }
        $this->save();
    }

    static function get_header_sizes()
    {
        return array(
            'large' => '700x150',
        );
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
       
    function get_blog_dates()
    {   
        $sql = "SELECT guid, time_created from news_updates WHERE status=1 AND container_guid=? ORDER BY guid ASC";
        return Database::get_rows($sql, array($this->guid));
    }

    public function is_approved()
    {
        return $this->approval > 0 || $this->admin;
    }

    public function set_password($password)
    {
        $this->salt = generate_random_code(8);
        $this->password = $this->generate_password($password);
    }

    static function _new($row)
    {
        $cls = EntityRegistry::get_subtype_class($row->subtype);
        return new $cls($row);
    }
    
    static function query()
    {
        return new Query_SelectUser(static::$table_name, get_called_class());
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
        $fails = (int)$this->login_failures;

        if ($fails) 
        {
            for ($n=1; $n <= $fails; $n++)
                $this->set("login_failure_$n", null);

            $this->login_failures = null;
        }
    }
    
    function check_rate_limit_exceeded()
    {
        $limit = 50;
        $fails = (int)$this->login_failures;
        if ($fails >= $limit)
        {
            $cnt = 0;
            $time = time();
            for ($n=$fails; $n>0; $n--)
            {
                $f = $this->get("login_failure_$n");
                if ($f > $time - (60*5))
                    $cnt++;

                if ($cnt==$limit) return true; // Limit reached
            }
        }

        return false;
    }
    
    function log_login_failure()
    {
        $fails = (int)$this->login_failures;
        $fails++;

        $this->login_failures = $fails;
        $this->set("login_failure_$fails", time());
    }        
    
    function generate_password($password)
    {
        return md5($password . $this->salt);
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
    
    static function get_cache_key_for_username($username)
    {
        return make_cache_key("guid_for_username", $username);
    }

    static function get_by_username($username)
    {
        if (!$username)
            return null;
    
        /*
         * some people might try entering their email address as their username,
         * so if the username has an @, we try that (@ is not allowed in usernames)
         */ 
        if (strpos($username,'@') !== false)
        {
            // if there are multiple accounts with the same email address, we just return one arbitrarily
            return User::query()->where('email = ?', $username)->get();
        }

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

        return static::get_by_guid($guid);
    }    
}
