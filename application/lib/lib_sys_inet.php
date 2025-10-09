<?php
/**
 * In this library exist only internet related functions
 * 
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 4 juli 2012
 * ===========
 * doNotTrack --> getDoNotTack
 * 
 * 23 juli 2012: lib_inet: checkCookiesEnabled() added
 * 24 okt 2012: lib_inet: addVariableToURL() added
 * 24 okt 2012: lib_inet: addVariableToURL() ondersteuning voor unieke variabelen
 * 25 okt 2012: lib_inet: checkCookiesEnabled() verwijderd. problemen met cookie law
 * 17 jan 2013: lib_inet: getSubdomain() toegevoegd
 * 8 mei 2015: lib_inet: addVariableToURL() extra declaraties zorgen voor meer snelheid
 * 14 mei 2015: lib_inet: addVariableToURL() uitgebreid met mogelijkheid om meerdere waardes aan 1 variabele toe te wijzen
 * 21 mei 2015: lib_inet: redirectToHTTPS() toegevoegd
  * 21 mei 2015: lib_inet: getURLThisScript() retourneert nu $_SERVER["SCRIPT_URI"]; (is sneller en neemt https mee, wat voorheen niet gebeurde)
 * 7 juni 2015: lib_inet: isSSL() added
 * 7 juni 2015: lib_inet: redirectToHTTPS() changed to up-to-date headers to force TLS encryption
 * 17 juni 2014: lib_inet: isHTTPS() aangepast, gaf fout
  * 17 juni 2014: lib_inet: getURLThisScript() aangepast, gaf fout
  * 6 juli 2014: lib_inet: getURLThisScript() sanites the url
 * 11 april 2019: generatePrettyURLSafeURL(): bugfix: allways put out ''
 * 11 april 2019: generatePrettyURLSafeURL(): bugfix: no numbers allowed and minus sign was filtered
 * 7 mei 2019: addVariableToURL(): bugfix: when url ended with question mark it had an unintended result
 * 16jan2020: lib_inet: getIPAddressBrowser)_ added
 * 16jan2020: lib_inet: getIPAddressBrowser)_ added
 * 18 jan 2020:: lib_inet: getFingerprintClient() added
 * 22 jan 2020:: lib_inet: getFingerprintClient() HTTP_X_REAL_IP not always present, so removed
 * 23 jan 2020:: lib_inet: getFingerprintClient() ==> getFingerprintBrowser()
 * 29 nov 2020:: lib_inet: getBrowserName() and getBrowserOS added
 * 25 jun 2021: lib_inet: getFingerprintBrowser() has parameter to exclude ip address
 * 25 jun 2021: lib_inet: getFingerprintBrowser() is more unique now: language is added
 * 14 sept 2021: lib_inet: getFingerprintBrowser() is more unique now: do not track added
 * 14 sept 2021: lib_inet: isEmailValid(), checkEmail(), getBrowserFingerprint() moved from lib_string to lib_inet
 * 14 sept 2021: lib_inet: getFingerprintBrowser() werkt nu ook in een js Fetch() call
 * 2 mrt 2022: lib_inet: bugfix: redirectToHTTPS() werkt nu 
 * 2 mrt 2022: lib_inet rename to lib_sys_inet: omday redirectToHTTPS() wordt gebruikt in bootstrap
 * 18 mrt 2022: lib_sys_inet: generatePrettyURLSafeURL() updated with support for accents
 * 18 jan 2023: lib_sys_inet: isValidEmail() extra parameter for checking only latin characters, to avoid spoof email addresses
 * 18 jan 2023: lib_sys_inet: isValidEmail() check on dns went wrong when no @ in email address. FIXED
 * 20 jan 2023: lib_sys_inet: isValidEmail() bugfix
 * 20 jan 2023: lib_sys_inet: isValidURL() added
 * 29 may 2023: lib_sys_inet: getDomain() updated to allow a whole url to be filtered for domainname
 * 3 june 2023: lib_sys_inet: removeVariableFromUrl() updated so it can remove all variables in one sweep
 * 27 june 2023: lib_sys_inet: obfuscateEmail() added: email@example.com --> em***@e******.c**
 * 30 may 2024: lib_sys_inet: sanitizeURLSlug() alias
 * 6 jun 2024: lib_sys_inet: sanitizeFileName() alias
 * 6 jun 2024: lib_sys_inet: getIPAddress() nu parameter met replace colon(:)
 * 30 jan 2025: lib_sys_inet: purifyHTML() function added
 * 13 aug 2025: lib_sys_inet: purifyHTMLRecursive() function uit purifyHTML() gehaald (anders php klaagt over dubbele declaratie)
 * 13 aug 2025: lib_sys_inet: filterXSSRecursive() function uit filterXSS() gehaald   (anders php klaagt over dubbele declaratie)
 * 13 aug 2025: lib_sys_inet: function purifyHTMLSVG($sSVG) added
 * 
 * 
 * @author Dennis Renirie
 */

//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');

/**
 * returns if HTTPS is used instead of plain HTTP
 * @return boolean
 */
function isHTTPS()
{

        if (isset($_SERVER['https']))
        {
                if($_SERVER['https'] == 1) /* Apache */ 
                        return true; 
                elseif ($_SERVER['https'] == 'on') /* IIS */ 
                        return true;
         }
        elseif (isset($_SERVER['SERVER_PORT']))
        {
            
        if ($_SERVER['SERVER_PORT'] == 443) /* others */ 
                        return true;
                else 
                        return false; /* just using http */
        }
        else 
                return false;
 
    
}

/**
 * function does a header redirect to the secure HTTPS version of the page when 
 * unsecure HTTP protocol is detected
 * 
 */
function redirectToHTTPS()
{
    //declare
    $sURL = '';
    $sURLHTTPS = '';
    $sPathWWW = '';

    //init
    $sURL = getURLThisScript();    
    $sURLHTTPS = str_replace('http://', 'https://', $sURL);
    if (defined('APP_PATH_WWW'))
        $sPathWWW = APP_PATH_WWW;
    elseif (defined('APP_URL_CMS'))
        $sPathWWW = APP_URL_CMS;

    //only when path in config file is indeed httpS instead of http (otherwise that can cause problems on dev server)    
    if (stripos($sPathWWW, 'https://') === false) 
    {
        return; //abort redirect
    }    


    //if changed by replace then do header redirect
    if ($sURL != $sURLHTTPS)
    {
        // if ($bMovedPermanently)
        //     header( "HTTP/1.1 301 Moved Permanently" ); 

        //31536000 = 1 year
        //preload means: if you like the domain to be included in the HSTS preload list maintained by Chrome (and used by Firefox and Safari)
        // header("Strict-Transport-Security: max-age=31536000; includeSubDomains; preload");
        header('Location: '.$sURLHTTPS);    
    }

}

/**
 * detect if site visitor is using a mobile device
 * 
 * @return bool
 */
function isMobile()
{
	return (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]) === 1);
}



/**
 * determine if the user agent is a spider, fetcher, bot or crawler of some kind
 * by analyzing the httpuseragent
 * 
 * @param string $sHTTPUserAgent
 */
function isSearchEngine($sHTTPUserAgent = '')
{
    //replacing default
    if ($sHTTPUserAgent == '')
        $sHTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];
    
    $arrCrawlers = array();
    $arrCrawlers[] = 'crawler'; //general word
    $arrCrawlers[] = 'spider'; //general word
    $arrCrawlers[] = 'fetch'; //general word
    $arrCrawlers[] = 'googlebot';
    $arrCrawlers[] = 'AhrefsBot'; // http://ahrefs.com/robot/
    $arrCrawlers[] = 'Vagabondo'; // http://webagent.wise-guys.nl/
    $arrCrawlers[] = 'bingbot'; 
    $arrCrawlers[] = 'acoonbot'; 
    $arrCrawlers[] = 'seznambot'; 
    $arrCrawlers[] = 'msnbot'; 
    $arrCrawlers[] = 'yandexbot';     
    $arrCrawlers[] = 'wotbox';     
    $arrCrawlers[] = 'exabot';     
    $arrCrawlers[] = 'LinkWalker';     
    $arrCrawlers[] = 'UnwindFetchor';     ///http://www.gnip.com/
    $arrCrawlers[] = 'TweetmemeBot';     
    $arrCrawlers[] = 'Twitterbot';    
    $arrCrawlers[] = 'panscient.com';    
    $arrCrawlers[] = 'TurnitinBot';    
    $arrCrawlers[] = 'Summify';     
    $arrCrawlers[] = 'discobot';     //discovery channel bot
    $arrCrawlers[] = 'HomeTags';    
    $arrCrawlers[] = 'RexyoBot';    
    $arrCrawlers[] = 'Yeti';    
    $arrCrawlers[] = 'findlinks';    
    $arrCrawlers[] = 'Snapbot';     
    $arrCrawlers[] = 'PagesInventory';    
    $arrCrawlers[] = 'DoCoMo';    //google iets
    
    //search the array
    foreach($arrCrawlers as $sCrawler)
    {
        if (stripos($sHTTPUserAgent, $sCrawler))
        {   
            return true;          
        }
    }
    
    return false;
}


/**
 * determine if the user agent is somewhat reliable
 * 
 * Let op
 * 
 * @param string $sHTTPUserAgent
 */
function isValidUserAgent($sHTTPUserAgent = '')
{
    //replacing default
    if ($sHTTPUserAgent == '')
        $sHTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];
    
       
    //====LOOSELY analyse HTTP_USER_AGENT
    $arrProhibitedLoose[] = 'java';     // java is often used by spammers and people with no neat intentions (otherwise they would set the user agent properly)
    $arrProhibitedLoose[] = 'Kimengi';     // lijkt dubieus
    $arrProhibitedLoose[] = 'nineconnections.com';     // lijkt dubieus
    $arrProhibitedLoose[] = 'Jakarta';     // Don’t ask. Just block
    $arrProhibitedLoose[] = 'panscient.com';     //door verschillende sites aangegevens als gevaarlijk
    $arrProhibitedLoose[] = 'MorMor';     // lijkt dubieus
    $arrProhibitedLoose[] = 'ichiro';     // lijkt me dubieus
    
    //search the array
    foreach($arrProhibitedLoose as $sProhibited)
    {
        if (stripos($sHTTPUserAgent, $sProhibited)) //case insensitive compare
            return false;          
    }
    

    //====LITTERALY analyse HTTP_USER_AGENT
    $arrProhibitedStrict[] = '';     /// empty user agent: no good
    $arrProhibitedStrict[] = 'Android';     ///with only 'Android' as user agent is suspicious
    $arrProhibitedStrict[] = 'Mozilla';     //Only the string Mozilla of course
    $arrProhibitedStrict[] = 'User-Agent';     //with only 'mozilla' as user agent is suspicious
    $arrProhibitedStrict[] = 'compatible ;';     //
        
    //search the array
    foreach($arrProhibitedStrict as $sProhibited)
    {
        if (strcasecmp($sHTTPUserAgent, $sProhibited) == 0) //case insensitive compare
            return false;          
    }
    
    
    
    return true;
}


/**
 * returns subdomain 
 * i.e. in hostname 'breda.mijndomein.nl' it returns 'breda'
 * (it filters subdomain out of the hostname)
 * 17-01-2012
 * 
 * This function can return the default 'www'
 * 
 * @param $sHostname string if empty $_SERVER["HTTP_HOST"] is assumed
 * @return string 
 */
function getSubdomain($sHostname = '')
{
    if ($sHostname == '')
        $sHostname = $_SERVER["HTTP_HOST"];
        
    $iPosFirstDot = stripos ( $sHostname , '.' );
    $sSubdomein = filterXSS(substr($sHostname, 0, $iPosFirstDot));

    return $sSubdomein;    
}

/**
 * add a variable safely to an url
 * 
 * sometimes you don't know how many variables you already have in the url, so do you have to use '?' or '&' to add  the variable. 
 * This function figures this out for you
 * 
 * this function does also urlencode (!!!) the variable and the value
 * 
 * if $bAddMultipleValuesToOneVariable == false:
 * this function also checks if you didn't add the variable twice (the url of current script may already contain the variable),
 * if this is the case the new one will override the old one
 * 
 * if $bAddMultipleValuesToOneVariable == true you can add more values to a variable 
 * separated with $sValueSeparator
 * (this way it is easy do an explode() to make it an array)
 * may become very handy for incrementing multiple searchqueries or sorting multiple columns
 * 
 * for example your url: http://www.mysite.nl/whatever.php?super=duper 
 * and you want to add variable 'you' with value 'no fool'
 * this function makes: http://www.mysite.nl/whatever.php?super=duper&you=no+fool
 * 
 * the counterpart is removeVariableFromURL()
 * 
 * @param string $sURL the url you want
 * @param string $sVariable
 * @param string $sValue
 * @param bool $bAddMultipleValuesToOneVariable add more values to 1 variable by adding a separator
 * @return string
 */
function addVariableToURL($sURL, $sVariable, $sValue, $bAddMultipleValuesToOneVariable = false, $sValueSeparator = ',')
{
    //catch if sURL is empty
    if (($sURL !== '') && ($sURL !== null)) 
    {
        $iPosQuestionMark = strpos($sURL, '?');
    }
    else
    {
        $iPosQuestionMark = 0;
        $sURL = '';
    }


    if ($iPosQuestionMark)//if question mark exists in url
    {
        $bVariableExists = false;
        
        
        //preventing that you can't add the same variable twice, so we have to parse the url a little
        $sURLPreQuestionMark  = substr($sURL, 0, $iPosQuestionMark);
        $sURLPostQuestionMark = substr($sURL, $iPosQuestionMark+1);
        $sNewVariablesSection = ''; //construct new variables section in url (build the url again)
        $arrURLVars = explode('&', $sURLPostQuestionMark);
        $bFirstTimeLoop = true;
        $bVariableExists = false;
        $arrVarValue = null;
          
        
        if (strlen($sURLPostQuestionMark) > 0) //could be empty if  url ends with '?' (like in a malformed form)
        {
            foreach ($arrURLVars as $sVarValue)//looping al variables in url
            {
                $arrVarValue = explode('=', $sVarValue);
                if ($arrVarValue[0] == urlencode($sVariable)) //if the variable already exists in url then replace it(the first index of the array is the variable, the second the value)
                {
                    $bVariableExists = true;

                    if ($bAddMultipleValuesToOneVariable)
                        $arrVarValue[1] .= $sValueSeparator.urlencode($sValue);
                    else
                        $arrVarValue[1] = urlencode($sValue);  //replace value (second index)                
                }

                if ($bFirstTimeLoop)
                    $sNewVariablesSection.= $arrVarValue[0].'='.$arrVarValue[1];
                else
                    $sNewVariablesSection.= '&'.$arrVarValue[0].'='.$arrVarValue[1];
                $bFirstTimeLoop = false;
            }        
            
            if (!$bVariableExists) //if not exists, then add
                $sNewVariablesSection.= '&'.urlencode($sVariable).'='.urlencode($sValue);
            
            $sURL = $sURLPreQuestionMark.'?'.$sNewVariablesSection;                    
        }
        else //if url ends with '?' only add variable
            $sURL .= urlencode($sVariable).'='.urlencode($sValue);
        
        

    }
    else //if question mark doesn't exist in url
        $sURL.= '?'.urlencode($sVariable).'='.urlencode($sValue);
   
    return $sURL;
}

/**
 * with addVariableToURL you add a variable to a url, with removeVariableFromURL, you remove it
 * 
 * @param string $sURL
 * @param string $sVariable variable to remove from url. if empty it removes ALL variables
 */
function removeVariableFromURL($sURL, $sVariable = '')
{
    $iPosQuestionMark = strpos($sURL, '?');
    
    if ($iPosQuestionMark)//if question mark exists in url
    {                      
        $sURLPreQuestionMark  = substr($sURL, 0, $iPosQuestionMark);
        $sURLPostQuestionMark = substr($sURL, $iPosQuestionMark+1);
        $arrURLVars = explode('&', $sURLPostQuestionMark);
        $arrNewVarValue = array();
        $arrTempVarVal = array();
          
        //removing all variables
        if ($sVariable == '')
            return $sURLPreQuestionMark;            

        //check all the variables in the url and compose $arrNewVarValue with the good variables
        if (strlen($sURLPostQuestionMark) > 0) //could be empty if  url ends with '?' (like in a malformed form)
        {
            foreach ($arrURLVars as $sVarValue)//looping al variables in url
            {
                $arrTempVarVal = explode('=', $sVarValue);
                if ($arrTempVarVal[0] != $sVariable) // if it' not the variable we are looking for, add it to $arrNewVarValue
                    $arrNewVarValue[] = $sVarValue;
            }
        }
        
        //parse the url back together
        if (count($arrNewVarValue) > 0)
        {
            return $sURLPreQuestionMark.'?'.implode('&', $arrNewVarValue);
        }
        else
            return $sURLPreQuestionMark;
    
        
    }
    else //no question mark, so no variables
    {
        if ($sVariable == '') //returning all variables?
            return $sURL;//just return the current url
    }

}



/**
 * returns the domain name 
 * it tries constants APP_PATH_DOMAIN and APP_PATH_DOMAIN_CMS first, 
 * if not exists, it is extracted from url
 * function can filter subdomains
 * 
 * @param $sURL when empty it returns APP_PATH_DOMAIN or APP_PATH_DOMAIN_CMS, otherwise when string is empty $_SERVER["HTTP_HOST"] is assumed
 * @param $bRemoveSubdomains filters out subdomains
 * @return string 
 */
function getDomain($sURL = '', $bRemoveSubdomains = true)
{
    $arrExplode = array();


    //first try constants
    if ($sURL === '') //domain of current site assumed
    {
        if (defined('APP_PATH_DOMAIN'))
            return APP_PATH_DOMAIN;
        elseif (defined('APP_PATH_DOMAIN_CMS'))
            return APP_PATH_DOMAIN_CMS;
    }

    //if constants don't help, try to figure it out by looking at the url
    if ($sURL === '') //domain of current site assumed
        $sURL = $_SERVER["HTTP_HOST"];

    //remove protocol like https://
    $arrExplode = explode('://', $sURL);
    if (count($arrExplode) > 1) //www exists?
        $sURL = $arrExplode[1];

    //remove www.
    if (startswith($sURL, 'www.'))
    {
        $arrExplode = explode('www.', $sURL);
        if (count($arrExplode) > 1) //www exists?
            $sURL = $arrExplode[1];        
    }

    //remove everything after first /
    $arrExplode = explode('/', $sURL);
    if (count($arrExplode) > 1) // slash exists?
        $sURL = $arrExplode[0];

    //remove subdomains
    if ($bRemoveSubdomains)
    {
        $iCountExpl = 0;
        $arrExplode = explode('.', $sURL);
        $iCountExpl = count($arrExplode);

        if ($iCountExpl >= 2) 
        {
            $sURL = $arrExplode[$iCountExpl-2];
            $sURL.= '.'.$arrExplode[$iCountExpl-1];
        }
    }


    return $sURL;
}




/**
*  in browsers is het heden-ten-dage mogelijk om de do-not-track optie
* in te stellen. Deze functie kijkt of deze optie ingeschakeld is
* added: 3 juni 2012
*
* @return bool
*/
function getDoNotTack()
{
    if (isset($_SERVER['HTTP_DNT']))
    {
        if ($_SERVER['HTTP_DNT']==1)
            RETURN TRUE;
    }
    elseif (function_exists('getallheaders'))
    {
        foreach (getallheaders() as $k => $v)
        {
            if (strtolower($k)==="dnt" && $v==1)
                RETURN TRUE;
        }
    }
    RETURN FALSE;
}
    
/**
 * generate a url-safe string
 * Useful for url slugs and pretty urls. 
 * Example www.mysite.com/nieuws/432/dit-is-zon-string-die-we-met-deze-functie-converteren.html
 *
 * @param string $sUrl
 * @return string
 */
function generatePrettyURLSafeURL($sUrl)
{
    //alternative method:
    //return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ -]+/', '/^-|-$/'),    array('', '-', ''), remove_accent($str))); 

    $sUrl = str_replace(' ', '-', $sUrl);
    $sUrl = collateString($sUrl);
    $sUrl = strtolower($sUrl);

    return preg_replace( '/[^_\-abcdefghijklmnopqrstuvwxyz0123456789]/', '', $sUrl );
}

/**
 * wrapper for generatePrettyURLSafeURL()
 *
 * @param string $sUrl
 * @return string
 */
function getURLSlug($sUrl)
{
    return generatePrettyURLSafeURL($sUrl);
}

/**
 * "reverses" the url slug conversion as good as possible
 * 
 * WARNING: 
 * we do some damage to the url slug that is unrecoverable to the original string,
 * like accents, so this function is not a 100% reverse!
 *
 * @param string $sURLSlug url slug
 * @param bool $bRemoveExtension should remove the file extion? i.e. 'mountain.jpg' becomes 'mountain'
 * @return string
 */
function getURLSlugReverse($sURLSlug, $bRemoveFileExtension = false)
{
    $sURLSlug = str_replace('-', ' ', $sURLSlug);

    if ($bRemoveFileExtension)
    {
        $arrPathParts = pathinfo($sURLSlug);
        $sURLSlug = $arrPathParts['filename'];
    }

    return $sURLSlug;
}

/**
 * wrapper for generatePrettyURLSafeURL()
 *
 * @param string $sUrl
 * @return string
 */
function sanitizeURLSlug($sUrl)
{
    return generatePrettyURLSafeURL($sUrl);
}

/**
 * Met deze functie kun je een bestand downloaden. Deze functie verzorgt de headers en de hele mikmak. zelfs mogelijk om een download te resumen
 * LET OP zoals kunnen zien heeft firefox wat problemen met extensies
 *
 * @author pechkin at zeos dot net
 *
 * @param string $sLocalPath niet relatieve path op de server van het bestan
 * @param string $sCustomFileName bestandsnaam zoals deze aan de client kant opgeslagen moet worden
 */
function downloadFile($sLocalPath, $sCustomFileName)
{
        $fname = $sCustomFileName;
        $fpath = $sLocalPath;
        $fsize = filesize($fpath);
        $bufsize = 20000;

        if(isset($_SERVER['HTTP_RANGE']))  //Partial download
        {
           if(preg_match("/^bytes=(\\d+)-(\\d*)$/", $_SERVER['HTTP_RANGE'], $matches)) { //parsing Range header
                   $from = $matches[1];
                   $to = $matches[2];
                   if(empty($to))
                   {
                           $to = $fsize - 1;  // -1  because end byte is included
                                                                   //(From HTTP protocol:
        // 'The last-byte-pos value gives the byte-offset of the last byte in the range; that is, the byte positions specified are inclusive')
                   }
                   $content_size = $to - $from + 1;

                   header("HTTP/1.1 206 Partial Content");
                   header("Content-Range: $from-$to/$fsize");
                   header("Content-Length: $content_size");
                   header("Content-Type: application/force-download");
                   header("Content-Disposition: attachment; filename=$sCustomFileName");
                   header("Content-Transfer-Encoding: binary");

                   if(file_exists($fpath) && $fh = fopen($fpath, "rb"))
                   {
                           fseek($fh, $from);
                           $cur_pos = ftell($fh);
                           while($cur_pos !== FALSE && ftell($fh) + $bufsize < $to+1)
                           {
                                   $buffer = fread($fh, $bufsize);
                                   print $buffer;
                                   $cur_pos = ftell($fh);
                           }

                           $buffer = fread($fh, $to+1 - $cur_pos);
                           print $buffer;

                           fclose($fh);
                   }
                   else
                   {
                           header("HTTP/1.1 404 Not Found");
                           exit;
                   }
           }
           else
           {
                   header("HTTP/1.1 500 Internal Server Error");
                   exit;
           }
        }
        else // Usual download
        {
           header("HTTP/1.1 200 OK");
           header("Content-Length: $fsize");
           header("Content-Type: application/force-download");
           header("Content-Disposition: attachment; filename=$sCustomFileName");
           header("Content-Transfer-Encoding: binary");

           if(file_exists($fpath) && $fh = fopen($fpath, "rb")){
                   while($buf = fread($fh, $bufsize))
                           print $buf;
                   fclose($fh);
           }
           else
           {
                   header("HTTP/1.1 404 Not Found");
           }
        }
}

/**
 * getting the url of this script
 * het verkrijgen van de complete url (inclusief servernaam en parameters) van dit script
 *
 * sanitizes the url
 * 
 * @return string
 */
function getURLThisScript()
{
	//$_SERVER["SCRIPT_URI"]; is niet altijd aanwezig

	
	$sURL = '';
	if (isHTTPS())
		$sURL = 'https://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	else 
		$sURL = 'http://'.$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"];
	
	return filter_var($sURL, FILTER_SANITIZE_URL);
}

/**
 * krijgen van een pagina (zoals de browser dat ook doet) in een string
 * gewoon aanroepen met httpgetpost("http://www.hotmail.com");
 * Je krijgt dan de html codes terug van www.hotmail.com
 *
 * @author some chinese guy at http://www.spencernetwork.org/memo/tips-3.php
 *
 * @param string $sUrl
 * @param string $sMethod
 * @param string $sHeaders
 * @param array $post
 * @return string
 */
function httpgetpost($sUrl, $sMethod="GET", $sHeaders="", $post=array(""))
{
        /* URL??? */
        $sUrl = parse_url($sUrl);

        /* ???? */
        if (isset($sUrl['query'])) {
                $sUrl['query'] = "?".$sUrl['query'];
        } else {
                $sUrl['query'] = "";
        }

        /* ??????????80 */
        if (!isset($sUrl['port'])) $sUrl['port'] = 80;

        /* ???????? */
        $request  = $sMethod." ".$sUrl['path'].$sUrl['query']." HTTP/1.0\r\n";

        /* ???????? */
        $request .= "Host: ".$sUrl['host']."\r\n";
        $request .= "User-Agent: PHP/".phpversion()."\r\n";

        /* Basic??????? */
        if (isset($sUrl['user']) && isset($sUrl['pass'])) {
                $request .= "Authorization: Basic ".base64_encode($sUrl['user'].":".$sUrl['pass'])."\r\n";
        }

        /* ????? */
        $request .= $sHeaders;

        /* POST??????????????URL????????????? */
        if (strtoupper($sMethod) == "POST")
        {
                while (list($name, $value) = each($post))
                {
                        $POST[] = $name."=".urlencode($value);
                }
                $postdata = implode("&", $POST);
                $request .= "Content-Type: application/x-www-form-urlencoded\r\n";
                $request .= "Content-Length: ".strlen($postdata)."\r\n";
                $request .= "\r\n";
                $request .= $postdata;
        }
        else
        {
                $request .= "\r\n";
        }

        /* WEB?????? */
        $fp = fsockopen($sUrl['host'], $sUrl['port']);

        /* ??????????? */
        if (!$fp)
        {
                die("ERROR\n");
        }

        /* ??????? */
        fputs($fp, $request);

        /* ??????? */
        $response = "";
        while (!feof($fp))
        {
                $response .= fgets($fp, 4096);
        }

        /* ????? */
        fclose($fp);

        /* ?????????????? */
        $DATA = split("\r\n\r\n", $response, 2);

        /* ???????????????????? */
        //echo "<!--\n".$request."\n-->\n";

        /* ???????????????????? */
        //echo "<!--\n".$DATA[0]."\n-->\n";

        /* ??????????? */
        return $DATA[1];
}




/**
 * return the REAL IP adres of the client
 * 
 * the colons in IPv6 addresses do not go well with file names for example.
 * This is why it's possible to replace them directly in this function
 * 
 * 
 * @var string $sReplaceColonWithChar replace colons(:) in ipv6 addresses with this character
 * @return string
 */
function getIPAddressClient($sReplaceColonWithChar = '')
{
    $sIP = '';

    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $sIP = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $sIP = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $sIP = $_SERVER['REMOTE_ADDR'];
    }

    if ($sReplaceColonWithChar != '')
    {
        $sIP = str_replace(':', $sReplaceColonWithChar, $sIP);
    }

    return $sIP;
}

/**
 * get protocol of url
 * ftp, http, https
 * @param string $sURL (if empty url of this script is used)
 */
function getProtocolURL($sURL = '')
{
	if ($sURL == '')
		$sURL = getURLThisScript();
	
	$arrSplit = explode('://', $sURL);
	
	return $arrSplit[0];
}

/**
 * get a somewhat unique fingerprint of the user.
 * don't rely on it to be super unique.
 * ip addresses can change, some same-version browsers can return the same 
 * fingerprint
 * can we add more? resolution monitor? browse history? installed fonts?
 * 
 * @param bool $bIncludeIPAddress
 * @return string
 * 
 */
function getFingerprintBrowser($bIncludeIPAddress = true)
{
    $arrParts = array();
    $sFingerprint = '';
    if ($bIncludeIPAddress)
        $sFingerprint .= getIPAddressClient();

    $sFingerprint .= $_SERVER['HTTP_USER_AGENT'];
    $sFingerprint .= $_SERVER['HTTP_ACCEPT_ENCODING'];
    // $sFingerprint .= $_SERVER['HTTP_ACCEPT'];
    if (isset($_SERVER['HTTP_DNT']))
        $sFingerprint .= $_SERVER['HTTP_DNT'];//do not track
    else
        $sFingerprint .= 'nope';

    /*
    HTTP_ACCEPT_LANGUAGE
    accept language, we want to get rid of the last parts after the ';'

    examples: 
    Accept-Language: de
    Accept-Language: de-CH
    Accept-Language: en-US,en;q=0.5
    en-US,en;q=0.7,nl;q=0.3
    Accept-Language: da, en-gb;q=0.8, en;q=0.7
    */

    $arrParts = explode(';', $_SERVER['HTTP_ACCEPT_LANGUAGE']);
    $sFingerprint .=  $arrParts[0];

    //HTTP_ACCEPT
    // this is set to */* in a fetch request so we can't use it, while a normal browser request is something like: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8 but you can't change that because you also want to upload files with fetch for other file types ;)
    // $arrParts = explode(';', $_SERVER['HTTP_ACCEPT']);
    // $sFingerprint .=  $arrParts[0];

//@todo remove --> there is a bug that sometimes generates 2 different fingerprints on different page loads for the same browser, this function logs that in order to see the difference
// logDebug('lib_sysinet: getFingerprintBrowser()','fingerprintginger: '.$sFingerprint);    

    return md5($sFingerprint);
}

/**
 * get name of the webbrowser
 *
 * returns '' if it didn't detect a popular browser
 * 
 * @return string
 */
function getBrowserName()
{
    $sAgent = '';
    $sAgent = $_SERVER['HTTP_USER_AGENT'];

    //in order of common-ness (to increase speed)
    if (stripos($sAgent, 'chrome') !== false)
    {   
        //edge has chrome, safari and edg in the user agent string
        if (stripos($sAgent, 'edg') !== false)
            return BROWSER_EDGE; 

        //opera uses webkit/chrome/safari
        if (stripos($sAgent, 'opr') !== false)
            return BROWSER_OPERA;

        return 'Chrome';
    }

    if (stripos($sAgent, 'googlebot') !== false)
        return BROWSER_GOOGLEBOT;

    if (stripos($sAgent, 'safari') !== false)
        return BROWSER_SAFARI;

    if (stripos($sAgent, 'firefox') !== false)
        return BROWSER_FIREFOX;

    if (stripos($sAgent, 'lynx') !== false)
        return BROWSER_LYNX;        


    //the ancient ones
    if (stripos($sAgent, 'msie') !== false)
        return BROWSER_INTERNETEXPLORER;      
        
    if (stripos($sAgent, 'explorer') !== false)
        return BROWSER_INTERNETEXPLORER;          

    //no popular browser found
    return '';
}


/**
 * get operating system of the webbrowser 
 * mac, windows, linux etc
 *
 * @return string
 */
function getBrowserOS()
{
    $sAgent = '';
    $sAgent = $_SERVER['HTTP_USER_AGENT'];

    //in order of common-ness (to increase speed)
    if (stripos($sAgent, 'windows') !== false)
        return OS_WINDOWS;

    if (stripos($sAgent, 'android') !== false)
        return OS_ANDROID;

    if (stripos($sAgent, 'mac') !== false)
        return OS_MAC;

    if (stripos($sAgent, 'ipad') !== false)
        return OS_IPAD;     
        
    if (stripos($sAgent, 'iphone') !== false)
        return OS_IPHONE;           

    if (stripos($sAgent, 'linux') !== false)
        return OS_LINUX;

    //the ancient ones
    if (stripos($sAgent, 'nokia') !== false)
        return OS_NOKIA;

    if (stripos($sAgent, 'ipod') !== false)
        return OS_IPOD;        

    if (stripos($sAgent, 'blackberry') !== false)
        return OS_BLACKBERRY;        

    //no popular os found
    return '';
}


/**
 * surrogate for isValidEmail();
 * 
 * it is here for compatibility reasons with CMS2
 * 
 * kijkt naar een emailadres of dit geldig is ja of nee (LET OP WERKT NIET OP ALS PHP OP WINDOWS DRAAIT)
 * kijkt of layout emailadres goed is, zo ja dan checked deze of de server bestaat
 *
 * @param string $emailadres
 * @param bool $bCheckMXRecord in loops (zoals bij nieuwsbriefemail versturen) kan het heel ongewenst zijn om het mx record te checken voor iedere entry
 * @return bool - true = goed emailadres, false= geen geldig emailadres
 */
function checkEmail($sEmailAddress, $bCheckMXRecord = false)
{
    return isValidEmail($sEmailAddress, $bCheckMXRecord);
}

/**
 * checks if email address is valid
 *
 * kijkt naar een emailadres of dit geldig is ja of nee (LET OP WERKT NIET OP ALS PHP OP WINDOWS DRAAIT)
 * kijkt of layout emailadres goed is, zo ja dan checked deze of de server bestaat
 * 
 * @param string $emailadres
 * @param bool $bCheckDNSRecord in loops (zoals bij nieuwsbriefemail versturen) kan het heel ongewenst zijn om het mx record te checken voor iedere entry
 * @param bool $bCheckLatinChars checks for latin characters to preven the cyrillic А to the latin A. This is used to spoof email addresses. an emailaddress looking to come from apple.com but is actually coming from *cyrillic A*pple.com
 * @return bool - true = goed emailadres, false= geen geldig emailadres
 */
function isValidEmail($sEmailAddress, $bCheckDNSRecord = false, $bCheckLatinChars = false)
{
    if ($bCheckLatinChars)
    {
        $sWhiteList = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTuVWXYZ_-@.';
        $sLatinFilter = '';
        $sLatinFilter = filterBadCharsWhiteList($sEmailAddress, $sWhiteList);
        // vardump($sLatinFilter, 'hootietooei');
        if ($sLatinFilter != $sEmailAddress)
            return false;
    }

  	if (!filter_var($sEmailAddress, FILTER_VALIDATE_EMAIL))
        return false;


    //last: check mx record
    if ($bCheckDNSRecord)
    {
        $arrExplode = array();
        $arrExplode = explode('@',$sEmailAddress);
        if (count($arrExplode) == 2)
            return checkdnsrr($arrExplode[1],'MX');
        else
            return false; //no domain = instant error
    }

    return true;
}

/**
 * obfuscate email address : 
 * email@example.com --> em***@e******.c**
 * 
 * for security reasons we sometimes need to show an email address that is not visible to everybody
 */
function obfuscateEmail(&$sEmailAddress, $sObfuscateChar = '*')
{
    $sResult = '';
    $iPosAt = 0;
    $iPosDot = 0;
    $iLenEmail = 0;

    if (!$sEmailAddress) //if empty: quit
        return '';

    $sResult = $sEmailAddress;
    $iPosAt = strpos($sEmailAddress, '@');
    $iPosDot = strpos($sEmailAddress, '.');
    $iLenEmail = strlen($sEmailAddress);

    //====replace chars with stars
    //replace before at (@)
    for ($iPos = 2; $iPos < $iPosAt; ++$iPos)
    {
        $sResult[$iPos] = $sObfuscateChar;
    }

    //replace after at (@) before dot (.)
    for ($iPos = $iPosAt+2; $iPos < $iPosDot; ++$iPos)
    {
        $sResult[$iPos] = $sObfuscateChar;
    }

    //replace after dot (.)
    for ($iPos = $iPosDot+2; $iPos < $iLenEmail; ++$iPos)
    {
        $sResult[$iPos] = $sObfuscateChar;
    }


    return $sResult;
}

/**
 * placeholder for obfuscateEmail()
 *
 */
function emailObfuscate(&$sEmailAddress)
{
    return obfuscateEmail($sEmailAddress);
}

/**
 * check if url is valid
 * 
 * source: https://www.w3schools.com/php/php_form_url_email.asp
 */
function isValidURL($sURL)
{
    return preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$sURL);
}

/** 
 * Generate a fingerprint of an email address.
 * 
 * You can use this in a database to quickly lookup an email address without storing the actual email address 
 * in plain text in the database (to comply with european law).
 * You can 2-way encrypt the email address, but then you can't look it up in the database anymore
 * (because the output of 2 way encryption changes over time).
 * So in addition to storing the 2-way-encrypted email address in the database, store an email address identifier
 * 
 * This function will generate a fingerprint based on an email address that is the same every single time.
 * So, to look up an email address in a database table:
 * -generate the email fingerprint from the users email address (with this function)
 * -lookup the records with the same fingerprints in the database 
 *      (you will get a reasonable amount records back, probably just 1)
 * -compare the 2 way encrypted email address with the email of this is supplied by the user 
 *      to see if you have the right record (you need 2 fields: fingerprint field and 2way encrypted email address field)
 * 
 * Be aware that this id is reasonably unique, but not entirely.
 * Different email addresses can generate the same ids (although rare)
 * 
 * For security reasons, most after the @ sign is thrown away.
 * When you want to crack the data, you are probably going to look at @-signs and domain names as 
 * common denominator to see verify if your output makes sense.
 * This function mutulates the email address enough that the output makes no sense even when you crack the hash
 * 
 * THIS IS NOT A CRYPTOGRAPHICALLY SAFE FUNCTION!!!
 * but enough to keep attackers busy for a while
 * 
 * @param string $sEmailAddress
 * @param string $sSeed so this function doesn't generate the same id everywhere throughout the whole system
 * @param string $sAlgorithm 
 * @return string with id (length depends on algorithm)
*/
function getFingerprintEmail($sEmailAddress, $sSeed, $sAlgorithm = ENCRYPTION_DIGESTALGORITHM_DEFAULT)
{
    $sResult = '';
    $sEmailAddress = strtolower($sEmailAddress); //case insensitive
    $sResult = $sEmailAddress;
    $sEmailAddressMD5 = '';

    if (isValidEmail($sEmailAddress))
    {
        $arrExplAt = array();
        $arrExplDot = array();
        $arrExplAt = explode('@', $sEmailAddress);

        $arrExplDot = explode('.', $arrExplAt[1]);
        
        //I use md5 for quick (seemingly random) additions to the result-string that we eventually going to hash much stronger later
        $sEmailAddressMD5 = md5($sEmailAddress);

        $sResult = lastChar($arrExplAt[0]).
                    lastChar($sEmailAddress).
                    md5($arrExplAt[0]).
                    md5($sSeed).
                    (strlen($sEmailAddress)-2).
                    strlen($arrExplAt[1]).
                    strlen($arrExplDot[0]).
                    $sEmailAddressMD5.
                    $sEmailAddressMD5[0];
    }
    
    //the real hashing
    $sResult = hash($sAlgorithm, $sResult);

    return $sResult;
}

/**
 * filter user input for XSS/javascript injection in <INPUT TYPE="text"> fields
 * 
 * @param mixed $mInput string or array
 * @param bool $bAllowHTML
 */
DEFINE('FILTERXSS_ALLOW_HTML_NONE', 0);//NO HTML = default, to prevent cross site scripting
DEFINE('FILTERXSS_ALLOW_HTML_MARKUP', 1);//<b><i><u> tags
DEFINE('FILTERXSS_ALLOW_HTML_FILTEREDXSS', 2);//gefilterd
DEFINE('FILTERXSS_ALLOW_ALL', 3);//not filtered at all
function filterXSS($mInput, $iAllowHTML = FILTERXSS_ALLOW_HTML_NONE)
{
    


    if ($iAllowHTML == FILTERXSS_ALLOW_ALL)
        return $mInput;

    //is array
    if (is_array($mInput))
    {
        $iLen = $mInput->count();
        for ($iIndex = 0; $iIndex < $iLen; ++$iIndex)
        {
            $mInput[$iIndex] = filterXSSRecursive($mInput[$iIndex], $iAllowHTML);
        }
    }
    else
    {
        $mInput = filterXSSRecursive($mInput, $iAllowHTML);
    }


	return $mInput;
}

/**
 * WARNING: DON'T USE THIS FUNCTION, USE filterXSS() INSTEAD!!!!!!!!!!
 * 
 * this is the recursive part of filterXSS()
 */
function filterXSSRecursive($mOrgValue, $iAllowHTML)
{    
    $mNewValue = '';
    $mNewValue = $mOrgValue;


    $mNewValue = mb_convert_encoding($mNewValue, "UTF-8", mb_detect_encoding($mNewValue)); //make sure everything is UTF-8 to prevent funny business with encoding (like a < character in utf8 while the rest has another encoding)


    //start filtering xss
    switch ($iAllowHTML)
    {
        case FILTERXSS_ALLOW_HTML_NONE:		
                //FIRST ROUND: PHP NATIVE
                $mNewValue = strip_tags($mNewValue); //whitelist of allowed html tags		

                //SECOND ROUND: strip tags manually
                $mNewValue = strip_tags($mNewValue);	
                $mNewValue = str_ireplace('<', '', $mNewValue); //utf equivalent
                $mNewValue = str_ireplace(htmlentities('<'), '', $mNewValue); //html equivalent
                $mNewValue = str_ireplace(urlencode('<'), '', $mNewValue); //url equivalent					
                $mNewValue = str_ireplace('>', '', $mNewValue); //utf equivalent
                $mNewValue = str_ireplace(htmlentities('>'), '', $mNewValue); //html equivalent
                $mNewValue = str_ireplace(urlencode('>'), '', $mNewValue); //url equivalent					
                return $mNewValue;
                break;
        case FILTERXSS_ALLOW_HTML_MARKUP:
                //FIRST ROUND: PHP NATIVE
                $mNewValue = strip_tags($mNewValue, '<br><br/><p><b><i><u><font><strong>'); //whitelist of allowed html tags. SVG is not on the list for security reasons (possible to embed links)

                //SECOND ROUND: HTML PURIFYER
                //NOTE: disallows <iframe><script><svg><embed> because of possibily to embed links
                $mNewValue = purifyHTML($mNewValue, array('br', 'p', 'b', 'i', 'u', 'font', 'strong'), array('class', 'name', 'id'));

                return $mNewValue;
                break;
        case FILTERXSS_ALLOW_HTML_FILTEREDXSS:			
                //FIRST ROUND: PHP NATIVE
                $mNewValue = strip_tags($mNewValue, '<br><br/><p><b><i><u><font><strong><a><img><table><tr><thead><td><th><div><span><h1><h2><h3><h4><h5><h6><ol><ul><li><hr>'); //whitelist of allowed html tags

                //SECOND ROUND: HTML PURIFYER
                //NOTE: disallows <iframe><script><svg>(because of possibily to embed links)<embed>
                $mNewValue = purifyHTML($mNewValue, array('br', 'p', 'b', 'i', 'u', 'font', 'strong', 'a', 'img', 'table', 'tr', 'thead', 'th', 'div', 'span', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ol', 'ul', 'li', 'hr'), array('class', 'name', 'id', 'href'));
                

                //do extra filters these (if for some hacky reason a bad actor still managed to sneak something in)
                $arrBannedWords = array('script', 'javascript', 'vbscript', 'iframe', 'location.href',
                        'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut',
                        'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate',
                        'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut',
                        'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend',
                        'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange',
                        'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                        'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover',
                        'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange',
                        'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted',
                        'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'
                );

                foreach ($arrBannedWords as $sBannedWord)
                {
                    $mNewValue = str_ireplace($sBannedWord, '', $mNewValue); //case insensitive replace
                    $mNewValue = str_ireplace($sBannedWord, '', $mNewValue); //utf equivalent
                    $mNewValue = str_ireplace(htmlentities($sBannedWord), '', $mNewValue); //html equivalent
                    $mNewValue = str_ireplace(urlencode($sBannedWord), '', $mNewValue); //url equivalent
                }

                return $mNewValue;	
        default:
            //FILTERXSS_ALLOW_ALL
            break;
                    
    }//switch	

} //end: function recursive


/**
 * alias for filterXSS()
 * filter user input for XSS/javascript injection
 * 
 * @param mixed $mInput string or array
 * @param bool $bAllowHTML
 */
function filterJavascriptInjection($mInput, $iAllowHTML = FILTERXSS_ALLOW_HTML_NONE)
{
    return filterXSS($mInput, $iAllowHTML);
}


/**
 * Find and DELETE url from string
 *
 * @param string $sInput
 * @param string $sReplaceText
 * @param bool $bProtocolDetect (http/ftp etc) if true: looks for protocols in the text
 * @param bool $bTLDDetect (top level domain) if true: also detects end of sentences without space as url like: "Thank you.This was helpful"
 * @return string
 */
function filterURL($sInput, $sReplaceText = '[url removed]', $bProtocolDetect = true, $bTLDDetect = true)
{
    $arrExplode = array();
    $iCount = 0;
    $sOutput = $sInput;

    //method 1: filter on protocol and beginning of www and http
    if ($bProtocolDetect)
    {
        $arrExplode = explode(' ', $sInput);
        $iCount = count($arrExplode);
        for ($iIndex = 0; $iIndex < $iCount; ++$iIndex)
        {
            if (is_numeric(stripos($arrExplode[$iIndex], '://'))) //protocol divider like in: https://
            {
                $arrExplode[$iIndex] = $sReplaceText;
            }
            else
            {
                if (startswith($arrExplode[$iIndex], 'http')) //also includes https
                    $arrExplode[$iIndex] = $sReplaceText;
                if (startswith($arrExplode[$iIndex], 'ftp'))
                    $arrExplode[$iIndex] = $sReplaceText;
                if (startswith($arrExplode[$iIndex], 'www.'))
                    $arrExplode[$iIndex] = $sReplaceText;
            }
        }
        $sOutput = implode(' ', $arrExplode);
    }

    //method 2: filter on TLDs like .com and .nl
    if ($bTLDDetect)
    {
        //$patternexample = '/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/';     
        $sOutput = preg_replace( '/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,10}(\/\S*)?/', $sReplaceText, $sOutput);
    }

    return $sOutput;
}

/**
 * sanitize a string to be a valid url
 * 
 */
function sanitizeURL($mInput)
{
    return filter_var($mInput, FILTER_SANITIZE_URL);
}

/**
 * sanitize a string to be a valid email
 */
function sanitizeEmailAddress($mInput)
{
    return filter_var($mInput, FILTER_SANITIZE_EMAIL);
}

/**
 * purifies HTML
 * 
 * uses HTMLTag class
 * 
 * @param string $sHTML input HTML
 * @return string purified HTML
 */
function purifyHTML($sHTML, $arrAllowedTags = [], $arrAllowedAttributes = [])
{



    //==== BEGINS HERE ====

    //cant find class
    if (!class_exists('dr\classes\dom\tag\HTMLTag', true))
    {
        logError(__FILE__.__FUNCTION__.__LINE__, 'Class HTMLTag does not (yet) exist');
        return '';
    }

    $objHTML = new dr\classes\dom\tag\HTMLTag();
    $objHTML->parse($sHTML, 100);
    purifyHTMLRecursive($objHTML, $arrAllowedTags, $arrAllowedAttributes);
    return $objHTML->render();
}

/**
 * WARNING: DON'T USE THIS FUNCTION, USE purifyHTML() INSTEAD!!!!!!!!!!
 * 
 * this is the recursive part of purifyHTML()
 */
function purifyHTMLRecursive($objNode, $arrAllowedTags, $arrAllowedAttributes)
{       
    //declare
    $bTagAllowed = false;
    $bAttributeAllowed = false;
    $arrAttrNames = array();
    $arrAttributes = array();
    $arrChilds = array();
    $iCountTagsAllowed = 0;
    $iCountAttrAllowed = 0;
    $iCountChilds = 0;
    $objParent = null;

    //init
    $iCountTagsAllowed = count($arrAllowedTags);
    $iCountAttrAllowed = count($arrAllowedAttributes);
    $arrChilds = $objNode->getChildren();
    $iCountChilds = count($arrChilds);
    $objParent = $objNode->getParentNode(); //can be null

    if ($objNode->isTextNode())
        return; //halt further execution, because there is nothing else todo in a text node

    //==== is tag allowed?
    if (($iCountTagsAllowed > 0) && ($objParent)) //only when there are arrAllowedTags and when its NOT the root node
    {           
        //go through the allowed tags 
        for ($iIndex = 0; $iIndex < $iCountTagsAllowed; ++$iIndex)
        {
            if ($objNode->getTagName() == $arrAllowedTags[$iIndex])
            {
                $bTagAllowed = true;
                $iIndex = $iCountTagsAllowed;//jump out of loop
            }
        }

        //remove tag
        if ($bTagAllowed === false)
        {
            if ($objParent->removeChild($objNode) === false) //remove
                logError(__FILE__.__LINE__.__FUNCTION__, 'remove child with tagname "'.$objNode->getTagName().'" failed');
            return; //halt further execution, because there is nothing else todo
        }
    }

    //==== is each attribute allowed?
    if (($iCountAttrAllowed > 0) && ($objParent)) //only when there are arrAllowedAttr and when its NOT the root node 
    {
        //go through attributes
        $arrAttrNames = $objNode->getAttributes();
        $arrAttributes = $objNode->getAttributesAss();
        if (count($arrAttrNames) > 0)
        {
            foreach ($arrAttrNames as $sAttrName)
            {
                $bAttributeAllowed = false;

                for ($iIndex = 0; $iIndex < $iCountAttrAllowed; ++$iIndex)
                {
                    if ($sAttrName == $arrAllowedAttributes[$iIndex])
                    {
                        $bAttributeAllowed = true;
                    }
                }

                //remove attribute
                if ($bAttributeAllowed === false)
                {
                    if ($objNode->removeAttribute($sAttrName) === false) //remove
                        logError(__FILE__.__LINE__.__FUNCTION__, 'remove attribute "'.$sAttrName.'" failed');
                }                
            }//end: for
        }//end: if
    } //end: if

    //has child elements? then recursive call for each child
    if ($iCountChilds > 0)
    {
        for ($iIndex = 0; $iIndex < $iCountChilds; ++$iIndex)
        {
            purifyHTMLRecursive($arrChilds[$iIndex], $arrAllowedTags, $arrAllowedAttributes);
        }
    }
}

/**
 * purifies HTML of an SVG image
 * 
 * uses HTMLTag class
 * 
 * @param string $sHTML input HTML
 * @return string purified HTML
 */
function purifyHTMLSVG($sSVG)
{
    $arrAllowedTags = array(
        'animate',
        'animateMotion',
        'animateTransform',
        'circle',
        'clipPath',
        'defs',
        'desc',
        'ellipse',
        'feBlend',
        'feColorMatrix',
        'feComponentTransfer',
        'feComposite',
        'feConvolveMatrix',
        'feDiffuseLighting',
        'feDisplacementMap',
        'feDistantLight',
        'feDropShadow',
        'feFlood',
        'feFuncA',
        'feFuncB',
        'feFuncG',
        'feFuncR',
        'feGaussianBlur',
        'feImage',
        'feMerge',
        'feMergeNode',
        'feMorphology',
        'feOffset',
        'fePointLight',
        'feSpecularLighting',
        'feSpotLight',
        'feTile',
        'feTurbulence',
        'filter',
        'foreignObject',
        'g',
        'image',
        'line',
        'linearGradient',
        'marker',
        'mask',
        'metadata',
        'mpath',
        'path',
        'pattern',
        'polygon',
        'polyline',
        'radialGradient',
        'rect',
        'set',
        'stop',
        'style',
        'svg',
        'switch',
        'symbol',
        'text',
        'textPath',
        'title',
        'tspan',
        'use',
        'view',        
    );
    $arrAllowedAttributes = array(
        'accumulate',
        'additive',
        'alignment-baseline',
        'amplitude',
        'attributeName',
        'attributeType',
        'azimuth',
        'baseFrequency',
        'baseline-shift',
        'baseProfile',
        'begin',
        'bias',
        'by',
        'calcMode',
        'class',
        'clip',
        'clipPathUnits',
        'clip-path',
        'clip-rule',
        'color',
        'color-interpolation',
        'color-interpolation-filters',
        'crossorigin',
        'cursor',
        'cx',
        'cy',
        'd',
        'decoding',
        'diffuseConstant',
        'direction',
        'display',
        'divisor',
        'dominant-baseline',
        'dur',
        'dx',
        'dy',
        'edgeMode',
        'elevation',
        'end',
        'exponent',
        'fetchpriority',
        'fill',
        'fill-opacity',
        'fill-rule',
        'filter',
        'filterUnits',
        'flood-color',
        'flood-opacity',
        'font-family',
        'font-size',
        'font-size-adjust',
        'font-stretch',
        'font-style',
        'font-variant',
        'font-weight',
        'fr',
        'from',
        'fx',
        'fy',
        'glyph-orientation-horizontal',
        'glyph-orientation-vertical',
        'gradientTransform',
        'gradientUnits',
        'height',
        'id',
        'image-rendering',
        'in',
        'in2',
        'intercept',
        'k1',
        'k2',
        'k3',
        'k4',
        'kernelMatrix',
        'kernelUnitLength',
        'keyPoints',
        'keySplines',
        'keyTimes',
        'lang',
        'lengthAdjust',
        'letter-spacing',
        'lighting-color',
        'limitingConeAngle',
        'marker-end',
        'marker-mid',
        'marker-start',
        'markerHeight',
        'markerUnits',
        'markerWidth',
        'mask',
        'maskContentUnits',
        'maskUnits',
        'max',
        'media',
        'method',
        'min',
        'mode',
        'numOctaves',
        'offset',
        'opacity',
        'operator',
        'order',
        'orient',
        'origin',
        'overflow',
        'paint-order',
        'path',
        'pathLength',
        'patternContentUnits',
        'patternTransform',
        'patternUnits',
        'ping',
        'pointer-events',
        'points',
        'pointsAtX',
        'pointsAtY',
        'pointsAtZ',
        'preserveAlpha',
        'preserveAspectRatio',
        'primitiveUnits',
        'r',
        'radius',
        'referrerPolicy',
        'refX',
        'refY',
        'rel',
        'repeatCount',
        'repeatDur',
        'requiredExtensions',
        'requiredFeatures',
        'restart',
        'result',
        'rotate',
        'rx',
        'ry',
        'scale',
        'seed',
        'shape-rendering',
        'side',
        'slope',
        'spacing',
        'specularConstant',
        'specularExponent',
        'spreadMethod',
        'startOffset',
        'stdDeviation',
        'stitchTiles',
        'stop-color',
        'stop-opacity',
        'stroke',
        'stroke-dasharray',
        'stroke-dashoffset',
        'stroke-linecap',
        'stroke-linejoin',
        'stroke-miterlimit',
        'stroke-opacity',
        'stroke-width',
        'style',
        'surfaceScale',
        'systemLanguage',
        'tabindex',
        'tableValues',
        'target',
        'targetX',
        'targetY',
        'text-anchor',
        'text-decoration',
        'text-rendering',
        'textLength',
        'to',
        'transform',
        'transform-origin',
        'type',
        'unicode-bidi',
        'values',
        'vector-effect',
        'version',
        'viewBox',
        'visibility',
        'width',
        'word-spacing',
        'writing-mode',
        'x',
        'x1',
        'x2',
        'xChannelSelector',
        'xml:lang',
        'xml:space',
        'y',
        'y1',
        'y2',
        'yChannelSelector',
        'z',
        'zoomAndPan',
    );

    $sResult = '';
    $sResult = purifyHTML($sSVG, $arrAllowedTags, $arrAllowedAttributes);

    if ($sResult !== $sSVG)
    {
        logDebug(__FILE__.':'.__LINE__,'purifySVG: BEFORE:'.$sSVG);
        logDebug(__FILE__.':'.__LINE__,'purifySVG: AFTER:'.$sSVG);
    }

    return $sResult;
}

?>
