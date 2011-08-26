<?php

class SMS_State extends Model
{
    static $table_name = 'sms_state';
    static $table_attributes = array(
        'service_id' => '',
        'phone_number' => '',
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
    
    function set($name, $value)
    {
        $args =& $this->get_args();
        $args[$name] = $value;
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
            $this->args = unserialize($this->value);
        }        
        return $this->args;
    }
}