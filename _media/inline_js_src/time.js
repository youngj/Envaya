function getTimeText(date, tzStr)
{
    var hours = date.getHours();
    return __['date:time'].
        replace('{hour}', pad2(hours)).
        replace('{hour12}', '' + ((hours % 12) || 12)).
        replace('{minute}', pad2(date.getMinutes())).
        replace('{tz}', (tzStr ? (' ' +tzStr) : '')).
        replace('{ampm}',' ' + (hours < 12 ? __['date:am'] : __['date:pm']));
}

function pad2(num)
{
    return (num < 10 ? '0' : '') + num;
}

function getDateText($date, $options)
{
    if (!$date)
    {   
        return '';
    }

    if (!$options)
    {
        $options = {};
    }
    
    var $always_show_year = $options['always_show_year'] || false;
    var $show_time = $options['show_time'] || false;
    var $tzStr = '';
    
    var $now = new Date();

    var $format = ($always_show_year || $now.getFullYear() != $date.getFullYear()) ? __['date:with_year'] : __['date:no_year'];
    
    var $dateStr = $format.
        replace('{month}', __["date:month:" + ($date.getMonth() + 1)]).
        replace('{day}', $date.getDate()).
        replace('{year}', $date.getFullYear());
    
    if ($show_time)
    {
        var $timeStr = getTimeText($date,'');
        
        return __['date:date_time'].
            replace('{date}', $dateStr),
            replace('{time}', $timeStr);
    }
    else
    {        
        return $dateStr;
    }
}

function friendlyTime($date, $options)
{
    var $now = new Date();    
    var $nowSec = $now.getTime()/1000;
    var $dateSec = $date.getTime()/1000;    
    var $diff = $nowSec - $dateSec;
    
    if ($diff < 60) 
    {
        return __["date:justnow"];
    } 
    else if ($diff < 3600) 
    {
        var $minutes = Math.round($diff / 60);
        return ($minutes > 1) 
            ? __['date:minutes_ago'].replace('%s', $minutes) 
            : __['date:minutes_ago:singular'];
    } 
    else if ($diff < 86400) 
    {
        var $hours = Math.round($diff / 3600);
        return ($hours > 1)
            ? __['date:hours_ago'].replace('%s', $hours)
            : __['date:hours_ago:singular'];
    } 
    else if ($diff < 604800) 
    {
        var $days = Math.round($diff / 86400);
        return ($days > 1)
            ? __['date:days_ago'].replace('%s', $days)
            : __['date:days_ago:singular'];
    } 
    else 
    {
        return getDateText($date, $options);
    }    
}
