<?php
namespace dr\classes\locale;

use dr\classes\patterns\TObject;

/**
 * Description of TLocale
 * 
 * Deze klasse gaat over taalinstellingen en land-specifieke-instellingen
 * taalinstellingen zijn: bijvoorbeeld de Nederlandse, Duitse of Engelse taal
 * de land-specifieke instellingen zijn: bijvoorbeeld het-decimaal-scheidingsteken, munteenheid, datumnotatie
 * 
 * Deze klasse maakt gebruik van de vaak gebruikte notatie voor locales: [taalcode]-[landcode] , bijvoorbeeld nl-NL of nl-BE of en-US
 * De taalcode wordt altijd met kleine letters geschreven en de landcode altijd met hoofletters geschreven
 * 
 * 
 * De setLocale functie van php geeft problemen omdat alle strings worden geconverteerd, ook de string die je niet wilt, zoals SQL queries
 * 
 * 11 juli 2012: 2x getpath functies aangemaakt voor het verkrijgen van de paths in TLocale
 * 12 juli 2012: TLocale: getTranslationFramework(), getTranslationModule, getTranslationWebsite, getCountrySetting toegeveogd
* 12 juli 2012: TLocale: lezen modules nu in 2d array
 * 12 julie 2012: TLocale: getTranslationModule() heeft een parameter voor de module gekregen
 * 12 juli 2012: TLocale: getTranslationFramework, getTranslationWebsite, getTranslationModule aangepast, zodat ze niet bestaande vertalingen toevoegen aan de vertalingsarray
 * 13 juli 2012: TLocale: writeTranslationFile() nu compleet
 * 13 juli 2012: alle translation related stuff uit TLocale gehaald (dit zit nu in een aparte klasse
 * 13 juli 2012: TLocale: getPathCountrySettingsFile() aangepast. de functie pakt nu de localecode
 * 19 juli 2012: TCountrySettings::JULY_SHORT miste bij het uitlezen settings file
 * 20 juli 2012: TLocalt:: PERCENT_SIGN erbij
 * 22 juli 2012: TLocale: De countrysettings uit TLocale gehaald en in TCountrySettings gestopt
 * 
 * @author drenirie
 */
class TLocale
{
    private $sLanguageCode = 'nl';
    private $sCountryCode = 'NL';
      
    
    public function __construct($sDefaultLocale = APP_LOCALE_DEFAULT) 
    {
        //set default
        $this->setLocale($sDefaultLocale);
    }
    
    public function __destruct() 
    {

        
    }
    
    
    /**
    * gets the default locale of the browser by looking in the browser headers
    * 
    * in the header HTTP_ACCEPT_LANGUAGE is something like "nl,en-us;q=0.3,de;q=0.1"
    * q=value between 0 and 1.0. Beeing 1.0 the preferred language, 0.9 less preferred etc. 
    * when no q-value: the value 1.0 is assumed
    * When HTTP_ACCEPT_LANGUAGE is empty the default language $sDefaultLanguage is assumed
    * 
    * Copyright 2008 Darrin Yeager http://www.dyeager.org/  Licensed under BSD license:  http://www.dyeager.org/downloads/license-bsd.php 
    * script source: http://www.dyeager.org/blog/2008/10/getting-browser-default-language-php.html
    * 
    * if you���re sending different language-specific content at the same URL, 
    * be sure to send the appropriate Vary header. 
    * If you don���t, intermediate proxy caches might be confused and serve the wrong language to some people. 
    * To do that, just use the following first in your PHP code: header("Vary: Accept-Language"). 
    * But be warned Internet Explorer has some bugs with the Vary header you should be aware of. 
    * 
    * @param string $sDefaultLanguage
    * @return string 
    */     
    public function getLocaleFromBrowser($sDefaultLanguage = APP_LOCALE_DEFAULT)
    {
        if (isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
        {
            $http_accept = $_SERVER["HTTP_ACCEPT_LANGUAGE"];

            if (isset($http_accept) && strlen($http_accept) > 1) 
            {
                // Split possible languages into array
                $x = explode(",", $http_accept);
                foreach ($x as $val) 
                {
                    //check for q-value and create associative array. No q-value means 1 by rule
                    if (preg_match("/(.*);q=([0-1]{0,1}\.\d{0,4})/i", $val, $matches))
                        $lang[$matches[1]] = (float) $matches[2];
                    else
                        $lang[$val] = 1.0;
                }

                //return default language (highest q-value)
                $qval = 0.0;
                foreach ($lang as $key => $value) 
                {
                    if ($value > $qval) 
                    {
                        $qval = (float) $value;
                        $sDefaultLanguage = $key;
                    }
                }
            }

            return strtolower($sDefaultLanguage);
        }
        else
            return strtolower($sDefaultLanguage);
                
    }
    
    /**
     * get the locale code
     * 
     * @return string 
     */
    public function getLocale()
    {
        //return 1 code (without dash (-)) if de country and language code are the same
        if ($this->getLanguageCode() == strtolower($this->getCountryCode()))//case incensitive compare
            return $this->getLanguageCode();
        else
            return $this->getLanguageCode().'-'.$this->getCountryCode();
    }
    
    
    /**
     * set the locale code
     * 
     * the locale code must be in format: [taalcode]-[landcode]
     * underscore (_) in stead of a dash (-) is allowed
     * 
     * @param type $sLocale 
     */
    public function setLocale($sLocale)
    {
        if ((strlen($sLocale) >= 5) || (strlen($sLocale) >= 7))
        {
            $sLocale = str_replace ('_', '-', $sLocale);//de underscore vervangen
            
            $arrLocales = explode('-', $sLocale);
            if (count($arrLocales) == 2)
            {
                $this->setLanguageCode($arrLocales[0]);
                $this->setCountryCode($arrLocales[1]);
            }
            else
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'setLocale(): the locale code must consist of 2 elements, separated with a dash (-)');
        }
        elseif ((strlen($sLocale) == 2) || (strlen($sLocale) == 3) )
        {
            $this->setLanguageCode($sLocale);
            $this->setCountryCode($sLocale);
        }
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'setLocale(): locale code ('.$sLocale.') must contain 2-3 or 5-7 characters: i.e. nl or nl-NL, now its only '.strlen($sLocale).' characters.');
    }
    
    /**
     * set the 2-character countrycode
     * for example: NL, US, DE
     * @param string $sCode 
     */
    public function setCountryCode($sCode)
    {
        if ((strlen($sCode) == 2) || (strlen($sCode) == 3))
            $this->sCountryCode = strtoupper($sCode);
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'setCountryCode(): Country code not 2 or 3 characters', $this);
    }
    
    /**
     * returns the 2-character countrycode
     * 
     * @return string
     */
    public function getCountryCode()
    {
        return $this->sCountryCode;
    }
    
    /**
     * set the 2-character languagecode
     * for example: nl, en, fr
     * 
     * @param type $sCode 
     */
    public function setLanguageCode($sCode)
    {
        if ((strlen($sCode) == 2) || (strlen($sCode) == 3))
            $this->sLanguageCode = strtolower($sCode);
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'setLanguageCode(): Language code not 2 or 3 characters', $this);        
    }
    
    /**
     * return the 2-character language code
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->sLanguageCode;
    }
           
    
            
   
}

?>
