<?php
        /****
         * THE OLD lib_std.php FROM CMS2
         * ===============================
         * this is the backwards compatible library, to maintain compatibility with CMS2
         * In this library all the functions are disabled that the CMS3/4/5 libraries replaces
         */

	/********************************************************************************************************************************
	NAME GIVING CONVENTIONS
	i	integer		$iGetal
	s	string		$sString
	img	image		$imgPlaatje
	b	boolean		$bIsWaar
	f	float		$fMijnGedeeldGetal
	
	********************************************************************************************************************************/
 



	/********************************************************* VERSIE BEHEER ********************************************************
         * 
		 * 26-7-2023
		 * ==========
		 * addDBRecord() added real_escape_string()
		 * 
		 * 
         * 4 april 2019
         * ============
         * - alle functies weg gecomment die de cms3/4 functies vervangen (lang leve de //)
         * 
         * 
         * 3 april 2019 - ingrijpende veranderingen verschillende functies om geschikt voor php 7 te maken
         * ============
         * -mysqliToArray() bugfix if results from sql query like 'DELETE' for example
         * -logPageVisit() verwijderd
         * - ... heel veel functies aangepast
         * 
         * 
         * 25 juli 2017
         * ===========
         * -redirect to www, kan nu ook een ander subdomein zijn
         * 
         * 19 jul 2017
         * -==========
         * lib_cust functions die algemeen zijn, verhuisd naar lib_std 
         * 
         * 4 juni 2017
         * =========
         * function redirectWWW toegoevoegd
         * function redirectToHTTPS geeft boolean terrug of deze geredirect wordt
        
         2 juni 2017
         * =======
         * function getSubdomain($sHostname) bugfix -->als http://domein.nl dan werd het domein terug gegeven ipv subdomein
         * 
         * 
        15 mei 2017
         * ============
         * changeRecord() aangepast zodat deze 2 primary keys vreeet


	6 jan 2017
	===========
	-generatePrettyURLSafeURL() bugfix: accepteerde geen cijfers

	19 sept 2016
	===========
	-generatePrettyURLSafeURL() aangepast snelheid en . accepteren

         * 
         * 9 mrt 2016:
         * ============
         * hex2bin verwijderd
         * 
         * 
    14 jan 2015
    ============
    - filtersqlinjection aangepast op dubbele punt

         * 
         *     30 sept 2014
    ============
    - sendemail functie genereert goed messageid, waardoor Mail niet iedere keer op zn bek gaat
        
         * 
         * 12 juni 2013
    ============
    - filterSQLInjection aangepast
    

    12 juni 2012
    ============
    - httpgetpost geeft false terug ipv ERROR + script stop als het niet goed gaat


    3 juni 2012
    ============
    -doNotTrack
     
    25 mei 2012
    ===========
    -loadFromFileString, saveToFileString
     
   	24 mei 2012
	=========
	-getSearchKeywordsArray


	07 juli 2009
	========
	-sendemail() aangepast zodat mail via het Zend framework wordt verstuurd. Als framework niet geladen dan wordt oude methode gebruikt

	04 jan 08
	========
	-filterSQLInjection() lust nu als paramater ook een array. Dit is handig om bijvoorbeeld in 1 keer de $_GET array te filteren op foute input
	
	25 sept 08
	========
	-filterSQLInjection() geupdate, zodat deze nog veiliger is

	8 aug 08
	========
	-deleteRecordInclImg() geupdate, zodat je de veldnamen van de afbeeldingen zelf kan opgeven

	4 juni 08
	========
	-hex2bin()

	7 nov 07
	========
	-verschillende aanpassingen in verschillende functie voor php5

	21 aug 07
	========
	-bugfix: in de functie changeRecordOrder() werd $sVolgordeField niet altijd gebruikt, maar i_volgorde

	26 juli 07
	========
	-showSubjectBar nbsp zonder ;
	-de functie logPageVisit delete automatisch items ouder dan 6 maanden	
	
	29 mei 07
	=========
	-sendemail geupdate -> mogelijk om bij attachment "nice_filename" als optie op te geven

	10 jan 07
	=========
	-sendemail geupdate -> deze kan nu attachments versturen en mime text format/html versturen

	03 jan 07
	=========
	-generateImageValidation vermijd de Q (deze kan teveel lijken op de O)
	-generatePrettyURLSafeURL
	
	29 dec 06
	=========
	-makeArrayKeyGlobalVar updates: checked nu of parameter array is

	29 okt 06
	=========
	-generateImageValidation toegevoegd
	-sendemail aangepast ivm flood protection

	6 okt 06
	=========
	-sendemail aangepast om email injection te voorkomen

	16 mei 06
	=========
	-toegevoegd downloadFile
	
	3 jan 06
	=========
	-bijgevoegd getImageByExtension bestandsformaten
	
	6 dec 05
	=========
	-bugfix:convertToMoney

	26 okt 05
	=========
	-bugfix:filterSQLInjection werkt niet goed

	25 okt 05
	=========
	-arrayunique (ter vervanging van de php functie array_unique)
	
	14 okt 05
	=========
	-getURLThisScript

	12 okt 05
	=========
	-convertPlaintText2HTML
	-extractFileFromPath geschikt gemaakt voor verschillende directory separators (dus windows paths kunnen nu ook)

	28 juni 05
	===========
	-function readlanguagefile($sPathLanguageFile)
	-function includeAll
	-function br2nl($sValue)
	-function debug($sValue = null)
	-function deb($sValue = null) //alias for debug

	11 juni 05
	==========
	-generatiepassword  : leestekens genereren weggehaald. bepaalde letters worden niet meer gegenereerd (i, 0, 1, 0)
	
	24 april 05
	==========
	-generatiepassword genereerd vaker leters van cijfers / bugje : cijfers werden niet gegenereerd
	
	2 april 05
	==========
	-debug en error toegevoegd aan deze lib. file
	
	18 maart 05
	==========
	-generatePassword

	16 maart 05
	==========
	-filterSQLInjection
	
	10 maart 05
	==========
	-sendemail geeft een boolean terug
		
	5 maart 05
	==========
	-convertToMoney geupdate
	-convertToFloat (omgekeerde van convertToMoney)
	
	22 feb 05
	==========
	-extractDirectoryFromPath toegevoegd
	-extractFileFromPath toegevoegd
	
	18 feb 05
	==========
	-filterSearchKeywords
	
	24 jan 05
	==========
	-function checkEmail($emailadres) toegevoegd
	
	30 dec 04
	==========
	-bugfix convertToExBTW en convertToInBTW 
	-toevoeging convertToMoney
	
	12 dec
	=======
	-sendemail goed gemaakt
	
	7 dec
	=======
	-filterBadSQLChar bijgewerkt
	
	26 okt
	=======
	-convertToExBTW
	-convertToInBTW
	-replaceEnterBR
	
	24 okt
	=======
	-bug uit converToMoney

	20 okt
	=======
	-convertToMoney

	5 okt 04
	========
	-uploadfile aangepast
	
	30 sept 2004
	============
	-uploadFile toegevoegd

	12 augustus
	============
	-bug uit removeLastChar gehaald

	2 augustus
	==========
	-removeLastChar toegevoegd
	-getPreviousDirectory toegevoegd

	17 juni
	========
	-filterBadFileChar geupdate met nieuwe "foute" karakters
	-result en return doorelkaar gehaald --- hersteld
	-deleteRecord en deleteRecordImage sql fout ondervanging beter (sql query wordt weergegeven)

	1 juni
	=======
	-nieuwe functie :getFileFolderArrayExtension
	-getImageByExtension case insensitive gemaakt
	-getFileArray / getFileFolderArray aangepast. Eerst wordt gecontroleerd of opgegeven directory wel bestaat

	24 mei :
	==========
	-nieuwe functie : dayOfWeekToString

	10 mei:
	==========
	-nieuwe functie : getSiteContentImageSmall
	-nieuwe functie : getSiteContentImageLarge
	-nieuwe functie : getFileFolderArray
	-nieuwe functie : random

	11 mei:
	==========
	-nieuwe functie : getSiteContentTitle
	
	

	*********************************************************************************************************************************/





    
/**
 * requests content from database and returns it as array in php7 mysqli way
 * 
 * uses also alternative text (at random)
 *
 * @return array --> empty if not found
 * @param string $sPage
 * @param int $iSiteID
 * @param int $iWichContent 0 = choose random, 1= regular, 2 = alternative
 */
function getContentDB($sPage, $iSiteID, $iWichContent = 0)
{
	$arrContent = array();
	global $objDB;
	global $tblSitecontent;
        
        if ($iWichContent == 0) //randomize termines is alternative content is loaded
            $iWichContent = rand ( 1 , 2 ); //1= load alternative
        
       

        $objResult = $objDB->query('SELECT * FROM '.$tblSitecontent.' WHERE s_pagina = "'.$sPage.'" AND i_siteid ="'. $iSiteID.'"');

        if ($objResult)
        {
            $arrRecords = array();            
            while ($arrRecord = $objResult->fetch_assoc())
            {
                $arrContent = $arrRecord;
                if ($iWichContent == 2)
                {
                    $arrContent['s_titel'] = $arrRecord['s_titel_alt'];
                    $arrContent['s_htmltitle'] = $arrRecord['s_htmltitle_alt'];
                    $arrContent['s_htmldescription'] = $arrRecord['s_htmldescription_alt'];
                    $arrContent['s_tekst'] = $arrRecord['s_tekst_alt'];  
                }
                                          
            }   

        }
        else
        {
            $arrContent['s_titel'] = '[page not found]';
            $arrContent['s_htmltitle'] = '';
            $arrContent['s_htmldescription'] = '';
            $arrContent['s_tekst'] = '[page not found]';
            $arrContent['s_plaatjeurlklein'] = '';
            $arrContent['s_plaatjeurl'] = '';
        }

	return $arrContent;
}

    /**
     * looks if a file is older then $iDaysCacheTimeOut
     * 
     * @param string $sFilePath
     * @param int $iDaysCacheTimeOut if 0 zero, function will always return true
     * @return bool
     */
    function isCacheFileTimedOut($sFilePath, $iDaysCacheTimeOut)
    {
        if ($iDaysCacheTimeOut == 0)
        {
            return true;
        }
        
        $iFileTime = 0;
        $bUpdateCacheFile = true;
        $bFileExists = file_exists($sFilePath);
        if ($bFileExists)
        {
            $iFileTime = filemtime($sFilePath);
            $bUpdateCacheFile = ($iFileTime < (time() - ($iDaysCacheTimeOut * 60 * 60 * 24)));  
        }
 
        
        return $bUpdateCacheFile;
    }
    
    /**
     * example $sOutput = renderTemplate('skin_stdtwocolumn.php', get_defined_vars());
     * 
     * @param string $sPathTemplateFile
     * @param array $arrVariables
     * @return type
     */
//    function renderTemplate($sPathTemplateFile, &$arrVariables)
//    {
//        //set all vars
//        $arrKeys = array_keys($arrVariables);
//        foreach ($arrKeys as $sKey)
//        {
//            $$sKey = $arrVariables[$sKey];
//        }
//        
//        //get template content
//        ob_start();
//        include $sPathTemplateFile;
//        $sOutput = ob_get_contents();
//        ob_end_clean();  
//        
//        return $sOutput;
//    }
    
    /**
     * returns a file safe and sql injection safe url-part (https:// etc is filtered)
     * @param string $sPrettyURL
     * @return string
     */
    function filterPrettyURL($sPrettyURLID)
    {
        $sPrettyURLID = filterSQLInjection($sPrettyURLID);
	$sWhitelist = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ.-';
	return preg_replace( '/[^'.$sWhitelist.']/', '', $sPrettyURLID );
    }
    
    
//    function filterBadCharsWhiteList($sValue, $sWhitelist = null) 
//    {
//            if ($sWhitelist == null) //als geen whitelist/array meegegeven dan zelf een samenstellen
//                    $sWhitelist = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ., éëèęėēúüûùūíïìióöôòáäâà';
//
//            return preg_replace( '/[^'.$sWhitelist.']/', '', $sValue );
//    }    
    
    /**
     * counts number of spaces in a string
     * 
     * @param string $sInput
     * @return int
     */
    function getWordCount($sInput)
    {
        $iCount = 0;
        $iCount+= substr_count ($sInput , ' ');
        $iCount+= substr_count ($sInput , "\n");
        return $iCount;
    }
    
    
    /* =================================================================================================================
    *	function 	: getValuePlus1 (mysqli version)
    *	programmer	: Dennis Renirie
    *	date 		: 3 juni 2017
    * 	input		: $sField (bv i_d), $sTable
    *	output		: max waarde van $sField + 1
    * 	use for		: snel nieuwe id geven bijvoorbeeld of nieuwe sorteercode
    *	description	: 
    * =================================================================================================================*/
    function getDBValuePlus1($sField, $sTable)
    {
        global $objDB;
        
        $sSQL = "SELECT MAX($sField) AS maxval FROM $sTable";
        $objResult = $objDB->query($sSQL); 
        $arrResult= array();
        while ($arrCurrRecord = $objResult->fetch_assoc())
        {
            $arrResult = $arrCurrRecord;
        }
    
        return (((int)$arrResult['maxval']) + 1);
    }    
    
    
    /* =================================================================================================================
    *	function 	: addRecord (mysqli version)
    *	programmer	: Dennis Renirie
    *	date 		: 3 jun 2017
    * 	input		: $arrVariables (array met fieldnames van mysql), $arrValues (array met waardes van de fields), $sTable (tabel in mysql)
    *	@return	bool
    * 	use for		: toevoegen van een record aan mysql
    *	description	: 
    * =================================================================================================================*/
    function addDBRecord($arrVariables, $arrValues, $sTable)
    {
        global $objDB;
        
        $sSQL = "INSERT INTO $sTable SET ";
        for ($iTeller = 0; $iTeller < count($arrVariables); $iTeller++)
        {
                // $arrValues[$iTeller] = str_replace("'", "`", $arrValues[$iTeller]);  //removed 26-7-2023	
				$arrValues[$iTeller] = $objDB->real_escape_string($arrValues[$iTeller]);// added 26-7-2023
                $sSQL = $sSQL.$arrVariables[$iTeller]."='".$arrValues[$iTeller]."'";
                if ($iTeller < count($arrVariables)-1)
                         $sSQL = $sSQL.", ";
        }
            
//         echo $sSQL;
        
        return $objDB->query($sSQL);
            
    }
    
    /* =================================================================================================================
    *	function 	: changeRecord (mysqli version)
    *	programmer	: Dennis Renirie
    *	date 		: 18 jan 2004
    * 	input		: $arrVariables (array met fieldnames van mysql), $arrValues (array met waardes van de fields), $sTable (tabel in mysql), $pkfield (primary key field in mysql [meestal is dit id of code], $pkvalue (waarde van de primary key)
    *	@return	bool
    * 	use for		: wijzigen van een record in mysql
    *	description	: 
    * =================================================================================================================*/
//    function changeDBRecord($arrVariables, $arrValues, $sTable, $pkfield, $pkvalue, $pk2field = '', $pk2value)
//    {
//        global $objDB;
//
//        $sSQL = "UPDATE $sTable SET ";		
//        for ($iTeller = 0; $iTeller < count($arrVariables); $iTeller++)
//        {
//                $arrValues[$iTeller] = str_replace("'", "`", $arrValues[$iTeller]);
//                $sSQL = $sSQL.$arrVariables[$iTeller]."='".$arrValues[$iTeller]."'";
//                if ($iTeller < count($arrVariables)-1)
//                         $sSQL = $sSQL.", ";
//        }
//        $sSQL = $sSQL." WHERE $pkfield = '$pkvalue'";
//        if ($pk2field != '')
//            $sSQL = $sSQL." AND $pk2field = '$pk2value'";    
//         //echo $sSQL;
//        
//        return $objDB->query($sSQL);
//    }    
    
    /**
     * get an 2d array with results from a mysqli query
     * 
     * @global type $objDB
     * @param type $sSQL
     * @return type
     */
    function mysqliToArray($sSQL)
    {
        global $objDB;
        
        $objResult = $objDB->query($sSQL); 
        $arrResult = array();
        if ($objResult)
        {
            if (!is_bool($objResult))
            {
                while ($arrCurrRecord = $objResult->fetch_assoc())
                {
                    $arrResult[] = $arrCurrRecord;
                }
            }
        }
    
        return $arrResult;
    }
    
    function redirect301($sURL)
    {
        header( "HTTP/1.1 301 Moved Permanently" ); 
        header("Location: $sURL");         
    }
            
        
/**
 * function does a header redirect to the secure HTTPS version of the page when 
 * unsecure HTTP protocol is detected
 * 
 * @param bool $bMovedPermanently sends a 301 header when true
 */
//function redirectToHTTPS($bMovedPermanently = true)
//{
//    $sURL = getURLThisScript();    
//    $sURLHTTPS = str_replace('http://', 'https://', $sURL);
//
//    //if changed by replace then do header redirect
//    if ($sURL != $sURLHTTPS)
//    {
//        if ($bMovedPermanently)
//            header( "HTTP/1.1 301 Moved Permanently" ); 
//        header("Location: $sURLHTTPS"); 
//        
//        return true;
//    }
//    return false;
//}
/*
 * 4 jun 2017
 * 
 * prevent that 2 versions of a website exists, one with www in the url and one without
 * 
 * voorkomt http://mijnsite.nl is hetzelfde als http://www.mijnsite.nl
 */
function redirectToWWW($bMovedPermanently = true, $sSubdomain = 'www')
{
    $sURL = getURLThisScript();   
    $sNewURL = $sURL;
    
    if (stripos($sURL, $sSubdomain.'.') == false) 
    {
        $sNewURL = str_replace('http://', 'http://'.$sSubdomain.'.', $sURL);       
        $sNewURL = str_replace('https://', 'https://'.$sSubdomain.'.', $sNewURL);
    }
    //if changed by replace then do header redirect
    if ($sURL != $sNewURL)
    {
        if ($bMovedPermanently)
            header( "HTTP/1.1 301 Moved Permanently" ); 
        header('Location: '.$sNewURL);   
        
        return true;
    }
    
    return false;
}

/**
visitor using a mobile device? 
*/
//function isMobile()
//{
//	return (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]) === 1);
//}

 
        
/**
 * determine is the user agent is a spider, fetcher, bot or crawler of some kind
 * by analysing the httpuseragent
 * 
 * @param string $sHTTPUserAgent
 */
//function isSearchEngine($sHTTPUserAgent = '')
//{
//    //replacing default
//    if ($sHTTPUserAgent == '')
//        $sHTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];
//    
//    $arrCrawlers = array();
//    $arrCrawlers[] = 'crawler'; //general word
//    $arrCrawlers[] = 'spider'; //general word
//    $arrCrawlers[] = 'fetch'; //general word
//    $arrCrawlers[] = 'googlebot';
//    $arrCrawlers[] = 'AhrefsBot'; // http://ahrefs.com/robot/
//    $arrCrawlers[] = 'Vagabondo'; // http://webagent.wise-guys.nl/
//    $arrCrawlers[] = 'bingbot'; 
//    $arrCrawlers[] = 'acoonbot'; 
//    $arrCrawlers[] = 'seznambot'; 
//    $arrCrawlers[] = 'msnbot'; 
//    $arrCrawlers[] = 'yandexbot';     
//    $arrCrawlers[] = 'wotbox';     
//    $arrCrawlers[] = 'exabot';     
//    $arrCrawlers[] = 'LinkWalker';     
//    $arrCrawlers[] = 'UnwindFetchor';     ///http://www.gnip.com/
//    $arrCrawlers[] = 'TweetmemeBot';     
//    $arrCrawlers[] = 'Twitterbot';    
//    $arrCrawlers[] = 'panscient.com';    
//    $arrCrawlers[] = 'TurnitinBot';    
//    $arrCrawlers[] = 'Summify';     
//    $arrCrawlers[] = 'discobot';     //discovery channel bot
//    $arrCrawlers[] = 'HomeTags';    
//    $arrCrawlers[] = 'RexyoBot';    
//    $arrCrawlers[] = 'Yeti';    
//    $arrCrawlers[] = 'findlinks';    
//    $arrCrawlers[] = 'Snapbot';     
//    $arrCrawlers[] = 'PagesInventory';    
//    $arrCrawlers[] = 'DoCoMo';    //google iets
//    
//    //search the array
//    foreach($arrCrawlers as $sCrawler)
//    {
//        if (stripos($sHTTPUserAgent, $sCrawler))
//        {   
//            return true;          
//        }
//    }
//    
//    return false;
//}


/**
 * determine if the user agent is somewhat reliable
 * 
 * Let op
 * 
 * @param string $sHTTPUserAgent
 */
//function isValidUserAgent($sHTTPUserAgent = '')
//{
//    //replacing default
//    if ($sHTTPUserAgent == '')
//        $sHTTPUserAgent = $_SERVER['HTTP_USER_AGENT'];
//    
//       
//    //====LOOSELY analyse HTTP_USER_AGENT
//    $arrProhibitedLoose[] = 'java';     // java is often used by spammers and people with no neat intentions (otherwise they would set the user agent properly)
//    $arrProhibitedLoose[] = 'Kimengi';     // lijkt dubieus
//    $arrProhibitedLoose[] = 'nineconnections.com';     // lijkt dubieus
//    $arrProhibitedLoose[] = 'Jakarta';     // Don���t ask. Just block
//    $arrProhibitedLoose[] = 'panscient.com';     //door verschillende sites aangegevens als gevaarlijk
//    $arrProhibitedLoose[] = 'MorMor';     // lijkt dubieus
//    $arrProhibitedLoose[] = 'ichiro';     // lijkt me dubieus
//    
//    //search the array
//    foreach($arrProhibitedLoose as $sProhibited)
//    {
//        if (stripos($sHTTPUserAgent, $sProhibited)) //case insensitive compare
//            return false;          
//    }
//    
//
//    //====LITTERALY analyse HTTP_USER_AGENT
//    $arrProhibitedStrict[] = '';     /// empty user agent: no good
//    $arrProhibitedStrict[] = 'Android';     ///with only 'Android' as user agent is suspicious
//    $arrProhibitedStrict[] = 'Mozilla';     //Only the string Mozilla of course
//    $arrProhibitedStrict[] = 'User-Agent';     //with only 'mozilla' as user agent is suspicious
//    $arrProhibitedStrict[] = 'compatible ;';     //
//        
//    //search the array
//    foreach($arrProhibitedStrict as $sProhibited)
//    {
//        if (strcasecmp($sHTTPUserAgent, $sProhibited) == 0) //case insensitive compare
//            return false;          
//    }
//    
//    
//    
//    return true;
//}

/**
 * returns subdomain
 * (it filters subdomain out of the hostname)
 * 
 * @param $sHostname string if empty $_SERVER["HTTP_HOST"] is assumed
 * @return string 
 */
//function getSubdomain($sHostname = '')
//{
//    if ($sHostname == '')
//        $sHostname = $_SERVER["HTTP_HOST"];
//                
//    $sWithoutHttp = str_replace('http://www.', '', $sHostname);
//    $sWithoutHttp = str_replace('https://www.', '', $sWithoutHttp);
//    
//    $arrParts = explode('.', $sWithoutHttp);
//    $iCountParts = count($arrParts);
//    
//    if ($iCountParts == 2) //geen subdomein
//            return '';
//
//    if ($iCountParts >= 3) //geen subdomein
//    {   
//        if ($arrParts[0] == 'www')
//            return '';
//        else
//            return $arrParts[0];
//    }
//    
//    return '';
//}


/**
 *  de resultset van MySQL in een associatieve verkrijgen
 * returns false when something went wrong
 * 13 juni 2012
 * 
 *
 * @return array
 */   
function MySQLToArray($sSQLQuery, $bShowError = true, $bShowMySQLQueryOnError = false, $bAllwaysShowSQLQuery = false)
{
    $iMySQLResult = mysql_query($sSQLQuery);
    
    if ($bAllwaysShowSQLQuery)
      echo "<br>query: $sSQLQuery<br>";
    
    if (!$iMySQLResult)
    {
        if ($bShowError)
        {
              echo '<br><b>'.mysql_error().'</b>';

              if ($bShowMySQLQueryOnError)
                  echo ' in query : '.$sSQLQuery;

              echo '<br>';
        }

        return false;
    }
    else
    {
        $arrResult = false;
        
        while ($arrRow = @ mysql_fetch_array($iMySQLResult)) 
        {
            $arrResult[] = $arrRow;
        }
      
        return $arrResult;
    }
    

}


    
    
    
/**
 *  in browsers is het heden-ten-dage mogelijk om de do-not-track optie
 * in te stellen. Deze functie kijkt of deze optie ingeschakeld is
 * 3 juni 2012
 *
 * @return bool
 */
function doNotTrack()
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
    * saves a single string to a file
     * 25 mei 2012
    *
    * @param string $sStringToWriteToFile
    * @param string $sFileName
    * @return bool true if ok
    */
//    function saveToFileString($sStringToWriteToFile, $sFileName)
//    {
//        try
//        {
//            $fp = fopen($sFileName, 'w');
//
//            fwrite($fp,utf8_encode($sStringToWriteToFile));
//
//            return fclose($fp);
//
//        }
//        catch (Exception $objException)
//        {
//            error($objException);
//            return false;
//        }
//    }

    /**
    * load a string from a file
     * 25 mei 2012
    *
    * @param string $sStringToWriteToFile
    * @return string from the file
    */
//    function loadFromFileString($sStringToWriteToFile)
//    {
//
//        try
//        {
//            return file_get_contents($sStringToWriteToFile);
//
//        }
//        catch (Exception $objException)
//        {
//            error($objException);
//            return false;
//        }
//    }    
    
    
	/* =================================================================================================================
	*	function 	: getSearchKeywordsArray
	*	programmer	:
	*	date 		: 24 mei 2012
	* 	input		: $arrSearchKeywords (2d array opgebouwd als volgt: $arrSearchKeywords[] = array('zoekwoord', 10);
					  $iPriority - de prioriteit waar naar gezocht moet worden
					  $iNumberOfResults - hoeveel resultaten wil je hebben ?
	*	output		: 2d array met zoekwoorden
	* 	use for		: het verkrijgen van (zoek)woorden aan de hand van een bepaalde prioriteit
	*	description	: 
	* =================================================================================================================*/
	function getSearchKeywordsArray($arrSearchKeywords, $iPriority = 10, $iNumberOfResults = 10)
	{			
		//eerst filteren op prio 
		$arrWithRightPrio = array();
		foreach($arrSearchKeywords as $arrItem)
		{
			if ($arrItem[1] == $iPriority)
				$arrWithRightPrio[] = $arrItem;
		}
		
		//loop is alleen nodig als er MINDER resultaten gewild zijn	dan de array met prio's groot is)
		if ($iNumberOfResults < count($arrWithRightPrio))
		{ 
			$arrResult = array();
			
			//loopen
			for ($iCounter = 0; $iCounter < $iNumberOfResults; $iCounter++)
			{
				//keuze maken en item in in array weggooien
				shuffle($arrWithRightPrio);
				$arrResult[] = array_pop($arrWithRightPrio); 
			}			
		}
		else
		{
			shuffle($arrWithRightPrio);
			$arrResult = $arrWithRightPrio;
		}			
		
		return $arrResult;
	}


	/* =================================================================================================================
	*	function 	: endswith
	*	programmer	:
	*	date 		: 9 mei 2010
	* 	input		: 
	*	output		: true als string eindigt met $needle
	* 	use for		: het verkrijgen van de hoogte van een afbeelding
	*	description	: 
	* =================================================================================================================*/
//    function endswith($haystack, $needle) {
//    // @param mixed haystack: Object to look in
//    // @param string needle: String to look for
//    // @return: if found, true, otherwise false
//    
//        if (is_array($haystack)) {
//            foreach($haystack as $hay) {
//                if(substr($hay, strlen($hay)-strlen($needle), strlen($hay)) == $needle) {
//                    return true;
//                }
//            }
//            //return in_array($needle, $haystack);
//            return false;
//        }
//        else {
//            return (substr($haystack, strlen($haystack)-strlen($needle), strlen($haystack)) == $needle);
//        }
//    }

	/* =================================================================================================================
	*	function 	: startswith
	*	programmer	:
	*	date 		: 9 mei 2010
	* 	input		: 
	*	output		: true als string begint met $needle
	* 	use for		: het verkrijgen van de hoogte van een afbeelding
	*	description	: 
	* =================================================================================================================*/
//    function startswith($haystack, $needle) {
//    // @param mixed haystack: Object to look in
//    // @param string needle: String to look for
//    // @return: if found, true, otherwise false
//    
//        if (is_array($haystack)) {
//            foreach($haystack as $hay) {
//                if(substr($hay, 0, strlen($needle)) == $needle) {
//                    return true;
//                }
//            }
//            //return in_array($needle, $haystack);
//            return false;
//        }
//        else {
//            return (substr($haystack, 0, strlen($needle)) == $needle);
//        }
//    }




	/* =================================================================================================================
	*	function 	: getHeightJPG
	*	programmer	: dennis renirie
	*	date 		: 22 augustus 2008
	* 	input		: 
	*	output		: hoogte van afbeelding
	* 	use for		: het verkrijgen van de hoogte van een afbeelding
	*	description	: 
	* =================================================================================================================*/
//	function getHeightJPG($sImagePath)
//	{	
//		$iResult = 0;
//		
//		$src_img = ImageCreateFromJpeg($sImagePath); 
//		$iResult = ImageSY($src_img); 	
//		ImageDestroy($src_img); 
//		
//		return $iResult; 
//	}


	/* =================================================================================================================
	*	function 	: getWidthJPG
	*	programmer	: dennis renirie
	*	date 		: 22 augustus 2008
	* 	input		: 
	*	output		: hoogte van afbeelding
	* 	use for		: het verkrijgen van de hoogte van een afbeelding
	*	description	: 
	* =================================================================================================================*/
//	function getWidthJPG($sImagePath)
//	{	
//		$iResult = 0;
//		
//		$src_img = ImageCreateFromJpeg($sImagePath); 
//		$iResult = ImageSX($src_img); 		
//		ImageDestroy($src_img); 
//		
//		return $iResult; 
//	}


	/* =================================================================================================================
	*	function 	: hex2bin
	*	programmer	: Dennis Renirie
	*	date 		: 4 juni 08
	* 	input		: hexadecimale string
	*	output		: binaire string
	* 	use for		: in php is er geen functie die dit doet
	*	description	: tegenovergestelde van de functie bin2hex
	* =================================================================================================================*/
/*
        function hex2bin($sHexString)
	{
		return pack("H*", $sHexString);
	}
*/

	/* =================================================================================================================
	*	function 	: generatePrettyURLSafeURL
	*	programmer	: Dennis Renirie
	*	date 		: 3 jan 2007 (19 sept 2016 aangepast)
	* 	input		: 	$sUrl (string) : string waarvan je een url wilt maken
	*	output		: gegenereerde string die gebruikt kan worden als url
	* 	use for		: het genereren van een url safe string. deze kan gebruikt worden voor pretty urls. Bijvoorbeeld www.mysite.com/nieuws/432/dit-is-zon-string-die-we-met-deze-functie-converteren.html
	*	description	: 
	* =================================================================================================================*/
//	function generatePrettyURLSafeURL($sUrl)
//	{
//		trim($sUrl);
//		$sUrl = strtr($sUrl, ' ', '-');
//		$sUrl = strtolower($sUrl);
//
//		$sResult = preg_replace( '/[^ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789\.\-]/', '', $sUrl );  /* filter for unknown chars */
//
//		
//		if ($sResult == '')
//			$sResult = md5("");
//		
//		return $sResult;
//	}


	/* =================================================================================================================
	*	function 	: generateImageValidation2
	*	programmer	: some dude @ totaalnet
	*	date 		: 31 mei 2007
	* 	input		: 	$iWidth (integer) : breedte van het plaatje
						$iHeight (integer) : hoogte van het plaatje
	*	output		: gegenereerde string in uppercase (die moet worden opgeslagen in de session om in het formulier te kunnen valideren)
	* 	use for		: het genereren van een image die gebruikt kan worden voor validatie. dit ivm spambots voor bijvoorbeeld mailformulieren
	*	description	: 
	* =================================================================================================================*/
	function generateImageValidation2($iWidth=90, $iHeight=18)
	{
		$iLengte = 6;
		$sTekens = '23456789ABCDEFGHKLMNPRSTUVWXYZ';
		$sResultSecurityCode   = ''; 
		
		for ($i=0; $i<$iLengte; $i++) 
			$sResultSecurityCode .= $sTekens [rand (0, strlen ($sTekens) - 1)]; 
					
		$objImage = imagecreatetruecolor($iWidth, $iHeight);
		$iBackground  = imagecolorallocate($objImage, 192, 198, 195); 
		$black = imagecolorallocate($objImage, 250, 250, 250); 
		$red   = imagecolorallocate($objImage, 225, 225, 225); 
		
		imagefill($objImage, 0, 0, $iBackground);
		imagerectangle($objImage, 0, 0, 89, 49, $black); 
		
		for ($i = 0; $i <= 50; $i+=5) 
			imageline($objImage, mt_rand($i,$i+40), 0, mt_rand($i,$i+40), 50, $black); 
		
		for ($i = 0; $i <= 90; $i+=5) 
			imageline($objImage, 0, mt_rand($i,$i+30), 90, mt_rand($i,$i+30), $red); 
		
		$textColor = imagecolorallocate($objImage,0,0,0); $font = 5; imagestring($objImage,$font,5,3,$sResultSecurityCode,$textColor);

		header('Content-type: image/jpeg');
		imagejpeg($objImage);
		imagedestroy($objImage);		
		
		//returning security code
		return strtoupper($sResultSecurityCode);
	}

	/* =================================================================================================================
	*	function 	: generateImageValidation
	*	programmer	: Dennis Renirie
	*	date 		: 29 okt 2006
	* 	input		: 	$sFontPath (string) : font van de tekst om te genereren (bijvoorbeeld "verdana.ttf")
						$iWidth (integer) : breedte van het plaatje
						$iHeight (integer) : hoogte van het plaatje
	*	output		: gegenereerde string in uppercase (die moet worden opgeslagen in de session om in het formulier te kunnen valideren)
	* 	use for		: het genereren van een image die gebruikt kan worden voor validatie. dit ivm spambots voor bijvoorbeeld mailformulieren
	*	description	: 
	* =================================================================================================================*/
	function generateImageValidation($sFontPath, $iWidth=150, $iHeight=50)
	{
		if (!function_exists("generateRandomGDColor"))
		{
			function generateRandomGDColor($objImg, $iMin, $iMax)
			{
				return imagecolorallocate($objImg, mt_rand($iMin, $iMax), mt_rand($iMin, $iMax), mt_rand($iMin, $iMax));
			}
		}
				
		$sFont = $sFontPath;
		$iFontSize = 15;
		$sResultSecurityCode = null;
		
		
		//create en set background
		$objImg = imagecreate($iWidth, $iHeight);
		generateRandomGDColor($objImg, 250, 254);
	
	
		//draw ellipses for confusing OCR software
	
		/* vanwege een vage reden lopen de letters buiten het plaatje met de onderstaande code
		$iAantalEllipses = mt_rand(1, 4);
	
		for ($iTeller = 0; $iTeller < $iAantalEllipses-1; $iTeller++)
		{
			$clCurrColor = generateRandomGDColor($objImg, 220, 250);
			$iPositionX = mt_rand(10, $iWidth-10);
			$iPositionY = mt_rand(10, $iHeight-10);
			$iWidth = mt_rand(110, $iWidth-$iPositionX);
			$iHeight = mt_rand(110, $iHeight-$iPositionY);
			
			imageellipse($objImg, $iPositionX, $iPositionY, $iWidth, $iHeight, $clCurrColor);
			imageellipse($objImg, $iPositionX+1, $iPositionY+1, $iWidth+1, $iHeight+1, $clCurrColor);
		}
		*/
		$clCurrColor = generateRandomGDColor($objImg, 220, 250);
		imageellipse($objImg, 10, 10, 110, 30, $clCurrColor);
		imageellipse($objImg, 11, 11, 111, 31, $clCurrColor);
		
		$clCurrColor = generateRandomGDColor($objImg, 220, 250);
		imageellipse($objImg, 110, 10, 110, 40, $clCurrColor);
		imageellipse($objImg, 111, 11, 111, 41, $clCurrColor);
		
		$clCurrColor = generateRandomGDColor($objImg, 220, 250);
		imageellipse($objImg, 110, 10, 110, 40, $clCurrColor);
		imageellipse($objImg, 111, 11, 111, 41, $clCurrColor);
		
		$clCurrColor = generateRandomGDColor($objImg, 220, 250);
		imageellipse($objImg, 90, 45, 140, 50, $clCurrColor);
		imageellipse($objImg, 90, 46, 141, 51, $clCurrColor);	
		
	
	
		//generate security string
		$sPossibleChars = '23456789ABCDEFGHKLMNPRSTUVWXYZ';
		$iMaxLengthPossibleChars = strlen($sPossibleChars) - 1;
		$iAantalCharsToGenerate = mt_rand(4, 6);
		
		for($iTeller = 0; $iTeller < $iAantalCharsToGenerate; $iTeller++)
		{
			$clCurrColor = generateRandomGDColor($objImg, 50, 240);
			$iPositionX = $iTeller * $iFontSize + (mt_rand(0,$iFontSize/4));
			$iPositionY = mt_rand($iFontSize, $iHeight);
			$sChar = $sPossibleChars[mt_rand(0, $iMaxLengthPossibleChars)];
			$iRotation = ($iTeller % 2) ? -6 : 6;
			//imagettftext($objImg, $iFontSize, $iRotation, $iTeller * 26 + 20, 26, generateRandomGDColor($objImg 50, 240), $sFont, $sChar);
			imagettftext($objImg, $iFontSize, $iRotation, $iPositionX, $iPositionY, $clCurrColor, $sFont, $sChar);
			$sResultSecurityCode .= $sChar;
		}
	
		//output image
		header('Content-type: image/jpeg');
		imagejpeg($objImg);
		imagedestroy($objImg);
		
		//returning security code
		return strtoupper($sResultSecurityCode);
	}


	/*========================================================================================================================\
	* name				: downloadFile
	* programmer name 	: pechkin at zeos dot net
	* date 				: 16 mei 2006
	* input				: $sLocalPath : string - niet relatieve path op de server van het bestan
						  $sCustomFileName : string - bestandsnaam zoals deze aan de client kant opgeslagen moet worden
	* output			: output buffer : file
	* use for			: het downloaden van een file, die desgewenst op een andere locatie op de webserver staat
	* description		: Met deze functie kun je een bestand downloaden. Deze functie verzorgt de headers en de hele mikmak.
						  zelfs mogelijk om een download te resumen
						  LET OP zoals kunnen zien heeft firefox wat problemen met extensies
	\========================================================================================================================*/   
//	function downloadFile($sLocalPath, $sCustomFileName)
//	{
//		$fname = $sCustomFileName;
//		$fpath = $sLocalPath;
//		$fsize = filesize($fpath);
//		$bufsize = 20000;
//		
//		if(isset($_SERVER['HTTP_RANGE']))  //Partial download
//		{
//		   if(preg_match("/^bytes=(\\d+)-(\\d*)$/", $_SERVER['HTTP_RANGE'], $matches)) { //parsing Range header
//			   $from = $matches[1];
//			   $to = $matches[2];
//			   if(empty($to))
//			   {
//				   $to = $fsize - 1;  // -1  because end byte is included
//									   //(From HTTP protocol:
//		// 'The last-byte-pos value gives the byte-offset of the last byte in the range; that is, the byte positions specified are inclusive')
//			   }
//			   $content_size = $to - $from + 1;
//		
//			   header("HTTP/1.1 206 Partial Content");
//			   header("Content-Range: $from-$to/$fsize");
//			   header("Content-Length: $content_size");
//			   header("Content-Type: application/force-download");
//			   header("Content-Disposition: attachment; filename=$sCustomFileName");
//			   header("Content-Transfer-Encoding: binary");
//		
//			   if(file_exists($fpath) && $fh = fopen($fpath, "rb"))
//			   {
//				   fseek($fh, $from);
//				   $cur_pos = ftell($fh);
//				   while($cur_pos !== FALSE && ftell($fh) + $bufsize < $to+1)
//				   {
//					   $buffer = fread($fh, $bufsize);
//					   print $buffer;
//					   $cur_pos = ftell($fh);
//				   }
//		
//				   $buffer = fread($fh, $to+1 - $cur_pos);
//				   print $buffer;
//		
//				   fclose($fh);
//			   }
//			   else
//			   {
//				   header("HTTP/1.1 404 Not Found");
//				   exit;
//			   }
//		   }
//		   else
//		   {
//			   header("HTTP/1.1 500 Internal Server Error");
//			   exit;
//		   }
//		}
//		else // Usual download
//		{
//		   header("HTTP/1.1 200 OK");
//		   header("Content-Length: $fsize");
//		   header("Content-Type: application/force-download");
//		   header("Content-Disposition: attachment; filename=$sCustomFileName");
//		   header("Content-Transfer-Encoding: binary");
//		
//		   if(file_exists($fpath) && $fh = fopen($fpath, "rb")){
//			   while($buf = fread($fh, $bufsize))
//				   print $buf;
//			   fclose($fh);
//		   }
//		   else
//		   {
//			   header("HTTP/1.1 404 Not Found");
//		   }
//		}
//	}
	
	
	/*========================================================================================================================\
	* name				: arrayunique
	* programmer name 	: Dennis Renirie
	* date 				: 25 okt 2005
	* input				: arrData (enkelvoudige array met data)
	* output			: een unique array
	* use for			: alle waardes uit de array zijn uniek
	* description		: vervanging van array_unique van php, want deze delete alleen de dubbele waardes uit de array, maar de keys blijven hetzelfde
	\========================================================================================================================*/   
//	function arrayunique($arrData)
//	{
//		$arrUnique = null;
//		
//		for ($iTeller = 0; $iTeller < count($arrData); $iTeller++) //voor elk element in de array kijken
//		{
//			$sValue = $arrData[$iTeller];
//			$bKomtVoor = false;
//			for ($iTeller2 = 0; $iTeller2 < count($arrData); $iTeller2++)
//			{
//				if (($arrData[$iTeller2] == $sValue) && ($iTeller != $iTeller2))//als waarde voorkomt in de array (en het is niet zijn eigen waarde)
//					$bKomtVoor = true;					
//			}
//
//			if (!$bKomtVoor)
//				$arrUnique[] = $sValue;
//		}
//		
//		return $arrUnique;		
//	}


	/*========================================================================================================================\
	* name				: getURLThisScript
	* programmer name 	: Dennis Renirie
	* date 				: 14 okt 2005
	* input				: 
	* output			: url van het aanroepende script
	* use for			: het verkrijgen van de complete url (inclusief servernaam en parameters) van dit script
	* description		: 
	\========================================================================================================================*/   
//	function getURLThisScript()
//	{   
//            $sProtocol = 'http://';
//            
//            if(isset($_SERVER['HTTPS'])) 
//            {
//                if ($_SERVER['HTTPS'] == "on")
//                     $sProtocol = 'https://';
//            }
//
//            return $sProtocol.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 	
//	}


	/*========================================================================================================================\
	* name				: convertPlaintText2HTML
	* programmer name 	: Dennis Renirie
	* date 				: 12 okt 2005
	* input				: $sPlaintext
	* output			: string met html tekst
	* use for			: het converteren van een platte tekst naar html
	* description		: 
	\========================================================================================================================*/   
//	function convertPlaintText2HTML($sPlaintext)
//	{
//		$sResult = $sPlaintext;
//
//		$sResult = htmlentities($sResult);
//		$sResult = nl2br($sResult);
//
//
//		//zoeken naar links en deze vervolgens voorzien van <A href></A> tags
//		if (($sResult != "") && ($sResult != null))
//		{
//			$arrWoorden = explode(" ", $sResult);
//
//			for ($iTeller = 1; $iTeller < count($arrWoorden); $iTeller++)
//			{
//				if (substr($arrWoorden[$iTeller], 0, 7) == "http://")
//					$arrWoorden[$iTeller] = "<A href=\"$arrWoorden[$iTeller]\" target=\"_blank\">$arrWoorden[$iTeller]</A>";			
//			}
//			$sResult = implode(" ", $arrWoorden);
//		}
//								
//		return $sResult;
//	}



	/*========================================================================================================================\
	* name				: includeModules - voor POSWISE cms3 modules
	* programmer name 	: Dennis Renirie
	* date 				: 4 maart 2005
	* input				: $sModuleDirectory : directory waar de module uithangen --> directory inclusief / op het einde (dus bijvoorbeeld /var/html/crm/modules/)
	* output			: 2d array (modulenaam , nieuwe module) [nieuwe module = geen cms2 module]
	* use for			: includen van de modules. Wordt gebruikt
	* description		: 
	\========================================================================================================================*/   
	function includeModules($sModuleDirectory)
	{
		global $sCURRENTLANGUAGECODE;
		global $arrLang;
		 
		$objDH = opendir($sModuleDirectory);
		while(false !== ($sFile = readdir($objDH)))
		{
			if( strstr ($sFile, "_underconstruction_") == false && strstr ($sFile, "_UNDERCONSTRUCTION_") == false)  //je kunt in de directorynaam van de module _underconstruction_ zetten, dan wordt deze niet geinclude
			{
		
				if ( is_dir ( $sModuleDirectory.$sFile) && ($sFile != "." && $sFile != "..") )
				{
					if( is_file( correctPath ( $sModuleDirectory.$sFile."/config.php" ) ) ) 
						include_once ( correctPath ( $sModuleDirectory.$sFile."/config.php" ) );
					else
						err($arrLang['framework_message_filenotfound']." : ".correctPath($sModuleDirectory.$sFile."/config.php"));

					if( is_file( correctPath ( $sModuleDirectory.$sFile."/module.class.php" ) ) ) 
						include_once ( correctPath ( $sModuleDirectory.$sFile."/module.class.php" ) );
					else
						err($arrLang['framework_message_filenotfound']." : ".correctPath($sModuleDirectory.$sFile."/module.class.php"));

				} // einde is_dir
				
			} //einde "_underconstruction_"
		}//einde while
		closedir($objDH);
		
		unset($sCURRENTLANGUAGECODE);
		unset($arrLang);
	
		return $arrModules;
	}

	/* =================================================================================================================
	*	function 	: readlanguagefile
	*	programmer	: POSWISE
	*	date 		: 9 juni 2005
	* 	input		: $sPathLanguageFile (path van een languagefile)
	*	output		: globale variabelen van de variabelen welke in de language file gedefineerd zijn
	* 	use for		: het inlezen van taalbestanden
	*	description	: 
	* =================================================================================================================*/
	function readlanguagefile($sPathLanguageFile)
	{
		$bSuccess = true;
		
		if( file_exists($sPathLanguageFile)) // bestaat het bestand ?
		{
			$arrLines = file($sPathLanguageFile);//bestand inlezen 
			if ($arrLines != false) //inlezen goed gegaan ?
			{
				foreach ($arrLines as $sLine) // alle regels langslopen
				{
					$sTrimmedLine = trim($sLine);
					if (($sTrimmedLine != null) && ($sTrimmedLine != ""))
					{
						if (($sTrimmedLine[0] != "#")) //commentaar regels niet inlezen
						{
							//zoeken wat de variabele is en wat de waarde
							$arrExploded 	= explode("=", $sTrimmedLine);
							if ( count($arrExploded) > 0 )//checken of er uberhaupt wel iets terugkomt
							{
								$sVariable 		= strtolower(trim($arrExploded[0]));
								if ( count($arrExploded) > 1 ) //checken of er wel een vertaling is
									$sValue 		= trim($arrExploded[1]); 
								else
									$sValue = "";
			
								//als globale variabele setten
								global $$sVariable;
								$$sVariable = $sValue;
								unset ($$sVariable);
							}
							else
								$bSuccess = false;
						}
					}
				}
				

			}
			else
				$bSuccess = false;

		}
		else
		{
			echo "Cannot read language file because '$sPathLanguageFile' doesn't exist!";
			$bSuccess = false;
		}
		
		return $bSuccess;
	}


	/*========================================================================================================================\
	* name				: includeAll
	* programmer name 	: 
	* date 				: 28 juni 2005
	* input				: $dir: string - wich directory to include ? ;$ext - file extensie
	* output			: 
	* use for			: het includen van alle files in een directory met een bepaalde extensie
	* description		: 
	\========================================================================================================================*/   
//	function includeAll($dir, $ext = '.php')
//	{
//	  $dh = opendir($dir);
//	  //deb($dir);
//	  while(false !== ($file = readdir($dh)))
//		if (strpos($file, $ext) != false)
//		{
//		  include_once($dir.$file);
//		}
//	  closedir($dh);
//	}
//	
	/*========================================================================================================================\
	* name				: br2nl
	* programmer name 	: Dennis Renirie
	* date 				: 21 april 2005
	* input				: $sValue: string 
	* output			: 
	* use for			: het vervangen 
	* description		: omgekeerde functie van de php functie nl2br
	\========================================================================================================================*/   
//	function br2nl($sValue)
//	{
//	
//		return preg_replace( '!<br.*>!iU', "\n", $sValue );
//	/*
//		$sValue = str_replace("<BR>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		$sValue = str_replace("<br>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		$sValue = str_replace("<BR\>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		$sValue = str_replace("<br\>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		$sValue = str_replace("<BR \>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		$sValue = str_replace("<br \>\n","\n", $sValue); //afvangen dat er geen ' in staat, zodat javascript op z'n bek gaat.
//		
//		return $sValue;
//	*/
//	}


	/*========================================================================================================================\
	* name				: debug, deb
	* programmer name 	: Dennis Renirie, Albers
	* date 				: 4 maart 2005, 27 april 2005
	* input				: $sValue: string -wat wil je tonen
	* output			: 
	* use for			: het weergeven van bepaalde debug informatie. of ie debug info weergeeft, kun je in de config file instellen
	* description		:
	\========================================================================================================================*/   
	function debug($sValue = null)
	{
		global $bShowDebugInfo;
		
		 
		if ($bShowDebugInfo)
		{
			if ( ( $sValue == null ) && ( !is_numeric($sValue) ) )
				echo "<br />--><font color=\"#0000FF\">[NULL]</font><--";
			else
			{
				if (stristr ($sValue, 'SELECT') && stristr ($sValue, 'FROM')) //als het een SQL query is dan syntax coloring
				{
	
					$sValue = str_replace (";", ";<BR>", $sValue);
					$sValue = str_replace ("SELECT", "<BR><B>SELECT</B>", $sValue);
					$sValue = str_replace ("FROM", "<BR><B>FROM</B>", $sValue);
					$sValue = str_replace ("WHERE", "<BR><B>WHERE</B>", $sValue);
					$sValue = str_replace ("ORDER BY", "<BR><B>ORDER BY</B>", $sValue);
					$sValue = str_replace ("LIKE", "<BR><B>LIKE</B>", $sValue);
					$sValue = str_replace ("DELETE", "<BR><B>DELETE</B>", $sValue);
					$sValue = str_replace ("ALTER", "<BR><B>ALTER</B>", $sValue);
					$sValue = str_replace ("AND", "<BR><B>AND</B>", $sValue);
					$sValue = str_replace ("OR", "<BR><B>OR</B>", $sValue);
					$sValue = str_replace ("LIMIT", "<BR><B>LIMIT</B>", $sValue);
					$sValue = str_replace ("ASC", "<B>ASC</B>", $sValue);
					$sValue = str_replace ("DESC", "<B>DESC</B>", $sValue);
					$sValue = str_replace ("AS", "<B>AS</B>", $sValue);				
	
					$sValue = $sValue."<BR>";
				}
				
				echo "<br />--><font color=\"#0000FF\">".$sValue."</font><--";				
			}
		}
		
		unset($bShowDebugInfo);	
	}
	
	function deb($sValue = null) //alias for debug
	{
		debug($sValue);	
	}


	/*========================================================================================================================\
	* name				: makeArrayKeyGlobalVar
	* programmer name 	: Dennis Renirie
	* date 				: 2 april 2005
	* input				: arrArrayWithKeys : 1d associatieve array
	* output			: globale variabelen van de keys van de ingevoerde array
	* use for			: deze methode maakt van de keys van een associatieve array globale variabelen. Dit kan handig zijn met bijvoorbeeld databases
	* description		: 

		een voorbeeld om deze methode te gebruiken:
	
		$sSQL = "SELECT s_name FROM users";	
		$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
		$row = @ mysql_fetch_array($result) 
		makeArrayKeyGlobalVar($row);
		echo $s_name;
	
	\========================================================================================================================*/   
	function makeArrayKeyGlobalVar($arrArrayWithKeys)
	{
		if (is_array($arrArrayWithKeys))
		{
			foreach(array_keys($arrArrayWithKeys) as $sKey)
			{
				global $$sKey;
				$$sKey = $arrArrayWithKeys[$sKey];
				unset($$sKey);
				//echo("--->".$sKey."=".$$sKey);
			}	
		}	
	}

	
	/*========================================================================================================================\
	* name				: err, error, message, showMessage
	* programmer name 	: Dennis Renirie
	* date 				: 31 maart 2005
	* input				: $sModuleDirectory : directory waar de module uithangen --> directory inclusief / op het einde (dus bijvoorbeeld /var/html/crm/modules/)
	* output			: 
	* use for			: includen van de modules. Wordt gebruikt
	* description		: 
	\========================================================================================================================*/   
//	function error($sValue)
//	{
//		global $bShowFrameworkErrorMessages;
//		global $bIfFrameworkErrorTriggerPHPError;
//		
//		if ($bShowFrameworkErrorMessages)
//		{
//			if ($bIfFrameworkErrorTriggerPHPError)
//				trigger_error($sValue, E_USER_ERROR);
//			else
//				echo "<BR>--><FONT color=\"#FF0000\">".$sValue."</FONT><--";
//		}
//	
//		unset($bShowFrameworkErrorMessages);	
//		unset($bIfFrameworkErrorTriggerPHPError);		
//	}
	
	function err($sValue) //alias for error
	{
		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sValue);
	}
	
	function message($sValue) //alias for error
	{
		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sValue);
	}
	
	function showMessage($sValue) //alias for error
	{
		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sValue);
	}


	/*========================================================================================================================\
	* name				: generatePassword
	* programmer name 	: Dennis Renirie
	* date 				: 18 maart 2005
	* input				: $iLength : lengte van het wachtwoord
	* output			: random wachwoord
	* use for			: het genereren van een wachtwoord
	* description		: maximum lengte password = 2  20 karakters
	\========================================================================================================================*/   
//	function generatePassword($iLength = 8)
//	{
//		/*
//		$sRandomstring = md5(time());
//		$sRandomstring2 = "";
//		$sResult = "";
//		
//		for ($iTeller = 0; $iTeller < strlen($sRandomstring); $iTeller++)
//		{
//			$sRandomstring2 = $sRandomstring2.$sRandomstring[$iTeller].random(1,9);
//		}
//
//		
//		for ($iTeller = 0; $iTeller < $iLength; $iTeller++)
//		{
//			$sResult = $sResult.$sRandomstring2[$iTeller];
//		}
//		*/
//		
//		$sResult = null;
//		
//		for ($iTeller = 0; $iTeller < $iLength; $iTeller++)
//		{
//			$iCharChoice = random(0, 6);
//			
//			switch ($iCharChoice)
//			{
//				case 0 :  //kleine letters a t/m k (l vermijden)
//					$iChar = random(97, 107);
//       				break;
//				case 1 : //kleine letters m t/m n (o vermijden)
//					$iChar = random(109, 110);
//       				break;
//				case 2 : //kleine letters p t/m z 
//					$iChar = random(112, 122);
//       				break;
//				case 3 : //hoofdletters A t/m H (I vermijden)
//					$iChar = random(65, 72);
//					break;
//				case 4 :  //hoofdletters J t/m N (O vermijden)
//					$iChar = random(74, 78);
//       				break;
//				case 5 :  //hoofdletters P t/m Z
//					$iChar = random(80, 90);
//       				break;
//				case 6 :  //cijfers 2 t/m 9 (0 en 1 vermijden)
//					$iChar = random(50, 57);
//       				break;				
//			}
//
//			$sResult = $sResult.chr($iChar);
//		}
//		
//
//		return $sResult;
//	}


	/*========================================================================================================================\
	* name				: filterSQLInjection
	* programmer name 	: Dennis Renirie
	* date 				: 16 maart 2005 / 26 sept 08 / 4 jan 09 / 12 jun 2013
	* input				: $mInput  : string of array met strings (onbeperkt diep, dus array in array in array is mogelijk) 
						  waarin de waarde van een invulveld kan komen van een formulier
	* output			: schone string/array zonder injection code
	* use for			: het filteren van de waarde van een input veld om SQL injection te voorkomen
					      Je kunt in 1 keer een array invoeren (bijvoorbeeld de $_GET of $_POST array) om te filteren op foute input
	* description		: filteren op sql injection code
         * 
         *  LET OP: verschil met CMS 3 is dat deze functie een default heeft van array dimensionlevel=2 ivm backwards compatibiliteit
	\========================================================================================================================*/   
//        function filterSQLInjection($mInput, $iArrayDimensionLevel = 2)
//        {
//            if (!is_numeric($iArrayDimensionLevel))
//                $iArrayDimensionLevel = 0;
//
//
//            if (($iArrayDimensionLevel > 0) && (is_array($mInput)))
//            {
//                $iArrayDimensionLevel--;
//                foreach (array_keys($mInput) as $sKey)
//                    $mInput[$sKey] = filterSQLInjection($mInput[$sKey], $iArrayDimensionLevel);
//            } 
//            else 
//            {
//                $mInput = str_replace('\\', '/', $mInput); //  de backslash wordt vervangen door forward slash
//                $mInput = str_replace('"', '', $mInput);        
//                $mInput = str_replace("'", '', $mInput);
//                $mInput = str_replace(';', '.', $mInput);           
//                $mInput = str_replace('--', '', $mInput);
//                $mInput = str_replace('+', '', $mInput);
//                $mInput = str_replace('/*', '', $mInput);
//                $mInput = str_replace('*/', '', $mInput);
//                $mInput = str_replace('%', '', $mInput);
//                if ( (stripos($mInput, 'count(') != false) || (stripos($mInput, 'count (') != false) ) //count even uitsluiten
//                {
//                    $mInput = str_replace('(', '', $mInput);
//                    $mInput = str_replace(')', '', $mInput);
//                }
//                if ( (stripos($mInput, 'http://') != false) || (stripos($mInput, 'style:') != false) ) //dubbele punt uitzonderingen even uitsluiten
//                {
//                    $mInput = str_replace(': ', ':', $mInput); //met dubbele punt + variabale is placeholder in sql, die willen we ook niet in de string hebben
//                    $mInput = str_replace(':', ': ', $mInput); //om te voorkomen dat alle dubbele punten bij iedere filtercycle worden vervangen door dubbelepunt-spatie wordt eerst dubbelepunt-spatie vervangen door een dubbele punt
//                }
//
//
//                //their html equivalent
//                $mInput = str_replace(htmlentities('"'), '', $mInput);
//                $mInput = str_replace(htmlentities("'"), '', $mInput);
//                $mInput = str_replace(htmlentities(';'), '', $mInput);
//                $mInput = str_replace(htmlentities('--'), '', $mInput);
//                $mInput = str_replace(htmlentities('+'), '', $mInput);
//                $mInput = str_replace(htmlentities('/*'), '', $mInput);
//                $mInput = str_replace(htmlentities('*/'), '', $mInput);
//                $mInput = str_replace(htmlentities('%'), '', $mInput);
//                if ( (stripos($mInput, htmlentities('count(')) != false) || (stripos($mInput, htmlentities('count (')) != false) ) //count even uitsluiten
//                {        
//                    $mInput = str_replace(htmlentities('('), '', $mInput);
//                    $mInput = str_replace(htmlentities(')'), '', $mInput);
//                }
//                if ( (stripos($mInput, htmlentities('http://')) != false) || (stripos($mInput, htmlentities('style:')) != false) ) //dubbele punt uitzonderingen even uitsluiten
//                {
//                    $mInput = str_replace(htmlentities(': '), htmlentities(':'), $mInput); //met dubbele punt + variabale is placeholder in sql, die willen we ook niet in de string hebben
//                    $mInput = str_replace(htmlentities(':'), htmlentities(': '), $mInput); //om te voorkomen dat alle dubbele punten bij iedere filtercycle worden vervangen door dubbelepunt-spatie wordt eerst dubbelepunt-spatie vervangen door een dubbele punt
//                }
//
//
//                //their url-encoded equivalent
//                $mInput = str_replace(urlencode('"'), '', $mInput);
//                $mInput = str_replace(urlencode("'"), '', $mInput);
//                $mInput = str_replace(urlencode(';'), '', $mInput);
//                $mInput = str_replace(urlencode('--'), '', $mInput);
//                $mInput = str_replace(urlencode('+'), '', $mInput);
//                $mInput = str_replace(urlencode('/*'), '', $mInput);
//                $mInput = str_replace(urlencode('*/'), '', $mInput);
//                $mInput = str_replace(urlencode('%'), '', $mInput);
//                if ( (stripos($mInput, urlencode('count(')) != false) || (stripos($mInput, urlencode('count (')) != false) ) //count even uitsluiten
//                {        
//                    $mInput = str_replace(urlencode('('), '', $mInput);
//                    $mInput = str_replace(urlencode(')'), '', $mInput);
//                }
//                if ( (stripos($mInput, urlencode('http://')) != false) || (stripos($mInput, urlencode('style:')) != false) ) //dubbele punt uitzonderingen even uitsluiten
//                {
//                    $mInput = str_replace(urlencode(': '), urlencode(':'), $mInput); //met dubbele punt + variabale is placeholder in sql, die willen we ook niet in de string hebben
//                    $mInput = str_replace(urlencode(':'), urlencode(': '), $mInput); //om te voorkomen dat alle dubbele punten bij iedere filtercycle worden vervangen door dubbelepunt-spatie wordt eerst dubbelepunt-spatie vervangen door een dubbele punt
//                }
//
//
//                //their utf8 equivalent
//                //we gebruiken urt8_decode omdat deze pagina al utf8 gecodeerd is
//                $mInput = str_replace(utf8_decode('"'), '', $mInput); 
//                $mInput = str_replace(utf8_decode("'"), '', $mInput);
//                $mInput = str_replace(utf8_decode(';'), '', $mInput);
//                $mInput = str_replace(utf8_decode('--'), '', $mInput);
//                $mInput = str_replace(utf8_decode('+'), '', $mInput);
//                $mInput = str_replace(utf8_decode('/*'), '', $mInput);
//                $mInput = str_replace(utf8_decode('*/'), '', $mInput);
//                $mInput = str_replace(utf8_decode('%'), '', $mInput);
//                if ( (stripos($mInput, utf8_decode('count(')) != false) || (stripos($mInput, utf8_decode('count (')) != false) ) //count even uitsluiten
//                {        
//                    $mInput = str_replace(utf8_decode('('), '', $mInput);
//                    $mInput = str_replace(utf8_decode(')'), '', $mInput);
//                }
//                if ( (stripos($mInput, utf8_decode('http://')) != false) || (stripos($mInput, utf8_decode('style:')) != false) ) //dubbele punt uitzonderingen even uitsluiten
//                {
//                    $mInput = str_replace(utf8_decode(': '), utf8_decode(':'), $mInput); //met dubbele punt + variabale is placeholder in sql, die willen we ook niet in de string hebben
//                    $mInput = str_replace(utf8_decode(':'), utf8_decode(': '), $mInput); //om te voorkomen dat alle dubbele punten bij iedere filtercycle worden vervangen door dubbelepunt-spatie wordt eerst dubbelepunt-spatie vervangen door een dubbele punt
//                }
//
//
//                //the left-overs we dont want to allow
//                $mInput = str_ireplace('SUBSTRING', '', $mInput); //case insensitive replace
//                $mInput = str_ireplace('LOAD_FILE', '', $mInput); //case insensitive replace
//
//                //all string manipulation functions are a potential hazard. But we cannot not filter all of them because they can be valid input
//            }
//
//            return $mInput;
//        }

	/* =================================================================================================================
	*	function 	: extractFileFromPath
	*	programmer	: Dennis Renirie
	*	date 		: 22 feb 05
	* 	input		: $sString
	*	output		: de directory
	* 	use for		: het extraheren van een file uit een path. Bijvoorbeeld : je hebt /home/var/local/index.php en je wilt hebben : index.php (dus zonder /home/var/local/)
	*	description	: 
	* =================================================================================================================*/
//	function extractFileFromPath($sPath, $sDirectorySeparator = "/")
//	{
//		$sResult = $sPath;
//
//		$iTempPos = strrpos($sResult, $sDirectorySeparator); //pos opvragen van laatste /
//		$sResult = substr($sResult, $iTempPos+1, strlen($sResult));  //alles voor de laatste / pakken en als restult geven
//		
//		return $sResult;	
//	}	


	/* =================================================================================================================
	*	function 	: extractDirectoryFromPath
	*	programmer	: Dennis Renirie
	*	date 		: 22 feb 05
	* 	input		: $sString
	*	output		: de directory
	* 	use for		: het extraheren van een directory uit een path. Bijvoorbeeld : je hebt /home/var/local/index.php en je wilt hebben : /home/var/local/ (dus zonder index.php)
	*	description	: 
	* =================================================================================================================*/
//	function extractDirectoryFromPath($sPath)
//	{
//		$sTempDirZonderSlash = $sPath;
//		$iTempPos = strrpos($sTempDirZonderSlash, "/"); //pos opvragen van laatste /
//		$sResult = substr($sTempDirZonderSlash, 0, $iTempPos);  //alles voor de laatste / pakken en als restult geven
//		$sResult =$sResult."/";
//		
//		return $sResult;	
//	}


	/* =================================================================================================================
	*	function 	: filterSearchKeywords
	*	programmer	: Dennis Renirie
	*	date 		: 18 feb 05
	* 	input		: $sString
	*	output		: cleane tekst, die je kan gebruiken als zoekwoorden voor de website
	* 	use for		: filteren van een string zodat je deze als zoekwoorden kunt gebruiken voor je website 
	*	description	: 
	* =================================================================================================================*/
	function filterSearchKeywords($sString)
	{
		$sResult = $sString;

		$sResult = strip_tags($sResult);
		$sResult = str_replace("<", "", $sResult);
		$sResult = str_replace(">", "", $sResult);
		$sResult = str_replace("'", "", $sResult);
		$sResult = str_replace("\"", "", $sResult);
		
		return $sResult;	
	}


	/* =================================================================================================================
	*	function 	: checkEmail
	*	programmer	: Dennis Renirie
	*	date 		: 24 jan 05
	* 	input		: $emailadres
	*	output		: true of false (afhankelijk van de invoer)
	* 	use for		: kijkt naar een emailadres of dit geldig is ja of nee (LET OP WERKT NIET OP ALS PHP OP WINDOWS DRAAIT)
	*	description	: kijkt of layout emailadres goed is, zo ja dan checked deze of de server bestaat
	* =================================================================================================================*/
//	function checkEmail($emailadres)	
//	{
//		$bResult = false;
//		
//		if(ereg("^.+@.+\\..+$", $emailadres))
//		{
//			// take a given email address and split it into the username and domain. 
//			list($userName, $mailDomain) = split("@", $emailadres); 
//			if (checkdnsrr($mailDomain, "MX")) 
//			{ 
//				 // this is a valid email domain!
//				 $bResult = true; 
//			} 
//			else 
//			{ 
//				 // this email domain doesn't exist! bad dog! no biscuit! 
//				 $bResult = false;
//			} 
//		}
//		else
//		{
//			$bResult = false;
//		}
//
//
//		return $bResult; 
//	}


	/* =================================================================================================================
	*	function 	: getFileSize
	*	programmer	: Dennis Renirie
	*	date 		: 21 nov 2004
	* 	input		: $sFilePath (path van een file)
	*	output		: grootte van bestand in kilobytes.
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
//	function getFileSize($sFilePath)	
//	{
//		if (is_file($sFilePath))
//		{
//			$sizekb = (int)(filesize($sFilePath) / 1024);
//			
//			if ($sizekb == 0)
//			{
//				$size = "1 kb";
//			}
//			else
//			{
//				$size = "$sizekb kb";
//
//			}
//		}
//		else
//			$size = "0 kb";
//			
//		return($size);
//	}
	
	/* =================================================================================================================
	*	function 	: convertToInBTW
	*	programmer	: Dennis Renirie
	*	date 		: 26 okt 2004
	* 	input		: fBedragInBtw (float), fProcentBTW (float) 
	*	output		: float : resultaat van berekening naar in btw
	* 	use for		: bedrag omrekenen naar inclusief btw 
	*	description	: 
	* =================================================================================================================*/
	function convertToInBTW($fBedragExBtw, $fProcentBTW)	
	{
		$fBedragExBtw = str_replace(",", ".", $fBedragExBtw);
		$fProcentBTW = str_replace(",", ".", $fProcentBTW);

		$fReturn = (float)0.0;
		$fReturn = (float)(((float)$fBedragExBtw / 100.0) * (100.0 + (float)$fProcentBTW));
  		
		return($fReturn);	
	}



	/* =================================================================================================================
	*	function 	: convertToExBTW
	*	programmer	: Dennis Renirie
	*	date 		: 26 okt 2004
	* 	input		: fBedragInBtw (float), fProcentBTW (float)  
	*	output		: float : resultaat van berekening naar ex btw
	* 	use for		: bedrag omrekenen naar exclusief btw
	*	description	: 
	* =================================================================================================================*/
	function convertToExBTW($fBedragInBtw, $fProcentBTW)	
	{	 
		$fBedragInBtw = str_replace(",", ".", $fBedragInBtw);
		$fProcentBTW = str_replace(",", ".", $fProcentBTW);

		
		$fReturn = 0.0;
		$fReturn = (float)((float)$fBedragInBtw / (100.0 + (float)$fProcentBTW));
		$fReturn = (float)($fReturn * 100.0);
		 
		return($fReturn);	
	}


	/* =================================================================================================================
	*	function 	: replaceEnter
	*	programmer	: Dennis Renirie
	*	date 		: 26 okt 2004
	* 	input		: string : $sTekst
	*	output		: tekst met <BR> ipv ENTER
	* 	use for		: het weergeven van platte tekst in HTML
	*	description	: 
	* =================================================================================================================*/
	function replaceEnterBR($sTekst)	
	{
		$sReturn = $sTekst;
		$sReturn = str_replace("\n" , "<BR>\n", $sReturn);
		
		return($sReturn);	
	}


	/* =================================================================================================================
	*	function 	: convertToMoney
	*	programmer	: Dennis Renirie (POSWISE)
	*	date 		: 22 okt 2004
	* 	input		: $sValue (string presentatie van een floating point getal)
	*	output		: money
	* 	use for		: converteren van een float getal naar een getal met een komma en 2 cijfers achter de komma (met punt en komma wordt rekening gehouden)
	*	description	: 
	* =================================================================================================================*/
	function convertToMoney($sValue)
	{
		$sResult = "0,00";
		
		$sValue = str_replace(",", ".", $sValue); //ondervangen dat je een komma tikt ipv punt. nu hebben we zeker weten altijd een punt
		if (is_numeric($sValue))
		{
			$sValue = round($sValue, 2);
			$sValue = str_replace(",", ".", $sValue); //ondervangen dat je een komma tikt ipv punt. nu hebben we zeker weten altijd een punt

			if(strpos($sValue, ".") == false) // 59
			{
				$sValue	= $sValue.".00";
			}

			if(strpos($sValue, ".") == strlen($sValue)-1) // 59.
			{
				$sValue	= $sValue."00";
			}				
			
			if(strpos($sValue, ".") == strlen($sValue)-2) // 59.0
			{
				$sValue	= $sValue."0";
			}						
						
			$sResult = $sValue;
			
		}

		//uiteindelijk weer een komma ipv een punt
		$sResult = str_replace(".", ",", $sResult);
		
		return($sResult);
		
	}


	/* =================================================================================================================
	*	function 	: convertToFloat
	*	programmer	: Dennis Renirie
	*	date 		: 5 maart 05
	* 	input		: $sValue (string presentatie van een komma getal)
	*	output		: floating point getal
	* 	use for		: converteren van een getal met een komma (bijvoorbeeld currency) naar een floating point getal
	*	description	: 
	* =================================================================================================================*/
	function convertToFloat($sValue)
	{
		$sResult = "0.00";
		
		$sValue = str_replace(",", ".", $sValue); //ondervangen dat je een komma tikt ipv punt. nu hebben we zeker weten altijd een punt
		if (is_numeric($sValue))
		{
			$sResult = $sValue;
		}
		
		return($sResult);
	}

	/* =================================================================================================================
	*	function 	: uploadFileFunctionWithExtension
	*	programmer	: Dennis Renirie
	*	date 		: 05 okt 2004
	* 	input		: $sFieldName (veldnaam van form), $sNewPathFile (hoe heet het geuploade bestand ? (volledig path opgeven), $sExtension (string van toegestane bestandsextensie)
	*	output		: 
	* 	use for		: 
	*	description	: ALLEEN TE GEBRUIKEN DOOR DE FUNCTIE uploadFile(). DEZE 2 FUNCTIES HOREN BIJ ELKAAR!!!
	* =================================================================================================================*/
	function uploadFileFunctionWithExtension($sFieldName, $sExtension, $sNewPathFile)
	{
		$bSuccessSub = false;
		
		if (strtoupper(getExtension($sFieldName['name'])) == strtoupper($sExtension))
		{		
			if ( move_uploaded_file($sFieldName['tmp_name'], $sNewPathFile) )
			{
				echo "upload van '".$sFieldName['name']."' succesvol voltooid";
				$bSuccessSub = true;
			}
			else
			{
				echo  "upload van '".$sFieldName['name']."' MISLUKT !";
			}
		}
		
		return($bSuccessSub);
	}





	/* =================================================================================================================
	*	function 	: uploadFile
	*	programmer	: Dennis Renirie
	*	date 		: 30 sept 2004
	* 	input		: $sFieldName (veldnaam van form), $sNewPathFile (hoe heet het geuploade bestand ? (volledig path opgeven), $arrAllowedExtensions (array van toegestane bestandsextensies)
	*	output		: file op hard disk van webserver
	* 	use for		: uploaden van 1 file
	*	description	: LET OP : gebruikt functie : uploadFileFunctionWithExtension. DEZE 2 FUNCTIES HOREN BIJ ELKAAR!!!
	* =================================================================================================================*/
//	function uploadFile($sFieldName, $sNewPathFile, $arrAllowedExtensions)
//	{
//		global $maxuploadsize;	
//		
//		if (is_uploaded_file($sFieldName['tmp_name']))
//		{
//			//echo "uploaden";
//			
//			if ($sFieldName['size'] > $maxuploadsize)
//			{
//				echo "Het bestand neemt te veel ruimte in beslag";
//			}
//			
//			if (is_array($arrAllowedExtensions))
//			{
//				for ($iArrayTeller = 0; $iArrayTeller < count ($arrAllowedExtensions); $iArrayTeller++)
//				{
//					uploadFileFunctionWithExtension($sFieldName, $arrAllowedExtensions[$iArrayTeller], $sNewPathFile);
//				}
//			}
//			else
//			{
//				uploadFileFunctionWithExtension($sFieldName, $arrAllowedExtensions, $sNewPathFile);
//			}
//		}
//
//		unset($maxuploadsize);
//		
//	}




	/* =================================================================================================================
	*	function 	: removeLastChar
	*	programmer	: Dennis Renirie
	*	date 		: 2 augustus 2004
	* 	input		: $sString
	*	output		: $sString met 1 karakter minder aan het eind
	* 	use for		: het afhakken van het laatste teken van een string
	*	description	: keyword : stringRemoveLastChar
	* =================================================================================================================*/
//	function removeLastChar($sString)
//	{
//		$sResult = substr_replace($sString,"",-1);
//		return($sResult);
//	}


	/* =================================================================================================================
	*	function 	: getPreviousDirectory
	*	programmer	: Dennis Renirie
	*	date 		: 2 augustus 2004
	* 	input		: $sPath zonder / aan het einde
	*	output		: Vorige directory van de opgegeven string
	* 	use for		: file explorers. Als je een directory hebt, en de wilt de vorige directory hebben van  $sPath
	*	description	: Als je een path hebt, dan vorige directory teruggeven
	*					VB : als $sPath /var/www/project1/test , dan uitkomst van deze functie /var/www/project1
	* =================================================================================================================*/
	function getPreviousDirectory($sPath)
	{
		$sTempDirZonderSlash = $sPath;
//		echo "*$sTempDirZonderSlash*".$sPath."*";
		$iTempPos = strrpos($sTempDirZonderSlash, "/"); //pos opvragen van laatste /
		$sResult = substr($sTempDirZonderSlash, 0, $iTempPos);  //alles voor de laatste / pakken en als restult geven
		
		return($sResult);
	}


	/* =================================================================================================================
	*	function 	: random
	*	programmer	: Dennis Renirie
	*	date 		: 10 mei 2004
	* 	input		: $iMin (minimum) ; $iMax (maximum)
	*	output		: 
	* 	use for		: random, ook als de range bijv 1..1 is (de functie rand() ondersteund dit niet); voor verdere werking zie standaard functie rand() in PHP
	*	description	: 
	* =================================================================================================================*/
//	function random($iMin, $iMax)
//	{
//		if ($iMin == $iMax)
//		{
//			$iResult = $iMin;
//		}
//
//		if ($iMin > $iMax) //als min en max omgedraaid zijn, dan terugdraaien
//		{
//			$iTemp = $iMin;
//			$iMin = $iMax;
//			$iMax = $iTemp;	
//		}
//
//		if ($iMin < $iMax)
//		{
//			$iResult = rand($iMin, $iMax);
//		}
//				
//		return($iResult);
//	}


	/* =================================================================================================================
	*	function 	: showPhotobookPhoto
	*	programmer	: Dennis Renirie
	*	date 		: 19 april 2004
	* 	input		: 
	*	output		: 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function showPhotobookPhoto($sThumbnaildir, $file, $id)
	{
		global $iOctalRechtenOpFiles;
		global $local_sitemanagerfotoboek;
		global $www_sitemanagerfotoboek;
		
		if (file_exists($sThumbnaildir) == false)
			if(mkdir($sThumbnaildir, $iOctalRechtenOpFiles) == false)
				echo "kan thumbnail directory niet maken. Heeft u wel voldoende rechten ?";

 		$path = $sThumbnaildir.$file;
		if ($file != "." && $file != "..")
		{	
			$sExtension = strtoupper(getExtension($sThumbnaildir.$file));
			$bApproved = (($sExtension == "JPG") || ($sExtension == "JPEG")); //alleen jpg en jpeg files laten zien
			if (is_file($sThumbnaildir.$file) && ($bApproved))
			{
				if (is_file("$local_sitemanagerfotoboek$id/$file"))
					echo "<A href=\"javascript:popUpScrollNo('fotogalerij-popup.php?id=$id&file=$file')\"><IMG src=\"$www_sitemanagerfotoboek$id/thumbnails/$file"."\" alt=\"".getFileNameWithoutExtension($file)."\" border=\"0\"></a>";
			}
		}
		
		unset ($www_sitemanagerfotoboek);
		unset ($iOctalRechtenOpFiles);
		unset ($local_sitemanagerfotoboek);
	}


	/* =================================================================================================================
	*	function 	: chopText
	*	programmer	: Dennis Renirie
	*	date 		: 16 april 2004
	* 	input		: 
	*	output		: 
	* 	use for		: afkappen van een tekst na iMaxCharacters. Hij zoekt echter naar spaties, om de tekst af te kappen
	*	description	: iSpatieCharacters : hoeveel karakters voor het einde van de string moet ie gaan zoeken naar spaties
	* =================================================================================================================*/
//	function chopText($sString, $iMaxCharacters, $iSpatieCharacters)
//	{ 
//		$bChanged = false;
//		$sResult = $sString;
//		
//		$iStartPos = $iMaxCharacters - $iSpatieCharacters;
//		
//		
//		//zoeken naar spaties binnen een bepaald bereik
//		while (($iStartPos > 0) && ($iStartPos <= strlen($sResult)))
//		{
//			$iStartPos++;
//			if ((substr($sString, $iStartPos, 1)) == " " || (substr($sString, $iStartPos, 1) == "-") || (substr($sString, $iStartPos, 1) == "_"))
//			{
//				$sResult = substr($sResult, 0, $iStartPos);
//				$bChanged = true;
//			}
//		}
//		
//		//als niets veranderd, dan toch afkappen
//		if ($bChanged == false) 
//			$sResult = substr($sResult, 0, $iMaxCharacters);
//	
//		//puntjes zetten als afgekapt		
//		if (strlen($sResult) < strlen($sString)) 
//			$sResult = $sResult." ...";
//
//		return($sResult); 
//	}
	

	/* =================================================================================================================
	*	function 	: getSiteContentText
	*	programmer	: Dennis Renirie
	*	date 		: 15 april 2004
	* 	input		: $sContentNaam (s_naam uit de tabel $tblSitecontent)
	*	output		: 
	* 	use for		: het krijgen van de tekst (niet de titel) van een item uit de tabel $tblSitecontent
	*	description	: 
	* =================================================================================================================*/
	function getSiteContentText($sContentNaam)
	{ 
		global $tblSitecontent;
		
		$sSQL = "SELECT * FROM $tblSitecontent WHERE s_naam = '$sContentNaam'";
		$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sTekst				= $row["s_tekst"];
		}
		
		unset($tblSitecontent);
		
		return($sTekst); 
	}

	/* =================================================================================================================
	*	function 	: getSiteContentTitle
	*	programmer	: Dennis Renirie
	*	date 		: 11 mei 2004
	* 	input		: $sContentNaam (s_naam uit de tabel $tblSitecontent)
	*	output		: 
	* 	use for		: het krijgen van de titel (niet de tekst) van een item uit de tabel $tblSitecontent
	*	description	: 
	* =================================================================================================================*/
	function getSiteContentTitle($sContentNaam)
	{ 
		global $tblSitecontent;
		
		$sSQL = "SELECT * FROM $tblSitecontent WHERE s_naam = '$sContentNaam'";
		$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sTekst				= $row["s_titel"];
		}
		
		unset($tblSitecontent);
		
		return($sTekst); 
	}
	
	
	/* =================================================================================================================
	*	function 	: getSiteContentImageSmall
	*	programmer	: Dennis Renirie
	*	date 		: 10 mei 2004
	* 	input		: $sContentNaam (s_naam uit de tabel $tblSitecontent)
	*	output		: 
	* 	use for		: het krijgen van het kleine plaatje van een item uit de tabel $tblSitecontent
	*	description	: 
	* =================================================================================================================*/
	function getSiteContentImageSmall($sContentNaam)
	{ 
		global $tblSitecontent;
		
		$sSQL = "SELECT * FROM $tblSitecontent WHERE s_naam = '$sContentNaam'";
		$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sImage				= $row["s_plaatjeurlklein"];
		}
		
		unset($tblSitecontent);
		
		return($sImage); 
	}
	
	/* =================================================================================================================
	*	function 	: getSiteContentImageLarge
	*	programmer	: Dennis Renirie
	*	date 		: 10 mei 2004
	* 	input		: $sContentNaam (s_naam uit de tabel $tblSitecontent)
	*	output		: 
	* 	use for		: het krijgen van het grote plaatje van een item uit de tabel $tblSitecontent
	*	description	: 
	* =================================================================================================================*/
	function getSiteContentImageLarge($sContentNaam)
	{ 
		global $tblSitecontent;
		
		$sSQL = "SELECT * FROM $tblSitecontent WHERE s_naam = '$sContentNaam'";
		$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sImage				= $row["s_plaatjeurl"];
		}
		
		unset($tblSitecontent);
		
		return($sImage); 
	}		

	/* =================================================================================================================
	*	function 	: showShadowTable
	*	programmer	: Dennis Renirie
	*	date 		: 15 april 2004
	* 	input		: $iWidth, $sImgSrcPathHead, $sImgSrcPathBodyBorders, $iLeftMargin, $sTextInTable, $iRightMargin, $sImgSrcPathFooter
	*	output		: [html tekst schaduwtabel]
	* 	use for		: het maken van een html tabel met schaduw (opzet : hoofd, body, footer)
	*	description	: 
	* =================================================================================================================*/
	function showShadowTable($iWidth, $sImgSrcPathHead, $sImgSrcPathBodyBorders, $iLeftMargin, $sTextInTable, $iRightMargin, $sImgSrcPathFooter) 
	{ 
	?>
		<table border="0" cellpadding="0" cellspacing="0" width="<?php echo $iWidth ?>">
		<tr>
			<td height="1"><img src="<?php echo $sImgSrcPathHead ?>"></td>
		</tr>
		<tr>
			<td background="<?php echo $sImgSrcPathBodyBorders ?>" align="left" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
					<tr>
						<td width="<?php echo $iLeftMargin ?>"><img src="images/transparantpixel.gif" width="<?php echo $iLeftMargin ?>" height="1"></td>
						<td width="<?php echo ($iWidth - ($iLeftMargin + $iRightMargin))?>" align="left"><?php echo $sTextInTable ?></td>
						<td width="<?php echo $iRightMargin ?>"><img src="images/transparantpixel.gif" width="<?php echo $iRightMargin ?>" height="1"></td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td height="1"><img src="<?php echo $sImgSrcPathFooter ?>"></td>
		</tr>
	</table>
	<?php
	}


	/* =================================================================================================================
	*	function 	: getForumNaam
	*	programmer	: Dennis Renirie
	*	date 		: 2 april 2004
	* 	input		: $forumid 
	*	output		: [naam]
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function getForumNaam($iForumid) 
	{ 
		global $tblForumForums;
		
		$sSql = "SELECT s_naam FROM $tblForumForums WHERE i_id = '$iForumid'";
		$result = mysql_query($sSql) or die("<B>".mysql_error()."</B><BR>".$sSql);			
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sForumnaam = $row["s_naam"];	
		}
		
		unset($tblForumForums);

		return($sForumnaam); 
	} 

	/* =================================================================================================================
	*	function 	: getForumSubject
	*	programmer	: Dennis Renirie
	*	date 		: 2 april 2004
	* 	input		: $subjectid 
	*	output		: [subject]
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function getForumSubject($iSubjectid) 
	{ 
		global $tblForumSubjects;
		
		$sSql = "SELECT s_subject FROM $tblForumSubjects WHERE i_id = '$iSubjectid'";
		$result = mysql_query($sSql) or die("<B>".mysql_error()."</B><BR>".$sSql);			
		while ($row = @ mysql_fetch_array($result)) 
		{
			$sSubject = $row["s_subject"];	
		}
		
		unset($tblForumSubjects);

		return($sSubject); 
	} 

	/* =================================================================================================================
	*	function 	: dayOfYear
	*	programmer	: sploit (http://www.phpfreaks.com/quickcode/getting_the_day_of_the_year/198.php)
	*	date 		: 22 feb 2004
	* 	input		: dag, mnd, jaar
	*	output		: dag van het jaar
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function dayOfYear($d, $m, $y) 
	{ 
		 
		if(checkdate($m, $d, $y)!=true) 
		{ 
			return false; 
		} 
	
		for($i=1 ; $i < $m; $i++) 
		{ 
			$timestamp = mktime (0,0,0,$i,1,$y); 
			$num_days_in_month = date("t", $timestamp); 
			$day_of_year = $day_of_year + $num_days_in_month; 
		} 
	
		$day_of_year = $day_of_year + $d; 
	
		return($day_of_year); 
	} 

	/* =================================================================================================================
	*	function 	: weekOfYear
	*	programmer	: Dennis Renirie
	*	date 		: 22 feb 2004
	* 	input		: maand, dag, jaar
	*	output		: weeknr van het jaar
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function weekOfYear($iDag, $iMaand, $iJaar) 
	{ 
		$iResult = ceil(dayOfYear($iDag, $iMaand, $iJaar) / 7+.5);

		if ($iResult > 52)
			$iResult = ($iResult - 52);
			
		return $iResult;
	}

	/* =================================================================================================================
	*	function 	: dayOfWeek
	*	programmer	: Dennis Renirie
	*	date 		: 22 feb 2004
	* 	input		: unix time stamp van het tijdstip
	*	output		: dag van de week (maandag = 0, dinsdag = 1)
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function dayOfWeek($iUnixTimeStamp)
	{
		$iNrOfWeek = date("w",$iUnixTimeStamp);
		
		//ff zorgen van maandag dag 0 is
		$iNrOfWeek--;
		if ($iNrOfWeek == -1)
			$iNrOfWeek = 6;
		
		return $iNrOfWeek;
	}

	/* =================================================================================================================
	*	function 	: dayOfWeekToString
	*	programmer	: Dennis Renirie
	*	date 		: 24 mei 2004
	* 	input		: dag van de week (maandag = 0, dinsdag = 1)
	*	output		: 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function dayOfWeekToString($iDayOfWeek)
	{
		$sResult = "";
		switch ($iDayOfWeek)
		{
			case 0 :
				$sResult = "maandag";
				break;
			case 1 :		
				$sResult = "dinsdag";
				break;
			case 2 :		
				$sResult = "woensdag";
				break;
			case 3 :		
				$sResult = "donderdag";
				break;
			case 4 :		
				$sResult = "vrijdag";
				break;
			case 5 :		
				$sResult = "zaterdag";
				break;
			case 6 :		
				$sResult = "zondag";
				break;
		}
		return $sResult;
	}

	/* =================================================================================================================
	*	function 	: daysInFeb
	*	programmer	: [some guy at php.net]
	*	date 		: 22 feb 2004
	* 	input		: jaar
	*	output		: hoeveel dagen er in februari (voor het schrikkeljaar)
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function daysInFeb($year)
	{
	
	   //$year must be YYYY
	   //[gregorian] leap year math :
	   
	   if ($year < 0) $year++;
	   $year += 4800;
	
	   if ( ($year % 4) == 0) {
		   if (($year % 100) == 0) {
			   if (($year % 400) == 0) {
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

	/* =================================================================================================================
	*	function 	: daysInMonth
	*	programmer	: [some guy at php.net]
	*	date 		: 22 feb 2004
	* 	input		: maand en jaar
	*	output		: hoeveel dagen er zitten in de gegeven maand ...
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function daysInMonth($month, $year) 
	{
	   $daysInMonth = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	   if ($month != 2) return $daysInMonth[$month - 1];
	   return (checkdate($month, 29, $year)) ? 29 : 28;
	} 
	
	
	/* =================================================================================================================
	*	function 	: filterBadFileChar
	*	programmer	: Dennis Renirie
	*	date 		: 19 feb 2004
	* 	input		: filteren op slechte tekens (bijv in sql statements e.d.
	*	output		: 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function filterBadFileChar($sString)
	{
		$sResult = $sString;
		//deze weggehaald : 17 juni 04
		//$sString = strtr($sString, "\\", "\\\\");
		//$sString = strtr($sString, "\"", "\\\"");
		//$sString = strtr($sString, "\'", "\\\'");
		//$sString = strtr($sString, "&", "");

		//bijgevoegd 17 juni 04:
		$sResult = str_replace("\\", "", $sResult);
		$sResult = str_replace("\"", "", $sResult);
		$sResult = str_replace("'", "", $sResult);
		$sResult = str_replace("&", "", $sResult);
		$sResult = str_replace(":", "", $sResult);
		$sResult = str_replace("*", "", $sResult);
		$sResult = str_replace("/", "", $sResult);
		$sResult = str_replace(">", "", $sResult);
		$sResult = str_replace("<", "", $sResult);		
		$sResult = str_replace("?", "", $sResult);		
		$sResult = str_replace("%", "", $sResult);
		$sResult = str_replace("#", "", $sResult);
		$sResult = str_replace("+", "", $sResult);
		$sResult = str_replace("$", "", $sResult);
		$sResult = str_replace("^", "", $sResult);
		$sResult = str_replace("=", "", $sResult);
		$sResult = str_replace("!", "", $sResult);
		$sResult = str_replace("`", "", $sResult);
		$sResult = str_replace(";", "", $sResult);
		$sResult = str_replace("{", "", $sResult);						
		$sResult = str_replace("}", "", $sResult);						
		$sResult = str_replace("|", "", $sResult);						

		$sResult = str_replace(" ", "_", $sResult);
							
		return $sResult;
	}
	
	/* =================================================================================================================
	*	function 	: filterBadSQLChar
	*	programmer	: Dennis Renirie
	*	date 		: 19 feb 2004
	* 	input		: filteren op slechte tekens (bijv in bestansnamen e.d.
	*	output		: 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function filterBadSQLChar($sString)
	{
		$sResult = $sString;

		$sResult = str_replace("--", "", $sResult);
		$sResult = str_replace("'", "", $sResult);
		$sResult = str_replace("\"", "", $sResult);
		$sResult = str_replace("#", "", $sResult);	
		
		return $sResult;
	}

	/* =================================================================================================================
	*	function 	: rmdirrecursive
	*	programmer	: yuac at doom-3 dot ru [some guy at php.net]
	*	date 		: 19 feb 2004
	* 	input		: recursive delete directory
	*	output		: 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
//	function rmdirrecursive($dir)
//	{
//	   $dh=opendir($dir);
//	   while ($file=readdir($dh))
//	   {
//		   if($file!="." && $file!="..")
//		   {
//			   $fullpath=$dir."/".$file;
//			   if(!is_dir($fullpath))
//			   {
//				   unlink($fullpath);
//			   }else{
//				   rmdirrecursive($fullpath);
//			   }
//		   }
//	   }
//	   closedir($dh);
//	   if(rmdir($dir))
//	   {
//		   return true;
//	   }else{
//		   return false;
//	   }
//	} 
	

	/* =================================================================================================================
	*	function 	: getFileArray
	*	programmer	: Dennis Renirie
	*	date 		: 19 feb 2004
	* 	input		: directory inclusief /  (bijv. /opt/files/images/)
	*	output		: 
	* 	use for		: files uit een directory in array plaatsen
	*	description	: 
	* =================================================================================================================*/
	function getFileArray($sDirectory)
	{
		//alternative way, doesnt work on my php versions
		// Open a known directory, and proceed to read its contents
		/*
		if (is_dir($sDirectory)) 
		{
		   if ($dh = opendir($sDirectory)) 
		   {
			   while (($file = readdir($sDirectory)) !== false) 
			   {
				   //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
				   $arrFiles[] = $file;
			   }
			   closedir($dh);
		   }
		}
		*/

		if (is_dir($sDirectory))
		{
			$d = dir($sDirectory);
	
			while (false !== ($file = $d->read())) 
			{
				$path = $sDirectory.$file;  
				if ($file != "." && $file != "..")
				{
					$arrFiles[] = $file;
				}
			}
			$d->close();

			if (count($arrFiles) > 0)
				sort($arrFiles);
		}
		
		return $arrFiles;
	}

	/* =================================================================================================================
	*	function 	: getFileFolderArray
	*	programmer	: Dennis Renirie
	*	date 		: 10 mei 2004
	* 	input		: directory inclusief /  (bijv. /opt/files/images/); $bDirectories [directories in array ?]; $bFiles [files in array ?] 
	*	output		: 
	* 	use for		: subdirectories en/of bestanden uit een directory in array plaatsen (uitbreiding op getFileArray)
	*	description	: 
	* =================================================================================================================*/
//	function getFileFolderArray($sDirectory, $bDirectories, $bFiles)
//	{
//
//		if (is_dir($sDirectory))
//		{
//               
//			$d = dir($sDirectory);
//    		
//			while (false !== ($file = $d->read())) 
//			{
//                            
//				$path = $sDirectory.$file;  
//				if ($file != "." && $file != "..")
//				{
//					if ($bDirectories)
//						if (is_dir($sDirectory.$file))
//							$arrFiles[] = $file;
//					if ($bFiles)
//						if (is_file($sDirectory.$file))
//							$arrFiles[] = $file;				
//				}
//			}
//			$d->close();
//		}
//		if (count($arrFiles) > 0)
//			sort($arrFiles);
//		
//		return $arrFiles;
//	}

	/* =================================================================================================================
	*	function 	: getFileFolderArrayExtension
	*	programmer	: Dennis Renirie
	*	date 		: 1 juni 2004
	* 	input		: directory inclusief /  (bijv. /opt/files/images/); $bDirectories [directories in array ?]; $bFiles [files in array ?] ; arrExtensions [array van extensions zonder punt bv jpg, jpeg, bmp]
	*	output		: 
	* 	use for		: subdirectories en/of bestanden uit een directory in array plaatsen (uitbreiding op getFileFolderArray)
	*	description	: 
	* =================================================================================================================*/
//	function getFileFolderArrayExtension($sDirectory, $bDirectories, $bFiles, $arrExtensions)
//	{
//		$arrFiles = array();
//                
//		if (is_dir($sDirectory))
//		{
//			$d = dir($sDirectory);
//		
//			while (false !== ($file = $d->read())) 
//			{
//				$path = $sDirectory.$file;  
//				if ($file != "." && $file != "..")
//				{
//					if ($bDirectories)
//						if (is_dir($sDirectory.$file))
//							$arrFiles[] = $file;
//					if ($bFiles)
//						if (is_file($sDirectory.$file))
//							for ($iTeller = 0; $iTeller < count($arrExtensions)-1; $iTeller++)
//								if (strtoupper($arrExtensions[$iTeller]) == strtoupper(getExtension($sDirectory.$file)))
//									$arrFiles[] = $file;
//				}
//			}
//			$d->close();
//		}
//		if (count($arrFiles) > 0)
//			sort($arrFiles);
//		
//		return $arrFiles;
//	}

	/* =================================================================================================================
	*	function 	: showSubjectBar
	*	programmer	: Dennis Renirie
	*	date 		: 20 feb 2004
	* 	input		: 
	*	output		: een nette tabel met daarin het subject
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function showSubjectBar($sTitle)
	{
		global $www_sitemanageradminimages;
	?>
		<table width="100%" border="0">
		  <tr>
			<td bgcolor="#808080">
				<table width="100%" border="0" bgcolor="#999999">
        <tr>		  
						<td valign="middle" width="1%"><IMG src="<?php echo $www_sitemanageradminimages."subjectbar.gif"; ?>"></td>
						<td valign="middle">&nbsp;<font color="#FFFFFF"><B><?php echo $sTitle ?></B></font></td>
					</tr>
			  	</table>
			</td>
		  </tr>
		</table>
	<?php
		unset($www_sitemanageradminimages);
	}
	
	/* =================================================================================================================
	*	function 	: showTip
	*	programmer	: Dennis Renirie
	*	date 		: 19 feb 2004
	* 	input		: 
	*	output		: een nette tabel met daarin de tip
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
	function showTip($sTip)
	{
	?>
		<table width="100%" border="0">
		  <tr>
			<td bgcolor="#000099">
			<table width="100%" border="0" bgcolor="#EBEEFE">
		  <tr>
				  
			<td><font color="#000066"><?php echo $sTip ?></font></td>
				</tr>
			  </table></td>
		  </tr>
		</table>
	<?php 
	}	


	/* =================================================================================================================
	*	function 	: sendemail
	*	programmer	: [some guy at php.net] & Dennis Renirie
	*	date 		: 18 feb 2004
	* 	input		: 
	*	output		: of mail() functie gelukt is
	* 	use for		: het sturen van html emails
	* 	LET OP!		: versturen van attachments werkt NOG niet met Zend Framework
	*	LET OP!		: om de Zend functies te gebruiken moet de mail klasse van Zend geinclude zijn (met include_once), de Zend_Mail klasse moet beschikbaar zijn om van de functies gebruik te kunnen maken
	*	description	: de $sTo, $sToCC, $sToBCC en $sFrom velden mogen emailadressen 
         *                      $sEncoding kan ook 'utf-8' zijn, maar vanwege backwards compatibiliteit is dit default 'iso-8859-1'
	*				 $arrAttachments = Array(
												Array("file"=>"../../test.doc", "content_type"=>"application/msword", "nice_filename" => "test.doc"), 
												Array("file"=>"../../123.pdf", "content_type"=>"application/pdf", "nice_filename" => "123nicename.pdf")
											 );
	* =================================================================================================================*/
	function sendemail($sTo, $sToCC, $sToBCC, $sSubject, $sBody, $sFrom, $arrAttachments = false, $sEncoding = 'iso-8859-1')
	{	
		$bResult = false;

		//Als Zend framework aanwezig, dan deze mail functie gebruiken. Deze is veel uitgebreider en fail proof
		//LET OP! Attachments versturen werkt NOG NIET!
		if (class_exists (Zend_Mail)) 
		{
			$objMail = new Zend_Mail();
			$objMail->setBodyText('Your email client can not display HTML emails.');
			$objMail->setBodyHtml($sBody);
			$objMail->setFrom($sFrom);
			$objMail->addTo($sTo);
			$objMail->addCc($sToCC);			
			$objMail->addBcc($sToBCC);			
			$objMail->setSubject($sSubject);
			$objMail->send();			
			
			$bResult = true;
		}
		else
		{			
			$bAllowedToSend = false;
			$sMimeBound = md5(time());
			$sMessage = "";
			$sNL = "\r\n";
			
			//filterfunctie voor header velden
			if (!function_exists('preprocessHeaderField'))
			{
				function preprocessHeaderField($sField)
				{
					$sField = str_replace("\r", "", $sField);
					$sField = str_replace("\n", "", $sField);
		
					// Remove injected headers
					$sFind = array(	"/bcc\:/i", 
									"/Content\-Type\:/i", 
									"/Mime\-Type\:/i", 
									"/cc\:/i", 
									"/to\:/i");
					$sField = preg_replace($sFind, "** BOGUS HEADER FOUND, POSSIBLE EMAIL-INJECTION ATTEMPT **",  $sField);
					
					return $sField;
				}
			}
	
			if (!function_exists('getSessionsEnabled'))
			{				
				//detecteerfunctie of cookies/sessions wel ingeschakeld zijn (anders werkt de flood-protection niet)
				function getSessionsEnabled()
				{
					$bResult = false;
					
					$_SESSION['function_sendemail_detectsessionsenabled'] = null;
					$_SESSION['function_sendemail_detectsessionsenabled'] = 12345; //er wordt iets toegekend
					if ($_SESSION['function_sendemail_detectsessionsenabled'] == 12345) //er wordt gekeken of dit wel opgeslagen is
						$bResult = true;						
		
					return $bResult;
				}
			}
			
			//benodigde velden filteren op email-injection
			$sTo = preprocessHeaderField($sTo);
			$sToCC = preprocessHeaderField($sToCC);
			$sToBCC = preprocessHeaderField($sToBCC);
			$sSubject = preprocessHeaderField($sSubject);
	
			//(goede) headers plaatsen
			$sHeaders = "";
			$sHeaders .= "MIME-Version: 1.0$sNL";
			//$sHeaders .= "Content-type: text/html; charset=iso-8859-1$sNL";
			$sHeaders .= "From: ".$sFrom."$sNL";
			   
			if ($sToBCC != "")
				$sHeaders .= "Bcc: ".$sToBCC."$sNL";    
			if ($sToCC != "")
				$sHeaders .= "Cc: ".$sToCC."$sNL";    
	
			$sHeaders .= "Message-ID: <".  time() . '-'. uniqid() . '@'. $_SERVER['SERVER_NAME'].">$sNL";
			$sHeaders .= "X-Mailer: PHP v".phpversion()."$sNL";          // These two to help avoid spam-filters
			$sHeaders .= "Content-Type: multipart/related; boundary=\"".$sMimeBound."\""."$sNL";
	
			
	
			# HTML Version
			$sMessage .= "--".$sMimeBound."$sNL";
			$sMessage .= "Content-Type: text/html; charset=$sEncoding$sNL";
			$sMessage .= "Content-Transfer-Encoding: 8bit$sNL$sNL";
			$sMessage .= $sBody."$sNL$sNL";		
			
			# Text Version
			$sMessage .= "--".$sMimeBound."$sNL";
			$sMessage .= "Content-Type: text/plain; charset=$sEncoding$sNL";
			$sMessage .= "Content-Transfer-Encoding: 8bit$sNL$sNL";
			$sTemp =  $sBody;
			$sTemp = str_replace("<br>", "$sNL", $sTemp);
			$sTemp = str_replace("<BR>", "$sNL", $sTemp);		
			$sTemp = str_replace("<br/>", "$sNL", $sTemp);
			$sTemp = str_replace("<BR/>", "$sNL", $sTemp);				
			$sMessage .= strip_tags($sTemp)."$sNL$sNL";
	
                        
			if ($arrAttachments !== false)
			{
				for ($iTeller = 0; $iTeller < count($arrAttachments); $iTeller++)
				{
					$arrCurrAttachment = $arrAttachments[$iTeller];
					$sFile = $arrCurrAttachment["file"];
					$sContentType = $arrCurrAttachment["content_type"];
					$sNiceFileName = $arrCurrAttachment["nice_filename"];
					if (($sNiceFileName == null) || ($sNiceFileName == "")) //als er geen nicefilename is opgegeven dan filename pakken
						$sNiceFileName = $sFile;
					//var_dump($arrCurrAttachment);
					
					$handle=fopen($sFile, 'rb');
					$sContents=fread($handle, filesize($sFile));
					$sContents=chunk_split(base64_encode($sContents));    //Encode The Data For Transition using base64_encode();
					fclose($handle);
					
					$sMessage .= "--".$sMimeBound."$sNL";
					$sMessage .= "Content-Type: ".$sContentType."; name=\"".$sNiceFileName."\"$sNL";
					$sMessage .= "Content-Transfer-Encoding: base64$sNL";
					$sMessage .= "Content-Disposition: attachment; filename=\"".$sNiceFileName."\"$sNL$sNL"; // !! This line needs TWO end of lines !! IMPORTANT !!
					$sMessage .= $sContents."$sNL$sNL";
					$sMessage .= "--".$sMimeBound."--$sNL$sNL";  // finish with two eol's for better security. see Injection.
					
				} 
			}
		
				
			# Finished
			$sMessage .= "--".$sMimeBound."--$sNL$sNL";  // finish with two eol's for better security. see Injection.
	  
			
			//flood protection (het is niet mogelijk om binnen 1 minuut weer een email te sturen)
			if (getSessionsEnabled())
			{
				$bAllowedToSend = false;
				//var_dump($_SESSION);
				if ($_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"] == null || $_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"] == "")
				{
					$bAllowedToSend = true;
				}
				else
				{
					if (is_numeric($_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"]))
					{
						if (($_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"] + 30) < time())  //als het langer dan 1 minuut geleden is
							$bAllowedToSend = true;
						else
						{
							echo "<BR><BR><B><U>ERROR!</U>You have already sent an email to this emailaddress too recently. Please wait a while and send again.";
							echo "<BR><B><U>ERROR!</U>U heeft in een te kort tijdsbestek reeds een email verstuurd aan dit emailadres. Wacht a.u.b. even om nogmaals een email te kunnen versturen. Dit ivm spammen.";
						}
					}
					else
						echo "sessie variabele niet numeriek";
				}
			}		
			else
			{
				echo "<BR><BR><B><U>ERROR!</U> Your browser need to accept so called 'Cookies' to send an email. Your browser doesn't accept Cookies at this moment!</B>";
				echo "<BR><B><U>ERROR!</U> Uw browser moet zogeheten 'Cookies' ondersteunen om een email te kunnen versturen. Uw browser accepteerd geen Cookies op dit moment!</B>";
			}
			
			//mag email versturen ?
			if ($bAllowedToSend)
			{
				//echo " mail($sTo, $sSubject, $sMessage, $sHeaders);";
				$bResult = mail($sTo, $sSubject, $sMessage, $sHeaders);
				$_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"] = time();
			}
			else
			{
				echo "<BR><BR><B><U>ERROR!</U> Your email has not been sent!</B>";
				echo "<BR><B><U>ERROR!</U> Uw email is niet verstuurd!</B>";
				$bResult = false;		
				$_SESSION["function_sendemail_floodprotection_timestamplastemail_$sTo"] = time();
			}
		}//einde if-zend framework
							
		//email uiteindelijk versturen		   
		return $bResult;
	} 
	

	/* =================================================================================================================
	*	function 	: httpgetpost
	*	programmer	: [some chinese guy at http://www.spencernetwork.org/memo/tips-3.php]
	*	date 		: 17 feb 2004 / change: 12-06-2012
	* 	input		: $url
	*	output		: pagina als string, false als niet kan openen
	* 	use for		: krijgen van een pagina, zoals de browser dat ook doet
	*	description	: gewoon aanroepen met httpgetpost("http://www.snotmail.com");
	* =================================================================================================================*/
//	function httpgetpost($url, $method="GET", $headers="", $post=array(""))
//	{
//		/* URL??? */
//		$URL = parse_url($url);
//	
//		/* ???? */
//		if (isset($URL['query'])) {
//			$URL['query'] = "?".$URL['query'];
//		} else {
//			$URL['query'] = "";
//		}
//	
//		/* ??????????80 */
//		if (!isset($URL['port'])) $URL['port'] = 80;
//	
//		/* ???????? */
//		$request  = $method." ".$URL['path'].$URL['query']." HTTP/1.0\r\n";
//	
//		/* ???????? */
//		$request .= "Host: ".$URL['host']."\r\n";
//		$request .= "User-Agent: PHP/".phpversion()."\r\n";
//	
//		/* Basic??????? */
//		if (isset($URL['user']) && isset($URL['pass'])) {
//			$request .= "Authorization: Basic ".base64_encode($URL['user'].":".$URL['pass'])."\r\n";
//		}
//	
//		/* ????? */
//		$request .= $headers;
//	
//		/* POST??????????????URL????????????? */
//		if (strtoupper($method) == "POST") 
//		{
//			while (list($name, $value) = each($post)) 
//			{
//				$POST[] = $name."=".urlencode($value);
//			}
//			$postdata = implode("&", $POST);
//			$request .= "Content-Type: application/x-www-form-urlencoded\r\n";
//			$request .= "Content-Length: ".strlen($postdata)."\r\n";
//			$request .= "\r\n";
//			$request .= $postdata;
//		} 
//		else 
//		{
//			$request .= "\r\n";
//		}
//	
//		/* WEB?????? */
//		$fp = fsockopen($URL['host'], $URL['port']);
//	
//		/* ??????????? */
//		if (!$fp) 
//		{
//			return false;
//		}
//	
//		/* ??????? */
//		fputs($fp, $request);
//	
//		/* ??????? */
//		$response = "";
//		while (!feof($fp)) 
//		{
//			$response .= fgets($fp, 4096);
//		}
//	
//		/* ????? */
//		fclose($fp);
//	
//		/* ?????????????? */
//		$DATA = split("\r\n\r\n", $response, 2);
//	
//	
//		/* ??????????? */
//		return $DATA[1];
//	}




	/* =================================================================================================================
	*	function 	: getExtension
	*	programmer	: Dennis Renirie
	*	date 		: 17 feb 2004
	* 	input		: 
	*	output		: extensie van bestand
	* 	use for		: geven van de extensie van een bestand
	*	description	: 
	* =================================================================================================================*/
	function getExtension($sFile)
	{
		$iPosPunt = strrpos($sFile, "."); 
		$iLengthExtension = strlen($sFile) - $iPosPunt -1;
		return substr($sFile, $iPosPunt + 1, $iLengthExtension);
	}
	
	/* =================================================================================================================
	*	function 	: getFileNameWithoutExtension
	*	programmer	: Dennis Renirie
	*	date 		: 17 feb 2004
	* 	input		: bestandsnaam (geen directory ervoor, puur de bestandsnaam)
	*	output		: bestandsnaam zonder extensie 
	* 	use for		: 
	*	description	: 
	* =================================================================================================================*/
//	function getFileNameWithoutExtension($sFile)
//	{
//		$iPosPunt = strrpos($sFile, "."); 
//		$iLengthFileNameWithoutExtension = $iPosPunt;
//		return substr($sFile, 0, $iLengthFileNameWithoutExtension);
//	}

	/* =================================================================================================================
	*	function 	: deliverFileName
	*	programmer	: Dennis Renirie
	*	date 		: 17 feb 2004
	* 	input		: $sDirectory, $sExtension (extensie van bestand), $sPrefix (bv. nieuws_), $sPostfixId (databaseid van het item)
	*	output		: 
	* 	use for		: geven van een unieke filename in die directory
	*	description	: 
	* =================================================================================================================*/
	function deliverFileName($sDirectory, $sExtension, $sPrefix, $sPostfixId)
	{
		$sPrefix = $sPrefix."_";
		$iHoogsteWaarde = 0;

		//kijken wat de hoogste waarde is van bestanden met dezelfde prefix(inclusief $sPostfixId)
		$d = dir($sDirectory);
		while (false !== ($file = $d->read())) //directory doorlopen
		{
			$path = "$sDirectory/$file";  
		
			if (($file != "." && $file != "..") && is_file($path))
			{
				if(substr($file, 0, strlen($sPrefix.$sPostfixId)) == $sPrefix.$sPostfixId) //komen er bestanden voor met dezelde prefix ?
					if(strrpos($file, "(")) // ja ? dan kijk naar de postfix (tussen () haakjes)
						$iHoogsteWaarde = substr($file, strrpos($file, "(")+1, 1);
			}
		}
		$iNieuwNr = $iHoogsteWaarde+1;
		$sReturn = $sPrefix.$sPostfixId."(".$iNieuwNr.").".$sExtension; //de haakjes () erbij
				
		return $sReturn;
	}



	/* =================================================================================================================
	*	function 	: stripLastBRTags
	*	programmer	: Dennis Renirie
	*	date 		: 16 feb 2004
	* 	input		: $sText
	*	output		: $sText zonder <BR> tags aan het einde
	* 	use for		: in nieuwsberichten staan er vaak een aantal <BR> aan het einde, deze worden verwijderd
	*	description	: NB WERKT NIET GOED!!!!! (verliest data)
	* =================================================================================================================*/
	function stripLastBRTags($sText)
	{
		while (substr(strtoupper(rtrim($sText)), strlen($sText)-5, 4) == "<BR>")
			$sReturn = substr($sText, 0, strlen($sText)-5);
		return $sReturn;
	}



	/* =================================================================================================================
	*	function 	: stripTag
	*	programmer	: Dennis Renirie
	*	date 		: 15 feb 2004
	* 	input		: ($sText, $sHTMLTag)
	*	output		: tekst zonder tag
	* 	use for		: strippen van 1 html tag
	*	description	: 
	* =================================================================================================================*/
//	function stripTag($sText, $sHTMLTag, $sHTMLEndTag)
//	{
//		$sReturn = $sText;
//		
//		$sReturn = str_replace(strtolower($sHTMLTag) , "", $sReturn);
//		$sReturn = str_replace(strtoupper($sHTMLTag) , "", $sReturn);
//		$sReturn = str_replace(strtolower($sHTMLEndTag) , "", $sReturn);
//		$sReturn = str_replace(strtoupper($sHTMLEndTag) , "", $sReturn);
//		
//		return $sReturn;
//	}

	/* =================================================================================================================
	*	function 	: replacePTag
	*	programmer	: Dennis Renirie
	*	date 		: 15 feb 2004
	* 	input		: $sText
	*	output		: p tag vervangen door <BR><BR>
	* 	use for		: ondervangen p tag
	*	description	: 
	* =================================================================================================================*/
//	function replacePTag($sText)
//	{
//		$sReturn = $sText;
//		$sReturn = str_replace("<P>", "", $sReturn);
//		$sReturn = str_replace("<p>", "", $sReturn);
//		$sReturn = str_replace("</P>" , "<BR><BR>", $sReturn);
//		$sReturn = str_replace("</p>" , "<BR><BR>", $sReturn);
//		
//		return $sReturn;
//	}
		

	/* =================================================================================================================
	*	function 	: getMaand(iMaandNr)
	*	programmer	: Dennis Renirie
	*	date 		: 15 feb 2004
	* 	input		: iMaandNr (het nummer van de maand 1 = januari, 2 = februari enz.
	*	output		: string : maand (als iMaandNummer = 1 dan returnd deze functie : januari
	* 	use for		: omzetten maandnummers in strings
	*	description	: 
	* =================================================================================================================*/
	function getMaand($iMaandNr)
	{
		switch($iMaandNr)
		{
			case 1 :
				$sReturn = "januari";
				break;
			case 2 :
				$sReturn = "februari";
				break;
			case 3 :
				$sReturn = "maart";
				break;
			case 4 :
				$sReturn = "april";
				break;
			case 5 :
				$sReturn = "mei";
				break;
			case 6 :
				$sReturn = "juni";
				break;
			case 7 :
				$sReturn = "juli";
				break;
			case 8 :
				$sReturn = "augustus";
				break;
			case 9 :
				$sReturn = "september";
				break;
			case 10 :
				$sReturn = "oktober";
				break;
			case 11 :
				$sReturn = "november";
				break;
			case 12 :
				$sReturn = "december";
				break;
			default :
				$sReturn = "";
				break;
		}
		
		return $sReturn;
	}

	/* =================================================================================================================
	*	function 	: htmlRedirect
	*	programmer	: Dennis Renirie
	*	date 		: 15 feb 2004
	* 	input		: aantal seconden voor direction, pagina om naar toe te redi
	*	output		: html tekst voor in de <HEAD> tag
	* 	use for		: redirection op html niveau naar een andere pagina
	*	description	: 
	* =================================================================================================================*/
	function htmlRedirect($sPage)
	{
		global $iAantalSecondenRedirect;
		return "<meta http-equiv=\"REFRESH\" content=\"$iAantalSecondenRedirect;url=$sPage\">";
		unset($iAantalSecondenRedirect);
	}


	/* =================================================================================================================
	*	function 	: changeRecordOrder
	*	programmer	: Dennis Renirie
	*	date 		: 1 feb 2004
	* 	input		: 
	*	output		: omgedraaide records in MySQL
	* 	use for		: om de sorteervolgorde van 2 records te veranderen. Hiervoor dient er een volgorde veld te zijn waarop gesorteerd wordt
	*	description	: 
	* =================================================================================================================*/
	function changeRecordOrderDB($sTable, $sVolgordeField, $iPKField, $iPKValue, $bUp)
	{
		function getVolgordeNr($sTable, $iID, $sVolgordeField, $iPKField, $iPKValue)
		{	
			$sSQL = "SELECT $sVolgordeField FROM $sTable WHERE $iPKField =  '$iPKValue'";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
			while ($row = @ mysql_fetch_array($result)) 
			{
				$iVolgordeNr = $row[$sVolgordeField];
			}

			return $iVolgordeNr;
		}
		
		function getID($sTable, $iVolgordeNr, $iPKField, $sVolgordeField)
		{
			$sSQL = "SELECT $iPKField FROM $sTable WHERE $sVolgordeField = '$iVolgordeNr'";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
			while ($row = @ mysql_fetch_array($result)) 
			{
				$iTempId = $row[$iPKField];
			}
			
			return $iTempId;
		}
		
		function setVolgordeNr($sTable, $iID, $iVolgordeNr, $sVolgordeField, $iPKField)
		{
			$sSQL = "UPDATE $sTable SET $sVolgordeField = '$iVolgordeNr' WHERE $iPKField = '$iID'";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);	
		}


		function getNextVolgordeNr($sTable, $iHuidigeVolgordeNr, $sVolgordeField) //volgende i_volgorde zoeken in de tabel
		{
			//====grootste waarde verkrijgen
			$sSQL = "SELECT MAX($sVolgordeField) AS maxvolg FROM $sTable";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
	
			while ($row = @ mysql_fetch_array($result)) 
				$iTempKleinste = $row["maxvolg"];
			//EINDE====grootste waarde verkrijgen
			
			$sSQL = "SELECT $sVolgordeField FROM $sTable WHERE $sVolgordeField > $iHuidigeVolgordeNr ORDER BY $sVolgordeField ASC";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
			$row = @mysql_fetch_array($result); // omdat gesorteerd op volgorde is de eerste altijd de kleinste	
			if ($row[$sVolgordeField] != null)
				$iTempKleinste = $row[$sVolgordeField];
			
			terminate;
			return $iTempKleinste;		
		}

		function getPreviousVolgordeNr($sTable, $iHuidigeVolgordeNr, $sVolgordeField) //vorige i_volgorde zoeken in de tabel
		{
			//====kleinste waarde verkrijgen
			$sSQL = "SELECT MIN($sVolgordeField) AS minvolg FROM $sTable";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
	
			while ($row = @ mysql_fetch_array($result)) 
				$iTempGrootste = $row["minvolg"];
			//EINDE====kleinste waarde verkrijgen
			
			$sSQL = "SELECT $sVolgordeField FROM $sTable WHERE $sVolgordeField < $iHuidigeVolgordeNr ORDER BY $sVolgordeField DESC";
			$result = mysql_query($sSQL) or die("<B>".mysql_error()."</B><BR>".$sSQL);
			$row = @mysql_fetch_array($result); // omdat gesorteerd op volgorde is de eerste altijd de grootste	
			if ($row[$sVolgordeField] != null)
				$iTempGrootste = $row[$sVolgordeField];
		
			return $iTempGrootste;		
		}


		$iVan = getVolgordeNr($sTable, $iPKValue, $sVolgordeField, $iPKField, $iPKValue);
		
		if ($bUp)
			$iNaar = getPreviousVolgordeNr($sTable, $iVan, $sVolgordeField);
		else
			$iNaar = getNextVolgordeNr($sTable, $iVan, $sVolgordeField);

		$iIDNaar = getID($sTable, $iNaar, $iPKField, $sVolgordeField);
		
		//echo "id $iPKValue krijgt i_volgorde $iNaar<BR>"; 
		//echo "id $iIDNaar krijgt i_volgorde $iVan<BR>"; 
		setVolgordeNr ($sTable, $iPKValue, $iNaar, $sVolgordeField, $iPKField);
		setVolgordeNr ($sTable, $iIDNaar, $iVan, $sVolgordeField, $iPKField);
	}


	/* =================================================================================================================
	*	function 	: getRowColor
	*	programmer	: Dennis Renirie
	*	date 		: 22 jan 2004
	* 	input		: global : $sRowColor1, $sRowColor2, $sRowColorCurrent
	*	output		: de string: bgcolor="kleur"
	* 	use for		: om en om kleuren van rijen in een tabel
	*	description	: 
	* =================================================================================================================*/
	function getRowColor()
	{
		global $sRowColor1;
		global $sRowColor2;
		global $sRowColorCurrent;
		
		if ($sRowColorCurrent == $sRowColor1)
		{
			$sRowColorCurrent = $sRowColor2;
		}
		else
		{
			$sRowColorCurrent = $sRowColor1;
		}

		return "bgcolor=\"$sRowColorCurrent\"";
				
		unset($sRowColor1);
		unset($sRowColor2);		
		unset($sRowColorCurrent);	
	}

	
	

	/* =================================================================================================================
	*	function 	: getImageByExtension
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $sFile (bestandsnaam)
	*	output		: plaatjenaam (afhankelijk van extensie van file)
	* 	use for		: net zoals in windows weergeven wat voor type file het is d.m.v. een plaatje
	*	description	: de bestandsextensie wordt bekeken. Afhankelijk daarvan retourneert functie een icoontjenaam
	* =================================================================================================================*/
//	function getImageByExtension($sFile)
//	{
//		if (is_dir($sFile))
//		{
//			return "file-folder.gif";
//		}
//		else
//		{
//			switch(getExtension(strtolower($sFile))) 
//			{
//				case "avi": 
//					return "file-avi.gif";
//					break;
//				case "mpg": 
//					return "file-avi.gif";
//					break;
//				case "mpeg": 
//					return "file-avi.gif";
//					break;
//				case "mov": 
//					return "file-avi.gif";
//					break;
//				case "bat": 
//					return "file-bat.gif";
//					break;
//				case "dfm": 
//					return "file-dfm.gif";
//					break;
//				case "gif": 
//					return "file-gif.gif";
//					break;
//				case "dll": 
//					return "file-dll.gif";
//					break;
//				case "dpr": 
//					return "file-dpr.gif";
//					break;
//				case "exe": 
//					return "file-exe.gif";
//					break;
//				case "com": 
//					return "file-com.gif";
//					break;
//				case "gif": 
//					return "file-gif.gif";
//					break;
//				case "htm": 
//					return "file-htm.gif";
//					break;
//				case "html": 
//					return "file-htm.gif";
//					break;
//				case "asp": 
//					return "file-htm.gif";
//					break;
//				case "php": 
//					return "file-htm.gif";
//					break;
//				case "pl": 
//					return "file-htm.gif";
//					break;
//				case "ini": 
//					return "file-ini.gif";
//					break;
//				case "jpg": 
//					return "file-jpg.gif";
//					break;
//				case "jpeg": 
//					return "file-jpg.gif";
//					break;
//				case "png": 
//					return "file-jpg.gif";
//					break;
//				case "bmp": 
//					return "file-jpg.gif";
//					break;
//				case "mdb": 
//					return "file-mdb.gif";
//					break;
//				case "csv": 
//					return "file-txt.gif";
//					break;
//				case "sql": 
//					return "file-txt.gif";
//					break;
//				case "png": 
//					return "file-txt.gif";
//					break;
//				case "java": 
//					return "file-txt.gif";
//					break;
//				case "mp3": 
//					return "file-mp3.gif";
//					break;
//				case "msi": 
//					return "file-msi.gif";
//					break;
//				case "pas": 
//					return "file-pas.gif";
//					break;
//				case "pdf": 
//					return "file-pdf.gif";
//					break;
//				case "ppt": 
//					return "file-ppt.gif";
//					break;
//				case "rar": 
//					return "file-rar.gif";
//					break;
//				case "ttf": 
//					return "file-ttf.gif";
//					break;
//				case "txt": 
//					return "file-txt.gif";
//					break;
//				case "vsd": 
//					return "file-vsd.gif";
//					break;
//				case "doc": 
//					return "file-doc.gif";
//					break;
//				case "xls": 
//					return "file-xls.gif";
//					break;
//				case "zip": 
//					return "file-zip.gif";
//					break;
//				case "bmp": 
//					return "file-bmp.gif";
//					break;
//				case "xml": 
//					return "file-xml.gif";
//					break;
//				case "rtf": 
//					return "file-rtf.gif";
//					break;
//				case "tar": 
//					return "file-tar.gif";
//					break;						
//				case "psd": 
//					return "file-psd.gif";
//					break;			
//				case "png": 
//					return "file-png.gif";
//					break;			
//				case "bmp": 
//					return "file-bmp.gif";
//					break;			
//					
//				default:
//					return "file.gif";
//					break;
//			}//einde case
//		}//einde else is_dir
//	}
	
	/* =================================================================================================================
	*	function 	: terminate
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: 
	*	output		: 
	* 	use for		: afbreken van je php script. Equivalent van response.end in ASP.
	*	description	: 
	* =================================================================================================================*/
	function terminate()
	{
		exit();
	}
	
	/* =================================================================================================================
	*	function 	: redirect
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $sUrl
	*	output		: [niks, behalve een html header]
	* 	use for		: meteen naar andere pagina gaan. Equivalent van response.redirect van ASP
	*	description	: 
	* =================================================================================================================*/
	function redirect($sUrl)
	{
		header("location: $sUrl"); //kan ook nog zijn : location $sUrl
	}
	
	/* =================================================================================================================
	*	function 	: outputFile
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $sOriginalFile (file om te downloaden), $sNewFileName (file om te downloaden voor client), $sType (content-type)
	*	output		: [niks, behalve een html header]
	* 	use for		: download van een file verwerken in een script (bijvoorbeeld tellen hoevaak dat een file gedownload is
	*	description	: 
	* =================================================================================================================*/
	function outputFile($sOriginalFile, $sNewFileName, $sType)
	{
		//$sType kan zijn: application/pdf
		
		// We gaan een $sFile outputten
		header("Content-type: $sType");
		// De $sType die we gaan outputten heet $sNewFileName
		header("Content-Disposition: attachment; filename=$sNewFileName");
		// De $sType source is in $sOriginalFile
		readfile($sOriginalFile);
		
		/* =====origineel van PHP.net:
		// We gaan een PDF outputten
		header("Content-type: application/pdf");
		// De PDF die we gaan outputten heet downloaded.pdf
		header("Content-Disposition: attachment; filename=downloaded.pdf");
		// De PDF source is in original.pdf
		readfile("original.pdf");
		*/
	}
	

	/* =================================================================================================================
	*	function 	: boolToSelected
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: boolean
	*	output		: string : selected of ""
	* 	use for		: in html een boolean uitlezen. bv checkbox: <SELECT echo boolToSelected($mijnBool); ">
	*	description	: kijken of boolean true, dan selected teruggeven
	* =================================================================================================================*/
	function boolToSelected($bBoolean)
	{
		if(($bBoolean == true) || ($bBoolean == "1"))
		{
			return "selected";
		}
		else
		{
			return "";
		}
	}
	
		/* =================================================================================================================
	*	function 	: boolToChecked
	*	programmer	: Dennis Renirie
	*	date 		: 23 jan 2004
	* 	input		: boolean
	*	output		: string : checked of ""
	* 	use for		: in html een boolean uitlezen. bv checkbox: <INPUT type="checkbox"  echo boolToSelected($mijnBool); ">
	*	description	: kijken of boolean true, dan selected teruggeven
	* =================================================================================================================*/
	function boolToChecked($bBoolean)
	{
		if(($bBoolean == true) || ($bBoolean == "1"))
		{
			return "checked";
		}
		else
		{
			return "";
		}
	}
	
	/* =================================================================================================================
	*	function 	: showTijdSelectBoxen
	*	programmer	: Dennis Renirie
	*	date 		: 22 feb 2004
	* 	input		: $iSelectUur,$iSelectMinuut
	*	output		: [de functie echo't zelf de output naar HTML]
	* 	use for		: het geven van de tijd in selectboxen
	*	description	: 
	* =================================================================================================================*/
	function showTijdSelectBoxen($iSelectUur,$iSelectMinuut)
	{
		echo "<SELECT name=\"edtUur\" size=\"1\">\n";
		for ($iTeller = 1; $iTeller < 24; $iTeller++)
		{		
			echo "<OPTION value=\"$iTeller\" ".boolToSelected($iSelectUur == $iTeller).">$iTeller</option>\n";
		}
		echo "</SELECT>\n";
		
		echo ":";

		echo "<SELECT name=\"edtMinuut\" size=\"1\">\n";
		for ($iTeller = 0; $iTeller < 60; $iTeller = $iTeller + 5)
		{		
			echo "<OPTION value=\"$iTeller\" ".boolToSelected($iSelectMinuut == $iTeller).">$iTeller</option>\n";
		}
		echo "</SELECT>\n";
		
		echo "u";
	}
	
	/* =================================================================================================================
	*	function 	: showDatumSelectBoxen
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $iSelectDag $iSelectMaand $iSelectJaar (welke dag, maand, jaar selecteren ?) 
	*	output		: [de functie echo't zelf de output naar HTML]
	* 	use for		: het geven van de datum (dag, maand, jaar) in selectboxen (HTML)
	*	description	: 
	* =================================================================================================================*/
	function showDatumSelectBoxen($iSelectDag,$iSelectMaand, $iSelectJaar)
	{
		global $www_sitemanageradmin;
		
		echo "<SELECT name=\"edtDag\" size=\"1\">\n";
		for ($iTeller = 1; $iTeller < 32; $iTeller++)
		{		
			echo "<OPTION value=\"$iTeller\" ".boolToSelected($iSelectDag == $iTeller).">$iTeller</option>\n";
		}
		echo "</SELECT>\n";
		
		echo "<SELECT name=\"edtMaand\" size=\"1\">\n";
		for ($iTeller = 1; $iTeller < 13; $iTeller++)
		{		
			echo "<OPTION value=\"$iTeller\" ".boolToSelected($iSelectMaand == $iTeller).">".getMaand($iTeller)."</option>\n";
		}
		echo "</SELECT>\n";
		
		echo "<SELECT name=\"edtJaar\" size=\"1\">\n";
		for ($iTeller = 1970; $iTeller < 2099; $iTeller++)
		{		
			echo "<OPTION value=\"$iTeller\" ".boolToSelected($iSelectJaar == $iTeller).">$iTeller</option>\n";
		}
		echo "</SELECT>\n";
		echo "<INPUT TYPE=\"button\" VALUE=\"Kalender\" onClick=\"newCal()\"> (dag-maand-jaar)";
		
		?>
			<SCRIPT TYPE="text/javascript" LANGUAGE="JavaScript">
				function y2k(number)    
				{ 
					return (number < 1000) ? number + 1900 : number; 
				}

				var today = new Date();
				var day   = today.getDate();
				var month = today.getMonth();
				var year  = y2k(today.getYear());

				function padout(number) 
				{ 
					return (number < 10) ? '0' + number : number; 
				}

				function restart() 
				{
					for (iTeller=0; iTeller<document.frmEdit.edtDag.length; iTeller++) 
					{ 
						//alert(padout(day));
						if (day == document.frmEdit.edtDag.options[iTeller].value)
						{
							document.frmEdit.edtDag.options[iTeller].selected = true; 
						}
						else
						{
							document.frmEdit.edtDag.options[iTeller].selected = false;
						} 
					} 
					
					for (iTeller=0; iTeller<document.frmEdit.edtMaand.length; iTeller++) 
					{ 
						
						if (month - 0 + 1 == document.frmEdit.edtMaand.options[iTeller].value)
						{
							document.frmEdit.edtMaand.options[iTeller].selected = true; 
						}
						else
						{
							document.frmEdit.edtMaand.options[iTeller].selected = false; 
						}					
					} 

					for (iTeller=0; iTeller<document.frmEdit.edtJaar.length; iTeller++) 
					{ 
						if (year == document.frmEdit.edtJaar.options[iTeller].value)
						{
							document.frmEdit.edtJaar.options[iTeller].selected = true; 
						}
						else
						{
							document.frmEdit.edtJaar.options[iTeller].selected = false; 
						}
					} 
					mycalwindow.close();
				}

				function newCal() 
				{
					mycalwindow=open('<?php echo $www_sitemanageradmin; ?>calendar.htm','myname','resizable=no,width=350,height=270');
					mycalwindow.location.href = '<?php echo $www_sitemanageradmin; ?>calendar.htm';
					if (mycalwindow.opener == null) mycalwindow.opener = self;
				}
			</SCRIPT>
		<?php
		
		unset($www_sitemanageradmin);
	}
	
	
	/* =================================================================================================================
	*	function 	: addRecord
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $arrVariables (array met fieldnames van mysql), $arrValues (array met waardes van de fields), $sTable (tabel in mysql)
	*	output		: [nieuw record in mysql]
	* 	use for		: toevoegen van een record aan mysql
	*	description	: 
	* =================================================================================================================*/
	function addRecordDB($arrVariables, $arrValues, $sTable)
	{
                global $objDB;
                
		$sSQL = "INSERT INTO $sTable SET ";
		for ($iTeller = 0; $iTeller < count($arrVariables); $iTeller++)
		{
			$arrValues[$iTeller] = str_replace("'", "`", $arrValues[$iTeller]);			
			$sSQL = $sSQL.$arrVariables[$iTeller]."='".$arrValues[$iTeller]."'";
			if ($iTeller < count($arrVariables)-1)
				 $sSQL = $sSQL.", ";
		}
		 
		return $objDB->query($sSQL);  
	}
	
	


	/* =================================================================================================================
	*	function 	: changeRecord
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $arrVariables (array met fieldnames van mysql), $arrValues (array met waardes van de fields), $sTable (tabel in mysql), $pkfield (primary key field in mysql [meestal is dit id of code], $pkvalue (waarde van de primary key)
	*	output		: [gewijzigd record in mysql]
	* 	use for		: wijzigen van een record in mysql
	*	description	: 
	* =================================================================================================================*/
	function changeRecordDB($arrVariables, $arrValues, $sTable, $pkfield, $pkvalue, $pk2field = '', $pk2value = '')
	{
/*
		$sSQL = "UPDATE $sTable SET ";		
		for ($iTeller = 0; $iTeller < count($arrVariables); $iTeller++)
		{
			$arrValues[$iTeller] = str_replace("'", "`", $arrValues[$iTeller]);
			$sSQL = $sSQL.$arrVariables[$iTeller]."='".$arrValues[$iTeller]."'";
			if ($iTeller < count($arrVariables)-1)
				 $sSQL = $sSQL.", ";
		}
		$sSQL = $sSQL." WHERE $pkfield = '$pkvalue'";
                if ($pk2field != '')
                    $sSQL = $sSQL." AND $pk2field = '$pk2value'";    
		 //echo $sSQL;
		return mysql_query($sSQL) or die("<BR>De volgende fout heeft zich voorgedaan :<BR><B>".mysql_error()."</B><BR>".$sSQL."<BR><BR><B>LET OP : Record is NIET gewijzigd!</B>");
 * */
            global $objDB;

            $sSQL = "UPDATE $sTable SET ";		
            for ($iTeller = 0; $iTeller < count($arrVariables); $iTeller++)
            {
                    $arrValues[$iTeller] = str_replace("'", "`", $arrValues[$iTeller]);
                    $sSQL = $sSQL.$arrVariables[$iTeller]."='".$arrValues[$iTeller]."'";
                    if ($iTeller < count($arrVariables)-1)
                             $sSQL = $sSQL.", ";
            }
            $sSQL = $sSQL." WHERE $pkfield = '$pkvalue'";
            if ($pk2field != '')
                $sSQL = $sSQL." AND $pk2field = '$pk2value'";    
             //echo $sSQL;

            return $objDB->query($sSQL);            
 
	}
        
        
	
	
	/* =================================================================================================================
	*	function 	: deleteRecord
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $sTable (tabel waarin verwijderd moet worden), $pkfield (naam van het primary key), $pkvalue (waarde van de primary key)
	*	output		: [verwijderd record in mysql]
	* 	use for		: verwijderen van een record in mysql
	*	description	: 
	* =================================================================================================================*/
	function deleteRecordDB($sTable, $pkfield, $pkvalue)
	{
            global $objDB;
            
            if (is_numeric($pkvalue))
                $sSQL = "DELETE FROM $sTable WHERE $pkfield = '$pkvalue'";               
            else
            {
                echo 'primary key value not numeric';
                return false;
            }
                
                    
            return $objDB->query($sSQL); 
	}

	/* =================================================================================================================
	*	function 	: deleteRecordInclImg
	*	programmer	: Dennis Renirie
	*	date 		: 18 jan 2004
	* 	input		: $sTable (tabel waarin verwijderd moet worden), $pkfield (naam van het primary key), $pkvalue (waarde van de primary key)
	*	output		: [verwijderd record in mysql]
	* 	use for		: verwijderen van een record in mysql en de plaatjes in s_plaatjeurl en s_plaatjeurlklein
	*	description	: 
	* =================================================================================================================*/
	function deleteRecordDBInclImg($sTable, $pkfield, $pkvalue, $sDirectory, $sFieldImage1 = '', $sFieldImage2 = '')
	{
		if ($sFieldImage1 == '')
			$sFieldImage1 = 's_plaatjeurl';
			
		if ($sFieldImage2 == '')
			$sFieldImage2 = 's_plaatjeurlklein';
	
		$sSQL = "SELECT $sFieldImage1, $sFieldImage2 FROM $sTable WHERE $pkfield = '$pkvalue'";
                $arrResult = mysqliToArray($sSQL);
                
                foreach($arrResult as $sRow)
                {
                    $sPlaatjeurl = $sRow[$sFieldImage1];
                    $sPlaatjeurlklein = $sRow[$sFieldImage2];
                }
		
		if (is_file($sDirectory.$sPlaatjeurl))
			if (unlink($sDirectory.$sPlaatjeurl) == false)
				echo "kon bestand $sPlaatjeurl niet verwijderen";

		if (is_file($sDirectory.$sPlaatjeurlklein))
			if (unlink($sDirectory.$sPlaatjeurlklein) == false)
				echo "kon bestand $sPlaatjeurlklein niet verwijderen";	
		
		return deleteRecordDB($sTable, $pkfield, $pkvalue);
	}

	/* =================================================================================================================
	*	function 	: checkSizeAndResize
	*	programmer	: Dennis Renirie
	*	date 		: 17 juni 2004
	* 	input		: sImagePath (path van het plaatje dus bijv. /var/www/plaatje.jpg); $iMaxWidth en $iMaxHeight
	*	output		: geresized plaatje als nodig. true of false (true als geresized, false als niet geresized)
	* 	use for		: resizen van plaatjes als boven. handig voor nieuwsitems bijvoorbeeld. Als je ALTIJD resized worden 
						kleine plaatjes zo lelijk
	*	description	: deze functie kijkt of een plaatje groter is, dan een bepaalde afmetingen, zo ja, dan resizen. 
	* =================================================================================================================*/
//	function checkSizeAndResize($sImagePath, $iMaxWidth, $iMaxHeight, $iImageQuality)
//	{
//		$bResized = false;
//		// Set a few variables 
//		//$image = "/home/web/images/original.jpg"; 
//		//$newimage = "/home/web/images/new.jpg"; 
//		//$image_quality = 80; 
//		//$addborder = 1; 
//		//$max_height = 200; 
//		//$max_width = 300;
//		$imgSrc = ImageCreateFromJpeg($sImagePath);
//		$iWidth = ImageSX($imgSrc); 
//		$iHeight = ImageSY($imgSrc); 
//		ImageDestroy($imgSrc);
//		
//		if (($iWidth > $iMaxWidth) || ($iHeight > $iMaxHeight)) //als buiten maximale waarden dan resizen
//		{
//			resizeJPG($sImagePath, $sImagePath, $iImageQuality, 0, $iMaxHeight, $iMaxWidth);
//			$bResized = true;	
//		}
//		
//		return($bResized);
//	}


	/* =================================================================================================================
	*	function 	: resizeJPG
	*	programmer	: liquidkernel
	*	date 		: 18 jan 2004
	* 	input		: 
	*	output		: [plaatje met de naam $newimage]
	* 	use for		: het resizen van een jpg
	*	description	: 
	* =================================================================================================================*/
//	function resizeJPG($image, $newimage, $image_quality, $addborder, $max_height, $max_width)
//	{	
//		// Set a few variables 
//		//$image = "/home/web/images/original.jpg"; 
//		//$newimage = "/home/web/images/new.jpg"; 
//		//$image_quality = 80; 
//		//$addborder = 1; 
//		//$max_height = 200; 
//		//$max_width = 300; 
//		
//		// Main code 
//		$src_img = ImageCreateFromJpeg($image); 
//		$orig_x = ImageSX($src_img); 
//		$orig_y = ImageSY($src_img); 
//		
//		$new_y = $max_height; 
//		$new_x = $orig_x/($orig_y/$max_height);   
//		
//		if ($new_x > $max_width) { 
//			$new_x = $max_width; 
//			$new_y = $orig_y/($orig_x/$max_width); 
//		} 
//		
//		$dst_img = ImageCreateTrueColor($new_x,$new_y); 
//		ImageCopyResampled($dst_img, $src_img, 0, 0, 0, 0, $new_x, $new_y, $orig_x, $orig_y); 
//		
//		if ($addborder == 1) { 
//			// Add border 
//			$black = ImageColorAllocate($dst_img, 0, 0, 0); 
//			ImageSetThickness($dst_img, 1); 
//			ImageLine($dst_img, 0, 0, $new_x, 0, $black); 
//			ImageLine($dst_img, 0, 0, 0, $new_y, $black); 
//			ImageLine($dst_img, $new_x-1, 0, $new_x-1, $new_y, $black); 
//			ImageLine($dst_img, 0, $new_y-1, $new_x, $new_y-1, $black); 
//		} 
//			 
//		ImageJpeg($dst_img, $newimage, $image_quality); 
//		ImageDestroy($src_img); 
//		ImageDestroy($dst_img); 
//	}
	
//========================================================================LOGGING FUNCTIONS=============================
//======================================================================================================================
function extract_agent($AGT) 
{
	$st_sys = ''; 
	$st_ver = ''; 
	$st_sysver = ''; 
	if (eregi("(opera) ([0-9]{1,2}.[0-9]{1,3}){0,1}",$AGT,$st_regs) || eregi("(opera/)([0-9]{1,2}.[0-9]{1,3}){0,1}",$AGT,$st_regs)) {$st_brows = "Opera"; $st_ver = $st_regs[2];
	} 
	else if(eregi("(konqueror)/([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Konqueror"; $st_ver = $st_regs[2]; $st_sys = "Linux";} 
	else if(eregi("(lynx)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})",$AGT,$st_regs) ) {$st_brows = "Lynx"; $st_ver = $st_regs[2];} 
	else if(eregi("(links) ([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Links"; $st_ver = $st_regs[2];} 
	else if(eregi("(omniweb/)([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "OmniWeb"; $st_ver = $st_regs[2];} 
	else if(eregi("(webtv/)([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "WebTV"; $st_ver = $st_regs[2];} 
	else if(eregi("(msie) ([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Internet Explorer"; $st_ver = $st_regs[2];} 
	else if(eregi("(netscape6)/(6.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Netscape"; $st_ver = $st_regs[2];} 
	else if(eregi("mozilla/5",$AGT)) {$st_brows = "Netscape"; $st_ver = '6'; } 
	else if(eregi("(mozilla)/([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Netscape"; $st_ver = $st_regs[2];} 
	else if(eregi("w3m",$AGT)) {$st_brows = "w3m"; } 
	else if(eregi("(scooter)-([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Scooter"; $st_ver = $st_regs[2];} 
	else if(eregi("(w3c_validator)/([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "W3C"; $st_ver = $st_regs[2];} 
	else if(eregi("(googlebot)/([0-9]{1,2}.[0-9]{1,3})",$AGT,$st_regs)) {$st_brows = "Google"; $st_ver = $st_regs[2];} 
	else {$st_brows = "Onbekend"; $st_ver = "";} 
	if (eregi("linux",$AGT)) {$st_sys = "Linux";} 
	else if(eregi("Win 9x 4.90",$AGT)) {$st_sys = "Windows Me";} 
	else if(eregi("win32",$AGT)) {$st_sys = "Windows 32-bit";} 
	else if(eregi("windows 2000",$AGT)) {$st_sys = "Windows 2000";} 
	else if((eregi("(win)([0-9]{2})",$AGT,$st_regs)) || (eregi("(windows) ([0-9]{2})",$AGT,$st_regs))) {$st_sys = "Windows ".$st_regs[2];} 
	else if(eregi("(windows nt)( ){0,1}(5.0)",$AGT,$st_regs)) {$st_sys = "Windows 2000";} 
	else if(eregi("(windows nt)( ){0,1}(5.1)",$AGT,$st_regs)) {$st_sys = "Windows XP";} 
	else if(eregi("(winnt)([0-9]{1,2}.[0-9]{1,2}){0,1}",$AGT,$st_regs)) {$st_sys = "Windows NT".$st_regs[2];} 
	else if(eregi("(windows nt)( ){0,1}([0-9]{1,2}.[0-9]{1,2}){0,1}",$AGT,$st_regs)) {$st_sys = "Windows NT".$st_regs[3];} 
	else if(eregi("PPC",$AGT) || eregi("Mac_PowerPC",$AGT)) {$st_sys = "Macintosh Power PC";} 
	else if(eregi("mac",$AGT)) {$st_sys = "Macintosh OS";} 
	else if(eregi("(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}",$AGT,$st_regs)) {$st_sys = "SunOS"; $st_sysver = $st_regs[2];} 
	else if(eregi("(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}",$AGT,$st_regs)) {$st_sys = "BeOS"; $st_sysver = $st_regs[2];} 
	else if(eregi("freebsd",$AGT)) {$st_sys = "FreeBSD";} 
	else if(eregi("openbsd",$AGT)) {$st_sys = "OpenBSD";} 
	else if(eregi("irix",$AGT)) {$st_sys = "IRIX";} 
	else if(eregi("os/2",$AGT)) {$st_sys = "OS/2";} 
	else if(eregi("plan9",$AGT)) {$st_sys = "Plan9";} 
	else if(eregi("unix",$AGT) || eregi("hp-ux",$AGT) ) {$st_sys = "Unix";} 
	else if(eregi("osf",$AGT)) {$st_sys = "OSF";} 
	else if(eregi("X11",$AGT) && !isset($st_sys)) {$st_sys = "Unix";} 
	else {$st_sys = "Onbekend";} 
	if ($st_brows != '' || $st_sys != '') { 
	$new_agt[0] = $st_brows; 
	$new_agt[1] = $st_ver; 
	$new_agt[2] = $st_sys; 
	} 
	else 
	{ 
	$new_agt = $AGT; 
	} 
	
	return($new_agt); 
} 

//voorbeeld: 
// $agt = extract_agent($_SERVER['HTTP_USER_AGENT']); 
// if(is_array($agt)) { 
// $naam = $agt[0]; 
// $versie = $agt[1]; 
// $os = $agt[2]; 
// } 


//=========================land: 

$ccodes = array ( 
"ad" => "Andorra", 
"ae" => "United Arab Emirates", 
"af" => "Afghanistan", 
"ag" => "Antigua and Barbuda", 
"ai" => "Anguilla", 
"al" => "Albania", 
"am" => "Armenia", 
"an" => "Netherlands Antilles", 
"ao" => "Angola", 
"aq" => "Antarctica", 
"ar" => "Argentina", 
"as" => "American Samoa", 
"at" => "Austria", 
"au" => "Australia", 
"aw" => "Aruba", 
"az" => "Azerbaijan", 
"ba" => "Bosnia Herzegovina", 
"bb" => "Barbados", 
"bd" => "Bangladesh", 
"be" => "Belgium", 
"bf" => "Burkina Faso", 
"bg" => "Bulgaria", 
"bh" => "Bahrain", 
"bi" => "Burundi", 
"bj" => "Benin", 
"bm" => "Bermuda", 
"bn" => "Brunei Darussalam", 
"bo" => "Bolivia", 
"br" => "Brazil", 
"bs" => "Bahamas", 
"bt" => "Bhutan", 
"bv" => "Bouvet Island", 
"bw" => "Botswana", 
"by" => "Belarus", 
"bz" => "Belize", 
"ca" => "Canada", 
"cc" => "Cocos (Keeling) Islands", 
"cf" => "Central African Republic", 
"cg" => "Congo", 
"ch" => "Switzerland", 
"ci" => "Cote DIvoire", 
"ck" => "Cook Islands", 
"cl" => "Chile", 
"cm" => "Cameroon", 
"cn" => "China", 
"co" => "Colombia", 
"cr" => "Costa Rica", 
"cs" => "Czechoslovakia", 
"cu" => "Cuba", 
"cv" => "Cape Verde", 
"cx" => "Christmas Island", 
"cy" => "Cyprus", 
"cz" => "Czech Republic", 
"de" => "Germany", 
"dj" => "Djibouti", 
"dk" => "Denmark", 
"dm" => "Dominica", 
"do" => "Dominican Republic", 
"dz" => "Algeria", 
"ec" => "Ecuador", 
"ee" => "Estonia", 
"eg" => "Egypt", 
"eh" => "Western Sahara", 
"er" => "Eritrea", 
"es" => "Spain", 
"et" => "Ethiopia", 
"fi" => "Finland", 
"fj" => "Fiji", 
"fk" => "Falkland Islands (Malvinas)", 
"fm" => "Micronesia", 
"fo" => "Faroe Islands", 
"fr" => "France", 
"fx" => "France (Metropolitan)", 
"ga" => "Gabon", 
"gb" => "Great Britain (UK)", 
"gd" => "Grenada", 
"ge" => "Georgia", 
"gf" => "French Guiana", 
"gh" => "Ghana", 
"gi" => "Gibraltar", 
"gl" => "Greenland", 
"gm" => "Gambia", 
"gn" => "Guinea", 
"gp" => "Guadeloupe", 
"gq" => "Equatorial Guinea", 
"gr" => "Greece", 
"gs" => "S. Georgia and S. Sandwich Islands", 
"gt" => "Guatemala", 
"gu" => "Guam", 
"gw" => "Guinea-Bissau", 
"gy" => "Guyana", 
"hk" => "Hong Kong", 
"hm" => "Heard and McDonald Islands", 
"hn" => "Honduras", 
"hr" => "Croatia (Hrvatska)", 
"ht" => "Haiti", 
"hu" => "Hungary", 
"id" => "Indonesia", 
"ie" => "Ireland", 
"il" => "Israel", 
"in" => "India", 
"io" => "British Indian Ocean Territory", 
"iq" => "Iraq", 
"ir" => "Iran", 
"is" => "Iceland", 
"it" => "Italy", 
"jm" => "Jamaica", 
"jo" => "Jordan", 
"jp" => "Japan", 
"ke" => "Kenya", 
"kg" => "Kyrgyzstan", 
"kh" => "Cambodia", 
"ki" => "Kiribati", 
"km" => "Comoros", 
"kn" => "Saint Kitts and Nevis", 
"kp" => "North Korea", 
"kr" => "South Korea", 
"kw" => "Kuwait", 
"ky" => "Cayman Islands", 
"kz" => "Kazakhstan", 
"la" => "Laos", 
"lb" => "Lebanon", 
"lc" => "Saint Lucia", 
"li" => "Liechtenstein", 
"lk" => "Sri Lanka", 
"lr" => "Liberia", 
"ls" => "Lesotho", 
"lt" => "Lithuania", 
"lu" => "Luxembourg", 
"lv" => "Latvia", 
"ly" => "Libya", 
"ma" => "Morocco", 
"mc" => "Monaco", 
"md" => "Moldova", 
"mg" => "Madagascar", 
"mh" => "Marshall Islands", 
"mk" => "Macedonia", 
"ml" => "Mali", 
"mm" => "Myanmar", 
"mn" => "Mongolia", 
"mo" => "Macau", 
"mp" => "Northern Mariana Islands", 
"mq" => "Martinique", 
"mr" => "Mauritania", 
"ms" => "Montserrat", 
"mt" => "Malta", 
"mu" => "Mauritius", 
"mv" => "Maldives", 
"mw" => "Malawi", 
"mx" => "Mexico", 
"my" => "Malaysia", 
"mz" => "Mozambique", 
"na" => "Namibia", 
"nc" => "New Caledonia", 
"ne" => "Niger", 
"nf" => "Norfolk Island", 
"ng" => "Nigeria", 
"ni" => "Nicaragua", 
"nl" => "Netherlands", 
"no" => "Norway", 
"np" => "Nepal", 
"nr" => "Nauru", 
"nt" => "Neutral Zone", 
"nu" => "Niue", 
"nz" => "New Zealand (Aotearoa)", 
"om" => "Oman", 
"pa" => "Panama", 
"pe" => "Peru", 
"pf" => "French Polynesia", 
"pg" => "Papua New Guinea", 
"ph" => "Philippines", 
"pk" => "Pakistan", 
"pl" => "Poland", 
"pm" => "St. Pierre and Miquelon", 
"pn" => "Pitcairn", 
"pr" => "Puerto Rico", 
"pt" => "Portugal", 
"pw" => "Palau", 
"py" => "Paraguay", 
"qa" => "Qatar", 
"re" => "Reunion", 
"ro" => "Romania", 
"ru" => "Russian Federation", 
"rw" => "Rwanda", 
"sa" => "Saudi Arabia", 
"sb" => "Solomon Islands", 
"sc" => "Seychelles", 
"sd" => "Sudan", 
"se" => "Sweden", 
"sg" => "Singapore", 
"sh" => "St. Helena", 
"si" => "Slovenia", 
"sj" => "Svalbard and Jan Mayen Islands", 
"sk" => "Slovak Republic", 
"sl" => "Sierra Leone", 
"sm" => "San Marino", 
"sn" => "Senegal", 
"so" => "Somalia", 
"sr" => "Suriname", 
"st" => "Sao Tome and Principe", 
"su" => "USSR (Former)", 
"sv" => "El Salvador", 
"sy" => "Syria", 
"sz" => "Swaziland", 
"tc" => "Turks and Caicos Islands", 
"td" => "Chad", 
"tf" => "French Southern Territories", 
"tg" => "Togo", 
"th" => "Thailand", 
"tj" => "Tajikistan", 
"tk" => "Tokelau", 
"tm" => "Turkmenistan", 
"tn" => "Tunisia", 
"to" => "Tonga", 
"tp" => "East Timor", 
"tr" => "Turkey", 
"tt" => "Trinidad and Tobago", 
"tv" => "Tuvalu", 
"tw" => "Taiwan", 
"tz" => "Tanzania", 
"ua" => "Ukraine", 
"ug" => "Uganda", 
"uk" => "United Kingdom", 
"um" => "US Minor Outlying Islands", 
"us" => "United States", 
"uy" => "Uruguay", 
"uz" => "Uzbekistan", 
"va" => "Vatican City State (Holy See)", 
"vc" => "Saint Vincent and the Grenadines", 
"ve" => "Venezuela", 
"vg" => "Virgin Islands (British)", 
"vi" => "Virgin Islands (US)", 
"vn" => "Vietnam", 
"vu" => "Vanuatu", 
"wf" => "Wallis and Futuna Islands", 
"ws" => "Samoa", 
"ye" => "Yemen", 
"yt" => "Mayotte", 
"yu" => "Yugoslavia", 
"za" => "South Africa", 
"zm" => "Zambia", 
"zr" => "Zaire", 
"zw" => "Zimbabwe", 
"com" => "US Commercial", 
"edu" => "US Educational", 
"gov" => "US Government", 
"int" => "International", 
"mil" => "US Military", 
"net" => "Network", 
"org" => "Non-Profit Organization", 
"arpa" => "Old-Style Arpanet", 
"nato" => "NATO Field" 
); 

function ccode($ip) 
{ 
	global $ccodes; 
	$remotehost = gethostbyaddr($ip); 
	if ($remotehost == $ip) 
	{ 
		return "Unknown"; 
	} 
	$hostsplit = explode(".", $remotehost); 
	foreach($hostsplit as $hostpiece) 
	{ 
		$ext = $hostpiece; 
	} 
	$land = $ccodes[$ext]; 
	if ($land == "") 
	{ 
		$land = "Unknown"; 
	} 
	
	return $land; 
} 

//voorbeeld: 
//$land = ccode($REMOTE_ADDR); 
?>