<?php
/**
 * WARNING:
 * This is NOT the standard version of Zend/Date.php.
 *
 * The standard version of Zend/Date.php is 194 KB (4955 lines)
 * and has dependencies on the Zend Locale framework, which has about 10 MB of data (seriously).
 *
 * The Zend RSS feed parser has a dependency on Zend_Date, but doesn't use much of
 * of the Zend_Date functionality. This version is a stub that lets us use the RSS feed parser
 * and convert dates to UNIX timestamps.
 *
 * =====
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Date
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 * @version   $Id: Date.php 23775 2011-03-01 17:25:24Z ralph $
 */

require_once 'Zend/Date/DateObject.php';

class Zend_Date extends Zend_Date_DateObject
{
    // Class wide Date Constants
    const DAY               = 'dd';
    const DAY_SHORT         = 'd';
    const DAY_SUFFIX        = 'SS';
    const DAY_OF_YEAR       = 'D';
    const WEEKDAY           = 'EEEE';
    const WEEKDAY_SHORT     = 'EEE';
    const WEEKDAY_NARROW    = 'E';
    const WEEKDAY_NAME      = 'EE';
    const WEEKDAY_8601      = 'eee';
    const WEEKDAY_DIGIT     = 'e';
    const WEEK              = 'ww';
    const MONTH             = 'MM';
    const MONTH_SHORT       = 'M';
    const MONTH_DAYS        = 'ddd';
    const MONTH_NAME        = 'MMMM';
    const MONTH_NAME_SHORT  = 'MMM';
    const MONTH_NAME_NARROW = 'MMMMM';
    const YEAR              = 'y';
    const YEAR_SHORT        = 'yy';
    const YEAR_8601         = 'Y';
    const YEAR_SHORT_8601   = 'YY';
    const LEAPYEAR          = 'l';
    const MERIDIEM          = 'a';
    const SWATCH            = 'B';
    const HOUR              = 'HH';
    const HOUR_SHORT        = 'H';
    const HOUR_AM           = 'hh';
    const HOUR_SHORT_AM     = 'h';
    const MINUTE            = 'mm';
    const MINUTE_SHORT      = 'm';
    const SECOND            = 'ss';
    const SECOND_SHORT      = 's';
    const MILLISECOND       = 'S';
    const TIMEZONE_NAME     = 'zzzz';
    const DAYLIGHT          = 'I';
    const GMT_DIFF          = 'Z';
    const GMT_DIFF_SEP      = 'ZZZZ';
    const TIMEZONE          = 'z';
    const TIMEZONE_SECS     = 'X';
    const ISO_8601          = 'c';
    const RFC_2822          = 'r';
    const TIMESTAMP         = 'U';
    const ERA               = 'G';
    const ERA_NAME          = 'GGGG';
    const ERA_NARROW        = 'GGGGG';
    const DATES             = 'F';
    const DATE_FULL         = 'FFFFF';
    const DATE_LONG         = 'FFFF';
    const DATE_MEDIUM       = 'FFF';
    const DATE_SHORT        = 'FF';
    const TIMES             = 'WW';
    const TIME_FULL         = 'TTTTT';
    const TIME_LONG         = 'TTTT';
    const TIME_MEDIUM       = 'TTT';
    const TIME_SHORT        = 'TT';
    const DATETIME          = 'K';
    const DATETIME_FULL     = 'KKKKK';
    const DATETIME_LONG     = 'KKKK';
    const DATETIME_MEDIUM   = 'KKK';
    const DATETIME_SHORT    = 'KK';
    const ATOM              = 'OOO';
    const COOKIE            = 'CCC';
    const RFC_822           = 'R';
    const RFC_850           = 'RR';
    const RFC_1036          = 'RRR';
    const RFC_1123          = 'RRRR';
    const RFC_3339          = 'RRRRR';
    const RSS               = 'SSS';
    const W3C               = 'WWW';

    public function __construct($date = null, $part = null, $locale = null)
    {
        if ($date)
        {
            $this->set($date);
        }
    }
 
    public function set($date, $part = null, $locale = null)
    {
        $date = strtotime($date) ?: $date;
        $this->setUnixTimestamp($date);
        return $this;
    }
    
    public function __call($name, $args)
    {
        throw new NotImplementedException($name);
    }
    
    public function getTimestamp()
    {
        return $this->getUnixTimestamp();
    }
}
