<?php

/*
 * A phone number that an organization has registered for the SMS interface.
 * (Different from phone number(s) listed on the contact page)
 */
class OrgPhoneNumber extends Model
{
    static $table_name = 'org_phone_numbers';
    static $table_attributes = array(
        'phone_number' => '',
        'last_digits' => 0,
        'org_guid' => 0,
        'confirmed' => 0,
    );
    
    static $num_last_digits = 7;
    
    function save()
    {
        $this->last_digits = static::get_last_digits($this->phone_number);
        parent::save();
    }
    
    function get_org()
    {
        return Organization::get_by_guid($this->org_guid);
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

    static function split_phone_number($phone_number_str, $country_code = '')
    {
        // detect multiple phone numbers by commonly used separator characters 
        $phone_numbers = preg_split('/[\+\,\|\;\/]+/', $phone_number_str);
                
        $phone_numbers_map = array();
        foreach ($phone_numbers as $phone_number)
        {
            $phone_number = static::canonicalize_phone_number($phone_number, $country_code);
            if ($phone_number)
            {
                $phone_numbers_map[$phone_number] = true;
            }
        }
        return array_keys($phone_numbers_map);    
    }
            
    static function canonicalize_phone_number($phone_number, $country_code = '')
    {
        if (!$phone_number)
            return null;
            
        // remove spaces, dashes, etc
        $phone_number = preg_replace('/[^\d]/','', $phone_number);
        
        $len = strlen($phone_number);
        if ($len < 7)
            return null;
        
        // convert from local style to international style with country code
        switch ($country_code)
        {            
            case 'tz':
                if ($len == 9) 
                    $phone_number = "0$phone_number";
                
                return preg_replace('/^(0|2550)/', '255', $phone_number);
            default:
                return $phone_number;
        }        
    }    
}
