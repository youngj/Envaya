<?php

abstract class TimeUtil
{
    static function seconds_since_midnight($time, $tz)
    {
        $dtstart = new DateTime("@{$time}");
        if ($tz)
        {
            $dtstart->setTimezone($tz);               
        }
        
        return 3600 * ((int)$dtstart->format('H')) + 60 * ((int)$dtstart->format('i')) + (int)$dtstart->format('s');
    }    

    static function local_midnight($time = null, $tz = null)
    {
        $local_date = self::local_date($time, $tz);        
        $local_date->setTime(0,0,0);
        return $local_date;
    }
    
    static function local_date($time = null, $tz = null)
    {
        $date_time = new DateTime("@" . ($time ?: timestamp()));
        $date_time->setTimeZone($tz ?: self::get_local_time_zone());
        return $date_time;
    }
    
    static function get_local_time_zone()
    {
        return new DateTimeZone(date_default_timezone_get() ?: 'UTC');
    }
    
    static $cur_year;
    
    static function format_opt_year($date, $same_year_format = 'D M j', $diff_year_format = 'D M j, Y')
    {
        if (!self::$cur_year)
        {
            self::$cur_year = self::local_date()->format('Y');
        }
        
        if ($date->format('Y') == self::$cur_year)
        {
            return $date->format($same_year_format);   
        }
        else
        {
            return $date->format($diff_year_format);
        }
    }
    
    static function format_short($date)
    {
        $now = new DateTime();
        $now->setTimezone($date->getTimezone());
        
        if ($now->format("n/j/Y") == $date->format("n/j/Y"))
        {
            return $date->format("g:i a");
        }
        else if ($now->format("Y") == $date->format("Y"))
        {
            return $date->format("n/j g:i a");
        }
        else
        {
            return $date->format("n/j/y g:i a");
        }
    }
    
    static function format_minutes($minutes, $format = 'g:i A')
    {
        $hour = (int)($minutes / 60);
        $minute = $minutes % 60;
        
        $date = new DateTime();
        $date->setTime($hour,$minute,0);
        return $date->format($format);
    }
    
    static function parse_time_of_day($time_str)
    {
        if (preg_match('#^(?P<hour>\d+)(:(?P<minute>\d+))?\s*(?P<ampm>a|p|am|pm)?$#i', $time_str, $match))
        {
            $hour = (int)$match['hour'];
            $minute = (int)$match['minute'];
            $ampm = @$match['ampm'];
            
            if ($ampm)
            {
                $hour24 = $hour % 12 + (strtolower($ampm[0]) == 'p' ? 12 : 0);
            }
            else
            {
                $hour24 = $hour % 24;
            }
            
            return array($hour24, $minute % 60);
        }
        else
        {
            throw new ValidationException("Invalid time of day.");
        }
    }    
    
    static function parse_minute_of_day($time_str)
    {
        list($hour24, $minute) = self::parse_time_of_day($time_str);
        return $hour24 * 60 + $minute;
    }
}