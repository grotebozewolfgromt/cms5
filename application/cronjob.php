<?php

/**
 * This script will execute all cronjobs from all modules
 */

use dr\classes\models\TSysModel;
use dr\classes\TConfigFileApplication;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;

    //session started in bootstrap
    include_once 'bootstrap_cms.php';




    $sTitle = transcms('cronjob_title', 'Cron job');
    $sHTMLTitle = transcms('cronjob_htmltitle', 'Cron job');
    $sHTMLMetaDescription = transcms('cronjob_htmlmetadescription', 'Cron job');

    //only execute if you have access to the right crobjobid (defined in config file)
    if (isset($_GET[ACTION_VARIABLE_ID]))
    {
        if ($_GET[ACTION_VARIABLE_ID] != APP_CRONJOBID)
        {
            showAccessDenied();
            die();
        }
    }
    else
    {
        showAccessDenied();
        die();        
    }

    logAccess('cronjob','Loading page:"'.getURLThisScript().'". Starting cronjob.');


    //==== redirecting output
    ob_start();

    //==== is only cronjob executing?
    if (!APP_CRONJOB_ISEXECUTING)
    {

        //prevent other cronjobs from being executed
        $objConfig = new TConfigFileApplication();
        $objConfig->loadFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);
        $objConfig->setAsBool('APP_CRONJOB_ISEXECUTING', true);
        $objConfig->saveFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);


        
        //==== starting the ACTUAL cronjob
        echo 'cronjob running (extra details in logfiles) '.date('d-m-Y H:i').'h ...<br>';
        logCronjob(__FILE__.': '.__LINE__,'=== Starting Cronjob.php ===');

        //==== warn if installer is enabled (should be disabled)
        if (APP_INSTALLER_ENABLED)
        {
            echo '<b>The installer is enabled while it should be disabled. This allows malicious actors to remove data from the database! [WARNING]</b><br>';
            logCronjob(__FILE__.': '.__LINE__,'WARNING: Installer is enabled. It should be disabled after installation.');
        }

        //==== warn if php extensions are not loaded
        $arrPHPExtensions = getPHPExtensionsNotLoaded();
        if (count($arrPHPExtensions) > 0)
        {
            echo '<b>The following PHP extensions are not loaded: '.implode(",", $arrPHPExtensions).' [WARNING]</b><br>';
            logCronjob(__FILE__.': '.__LINE__,'WARNING: PHP extensions not loaded:'.implode(",", $arrPHPExtensions));
        }

        //==== anonimize data
        echo 'anonymize contacts older than '.APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS.' days<br>';
        logCronjob(__FILE__.': '.__LINE__,'anonymize contacts older than '.APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS.' days');    
        if (is_int(APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS))
        {
            if (APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS > 0)
            {
                $objDate = new TDateTime(time());
                $objDateZero = new TDateTime();
                $objDateZero->setZero();
                $objDate->subtractDays(APP_DATAPROTECTION_CONTACTS_ANONYMIZEDATAAFTERDAYS);
                $objContacts = new TSysContacts();
                // $objContacts->select(array(TSysModel::FIELD_ID)); ==> select all, otherwise we can't save
                $objContacts->find(TSysContacts::FIELD_LASTCONTACT, $objDateZero, COMPARISON_OPERATOR_NOT_EQUAL_TO, '', TP_DATETIME, LOGICAL_OPERATOR_AND); //exclude 0 dates
                $objContacts->find(TSysContacts::FIELD_LASTCONTACT, $objDate, COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO, '', TP_DATETIME, LOGICAL_OPERATOR_AND);
                $objContacts->find(TSysContacts::FIELD_COMPANYNAME, TSysContacts::VALUE_ANONYMOUS, COMPARISON_OPERATOR_NOT_EQUAL_TO, '', TP_STRING, LOGICAL_OPERATOR_AND); //skip records that are already anonymized. I picked a field that is NOT encrypted!
                $objContacts->loadFromDB();

                $objContacts->clearFind(); //we want to use the same query for saving, but exclude the find()
                while($objContacts->next())
                {
                    $objContacts->anonymizeData();
                    $objContacts->saveToDB();
                }

            }
        }


        //====deleting logfiles older than a year
        echo 'deleting old logfiles<br>';
        logCronjob(__FILE__.': '.__LINE__,'deleting old logfiles (older than a year)');    
        $arrLogFiles = getFileFolderArray(APP_PATH_LOGFILES, true, false);
        foreach($arrLogFiles as $sLogFile)
        {
            if (filemtime(APP_PATH_LOGFILES.DIRECTORY_SEPARATOR.$sLogFile) < (time() - YEAR_IN_SECS)) //older than a year
            {
                if(!rmdirrecursive(APP_PATH_LOGFILES.DIRECTORY_SEPARATOR.$sLogFile))//delete dir
                {
                    logCronjob(__FILE__.': '.__LINE__,'error occured deleting logfile dir: '.APP_PATH_LOGFILES.DIRECTORY_SEPARATOR.$sLogFile);
                    echo 'error occured deleting logfile (details in cronjob logfile)'.'<br>';                
                }
            }
        } 


        //====look for important directories than cannot be accessed via browser (like logfiles)
        echo 'checking restrictions to public directories<br>';
        logCronjob(__FILE__.': '.__LINE__,'creating htaccess files');
        
        $arrHtaccessDirs = array(APP_PATH_LOGFILES, 
                APP_PATH_CMS_BACKUPS,
                APP_PATH_CMS_CACHE,
                APP_PATH_LANGUAGES,
                APP_PATH_CMS_TEMPLATES,
                APP_PATH_LIBRARIES,
                APP_PATH_CMS_CLASSES, 
                APP_PATH_CMS_CONFIGS,
        );

        $arrIndexDirs = array(
                APP_PATH_UPLOADS,
        );
        
        //place htaccess files that restict everything
        foreach ($arrHtaccessDirs as $sHtaccesDir)
        {
            if (!createHtaccessFile($sHtaccesDir))
            {
                echo 'error in creating restrictions<br>';
                logCronjob(__FILE__.': '.__LINE__,'error creating htaccess file in directory: '.$sHtaccesDir);        
            }
        }


        //place index.php files to avoid directory listing
        foreach ($arrIndexDirs as $sIndexDir)
        {
            if (!createIndexFile404InDir($sIndexDir, true))
            {
                echo 'error in creating index.php\'s<br>';
                logCronjob(__FILE__.': '.__LINE__,'error creating index file in directory: '.$sIndexDir);        
            }            
        }    


        //==== cronjob of modules
        echo 'executing cronjobs modules<br>';
        logCronjob(__FILE__.': '.__LINE__,'executing cronjobs modules');

        $arrSysModules = getModuleFolders();
        $sTempModClass = '';
        foreach($arrSysModules as $sMod)
        {
            // $sTempModClass = '\dr\modules\\'.$sMod.'\\'.$sMod; -->replaced 13-11-2020
            $sTempModClass = getModuleFullNamespaceClass($sMod);
            $objCurrMod = new $sTempModClass; 

            //regular cronjob
            if ($objCurrMod->handleCronJob())
            {
                logCronjob(__FILE__.': '.__LINE__, 'handle cronjob module: '.$sMod);
            }
            else
            {
                logCronjob(__FILE__.': '.__LINE__, 'error occured executing cronjob in module: '.$sMod);
                echo 'error occured executing cronjob in module: '.$sMod.'<br>';
            }

            //cron job demo mode
            if (APP_DEMOMODE) //only in demo mode
            {
                if (method_exists($objCurrMod, 'handleCronJobDemoMode'))
                {
                    if ($objCurrMod->handleCronJobDemoMode())
                    {
                        logCronjob(__FILE__.': '.__LINE__, 'handle handleCronJobDemoMode() module: '.$sMod);
                    }
                    else
                    {
                        logCronjob(__FILE__.': '.__LINE__, 'error occured executing handleCronJobDemoMode() in module: '.$sMod);
                        echo 'error occured executing handleCronJobDemoMode() in module: '.$sMod.'<br>';
                    }
                }
            }

        }
        //==== END cronjob of modules



        //==== cleanup rate limiter files
        echo 'clean up old rate limiter files<br>';
        logCronjob(__FILE__.': '.__LINE__,'cleanup rate limiter files');

        //per day
        $arrRLFilesDay = array();
        $arrRLFilesDay = getFileFolderArray(APP_PATH_CMS_CACHE_RATELIMITER_PERDAY, false, true);
        foreach($arrRLFilesDay as $sFile)
        {
            if (filemtime(APP_PATH_CMS_CACHE_RATELIMITER_PERDAY.DIRECTORY_SEPARATOR.$sFile) < (time() - MONTH_IN_SECS)) //files used last more than 30 days ago
            {
                logCronjob(__FILE__.': '.__LINE__,'delete ratelimiter file: '.APP_PATH_CMS_CACHE_RATELIMITER_PERDAY.DIRECTORY_SEPARATOR.$sFile. ' (dated: '.date('Y-m-d', filemtime(APP_PATH_CMS_CACHE_RATELIMITER_PERDAY.DIRECTORY_SEPARATOR.$sFile)).')');            
                unlink(APP_PATH_CMS_CACHE_RATELIMITER_PERDAY.DIRECTORY_SEPARATOR.$sFile);
            }
        }

        //per second
        $arrRLFilesSecs = array();
        $arrRLFilesSecs = getFileFolderArray(APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND, false, true);
        foreach($arrRLFilesSecs as $sFile)
        {
            if (filemtime(APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND.DIRECTORY_SEPARATOR.$sFile) < (time() - MONTH_IN_SECS)) //files used last more than 30 days ago
            {
                logCronjob(__FILE__.': '.__LINE__,'delete ratelimiter file: '.APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND.DIRECTORY_SEPARATOR.$sFile. ' (dated: '.date('Y-m-d', filemtime(APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND.DIRECTORY_SEPARATOR.$sFile)).')');
                unlink(APP_PATH_CMS_CACHE_RATELIMITER_PERSECOND.DIRECTORY_SEPARATOR.$sFile);
            }
        }    
        //==== end: cleanup rate limiter files

        
        //==== write date to config file
        $objConfig->setAsInt('APP_CRONJOB_LASTEXECUTED', time());
        $objConfig->setAsBool('APP_CRONJOB_ISEXECUTING', false);
        $objConfig->saveFile(APP_PATH_CMS_CONFIGFILE_APPLICATION);
        //==== end: write date to config file    
        

        logCronjob(__FILE__.': '.__LINE__, '=== end of Cronjob.php ===');
        echo 'cronjob done '.date('d-m-Y H:i').'h .<br>';    
        //end: actually doing the cronjob        
    }
    else
    {
        echo 'CALLED CRONJOB!!! Another cronjob is currently being executed!';
    }

    //============ RENDER de templates
    $sHTMLContentMain = ob_get_contents();
    ob_end_clean();  

    $sContentsPage = '';
    $sContentsPage = renderTemplate(APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withoutmenu.php', get_defined_vars());

    echo $sContentsPage;


?>

