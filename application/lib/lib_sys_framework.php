<?php


/**
 * In this library exist only framework-itself related functions
 * this library contains the functions that are typically framework related
 * such as displaying error messages, logs and tracepoints
 *
 *
 * IMPORTANT:
 * -This library is language independant, so don't use language specific element
 * -This library uses the variables of the config file!
 *
 * 9 april 2019: rendertemplate() checkt op aanwezigheid file
 * 25 april 2019: installFramework, uninstallFramework, updateFramework toegevoegd
 * 25 aprul 2019: -lib_framework meer functions als error_log() uit CMS3 toegevoegd
 * 25 aprul 2019: lib_framework -> lib_system
 * 27 april 2019: logThis() removed, use error_log() instead
 * 27 april 2019: logThisDev() renamed -> logDev()
 * 8 mei 2019: uninstallFramework() system models uninstall reversed
 * 25 jun 2021: lib_sys_framework:  -
 * 15 apr 2024: lib_sys_framework: logError() geeft een div (rode achtergrond) met error info  in debug mode
 * 3 jan 2025: lib_sys_framework: function getPathConfigWebsite() added
 * 31 jan 2025: lib_sys_framework: autoLoaderVendor() added
 * 14 aug 2025: lib_sys_framework: authRes() added
 * 15 aug 2025: lib_sys_framework: speed improve: auth() en authRes() doen nu 1 check ipv 2
 * 9 sept 2025: lib_sys_framework: createIndexFile404InDir() toegevoegd
 * 
 * @author Dennis Renirie
 */


//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_file.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');


$bDisableCustomErrorHandlerFramework = false; //disables custom error handler when true. functions like imagecreatefromjpeg() always produce an error which you can't suppress. Thus variable helps us disable the error message


/**
 * the new autoloader based on the PSR-0 standard using namespaces representing directory structure
 * 
 * CLASSES opbouw voor system:      dr\classes\[subdir](\subdir)\classname.php
 * CLASSES opbouw voor modules:     dr\modules\[modulename](\subdir)\classname.php
 * 
 * EXAMPLE:
 * $objYrmp = new dr\modules\Mod_Sys_Localisation\TTemp();
 * $objYrmp->test();
 * 
 * @param string $sAutoLoaderClassPath 
 * @return boolean false = not found, true = found
 */
function autoLoaderFramework($sAutoLoaderClassPath)
{

    $sInludeFilePath = ''; //path of file to include
    $iStartDir = 0; //start looking in directory no: 0 = 0 of 0/1/2/3/4 (dr\classes\subdir\classname)
    $sModuleName = '';
    $arrDirs = array();
    $iCountDirs = 0;
    
    
    $arrDirs = explode('\\', $sAutoLoaderClassPath); //performance test show that explode is faster than substr() by a ratio 1 to 10
    $iCountDirs = count($arrDirs);
    if ($iCountDirs == 1)
    {		
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'autoloader fail for "'.$sAutoLoaderClassPath.'": no namespace specified. Therefore dont know wich path to look for the class. See naming conventions in autoLoader() in lib_system');
        return false;
    }


    //look for system classes or module? SYSTEM
    if ($arrDirs[1] == 'classes')
    {
        $iStartDir = 2; //start looking in directory no: 2 of 0/1/2/3/4 (dr\classes\[subdir]\[classname])

        $sInludeFilePath = APP_PATH_CMS_CLASSES.DIRECTORY_SEPARATOR;       
 
    }
    elseif($arrDirs[1] == 'modules') //zo weinig mogelijk code proberen uit te voeren, dus alleen naar de modules kijken als STRICT noodzakelijk is
    {
        //look for system classes or modules? MODULES
        $sModuleName = $arrDirs[2];
        
        $iStartDir = 3; //start looking in directory no: 2 of 0/1/2/3/4 (dr\modules\[module]\[classname])

        // $sInludeFilePath = APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR;            
        $sInludeFilePath = APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR;
    }

    
    //if it is the default value, detection didn't work
    if ($iStartDir == 0)
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'autoloader fail for "'.$sAutoLoaderClassPath.'": for naming conventions see autoLoader() in lib_system.php');
        return false;
    }
    else
    {
        //stitch path back together --> performance test show that a string-concatenation loop is faster than using substring for therestoftheclassname AND using strtr for replacing the '\' TOGETHER!. Also implode() is not faster (because it needs to start at a certain item, then we need to slice() the array, which makes it slower than string concatanation)
        for ($iArrIndex = $iStartDir; $iArrIndex < $iCountDirs; $iArrIndex++)
        {	
            $sInludeFilePath.= $arrDirs[$iArrIndex];
            if (($iArrIndex < $iCountDirs -1))//overal dir separator behalve de laatste
                    $sInludeFilePath.= DIRECTORY_SEPARATOR;
        }   
        
        
        $sInludeFilePath.= '.php';



        if (APP_DEBUGMODE) //extra check in debugging mode for detecting problems
        {
            if(!is_file($sInludeFilePath))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'autoloader: file not found ('.$sInludeFilePath.'). Looking for class "'.$sAutoLoaderClassPath.'". Paths maybe misconfigured in config file. This also maybe a typo but more likely a namespace-alias issue: you probably used a class in a file that you didnt alias by using i.e. "use dr\framework\classes\db\someclass"');
            }
        }

        require $sInludeFilePath;    
    }
       
    return true;
}

/**
 * handles the autoloaders for 3rd party components in "vendor" directory
 * 
 * NOTE:
 * Works only when the component abides by the rules that the classpath matches directory structure,
 * so Mike42\Escpos\PrintConnectors\FilePrintConnector matches [vendordir]/Mike42/Escpos/PrintConnectors/FilePrintConnector
 */
function autoLoaderVendor($sAutoLoaderClassPath)
{
    include_once(APP_PATH_VENDOR.DIRECTORY_SEPARATOR.$sAutoLoaderClassPath.'.php');
}

/**
 * custom error handler, passes flow over the exception logger with new ErrorException.
 * 
 * @param type $errno
 * @param type $errstr
 * @param type $errfile
 * @param type $errline
 * @return boolean
 */
function customErrorHandler( $iErrorNumber, $sErrorMessage, $sFile, $iLine, $context = null )
{
    global $bDisableCustomErrorHandlerFramework;
    if ($bDisableCustomErrorHandlerFramework == true)
        return true;//disables the native php error

    //GIVE ERROR IN JSON FORMAT
    if (isset($_GET[ACTION_VARIABLE_RENDERVIEW]))
    {
        if ($_GET[ACTION_VARIABLE_RENDERVIEW] == ACTION_VALUE_RENDERVIEW_JSONDATA)
        {
            header(JSONAK_RESPONSE_HEADER);

            if (APP_DEBUGMODE)
            {
                $arrJSON = array(
                        JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_ERRORCODE_UNKNOWN,
                        JSONAK_RESPONSE_MESSAGE => "Error occured: '$sErrorMessage'.<br>In $sFile on line $iLine",
                );
            }
            else
            {
                $arrJSON = array(
                        JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_ERRORCODE_UNKNOWN,
                        JSONAK_RESPONSE_MESSAGE => "Error occured, see logfiles for details",
                );
            }

            echo json_encode($arrJSON);
        }
    }
    else //GIVE ERROR IN HTML-format
    {
        if (APP_DEBUGMODE)
        {

            print "<div style='text-align: center;'>";
            print "<h2 style='color: rgb(190, 50, 50);'>Error Occured:</h2>";
            print "<table style='display: inline-block;'>";
            print "<tr style='background-color:rgb(230,230,230);color:black;'><th style='width: 80px;'>Number</th><td>" . $iErrorNumber . "</td></tr>";
            print "<tr style='background-color:rgb(240,240,240);color:black;'><th>Message</th><td>$sErrorMessage</td></tr>";
            print "<tr style='background-color:rgb(230,230,230);color:black;'><th>File</th><td>$sFile</td></tr>";
            print "<tr style='background-color:rgb(240,240,240);color:black;'><th>Line</th><td>$iLine</td></tr>";
            print "<tr style='background-color:rgb(240,240,240);color:black;'><th>Call trace</th><td>";
            $arrTrace = debug_backtrace();
            $iSteps = 0;
            foreach ($arrTrace as $arrStep)
            {
                if ($iSteps > 0) //skip the first, because it is the error handler itself
                {
                    print "#".($iSteps-1)." - <span style='color:rgb(209, 73, 0);'>".$arrStep["function"]."(</span>";
                    if (isset($arrStep["args"]))
                    {
                        $iIndex = 0;
                        foreach($arrStep["args"] as $mArgument)
                        {
                            if ($iIndex > 0)
                                print ", ";

                            if (is_string($mArgument) || is_bool($mArgument) || is_int($mArgument))
                                print $mArgument;
                            elseif (is_object($mArgument))
                                print "[".get_class($mArgument)." object]";
                            else 
                                print "[".gettype($mArgument)."] ";
  
                            $iIndex++;
                        }
                    }
                    if (isset($arrStep["file"]) && $arrStep["line"])
                        print "<span style='color:rgb(209, 73, 0);'>)</span> in <span style='color:rgb(3, 120, 209);'>".$arrStep["file"]."</span> on <span style='color:rgb(49, 133, 0);'>line <b>".$arrStep["line"]."</b></span><br>";
                }
                ++$iSteps;

            }
            print "</td></tr>";
            print "</table></div>";

        }
        else
        {
            print "<div style='text-align: center;'>";
            print "<h2 style='color: rgb(190, 50, 50);'>Error occured</h2>";
            print "Error messages are suppressed in this mode for safety reasons, see logfiles for details.<br>";
            print "Sorry for the inconvenience.<br>";
            print "</div>";
        }
    }

    /* Don't execute PHP internal error handler */
    return true;
}

/**
 * disables custom error handler of framework
 * 
 * its ugly, but regretfully necessary because some functions like imagecreatefromjpeg() always throw an error, 
 * wich you can't suppress.
 * This function lets you do that
 */
function disableCustomErrorHandler()
{
    global $bDisableCustomErrorHandlerFramework;
    $bDisableCustomErrorHandlerFramework = true;
}

/**
 * enables custom error handler of framework
 * 
 * its ugly, but regretfully necessary because some functions like imagecreatefromjpeg() always throw an error, 
 * wich you can't suppress.
 * This function lets you do that
 */
function enableCustomErrorHandler($bEnable)
{
    global $bDisableCustomErrorHandlerFramework;
    $bDisableCustomErrorHandlerFramework = $bEnable;
}

/**
 * Uncaught exception handler.
 */
function customExceptionHandler(  $objException )
{  
    //GIVE ERROR IN JSON FORMAT
    if (isset($_GET[ACTION_VARIABLE_RENDERVIEW]))
    {
        if ($_GET[ACTION_VARIABLE_RENDERVIEW] == ACTION_VALUE_RENDERVIEW_JSONDATA)
        {
            header(JSONAK_RESPONSE_HEADER);

            if (APP_DEBUGMODE)
            {
                $arrJSON = array(
                        JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_ERRORCODE_UNKNOWN,
                        JSONAK_RESPONSE_MESSAGE => 'Error occured: '.$objException->getMessage().'<br>In '.$objException->getFile().' on line '.$objException->getLine(),
                );
            }
            else
            {
                $arrJSON = array(
                        JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_ERRORCODE_UNKNOWN,
                        JSONAK_RESPONSE_MESSAGE => "Error occured, see logfiles for details",
                );
            }

            echo json_encode($arrJSON);
        }
    }
    else //GIVE ERROR IN HTML-format
    {    
        if (APP_DEBUGMODE)
        {
            print "<div style='text-align: center;'>";
            print "<h2 style='color: rgb(190, 50, 50);'>Exception Occured:</h2>";
            print "<table style='display: inline-block;'>";
            print "<tr style='background-color:rgb(230,230,230);color:black;'><th style='width: 80px;'>Type</th><td>" . get_class( $objException ) . "</td></tr>";
            print "<tr style='background-color:rgb(240,240,240);color:black;'><th>Message</th><td>{$objException->getMessage()}</td></tr>";
            print "<tr style='background-color:rgb(230,230,230);color:black;'><th>File</th><td>{$objException->getFile()}</td></tr>";
            print "<tr style='background-color:rgb(240,240,240);color:black;'><th>Line</th><td>{$objException->getLine()}</td></tr>";
            print "<tr style='background-color:rgb(240,240,230);color:black;'><th>Call trace</th><td>".str_replace("\n", "<br>", $objException->getTraceAsString())."</td></tr>";
            print "</table></div>";
        }
        else
        {
            print "<div style='text-align: center;'>";
            print "<h2 style='color: rgb(190, 50, 50);'>Exception occured</h2>";
            print "Error messages are suppressed in this mode for safety reasons, see logfiles for details.<br>";
            print "Sorry for the inconvenience.<br>";
            print "</div>";
        }
    }
        
    
    exit();
}

/**
 * Checks for a fatal error, work around for set_error_handler not working on fatal errors.
 */
function customShutdownOnError()
{
    $arrError = error_get_last();

    if ($arrError)
    {
        if ( $arrError["type"] == E_ERROR )
            customErrorHandler( $arrError["type"], $arrError["message"], $arrError["file"], $arrError["line"] );
    }
}

/**
 * this function displays an error when in the development environment, else not
 * 
 * @deprecated use logError()
 * @param string $sErrorMessage 
 * @return null
 */
function error($sErrorMessage)
{
    logError('', $sErrorMessage);
}

/**
 * example $sOutput = renderTemplate('skin_stdtwocolumn.php', get_defined_vars());
 * 
 * @param string $sPathTemplateFile
 * @param array $arrTemplateVariables variables (and their values) that you can use in the templates
 * @return type
 */
function renderTemplate($sPathTemplateFile, $arrTemplateVariables)
{
    if (APP_DEBUGMODE)
    {
        if (!is_file($sPathTemplateFile))
        {   
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'template '.$sPathTemplateFile.' not found');
            return false;        
        }
    }
    
    //set all vars
    $arrKeys = array();
    $arrKeys = array_keys($arrTemplateVariables);
    foreach ($arrKeys as $sKey)
    {
        $$sKey = $arrTemplateVariables[$sKey];
    }

    //get template content
    $sOutput = '';
    ob_start();
    include $sPathTemplateFile;
    $sOutput = ob_get_contents();
    ob_end_clean();  

    return $sOutput;
}



 /**
  * weergeven van waarden voor debug doeleinden
  * 
  * Waarom deze functie gebruiken  ?
  * Omdat je in een development omgeving graag waarden naar het scherm geschreven
  * wil hebben met bijvoorbeeld vardump, maar je vergeet vaak waar je deze
  * statements in de code geplaatst hebt.
  * Gebruik daarom dit statement, omdat je dan door alleen deze procedure aan te passen
  * in 1 keer alle debug waardes niet meer laat weergeven
  *
  * @param mixed $mDumpValue value to dump to the screen
  * @param string $sTracepoint text to identify the vardump by (is printed to screen)
  */
 function vardump($mDumpValue, $sTracepoint = '')
 {
     if (APP_DEBUGMODE)//alleen weergeven in development environment
     {
//      	for ($iLineCounter = 0; $iLineCounter < 100; ++$iLineCounter)     		
//      		echo '<br>';
		if ($sTracepoint != '')
			tracepoint($sTracepoint);

        echo '<div><font color="0000FF"><pre>called vardump()'."\n";
        var_dump($mDumpValue);
        echo '</pre></font></div>';
        
        //tracepoint($sTracepoint);
     }
 }
 
 /**
  * call vardump() and call die()
  * @param string $sDumpValue
  */
 function vardumpdie($sDumpValue, $sTracepoint = '')
 {
     vardump($sDumpValue, $sTracepoint);
     die();
 }

 /**
  * dump a variable to the screen. only display, not trace the whole variable like vardump
  *
  * @param string $sDumpValue
  */
 function dump($sDumpValue)
 {
     if (APP_DEBUGMODE) //alleen weergeven in development environment
        echo '<font color="0000FF"><pre>"'.$sDumpValue.'"</pre></font>';
 }

 
 /**
  * wrapper for logDebug()
  * logDev() is DEPRECATED
  *
  * exact dezelfde functie als error_log(), maar checkt of het in de development omgeving is
  * (dit omdat deze logs vaak per ongeluk in de code blijven staan)
  * 
  *
  * @param string $sLogValue
  * @return boolean false als in development omgeving en schrijferror, anders true 
  * @deprecated use logDebug instead
  */
 function logDev($sLogValue)
 {
     //pas bij het lezen van de config file wordt APP_DEBUGMODE geset. 
     //Maar als er gelogd wordt VOOR het lezen van de config file, wordt er aangenomen
     //dat er gelogd moet worden
     
     $iErrorReportingLevel = error_reporting(); //je krijgt iedere keer een foutmelding als je een ongedefinieerde constante gebruikt, daarom error level even uitschakelen
     error_reporting(0);
     
     if (defined('APP_DEBUGMODE')) 
     {
        if (APP_DEBUGMODE) //only log in dev env
        {
            error_reporting($iErrorReportingLevel);
            logDebug('', $sLogValue);
        }
        else
        {
            error_reporting($iErrorReportingLevel);
            return true;
        }
     }
     else
     {
         error_reporting($iErrorReportingLevel);
         logDebug('', $sLogValue);
     }

 }
 
 /**
  * log SQL queries
  * NOTE: success logs are only created in development environment for speed reasons, otherwise only errors are registered

  * The logfiles are created in a CSV format with ; as a separator (deliberately not choose commas, because they exist a lot in sql queries)
  *
  * @param string $sSource the source of the log (class, file whatever). __CLASS__.': '.__FUNCTION__.': '.__LINE__ would be a good source
  * @param string $sQuery the SQL to log
  * @param string $sQuery state of the execution of the query (success or failed)
  */
function logSQL($sSource, $sQuery, $sState = 'success')
{
    $bLog = true;

    //only log all SQL statements in development-environment, otherwise only log the error statements
    if (($sState == 'success') && (!APP_DEBUGMODE))
        $bLog = false;

    if ($bLog) 
    {
        global $sLogDirToday;

        $fhLogfile = fopen($sLogDirToday.DIRECTORY_SEPARATOR.'sqllog_'.date('Y-m-d').'.csv', 'a'); 
        fwrite($fhLogfile, date('Y-m-d H:i').';'.$_SERVER['REMOTE_ADDR'].';'.session_id().';'.$sSource.';'.$sState.';'.$sQuery."\n");
        fclose($fhLogfile);
    }
}


 /**
  * log debug 
  * NOTE: logs are only created in development environment for speed reasons
  *
  * The logfiles are created in a CSV format with ; as a separator (deliberately not choose commas, because they exist a lot in sql queries)
  *
  * @param string $sSource the source of the log (class, file whatever). __CLASS__.': '.__FUNCTION__.': '.__LINE__ would be a good source
  * @param string $sValue the value to log
  */
function logDebug($sSource, $sValue, $bShowOnScreen = false)
{
    if (APP_DEBUGMODE) 
    {
        global $sLogDirToday;

        if ($bShowOnScreen)
            echo '<div style="z-index:99999; position:relative; top:0px; left:0px; background-color:blue; color:white;">'.htmlspecialchars($sValue).'</div>';
    
        $fhLogfile = fopen($sLogDirToday.DIRECTORY_SEPARATOR.'debuglog_'.date('Y-m-d').'.csv', 'a'); 
        fwrite($fhLogfile, date('Y-m-d H:i').';'.$_SERVER['REMOTE_ADDR'].';'.session_id().';'.$sSource.';'.$sValue."\n");
        fclose($fhLogfile);
    }
}

 /**
  * log access
  * The logfiles are created in a CSV format with ; as a separator (deliberately not choose commas, because they exist a lot in sql queries)
  *
  * @param string $sSource the source of the log (class, file whatever). __CLASS__.': '.__FUNCTION__.': '.__LINE__ would be a good source
  * @param string $sValue the value to log
  * @param string $sUser the user to log (leave empty when no user information is available)
  */
  function logAccess($sSource, $sValue, $sUser = '?')
  {
      global $sLogDirToday;

      $fhLogfile = fopen($sLogDirToday.DIRECTORY_SEPARATOR.'accesslog_'.date('Y-m-d').'.csv', 'a'); 
      fwrite($fhLogfile, date('Y-m-d H:i').';'.$_SERVER['REMOTE_ADDR'].';'.session_id().';'.$sUser.';'.$sSource.';'.$sValue."\n");
      fclose($fhLogfile);
  }

  /**
   * log errors
   * 
  * @param string $sSource the source of the log (class, file whatever). __CLASS__.': '.__FUNCTION__.': '.__LINE__ would be a good source
  * @param string $sValue the value to log
  * @param string $sUser the user to log (leave empty when no user information is available)
   */
  function logError($sSource, $sValue, $sUser = '?')
  {
    if (APP_DEBUGMODE)
    {
        // echo '<div style="z-index:99999; position:relative; top:0px; left:0px; background-color:red; color:white;">'.htmlspecialchars($sValue).'</div>';
    }    
    error_log($sSource.'; '.$_SERVER['REMOTE_ADDR'].'; user:'.$sUser.' -->'.$sValue);
  }

 /**
  * log cronjob
  * The logfiles are created in a CSV format with ; as a separator (deliberately not choose commas, because they exist a lot in sql queries)
  *
  * @param string $sSource the source of the log (class, file whatever). __CLASS__.': '.__FUNCTION__.': '.__LINE__ would be a good source
  * @param string $sValue the value to log
  */
  function logCronjob($sSource, $sValue)
  {
      global $sLogDirToday;
      
      $fhLogfile = fopen($sLogDirToday.DIRECTORY_SEPARATOR.'cronjoblog_'.date('Y-m-d').'.csv', 'a'); 
      fwrite($fhLogfile, date('Y-m-d H:i').';'.$_SERVER['REMOTE_ADDR'].';'.session_id().';'.$sSource.';'.$sValue."\n");
      fclose($fhLogfile);
  }  

 /**
  * in de code wil je soms kijken of je wel op een bepaalde plek in je code
  * komt.
  * Omdat deze items vaak per ongeluk blijven staan na debugging, deze functie.
  * Je kunt dus veilig tracepoints plaatsen in je code. bij publiceren wijzig
  * je deze functie (bijvoorbeeld middels een variabele debug in een config bestand)
  *
  * @param <type> $sRecognisableText
  */
 function tracepoint($sRecognizableTracepointText = 'TRACEPOINT !!!!')
 {
     if (APP_DEBUGMODE) //alleen weergeven in development environment
        echo '<br><font color="0000FF">tracepoint : '.$sRecognizableTracepointText.'</font><br>';
 }


/**
 * install the database tables of the framework and all the modules
 * this function calls also updateFramework()
 * 
 * @return boolean install succesful?
 */ 
/*
 function installFramework()
 {
    set_time_limit(60*10);//10 minute timeout
    
    include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');
    
    $arrPreviousDependenciesModelClasses = array(); 
    echo '<h2>Install progress</h2>';
    echo '<br>';
    echo 'start installation ...<br>';
    error_log('start installation ...');

    echo 'checking prequisites ... <br>';
    error_log('checking prequisites ... <br>');    

    //64 bit integer check
    echo 'Checking for 64 bit integer. (PHP_INT_MAX = '.PHP_INT_MAX.') ';    
    if (PHP_INT_MAX == 2147483647) //if 32 bit integer
    {
        echo '<br>WARNING: This PHP version uses 32 bit integers instead of 64 bit. We assume 64 bits integers, which can lead to unexpected behavior when 32 bit values are exceeded. Installation will continue. [FAILED]<br>';
        error_log('WARNING: This PHP version uses 32 bit integers instead of 64 bit. Installation will continue. PHP_INT_MAX == '.PHP_INT_MAX);    
    }
    else
        echo '[SUCCESS]<br>';

    //php mods
    $arrPrequisitesNotLoaded = getPHPExtensionsNotLoaded();
    if ($arrPrequisitesNotLoaded)
    {
        echo '<br>';
        echo 'the following PHP modules are not loaded:<br>';
        foreach ($arrPrequisitesNotLoaded as $sPHPMod)
        {
            echo '- "'.$sPHPMod.'" [NOT LOADED]<br>';
        }
        echo 'Installation will continue, but the system might not work properly<br>';
    }
    // else
        // echo '[SUCCESS]<br>';      

    //==== block install 
    //only install whith confirmation parameter
    if (isset($_GET['confirm']))
    {
        if (APP_INSTALLER_PASSWORD === '') //technically should never occur, but it's an extra insurance
            die('install password not set in config file. [FAILED]');
        
        if ($_GET['confirm'] != APP_INSTALLER_PASSWORD) 
        {
            echo 'sorry, can\'t install, install password not correct. You can configure it in config file<br>';        
            error_log('sorry, can\'t install, wrong confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

            return false;
        }
    }
    else
    {
        echo '<b>ABORTED INSTALLATION!!!!<br>sorry, can\'t install, need confirmation password. You can configure it in config file</b><br>';        
        error_log('sorry, can\'t install, need confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

        return false;        
    }   

    //==== create database
        // echo 'creating database ... ';
        // error_log('creating database ... ');

        // global $objDBConnection;
        // $objPrepStat = $objDBConnection->getPreparedStatement();
        // if (!$objPrepStat->databaseExists(APP_DB_DATABASE))
        //     $objPrepStat->createDatabase(APP_DB_DATABASE);

    //==== 1. install systemtables
        echo 'installing system database tables ... ';
        error_log('installing system database tables ... ');
    
        $arrSystemDBTables = getSystemModelsInstantiated();
        
        foreach ($arrSystemDBTables as $objSystemObject)
        {
                if (!$objSystemObject->install(null))
                {
                    echo '[FAILED]<br>';                    
                    return false;
                }
        }        

        echo '[SUCCESS]<br>';
        
    //==== 2. install modules
        echo 'installing modules ... <br>';
        error_log('installing modules  ... ');
        
        $arrModuleFolders = getModuleFolders();
        for ($iModIndex = 0; $iModIndex < count($arrModuleFolders); $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            echo 'installing module: '.$sCurrMod.' ...<br>';
            error_log('installing module '.$sCurrMod.' ...');
            
            $objCurrMod = new $sCurrMod;
            if (!$objCurrMod->installModule())
            {
                echo 'install failed for module: '.$sCurrMod.'!<br>';
                error_log('install failed for module '.$sCurrMod);
                return false;
            }            
        }
         
        echo 'Install modules [SUCCESS]<br>';

    

    //==== synchronizing database table versions happened already in updateFramework()
        error_log('finished installation');         
        echo 'finished installation<br>';

    //==== running updates also
        updateFramework();
        
 }
*/
 /**
  * after installing we want to propagate data into database tables for convenience.
  * most important data and defaults are already inserted during installation
  * propagating extra data like all languages is convenient
  */
 /*
 function propagateDataFramework()
 {
    //==== propagate data system tables
        echo 'propagating system database tables ... ';
        error_log('propagating system database tables ... ');

        $arrSystemDBTables = getSystemModelsInstantiated();
        
        foreach ($arrSystemDBTables as $objSystemObject)
        {
                if (!$objSystemObject->propagateData())
                {
                    echo '[FAILED]<br>';                    
                    return false;
                }
        }        

        echo '[SUCCESS]<br>';

    //==== propagate data modules
        echo 'propagating data modules ... <br>';
        error_log('propagating data modules  ... ');
        
        $arrModuleFolders = getModuleFolders();
        for ($iModIndex = 0; $iModIndex < count($arrModuleFolders); $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            echo 'propagate data module: '.$sCurrMod.' ...<br>';
            error_log('propagate data module '.$sCurrMod.' ...');
            
            $objCurrMod = new $sCurrMod;
            if (!$objCurrMod->propagateDataModule())
            {
                echo 'data propagation failed for module: '.$sCurrMod.'!<br>';
                error_log('data propagation failed for module '.$sCurrMod);
                return false;
            }            
        }
        
        echo 'data propagation modules [SUCCESS]<br>';
 }
 */
 
 /**
 * update the database tables of the framework and all the modules
 * this function will also be called from installFramework()
 * 
 * @return boolean update succesful?
 */ 
/*
 function updateFramework()
 {
    include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');

    $arrPreviousDependenciesModelClasses = array();
    echo 'start update ...<br>';
    error_log('start update ...');
     
     
    //==== block update 
    //only update whith confirmation parameter
    if (isset($_GET['confirm']))
    {
        if (APP_INSTALLER_PASSWORD === '') //technically should never occur, but it's an extra insurance
            die('install password not set in config file. [FAILED]');
        
        if ($_GET['confirm'] != APP_INSTALLER_PASSWORD) 
        {
            echo 'sorry, can\'t update, install password not correct. Add correct password to url with ?confirm=[installpassword]. You can find the password in config file<br>';        
            error_log('sorry, can\'t update, wrong confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

            return false;
        }
    }
    else
    {
        echo 'sorry, can\'t update, need confirmation password. Add correct password to url with ?confirm=[installpassword]. You can find the password in config file.<br>';        
        error_log('sorry, can\'t update, need confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

        return false;        
    }        


    //=== update versions
        echo 'table version system: running synchronisation ... ';
        error_log('table version system: running synchronisation ...');
        
        $objTableVersionsFromDBModels = new dr\classes\models\TSysTableVersions();
        if ($objTableVersionsFromDBModels->synchronizeTablesDB())
        { 
           echo '[SUCCESS]<br>';
            
        }
        else
        {
           echo '[FAILED]<br>';
           error_log('synchronizeTables() failed!');   
           
           //it failed but we continue to run the script
        }


    //==== systeemtabellen update 
        echo 'updating system database tables ... ';
        error_log('updating system database tables ... ');
        
        $arrSystemDBTables = getSystemModelsInstantiated();
        foreach ($arrSystemDBTables as $objSystemObject) //tmodel
        {
                if (!$objSystemObject->refactorDB($arrPreviousDependenciesModelClasses, $objTableVersionsFromDBModels))
                {
                    echo '[FAILED]<br>';                    
                    return false;
                }
        }             

        echo '[SUCCESS]<br>';         
       
        
    //==== nu alle modules updaten  
        echo 'updating module database tables ... <br>';
        error_log('updating module database tables ... ');   
        
        
        $arrModuleFolders = getModuleFolders();
        for ($iModIndex = 0; $iModIndex < count($arrModuleFolders); $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            echo 'updating database tables for module: '.$sCurrMod.' ...<br>';
            error_log('updating database tables for module '.$sCurrMod.' ...');
            
            $objCurrMod = new $sCurrMod;
            if (!$objCurrMod->updateModels($arrPreviousDependenciesModelClasses, $objTableVersionsFromDBModels))
            {
                echo '[FAILED]<br>';                    
                error_log('creating tables (TSysModel) failed for module '.$sCurrMod);
                return false;
            }     
            
        }
        
        echo '[SUCCESS]<br>';        
        

    //===== permissions
        echo 'updating permissions ... ';
        error_log('updating permissions ... ');   

        $objPermissions = new dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions();
        if (!$objPermissions->updatePermissions())
        {
            echo '[FAILED]<br>';                    
            error_log('updating permissions failed');
            return false;
        }
        unset($objPermissions);

        echo '[SUCCESS]<br>';        

    //==== settings
        echo 'updating settings ... ';
        error_log('updating settings ... ');   

        $objSettings = new dr\modules\Mod_Sys_Settings\models\TSysSettings();
        if (!$objSettings->updateSettingsDB())
        {
            echo '[FAILED]<br>';   
            error_log('updating settings failed');
            return false;
        }
        unset($objSettings);

        echo '[SUCCESS]<br>';   
    
    
    //==== versienummers opslaan in registratietabel      
        echo 'table version system: updating version numbers  ... ';
        error_log('table version system: updating version numbers ... ');
        
        if ($objTableVersionsFromDBModels->saveToDB())
        {
            echo '[SUCCESS]<br>';            
        }
        else
        {
            echo 'table version system: saveToDB() failed';
            error_log('[FAILED]<br>');   
            
            return false;
        }
        
        unset($objTableVersionsFromDBModels);

    

    //==== END
        error_log('finished update');        
        echo 'finished update<br>';

                
        return true;  
 }
 */
 /**
 * remove database tables from database
 * WARNING!!! THIS WILL REMOVE ALL DATA!!!!
 * 
 * @return boolean update succesful?
 */ 
/*
 function uninstallFramework()
 {
    set_time_limit(60*10);//10 minute timeout
    include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');

    $arrPreviousDependenciesModelClasses = array();
    echo '<h1>uninstall framework</h1>';
    echo 'start uninstall ...<br>';        
    error_log('start uninstall ...');
    
     
    //==== block deinstall 
    //only can install when in development environment
    //otherwise bad actors could run the script if they know the url
    //--> 23-4-2024 removed because you need to remove the whole "installer" directory after installation
    // if (!APP_DEBUGMODE) 
    // {
    //     echo 'sorry, can\'t uninstall, not sufficient privileges<br>'; //deliberately vague       
    //     error_log('blocked uninstall, because not in development environment');  
        
    //     return false;
    // }
    
   //==== block uninstall 
    //only uninstall whith confirmation parameter
    if (isset($_GET['confirm']))
    {
        if (APP_INSTALLER_PASSWORD === '') //technically should never occur, but it's an extra insurance
            die('install password not set in config file. [FAILED]');
        
        if ($_GET['confirm'] != APP_INSTALLER_PASSWORD) 
        {
            echo 'sorry, can\'t uninstall, install password not correct. Add correct password to url with ?confirm=[installpassword]. You can find the password in config file.<br>';        
            error_log('sorry, can\'t uninstall, wrong confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

            return false;
        }
    }
    else
    {
        echo 'sorry, can\'t uninstall, need confirmation password. Add correct password to url with ?confirm=[installpassword]. You can find the password in config file.<br>';        
        error_log('sorry, can\'t uninstall, need confirmation password in parameter: ?confirm=[installpassword]. You can configure it in config file');  

        return false;        
    }   
     
    //=== first make a backup
//        echo 'making database backup in backup directory ...  ';
//        error_log('making database backup in backup directory  ...');   
//        makeBackupDatabase();
  
        
     //=== alle modules de-installeren
        echo 'removing modules ... <br>';
        error_log('removing modules ... ');   

        
        $arrModuleFolders = getModuleFolders();
        for ($iModIndex = 0; $iModIndex < count($arrModuleFolders); $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);
            echo 'removing module: '.$sCurrMod.' ...<br>';
            error_log('removing module '.$sCurrMod.' ...');
            
            $objCurrMod = new $sCurrMod;
            if (!$objCurrMod->uninstallModule(false))
            {
                error_log('removing tables (TSysModel) failed for module '.$sCurrMod);
                return false;
            }            
        }
         
        echo 'UnInstall modules [SUCCESS]<br>';        
    
        
 

        
        
    //==== als laatste pas systeemtabellen de-installeren
        echo 'removing system database tables ... ';
        error_log('removing system database tables ... ');
   
        $arrSystemDBTables = getSystemModelsInstantiated();
  
        //in reversed order, because of dependencies, somehow array_reverse($arrSystemDBTables) doesn't work
        $iMaxIndexSysTables = count($arrSystemDBTables) -1;//the index starts at 0, so the count is one too many
        $objModel = null;
        for ($iTableIndex = $iMaxIndexSysTables; $iTableIndex >= 0; $iTableIndex--) //for in reverse
        {
            $objModel = $arrSystemDBTables[$iTableIndex];
            if (!$objModel->uninstallDB(null))
            {
                echo '[FAILED]<br>';  
                
                error_log($objModel::getTable().' uninstall error  (TSysModel)');
                return false;

            }            
        }
        

        echo '[SUCCESS]<br>';  
        
        

    //==== END
        error_log('finished uninstall');        
        echo 'finished uninstall<br>';
        
    //==== NEXT    
    echo '<h2>What to do next?</h2>';
    echo '1. This installation removed the database, but didn\'t remove files.<br>';
    echo '2. You need to remove files in order to get rid of everything.<br>';
    echo '3. If you want to reinstall the database, you can <a href="'.APP_URL_CMS_INSTALLERSCRIPT.'">go here</a> to start the installer.<br>';
    echo '4. Bye bye!<br>';

	        
	return true;     
 }

*/
 
 
/**
 * translate global, applying to all websites (including cms)
 * 
 * 
 * if language content not found, it returns the $sOriginalText
 * @param string $sUniqueKey --> want a safe key? use getSafeTransKey() 
 * @param string $sDefaultEnglishTranslation
 */
function transg($sUniqueKey, $sDefaultEnglishTranslation = '', $sVariable1 = '', $sValue1 = '', $sVariable2 = '', $sValue2 = '', $sVariable3 = '', $sValue3 = '')
{
    global $objTranslationGlobal;
    
    if ($objTranslationGlobal)
    {        
        return $objTranslationGlobal->translate($sUniqueKey, $sDefaultEnglishTranslation, $sVariable1, $sValue1, $sVariable2, $sValue2, $sVariable3, $sValue3);
    }
    else
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'transg()-> $objTranslationGlobal is not set');           
    	if ($sDefaultEnglishTranslation == '')    		
       	 	return $sUniqueKey;
    	else
    		return $sDefaultEnglishTranslation;
    }  
}

/**
 * translate originaltext for WEBSITE
 * It uses the languagefiles per website
 * 
 * if language content not found, it returns the $sOriginalText
 * @param string $sWebsiteID --> needs to be file-safe, because is not checked against directory traversal because of speed
 * @param string $sUniqueKey --> want a safe key? use getSafeTransKey() 
 * @param string $sDefaultEnglishTranslation
 */
function transw($sWebsiteID, $sUniqueKey, $sDefaultEnglishTranslation = '', $sVariable1 = '', $sValue1 = '', $sVariable2 = '', $sValue2 = '', $sVariable3 = '', $sValue3 = '')
{
    global $arrTranslationsWebsites;
    global $objLocale;
    $objTempModTranslation = null; 

    if (isset($arrTranslationsWebsites))
    {        
        if (!array_key_exists($sWebsiteID, $arrTranslationsWebsites))//if key (=name of module) no exists make a new object
        {
            $objTempModTranslation = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            $objTempModTranslation->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.$sWebsiteID.'_translation_'.$objLocale->getLocale().'.csv');

            $arrTranslationsModules[$sWebsiteID] = $objTempModTranslation;
        }
        else
        {
            $objTempModTranslation = $arrTranslationsWebsites[$sWebsiteID];
        }
        
        return $objTempModTranslation->translate($sUniqueKey, $sDefaultEnglishTranslation, $sVariable1, $sValue1, $sVariable2, $sValue2, $sVariable3, $sValue3);        
    }
    else
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'transw()-> $arrTranslationsWebsites is not set');
    	if ($sDefaultEnglishTranslation == '')    		
       	 	return $sUniqueKey;
    	else
    		return $sDefaultEnglishTranslation;
    }  
}

/**
 * translate originaltext for modules in CMS
 * there is a translation file per module
 * (its annoying when you copy modules between sites that your whole translation is lost)
 * It uses the languagefiles per module
 *
 * 
 * @todo caching
 * 
 * if language content not found, it returns the $sOriginalText
 * @param string $sModuleName
 * @param string $sUniqueKey --> want a safe key? use getSafeTransKey()
 * @param string $sDefaultEnglishTranslation
 */
function transm($sModuleName, $sUniqueKey, $sDefaultEnglishTranslation = '', $sVariable1 = '', $sValue1 = '', $sVariable2 = '', $sValue2 = '', $sVariable3 = '', $sValue3 = '')
{
    global $arrTranslationsModules;
    global $objLocale;
    $objTempModTranslation = null; 

    if ($sModuleName == "") //should never occur
    {
        logError(__FILE__.__LINE__, 'transm() -> module name is empty for translation key: '.$sUniqueKey.' with translation "'.$sDefaultEnglishTranslation.'"');

        if (APP_DEBUGMODE)
        {
            echo 'transm() -> module name is empty for translation key: '.$sUniqueKey.' with translation "'.$sDefaultEnglishTranslation.'"';
        }
    }

    if (isset($arrTranslationsModules))
    {
        
        if (!array_key_exists($sModuleName, $arrTranslationsModules))//if key (=name of module) no exists make a new object
        {
            $objTempModTranslation = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            $objTempModTranslation->setFileName(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$sModuleName.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$objLocale->getLocale().'.csv');

            $arrTranslationsModules[$sModuleName] = $objTempModTranslation;
        }
        else
        {
            $objTempModTranslation = $arrTranslationsModules[$sModuleName];
        }
        
        return $objTempModTranslation->translate($sUniqueKey, $sDefaultEnglishTranslation, $sVariable1, $sValue1, $sVariable2, $sValue2, $sVariable3, $sValue3);        
    }
    else
    {
        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'transm()-> $arrTranslationsModules is not set');
    	if ($sDefaultEnglishTranslation == '')    		
       	 	return $sUniqueKey;
    	else
    		return $sDefaultEnglishTranslation;
    } 
}

/**
 * for transm(), transf(), transg() generate a safe key
 * 1. spaces are replaced with _
 * 2. and everything is converted into lowercase
 * 3. non-asci chars are filtered
 * 
 * @return string
 */
function getSafeTransKey($sDirtyString)
{
    $sResult = '';
    $arrWords = array();

    //replace: space with underscore
    //exploding is faster than str_replace
    $arrWords = explode(' ', $sDirtyString);
    $sResult = implode('_', $arrWords);

    //to lowercase
    $sResult = strtolower($sResult);

    //filter non asci
    $sResult = filterBadCharsWhiteList($sResult, 'abcdefghijklmnopqrstuvwxyz_-');

    return $sResult;
}

/**
 * start a performance test
 */
$arrPerformanceTests = array(); //for performance testing reasons
function starttest($sID = 'framework')
{
	global $arrPerformanceTests;
	
	$arrPerformanceTests[$sID] = microtime(true); 	
}

/**
 * end and print a performance test to the screen
 * @param string $sID
 */
function stoptest($sID = 'framework')
{
	global $arrPerformanceTests;
		
	$iEndTest = microtime(true);
	$iTestTime = $iEndTest - $arrPerformanceTests[$sID];
	if (APP_DEBUGMODE)
		echo 'performance test. Execution time "', $sID, '": ',$iTestTime." secs\n";
}

/**
 * if you want to retrieve a classname without the namespace prefix
 * get_class returns the class including the namespaced
 * @param object $objObject
 */
function get_class_short($objObject)
{
	$objReflect = new \ReflectionClass($objObject); 
	return $objReflect->getShortName();
}


/**
 * send the user a message via the UI
 * 
 * @param string $sTranslatedMessage translated message (this function does not translate)
 */
function sendMessageSuccess($sTranslatedMessage)
{
    $_GET[GETARRAYKEY_CMSMESSAGE_SUCCESS][] = $sTranslatedMessage;
}

/**
 * send the user an error message via the UI
 * 
  * @param string $sTranslatedErrorMessage translated message (this function does not translate)
 */
function sendMessageError($sTranslatedErrorMessage)
{
    $_GET[GETARRAYKEY_CMSMESSAGE_ERROR][] = $sTranslatedErrorMessage;
}

/**
 * send the user a notification
 * 
  * @param string $sTranslatedMessage translated message (this function does not translate)
 */
function sendMessageNotification($sTranslatedMessage)
{
    $_GET[GETARRAYKEY_CMSMESSAGE_NOTIFICATION][] = $sTranslatedMessage;
}

/**
 * check if all the nessary php modules are present to run this framework
 * 
 * @return  array false if everything is ok, otherwise returns array with modules not loaded
 */
function getPHPExtensionsNotLoaded()
{
    $arrNotPresent = array();
    // $arrPrequisites = array('zip', 'mysqli', 'mbstring', 'json', 'bcmath', 'openssl', 'php_intl', 'gd');    
    $arrPrequisites = array(PHP_EXT_ZIP, PHP_EXT_MYSQLI, PHP_EXT_MBSTRING, PHP_EXT_JSON, PHP_EXT_BCMATH, PHP_EXT_OPENSSL, PHP_EXT_INTL, PHP_EXT_GD, PHP_EXT_FILEINFO);    
    //imagick pdf geoip oauth uuid


    foreach($arrPrequisites as $sPHPMod)
    {
        if (!extension_loaded($sPHPMod))
        {
            $bResult = false;          
            $arrNotPresent[] = $sPHPMod;
        }
    }
    
    return $arrNotPresent;    
}

/**
 * backups the database to backup directory
 * 
 * @global TDBConnection $objDBConnection
 * @return int with bytes written or false on failure
 */
function makeBackupDatabase()
{
    global $objDBConnection;
    
    $sFileName = '';
    $sFileName = date('Y-m-d H:m').'_'.md5(rand()).'.txt';
    
    $sFileContents = '';
    $sFileContents = $objDBConnection->getPreparedStatement()->getBackupDatabase();
    return file_put_contents(APP_PATH_BACKUPS.DIRECTORY_SEPARATOR.$sFileName, $sFileContents);
}

/**
 * convert Mod_Courses into a fully namespaced classname
 * 
 * @param string $sSmallClassName
 */
function getModuleFullNamespaceClass($sSmallClassName)
{
    return '\dr\modules\\'.$sSmallClassName.'\\'.$sSmallClassName;
}

function getModuleFolders()
{ 
    return getFileFolderArray(APP_PATH_MODULES, true, false);
}

/**
 * returns class names that are regarded as system classes,
 * classes that must be present in order for the framework to run
 * 
 * 
 * THE ORDER IS IMPORTANT!!!
 * If A depends on B, put B first in the array
 * for example: for PROJECTS you need ADDRESSBOOK and ARTICLES
 * put ADDRESSBOOK and ARTICLES first, after that PROJECTS
 * 
 * The array is defined in this function and NOT in the bootstrap, 
 * because php needs to include all the files, evaluate and compile the php just to return class names.
 * This takes time, which is unnessary since we don't need them for every webpage request
 */
function getSystemModels()
{
    //situation before 15-11-2022
    // return array(                                               //DEPENDENCIES:
    //     dr\classes\models\TSysSettings::class,                  //-
    //     dr\classes\models\TSysLanguages::class,                 //-
    //     dr\classes\models\TSysTableVersions::class,             //-
    //     dr\modules\Mod_Sys_Modules\models\TSysModulesCategories::class,         //-
    //     dr\modules\Mod_Sys_Modules\models\TSysModules::class,                   //needs module categories
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles::class,             //-
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers::class,                  //needs accounts+langauges+userroles
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect::class,       //-
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions::class,          //needs users
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions::class,            //needs userroles
    //     dr\classes\models\TSysWebsites::class,                  //needs languages
    //     dr\classes\models\TSysActiveLanguagesPerSite::class,    //needs sites+languages
    //     dr\classes\models\TSysCountries::class,                 //-
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries::class,   //needs countries
    //     dr\classes\models\TSysContacts::class,                  //needs countries 
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations::class,           //needs contacts 
    //     dr\modules\Mod_Sys_CMSUsers\models\TSysCMSInvitationCodes::class         //-
    // );

    return array(                                                               //DEPENDENCIES:
        dr\modules\Mod_Sys_Settings\models\TSysSettings::class,                 //-
        dr\modules\Mod_Sys_Settings\models\TSysCMSMenu::class,                  //-
        dr\modules\Mod_Sys_Localisation\models\TSysLanguages::class,            //-
        dr\modules\Mod_Sys_Localisation\models\TSysCountries::class,            //-
        dr\classes\models\TSysTableVersions::class,                             //-
        dr\modules\Mod_Sys_Modules\models\TSysModules::class,                   //needs module categories
        dr\modules\Mod_Sys_Contacts\models\TSysContactsLastNamePrefixes::class, //-
        dr\modules\Mod_Sys_Contacts\models\TSysContactsSalutations::class,       //
        dr\modules\Mod_Sys_Contacts\models\TSysContacts::class,                  //needs countries, last-name-prefixes and salutations
        dr\modules\Mod_Sys_Localisation\models\TSysCurrencies::class,            //-
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles::class,             //-
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations::class,           //needs contacts 
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers::class,                  //needs accounts+langauges+userroles
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect::class,       //-
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions::class,          //needs users
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersHistory::class,          //needs users
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions::class,            //needs userroles
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRolesAssignUsers::class,  //needs userroles 2x
        dr\modules\Mod_Sys_Websites\models\TSysWebsites::class,                  //needs languages
        dr\modules\Mod_Sys_Localisation\models\TSysActiveLanguagesPerSite::class,    //needs sites+languages
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries::class,   //needs countries
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist::class,   //-        
        dr\modules\Mod_Sys_CMSUsers\models\TSysCMSInvitationCodes::class,        //-
        dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses::class, //-
        dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages::class             //needs TPageBuilderDocumentsStatusses
    );

}

/**
 * return all instantiated classes of getSystemModels()
 *
 * @return array with objects
 */
function getSystemModelsInstantiated()
{
    $arrClasses = getSystemModels();
    $arrObjects = array();

    foreach ($arrClasses as $sClassName)
    {
        $arrObjects[] = new $sClassName;
    }

    return $arrObjects;
}


/**
 * authorize an operation
 * determine if a cms user is allowed to perform an operation
 * 
 * this function will return false when not authorised or when 
 * you forget to return a permission in TModuleAbstract-child->getPermissions()
 * 
 * a typical auth() call would look like this:
 * auth(APP_ADMIN_CURRENTMODULE, TCDCollection::PERM_CAT_ARTISTS, TCDCollection::PERM_OP_VIEW)
 * 
 * the same method is used as getAuthResource() to compile a resource string
 * 
 * this (and related methods) are available in a framework library, so it is standard availble 
 * for every type of application you want to make with the framework
 * 
 * @param string $sModule if empty ('') system is assumed
 * @param string $sCategoryOrScreen the screen of the operation (or category if it doesn't concern a screen) (is defined TModuleAbstract-child)
 * @param string $sOperation (is defined TModuleAbstract-child)
 * @return boolean true = allowed, false = disallowed
 * 
 */
function auth($sModule, $sCategoryOrScreen, $sOperation)
{
    $sResource = '';
    $sResource = $sModule.AUTH_RESOURCESEPARATOR.$sCategoryOrScreen.AUTH_RESOURCESEPARATOR.$sOperation;
    

    // if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS]))
    // {
    //     if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sResource]))
    //         return $_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sResource];
    //     else
    //     {
    //         error_log(__FILE__.':'.__LINE__.': auth(): resource "'.$sResource.'" not found');
    //         return false;
    //     }
    // }
    // else
    // {
    //     error_log(__FILE__.':'.__LINE__.': auth(): SESSIONARRAYKEY_PERMISSIONS not found in $_SESSION. (requested resource: "'. $sResource.'")');
    //     return false;
    // }
    
    //15-8-2025 rewrite doing 1 check instead of 2
    if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sResource]))
    {
        return $_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sResource];
    }
    else
    {
        error_log(__FILE__.':'.__LINE__.': auth(): SESSIONARRAYKEY_PERMISSIONS not found in $_SESSION. Or resource not found (requested resource: "'. $sResource.'")');
        return false;
    }
    

    return false;
}

/**
 * This is a copy of auth() and does exactly the same as auth(), 
 * but it excepts a full resource string (that can be created with getAuthResourceString())
 */
function authRes($sFullResourceString)
{
    // if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS]))
    // {
    //     if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sFullResourceString]))
    //         return $_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sFullResourceString];
    //     else
    //     {
    //         error_log(__FILE__.':'.__LINE__.': authRes(): resource "'.$sFullResourceString.'" not found');
    //         return false;
    //     }
    // }
    // else
    // {
    //     error_log(__FILE__.':'.__LINE__.': authRes(): SESSIONARRAYKEY_PERMISSIONS not found in $_SESSION. (requested resource: "'. $sFullResourceString.'")');
    //     return false;
    // }

    //15-8-2025 rewrite doing 1 check instead of 2
    if (isset($_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sFullResourceString]))
    {
        return $_SESSION[SESSIONARRAYKEY_PERMISSIONS][$sFullResourceString];
    }
    else
    {
        error_log(__FILE__.':'.__LINE__.': authRes(): SESSIONARRAYKEY_PERMISSIONS not found in $_SESSION. Or resource not found (requested resource: "'. $sResource.'")');
        return false;
    }    
}

/**
 * get a resource string  for auth() related functions.
 * auth() itself doesnt use this function because of speed, but it uses the same method
 * 
 *
 * @param string $sModule
 * @param string $sCategoryOrScreen
 * @param string $sOperation
 * @return string
 */
function getAuthResourceString($sModule, $sCategoryOrScreen, $sOperation)
{
    return $sModule.AUTH_RESOURCESEPARATOR.$sCategoryOrScreen.AUTH_RESOURCESEPARATOR.$sOperation;
}

/**
 * split a resource string in 3 components: module, category, operation
 * this is the reverse function of getAuthResourceString();
 * 
 * returns an array with keys: MODULE, CATEGORY and OPERATION
 *                            array(
 *                                  module => 'modulename', 
 *                                  category => 'categoryname',
 *                                  operation => 'operationname'
*                                   )
 */
function getAuthResourceArray($sResourceString)
{
    $arrResource = array();
    $arrResource = explode(AUTH_RESOURCESEPARATOR, $sResourceString);

    $arrResult = array();
    $arrResult['module'] = $arrResource[0];
    $arrResult['category'] = $arrResource[1];
    $arrResult['operation'] = $arrResource[2];
    return $arrResult;
}


/**
 * retrieve a setting that is stored in the database
 * 
 * this is the equivalent of the auth() but for settings
 * 
 * @param string $sModule module name (can be cms or something else too)
 * @param string $sSettingName the name of the setting you want to retrieve
 * @param bool $bForceLoad force load from database (sometimes neccesary for security reasons)
 */
function getSetting($sModule, $sSettingName, $bForceReload = false)
{
 
    $sResource = '';
    $sResource = $sModule.SETTINGS_RESOURCESEPARATOR.$sSettingName;

    //load on first request
    if ((!isset($_SESSION[SESSIONARRAYKEY_SETTINGS][$sResource])) || $bForceReload)
    {
        settingsReload();

        if (!isset($_SESSION[SESSIONARRAYKEY_SETTINGS][$sResource]))
        {
            error_log(__FILE__.': '.__FUNCTION__.' setting resource "'.$sResource.'" NOT FOUND!');
            return NULL;
        }
    }

    return $_SESSION[SESSIONARRAYKEY_SETTINGS][$sResource];

}

/**
 * retrieve a setting that is stored in the database as boolean
 * 
 * @param string $sModule module name (can be cms or something else too)
 * @param string $sSettingName the name of the setting you want to retrieve
 * @param bool $bForceReload force load from database (sometimes neccesary for security reasons)
 *
 * @return void
 */
function getSettingAsBool($sModule, $sSettingName, $bForceReload = false)
{
    return strToBool(getSetting($sModule, $sSettingName, $bForceReload));
}



/**
 * save a setting in the database
 * 
 * this is an 'expensive' operation, because:
 * - it loads the database record from database
 * - saves the setting in database 
 * - (optional parameter: $bReloadAllSettings) then does a database reload of ALL settings!! (that THEN are stored in the session) 
 * 
 * @param string $sModule module name (can be cms or something else too)
 * @param string $sSettingName the name of the setting you want to save
 * @param string $sValue the name of the setting you want to retrieve
 * @param boolean $bReloadAllSettings do you want to grab the opportunity to reload all settings from database into session? (nevertheless the sessionarray is updated also if this is false)
 */
function setSetting($sModule, $sSettingName, $sValue, $bReloadAllSettings = false)
{
 
    $sResource = '';
    $sResource = $sModule.SETTINGS_RESOURCESEPARATOR.$sSettingName;
 

    //we have to do a load first to make the db record dirty so we can update it
    //otherwise the record is new and added, also description etc are otherwise gone
    //also we want to make sure that the setting exists in database
    $objSettings = new dr\modules\Mod_Sys_Settings\models\TSysSettings();
    $objSettings->limit(1);
    $objSettings->find(dr\modules\Mod_Sys_Settings\models\TSysSettings::FIELD_RESOURCE, $sResource);
    $objSettings->loadFromDB();
    
    // tracepoint('loaded from db settings');
    if ($objSettings->count() == 0)
    {
        return false;
    }
    else
    {
        while ($objSettings->next())
        {
            $objSettings->setValue($sValue);
            $_SESSION[SESSIONARRAYKEY_SETTINGS][$objSettings->getResource()] = $objSettings->getValue();
        }    

        if (!$objSettings->saveToDBAll(true, true))
            return false;
    }

    unset($objSettings);

    if ($bReloadAllSettings)
        return settingsReload();
    return true;
}

/**
 * retun an array with all settings for the cms
 *
    * this will return an array in this format:
    *         return array(
    *       SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER => array ('0', TP_BOOL, 'anyone can register') //default, type
    *       );   
 * 
 * technically speaking, this function would belong in lib_cms, but on installation
 * we only include bootstrap.php not bootstrap_admin.php.
 * We can't include bootstrap_admin.php due to instantiation of classes in that file
 * of wich the database tables don't exist yet, because they are not installed
 * 
 * @return array
 */
function getSettingsEntriesCMS()
{
    return array(
        /* SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER => array ('0', TP_BOOL, 'anyone can register an account'),*/
        SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID => array (dr\classes\models\TSysModel::FIELD_ID_VALUE_DEFAULT, TP_INTEGER, 'which groupid is assumed for a new user?'),
        SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS => array ('0', TP_INTEGER, 'user password expires auto after x days. 0 = never'),
        SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE => array ('100', TP_INTEGER, '# of results per page in the paginator in the cms'),
        SETTINGS_CMS_SYSTEMMAILBOT_FROM_EMAILADDRESS => array ('noreply@example.com', TP_STRING, 'return email address of the system email bot'),
        SETTINGS_CMS_SYSTEMMAILBOT_FROM_NAME => array ('system', TP_STRING, 'displayed name of the email address of the system email bot')
        );   
} 

/**
 * retun an array with all settings for the cms
 *
    * this will return an array in this format:
    *         return array(
    *       SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER => array ('0', TP_BOOL, 'anyone can register') //default, type
    *       );   
 * 
 * @return array
 */
function getSettingsEntriesSystem()
{
    return array(
        SETTINGS_SYSTEM_EMAILSYSADMIN => array ('', TP_STRING, 'email address of the system administrator to send error messages regarding system')
        );   
}


/**
 * return setting resource string
 */
function getSettingsResourceString($sModule, $sSettingName)
{
    return $sModule.SETTINGS_RESOURCESEPARATOR.$sSettingName;
}


/**
 * split a resource string in 3 components: module, category, operation
 * this is the reverse function of getAuthResourceString();
 * 
 * returns an array with keys: MODULE, SETTINGNAME
 *                            array(
 *                                  module => 'modulename', 
 *                                  settingname => 'setting name'
*                                   )
 */
function getSettingsResourceArray($sResourceString)
{
    $arrResource = array();
    $arrResource = explode(SETTINGS_RESOURCESEPARATOR, $sResourceString);

    $arrResult = array();
    $arrResult['module'] = $arrResource[0];
    $arrResult['settingname'] = $arrResource[1];
    return $arrResult;
}

/**
 * refresh the settings stored in the database
 */
function settingsReload()
{
    $objSettings = new dr\modules\Mod_Sys_Settings\models\TSysSettings();
    $objSettings->limit(0);
    $objSettings->loadFromDB();
    // tracepoint('loaded from db settings');
    if ($objSettings->count() == 0)
    {
        return false;
    }
    else
    {
        //delete old settings
        $_SESSION[SESSIONARRAYKEY_SETTINGS] = array();

        while ($objSettings->next())
        {
            $_SESSION[SESSIONARRAYKEY_SETTINGS][$objSettings->getResource()] = $objSettings->getValue();
        }    
    }

    unset($objSettings);

    return true;
}


/**
 * install a theme from the all-themes directory into the current-theme directory
 * this can be used for the cms or any website
 * 
 * I use a folder for previous theme, if the installation of the current theme fails, there is a fallback
 * 
 * @param string $sNewThemeName the folder-path of the theme to install inside the all-themes directory
 * @param string $sFolderCurrentTheme folder-path where the current theme is stored
 * @param string $sFolderPreviousTheme folder-path where the previous theme is stored
 * @param string $sFolderAllThemes folder-path where are all the available stored?
 */
function installTheme($sNewThemeName, $sFolderCurrentTheme, $sFolderPreviousTheme, $sFolderAllThemes)
{
    $sNewThemeName = filterDirectoryTraversal($sNewThemeName);



    //first check if the folder of the new theme exists, otherwise we end up with a empty current-theme folder.
    //this can be due to the directory traversal filter!
    if (!file_exists($sFolderAllThemes.DIRECTORY_SEPARATOR.$sNewThemeName))
    {
        error_log(__FUNCTION__.': '.__LINE__.': installation theme failed, directory of new folder "'.$sFolderAllThemes.DIRECTORY_SEPARATOR.$sNewThemeName.'" doesnt exist');
        return false;
    }

    //skip installation if you are installing the current theme
    if (strpos($sNewThemeName, INSTALLED_POSTFIX) !== false) //if __installed__ exists
    {
        error_log(__FUNCTION__.': '.__LINE__.': installation theme not done, because user is installing the current theme: "'.$sNewThemeName.'"');
        return true;
    }


    //first remove previous theme from "previous-theme" folder
    if (file_exists($sFolderPreviousTheme))
    {    
        if (!rmdirrecursive($sFolderPreviousTheme))
            error_log(__FUNCTION__.': '.__LINE__.': installation theme failed, deleting directory previous-theme "'.$sFolderPreviousTheme.'" failed');
    }

    //rename "current-theme" folder to "previous-theme" as backup (this trick probably won't work on windows since it technically is use)
    if (!rename($sFolderCurrentTheme, $sFolderPreviousTheme))
    {
        error_log(__FUNCTION__.': '.__LINE__.': installation theme failed, rename current-theme to previous-theme "'.$sFolderPreviousTheme.'" failed');
    }

    //create current-theme directory
    if (!mkdir($sFolderCurrentTheme))
    {
        error_log(__FUNCTION__.': '.__LINE__.': installation theme failed, making directory "'.$sFolderCurrentTheme.'" failed');

        //recover previous theme as current
        rmdirrecursive($sFolderCurrentTheme);//delete current theme
        rename($sFolderPreviousTheme, $sFolderCurrentTheme);//restore old theme 
    }

    //copy theme from all-themes folder
    if (!copyRecursive($sFolderAllThemes.DIRECTORY_SEPARATOR.$sNewThemeName, $sFolderCurrentTheme))
    {
        error_log(__FUNCTION__.': '.__LINE__.': installation theme failed, copy to previous directory failed');

        //recover previous theme as current
        rmdirrecursive($sFolderCurrentTheme);//delete current theme
        rename($sFolderPreviousTheme, $sFolderCurrentTheme);//restore old theme 

        return false;
    }

    //rename all current  '__installed__' directories in all-themes folder to original name
    $arrFolders = getFileFolderArray($sFolderAllThemes, true, false);
    foreach ($arrFolders as $sFolder)
    {
        if (strpos($sFolder, INSTALLED_POSTFIX) !== false) //if __installed__ exists
        {
            if (!rename($sFolderAllThemes.DIRECTORY_SEPARATOR.$sFolder, str_replace(INSTALLED_POSTFIX, '', $sFolderAllThemes.DIRECTORY_SEPARATOR.$sFolder)))
                error_log(__FUNCTION__.': '.__LINE__.': rename theme failed: rename '.INSTALLED_POSTFIX.' to original name');        
        }
    }

    //rename currently installed theme to '__installed__'
    if (!rename($sFolderAllThemes.DIRECTORY_SEPARATOR.$sNewThemeName, $sFolderAllThemes.DIRECTORY_SEPARATOR.$sNewThemeName.INSTALLED_POSTFIX))
        error_log(__FUNCTION__.': '.__LINE__.': rename theme failed: rename original name to '.INSTALLED_POSTFIX);        



    return true;
}

/**
 * simple rate limiter
 * The rate limiter helps to limit the amount of requests a client can make.
 * This helps to prevent bot scraping and Denial-Of-Service attacks.
 * 
 * How it works:
 * in the directory are files named $sIdentifier (this could be an IP-address or username)
 * in these files is a serialized array that contain timestamps
 * when the amount of timestamps exeeds the limits ($iAllowedRequests, $iPerSeconds),
 * the rate is limited.
 * 
 * This function throttles speed when the time window is less than 1 minute,
 * else it will die() with a (language unaware) English error message.
 * 
 * CHOICES:
 * - I use a simple function instead of a class to save time
 * - I use the file system to be as closest to the metal as possible, to save a roundtrip to the database
 * - I use serialized arrays to write and load from a file to make it fast
 * - I don't use timestamps as array keys to save memory, just regular indexed arrays
 * 
 * ====================
 * WARNING:
 * =====================
 * If either $iAllowedRequests or $iPerSeconds = 0, rate limiting is disabled, function will return true!
 * 
 * 
 * @param string $sDBDirectory stores temporary files in this directory
 * @param string $sIdentifier like ip address or user name
 * @param int $iAllowedRequests for example 10 requests per second
 * @param int $iPerSeconds window in seconds for example 10 requests per 1 second
 * @param bool $bSendRateLimitHeaders might not want to send headers when using multiple rate limiters
 * 
 * @return bool true=ok, false=rate-limited --> WARNING this function can die() execution when can not throttle
 */
function rateLimiter($sDBDirectory, $sIdentifier, $iAllowedRequests, $iPerSeconds, $bSendRateLimitHeaders = false)
{
    global $arrRateLimiterCalls;//array with resources that called this rateLimiter. This prevents the ratelimiter being called multiple times for the same resource in the same script (multiple includes can cause this issue)

    $sFileContents = '';
    $iRate = 0;
    $arrOldTimeStamps = array();
    $arrTSWindow = array();
    $sFileName = '';
    $bIsThrottled = false;
    
        
    if (($iAllowedRequests === 0) || ($iPerSeconds === 0))
        return true;

    if (!is_dir($sDBDirectory))
    {
        if (!mkdir($sDBDirectory))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'failed to create rate limiter directory: '.$sDBDirectory);
            die('Fatal error occurred, see logfiles for details');
        }
    }

    $sFileName = $sDBDirectory.DIRECTORY_SEPARATOR.$sIdentifier.'.txt';

    //==== prevent multiple calls being made on same resource
    // if (!isset($arrRateLimiterCalls)) //declare $arrRateLimiterCalls if not exists
    //     $arrRateLimiterCalls = array(); 
    // else
    if (in_array($sFileName, $arrRateLimiterCalls)) //stop execution was already called in same script
        return true;
    $arrRateLimiterCalls[] = $sFileName;

    //==== read identifier file
    if (file_exists($sFileName))
    {
        $sFileContents = file_get_contents($sFileName);
    
        if ($sFileContents) //if file exists
            $arrOldTimeStamps = unserialize($sFileContents);
    }

    //==== add timestamp
    $arrOldTimeStamps[] = time();

    //==== remove old timestamps: 
    //put relevant timestamps in new array 
    foreach ($arrOldTimeStamps as $sTimestamp)
    {
        if ((int)$sTimestamp > (time() - $iPerSeconds))  
            $arrTSWindow[] = $sTimestamp;
    }
    unset($arrOldTimeStamps);

    //====  write timestamps current window to file
    file_put_contents($sFileName, serialize($arrTSWindow));

    //==== determine current rate
    $iRate = count($arrTSWindow);

    //==== send headers with rate limit info
    if ($bSendRateLimitHeaders)
    {
        header('X-RateLimit-Limit: '.$iAllowedRequests); // the rate limit ceiling that is applicable for the current request.
        header('X-RateLimit-Remaining: '.($iAllowedRequests - $iRate)); //the number of requests left for the current rate-limit window.
        header('X-RateLimit-Reset: '.(time() + $iPerSeconds));// the time at which the rate limit resets, specified in UTC epoch time (in seconds).
    }

    //==== rate exceeded
    if ($iRate > $iAllowedRequests)
    {  
        $fSecPerReq = 0.0;

        //throttle request
        $fSecPerReq = $iPerSeconds / $iAllowedRequests;
        if ($fSecPerReq < 60) //when less than 1 min
        {
            $bIsThrottled = true;
            logAccess(__FILE__.':'.__FUNCTION__.':'.__LINE__, 'Rate limiter: id "'. $sIdentifier.'" was rate limited by throttling for '.$fSecPerReq.' secs', $sIdentifier);
            sleep((int)$fSecPerReq); //wait for time-window to pass
            return false;
        }

        //die when could not throttle
        if (!$bIsThrottled) 
        {
            header('HTTP/1.1 429 Too many requests');
            echo 'Too many requests';
            logError(__FILE__.':'.__FUNCTION__.':'.__LINE__, 'Rate limiter: id "'. $sIdentifier.'" has shown rate-limit error and terminated execution');
            die();
        }


        return false;
    }

    return true;
}

/**
 * get path of config file website
 * 
 * @param string $sWebsiteName internal name of the website
 * @param string $sHostServer hostname of the server, when '' then $_SERVER['SERVER_NAME'] is assumed
 * @return string
 */
function getPathConfigWebsite($sWebsiteName, $sHostServer = '')
{
    if ($sHostServer == '')
        $sHostServer = $_SERVER['SERVER_NAME'];

    return APP_PATH_CMS_CONFIGS.DIRECTORY_SEPARATOR.'website_'.filterDirectoryTraversal($sWebsiteName).'_'.filterDirectoryTraversal($sHostServer).'.php';
}

/**
 * adds file path to $arrIncludeJS, which is read in templates and includes it in the html file
 */
function includeJS($sPathJSFile)
{
    global $arrIncludeJS;

    $arrIncludeJS[] = $sPathJSFile;
}

/**
 * adds file path to $arrIncludeJSDOMEnd, which is read in templates and includes it in the html file AT THE END OF THE DOM
 */
function includeJSDOMEnd($sPathJSFile)
{
    global $arrIncludeJSDOMEnd;

    $arrIncludeJSDOMEnd[] = $sPathJSFile;
}

/**
 * adds file path to $arrIncludeJSDOMEnd of inhouse <dr-*> webcomponent
 * 
 * @param string $sDRComponentHTMLTagName like 'dr-icon-info' to include JS for <dr-icon-info> or 'dr-input-text' to include JS for <dr-input-text> --> dr-components-lib is the central js library for all web components
 */
function includeJSWebcomponent($sDRComponentHTMLTagName = 'dr-components-lib')
{

    if ($sDRComponentHTMLTagName == 'dr-components-lib')
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.$sDRComponentHTMLTagName.'.js');    
    else
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.$sDRComponentHTMLTagName.DIRECTORY_SEPARATOR.$sDRComponentHTMLTagName.'.js');    
}

/**
 * adds file path to $arrIncludeCSS, which is read in templates and includes it in the html file
 */
function includeCSS($sPathCSSFile)
{
    global $arrIncludeCSS;

    $arrIncludeCSS[] = $sPathCSSFile;
}

/**
 * creates index.php file in directory that generates a 404 error
 * used for example by the "upload" directory to avoid directory listings
 */
function createIndexFile404InDir($sPathDirectory, $bRecursive)
{
    $sIndexFile = '';
    $sIndexFile = $sPathDirectory.DIRECTORY_SEPARATOR.'index.php';

    //create index file
    $fhIndex = fopen($sIndexFile, 'w');
    fwrite($fhIndex, '
        <?php 
            //fake 404 error generated by cronjob
            http_response_code(404); 
        ?>
        ');
    fwrite($fhIndex, renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_404.php', get_defined_vars()));
    fclose($fhIndex);
 
    //created index.php's for subdirectories
    if ($bRecursive)
    {
        $arrSubDirs = getFileFolderArray($sPathDirectory, true, false);

        foreach($arrSubDirs as $sDir)
        {
            createIndexFile404InDir($sPathDirectory.DIRECTORY_SEPARATOR.$sDir, true);
        }
    }

}

?>
