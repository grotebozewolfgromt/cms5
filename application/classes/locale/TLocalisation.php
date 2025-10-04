<?php
namespace dr\classes\locale;

use dr\classes\patterns\TObject;
use dr\classes\files\TIni;

/**
 * Description of TLocalisation
 * 
 * 
 * 22 juli 2012: TLocalisation created
 * 24 apr 2024: TLocalisation: setSetting() added
 * 24 apr 2024: TLocalisation: loadFromFile() parameter added as protection to be loaded twice (and wasting system resources)
 * 24 apr 2024: TLocalisation: timezone is set in this class: date_default_timezone_set --> this is done to ensure that the timezone is always set
 * 24 apr 2024: TLocalisation: rename getCountrySetting() -> getSetting()
 * 
 * @author drenirie
 */
class TLocalisation
{
    private $arrCountrySettings = array();
    private $sFilePath = '';
    private $bFileLoaded = false; //helper boolean: determine if the file is loaded. So we can ONLY use it when it is requested by the code (otherwise it only takes up system resources)
    
    //class constants and their key in the ini file  
    const SEPARATOR_THOUSAND = 'separator-thousand';
    const SEPARATOR_DECIMAL = 'separator-decimal';
    const UNITS_OF_MEASUREMENT = 'units-of-measurement';//'metric' of 'imperial'
    
    const TIMEZONE = 'timezone';
    const CALENDAR_TYPE = 'calendar-type'; //'gregorian', 'julian' etc.  
    const DATEFORMAT_LONG = 'dateformat-long';
    const DATEFORMAT_SHORT = 'dateformat-short';
    const TIMEFORMAT_LONG = 'timeformat-long';
    const TIMEFORMAT_SHORT = 'timeformat-short';
    
    const FIRST_DAY_OF_THE_WEEK = 'first-day-of-the-week'; //0=monday, 1=tuesday, 6=sunday etc.

    const JANUARY = 'January';
    const FEBRUARY = 'February';
    const MARCH = 'March';
    const APRIL = 'April';
    const MAY = 'May';
    const JUNE = 'June';
    const JULY = 'July';
    const AUGUST = 'August';
    const SEPTEMBER = 'September';
    const OCTOBER = 'October';
    const NOVEMBER = 'November';
    const DECEMBER = 'December';
    const JANUARY_SHORT = 'january-short';
    const FEBRUARY_SHORT = 'february-short';
    const MARCH_SHORT = 'march-short';
    const APRIL_SHORT = 'april-short';
    const MAY_SHORT = 'may-short';
    const JUNE_SHORT = 'june-short';
    const JULY_SHORT = 'july-short';
    const AUGUST_SHORT = 'august-short';
    const SEPTEMBER_SHORT = 'september-short';
    const OCTOBER_SHORT = 'october-short';
    const NOVEMBER_SHORT = 'november-short';
    const DECEMBER_SHORT = 'december-short';
    
    const SUNDAY = 'sunday';
    const MONDAY = 'monday';
    const TUESDAY = 'tuesday';
    const WEDNESDAY = 'wednesday';
    const THURSDAY = 'thursday';
    const FRIDAY = 'friday';
    const SATURDAY = 'saturday';
    const SUNDAY_SHORT = 'sunday-short';
    const MONDAY_SHORT = 'monday-short';
    const TUESDAY_SHORT = 'tuesday-short';
    const WEDNESDAY_SHORT = 'wednesday-short';
    const THURSDAY_SHORT = 'thursday-short';
    const FRIDAY_SHORT = 'friday-short';
    const SATURDAY_SHORT = 'saturnday-short';

    const DAY_ONE = 'day-one';
    const DAY_OTHER = 'day-other';
    const MONTH_ONE = 'month-one';
    const MONTH_OTHER = 'month-other';
    const YEAR_ONE = 'year-one';
    const YEAR_OTHER = 'year-other';
    const HOUR_ONE = 'hour-one';
    const HOUR_OTHER = 'hour-other';
    const MINUTE_ONE = 'minute-one';
    const MINUTE_OTHER = 'minute-other';
    const SECOND_ONE = 'second-one';
    const SECOND_OTHER = 'second-other';
    

    
    public function __construct() 
    {
        $this->init();
    }
    
    public function init()
    {
        if ($this->arrCountrySettings)
            unset($this->arrCountrySettings);
        $this->arrCountrySettings = array();          
        
    }
    
    
    public function setFileName($sFilePath)
    {
        $this->sFilePath = $sFilePath;
    }
    
    public function getFileName()
    {
        return $this->sFilePath;
    }
    
    /**
     * read the locale settings from the countrysettings ini file 
     * 
     * @return bool
     */
    public function loadFromFile($bOnlyLoadOnce = true)
    {
        $sFileName = '';
        $sFileName = $this->getFileName();
        
        //prevent file from being loaded twice by accident
        if ($bOnlyLoadOnce)
            if ($this->bFileLoaded) //if file already loaded
                return true;

        $objLocalisationFile = new TIni(false, false);
        if (is_file($sFileName))
        {
            if (!$objLocalisationFile->loadFromFile($sFileName))
                return false;
        }        

       
        //====numbers
        $this->arrCountrySettings[TLocalisation::SEPARATOR_THOUSAND] = $objLocalisationFile->read('numbers', TLocalisation::SEPARATOR_THOUSAND, '.');
        $this->arrCountrySettings[TLocalisation::SEPARATOR_DECIMAL] = $objLocalisationFile->read('numbers', TLocalisation::SEPARATOR_DECIMAL, ',');
        $this->arrCountrySettings[TLocalisation::UNITS_OF_MEASUREMENT] = $objLocalisationFile->read('numbers', TLocalisation::UNITS_OF_MEASUREMENT, 'metric');//'metric' of 'imperial'
        
        
        //====dates and times
        $this->arrCountrySettings[TLocalisation::CALENDAR_TYPE] = $objLocalisationFile->read('dates', TLocalisation::CALENDAR_TYPE, 'gregorian');//'gregorian', 'buddhist' etc.
        $this->arrCountrySettings[TLocalisation::TIMEZONE] = $objLocalisationFile->read('dates', TLocalisation::TIMEZONE, 'Europe/London');
        date_default_timezone_set($objLocalisationFile->read('dates', TLocalisation::TIMEZONE, 'Europe/London'));

        $this->arrCountrySettings[TLocalisation::DATEFORMAT_LONG] = $objLocalisationFile->read('dates', TLocalisation::DATEFORMAT_LONG, 'D j D Y'); //ma 9 jan 2012
        $this->arrCountrySettings[TLocalisation::DATEFORMAT_SHORT] = $objLocalisationFile->read('dates', TLocalisation::DATEFORMAT_SHORT, 'j-n-y');//9-1-12      

        $this->arrCountrySettings[TLocalisation::TIMEFORMAT_LONG] = $objLocalisationFile->read('dates', TLocalisation::TIMEFORMAT_LONG, 'H:i:s'); //09:12:22
        $this->arrCountrySettings[TLocalisation::TIMEFORMAT_SHORT] = $objLocalisationFile->read('dates', TLocalisation::TIMEFORMAT_SHORT, 'H:i'); //09:12
        
        $this->arrCountrySettings[TLocalisation::FIRST_DAY_OF_THE_WEEK] = $objLocalisationFile->read('dates', TLocalisation::FIRST_DAY_OF_THE_WEEK, '1'); //0=monday, 6=sunday
        
        
        //====dates and times - names
        $this->arrCountrySettings[TLocalisation::JANUARY] = $objLocalisationFile->read('dates', TLocalisation::JANUARY, 'January'); 
        $this->arrCountrySettings[TLocalisation::FEBRUARY] = $objLocalisationFile->read('dates', TLocalisation::FEBRUARY, 'February'); 
        $this->arrCountrySettings[TLocalisation::MARCH] = $objLocalisationFile->read('dates', TLocalisation::MARCH, 'March'); 
        $this->arrCountrySettings[TLocalisation::APRIL] = $objLocalisationFile->read('dates', TLocalisation::APRIL, 'April'); 
        $this->arrCountrySettings[TLocalisation::MAY] = $objLocalisationFile->read('dates', TLocalisation::MAY, 'May'); 
        $this->arrCountrySettings[TLocalisation::JUNE] = $objLocalisationFile->read('dates', TLocalisation::JUNE, 'June'); 
        $this->arrCountrySettings[TLocalisation::JULY] = $objLocalisationFile->read('dates', TLocalisation::JULY, 'July'); 
        $this->arrCountrySettings[TLocalisation::AUGUST] = $objLocalisationFile->read('dates', TLocalisation::AUGUST, 'August'); 
        $this->arrCountrySettings[TLocalisation::SEPTEMBER] = $objLocalisationFile->read('dates', TLocalisation::SEPTEMBER, 'September'); 
        $this->arrCountrySettings[TLocalisation::OCTOBER] = $objLocalisationFile->read('dates', TLocalisation::OCTOBER, 'October'); 
        $this->arrCountrySettings[TLocalisation::NOVEMBER] = $objLocalisationFile->read('dates', TLocalisation::NOVEMBER, 'Novermber'); 
        $this->arrCountrySettings[TLocalisation::DECEMBER] = $objLocalisationFile->read('dates', TLocalisation::DECEMBER, 'December'); 
        $this->arrCountrySettings[TLocalisation::JANUARY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::JANUARY_SHORT, 'Jan'); 
        $this->arrCountrySettings[TLocalisation::FEBRUARY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::FEBRUARY_SHORT, 'Feb'); 
        $this->arrCountrySettings[TLocalisation::MARCH_SHORT] = $objLocalisationFile->read('dates', TLocalisation::MARCH_SHORT, 'Mrch'); 
        $this->arrCountrySettings[TLocalisation::APRIL_SHORT] = $objLocalisationFile->read('dates', TLocalisation::APRIL_SHORT, 'Apr'); 
        $this->arrCountrySettings[TLocalisation::MAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::MAY_SHORT, 'May'); 
        $this->arrCountrySettings[TLocalisation::JUNE_SHORT] = $objLocalisationFile->read('dates', TLocalisation::JUNE_SHORT, 'Jun'); 
        $this->arrCountrySettings[TLocalisation::JULY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::JULY_SHORT, 'Jul'); 
        $this->arrCountrySettings[TLocalisation::SEPTEMBER_SHORT] = $objLocalisationFile->read('dates', TLocalisation::SEPTEMBER_SHORT, 'Aug'); 
        $this->arrCountrySettings[TLocalisation::OCTOBER_SHORT] = $objLocalisationFile->read('dates', TLocalisation::OCTOBER_SHORT, 'Oct'); 
        $this->arrCountrySettings[TLocalisation::NOVEMBER_SHORT] = $objLocalisationFile->read('dates', TLocalisation::NOVEMBER_SHORT, 'Nov'); 
        $this->arrCountrySettings[TLocalisation::DECEMBER_SHORT] = $objLocalisationFile->read('dates', TLocalisation::DECEMBER_SHORT, 'Dec'); 
        
        $this->arrCountrySettings[TLocalisation::SUNDAY] = $objLocalisationFile->read('dates', TLocalisation::SUNDAY, 'Sunday'); 
        $this->arrCountrySettings[TLocalisation::MONDAY] = $objLocalisationFile->read('dates', TLocalisation::MONDAY, 'Monday'); 
        $this->arrCountrySettings[TLocalisation::TUESDAY] = $objLocalisationFile->read('dates', TLocalisation::TUESDAY, 'Tuesday'); 
        $this->arrCountrySettings[TLocalisation::WEDNESDAY] = $objLocalisationFile->read('dates', TLocalisation::WEDNESDAY, 'Wednesday'); 
        $this->arrCountrySettings[TLocalisation::THURSDAY] = $objLocalisationFile->read('dates', TLocalisation::THURSDAY, 'Thursday'); 
        $this->arrCountrySettings[TLocalisation::FRIDAY] = $objLocalisationFile->read('dates', TLocalisation::FRIDAY, 'Friday'); 
        $this->arrCountrySettings[TLocalisation::SATURDAY] = $objLocalisationFile->read('dates', TLocalisation::SATURDAY, 'Saturday'); 
        $this->arrCountrySettings[TLocalisation::SUNDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::SUNDAY_SHORT, 'Su'); 
        $this->arrCountrySettings[TLocalisation::MONDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::MONDAY_SHORT, 'Mo'); 
        $this->arrCountrySettings[TLocalisation::TUESDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::TUESDAY_SHORT, 'Tu'); 
        $this->arrCountrySettings[TLocalisation::WEDNESDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::WEDNESDAY_SHORT, 'We'); 
        $this->arrCountrySettings[TLocalisation::THURSDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::THURSDAY_SHORT, 'Th'); 
        $this->arrCountrySettings[TLocalisation::FRIDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::FRIDAY_SHORT, 'Fr'); 
        $this->arrCountrySettings[TLocalisation::SATURDAY_SHORT] = $objLocalisationFile->read('dates', TLocalisation::SATURDAY_SHORT, 'Sa'); 

        $this->arrCountrySettings[TLocalisation::DAY_ONE] = $objLocalisationFile->read('dates', TLocalisation::DAY_ONE, 'day'); 
        $this->arrCountrySettings[TLocalisation::DAY_OTHER] = $objLocalisationFile->read('dates', TLocalisation::DAY_OTHER, 'days'); 
        $this->arrCountrySettings[TLocalisation::MONTH_ONE] = $objLocalisationFile->read('dates', TLocalisation::MONTH_ONE, 'month'); 
        $this->arrCountrySettings[TLocalisation::MONTH_OTHER] = $objLocalisationFile->read('dates', TLocalisation::MONTH_OTHER, 'months'); 
        $this->arrCountrySettings[TLocalisation::YEAR_ONE] = $objLocalisationFile->read('dates', TLocalisation::YEAR_ONE, 'year'); 
        $this->arrCountrySettings[TLocalisation::YEAR_OTHER] = $objLocalisationFile->read('dates', TLocalisation::YEAR_OTHER, 'years'); 
        $this->arrCountrySettings[TLocalisation::HOUR_ONE] = $objLocalisationFile->read('dates', TLocalisation::HOUR_ONE, 'hour'); 
        $this->arrCountrySettings[TLocalisation::HOUR_OTHER] = $objLocalisationFile->read('dates', TLocalisation::HOUR_OTHER, 'hours'); 
        $this->arrCountrySettings[TLocalisation::MINUTE_ONE] = $objLocalisationFile->read('dates', TLocalisation::MINUTE_ONE, 'minute'); 
        $this->arrCountrySettings[TLocalisation::MINUTE_OTHER] = $objLocalisationFile->read('dates', TLocalisation::MINUTE_OTHER, 'minutes'); 
        $this->arrCountrySettings[TLocalisation::SECOND_ONE] = $objLocalisationFile->read('dates', TLocalisation::SECOND_ONE, 'second'); 
        $this->arrCountrySettings[TLocalisation::SECOND_OTHER] = $objLocalisationFile->read('dates', TLocalisation::SECOND_OTHER, 'seconds'); 
        
      
        if (!is_file($sFileName)) //only if settings file not exists
        {
 
            if (APP_DEBUGMODE) //alleen in development stadium mag het bestand door iedereen aangepast worden, op webserver niet! (veiligheidsmaatregel)
            {
                if (!$objLocalisationFile->saveToFile($sFileName, PHP_EOL, 0777))
                    return false;
            }
            else
            {
                if (!$objLocalisationFile->saveToFile($sFileName, PHP_EOL, 0755))
                    return false;
            }
        }
        
        $this->bFileLoaded = true;
        
        unset($objLocalisationFile); //closes file
        
        return true;
    }
            
    
    
    /**
     * get a country-specific setting
     * such as the thousand separator
     * 
     * if not exists it returns null
     * 
     * @param string $sSettingName
     * @return string
     */
    public function getSetting($sSettingName)
    {
        if (!$this->bFileLoaded) //only load when not loaded yet
            $this->loadFromFile();
        
        if (APP_DEBUGMODE) //for performance reasons: do not check with array_key_exists() in live environment
        {
            if (!array_key_exists($sSettingName,$this->arrCountrySettings))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'getSetting(): setting "'.$sSettingName.'" not found');
                return null;    
            }
        }
                
        return $this->arrCountrySettings[$sSettingName];
    }

    /**
     * overwrite settings from localisation file
     */
    public function setSetting($sSettingName, $sSettingValue)
    {   
        $this->arrCountrySettings[$sSettingName] = $sSettingValue;

        if ($sSettingName == TLocalisation::TIMEZONE)
            date_default_timezone_set($sSettingValue);
    }
    

      
}

?>
