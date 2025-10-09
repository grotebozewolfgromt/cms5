<?php
/**
 * In this library exist only date and time related functions
 * 
 * it uses TDateTime for some functions
 *
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 13 mrt 2020: hoursLeftOffer() added
 * 
 * @author Dennis Renirie
 */
use dr\classes\locale\TLocalisation;

//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');


/**
 * this function is an expansion of the php date() function.
 * it returns a locale aware formatted date
 * 
 * don't use if not strictly necessary, because of the overhead
 * 
 * @param string $sFormat for format characters see: http://php.net/manual/en/function.date.php
 * @param int $iTimestamp unix timestamp, 0 = current timestamp
 * @return string
 */
//function dateLocale($sFormat = '', $iTimestamp = 0)
//{
//    $objDateTime = new drenirie\framework\classes\Types\TDateTime($iTimestamp);
//    return $objDateTime->getDateAsStringByPHPDateFormat($sFormat);
//}

/**
 * convert a date into a readable string
 * function uses TDateTime for the format
 * 
 * @param int $iTimeStamp unix timestamp, 0 returns current timestamp
 * @param $sFormatLocaleSetting string with a TLocale constant setting, i.e. TLocalisation::DATEFORMAT_MEDIUM.  Empty string = use default
 */
//function dateToStr($iTimeStamp = 0, $sFormatLocaleSetting = '')
//{
//    $objDateTime = new drenirie\framework\classes\Types\TDateTime($iTimeStamp);    
//    return $objDateTime->getDateAsString($sFormatLocaleSetting);
//}



/**
 * convert a date to a unix timestamp
 *
 * @param string $sDate string with a date
 * @param $sFormatLocaleSetting string with a TLocale constant setting, i.e. TLocalisation::DATEFORMAT_MEDIUM.  Empty string = use default
 * @return integer
 */
//function strToDate($sDate, $sFormatLocaleSetting = '')
//{
//        $iTimestamp = 0;
//        $iTimestamp = strtotime($sDateTime);
//        $this->setTimestamp($iTimestamp);
//}

/**
 * convert a time+date into a readable string
 *
 * @param int $iTimeStamp unix timestamp, 0 returns current date and time
 * @param $sFormatLocaleSetting string with a TLocale constant setting, i.e. TLocalisation::DATETIME_FORMAT_REGULAR. Empty string = use default
 */
//function dateTimeToStr($iTimeStamp = 0, $sFormatLocaleSetting = '')
//{
//    $objDateTime = new drenirie\framework\classes\Types\TDateTime($iTimeStamp);    
//    return $objDateTime->getDateTimeAsString($sFormatLocaleSetting);
//}

/**
 * convert a date-time to a unix timestamp
 *
 * @param string $sDate
 * @param string $sFormat
 * @return int unix timestamp
 */
//function strToDateTime($sDate, $sFormatLocaleSetting = '')
//{
//    $objDateTime = new drenirie\framework\classes\Types\TDateTime();    
//    $objDateTime->setDateTimeAsString($sDate, $sFormatLocaleSetting);
//    return $objDateTime->getTimestamp();
//}

/**
 * checks if the date is a valid date
 * 
 * function needs globel $objLocalisation to retrieve default format 
 * if parameter $sFormatLocaleSetting == ''
 *
 * @param string $sDate
 * @param $sPHPDateFormat string with a TLocale constant setting, i.e. TLocalisation::DATEFORMAT_MEDIUM. Empty string = use default
 * @global TCountrysettings $objLocalisation
 * @return bool : true=ok, false=not valid
 */
function isValidDate($sDate, $sPHPDateFormat = '')
{
    global $objLocalisation;
    


    if ($sPHPDateFormat == '') //use default
        $sFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT);
    else //use parameter
        $sFormat = $sPHPDateFormat;


    $arrDate = date_parse_from_format($sFormat, $sDate);
     
    
    $iYear = $arrDate['year'];
    $iMonth = $arrDate['month'];
    $iDay = $arrDate['day'];

    if (is_numeric($iYear) && is_numeric($iMonth) && is_numeric($iDay))
        return checkdate($iMonth, $iDay, $iYear);
    else
        return false;

}


/**
 * checks if the date is a valid date
 * 
 * function needs globel $objLocalisation to retrieve default format 
 * if parameter $sFormatLocaleSetting == ''
 *
 * @param string $sDate
 * @param $sPHPDateFormat string with a TLocale constant setting, i.e. TLocalisation::TIMEFORMAT_ANTEMERIDIUM_FULL. Empty string = use default
 * @global TCountrysettings $objLocalisation
 * @return bool : true=ok, false=not valid
 */
function isValidTime($sTime, $sPHPDateFormat='') 
{
    global $objLocalisation;
    
    if ($sPHPDateFormat == '') //use default
        $sFormat = $objLocalisation->getSetting(TLocalisation::TIMEFORMAT_LONG);
    else //use parameter
        $sFormat = $sPHPDateFormat;

    $objTempDate = DateTime::createFromFormat("Y-m-d $sPHPDateFormat", "2017-12-01 $sTime"); //just pick a date doesn't matter which one
    return $objTempDate && $objTempDate->format($sPHPDateFormat) == $sTime;
}



/**
 * checks if a datetime value is value
 *
 * @param string $sDateTime
 * @param string $sFormat
 * @return bool : true=ok, false=not valid
 */
//function isValidDateTime($sDateTime, $sFormatLocaleSetting = '')
//{
//    global $objApplication;
//    
//    if ($objApplication)
//    {
//        if ($objApplication->getLocale())
//        {
//            if ($sFormatLocaleSetting == '') //use default
//                $sFormat = $objApplication->getSettings()->getSetting(TLocalisation::DATETIME_FORMAT_REGULAR);
//            else //use parameter
//                $sFormat = $objApplication->getSettings()->getSetting($sFormatLocaleSetting);
//
//            $arrDate = date_parse_from_format($sFormat, $sDateTime);
//            $iYear = $arrDate['year'];
//            $iMonth = $arrDate['month'];
//            $iDay = $arrDate['day'];
//            $iHour = $arrDate['hour'];
//            $iMinute = $arrDate['minute'];
//            $iSecond = $arrDate['second'];
//
//            if (is_numeric($iHour) && is_numeric($iMinute) && is_numeric($iSecond))
//            {
//                if (($iHour < 0) || ($iHour > 23))
//                    return false;
//                if (($iMinute < 0) || ($iMinute > 59))
//                    return false;
//                if (($iSecond < 0) || ($iSecond > 59))
//                    return false;
//            }
//            else
//                return false;
//
//            if (is_numeric($iYear) && is_numeric($iMonth) && is_numeric($iDay))
//                return checkdate($iMonth, $iDay, $iYear);
//            else
//                return false;
//        }
//        else
//            return false;
//    }
//    else
//        return false;            
//}


/**
 * Get info about given date IN PHP VERSIONS < 5.3
 * it works with only one format: d-m-Y H:i:s
 *
 * deze functie wordt toegevoegd als php versie < 5.3 is, want deze functie wordt pas geintroduceerd in 5.3
 *
 * @param string $format NOT USED it works with only one format: d-m-Y H:i:s
 * @param string $date
 * @return array
 */
//if (!function_exists('date_parse_from_format'))
//{
//    function date_parse_from_format($format,$date)
//    {
//        if (($format != 'd-m-Y H:i:s') && ($format != 'd-m-Y'))
//            error('date_parse_from_format(): other formats than "d-m-Y H:i:s" or "d-m-Y" are currently not supported !!! (wait until php 5.3)');
//
//        $arrResult = array();
//
//        $arrDateTime = explode(' ', $date);
//        $sTime = $arrDateTime[1];
//        $sDate = $arrDateTime[0];
//
//        $arrDate = explode('-', $sDate);
//        $arrResult['day'] = $arrDate[0];
//        $arrResult['month'] = $arrDate[1];
//        $arrResult['year'] = $arrDate[2];
//
//        if (strlen($sTime) > 0)
//        {
//            $arrTime = explode(':', $sTime);
//            $arrResult['hour'] = $arrTime[0];
//            $arrResult['minute'] = $arrTime[1];
//            $arrResult['second'] = $arrTime[2];
//        }
//        else
//        {
//            $arrResult['hour'] = 0;
//            $arrResult['minute'] = 0;
//            $arrResult['second'] = 0;
//        }
//
//        return $arrResult;
//    }
//}

/**
 * Return current Unix timestamp
 * @return integer
 */
function now()
{
    return time();
}

/**
 * returns the amount of hours that an offer is "valid"
 * 
 * This function returns the amount of hours between now and tomorrow at 0:00h 
 * This function will never return 0 (on purpose)
 * 
 * @return integer
 */
function hoursLeftOffer()
{
    $iHoursLeft = 0;
    $iSameTimeTomorrow = (time() + DAY_IN_SECS);
    $sSameTimeTomorrow = date('d-m-Y', $iSameTimeTomorrow);
    $objTomorrow = date_create_from_format ( 'd-m-Y' , $sSameTimeTomorrow);
    $objTomorrow->setTime(0, 0); //time doesn't matter, but you need to specify it otherwise it is always the same time tomorrow (dat is nogal doorzichtig)
    $iDateOfferValidUntil = $objTomorrow->getTimestamp();
    $iHoursLeft = round(($iDateOfferValidUntil - time()) / 60 / 60);

    if ($iHoursLeft == 0)
        $iHoursLeft = 1;    
    
    return $iHoursLeft;
}
?>
