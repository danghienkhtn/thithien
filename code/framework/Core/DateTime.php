<?php
/**
 * @author      :   Linuxpham
 * @name        :   Core_DateTime
 * @version     :   201004
 * @copyright   :   My company
 * @todo        :   Using to date
 */
class Core_DateTime
{
    /**
     * Calculate difference in dates (dd-mm-yyyy)
     * @param <string> $startDate
     * @param <string> $endDate
     * @return <int>
     */
    public static function getDiff($startDate, $endDate)
    {
        $sDateI = explode('-', str_replace('/', '-', $startDate));
        $sDateF = explode('-', str_replace('/', '-', $endDate));
        $nStartDate = mktime(0, 0, 0, $sDateI[1], $sDateI[0], $sDateI[2]);
        $nFinalDate = mktime(0, 0, 0, $sDateF[1], $sDateF[0], $sDateF[2]);
        return ($nStartDate > $nFinalDate)?floor(($nStartDate - $nFinalDate)/86400):floor(($nFinalDate - $nStartDate)/86400);
    }

     /**
     * Calculate destance in dates (yyyy-mm-dd)
     * @param <string> $date1
     * @param <string> $date2
     * @return <int>
     */
    public static function getDistanceDate($date1, $date2)
    {
        $sDateI = explode('-', str_replace('/', '-', $date1));
        $sDateF = explode('-', str_replace('/', '-', $date2));
        $nStartDate = mktime(0, 0, 0, $sDateI[1], $sDateI[2], $sDateI[0]);
        $nFinalDate = mktime(0, 0, 0, $sDateF[1], $sDateF[2], $sDateF[0]);
        return ($nStartDate-$nFinalDate);
    }

    /**
     * The entry is the date of birth (dd-mm-yyyy)
     * @param <string> $dateBorned
     * @return <int>
     */
    public static function getAge($dateBorned)
    {
        $diff = self::getDiff($dateBorned, date("d-m-Y"));
        return number_format($diff/365, 0);
    }

    /**
     * Get the number of days in a month for a given year and calendar
     * @param <int> $month
     * @param <int> $year
     * @return <int>
     */
    public static function getTotalDateOfMonth($month=0, $year=0)
    {
        $month = empty($month)?date('m', time()):$month;
        $year = empty($year)?date('Y', time()):$year;
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    /**
     * Get date format string
     * @param <int> $timestamp
     * @param <int> $formatType
     * @param <boolean> $isFull
     * @return <string>
     */
    public static function getFormat($timestamp, $formatType=0, $isFull=0)
    {
        //Get all information
        $date = date('d', $timestamp);
        $month = date('m', $timestamp);
        $year = date('Y', $timestamp);
        $hour = date('h', $timestamp);
        $minute = date('i', $timestamp);
        $second = date('s', $timestamp);

        //Return format date
        switch($formatType)
        {            
            case 1: //English
                return ($isFull)?($month.'-'.$date.'-'.$year.' '.$hour.'-'.$minute.'-'.$second):($month.'-'.$date.'-'.$year);
                break;
            case 2: //Japanese
                return ($isFull)?($year.'-'.$month.'-'.$date.' '.$hour.'-'.$minute.'-'.$second):($year.'-'.$month.'-'.$date);
                break;
            default: //Vietnamese
                return ($isFull)?($date.'-'.$month.'-'.$year.' '.$hour.'-'.$minute.'-'.$second):($date.'-'.$month.'-'.$year);
                break;
        }
        
        return false;
    }

    /**
     * Add or sub date about number
     * @param <string> $startDate
     * @param <int> $number
     * @return <string>
     */
    public static function addDays($startDate, $number=1)
    {
        $sDateI = explode('-', str_replace('/', '-', $startDate));
        $nStartDate = mktime(0, 0, 0, $sDateI[1], $sDateI[0], $sDateI[2]);
        $nStartDate += $number*24*3600;
        return date('d-m-Y', $nStartDate);
    }

    /**
     * Add or sub month about number
     * @param <string> $startDate
     * @param <int> $number
     * @return <string>
     */
    public static function addMonths($startDate, $number=1)
    {
        //Get information
        $sDateI = explode('-', str_replace('/', '-', $startDate));
        $_day  = $sDateI[0];
        $_year  =   $sDateI[2];
        $_month = $sDateI[1];

        //Get div and mod of month
        $divNumber = (int)($number/12);
        $number = (int)($number%12);

        //If $divNumber > 0, Incretment of year
        if($divNumber > 0)
        {
            $_year += $divNumber;
        }

        //Add month
        $_month += $number;

        //Check month
        if($_month > 12)
        {
            $_month -= 12;
            $_year++;
        }

        //Get count days of month
        $numberDays = self::getTotalDateOfMonth($_month, $_year);

        //If day > numberDays
        if($_day > $numberDays)
        {
            $_day -= $numberDays;
        }

        //Incretment of month
        if($_day < $sDateI[0])
        {
            $_month++;
        }

        //Check month
        if($_month > 12)
        {
            $_month -= 12;
            $_year++;
        }

        return $_day.'-'.$_month.'-'.$_year;
    }

    /**
     * Add or sub year about number
     * @param <string> $startDate
     * @param <int> $number
     * @return <string>
     */
    public static function addYears($startDate, $number=1)
    {
        $sDateI = explode('-', str_replace('/', '-', $startDate));
        $nStartDate = mktime(0, 0, 0, $sDateI[1], $sDateI[0], $sDateI[2]);
        $nStartDate += $number*24*3600*365;
        return date('d-m-Y', $nStartDate);
    }

    /**
     * Get current time request
     * @return <int>
     */
    public static function getNowTimeStamp()
    {
        return (isset($_SERVER["REQUEST_TIME"]))?$_SERVER["REQUEST_TIME"]:mktime();
    }

    /**
     * Get last day of month in year
     * @param <int> $year
     * @param <int> $month
     * @return <int>
     */
    public static function getLastDayofMonth($year, $month)
    {
        for($day=31; $day>=28; $day--)
        {
            if(checkdate($month, $day, $year))
            {
                return $day;
            }
        }
        return 30;
    }
}

