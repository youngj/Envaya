<?php

class PhoneNumber
{    
    static function canonicalize($phone_number, $country_code = '')
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
            case 'us':
                if ($len == 10 && $phone_number[0] != '1')
                    $phone_number = "1$phone_number";
                return $phone_number;
            default:
                return $phone_number;
        }        
    }        
    
    static function canonicalize_multi($phone_number_str, $country_code = '')
    {
        // detect multiple phone numbers by commonly used separator characters 
        $phone_numbers = preg_split('/[\+\,\|\;\/\\\\a-z]+/', $phone_number_str);
                
        $phone_numbers_map = array();
        foreach ($phone_numbers as $phone_number)
        {
            $phone_number = static::canonicalize($phone_number, $country_code);
            if ($phone_number)
            {
                $phone_numbers_map[$phone_number] = true;
            }
        }
        return array_keys($phone_numbers_map);    
    }                
    
    static function get_international_prefix($country_code = '')
    {
        return '+';
    }
    
    static function get_dialed_number($phone_number, $user_country_code)
    {
        //$phone_number = static::canonicalize($phone_number);        
        $phone_country_code = static::get_country_code($phone_number);
        
        if ($phone_country_code != $user_country_code)
        {
            return static::get_international_prefix($user_country_code) . $phone_number;
        }
        else
        {
            return $phone_number;
        }
    }    
    
    static function get_country_code($phone_number)
    {
        $c1 = substr($phone_number, 0, 1);
        $c2 = substr($phone_number, 0, 2);
        $c3 = substr($phone_number, 0, 3);
        
        if ($c1 == '1')
        {
            // could be anywhere in NANP but just say US for now
            // http://en.wikipedia.org/wiki/List_of_North_American_Numbering_Plan_area_codes
            return 'us';
        }
        
        switch ($c3)
        {
            case '231':
                return 'lr';
            case '250':
                return 'rw';
            case '255':
                return 'tz';                
        }

        return null;
    }
}