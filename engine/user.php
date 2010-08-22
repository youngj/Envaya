<?php

class User extends Entity implements Locatable
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
            'enable_batch_email' => 1
        );

    /**
     * Initialise the attributes array.
     * This is vital to distinguish between metadata and base parameters.
     *
     * Place your base parameters here.
     */

    protected function initialize_attributes()
    {
        parent::initialize_attributes();
        $this->attributes['type'] = "user";
    }

    public function getFeedNames()
    {
        return array(
            get_feed_name(array()), // global feed
            get_feed_name(array('user' => $this->guid))
        );
    }
    
    public function isSetupComplete()
    {
        return !$this->subtype || $this->setup_state >= 5;
    }

    public function getNameForEmail()
    {
        $name = mb_encode_mimeheader($this->name, "UTF-8", "B");
        return "\"$name\" <{$this->email}>";
    }

    public function getTitle()
    {
        return $this->name;
    }

    public function getURL()
    {
        global $CONFIG;
        return $CONFIG->url . "{$this->username}";
    }

    public function getIconFile($size = '')
    {
        $file = new UploadedFile();
        $file->owner_guid = $this->guid;
        $file->filename = "icon$size.jpg";
        return $file;
    }

    public function getIcon($size = 'medium')
    {
        global $CONFIG;

        if ($this->custom_icon)
        {
            return $this->getIconFile($size)->getURL()."?{$this->time_updated}";
        }
        else if ($this->latitude || $this->longitude)
        {
            return get_static_map_url($this->latitude, $this->longitude, 6, 100, 100);
        }
        else
        {
            return "{$CONFIG->url}_graphics/default{$size}.gif";
        }
    }

    static function getIconSizes()
    {
        return array(
            'tiny' => '37x25',
            'small' => '60x40',
            'medium' => '150x100',
            'large' => '300x200',
        );
    }

    public function setIcon($imageFiles)
    {
        if (!$imageFiles)
        {
            $this->custom_icon = false;
        }
        else
        {
            foreach ($imageFiles as $size => $filedata)
            {
                $srcFile = $filedata['file'];
                $destFile = $this->getIconFile($size);
                $srcFile->copyTo($destFile);
            }

            $this->custom_icon = true;
        }
        $this->save();
    }

    public function getHeaderFile($size = '')
    {
        $file = new UploadedFile();
        $file->owner_guid = $this->guid;
        $file->filename = "header$size.jpg";
        return $file;
    }

    public function getHeaderURL($size = 'large')
    {
        if ($this->custom_header)
        {
            return $this->getHeaderFile($size)->getURL()."?{$this->time_updated}";
        }
        return '';
    }

    public function setHeader($imageFiles)
    {
        if (!$imageFiles)
        {
            $this->custom_header = null;
        }
        else
        {
            foreach ($imageFiles as $size => $filedata)
            {
                $srcFile = $filedata['file'];
                $destFile = $this->getHeaderFile($size);
                $srcFile->copyTo($destFile);
            }

            $this->custom_header = json_encode(array(
                'width' => $imageFiles['large']['width'],
                'height' => $imageFiles['large']['height']
            ));
        }
        $this->save();
    }

    public function getHeader()
    {
        return json_decode($this->custom_header, true);
    }

    static function getHeaderSizes()
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
        if ($this->canEdit())
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
        if ($this->canEdit())
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
    public function isBanned() { return $this->banned == 'yes'; }

    /**
     * If a user's owner is blank, return its own GUID as the owner
     *
     * @return int User GUID
     */
    function getOwner() {
        if ($this->owner_guid == 0)
            return $this->getGUID();

        return $this->owner_guid;
    }

    /**
     * Set latitude and longitude tags for a given entity.
     *
     * @param float $lat
     * @param float $long
     */
    public function setLatLong($lat, $long)
    {
        $this->attributes['latitude'] = $lat;
        $this->attributes['longitude'] = $long;

        return true;
    }

    public function getLatitude() { return $this->attributes['latitude']; }
    public function getLongitude() { return $this->attributes['longitude']; }

    public function getLocation() { return $this->get('location'); }

    function queryNewsUpdates()
    {
        return NewsUpdate::query()->where("container_guid=?", $this->guid)->order_by('time_created desc');
    }

    function queryFeedItems()
    {
        $feedName = get_feed_name(array('user' => $this->guid));
        return FeedItem::queryByFeedName($feedName);
    }
    
    
    function getBlogDates()
    {
        $sql = "SELECT guid, time_created from entities WHERE type='object' AND enabled='yes' AND subtype=? AND container_guid=? ORDER BY guid ASC";
        return get_data($sql, array(T_blog, $this->guid));
    }

    public function isApproved()
    {
        return $this->approval > 0;
    }

    public function setPassword($password)
    {
        $this->salt = substr(generate_random_cleartext_password(), 0, 8);
        $this->password = $this->generate_password($password);
    }

    static function getByEmailCode($emailCode)
    {
        return static::query()->where('email_code = ?', $emailCode)->get();
    }

    static function query($show_unapproved = false)
    {
        $query = new Query_SelectEntity('users_entity');
        $query->where("type='user'");
        if (static::$subtype_id)
        {
            $query->where("subtype=?", static::$subtype_id);
        }

        if (!Session::isadminloggedin() && !$show_unapproved)
        {
            $query->where("(approval > 0 || e.guid = ?)", Session::get_loggedin_userid());
        }
        
        return $query;
    }


    public function jsProperties()
    {
        return array(
            'guid' => $this->guid,
            'username' => $this->username,
            'name' => $this->name,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'url' => $this->getUrl()
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

        $headers = array('To' => $this->getNameForEmail());

        return send_mail($this->email, $subject, $message, $headers);    
    }

}
