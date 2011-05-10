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
    
    function get_date_text($time, $options = null)
    {
        if (!$time)
        {   
            return '';
        }

        if (!$options)
        {
            $options = array();
        }
        
        $always_show_year = @$options['always_show_year'] ?: false;
        $timezone_id = @$options['timezone_id'];
        $show_time = @$options['show_time'] ?: false;
        $tzStr = '';
        
        if ($timezone_id)
        {
            $dateTime = new DateTime("@$time");        
            $tz = new DateTimeZone($timezone_id);
            $tzOffset = $tz->getOffset($dateTime);
            
            $dateTime->setTimeZone($tz);
            $tzStr = $dateTime->format('T');
            
            if ($tzOffset)
            {
                $time += $tzOffset;
            }
        }        
        
        $date = getdate($time);
        $now = getdate();

        $format = ($always_show_year || $now['year'] != $date['year']) ? __('date:with_year') : __('date:no_year');
        
        $dateStr = strtr($format, array(
            '{month}' => __("date:month:{$date['mon']}"),
            '{day}' => $date['mday'],
            '{year}' => $date['year'],            
        ));
        
        if ($show_time)
        {
            $timeStr = get_time_text($date, $tzStr);
            
            return strtr(__('date:date_time'), array(
                '{date}' => $dateStr,
                '{time}' => $timeStr,
            ));
        }
        else
        {        
            return $dateStr;
        }
    }
    
    function get_time_text($date, $tzStr)
    {
        $hours = $date['hours'];
        return strtr(__('date:time'), array(
            '{hour}' => sprintf("%02d", $hours),
            '{hour12}' => ('' . (($hours % 12) ?: 12)),
            '{minute}' => sprintf("%02d", $date['minutes']),
            '{tz}' => ($tzStr ? (' ' . $tzStr) : ''),
            '{ampm}' => (' ' . ($hours < 12 ? __('date:am') : __('date:pm')))
        ));
    }