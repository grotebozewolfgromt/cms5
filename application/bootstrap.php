<?php
/**
 * BOOTSTRAP
 * 
 * boots the framework:
 * -includes config file
 * -sets constants and defaults, EVEN if the config file is corrupt!
 * -sets database connection
 * -starts session
 * -reads modules
 * -and much more
 * 
 * if you want authorised access to a page in the cms, include bootstrap_cms_auth (it will include all bootstraps nessesary)
 * 
 * april 2019 Dennis Renirie
 */

use dr\classes\db\TDBConnection;
use dr\classes\patterns\TConfigFile;





    //================== LOADING CONFIG FILE =================== 
        //every host has its own config file which is stored in the 'configs' directory
        define('APP_PATH_CMS', __DIR__); //it is assumed that the bootstrap is placed in the root path!!!
        define('APP_PATH_CMS_CONFIGS', APP_PATH_CMS.DIRECTORY_SEPARATOR.'configs');
        define('APP_PATH_CMS_CONFIGFILE_APPLICATION', APP_PATH_CMS_CONFIGS.DIRECTORY_SEPARATOR.'application_'.$_SERVER['SERVER_NAME'].'.php');
        // define('APP_PATH_CMS_CONFIGFILE_WEBSITE', APP_PATH_CMS_CONFIGS.DIRECTORY_SEPARATOR.'website_website.nl_'.$_SERVER['SERVER_NAME'].'.php'); //define in bootstrap website


        //config framework
        include_once(APP_PATH_CMS_CONFIGFILE_APPLICATION);   
        if (!defined('APP_DEBUGMODE')) //check 1 value to see if config is loaded properly
        {
            echo 'Error loading config file application for host: '.$_SERVER['SERVER_NAME'].'!<br>Does the config file even exist?';
            error_log('error loading config file: '.APP_PATH_CMS_CONFIGFILE_APPLICATION);
            die();
        }


        //================== DEFINING CONSTANTS (and defaults) ===================        
   
            define('APP_URL_ADMIN_CLASSES', APP_URL_ADMIN.'/classes');
            define('APP_PATH_CMS_CLASSES', APP_PATH_CMS.DIRECTORY_SEPARATOR.'classes');            

            //===installer scripts
            define('APP_URL_ADMIN_INSTALLER', APP_URL_ADMIN.'/installer');
            define('APP_PATH_CMS_INSTALLER', APP_PATH_CMS.DIRECTORY_SEPARATOR.'installer');            

            define('APP_URL_ADMIN_INSTALLERSCRIPT', APP_URL_ADMIN_INSTALLER.'/index.php');
            define('APP_PATH_CMS_INSTALLERSCRIPT', APP_PATH_CMS_INSTALLER.DIRECTORY_SEPARATOR.'installer.php');            

            define('APP_URL_ADMIN_INSTALLSCRIPT', APP_URL_ADMIN_INSTALLER.'/step1.php');
            define('APP_PATH_CMS_INSTALLSCRIPT', APP_PATH_CMS_INSTALLER.DIRECTORY_SEPARATOR.'install.php');            

            define('APP_URL_ADMIN_UNINSTALLSCRIPT', APP_URL_ADMIN_INSTALLER.'/uninstall.php?confirm='.APP_INSTALLER_PASSWORD);
            define('APP_PATH_CMS_UNINSTALLSCRIPT', APP_PATH_CMS_INSTALLER.DIRECTORY_SEPARATOR.'uninstall.php');

            //===Models, (Views), Controllers
            define('APP_PATH_CMS_MODELS', APP_PATH_CMS_CLASSES.DIRECTORY_SEPARATOR.'models');   
            define('APP_PATH_CMS_CONTROLLERS', APP_PATH_CMS_CLASSES.DIRECTORY_SEPARATOR.'controllers');   

            //===Views/theme specific paths
            define('APP_URL_ADMIN_VIEWS', APP_URL_ADMIN.'/views');                        
            define('APP_PATH_CMS_VIEWS', APP_PATH_CMS.DIRECTORY_SEPARATOR.'views');                        

            define('APP_URL_ADMIN_VIEWS_THEMES', APP_URL_ADMIN_VIEWS.'/themes');                        
            define('APP_PATH_CMS_VIEWS_THEMES', APP_PATH_CMS_VIEWS.DIRECTORY_SEPARATOR.'themes');                        
            define('APP_URL_ADMIN_VIEWS_CURRENTTHEME', APP_URL_ADMIN_VIEWS_THEMES.'/'.APP_ADMIN_THEME);                        
            define('APP_PATH_CMS_VIEWS_CURRENTTHEME', APP_PATH_CMS_VIEWS_THEMES.DIRECTORY_SEPARATOR.APP_ADMIN_THEME);                        

            define('APP_URL_ADMIN_VIEWS_STYLESHEETS', APP_URL_ADMIN_VIEWS_CURRENTTHEME.'/css');
            define('APP_PATH_CMS_VIEWS_STYLESHEETS', APP_PATH_CMS_VIEWS_CURRENTTHEME.DIRECTORY_SEPARATOR.'css');            

            define('APP_URL_ADMIN_VIEWS_JAVASCRIPTS', APP_URL_ADMIN_VIEWS_CURRENTTHEME.'/js');
            define('APP_PATH_CMS_VIEWS_JAVASCRIPTS', APP_PATH_CMS_VIEWS_CURRENTTHEME.DIRECTORY_SEPARATOR.'js');            

            define('APP_URL_ADMIN_IMAGES', APP_URL_ADMIN_VIEWS_CURRENTTHEME.'/images');
            define('APP_PATH_CMS_IMAGES', APP_PATH_CMS_VIEWS_CURRENTTHEME.DIRECTORY_SEPARATOR.'images');

            //==Global (none theme-specific)
            define('APP_URL_ADMIN_STYLESHEETS', APP_URL_ADMIN_VIEWS.'/css');
            define('APP_PATH_CMS_STYLESHEETS', APP_PATH_CMS_VIEWS.DIRECTORY_SEPARATOR.'css');            

            define('APP_URL_ADMIN_JAVASCRIPTS', APP_URL_ADMIN_VIEWS.'/js');
            define('APP_PATH_CMS_JAVASCRIPTS', APP_PATH_CMS_VIEWS.DIRECTORY_SEPARATOR.'js');    

            define('APP_URL_ADMIN_FONTS', APP_URL_ADMIN_VIEWS.'/fonts');
            define('APP_PATH_CMS_FONTS', APP_PATH_CMS_VIEWS.DIRECTORY_SEPARATOR.'font');    
            
            define('APP_PATH_CMS_TEMPLATES', APP_PATH_CMS_VIEWS_CURRENTTHEME.DIRECTORY_SEPARATOR.'templates');                        

            define('APP_URL_ADMIN_PAGEBUILDERROUTER', APP_URL_ADMIN.'/pagebuilderrouter');                        

            define('APP_URL_MODULES', APP_URL_ADMIN.'/'.APP_ADMIN_MODULESDIR);
            define('APP_PATH_MODULES', APP_PATH_CMS.DIRECTORY_SEPARATOR.APP_ADMIN_MODULESDIR);            
                    
            define('APP_URL_VENDOR', APP_URL_ADMIN.'/vendor');
            define('APP_PATH_VENDOR', APP_PATH_CMS.DIRECTORY_SEPARATOR.'vendor');            

            define('APP_PATH_LIBRARIES', APP_PATH_CMS.DIRECTORY_SEPARATOR.'lib');
            define('APP_PATH_LOGFILES', APP_PATH_CMS.DIRECTORY_SEPARATOR.'logfiles');            
            define('APP_PATH_LANGUAGES', APP_PATH_CMS.DIRECTORY_SEPARATOR.'languages');            
            define('APP_PATH_CMS_CACHE', APP_PATH_CMS.DIRECTORY_SEPARATOR.'cache'); 
            define('APP_PATH_CMS_CACHE_RATELIMITER_PERDAY', APP_PATH_CMS_CACHE.DIRECTORY_SEPARATOR.'ratelimiter'.DIRECTORY_SEPARATOR.'requestsperday'); 
            define('APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND', APP_PATH_CMS_CACHE.DIRECTORY_SEPARATOR.'ratelimiter'.DIRECTORY_SEPARATOR.'requestspersecond');                       
            define('APP_PATH_CMS_BACKUPS', APP_PATH_CMS.DIRECTORY_SEPARATOR.'backups');   
        
        //LOCALE
            define('APP_LOCALE_DEFAULT', 'en');
            define('APP_LOCATION_DEFAULT', 'NL');
            
        //CACHING
            define('APP_CACHE_TIMEOUT_DEFAULT', 86400);//in seconds; 86400 = 24 hour cache file timeout
            
        //ALL THE $SESSION VARIABLES for the system (so not defined in controllers or so)
            define('SESSIONARRAYKEY_SELECTEDSITEID', 'iSelectedSiteID');//used in cms5. this NEEDS to be iSelectedSiteID for compatibility with cms2/4, name for cms5 doesn't matter
            define('SESSIONARRAYKEY_SELECTEDLANGUAGEID', 'iSelectedLanguageID');//used in cms5 for default language per site
                        
        //ALL THE $COOKIE VARIABLES for the system (so not defined in controllers or so) 
            define('APP_COOKIE_EXPIREDAYS', 60);
            define('COOKIEARRAYKEY_SELECTEDSITEID', 'sid');//used in cms5
            
        //ALL THE $_GET VARIABLES for the system (so not defined in controllers or so) 
            define('GETARRAYKEY_SELECTEDSITEID', 'selectedSiteID');//used in cms5 for requesting the default language of the selected site (only lives in session,
            define('GETARRAYKEY_CMSMESSAGE_SUCCESS', 'cmsmessage'); //$GET array indexes for messages
            define('GETARRAYKEY_CMSMESSAGE_ERROR', 'cmserror'); //$GET array indexes for messages
            define('GETARRAYKEY_CMSMESSAGE_NOTIFICATION', 'cmsnotification'); //$GET array indexes for messages
            
        //ALL THE $_POST VARIABLES for the system (so not defined in controllers or so) 
            //still empty

        //WEB COMPONENTS
            define('APP_ADMIN_WEBCOMPONENTSDIR', 'webcomponents');//the url-rewritten directory in the url in which web components are stored (needed in urlrouter). While the actual directory would be http://localhost/cms5/application/classes/dom/tag/webcomponents/, now it will be http://localhost/cms5/application/[webcomponentsdir]/  
            define('APP_URL_ADMIN_WEBCOMPONTENTS', APP_URL_ADMIN.'/'.APP_ADMIN_WEBCOMPONENTSDIR); //a nice url-rewritten path (supported by the url router) for the CMS only
            define('APP_PATH_CMS_WEBCOMPONENTS', APP_PATH_CMS.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'dom'.DIRECTORY_SEPARATOR.'tag/webcomponents');

        //================== ERROR LOG =========================
            //use php's own error log mechanism ( error_log('just browsin');) , but put in a separate folder
        $sLogDirToday = '';
        $sLogDirToday = APP_PATH_LOGFILES.DIRECTORY_SEPARATOR.date('Y-m-d');
        
        if(!is_dir($sLogDirToday))
        {
            //create general logfiles dir
            if (!is_dir(APP_PATH_LOGFILES))
            {
                if (!mkdir(APP_PATH_LOGFILES))
                {             
                    die ('Fatal error occurred. See logfiles for details'); //this will be a public error, therefore deliberately vague
                    error_log('Could NOT create directory: '.$APP_PATH_LOGFILES);
                }                    
            }

            //create dir for today
            if (!mkdir($sLogDirToday))
            {             
                die ('Fatal error occurred. See logfiles for details'); //this will be a public error, therefore deliberately vague
                error_log('Could NOT create directory: '.$sLogDirToday.DIRECTORY_SEPARATOR);
            }

            //create htaccess that blocks access to dir
            $fhHtaccess = fopen($sLogDirToday.DIRECTORY_SEPARATOR.'.htaccess', 'w'); 
            if ($fhHtaccess === false)
            {             
                die ('Fatal error occurred. See logfiles for details'); //this will be a public error, therefore deliberately vague
                error_log('Could NOT create ".htaccessfile" in "'.$sLogDirToday.DIRECTORY_SEPARATOR.'"');
            }
            fwrite($fhHtaccess, 'deny from all');
            fclose($fhHtaccess);
        }

        ini_set('error_log', $sLogDirToday.DIRECTORY_SEPARATOR.'errorlog_'.date('Y-m-d').'.txt');        
        // ini_set('error_log', $sTempLogDir.DIRECTORY_SEPARATOR.''.date('Y-m-d').'_phplog.txt');        
            
        
        //================== DEVELOPMENT OR DEPLOYMENT ===================
        error_reporting(0);
        ini_set('display_errors', 'off');
        if (APP_DEBUGMODE === true)
        {
            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        }

        //================== MAINTENANCE MODE CHECK ===================
        if (APP_MAINTENANCEMODE === true)
        {
            $bHalt = true;

            //skip maintenance mode check. Necessary for maintenance scripts to do the actual maintenance
            if (defined('APP_MAINTENANCEMODE_SKIPCHECK')) //only when explicitly skipped by define('APP_MAINTENANCEMODE_SKIPCHECK', true); 
                if (APP_MAINTENANCEMODE_SKIPCHECK === true)
                    $bHalt = false;
            
            if ($bHalt)
            {
                echo 'Sorry, system is in maintenance mode.<br>Please check in later';
                die();
            }
            unset($bHalt);//being a good boy ;)
        }

        //================== INLCUDE ALL LIBRARIES FROM LIBRARY DIRECTORY =================== 
        //we don't autoload everything in 'lib' directory for performance reasons
        //you have to load in manually on top of every php page.
        //the lib files with _sys_ are system libraries that are loaded for you with the boot of the framewor
        //below are all the library files commented out, copy paste the lines in the php headers and uncomment the ones you need

        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_framework.php');
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_typedef.php'); //ONLY for compatibility reasons with cms2,, @remove if no cms2 components are present
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_types.php'); 
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_file.php'); //-->we need this later in this bootstrap file
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_sys_inet.php'); //-->we need this later in this bootstrap file
        
        //libraries below are included in bootstrap_admin.php:
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');          
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms_url.php');     
        
        //Use includes below in headers of php files:
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
        //include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');
        

        
        //================== AUTOLOADER CLASSES ===================        
        spl_autoload_register('autoLoaderFramework'); //autoloader function in lib_system
        spl_autoload_register('autoLoaderVendor'); //autoloader function in lib_system


        //================== CUSTOM ERROR HANDLER ===================        
        register_shutdown_function( 'customShutdownOnError' ); //nessesary to trigger the customErrorHandler for normal PHP errors (by default not triggered for php errors)
        set_error_handler( 'customErrorHandler' );
        set_exception_handler( 'customExceptionHandler' );
        

        //================== RATE LIMIT ======================
        if (!isset($arrRateLimiterCalls)) //array stores resources (identifiers) to prevent them being called twice in the same script (multiple inclusion for example)
            $arrRateLimiterCalls = array(); //declare $arrRateLimiterCalls if not exists
        rateLimiter(APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND, getIPAddressClient('_'), 2, 1, true); //allow 2 requests per second
        
        
        
        //================== INIT DATABASE ===================

        //deze moet boven de modules zitten (vanwege de modules die in de application zitten, die een database connectie nodig hebben)
        $sDatabaseClass = APP_DB_CONNECTIONCLASS;
        $objDBConnection = new $sDatabaseClass(NULL);//make instance of database object
        $objDBConnection->setHost(APP_DB_HOST);
        $objDBConnection->setUsername(APP_DB_USER);
        $objDBConnection->setPassword(APP_DB_PASSWORD);
        $objDBConnection->setDatabaseName(APP_DB_DATABASE);
        $objDBConnection->setPort(APP_DB_PORT);          
        if (APP_DEBUGMODE)
            $objDBConnection->setErrorReporting(TDBConnection::REPORT_ERROR_ON); //only show errors in debugmode
        else
            $objDBConnection->setErrorReporting(TDBConnection::REPORT_ERROR_OFF); //no errors in deployment environment

        //---> we don't connect automatically, 
        //---> connection is automatically established when executing a query
        //---> cached pages don't need a database connection at all, 
        //---> this prevents an unnessary roundtrip to the database
        // if (!$objDBConnection->connect())
        // {            
        //     logError(__FILE__.':'.__LINE__, 'Database connection problem');

        //     if (file_exists(APP_PATH_CMS_INSTALLSCRIPT)) //framework probably not installed when cant connect
        //         die('Database connection error.<br>This error is maybe caused because you didn\'t install the system (properly).<br><a href="'.APP_URL_ADMIN_INSTALLERSCRIPT.'">Start installer</a>');
        //     else
        //         die('Error occured, see error logfile for details'); //deliberately very vague
        // }
        
        
        
        //================== DEFINING SYSTEM DATABASE TABLES ===================   
        //  ===> system database tables are defined in lib_sys_framework:getSystemModels()


        
                    
        
        //================== DEFINE LANGUAGE FILES =================== 
        //@TODO cache Locale, countrysettings and language files (speed improvement)
        //we ONLY define default file names here, they may be overwritten, like in bootstrap_admin_auth.php with settings of the user
        //files are automatically loaded upon usage (information requests) of the classes
        //this way we don't have performance overhead (loading default language files and later loading the preferred language files of the user)
            
            
        //defining locale
            $objLocale = new dr\classes\locale\TLocale(APP_LOCALE_DEFAULT);
            $objLocalisation = new dr\classes\locale\TLocalisation();//will be autoloaded when needed            
            $objLocalisation->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'countrysettings_'.APP_LOCALE_DEFAULT.'.ini');
            
            setlocale(LC_TIME, APP_LOCALE_DEFAULT);
            
            
        //defining translation files
            $objTranslationGlobal = new dr\classes\locale\TTranslation(); //will be autoloaded when needed
            $objTranslationGlobal->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'global_translation_'.APP_LOCALE_DEFAULT.'.csv');
            $arrTranslationsWebsites = array(); //--> 1d array with TTranslation objects of all website. (key is the name of the module). the objects are created and added upon request in transw();
            //$arrTranslationsWebsites = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            //$arrTranslationsWebsites->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'[website]_translation_'.APP_LOCALE_DEFAULT.'.csv');        
            $arrTranslationsModules = array(); //--> 1d array with TTranslation objects of all modules. (key is the name of the module). the objects are created and added upon request in transm();
            //$objTranslationModules = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            //$objTranslationModules->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'module_translation_'.APP_LOCALE_DEFAULT.'.txt');        
        



        //================== START SESSION ===================
        session_set_cookie_params(MONTH_IN_SECS,'/',getDomain(), APP_ISHTTPS, true);//0 = till browser closes, forces https if available / http only means that it is not accessible by javascript (this prevents xss)
        session_start();
                    
            

                
        //================== REDIRECT TO HTTPS ===================
        //redirect to https version if available
        //only when we have paths and libraries, we can redirect
        redirectToHTTPS();
                

        //================== DEFINING ARRAYS FOR JS & CSS ===================   
        $arrIncludeJS = array(); //array of absolute paths with javascript files to include in views/templates
        $arrIncludeJSDOMEnd = array(); //array of absolute paths with javascript files to include in views/templates LAST IN THE HTML DOM
        $arrIncludeCSS = array(); //array of absolute paths with CSS files to include in views/templates

        
        //================== MISC ================================
        // $sURLThisScript = getURLThisScript();
        define('APP_URLTHISSCRIPT', getURLThisScript());
		
?>