<?php
    /**
     * ElggUser
     *
     * Representation of a "user" in the system.
     *
     * @package Elgg
     * @subpackage Core
     */
    class ElggUser extends ElggEntity
        implements Locatable
    {
    
        /**
         * Initialise the attributes array.
         * This is vital to distinguish between metadata and base parameters.
         *
         * Place your base parameters here.
         */

        protected function initialise_attributes()
        {
            parent::initialise_attributes();

            $this->attributes['type'] = "user";

            $this->initializeTableAttributes('users_entity', array(
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
                'notify_days' => 14,
                'last_notify_time' => null,
            ));
        }

        public function getFeedNames()
        {
            return array(
                get_feed_name(array()), // global feed
                get_feed_name(array('user' => $this->guid))
            );
        }

        public function allowUnsafeHTML()
        {
            return $this->username == 'envaya'; // could make this DB property if necessary
        }
        
        public function isSetupComplete()
        {
            return !$this->subtype || $this->setup_state >= 5;
        }

        protected function loadFromPartialTableRow($row)
        {
            $userEntityRow = (property_exists($row, 'username')) ? $row : $this->selectTableAttributes('users_entity', $row->guid);
            return parent::loadFromPartialTableRow($row) && $this->loadFromTableRow($userEntityRow);
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

        /**
         * Override the load function.
         * This function will ensure that all data is loaded (were possible), so
         * if only part of the ElggUser is loaded, it'll load the rest.
         *
         * @param int $guid
         * @return true|false
         */
        protected function load($guid)
        {
            return parent::load($guid) && $this->loadFromTableRow(get_user_entity_as_row($guid));
        }

        /**
         * Saves this user to the database.
         * @return true|false
         */
        public function save()
        {
            return parent::save() && $this->saveTableAttributes('users_entity');
        }

        /**
         * User specific override of the entity delete method.
         *
         * @return bool
         */
        public function delete()
        {
            return parent::delete() && $this->deleteTableAttributes('users_entity');
        }

        public function getURL()
        {
            global $CONFIG;
            return $CONFIG->url . "{$this->username}";
        }

        public function getIconFile($size = '')
        {
            $file = new ElggFile();
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
            $file = new ElggFile();
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

        function getFeedItems($limit = 10)
        {
            $feedName = get_feed_name(array('user' => $this->guid));
            return FeedItem::queryByFeedName($feedName)->limit($limit)->filter();
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
            $salt = generate_random_cleartext_password(); // Reset the salt

            $this->salt = $salt;
            $this->password = generate_user_password($this, $password);
        }

        static function getByEmailCode($emailCode)
        {
            return static::query()->where('email_code = ?', $emailCode)->get();
        }

        static function query()
        {
            $query = new Query_SelectEntity('users_entity');
            $query->where("type='user'");
            if (static::$subtype_id)
            {
                $query->where("subtype=?", static::$subtype_id);
            }

            if (!Session::isadminloggedin() && !access_get_show_hidden_status())
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
    }
