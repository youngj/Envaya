<?php

/*
 * Represents a user account on Envaya, each with a unique username.
 *
 * (The Organization subclass is the most common type of user.)
 */
class User extends Entity
{
    static $table_name = 'users_entity';

    static $table_attributes = array(
            'name' => '',
            'username' => '',
            'password' => '',
            'salt' => '',
            'email' => '',
            'language' => '',
            'code' => '',
            'banned' => 'no',
            'admin' => 0,
            'approval' => 0,
            'latitude' => null,
            'longitude' => null,
            'city' => '',
            'region' => '',
            'country' => '',
            'theme' => '',
            'email_code' => null,
            'setup_state' => 5,
            'custom_icon' => 0,
            'custom_header' => null,
            'last_notify_time' => null,
            'last_action' => 0,
            'last_login' => 0,    
			'notifications' => 3,
        );

    public function get_feed_names()
    {
        return array(
            get_feed_name(array()), // global feed
            get_feed_name(array('user' => $this->guid))
        );
    }
    
    public function is_setup_complete()
    {
        return true;
    }

    public function get_name_for_email()
    {
        $name = mb_encode_mimeheader($this->name, "UTF-8", "B");
        return "\"$name\" <{$this->email}>";
    }

    public function get_title()
    {
        return $this->name;
    }

    public function get_url()
    {
        return Config::get('url') . $this->username;
    }

    public function get_icon_file($size = '')
    {
        $file = new UploadedFile();
        $file->owner_guid = $this->guid;
        $file->filename = "icon$size.jpg";
        return $file;
    }

    public function get_icon($size = 'medium')
    {
        if ($this->custom_icon)
        {
            return url_with_param($this->get_icon_file($size)->get_url(), 't', $this->time_updated);
        }
        else if ($this->latitude || $this->longitude)
        {
            return get_static_map_url($this->latitude, $this->longitude, 6, 100, 100);
        }
        else
        {
            return Config::get('url')."_graphics/default{$size}.gif";
        }
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
            $this->custom_icon = false;
        }
        else
        {
            foreach ($imageFiles as $srcFile)
            {
                $size = $srcFile->size;
                $destFile = $this->get_icon_file($size);
                $srcFile->copy_to($destFile);
            }

            $this->custom_icon = true;
        }
        $this->save();
    }

    public function get_header_file()
    {
        $file = new UploadedFile();
        $file->owner_guid = $this->guid;
        $file->filename = "headerlarge.jpg";
        return $file;
    }

    public function get_header_url()
    {
        if ($this->custom_header)
        {
            return url_with_param($this->get_header_file()->get_url(), 't', $this->time_updated);
        }
        return '';
    }

    public function set_header($imageFiles)
    {
        if (!$imageFiles)
        {
            $this->custom_header = null;
        }
        else
        {
            $srcFile = $imageFiles[0];            
            
            $destFile = $this->get_header_file();
            $srcFile->copy_to($destFile);

            $this->custom_header = json_encode(array(
                'width' => $srcFile->width,
                'height' => $srcFile->height
            ));
        }
        $this->save();
    }

    public function get_header()
    {
        return json_decode($this->custom_header, true);
    }

    static function get_header_sizes()
    {
        return array(
            'large' => '700x150',
        );
    }

    /**
     * Ban this user.
     *
     * @param string $reason Optional reason
     */
    public function ban($reason = "")
    {
        if ($this->can_edit())
        {
            $this->ban_reason = $reason;
            $this->banned = 'yes';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Unban this user.
     */
    public function unban()
    {
        if ($this->can_edit())
        {
            $this->ban_reason = '';
            $this->banned = 'yes';
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Is this user banned or not?
     *
     * @return bool
     */
    public function is_banned() { return $this->banned == 'yes'; }

    /**
     * Set latitude and longitude tags for a given entity.
     *
     * @param float $lat
     * @param float $long
     */
    public function set_lat_long($lat, $long)
    {
        $this->attributes['latitude'] = $lat;
        $this->attributes['longitude'] = $long;

        return true;
    }

    public function get_latitude() { return $this->attributes['latitude']; }
    public function get_longitude() { return $this->attributes['longitude']; }

    function query_feed_items()
    {
        $feedName = get_feed_name(array('user' => $this->guid));
        return FeedItem::query_by_feed_name($feedName);
    }
       
    function get_blog_dates()
    {
        $sql = "SELECT guid, time_created from entities WHERE enabled='yes' AND subtype=? AND container_guid=? ORDER BY guid ASC";
        return Database::get_rows($sql, array(NewsUpdate::get_subtype_id(), $this->guid));
    }

    public function is_approved()
    {
        return $this->approval > 0;
    }

    public function set_password($password)
    {
        $this->salt = substr(generate_random_cleartext_password(), 0, 8);
        $this->password = $this->generate_password($password);
    }

    static function query($show_unapproved = false)
    {
        $query = new Query_SelectEntity('users_entity');
        
        if (!Session::isadminloggedin() && !$show_unapproved)
        {
            $query->where("(approval > 0 || e.guid = ?)", Session::get_loggedin_userid());
        }
        
        return $query;
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

    function notify($subject, $message)
    {
        if (!$this->email)
            throw new NotificationException(sprintf(__('error:NoEmailAddress'), $this->guid));

        $headers = array('To' => $this->get_name_for_email());

        return send_mail($this->email, $subject, $message, $headers);    
    }
	
	function is_notification_enabled($notification)
	{
		return ($this->notifications & $notification) != 0;
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

}
