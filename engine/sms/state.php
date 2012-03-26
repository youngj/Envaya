<?php

class SMS_State extends Model
{
    static $table_name = 'sms_state';
    static $table_attributes = array(
        'service_id' => '',
        'phone_number' => '',
        'user_guid' => 0,
        'time_updated' => 0,
        'value' => '',
    );
    
    protected $args = null;
    
    function save()
    {
        $this->time_updated = timestamp();
        $this->value = serialize($this->args);
        parent::save();
    }
    
    function reset()
    {
        $this->args = array();
    }
    
    function set_logged_in_user($user)
    {
        $this->user_guid = $user ? $user->guid : 0;
    }
    
    function get_logged_in_user()
    {
        return User::get_by_guid($this->user_guid);
    }
    
    function set($name, $value)
    {
        $args =& $this->get_args();
                
        if (isset($value))
        {
            $args[$name] = $value;
        }
        else
        {
            unset($args[$name]);
        }
    }
    
    function get($name)
    {
        $args =& $this->get_args();
        return @$args[$name];
    }
    
    function &get_args()
    {
        if ($this->args === null)
        {
            $this->args = unserialize($this->value) ?: array();
        }        
        return $this->args;
    }
}