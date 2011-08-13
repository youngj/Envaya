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
        $phone_numbers = preg_split('/[\+\,\|\;\/]+/', $phone_number_str);
                
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
}