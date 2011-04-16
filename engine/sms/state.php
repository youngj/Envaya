<?php

class SMS_State extends Model
{
    static $table_name = 'sms_state';
    static $table_attributes = array(
        'phone_number' => '',
        'time_updated' => 0,
        'args_json' => '[]',
    );
    
    function save()
    {
        $this->time_updated = time();
        parent::save();
    }
    
    function get_org()
    {
        return Organization::get_by_guid($this->org_guid);
    }
    
    function reset()
    {
        $this->set_args(array());
    }
    
    function set_arg($name, $value)
    {
        $args = $this->get_args();
        $args[$name] = $value;
        $this->set_args($args);
    }
    
    function get_arg($name)
    {
        $args = $this->get_args();
        return @$args[$name];
    }
    
    function get_args()
    {
        return json_decode($this->args_json, true);
    }

    function set_args($args)
    {
        $this->args_json = json_encode($args);
    }
}