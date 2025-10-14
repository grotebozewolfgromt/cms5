<?php

    // use dr\classes\models\TSysModel;
    use dr\classes\controllers\TCMSAuthenticationSystem;
    use dr\classes\controllers\uploadfilemanager;
    // use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
    
/**
 * BOOTSTRAP_CMS_AUTH
 * ===================
 * bootstrap file that includes the cms cms bootstrap but WITH AUTHORISATION CHECK if user is logged in
 * 
 * include this file if you want to have username and password checked!
 * 
 * created: 9 april 2019 Dennis Renirie
 */
	
    include_once(__DIR__.DIRECTORY_SEPARATOR.'bootstrap_admin.php');          
   
    //======================= MAKING VARIABLES GLOBAL ==============
    //when you use a class via the autoloader which needs bootstrap_cms_auth,
    //variables declared in this file are not globally available and global variables are not available
    //The code below makes them globally available
        global $objAuthenticationSystem;
        global $objWebsites;
        global $objLocale;
        global $objLocalisation;
        global $objTranslationGlobal;
        global $objTranslationCMS;
        global $arrTranslationsModules;
        global $objTranslationWebsite;


    //================= AUTHORISE USER ==================
    $objAuthenticationSystem = new TCMSAuthenticationSystem();    
    $objAuthenticationSystem->handleAuthentication();  

    


    
    //================= WEBSITES =================================
    //we always need a list of websites so we can switch websites by clicking on the combobox on the left side of the screen
        $iTempCMS5SelectedSiteID = 1;//default 1 issumed because it is most likely the first in the database
        $iTempCMS5LanguageIDSelectedSite = 0;
                

        //the selected site is set on login
        
        //====SELECTED SITE DEFAULT (if something went wrong with login)
        //we need to prefer the session over the cookie (for security reasons)
        //due to backwards compatibility with cms2/4 the session is always used, cookie is ignored, only when session expires, the cookie is used to fill the session array
        
        if (!isset($_SESSION[SESSIONARRAYKEY_SELECTEDSITEID])) //to be sure we have always a selected site (a session can expire)
        {
            $objWebsites = new dr\modules\Mod_Sys_Websites\models\TSysWebsites();
            $objWebsites->loadFromDBByIsDefault();  


            if (!isset($_COOKIE[COOKIEARRAYKEY_SELECTEDSITEID]))//cookie not set
            {
                $iTempCMS5SelectedSiteID = $objWebsites->getID();
                $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID] = $objWebsites->getID(); //make sure there is always a session with a site id for cms5
                sendMessageNotification(transcms('changed_website_default', 'You switched to defaultwebsite'));
            }
            else //if cookie set
            {
                if (is_numeric($_COOKIE[COOKIEARRAYKEY_SELECTEDSITEID]))
                {
                    $iTempCMS5SelectedSiteID = $_COOKIE[COOKIEARRAYKEY_SELECTEDSITEID];              
                    $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID] = $_COOKIE[COOKIEARRAYKEY_SELECTEDSITEID];
                }
                else //otherwise assume defaults
                {
                    $iTempCMS5SelectedSiteID = $objWebsites->getID();
                    $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID] = $objWebsites->getID();
                    sendMessageNotification(transcms('changed_website_default', 'You switched to defaultwebsite'));
                }
            }

        }
        else //session exists
        {
            $iTempCMS5SelectedSiteID = $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID];
        }
         
    
        
                
        //====CHANGE SELECTED SITE
        if (isset($_GET[GETARRAYKEY_SELECTEDSITEID]))
        {
            if (auth(AUTH_MODULE_CMS, AUTH_CATEGORY_SYSSITES, AUTH_OPERATION_SYSSITES_SWITCH))
            {
                if (is_numeric($_GET[GETARRAYKEY_SELECTEDSITEID]))
                {
                    $iTempCMS5SelectedSiteID = $_GET[GETARRAYKEY_SELECTEDSITEID];
                    
                    if (isset($_SESSION[SESSIONARRAYKEY_SELECTEDSITEID]))
                    {
                        $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID] = $_GET[GETARRAYKEY_SELECTEDSITEID];
                        sendMessageNotification(transcms('changed_website', 'You switched to managing another website'));
                        if (isset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]))//make sure the languageid is set later
                            unset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]) ;                           
                    }
                    
                    if (isset($_COOKIE[COOKIEARRAYKEY_SELECTEDSITEID]))
                    {
                        if (!setcookie(COOKIEARRAYKEY_SELECTEDSITEID, $_GET[GETARRAYKEY_SELECTEDSITEID], time() + (DAY_IN_SECS * APP_COOKIE_EXPIREDAYS), '/', getDomain(), APP_ISHTTPS, true)) // 86400 = 1 day
                            logError(__FILE__.__LINE__,'siteid cookie not set');
                        if (isset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]))//make sure the languageid is set later
                            unset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]) ;                       
                    }
                        
                }
                // else //use defaults
                // {
                //     $iTempCMS5SelectedSiteID = APP_DB_SITEID_DEFAULT;
                //     $_SESSION[SESSIONARRAYKEY_SELECTEDSITEID] = APP_DB_SITEID_DEFAULT;//@todo remove cms2/4 compatibility
                //     if (isset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]))//make sure the languageid is set later
                //         unset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]) ;                   
                // }
            }
        }       
        //END====SELECTED SITE  
        
        
        //===== default language id for the selected site
        if (!isset($_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID]))//if default site language is not set in session
        {
            //look it up in the websites object
            if (!$objWebsites) //we may have used it before
            {
                $objWebsites = new dr\modules\Mod_Sys_Websites\models\TSysWebsites();
                $objWebsites->loadFromDBByIsDefault();              
            }
            $iTempCMS5LanguageIDSelectedSite = $objWebsites->getDefaultLanguageID();
            $_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID] = $iTempCMS5LanguageIDSelectedSite; //save in session
        }        
        else //if set, read out of session, so we can store it later in a global constant
        {
            $iTempCMS5LanguageIDSelectedSite = $_SESSION[SESSIONARRAYKEY_SELECTEDLANGUAGEID];
        }
        //END===== default language id for the selected site
        
        
        
        define('APP_WEBSITEID_SELECTEDINCMS', $iTempCMS5SelectedSiteID); //new for cms5
        define('APP_LANGUAGEID_SELECTEDSITE', $iTempCMS5LanguageIDSelectedSite); //new for cms5
        unset($iTempCMS5SelectedSiteID); //prevent accessing it by accident
        unset($iTempCMS5LanguageIDSelectedSite); //prevent accessing it by accident
        if ($objWebsites) //prevent accessing it by accident
            unset($objWebsites);

        

    //================== DEFINE LANGUAGE FILES =================== 
    //@TODO Locale, countrysettings en language files cachen
    //objects are created in bootstrap.php 
    //files of language paths are defined by default in bootstrap.php
    //now we are gonna override the paths with values of the user

        $sTempLocale = '';
        $sTempLocale = $objAuthenticationSystem->getLanguages()->getLocale();
        
    //defining locale
        if ($sTempLocale != APP_LOCALE_DEFAULT) //only load another when different, otherwise keep the locale from bootstrap.php (=faster)
        {
            $objLocale->setLocale($sTempLocale);
            $objLocalisation = new dr\classes\locale\TLocalisation();//will be autoloaded when needed            
            $objLocalisation->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'countrysettings_'.$sTempLocale.'.ini');

            setlocale(LC_TIME, $sTempLocale);
        }

    //overwriting localisation settings by user settings
        if ($objAuthenticationSystem->getUsers()) //only if user exists, if logged out: there is no user
        {
            $objLocalisation->loadFromFile();//force read, because we need to overwrite the settings (read is otherwise done when the first value is requested, but then it overwrites the values of the user)
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::TIMEZONE, $objAuthenticationSystem->getUsers()->getTimeZone());            
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::DATEFORMAT_SHORT, $objAuthenticationSystem->getUsers()->getDateFormatShort());
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::DATEFORMAT_LONG, $objAuthenticationSystem->getUsers()->getDateFormatLong());
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::TIMEFORMAT_SHORT, $objAuthenticationSystem->getUsers()->getTimeFormatShort());
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::TIMEFORMAT_LONG, $objAuthenticationSystem->getUsers()->getTimeFormatLong());
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::SEPARATOR_THOUSAND, $objAuthenticationSystem->getUsers()->getThousandSeparator());
            $objLocalisation->setSetting(dr\classes\locale\TLocalisation::SEPARATOR_DECIMAL, $objAuthenticationSystem->getUsers()->getDecimalSeparator());

        }

    //defining translation files
        if ($sTempLocale != APP_LOCALE_DEFAULT) //only load another when different, otherwise keep the locale from bootstrap.php (=faster)
        {
            $objTranslationGlobal = new dr\classes\locale\TTranslation(); //will be autoloaded when needed
            $objTranslationGlobal->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'system_translation_'.$sTempLocale.'.txt');
            $objTranslationCMS = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            $objTranslationCMS->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'cms_translation_'.$sTempLocale.'.txt');        
            $arrTranslationsModules = array(); //--> 1d array with TTranslation objects of all modules. (key is the name of the module). the objects are created and added upon request in transm();
            //$objTranslationModule = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            //$objTranslationModule->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'module_translation_'.$sTempLocale.'.txt');        
            $objTranslationWebsite = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            $objTranslationWebsite->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'website_translation_'.$sTempLocale.'.txt');        
        }
        
        unset($sTempLocale); //cleanup: prevent using by accident

    // //================== UPLOAD FILE MANAGER =================== 
    // include_once(APP_PATH_CMS_CONTROLLERS.DIRECTORY_SEPARATOR.'uploadfilemanager.php');        
    // $objUploadFileManager = new uploadfilemanager(false);
    // define('CMS_UPLOADFILEMANAGER', $objUploadFileManager); //make it available everwhere in all controllers and all templates. Is this an ugly solution? YES!!!
?>