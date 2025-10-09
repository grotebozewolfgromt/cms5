<?php
namespace dr\classes\types;

use DateTime;
use dr\classes\locale\TLocalisation;

/**
 * Description of TDateTime
 *
 * type to do with date and tiume
 * locale is an important factor
 * 
 * timestamp 0 is equivalent to: no time set
 * 
 * 19 juli 2012: TDateTime created
 * 19 juli 2012: functies vanuit lib_date hiernaartoe verhuisd
 * 19 juli 2012: interne timestamp
 * 19 juli 2012: TDateTime: isLeapYear() toegeovegd
 * 19 juli 2012: TDateTime: de TLocale class constrants worden gebruikt als default parameters
 * 19 juli 2012: TDateTime: functienamen veranderd in setDate, getDate, setDateTime, getDateTime
 * 19 juli 2012: TDateTime: setDate() en setTime() wijzigen alleen datum resp. tijd (bij wijzigen tijd, blijft datum behouden en andersom)
 * 19 juli 2012: TDateTime: setHour, minute etc. en getHour, minute etc. toegevoegd
 * 19 juli 2012: TDateTime: toegevoegd: isEarlier(), isLater(), equals()
 * 19 juli 2012: TDateTime: getTimestampAuto() zorgt voor minder code, omdat deze automatisch de goede timestamp teruggeeft (als interne timestamp == 0)
 * 20 juli 2012: TDateTime: some function renames to make their functionality clearer
 * 20 juli 2012: TDateTime: bugfix: getDateTimeAsString() gebruikte de php date ipv de 'inhouse' getDateAsStringByPHPDateFormat, waardoor deze de engelse uitvoer gaf. hierdoor gaf ook getDate en getTime een engelse output
 * 22 maart 2013: TDateTime: is nu child van php DateTime() class
 * 6 juli 2015: TDateTime: het gedrag van de klasse is gewijzigd. Bij timestamp == 0 wordt niet meer aangenomen dat het om de timestamp van het huidige tijdstip gaat. Er wordt vanuit gegaan dat timestamp 0 een ongeldige datum is. Locale aware teruggeven bij timestamp 0 geeft een lege string meer terug.
 * 6 juli 2015: TDateTime: diverse optimalisaties die de klasse beter laat performen bij timestamp 0
 * 13 sept 2019: TDateTime: calls to depraced now() replaced door time()
 * 10 jan 2020: TDateTime: isZero() added
 * 10 jan 2020: TDateTime: grondige Biereco verherbouwing zonder $objApplication uit CMS3
 * 11 jan 2020: TDateTime: setNow() toegevoegd
 * 16 jan 2020: TDateTime: subtractDays() toegevoegd
 * 23 jun 2021: TDateTime: setZero() toegevoegd
 * 25 jun 2021: TDateTime: subtractHours() toegevoegd
 * 
 * @author drenirie
 */
class TDateTime extends \DateTime
{
    
    /**
     *
     * @param int $iTimestamp: if 0 then no-time is assumed
     */
    public function __construct($iTimestamp = 0) 
    {
        parent::__construct();
        $this->setTimestamp($iTimestamp);
    }
    
    /**
     * returns if timestamp is 0 or not
     * this is equivalent to: this-is-not-a-valid-date or dont-bother-with-this-date
     * 
     * @return bool 
     */
    public function isZero()
    {
        return ($this->getTimestamp() == 0);   
    }

    /**
     * set current date to zero
     *
     * @return void
     */
    public function setZero()
    {
        $this->setTimestamp(0);
    }
    
    /**
     * returns if timestamp is 0 or not
     * this is equivalent to: this-is-not-a-valid-date or dont-bother-with-this-date
     * 
     * @return bool 
     */
    public function isEmpty()
    {
        return ($this->getTimestamp() == 0);   
    }


    /**
     * exactly the same as getTimestamp(), but this functions returns the current timestamp if the internal timestamp == 0 
     * @return int 
     */
    public function getTimestampAuto()
    {
        if ($this->getTimestamp() == 0)
            return time();
        else
            return $this->getTimestamp();        
    }    
    
    /**
     * replaces the php date function, by a one thats locale aware
     * only use this function when strictly necessary, it creates some overhead on top of the PHP date() function
     * if not necessary, please use the regular PHP date() function
     * 
     * @global Application $objApplication
     * @return string
     * @param string $sPHPDateFormat php-date()-function-format, for format characters see: http://php.net/manual/en/function.date.php (returns '' if timestamp == 0)
     */
//    public function getDateAsStringByPHPDateFormat($sPHPDateFormat)
//    {
//        $iTimestamp = $this->getTimestamp();   		
//        if ($iTimestamp == 0) //geen geldige datum
//                return '';
//		
//    		
//        global $objApplication;
//        $sResult = '';
//        
//        $iTimestamp = $this->getTimestampAuto();        
//        
//                
//        for ($iLetterCounter = 0; $iLetterCounter < strlen($sPHPDateFormat); $iLetterCounter++)
//        {
//            $sCurrentLetter = $sPHPDateFormat[$iLetterCounter];
//
//            switch ($sCurrentLetter) 
//            {
//                //letters die gewoon letterlijk doorgeschoven kunnen worden naar de PHP date() functie:
//                case 'd':
//                case 'j':
//                case 'N':
//                case 'S':
//                case 'w':
//                case 'z':
//                case 'W':
//                case 'm':
//                case 'n':
//                case 't':                            
//                case 'L':                            
//                case 'o':                            
//                case 'Y':                            
//                case 'y':                            
//                case 'b':                            
//                case 'G':                            
//                case 'g':                            
//                case 'h':                            
//                case 'H':                            
//                case 'i':                            
//                case 's':                            
//                case 'u':                            
//                case 'e':                            
//                case 'I': /* uppercase i */                           
//                case 'O': 
//                case 'P': 
//                case 'T': 
//                case 'Z': 
//                case 'c': 
//                case 'r': 
//                case 'U': 
//                    $sResult .= date($sCurrentLetter, $iTimestamp);
//                    break;
//                //letters met een locale betekenis
//                case 'D':
//                    $sResult .= $this->getDayNameShort();
//                    break;
//                case 'l': /* lowercase L */
//                    $sResult .= $this->getDayName();
//                    break;
//                case 'F':
//                    $sResult .= $this->getMonthName();
//                    break;
//                case 'M':
//                    $sResult .= $this->getMonthNameShort();
//                    break;
//                case 'a':
//                    $sEnglish = date($sCurrentLetter, $iTimestamp);
//                    $sResult .= strtolower($this->translateAMPM($sEnglish));
//                    break;
//                case 'A':
//                    $sEnglish = date($sCurrentLetter, $iTimestamp);
//                    $sResult .= strtoupper($this->translateAMPM($sEnglish));
//                    break;                                              
//
//                default:
//                    $sResult .= $sCurrentLetter;
//            }                    
//        }
//
//
//        
//        return $sResult;
//    }
    
    /**
     * getting locale aware shortname of the day 
     * 
     * when parameter $iDayNumberOfWeek == 0 then it uses internal timestamp
     * 
     * @global TLocalisation $objLocalisation
     * @param int $iDayNumberOfWeek 1= monday, 7=sunday
     * @return string  (returns '' if timestamp == 0)
     */
    public function getDayNameShort($iDayNumberOfWeek = 0)
    {
        global $objLocalisation;
        
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return '';
    	
        if ($iDayNumberOfWeek == 0)
            $iDayNumberOfWeek = date('N', $iTimestamp);
        
        switch ($iDayNumberOfWeek) 
        {
            case 1:
                return $objLocalisation->getSetting(TLocalisation::MONDAY_SHORT);
                break;
            case 2:
                return $objLocalisation->getSetting(TLocalisation::TUESDAY_SHORT);
                break;
            case 3:
                return $objLocalisation->getSetting(TLocalisation::WEDNESDAY_SHORT);
                break;
            case 4:
                return $objLocalisation->getSetting(TLocalisation::THURSDAY_SHORT);
                break;   
            case 5:
                return $objLocalisation->getSetting(TLocalisation::FRIDAY_SHORT);
                break;              
            case 6:
                return $objLocalisation->getSettings(TLocalisation::SATURDAY_SHORT);
                break;              
            case 7:
                return $objLocalisation->getSetting(TLocalisation::SUNDAY_SHORT);
                break;                                          
        }             
    }
    
    /**
     * getting locale aware name of the day 
     * @global TLocalisation $objLocalisation
     * @param int $iDayNumberOfWeek
     * @return string  (returns '' if timestamp == 0)
     */
    public function getDayName($iDayNumberOfWeek = 0)
    {
        global $objLocalisation;
        
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return '';

        
        if ($iDayNumberOfWeek == 0)
            $iDayNumberOfWeek = date('N', $iTimestamp);        
        
        switch ($iDayNumberOfWeek) 
        {
            case 1:
                return $objLocalisation->getSetting(TLocalisation::MONDAY);
                break;
            case 2:
                return $objLocalisation->getSetting(TLocalisation::TUESDAY);
                break;
            case 3:
                return $objLocalisation->getSetting(TLocalisation::WEDNESDAY);
                break;
            case 4:
                return $objLocalisation->getSetting(TLocalisation::THURSDAY);
                break;   
            case 5:
                return $objLocalisation->getSetting(TLocalisation::FRIDAY);
                break;              
            case 6:
                return $objLocalisation->getSetting(TLocalisation::SATURDAY);
                break;              
            case 7:
                return $objLocalisation->getSetting(TLocalisation::SUNDAY);
                break;                                          
        }             
    }    
    
    /**
     * getting locale aware name of the month 
     * when $iMonthNumberOfYear == 0 : use internal timestamp
     * 
     * @global TLocalisation $objLocalisation
     * @param int $iMonthNumberOfYear (1= january, 12= december)
     * @return string (returns '' if timestamp == 0)
     */
    public function getMonthName($iMonthNumberOfYear = 0)
    {
        global $objLocalisation;
        
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return '';
        
        if ($iMonthNumberOfYear == 0)
            $iMonthNumberOfYear = date('n', $iTimestamp);             
        
        switch ($iMonthNumberOfYear) 
        {
            case 1:
                return $objLocalisation->getSetting(TLocalisation::JANUARY);
                break;
            case 2:
                return $objLocalisation->getSetting(TLocalisation::FEBRUARY);
                break;
            case 3:
                return $objLocalisation->getSetting(TLocalisation::MARCH);
                break;
            case 4:
                return $objLocalisation->getSetting(TLocalisation::APRIL);
                break;   
            case 5:
                return $objLocalisation->getSetting(TLocalisation::MAY);
                break;              
            case 6:
                return $objLocalisation->getSetting(TLocalisation::JUNE);
                break;              
            case 7:
                return $objLocalisation->getSetting(TLocalisation::JULY);
                break;                                          
            case 8:
                return $objLocalisation->getSetting(TLocalisation::AUGUST);
                break;                                          
            case 9:
                return $objLocalisation->getSetting(TLocalisation::SEPTEMBER);
                break;                                          
            case 10:
                return $objLocalisation->getSetting(TLocalisation::OCTOBER);
                break;                                          
            case 11:
                return $objLocalisation->getSetting(TLocalisation::NOVEMBER);
                break;                                          
            case 12:
                return $objLocalisation->getSetting(TLocalisation::DECEMBER);
                break;                                          
            
        }             
    }     
    
    
    /**
     * internal function for retrieving months
     * when $iMonthNumberOfYear == 0 : use internal timestamp
     * 
     * @global TLocalisation $objLocalisation
     * @param int $iMonthNumberOfYear (1= january, 12= december)
     * @return string (returns '' if timestamp == 0)
     */
    public function getMonthNameShort($iMonthNumberOfYear = 0)
    {
        global $objLocalisation;
        
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum    	
                return '';
    	    	
        if ($iMonthNumberOfYear == 0)
            $iMonthNumberOfYear = date('n', $iTimestamp);        
        
        switch ($iMonthNumberOfYear) 
        {
            case 1:
                return $objLocalisation->getSetting(TLocalisation::JANUARY_SHORT);
                break;
            case 2:
                return $objLocalisation->getSetting(TLocalisation::FEBRUARY_SHORT);
                break;
            case 3:
                return $objLocalisation->getSetting(TLocalisation::MARCH_SHORT);
                break;
            case 4:
                return $objLocalisation->getSetting(TLocalisation::APRIL_SHORT);
                break;   
            case 5:
                return $objLocalisation->getSetting(TLocalisation::MAY_SHORT);
                break;              
            case 6:
                return $objLocalisation->getSetting(TLocalisation::JUNE_SHORT);
                break;              
            case 7:
                return $objLocalisation->getSetting(TLocalisation::JULY_SHORT);
                break;                                          
            case 8:
                return $objLocalisation->getSetting(TLocalisation::AUGUST_SHORT);
                break;                                          
            case 9:
                return $objLocalisation->getSetting(TLocalisation::SEPTEMBER_SHORT);
                break;                                          
            case 10:
                return $objLocalisation->getSetting(TLocalisation::OCTOBER_SHORT);
                break;                                          
            case 11:
                return $objLocalisation->getSetting(TLocalisation::NOVEMBER_SHORT);
                break;                                          
            case 12:
                return $objLocalisation->getSetting(TLocalisation::DECEMBER_SHORT);
                break;                                          
            
        }             
    }        
    
    /**
     * internal function for translating AM and PM
     * @global TLocalisation $objLocalisation
     * @param string $sAMPM
     * @return string 
     */
    private function translateAMPM($sAMPM)
    {
        global $objLocalisation;
        
        $sAMPM = strtolower($sAMPM); //alway lower case
        
        switch ($sAMPM) 
        {
            case 'am':
                return $objLocalisation->getSetting(TLocalisation::TIMEFORMAT_ANTEMEDIDIUM_SHORT);
                break;
            case 'pm':
                return $objLocalisation->getSetting(TLocalisation::TIMEFORMAT_POSTMERIDIUM_SHORT);
                break;            
        }             
    }      
    
    /**
     * returns the day of the year
     * 
     * sploit (http://www.phpfreaks.com/quickcode/getting_the_day_of_the_year/198.php)
     * 
     * @return int returns 0 is timestamp is 0 
     */
    public function dayOfYear() 
    {
        $iTimestamp = $this->getTimestamp();    	
        if ($iTimestamp == 0) //geen geldige datum
                return 0;
    		                      
        
        $iDay = date('j', $iTimestamp);
        $iMonth = date('n', $iTimestamp);
        $iYear = date('Y', $iTimestamp);

        $day_of_year = 0;
        for ($i = 1; $i < $iMonth; $i++) 
        {
            $timestamp = mktime(0, 0, 0, $i, 1, $iYear);
            $num_days_in_month = date("t", $timestamp);
            $day_of_year = $day_of_year + $num_days_in_month;
        }

        $day_of_year = $day_of_year + $iDay;

        return $day_of_year;
    }
    
    
    /**
    * het aantal dagen in februari van het huidige jaar (van de interne timestamp)
    * @author [some guy at php.net]
    * 
     * @param int $iYear if 0 the internal timestamp is used
    * @return int  hoeveel dagen er in februari (ivm het schrikkeljaar) returns 0 if timestamp is zero
    */
    public function daysInFeb($iYear = 0)
    {
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return 0;
    		      
        if ($iYear == 0)
            $iYear = date('Y', $iTimestamp);
        
        if ($iYear < 0) $iYear++;
        $iYear += 4800;

        if ( ($iYear % 4) == 0) {
                if (($iYear % 100) == 0) {
                        if (($iYear % 400) == 0) {
                                return(29);
                        } else {
                                return(28);
                        }
                } else {
                        return(29);
                }
        } else {
                return(28);
        }
    }

    
    /**
    * tests if this date is in a leap year
    * schrikkeljaar
    * 
    * @return bool true = leapYear, false = NO leapyear (returns also false if timestamp == 0)
    */    
    public function isLeapYear()
    {
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return false;
    		              
        
        $iLeap = date('L', $iTimestamp);

        if ($iLeap == 1)
            return true;
        else
            return false;
    }


    
    /**
    * aantal dagen in de maand van de current interne timestamp
    * @return int returns 0 if timestamp == 0
    */
    public function daysInMonth()
    {
        $iTimestamp = $this->getTimestamp();
        if ($iTimestamp == 0) //geen geldige datum
                return 0;       
        
        $iMonth = date('n', $iTimestamp);
        $iYear = date('Y', $iTimestamp);        
        
        $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
        if ($iMonth != 2) return $daysInMonth[$iMonth - 1];
        return (checkdate($iMonth, 29, $iYear)) ? 29 : 28;
    }    
    

    /**
    * convert a date into a readable string
    * @global TLocalisation $objLocalisation if format == '' then tries to determin the format by looking at TLocalisation::DATEFORMAT_DEFAULT
    * @param $sFormatLocaleSetting string with a TLocale constant setting, i.e. TLocalisation::DATEFORMAT_MEDIUM.  Empty string = use default
    * @param string $sPHPDateFormat like 'd-m-Y' default is ''  
    */    
    public function getDateAsString($sPHPDateFormat)
    {
        return $this->getDateTimeAsString($sPHPDateFormat);
    }
    
    /**
    * convert a date to a TDateTime 
     * 
    * @global TLocalisation $objLocalisation if format == '' then tries to determine the format by looking at TLocalisation::DATEFORMAT_DEFAULT
    * @param string $sDate (if '' then internaltimestamp is set to 0)
    * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
    */
    public function setDateAsString($sDate, $sPHPDateFormat = '')
    {
        global $objLocalisation;
        
        if ($sDate == '')
        {
            $this->setTimestamp(0);
            return;
        }
    		    		

        $iPreviousHour = 0;
        $iPreviousMinute = 0;
        $iPreviousSecond = 0;
        
        $iPreviousTimestamp = $this->getTimestamp();        
        if ($iPreviousTimestamp > 0)
        {
            $iPreviousHour = date('H', $iPreviousTimestamp);
            $iPreviousMinute = date('i', $iPreviousTimestamp);
            $iPreviousSecond = date('s', $iPreviousTimestamp);
        }
		        
        if ($sPHPDateFormat == '')            
            $sPHPDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT);

        $arrDate = date_parse_from_format($sPHPDateFormat, $sDate);
        $iYear = $arrDate['year'];
        $iMonth = $arrDate['month'];
        $iDay = $arrDate['day'];
        $iHour = $iPreviousHour; //only change date, not time
        $iMinute = $iPreviousMinute; //only change date, not time
        $iSecond = $iPreviousSecond;//only change date, not time
        $this->setTimestamp(mktime($iHour,$iMinute,$iSecond, $iMonth, $iDay, $iYear));                                                     
    }    
    
    
    /**
    * convert a date into a readable string
    *
    * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
    */    
    public function getTimeAsString($sPHPDateFormat = '')
    {
        return $this->getDateTimeAsString($sPHPDateFormat);
    }    
    
    /**
     * convert string into TDateTime
     * 
     * @global $objLocalisation
     * @param string $sDate (if '' then internaltimestamp is set to 0)
     * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT 
     */
    public function setTimeAsString($sTime, $sPHPDateFormat = '')
    {
        global $objLocalisation;
        
        if ($sTime == '')
        {
            $this->setTimestamp(0);
            return;
        }
    	

        $iPreviousYear = 0;
        $iPreviousMonth = 0;
        $iPreviousDay = 0;
                
        $iPreviousTimestamp = $this->getTimestamp();        
        if ($iPreviousTimestamp > 0)
        {
                $iPreviousYear = date('Y', $iPreviousTimestamp);
                $iPreviousMonth = date('m', $iPreviousTimestamp);
                $iPreviousDay = date('d', $iPreviousTimestamp);
        }
        

        if ($sPHPDateFormat == '')            
            $sPHPDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT);

        $arrDate = date_parse_from_format($sPHPDateFormat, $sTime);
        $iYear = $iPreviousYear; //only time date, not date
        $iMonth = $iPreviousMonth;//only time date, not date
        $iDay = $iPreviousDay;//only time date, not date
        $iHour = $arrDate['hour']; 
        $iMinute = $arrDate['minute'];
        $iSecond = $arrDate['second'];
        $this->setTimestamp(mktime($iHour,$iMinute,$iSecond, $iMonth, $iDay, $iYear));                  
                                
    }
    
    
    /**
    * convert a date and time into a readable string
    * the format is read from the locale
    *
    * @global TLocalisation $objLocalisation 
    * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
    * @return string is empty if timestamp == 0
    */    
    public function getDateTimeAsString($sPHPDateFormat = '')
    {
        global $objLocalisation;
        
        if ($this->getTimestamp() == 0)
            return '';
        
        if ($sPHPDateFormat == '')            
            $sPHPDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT).' '.$objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
        
        return $this->format($sPHPDateFormat);
    }    
         

    /**
    * convert a date to a TDateTime 
    * if input is not according to input or invalid date, 
    * the internal timestamp will be set to 0
    * 
    *
    * @global TLocalisation $objLocalisation 
    * @param string $sDate (if '' then internaltimestamp is set to 0)
    * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
    * @return string is empty if timestamp == 0
    */
    public function setDateTimeAsString($sDateTime, $sPHPDateFormat = '')
    {
        global $objLocalisation;
        
        if ($sDateTime == '')
        {
            $this->setTimestamp(0);
            return;
        }
        
        if ($sPHPDateFormat == '')            
            $sPHPDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT).' '.$objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
        
        $objTempDT = null;
        $objTempDT = \DateTime::createFromFormat($sPHPDateFormat, $sDateTime);
        if ($objTempDT)
            $this->setTimestamp($objTempDT->getTimestamp());
        else
            $this->setTimestamp(0);
        
        unset($objTempDT);
    }           
    
    /**
     * set day
     * 
     
     * @param int $iDay 
     */
    public function setDay($iDay)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        $iPreviousYear = date('Y', $iPreviousTimestamp);
        $iPreviousMonth = date('m', $iPreviousTimestamp);
        //$iPreviousDay = date('d', $iPreviousTimestamp);        
        $iPreviousHour = date('H', $iPreviousTimestamp);
        $iPreviousMinute = date('i', $iPreviousTimestamp);
        $iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iPreviousHour,$iPreviousMinute,$iPreviousSecond, $iPreviousMonth, $iDay, $iPreviousYear));                        
    }          
    
    /**
     * set month
     * @param int $iMonth 
     */
    public function setMonth($iMonth)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        $iPreviousYear = date('Y', $iPreviousTimestamp);
        //$iPreviousMonth = date('m', $iPreviousTimestamp);
        $iPreviousDay = date('d', $iPreviousTimestamp);        
        $iPreviousHour = date('H', $iPreviousTimestamp);
        $iPreviousMinute = date('i', $iPreviousTimestamp);
        $iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iPreviousHour,$iPreviousMinute,$iPreviousSecond, $iMonth, $iPreviousDay, $iPreviousYear));                        
    }       
    
    /**
     * set year
     * @param int $iYear 
     */
    public function setYear($iYear)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        //$iPreviousYear = date('Y', $iPreviousTimestamp);
        $iPreviousMonth = date('m', $iPreviousTimestamp);
        $iPreviousDay = date('d', $iPreviousTimestamp);        
        $iPreviousHour = date('H', $iPreviousTimestamp);
        $iPreviousMinute = date('i', $iPreviousTimestamp);
        $iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iPreviousHour,$iPreviousMinute,$iPreviousSecond, $iPreviousMonth, $iPreviousDay, $iYear));                        
    }       
    
    
    /**
     * set hour
     * @param int $iHours 
     */
    public function setHour($iHours)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        $iPreviousYear = date('Y', $iPreviousTimestamp);
        $iPreviousMonth = date('m', $iPreviousTimestamp);
        $iPreviousDay = date('d', $iPreviousTimestamp);        
        //$iPreviousHour = date('H', $iPreviousTimestamp);
        $iPreviousMinute = date('i', $iPreviousTimestamp);
        $iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iHours,$iPreviousMinute,$iPreviousSecond, $iPreviousMonth, $iPreviousDay, $iPreviousYear));                        
    }    
    
    /**
     * set minute
     * @param int $iMinutes 
     */
    public function setMinute($iMinutes)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        $iPreviousYear = date('Y', $iPreviousTimestamp);
        $iPreviousMonth = date('m', $iPreviousTimestamp);
        $iPreviousDay = date('d', $iPreviousTimestamp);        
        $iPreviousHour = date('H', $iPreviousTimestamp);
        //$iPreviousMinute = date('i', $iPreviousTimestamp);
        $iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iPreviousHour,$iMinutes,$iPreviousSecond, $iPreviousMonth, $iPreviousDay, $iPreviousYear));                        
    }       
    
    /**
     * set minute
     * @param int $iSecond 
     */
    public function setSecond($iSecond)
    {
        $iPreviousTimestamp = $this->getTimestamp();        

        $iPreviousYear = date('Y', $iPreviousTimestamp);
        $iPreviousMonth = date('m', $iPreviousTimestamp);
        $iPreviousDay = date('d', $iPreviousTimestamp);        
        $iPreviousHour = date('H', $iPreviousTimestamp);
        $iPreviousMinute = date('i', $iPreviousTimestamp);
        //$iPreviousSecond = date('s', $iPreviousTimestamp);        
               
        $this->setTimestamp(mktime($iPreviousHour,$iPreviousMinute,$iSecond, $iPreviousMonth, $iPreviousDay, $iPreviousYear));                        
    }     
    
    
    /**
     * set date and time as current day and time (now())
     */
    public function setNow()
    {
        $this->setTimestamp(time());        
    }
    
    /**
     * returns day of the current date/time
     * @return int 
     */
    public function getDay()
    {
        return (int)date('d', $this->getTimestamp());
    }
    
    /**
     * returns month of the current date/time
     * @return int 
     */
    public function getMonth()
    {
        return (int)date('m', $this->getTimestamp());
    }
    
    /**
     * returns year of the current date/time
     * @return int 
     */
    public function getYear()
    {
        return (int)date('Y', $this->getTimestamp());
    }    
    
    /**
     * returns hour of the current date/time
     * @return int 
     */
    public function getHour()
    {
        return (int)date('H', $this->getTimestamp());
    }        
    
    /**
     * returns minute of the current date/time
     * @return int 
     */
    public function getMinute()
    {
        return (int)date('i', $this->getTimestamp());
    }     
    
    /**
     * returns second of the current date/time
     * @return int 
     */
    public function getSecond()
    {
        return (int)date('s', $this->getTimestamp());
    }       
    
    /**
     * checks if date of this class is earlier than the parameter's
     * 1 second precision
     * if equal returns false
     * 
     * @param TDateTime $objEarlierThan
     * @return boolean 
     */
    public function isEarlier(TDateTime &$objEarlierThan)
    {
        $iTimeStampThisObject = 0;
        $iTimeStampOtherObject = 0;
        
        $iTimeStampThisObject = $this->getTimestamp();                
        $iTimeStampOtherObject = $objEarlierThan->getTimestamp();

        return ($iTimeStampThisObject < $iTimeStampOtherObject);
    }
    
    /**
     * checks if date of this class is later than the parameter's
     * 1 second precision
     * if equal returns false
     * 
     * @param TDateTime $objLaterThan
     * @return boolean 
     */
    public function isLater(TDateTime &$objLaterThan)
    {
        $iTimeStampThisObject = $this->getTimestamp();                
        $iTimeStampOtherObject = $objLaterThan->getTimestamp();
        
        return ($iTimeStampThisObject > $iTimeStampOtherObject);
    }    
    
    /**
     * checks if the timestamp is in the past
     * 
     * function compares the current timestamp with the internal timestamp of this object
     */
    public function isInThePast()
    {
        $iTimeStampThisObject = $this->getTimestamp();

        return time() > $iTimeStampThisObject;
    }
    
    /**
     * checks if the timestamp is in the future
     *
     * function compares the current timestamp with the internal timestamp of this object
     */
    public function isInTheFuture()
    {
        $iTimeStampThisObject = $this->getTimestamp();

        return time() < $iTimeStampThisObject;
    }
        
    
     /**
     * checks if date of this class is later than the parameter's
     * if $sPHPDateFormat == '' then 1 second precision :if exactly equal on the second, it returns true, otherwise false
     * if $sPHPDateFormat != '' then php date function is used. i.e. 
     * d-m-Y is precision to the day, H:i is precision to the second 
     * d-m-Y H:i is precision to the minute
     * H:i is precision to the minute, BUT the dates are not taken into consideration, only the time
     * 
     * @param string $sPHPDateFormat the php date format for determining precision
     * @param TDateTime $objEquals
     * @return boolean 
     */
    public function equals(TDateTime $objEquals, $sPHPDateFormat = '')
    {
        $iTimeStampThisObject = $this->getTimestamp();        
        $iTimeStampOtherObject = $objEquals->getTimestamp();
        
        if ($sPHPDateFormat != '')
        {
            $sDateTimeThisObject = date($sPHPDateFormat, $iTimeStampThisObject);
            $sDateTimeOtherObject = date($sPHPDateFormat, $iTimeStampOtherObject);
            
            return ($sDateTimeThisObject == $sDateTimeOtherObject);
        }
        else      
            return ($iTimeStampThisObject == $iTimeStampOtherObject);
    }   
    
    /**
     * checks if the current object is today 
     * @return bool 
     */
    public function isToday()
    {
        $objToday = new TDateTime();
        
        return $this->equals($objToday, 'd-m-Y');
        
    }
    
    /**
     * returns true is timestamp is not 0
     * 
     * @return boolean
     */
    public function exists()
    {
    	return ($this->getTimestamp() > 0);
    }
    
    /** 
     * checks if current object is tomorrow
     * 
     * @return bool 
     */
    public function isTomorrow()
    {
        $objTomorrow = new TDateTime();
        $objTomorrow->setTimestamp($objTomorrow->getTimestamp() + (DAY_IN_SECS));
        
        return $this->equals($objTomorrow, 'd-m-Y');
        
    }
    
    /**
     *checks if current object is yesterday
     * 
     * @return bool  
     */
    public function isYesterday()
    {
        $objYesterday = new TDateTime();
        $objYesterday->setTimestamp($objYesterday->getTimestamp() + (DAY_IN_SECS));
        
        return $this->equals($objYesterday, 'd-m-Y');        
    }
    
    /**
     * add days to current datetime
     * @param int $iNumberOfDays
     */
    public function addDays($iNumberOfDays)
    {    	
    	$this->add(new \DateInterval('P'.(int)$iNumberOfDays.'D'));
    }
    
    /**
     * add week to current datetime
     *
     * @param int $iNumberOfWeeks
     */
    public function addWeeks($iNumberOfWeeks)
    {
    	$this->add(new \DateInterval('P'.((int)$iNumberOfWeeks * 7).'D'));
    }
    
    /**
     * adding a month to current datatime
     * 
     * LOOK OUT: january 31 will not become februari 31
     * 
     * @param unknown $iNumberOfMonths
     */
    public function addMonth($iNumberOfMonths)
    {
  	$this->add(new \DateInterval('P'.(int)$iNumberOfMonths.'M'));
    }
    
    /**
     * adding hours
     * @param int $iNumberOfHours
     */
    public function addHour($iNumberOfHours)
    {
   	$this->add(new \DateInterval('PT'.(int)$iNumberOfHours.'H'));
    }
    
    /**
     * adding minutes
     * @param int $iNumberOfMinutes
     */
    public function addMinutes($iNumberOfMinutes)
    {
    	$this->add(new \DateInterval('PT'.(int)$iNumberOfMinutes.'M'));
    }    
    
    /**
     * adding seconds
     * @param int $iNumberOfSeconds
     */
    public function addSeconds($iNumberOfSeconds)
    {
    	$this->add(new \DateInterval('PT'.(int)$iNumberOfSeconds.'S'));
    }    
    
    /**
     * subtract days
     * @param int $iNumberOfDays
     */
    public function subtractDays($iNumberOfDays)
    {
        $this->sub(new \DateInterval('P'.(int)$iNumberOfDays.'D'));
    }

    /**
     * subtract hours
     * @param int $iNumberOfHours
     */
    public function subtractHours($iNumberOfHours)
    {
        $this->setTimestamp($this->getTimestamp() - ($iNumberOfHours * 3600)); //60*60=3600

    }

    /**
     * subtract minutes
     * @param int $iNumberOfMinutes
     */
    public function subtractMinutes($iNumberOfMinutes)
    {
        $this->setTimestamp($this->getTimestamp() - ($iNumberOfMinutes * 60)); //60*60=3600
    }    

    /**
     * subtract seconds
     * @param int $iSeconds
     */
    public function subtractSeconds($iSeconds)
    {
        $this->setTimestamp($this->getTimestamp() - ($iSeconds)); 
    }       


    /**
     * sets date as ISO 8601 string
     * don't confuse with DateTime::setISODate (https://www.php.net/manual/en/datetime.setisodate.php)
     */
    public function setISOString($sISO8601)
    {
        $this->setTimestamp(strtotime($sISO8601));
    }

    /**
     * gets date as ISO 8601 string
     */
    public function getISOString()
    {
        return date(DATE_ATOM, $this->getTimestamp());
    }
}

?>
