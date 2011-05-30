<?php

    /**
     * Displays a UNIX timestamp in a friendly way (eg "less than a minute ago")
     *
     * @param int $time A UNIX epoch timestamp
     * @return string The friendly time
     */
    function friendly_time($time, $options = null) 
    {
        $diff = time() - ((int) $time);
        if ($diff < 60) 
        {
            return __("date:justnow");
        }
        else if ($diff < 3600) 
        {
            $minutes = round($diff / 60);
            return ($minutes > 1) 
                ? sprintf(__('date:minutes_ago'), $minutes) 
                : __('date:minutes_ago:singular');
        } 
        else if ($diff < 86400) 
        {
            $hours = round($diff / 3600);
            return ($hours > 1)
                ? sprintf(__('date:hours_ago'), $hours)
                : __('date:hours_ago:singular');
        } 
        else if ($diff < 604800) 
        {
            $days = round($diff / 86400);
            return ($days > 1)
                ? sprintf(__('date:days_ago'), $days)
                : __('date:days_ago:singular');
        } 
        else 
        {
            return get_date_text($time, $options);
        }
    }    
    
    /*
     * Returns a localized string representing a date
     *
     * $dateTime is either a DateTime object, or a unix timestamp.
     */    
    function get_date_text($dateTime, $options = null)
    {
        if (!$dateTime)
        {   
            return '';
        }

        if (!($dateTime instanceof DateTime))
        {
            $dateTime = new DateTime("@{$dateTime}");                
        }        
        
        if (!$options)
        {
            $options = array();
        }
                
        $alwaysShowYear = isset($options['alwaysShowYear']) ? $options['alwaysShowYear'] : false;
        $timezoneID = isset($options['timezoneID']) ? $options['timezoneID'] : null;
        $showTime = isset($options['showTime']) ? $options['showTime'] : false;
        $showDate = isset($options['showDate']) ? $options['showDate'] : true;
        
        if ($timezoneID)
        {            
            $dateTime->setTimeZone(new DateTimeZone($timezoneID));
        }

        if ($showDate)
        {        
            $now = new DateTime();
            
            $year = $dateTime->format('Y');
            
            $format = ($alwaysShowYear || $now->format('Y') != $year) ? __('date:with_year') : __('date:no_year');
            
            $dateStr = strtr($format, array(
                '{month}' => __("date:month:".$dateTime->format('n')),
                '{day}' => $dateTime->format('j'),
                '{year}' => $year,
            ));            
        }

        if ($showTime)
        {
            $timeStr = strtr(__('date:time'), array(
                '[hour]' => $dateTime->format('H'),
                '[hour12]' => $dateTime->format('g'),
                '{minute}' => $dateTime->format('i'),
                '[ampm]' => (($dateTime->format('a') == 'am') ? __('date:am') : __('date:pm'))
            ));
            
            if ($timezoneID)
            {
                $timeStr = strtr(__('date:time_with_tz'), array(
                    '{time}' => $timeStr,
                    '{tz}' => $dateTime->format('T'),
                ));
            }
        }
          
        if ($showDate && $showTime)
        {
            return strtr(__('date:date_time'), array(
                '{date}' => $dateStr,
                '{time}' => $timeStr,
            ));
        }
        else if ($showTime)
        {
            return $timeStr;
        }
        else if ($showDate)
        {
            return $dateStr;
        }
        else
        {
            return '';
        }
    }