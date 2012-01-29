<?php

/*
 * Represents a user account on Envaya, each with a unique username.
 *
 * (The Organization subclass is the most common type of user.)
 */
abstract class User extends Entity
{
    // approval
    const Approved = 1;
    const AwaitingApproval = 0;
    const Rejected = -1;
    
    static $table_name = 'users';
    static $query_class = 'Query_SelectUser';    
    static $table_base_class = 'User';
    static $admin_view = 'admin/entity/user';

    /* 
     * Constants for tracking the progress of a newly registered user 
     * through the setup process
     */
    const SetupStarted = 1;
    const SetupComplete = 5;    
    
    static $table_attributes = array(        
        'subtype_id' => '',
        'name' => '',
        'username' => '',
        'password' => '',        
        'salt' => '',
        'password_time' => 0,
        'email' => '',
        'phone_number' => '',
        'language' => '',
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
        'last_action' => 0,
    );
 
    static $mixin_classes = array(
        'Mixin_PageContainer',
    );  
  
    static $bad_usernames = array(
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

    function set_defaults()
    {
    }
    
    function init_default_widgets()
    {
    } 
 
    public function is_setup_complete()
    {
        return $this->setup_state >= User::SetupComplete;
    }    
    
    public function get_continue_setup_url()
    {
        return null;
    }        
 
    public function get_feed_names()
    {
        return array(
            FeedItem::make_feed_name(array()), // global feed
            FeedItem::make_feed_name(array('user' => $this->guid))
        );
    }
    
    public function get_title()
    {
        return $this->name;
    }

    public function get_url()
    {
        return "/{$this->username}";
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
        else
        {
            return $this->get_default_icon_props($size);
        }
    }
    
    function get_static_map_props($size = '')
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
    
    function get_default_icon_props($size = '')
    {
        if ($this->has_lat_long())
        {   
            return $this->get_static_map_props($size);
        } 
        else
        {
            return array(
                'url' => "/_media/images/defaultmedium.gif",
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
        return $this->approval >= User::Approved;
    }

    public function set_password($password)
    {
        $this->password = generate_password_hash($password);
        $this->password_time = timestamp();
    }
    
    public function get_password_age()
    {
        return timestamp() - ($this->password_time ?: $this->time_created);
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
        // don't reset per-IP rate since it would allow user to circumvent security
        Cache::get_instance()->delete(static::login_failures_cache_key($this->username));
    }
    
    private static function login_failures_cache_key($username)
    {
        return Cache::make_key("user_login_failures", strtolower($username));
    }
    
    private static function ip_login_failures_cache_key()
    {
        return Cache::make_key("ip_login_failures", Request::get_client_ip());        
    }
    
    /*
     * Returns the number of login attempts remaining for this username 
     * before the account is locked (from 0 to login_failure_limit).
     *
     * Always call before checking password.
     */
    static function get_login_attempts_remaining($username)
    {
        $failure_limit = Config::get('login:failure_limit');        
        $cache = Cache::get_instance();
        $num_user_failures = (int) $cache->get(static::login_failures_cache_key($username));
        return $failure_limit - $num_user_failures;
    }
    
    static function get_login_attempts_remaining_for_ip()
    {
        $failure_limit = Config::get('login:ip_failure_limit');       
        $cache = Cache::get_instance();        
        $num_ip_failures = (int) $cache->get(static::ip_login_failures_cache_key());    
        return $failure_limit - $num_ip_failures;
    }
        
    /*
     * Always call after checking password if password was incorrect.
     */
    static function log_login_failure($username, $user = null)
    {
        $failure_limit_interval = Config::get('login:failure_interval');
        $user_failure_limit = Config::get('login:failure_limit');
        $ip_failure_limit = Config::get('login:ip_failure_limit');        
    
        $cache = Cache::get_instance();
                
        $user_failures_key = static::login_failures_cache_key($username);
        $num_user_failures = (int)$cache->get($user_failures_key) + 1;
        $cache->set($user_failures_key, $num_user_failures, $failure_limit_interval * 60);        
              
        $ip_failures_key = static::ip_login_failures_cache_key();        
        $num_ip_failures = (int)$cache->get($ip_failures_key) + 1;
        $cache->set($ip_failures_key, $num_ip_failures, $failure_limit_interval * 60);        
                
        if ($num_user_failures >= $user_failure_limit || $num_ip_failures >= $ip_failure_limit)
        {            
            $ip_address = Request::get_client_ip();
            $country = GeoIP::get_country_name();                            
        
            $msg = "Username: {$username} ($num_user_failures attempts)\n".
                "IP Address: {$ip_address} [$country] ($num_ip_failures attempts)\n\n";

            if ($user)
            {
                $msg .= "Email: {$user->email}\n".
                    "Phone Number: {$user->get_primary_phone_number()}\n";;                
            }
        
            if ($num_user_failures >= $user_failure_limit)
            {
                $notify_admin_key = Cache::make_key("user_login_failure_notification", $username);

                // avoid repeatedly notifying admin for same user
                if (!$cache->get($notify_admin_key))
                {                                
                    OutgoingMail::create("Too many failed logins for {$username}", $msg)->send_to_admin();
                    $cache->set($notify_admin_key, timestamp(), 3600);
                }
            }
            else if ($num_ip_failures >= $ip_failure_limit)
            {        
                $notify_admin_key = Cache::make_key("ip_login_failure_notification", $ip_address);
                // avoid repeatedly notifying admin for same IP
                if (!$cache->get($notify_admin_key))
                {                
                    OutgoingMail::create("Too many failed logins from {$ip_address}", $msg)->send_to_admin();                
                    $cache->set($notify_admin_key, timestamp(), 3600);
                }
            }
        }
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
    
    function get_timezone_id()
    {
        if (!$this->timezone_id) 
        {
            if (!$this->has_lat_long())
            {
                return Config::get('geography:default_timezone');
            }
        
            try 
            {
                $geonames = Zend::geonames();
                $res = $geonames->timezone(array(
                    'lat' => $this->latitude,
                    'lng' => $this->longitude
                ));
                
                $this->timezone_id = @$res['timezoneId'] ?: Config::get('geography:default_timezone');
            }
            catch (Zend_Http_Client_Adapter_Exception $ex)
            {
                $this->timezone_id = Config::get('geography:default_timezone');            
            }
            catch (Bgy_Service_Geonames_Exception $ex)
            {
                $this->timezone_id = Config::get('geography:default_timezone');
            }
            
            $this->save();
        }
        return $this->timezone_id;
    }
        
    static function get_cache_key_for_username($username)
    {
        return Cache::make_key("guid_for_username", $username);
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
            
        $cache = Cache::get_instance();
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
    
    static function validate_username($username, $min_length = 3)
    {
        if (strlen($username) < $min_length)
        {
            throw new ValidationException(strtr(__('register:usernametooshort'), array('{min}' => $min_length)));
        }

        if (!preg_match('/^[a-z]/i', $username))
        {
            throw new ValidationException(sprintf(__('register:username_letter'), $username));
        }
        
        if (preg_match('/[^\w\-]/', $username, $matches))
        {
            throw new ValidationException(sprintf(__('register:invalidchars'), $username, $matches[0]));
        }

        $lower = strtolower($username);

        if (in_array($lower, static::$bad_usernames) || $username[0] == "_")
        {
            throw new ValidationException(sprintf(__('register:usernamenotvalid'), $username));
        }

        return $username;
    }

    function get_region_text($lang = null)
    {
        return $this->region ? __($this->region, $lang) : '';
    }
    
    function get_easy_password_words()
    {
        return array(
            $this->name, 
            $this->username,
            $this->email,
            $this->phone_number,
            $this->city,
            $this->get_region_text(),
            $this->get_country_text()
        );
    }
    
    static function validate_password($password, $password2, $easy_words, $min_strength = 2)
    {
        $min_length = 6;
    
        if (strlen($password) < $min_length)
        {
            throw new ValidationException(strtr(__('register:passwordtooshort'), array('{min}' => $min_length)));
        }       
                
        if (PasswordStrength::calculate($password, $easy_words) < $min_strength)
        {
            throw new ValidationException(__('register:password_too_easy'));
        }

        if (strcmp($password, $password2) != 0)
        {
            throw new ValidationException(__('register:passwords_differ'));
        }        
     
        return $password;
    }    
    
    public function get_country_text($lang = null)
    {
        if ($this->country)
        {
            return __("country:{$this->country}", $lang);
        }
        else
        {
            return '';
        }
    }    
    
    public function get_location_text($includeRegion = true, $lang = null)
    {
        $res = '';

        if ($this->city)
        {
            $res .= "{$this->city}, ";
        }
        if ($this->region && $includeRegion)
        {
            $regionText = $this->get_region_text($lang);

            if ($regionText != $this->city)
            {
                $res .= "$regionText, ";
            }
        }
        $res .= $this->get_country_text($lang);

        return $res;
    }
        
    function geocode_lat_long()
    {
        $latlong = Geography::geocode(
            $this->get_location_text(true, Config::get('language'))            
        );
        if ($latlong)
        {
            $this->set_lat_long($latlong['lat'], $latlong['long']);
            return true;
        }    
        return false;
    }
    
    protected $phone_numbers;

    function query_phone_numbers()
    {
        return UserPhoneNumber::query()->where('user_guid = ?', $this->guid);
    }
        
    function get_primary_phone_number()
    {
        $user_phone_number = $this->query_phone_numbers()->order_by('id')->get();
        if ($user_phone_number)
        {
            return $user_phone_number->phone_number;
        }
        return null;
    }
    
    function set_email($email)
    {
        $this->email = $email;
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
    }
    
    function save()
    {
		$phone_number_dirty = isset($this->dirty_attributes['phone_number']);
		$email_dirty = isset($this->dirty_attributes['email']);
		
        $res = parent::save();
        
        if ($phone_number_dirty)
        {            
            $newIds = array_map(function($op) { return $op->id; }, $this->phone_numbers);        
            
            // unsubscribe old primary phone number from comments, admin messages, etc.
            $this->delete_default_sms_subscriptions();
            
            foreach ($this->query_phone_numbers()
                ->where_not_in('id', $newIds)
                ->filter() 
                    as $old_phone_number)
            {
                // logout anyone using the removed phone number
                foreach (SMS_State::query()
                    ->where('phone_number = ?', $old_phone_number->phone_number)
                    ->filter() as $old_state)
                {
                    if ($old_state->user_guid == $this->guid)
                    {
                        $old_state->set_loggedin_user(null);
                        $old_state->save();
                    }
                }            
            
                $old_phone_number->delete();
            }
            
            foreach ($this->phone_numbers as $phone_number)
            {
                $phone_number->user_guid = $this->guid;
                $phone_number->save();
                
                // auto-login phones using the phone numbers that were added
                $sms_service = new SMS_Service_News();
                $state = $sms_service->get_state($phone_number->phone_number);
                if (!$state->get_logged_in_user())
                {
                    $state->set_loggedin_user($this);
                    $state->save();
                }
            }        
            
            // subscribe primary phone number to comments, admin messages, etc.
            $this->init_default_sms_subscriptions();
        }  

        if ($email_dirty)
        {
            $this->delete_default_email_subscriptions();
            $this->init_default_email_subscriptions();        
        }
        
        return $res;
    }

    function set_password_reset_code($code)
    {
        if ($code != null)
        {
            $this->set_metadata('password_reset_code', generate_password_hash($code));
            $this->set_metadata('password_reset_time', timestamp());        
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
        if (!$code_time || timestamp() - $code_time > 60*60*24*7)
        {
            return false;
        }
        
        $code_hash = $this->get_metadata('password_reset_code');    
        
        return ($code_hash && $code && $code_hash == crypt(strtoupper($code), $code_hash));
    }    
    
    /* 
     * WidgetContainer methods - a User is a container for Widgets
     * which are shown as pages on their site.
     */    
    function get_edit_url()
    {
        return $this->get_url() . "/dashboard";
    }           
    
    function get_default_widget_class_for_name($widget_name)
    {
        return Widget::get_default_class_for_name($widget_name)
            ?: Widget::get_default_class_for_name($widget_name, 'hidden_page')
            ?: 'Widget_Generic';
    }
    
    function get_entity_by_local_id($local_id, $show_disabled = false)
    {
        $row = Database::get_row("SELECT * FROM local_ids where user_guid = ? AND local_id = ?", array($this->guid, $local_id));
        if ($row)
        {
            return Entity::get_by_guid($row->guid, $show_disabled);
        }
        return null;
    }
    
    function delete_default_sms_subscriptions()
    {
        foreach (SMSSubscription::query_for_entity($this)
            ->where('owner_guid = ?', $this->guid)
            ->filter() as $subscription)
        {
            $subscription->delete();
        }
    }
    
    function delete_default_email_subscriptions()
    {
        foreach (EmailSubscription::query_for_entity($this)
            ->where('owner_guid = ?', $this->guid)
            ->filter() as $subscription)
        {
            $subscription->delete();
        }
    }
    
    function init_default_sms_subscriptions()    
    {
        foreach (SMSSubscription::$self_subscription_classes as $cls)
        {
            $cls::init_self_subscription($this);
        }        
    }
    
    function init_default_email_subscriptions()
    {
        foreach (EmailSubscription::$self_subscription_classes as $cls)
        {
            $cls::init_self_subscription($this);
        }
    }
    
    function update_scope()
    {
        $scope = UserScope::query()->where('container_guid = 0')->get();
        if ($scope)
        {
            $scope = $scope->find_scope($this);
        }        
        $this->set_container_entity($scope);        
    }
    
    private $permissions;
    
    function get_all_permissions()
    {
        if (!isset($this->permissions))
        {
            $this->permissions = Permission::query()->where('owner_guid = ?', $this->guid)->filter();
        }
        return $this->permissions;
    }
    
    function get_min_password_strength()
    {
        $required_password_strength = PasswordStrength::VeryWeak;                    
        foreach ($this->get_all_permissions() as $permission)
        {
            $min_password_strength = $permission->get_min_password_strength();
            if ($min_password_strength > $required_password_strength)
            {
                $required_password_strength = $min_password_strength;
            }
        }
        return $required_password_strength;
    }    
    
    function get_max_password_age()
    {   
        $required_password_age = null;    
        foreach ($this->get_all_permissions() as $permission)
        {    
            $max_password_age = $permission->get_max_password_age();
            if ($max_password_age && ($max_password_age < $required_password_age || !$required_password_age))
            {
                $required_password_age = $max_password_age;
            }            
        }
        return $required_password_age;
    }
    
    public function query_files()
    {    
        return UploadedFile::query()->where('container_guid=?',$this->guid);
    }    
    
    public function query_external_sites()
    {
        return ExternalSite::query()->where('container_guid = ?', $this->guid)->order_by('`order`');
    }           
}
