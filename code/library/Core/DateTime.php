<?php
class Core_DateTime 
{
    /**
     * making unix time from datetime
     * 
     * @param string $datetime string date format as: 2015/07/29 08:00 or 2015-07-29 08:00
     * @return int
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function convertYmdDateTimeToUnixTime($datetime)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})(\s+)(\d{1,2})(:)(\d{1,2})$/';
        $match = preg_match($pattern, $datetime, $matches);
        
        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];
            $hour = $matches[7];
            $minute = $matches[9];
            $second = 0;
            
            return mktime($hour, $minute, $second, $month, $day, $year);
        }
        
        return 0;
    }
    public static function convertYmdStrToUnixTime($date)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/';
        $match = preg_match($pattern, $date, $matches);
        
        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];
            
            return mktime($hour = 0, $minute = 0, $second = 0, $month, $day, $year);
        }
        
        return 0;
    }
    
    /**
     * get time fromm y/m/d H:i format
     * 
     * @param string $datetime
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function getHourFromYmdTime($datetime)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})(\s+)(\d{1,2})(:)(\d{1,2})$/';
        $match = preg_match($pattern, $datetime, $matches);
        
        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];
            $hour = $matches[7];
            $minute = $matches[9];
            $second = 0;
            
            return $hour . ':' . $minute;
        }
        
        return NULL;        
    }
    /**
     * convert from ymd date to mdy date format(english datetime format)
     * 
     * @param string $date
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function convertYmdToMdy($date)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/';
        $match = preg_match($pattern, $date, $matches);
        
        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];

            return $month . '-' . $day . '-' . $year;
        }
        
        return NULL;
    }    
    /**
     * convert from ymd date to dmy date format(vietnamese datetime format)
     * 
     * @param string $date
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function convertYmdToDmy($date)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})$/';
        $match = preg_match($pattern, $date, $matches);

        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];

            return $day . '-' . $month . '-' . $year;
        }
        
        return NULL;
    }    
    /**
     * convert from ymd date to unixtime
     * 
     * @param string $date
     * @return string
     * @author Tai Le Thanh <tai.lt@vn.gnt-global.com>
     */
    public static function convertYmdToUnixtime($year, $month, $day)
    {
        return mktime($hour = 0, $minute = 0, $second = 0, $month, $day, $year);
    }
    public static function getDayFromYMDDatetime($datetime)
    {
        $pattern = '/^(\d{4})(\/|-)(\d{1,2})(\/|-)(\d{1,2})(\s+)(\d{1,2})(:)(\d{1,2})$/';
        $match = preg_match($pattern, $datetime, $matches);
        
        if (isset($matches) && count($matches)) {
            
            $year = $matches[1];
            $month = $matches[3];
            $day = $matches[5];
            $hour = $matches[7];
            $minute = $matches[9];
            $second = 0;
            
            return $day;
        }
        
        return NULL;        
    }
}

