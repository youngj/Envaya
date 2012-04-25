<?php

/*
 * A phone number that an organization has registered for the SMS interface.
 * (Different from phone number(s) listed on the contact page)
 */
class UserPhoneNumber extends Model
{
    static $table_name = 'user_phone_numbers';
    static $table_attributes = array(
        'phone_number' => '',
        'last_digits' => 0,
        'user_guid' => null,
        'confirmed' => 0,
    );
    
    static $num_last_digits = 7;
    
    function save()
    {
        $this->last_digits = static::get_last_digits($this->phone_number);
        parent::save();
    }
    
    function get_user()
    {
        return User::get_by_guid($this->user_guid);
    }
    
    // ignore digits at beginning of string as they may be country/area codes 
    // which are more likely to cause a false negative if entered differently                
    static function get_last_digits($phone_number)
    {
        if (strlen($phone_number) > static::$num_last_digits)
        {
            $phone_number = substr($phone_number, -static::$num_last_digits);
        }
        return (int)$phone_number;
    }
}
