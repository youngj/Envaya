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
            return __("friendlytime:justnow");
        } 
        else if ($diff < 3600) 
        {
            $minutes = round($diff / 60);
            return ($minutes > 1) 
                ? sprintf(__("friendlytime:minutes"), $minutes) 
                : __("friendlytime:minutes:singular");
        } 
        else if ($diff < 86400) 
        {
            $hours = round($diff / 3600);
            return ($hours > 1)
                ? sprintf(__("friendlytime:hours"), $hours)
                : __("friendlytime:hours:singular");               
        } 
        else if ($diff < 604800) 
        {
            $days = round($diff / 86400);
            return ($days > 1)
                ? sprintf(__("friendlytime:days"), $days)
                : __("friendlytime:days:singular");
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
            $timeStr = strtr(__('date:time'), array(
                '{hour}' => $date['hours'],
                '{minute}' => sprintf("%02d", $date['minutes']),
                '{tz}' => $tzStr
            ));
            
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
