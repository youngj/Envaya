<?php

	/**
	 * Elgg users
	 * Functions to manage multiple or single users in an Elgg install
	 * 
	 * @package Elgg
	 * @subpackage Core

	 * @author Curverider Ltd

	 * @link http://elgg.org/
	 */

	/// Map a username to a cached GUID
	$USERNAME_TO_GUID_MAP_CACHE = array();
	
	/// Map a user code to a cached GUID
	$CODE_TO_GUID_MAP_CACHE = array();

	/**
	 * ElggUser
	 * 
	 * Representation of a "user" in the system.
	 * 
	 * @package Elgg
	 * @subpackage Core
	 */
	class ElggUser extends ElggEntity
		implements Friendable, Locatable
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
                'email_code' => null,
                'setup_state' => 5,
                'custom_icon' => 0,
            ));    
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
			clear_metadata_by_owner($this->guid);			
            return parent::delete() && $this->deleteTableAttributes('users_entity');
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
                if (trigger_elgg_event('ban', 'user', $this)) 
                {
                    $this->ban_reason = $reason;
                    $this->banned = 'yes';
                    $this->save();
                    return true;
                }       
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
                if (trigger_elgg_event('unban', 'user', $this)) 
                {
                    $this->ban_reason = '';
                    $this->banned = 'yes';
                    $this->save();
                    return true;
                }       
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
		 * Adds a user to this user's friends list
		 *
		 * @param int $friend_guid The GUID of the user to add
		 * @return true|false Depending on success
		 */
		function addFriend($friend_guid) { return user_add_friend($this->getGUID(), $friend_guid); }
		
		/**
		 * Removes a user from this user's friends list
		 *
		 * @param int $friend_guid The GUID of the user to remove
		 * @return true|false Depending on success
		 */
		function removeFriend($friend_guid) { return user_remove_friend($this->getGUID(), $friend_guid); }
		
		/**
		 * Determines whether or not this user is a friend of the currently logged in user
		 *
		 * @return true|false
		 */
		function isFriend() { return user_is_friend(get_loggedin_userid(), $this->getGUID()); }
		
		/**
		 * Determines whether this user is friends with another user
		 *
		 * @param int $user_guid The GUID of the user to check is on this user's friends list
		 * @return true|false
		 */
		function isFriendsWith($user_guid) { return user_is_friend($this->getGUID(), $user_guid); }
		
		/**
		 * Determines whether or not this user is on another user's friends list
		 *
		 * @param int $user_guid The GUID of the user to check against
		 * @return true|false
		 */
		function isFriendOf($user_guid) { return user_is_friend($user_guid, $this->getGUID()); }
		
		/**
		 * Retrieves a list of this user's friends
		 *
		 * @param string $subtype Optionally, the subtype of user to filter to (leave blank for all)
		 * @param int $limit The number of users to retrieve
		 * @param int $offset Indexing offset, if any
		 * @return array|false Array of ElggUsers, or false, depending on success
		 */
		function getFriends($subtype = "", $limit = 10, $offset = 0) { return get_user_friends($this->getGUID(), $subtype, $limit, $offset); }
		
		/**
		 * Retrieves a list of people who have made this user a friend
		 *
		 * @param string $subtype Optionally, the subtype of user to filter to (leave blank for all)
		 * @param int $limit The number of users to retrieve
		 * @param int $offset Indexing offset, if any
		 * @return array|false Array of ElggUsers, or false, depending on success
		 */
		function getFriendsOf($subtype = "", $limit = 10, $offset = 0) { return get_user_friends_of($this->getGUID(), $subtype, $limit, $offset); }
		
		/**
		 * Get an array of ElggObjects owned by this user.
		 *
		 * @param string $subtype The subtype of the objects, if any
		 * @param int $limit Number of results to return
		 * @param int $offset Any indexing offset
		 */
		public function getObjects($subtype="", $limit = 10, $offset = 0) { return get_user_objects($this->getGUID(), $subtype, $limit, $offset); }

		/**
		 * Get an array of ElggObjects owned by this user's friends.
		 *
		 * @param string $subtype The subtype of the objects, if any
		 * @param int $limit Number of results to return
		 * @param int $offset Any indexing offset
		 */
		public function getFriendsObjects($subtype = "", $limit = 10, $offset = 0) { return get_user_friends_objects($this->getGUID(), $subtype, $limit, $offset); }
		
		/**
		 * Counts the number of ElggObjects owned by this user
		 *
		 * @param string $subtype The subtypes of the objects, if any
		 * @return int The number of ElggObjects
		 */
		public function countObjects($subtype = "") {
			return count_user_objects($this->getGUID(), $subtype);
		}

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

        public function setLocation($location)
        {
            $this->location = $location;            
            return true;
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

        function getNewsUpdates($limit = 10, $offset = 0, $count = false)
        {
            $where = array();
            $args = array();
            
            $where[] = "subtype=?";
            $args[] = T_blog;
        
            $where[] = "container_guid=?";
            $args[] = $this->guid;
        
            return NewsUpdate::filterByCondition($where, $args, "time_created desc", $limit, $offset, $count);
        }
        
        function listNewsUpdates($limit = 10, $pagination = true) 
        {        
            $offset = (int) get_input('offset');

            $count = $this->getNewsUpdates($limit, $offset, true);
            $entities = $this->getNewsUpdates($limit, $offset);

            return elgg_view_entity_list($entities, $count, $offset, $limit, false, false, $pagination);
        }
        
        function getBlogDates()
        {
            $sql = "SELECT guid, time_created from entities WHERE type='object' AND enabled='yes' AND subtype=? AND container_guid=? ORDER BY guid ASC";
            return get_data($sql, array(T_blog, $this->guid));               
        }           
        
        public function userCanSee()
        {
            return ($this->isApproved() || isadminloggedin() || ($this->guid == get_loggedin_userid()));
        }

        public function isApproved()
        {
            return $this->approval > 0;
        }
                
        static function getByEmailCode($emailCode)
        {
            return static::getByCondition(
                array('email_code = ?'),
                array($emailCode)
            );                    
        }
                
        static function all($order_by = '', $limit = 10, $offset = 0, $count = false)
        {
            return static::filterByCondition(array(), array(), $order_by, $limit, $offset, $count);
        }
        
        static function listAll($limit = 10, $pagination = true) 
        {        
            $offset = (int) get_input('offset');

            $count = static::all($limit, $offset, true);
            $entities = static::all($limit, $offset);

            return elgg_view_entity_list($entities, $count, $offset, $limit, false, false, $pagination);
        }
                        
        static function getByCondition($where, $args)
        {
            $users = static::filterByCondition($where, $args, '', 1, 0, false);
            if (!empty($users))
            {
                return $users[0];
            }
            return null;
        }        
        
        static function filterByCondition($where, $args, $order_by = '', $limit = 10, $offset = 0, $count = false, $join = '')
        {
            $where[] = "type='user'";
            
            $subtypeId = static::$subtype_id;
            if ($subtypeId)
            {
                $where[] = "subtype=?";
                $args[] = $subtypeId;
            }
            
            if (!isadminloggedin())
            {
                $where[] = "(approval > 0 || e.guid = ?)";
                $args[] = get_loggedin_userid();                
            }

            return get_entities_by_condition('users_entity', $where, $args, $order_by, $limit, $offset, $count, $join);        
        }
        

        public function jsProperties()
        {
            return array(
                'guid' => $this->guid,
                'username' => $this->username,
                'name' => $this->name,
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'icon' => $this->getIcon('tiny'),
                'url' => $this->getUrl()
            );  
        }        
	}

	/**
	 * Adds a user to another user's friends list.
	 *
	 * @param int $user_guid The GUID of the friending user
	 * @param int $friend_guid The GUID of the user to friend
	 * @return true|false Depending on success
	 */
	function user_add_friend($user_guid, $friend_guid) {
		$user_guid = (int) $user_guid; 
		$friend_guid = (int) $friend_guid;
		if ($user_guid == $friend_guid) return false;
		if (!$friend = get_entity($friend_guid)) return false;
		if (!$user = get_entity($user_guid)) return false;
		if ( (!($user instanceof ElggUser)) || (!($friend instanceof ElggUser)) ) return false;
		return add_entity_relationship($user_guid, "friend", $friend_guid);
	}
	
	/**
	 * Removes a user from another user's friends list.
	 *
	 * @param int $user_guid The GUID of the friending user
	 * @param int $friend_guid The GUID of the user on the friends list
	 * @return true|false Depending on success
	 */
	function user_remove_friend($user_guid, $friend_guid) {
		$user_guid = (int) $user_guid; 
		$friend_guid = (int) $friend_guid;
		
		// perform cleanup for access lists.
		$collections = get_user_access_collections($user_guid);
		foreach ($collections as $collection) {
			remove_user_from_access_collection($friend_guid, $collection->id);
		}
		
		return remove_entity_relationship($user_guid, "friend", $friend_guid);
	}
	
	/**
	 * Determines whether or not a user is another user's friend.
	 *
	 * @param int $user_guid The GUID of the user
	 * @param int $friend_guid The GUID of the friend
	 * @return true|false
	 */
	function user_is_friend($user_guid, $friend_guid) {
		return check_entity_relationship($user_guid, "friend", $friend_guid);
	}

	/**
	 * Obtains a given user's friends
	 *
	 * @param int $user_guid The user's GUID
	 * @param string $subtype The subtype of users, if any
	 * @param int $limit Number of results to return (default 10)
	 * @param int $offset Indexing offset, if any
	 * @return false|array Either an array of ElggUsers or false, depending on success
	 */
	function get_user_friends($user_guid, $subtype = "", $limit = 10, $offset = 0) {
		return get_entities_from_relationship("friend",$user_guid,false,"user",$subtype,0,"time_created desc",$limit,$offset);
	}
	
	/**
	 * Obtains the people who have made a given user a friend
	 *
	 * @param int $user_guid The user's GUID
	 * @param string $subtype The subtype of users, if any
	 * @param int $limit Number of results to return (default 10)
	 * @param int $offset Indexing offset, if any
	 * @return false|array Either an array of ElggUsers or false, depending on success
	 */
	function get_user_friends_of($user_guid, $subtype = "", $limit = 10, $offset = 0) {
		return get_entities_from_relationship("friend",$user_guid,true,"user",$subtype,0,"time_created desc",$limit,$offset);
	}

	/**
	 * Obtains a list of objects owned by a user
	 *
	 * @param int $user_guid The GUID of the owning user
	 * @param string $subtype Optionally, the subtype of objects
	 * @param int $limit The number of results to return (default 10)
	 * @param int $offset Indexing offset, if any
	 * @param int $timelower The earliest time the entity can have been created. Default: all
	 * @param int $timeupper The latest time the entity can have been created. Default: all
	 * @return false|array An array of ElggObjects or false, depending on success
	 */
	function get_user_objects($user_guid, $subtype = "", $limit = 10, $offset = 0, $timelower = 0, $timeupper = 0) {
		$ntt = get_entities('object',$subtype, $user_guid, "time_created desc", $limit, $offset,false,0,$user_guid,$timelower, $timeupper);
		return $ntt;
	}
	
	/**
	 * Counts the objects (optionally of a particular subtype) owned by a user
	 *
	 * @param int $user_guid The GUID of the owning user
	 * @param string $subtype Optionally, the subtype of objects
	 * @param int $timelower The earliest time the entity can have been created. Default: all
	 * @param int $timeupper The latest time the entity can have been created. Default: all
	 * @return int The number of objects the user owns (of this subtype)
	 */
	function count_user_objects($user_guid, $subtype = "", $timelower, $timeupper) {
		$total = get_entities('object', $subtype, $user_guid, "time_created desc", null, null, true, 0, $user_guid,$timelower,$timeupper);
		return $total;
	}

	/**
	 * Displays a list of user objects of a particular subtype, with navigation.
	 *
	 * @see elgg_view_entity_list
	 * 
	 * @param int $user_guid The GUID of the user
	 * @param string $subtype The object subtype
	 * @param int $limit The number of entities to display on a page
	 * @param true|false $fullview Whether or not to display the full view (default: true)
	 * @param int $timelower The earliest time the entity can have been created. Default: all
	 * @param int $timeupper The latest time the entity can have been created. Default: all
	 * @return string The list in a form suitable to display
	 */
	function list_user_objects($user_guid, $subtype = "", $limit = 10, $fullview = true, $viewtypetoggle = true, $pagination = true, $timelower = 0, $timeupper = 0) {
		
		$offset = (int) get_input('offset');
		$limit = (int) $limit;
		$count = (int) count_user_objects($user_guid, $subtype,$timelower,$timeupper);
		$entities = get_user_objects($user_guid, $subtype, $limit, $offset, $timelower, $timeupper);
		
		return elgg_view_entity_list($entities, $count, $offset, $limit, $fullview, $viewtypetoggle, $pagination);
		
	}
	
	/**
	 * Obtains a list of objects owned by a user's friends
	 *
	 * @param int $user_guid The GUID of the user to get the friends of
	 * @param string $subtype Optionally, the subtype of objects
	 * @param int $limit The number of results to return (default 10)
	 * @param int $offset Indexing offset, if any
	 * @return false|array An array of ElggObjects or false, depending on success
	 */
	function get_user_friends_objects($user_guid, $subtype = "", $limit = 10, $offset = 0) {
		if ($friends = get_user_friends($user_guid, $subtype, 999999, 0)) {
			$friendguids = array();
			foreach($friends as $friend) {
				$friendguids[] = $friend->getGUID();
			}
			return get_entities('object',$subtype,$friendguids, "time_created desc", $limit, $offset, false, 0, $friendguids);
		}
		return false;
	}
	
	/**
	 * Counts the number of objects owned by a user's friends
	 *
	 * @param int $user_guid The GUID of the user to get the friends of
	 * @param string $subtype Optionally, the subtype of objects
	 * @return int The number of objects
	 */
	function count_user_friends_objects($user_guid, $subtype = "") {
		if ($friends = get_user_friends($user_guid, $subtype, 999999, 0)) {
			$friendguids = array();
			foreach($friends as $friend) {
				$friendguids[] = $friend->getGUID();
			}
			return get_entities('object',$subtype,$friendguids, "time_created desc", $limit, $offset, true, 0, $friendguids);
		}
		return 0;
	}

	/**
	 * Displays a list of a user's friends' objects of a particular subtype, with navigation.
	 *
	 * @see elgg_view_entity_list
	 * 
	 * @param int $user_guid The GUID of the user
	 * @param string $subtype The object subtype
	 * @param int $limit The number of entities to display on a page
	 * @param true|false $fullview Whether or not to display the full view (default: true)
	 * @param true|false $viewtypetoggle Whether or not to allow you to flip to gallery mode (default: true)
	 * @return string The list in a form suitable to display
	 */
	function list_user_friends_objects($user_guid, $subtype = "", $limit = 10, $fullview = true, $viewtypetoggle = true, $pagination = true) {
		
		$offset = (int) get_input('offset');
		$limit = (int) $limit;
		$count = (int) count_user_friends_objects($user_guid, $subtype);
		$entities = get_user_friends_objects($user_guid, $subtype, $limit, $offset);
		
		return elgg_view_entity_list($entities, $count, $offset, $limit, $fullview, $viewtypetoggle, $pagination);
		
	}
	
	/**
	 * Get a user object from a GUID.
	 * 
	 * This function returns an ElggUser from a given GUID.
	 * @param int $guid The GUID
	 * @return ElggUser|false 
	 */
	function get_user($guid)
	{
		if (!empty($guid)) // Fixes "Exception thrown without stack frame" when db_select fails
			$result = get_entity($guid);
		
		if ((!empty($result)) && (!($result instanceof ElggUser)))
			//throw new InvalidClassException(sprintf(elgg_echo('InvalidClassException:NotValidElggStar'), $guid, 'ElggUser'));
			return false;
			
		if (!empty($result))
			return $result;
		
		return false;	
	}
	
	/**
	 * Get user by username
	 *
	 * @param string $username The user's username
	 * @return ElggUser|false Depending on success
	 */
	function get_user_by_username($username)
	{
		global $USERNAME_TO_GUID_MAP_CACHE;
		
		$access = get_access_sql_suffix('e');
        
		if ( (isset($USERNAME_TO_GUID_MAP_CACHE[$username])) && (retrieve_cached_entity($USERNAME_TO_GUID_MAP_CACHE[$username])) )
			return retrieve_cached_entity($USERNAME_TO_GUID_MAP_CACHE[$username]);
		
		$row = get_data_row("SELECT e.*, u.* from users_entity u join entities e on e.guid=u.guid where u.username=? and $access ", array($username));
		if ($row) {
			$USERNAME_TO_GUID_MAP_CACHE[$username] = $row->guid;
            return entity_row_to_elggstar($row);
		} 
		
		return false;
	}
	
	/**
	 * Get user by session code
	 *
	 * @param string $code The session code
	 * @return ElggUser|false Depending on success
	 */
	function get_user_by_code($code)
	{
		global $CONFIG, $CODE_TO_GUID_MAP_CACHE;
		
		$access = get_access_sql_suffix('e');
		
		// Caching
		if ( (isset($CODE_TO_GUID_MAP_CACHE[$code])) && (retrieve_cached_entity($CODE_TO_GUID_MAP_CACHE[$code])) )
			return retrieve_cached_entity($CODE_TO_GUID_MAP_CACHE[$code]);
		
		$row = get_data_row("SELECT e.*, u.* from users_entity u join entities e on e.guid=u.guid where u.code=? and $access", array($code));
		if ($row) 
        {
			$CODE_TO_GUID_MAP_CACHE[$code] = $row->guid;
            return entity_row_to_elggstar($row);
		} 
		
		return false;
	}
	
	/**
	 * Get an array of users from their
	 *
	 * @param string $email Email address.
	 * @return Array of users
	 */
	function get_user_by_email($email)
	{
		$access = get_access_sql_suffix('e');
		
        return array_map('entity_row_to_elggstar', get_data(
            "SELECT e.* from entities e join users_entity u on e.guid=u.guid where email=? and $access", 
            array($email)
        ));
	}
	
	/**
	 * Searches for a user based on a complete or partial name or username.
	 *
	 * @param string $criteria The partial or full name or username.
	 * @param int $limit Limit of the search.
	 * @param int $offset Offset.
	 * @param string $order_by The order.
	 * @param boolean $count Whether to return the count of results or just the results. 
	 */
	function search_for_user($criteria, $limit = 10, $offset = 0, $order_by = "", $count = false, $user_subtype = "")
	{		
        $user_subtype_id = get_subtype_id('user', $user_subtype);
        
		$access = get_access_sql_suffix("e");
				
        $args = array();
        
		if ($count) 
        {
			$query = "SELECT count(e.guid) as total ";
		} 
        else 
        {
			$query = "SELECT e.*, u.* "; 
		}
		
        $query .= "FROM entities e JOIN users_entity u ON e.guid=u.guid WHERE (INSTR(u.username, ?) > 0 OR INSTR(u.name, ?) > 0) and $access";        
        $args[] = $criteria;        
        $args[] = $criteria;
        
        if ($user_subtype_id)
        {
            $query .= "AND e.subtype=?";
            $args[] = $user_subtype_id;
        }
		
		if (!$count) 
        {
            $args[] = (int)$offset;
            $args[] = (int)$limit;            

            $order_by = sanitize_order_by($order_by);
            if ($order_by == "") 
                $order_by = "e.time_created desc";
        
            return array_map('entity_row_to_elggstar', get_data("$query order by $order_by limit ?, ?", $args));
		} 
        else 
        {
			if ($count = get_data_row($query, $args)) 
            {
				return $count->total;
			}
		}
		return false;
	}
	
	/**
	 * Displays a list of user objects that have been searched for.
	 *
	 * @see elgg_view_entity_list
	 * 
	 * @param string $tag Search criteria
	 * @param int $limit The number of entities to display on a page
	 * @return string The list in a form suitable to display
	 */
	function list_user_search($tag, $limit = 10, $user_subtype = '') {
		
		$offset = (int) get_input('offset');
		$limit = (int) $limit;
        $count = (int) search_for_user($tag, 10, 0, '', true, $user_subtype);
		$entities = search_for_user($tag, $limit, $offset, '', false, $user_subtype);
		
		return elgg_view_entity_list($entities, $count, $offset, $limit, $fullview, false);
		
	}
	
	/**
	 * A function that returns a maximum of $limit users who have done something within the last 
	 * $seconds seconds.
	 *
	 * @param int $seconds Number of seconds (default 600 = 10min)
	 * @param int $limit Limit, default 10.
	 * @param int $offset Offset, defualt 0.
	 */
	function find_active_users($seconds = 600, $limit = 10, $offset = 0)
	{
		global $CONFIG;
		
		$seconds = (int)$seconds;
		$limit = (int)$limit;
		$offset = (int)$offset;
		
		$time = time() - $seconds;

		$access = get_access_sql_suffix("e");
		
		$query = "SELECT distinct e.* from entities e join users_entity u on e.guid = u.guid where u.last_action >= ? and $access order by u.last_action desc limit {$offset},{$limit}";
		
        return array_map('entity_row_to_elggstar', get_data($query, array($time)));
	}
	
	/**
	 * Generate and send a password request email to a given user's registered email address.
	 *
	 * @param int $user_guid
	 */
	function send_new_password_request($user_guid)
	{
		global $CONFIG;
		
		$user_guid = (int)$user_guid;
		
		$user = get_entity($user_guid);
		if ($user)
		{
			// generate code
			$code = generate_random_cleartext_password();
			//create_metadata($user_guid, 'conf_code', $code,'', 0, ACCESS_PRIVATE);
			set_private_setting($user_guid, 'passwd_conf_code', $code);
			
			// generate link
			$link = $CONFIG->url . "action/user/passwordreset?u=$user_guid&c=$code";
			
			// generate email
			$email = sprintf(elgg_echo('email:resetreq:body'), $user->name, $_SERVER['REMOTE_ADDR'], $link);
			
			return notify_user($user->guid, $CONFIG->site_guid, elgg_echo('email:resetreq:subject'), $email, NULL, 'email');

		}
		
		return false;
	}
	
	/**
	 * Low level function to reset a given user's password. 
	 * 
	 * This can only be called from execute_new_password_request().
	 * 
	 * @param int $user_guid The user.
	 * @param string $password password text (which will then be converted into a hash and stored)
	 */
	function force_user_password_reset($user_guid, $password)
	{
		global $CONFIG;
		
        $user = get_entity($user_guid);

        if ($user)
        {
            $salt = generate_random_cleartext_password(); // Reset the salt
            
            $user->salt = $salt;
            
            $hash = generate_user_password($user, $password);
            
            return update_data("UPDATE users_entity set password=?, salt=? where guid=?", array($hash, $salt, $user_guid));
        }
		
		return false;
	}
	
	/**
	 * Validate and execute a password reset for a user.
	 *
	 * @param int $user_guid The user id
	 * @param string $conf_code Confirmation code as sent in the request email.
	 */
	function execute_new_password_request($user_guid, $conf_code)
	{
		global $CONFIG;
		
		$user_guid = (int)$user_guid;
		
		$user = get_entity($user_guid);
		if (($user) && (get_private_setting($user_guid, 'passwd_conf_code') == $conf_code))
		{
			$password = generate_random_cleartext_password();
			
			if (force_user_password_reset($user_guid, $password))
			{
				//remove_metadata($user_guid, 'conf_code');
				remove_private_setting($user_guid, 'passwd_conf_code');
				
				$email = sprintf(elgg_echo('email:resetpassword:body'), $user->name, $password);
				
				return notify_user($user->guid, $CONFIG->site_guid, elgg_echo('email:resetpassword:subject'), $email, NULL, 'email');
			}
		}
		
		return false;
	}
    
	/**
	 * Validates an email address.
	 *
	 * @param string $address Email address.
	 * @return bool
	 */
	function is_email_address($address)
	{
		// TODO: Make this better!
		
		if (strpos($address, '@')=== false) 
			return false;
		
		if (strpos($address, '.')=== false)
			return false;
			
		return true;
	}
	
	/**
	 * Simple function that will generate a random clear text password suitable for feeding into generate_user_password().
	 *
	 * @see generate_user_password
	 * @return string
	 */
	function generate_random_cleartext_password()
	{
		return substr(md5(microtime() . rand()), 0, 8);
	}
	
	/**
	 * Generate a password for a user, currently uses MD5.
	 * 
	 * Later may introduce salting etc.
	 *
	 * @param ElggUser $user The user this is being generated for.
	 * @param string $password Password in clear text
	 */
	function generate_user_password(ElggUser $user, $password)
	{
		return md5($password . $user->salt);
	}
	
	/**
	 * Simple function which ensures that a username contains only valid characters.
	 * 
	 * This should only permit chars that are valid on the file system as well.
	 *
	 * @param string $username
	 * @throws RegistrationException on invalid
	 */
	function validate_username($username)
	{
		global $CONFIG;
				
		if (strlen($username) < 3)
        {
			throw new RegistrationException(elgg_echo('registration:usernametooshort'));
        }    
		
		if (preg_match('/[^a-zA-Z0-9\-\_]/', $username))
        {
			throw new RegistrationException(elgg_echo('registration:invalidchars'));
        }
        
        $lower = strtolower($username);
        
        $badUsernames = array(
            'pg', 
            'org', 
            'page',
            'action',
            'account',
            'envaya',
            'mod',
            'search',
            'admin',
            'dashboard',
            'engine'
        );    
        
        if (in_array($lower, $badUsernames) || $username[0] == "_")
        {
            throw new RegistrationException(elgg_echo('registration:usernamenotvalid'));
        }
        
        return true;
	}
	
	/**
	 * Simple validation of a password.
	 *
	 * @param string $password
	 * @throws RegistrationException on invalid
	 */
	function validate_password($password)
	{
		if (strlen($password)<6) 
            throw new RegistrationException(elgg_echo('registration:passwordtooshort'));
			
		return true;
	}
	
	/**
	 * Simple validation of a email.
	 *
	 * @param string $address
	 * @throws RegistrationException on invalid
	 * @return bool
	 */
	function validate_email_address($address)
	{
		if (!is_email_address($address)) 
            throw new RegistrationException(elgg_echo('registration:notemail'));
				
		return true;
	}
	
	/**
	 * Registers a user, returning false if the username already exists
	 *
	 * @param string $username The username of the new user
	 * @param string $password The password
	 * @param string $name The user's display name
	 * @param string $email Their email address
	 * @param bool $allow_multiple_emails Allow the same email address to be registered multiple times?
	 * @param int $friend_guid Optionally, GUID of a user this user will friend once fully registered 
	 * @return int|false The new user's GUID; false on failure
	 */
	function register_user($username, $password, $name, $email, $allow_multiple_emails = false, $friend_guid = 0, $invitecode = '') {
		
		// Load the configuration
			global $CONFIG;
			
			$username = trim($username);
			$password = trim($password);
			$name = trim($name);
			$email = trim($email);
			
		// A little sanity checking
			if (empty($username)
				|| empty($password)
				|| empty($name)
				|| empty($email)) {
					return false;
				}	
			
			// See if it exists and is disabled
			$access_status = access_get_show_hidden_status();
			access_show_hidden_entities(true);
				
			// Validate email address
			if (!validate_email_address($email)) throw new RegistrationException(elgg_echo('registration:emailnotvalid'));
			
			// Validate password
			if (!validate_password($password)) throw new RegistrationException(elgg_echo('registration:passwordnotvalid'));
			
			// Validate the username
			if (!validate_username($username)) throw new RegistrationException(elgg_echo('registration:usernamenotvalid'));
				
		// Check to see if $username exists already
			if ($user = get_user_by_username($username)) {
				//return false;
				throw new RegistrationException(elgg_echo('registration:userexists'));
			}
			
		// If we're not allowed multiple emails then see if this address has been used before
			if ((!$allow_multiple_emails) && (get_user_by_email($email)))
			{
				throw new RegistrationException(elgg_echo('registration:dupeemail'));
			}
			
			access_show_hidden_entities($access_status);
			
		// Check to see if we've registered the first admin yet.
		// If not, this is the first admin user!
			$admin = datalist_get('admin_registered');
			
		// Otherwise ...
			$user = new ElggUser();
			$user->username = $username;
			$user->email = $email;
			$user->name = $name;
			$user->access_id = ACCESS_PUBLIC;
			$user->salt = generate_random_cleartext_password(); // Note salt generated before password!
			$user->password = generate_user_password($user, $password); 
			$user->owner_guid = 0; // Users aren't owned by anyone, even if they are admin created.
			$user->container_guid = 0; // Users aren't contained by anyone, even if they are admin created.
			$user->save();
			
		// If $friend_guid has been set, make mutual friends
			if ($friend_guid) {
				if ($friend_user = get_user($friend_guid)) {
					if ($invitecode == generate_invite_code($friend_user->username)) {
						$user->addFriend($friend_guid);
						$friend_user->addFriend($user->guid);
					}
				}
			}
			
			global $registering_admin;
			if (!$admin) {
				$user->admin = true;
				datalist_set('admin_registered',1);
				$registering_admin = true;
			} else {
				$registering_admin = false;
			}
			
			// Turn on email notifications by default
			set_user_notification_setting($user->getGUID(), 'email', true);
			
			return $user->getGUID();
	}
	
	/**
	 * Generates a unique invite code for a user
	 *
	 * @param string $username The username of the user sending the invitation
	 * @return string Invite code
	 */
	function generate_invite_code($username) {
		
		$secret = datalist_get('__site_secret__');
		return md5($username . $secret);
		
	}
	
	/**
	 * Adds collection submenu items 
	 *
	 */
	function collections_submenu_items() 
    {
		global $CONFIG;
		$user = get_loggedin_user();
		add_submenu_item(elgg_echo('friends:collections'), $CONFIG->wwwroot . "pg/collections/" . $user->username);
		add_submenu_item(elgg_echo('friends:collections:add'),$CONFIG->wwwroot."pg/collections/add");
	}
	
	/**
	 * Page handler for friends
	 *
	 */
	function friends_page_handler($page_elements) {
		
		if (isset($page_elements[0]) && $user = get_user_by_username($page_elements[0])) {
			set_page_owner($user->getGUID());
		}
		if ($_SESSION['guid'] == page_owner()) {
			collections_submenu_items();
		}
		require_once(dirname(dirname(dirname(__FILE__))) . "/friends/index.php");
		
	}
	
	/**
	 * Page handler for friends of
	 *
	 */
	function friends_of_page_handler($page_elements) {
		
		if (isset($page_elements[0]) && $user = get_user_by_username($page_elements[0])) {
			set_page_owner($user->getGUID());
		}
		if ($_SESSION['guid'] == page_owner()) {
			collections_submenu_items();
		}
		require_once(dirname(dirname(dirname(__FILE__))) . "/friends/of.php");
		
	}
	
	/**
	 * Page handler for friends of
	 *
	 */
	function collections_page_handler($page_elements) {
		
		if (isset($page_elements[0])) {
			if ($page_elements[0] == "add") {
				set_page_owner($_SESSION['guid']);
				collections_submenu_items();
				require_once(dirname(dirname(dirname(__FILE__))) . "/friends/add.php"); 
			} else {
				if ($user = get_user_by_username($page_elements[0])) {
					set_page_owner($user->getGUID());
					if ($_SESSION['guid'] == page_owner()) {
						collections_submenu_items();
					}
					require_once(dirname(dirname(dirname(__FILE__))) . "/friends/collections.php");
				}
			}
		}
		
	}
	
	/**
	 * Page handler for dashboard
	 */
	function dashboard_page_handler($page_elements) {
		@require_once(dirname(dirname(dirname(__FILE__))) . "/dashboard/index.php");
	}

	/**
	 * Sets the last action time of the given user to right now.
	 *
	 * @param int $user_guid The user GUID
	 */
	function set_last_action($user_guid) 
    {	
		execute_delayed_write_query("UPDATE users_entity set prev_last_action = last_action, last_action = ? where guid = ?", array(time(), $user_guid));		
	}
	
	/**
	 * Sets the last logon time of the given user to right now.
	 *
	 * @param int $user_guid The user GUID
	 */
	function set_last_login($user_guid) 
    {	
		execute_delayed_write_query("UPDATE users_entity set prev_last_login = last_login, last_login = ? where guid = ?", array(time(), $user_guid));		
	}
	
	/**
	 * Sets up user-related menu items
	 *
	 */
	function users_pagesetup() 
    {		
        global $CONFIG;			

        if (get_context() == "friends" || 
            get_context() == "friendsof" || 
            get_context() == "collections") {
            add_submenu_item(elgg_echo('friends'),$CONFIG->wwwroot."pg/friends/" . page_owner_entity()->username);
            add_submenu_item(elgg_echo('friends:of'),$CONFIG->wwwroot."pg/friendsof/" . page_owner_entity()->username);
        }
		
	}
	
	/**
	 * Users initialisation function, which establishes the page handler
	 *
	 */
	function users_init() {
		
		global $CONFIG;
				
        if (isloggedin()) {
            $user = get_loggedin_user();
            add_menu(elgg_echo('friends'), $CONFIG->wwwroot . "pg/friends/" . $user->username);
        }
		
		register_page_handler('friends','friends_page_handler');
		register_page_handler('friendsof','friends_of_page_handler');
		register_page_handler('collections','collections_page_handler');
		register_page_handler('dashboard','dashboard_page_handler');
		register_action("register",true);
   		register_action("useradd",true);
		register_action("friends/add");
   		register_action("friends/remove");
		register_action('friends/addcollection');
		register_action('friends/deletecollection');
        register_action('friends/editcollection');
        register_action("user/spotlight");

		register_action("usersettings/save");
		
		register_action("user/passwordreset");
		register_action("user/requestnewpassword");
		
		// User name change
		extend_elgg_settings_page('user/settings/name', 'usersettings/user', 1);
		//register_action("user/name");
		
		// User password change
		extend_elgg_settings_page('user/settings/password', 'usersettings/user', 1);
		//register_action("user/password");
		
		// Add email settings
		extend_elgg_settings_page('user/settings/email', 'usersettings/user', 1);
		//register_action("email/save");
		
		// Add language settings
		extend_elgg_settings_page('user/settings/language', 'usersettings/user', 1);
		
		// Add default access settings
		extend_elgg_settings_page('user/settings/default_access', 'usersettings/user', 1);
		
		//register_action("user/language");
		
		// Register the user type
		register_entity_type('user','');
		
		register_plugin_hook('usersettings:save','user','users_settings_save');
		register_plugin_hook('search','all','search_list_users_by_name');
		
	}
	
	/**
	 * Returns a formatted list of users suitable for injecting into search.
	 *
	 */
	function search_list_users_by_name($hook, $user, $returnvalue, $tag) {

		// Change this to set the number of users that display on the search page
		$threshold = 4;

		$object = get_input('object');
		
		if (!get_input('offset') && (empty($object) || $object == 'user'))
		if ($users = search_for_user($tag,$threshold)) {
			
			$countusers = search_for_user($tag,0,0,"",true);
			
			$return = elgg_view('user/search/startblurb',array('count' => $countusers, 'tag' => $tag));
			foreach($users as $user) {
				$return .= elgg_view_entity($user);
			}
			$return .= elgg_view('user/search/finishblurb',array('count' => $countusers, 'threshold' => $threshold, 'tag' => $tag));
			return $return;
			
		}		
	}   
	
	function users_settings_save() {
		
		global $CONFIG;
		@include($CONFIG->path . "actions/user/name.php");
		@include($CONFIG->path . "actions/user/password.php");
		@include($CONFIG->path . "actions/email/save.php");
		@include($CONFIG->path . "actions/user/language.php");
		@include($CONFIG->path . "actions/user/default_access.php");
		
	}
	
	//register actions *************************************************************
   
   		register_elgg_event_handler('init','system','users_init',0);
   		register_elgg_event_handler('pagesetup','system','users_pagesetup',0);
	
?>