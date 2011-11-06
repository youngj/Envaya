<?php

/*
 * Base class for actions that are scheduled to execute at a particular time
 * and may recur at some arbitrary schedule between a start and (optional) end time.
 * Each ScheduledEvent instance is associated with a particular entity context 
 * (container_guid).
 *
 * Subclasses (in scheduledevent/) should define '_execute' to perform a specific task.
 *
 * The main use case is to send recurring notifications for a particular type of 
 * subscription. The notifier entity for the subscription is the ScheduledEvent 
 * itself, which notifies subscribers on the ScheduledEvent's parent/ancestor containers.
 *
 * Current schedules supported are:
 *  one-time
 *  every X days
 *  every X weeks
 *  every X months
 *
 * (Global recurring events with recurrence intervals less than 1 day typically
 * will be defined in crontab.php instead.)
 *
 */
abstract class ScheduledEvent extends Entity
{
    static $table_base_class = 'ScheduledEvent';
    static $table_name = 'scheduled_events';
    static $table_attributes = array(    
        'subtype_id' => '',
        'prev_time' => 0,
        'next_time' => null,
        'rrule' => '',
        'start_time' => 0,
        'end_time' => 0,
    );
    
    static function init_for_entity($entity, $options)
    {
        $cls = get_called_class();
        $event = new $cls();
        $event->set_container_entity($entity);
        foreach ($options as $prop => $val)
        {
            $event->$prop = $val;
        }        
        $event->calculate_next_time();
        $event->save();
        
        return $event;
    }        

    static function get_for_entity($entity)
    {
        return static::query_for_entity($entity)->get();
    }
    
    static function get_or_init_for_entity($entity, $defaults)
    {
        return static::get_for_entity($entity) ?: static::init_for_entity($entity, $options);
    }
    
    static function query_for_entity($entity)
    {
        return static::query()->where('container_guid = ?', $entity->guid);
    }
    
    function try_execute()
    {
        $time = timestamp();
        if (!$this->next_time || $this->next_time > $time)
        {
            error_log("scheduled event {$this->guid} cannot be triggered now");
            return false;
        }

        $notifier = $this->get_container_entity();
        if (!$notifier || !$notifier->is_enabled())
        {
            $this->next_time = null;
            $this->save();
            return false;
        }
        
        $this->execute();
        return true;
    }
    
    function execute()
    {
        $this->_execute();        
        $this->prev_time = timestamp();
        $this->calculate_next_time();
        $this->save();
    }
    
    abstract function _execute();

    function get_rrule_text()
    {
        return static::get_text_for_rrule($this->rrule);
    }
    
    function get_schedule_text()
    {
        return static::get_text_for_schedule($this->rrule, $this->start_time, $this->end_time);
    }
    
    static function get_text_for_schedule($rrule, $start_time, $end_time)
    {
        if (!$rrule)
        {
            return '';
        }        
    
        ob_start();
        $start_date = date('d-m-Y', $start_time);                
                
        if ($rrule == "COUNT=1")
        {
            echo $start_date;
        }
        else
        {        
            echo static::get_text_for_rrule($rrule);

            if ($end_time)
            {
                echo " from $start_date";
                echo " until " . date('d-m-Y', $end_time);
            }
            else
            {
                echo " starting $start_date";
            }
        }    
        return ob_get_clean();
    }
    
    static function get_text_for_rrule($rrule)
    {
        require_once Config::get('root') . "/vendors/When.php";
    
        $when = new When();
        $when->rrule($rrule);
        
        $interval = $when->interval;        
        
        if ($when->count == 1)
        {
            return "once";
        }
        
        switch ($when->frequency)
        {
            case 'MONTHLY':
                return ($interval == 1) ? "once a month" : "every $interval months";
            case 'WEEKLY':
                return ($interval == 1) ? "once a week" : "every $interval weeks";
            case 'YEARLY':
                return ($interval == 1) ? "once a year" : "every $interval years";
        }        
    }
    
    function get_when()
    {
        require_once Config::get('root') . "/vendors/When.php";
        
        $when = new When();
        
        $when->recur("@{$this->next_time}");
        $when->rrule($this->rrule);
        if (strpos($this->rrule, "FREQ=MONTHLY") !== false)
        {
            // need to say what day of month to send monthly notifications.
            // the day must exist in all months, so count days>28 from end of month        
            $start_monthday = (int)date('d', $this->start_time);
            if ($start_monthday > 28)
            {
                $start_month = (int)date('m', $this->start_time);
                $start_year = (int)date('Y', $this->start_time);
                $days_in_month = cal_days_in_month(CAL_GREGORIAN, $start_month, $start_year);                
                $start_monthday = ($start_monthday - $days_in_month) - 1;
            }
            $when->bymonthday(array($start_monthday));
        }
        
        if ($this->end_time)
        {
            $when->until("@{$this->end_time}");
        }    
        return $when;
    }

    function calculate_next_time()
    {
        if (!$this->rrule)
        {
            $this->next_time = null;
        }
        else if (!$this->next_time)
        {
            $this->next_time = $this->start_time;
        }
        else
        {
            $when = $this->get_when();                    
            
            $prev_time = $this->next_time;
            $next = $when->next();
            $this->next_time = ($next) ? $next->getTimestamp() : null;
            if ($this->next_time == $prev_time)
            {
                $next = $when->next();  
                $this->next_time = ($next) ? $next->getTimestamp() : null;                
            }
            
            if ($this->next_time && $this->next_time - $prev_time < 80000)
            {
                error_log("invalid notification frequency: {$this->rrule}");
                $this->next_time = null;
            }
        }
    }    
    
    function get_description()
    {
        return '';
    }
}