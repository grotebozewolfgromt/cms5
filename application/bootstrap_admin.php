<?php
/**
 * BOOTSTRAP_CMS
 * =============
 * bootstrap file that includes the (parent) bootstrap
 * include this file if you want the nessary additions for the cns like extra libraries , the selected site etc
 * 
 * this is a separate bootstrap only for the cms, so the (parent) bootstrap can be included for websites without the overhead of the CMS stuff
 * 
 * 9 april 2019 created Dennis Renirie
 * 27 apil onderverdeling tussen bootstrap_cms en bootstrap_cms_auth
 * 
 */
        use dr\modules\Mod_Sys_Modules\models\TSysModules;
        use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
    
	
        include_once(__DIR__.DIRECTORY_SEPARATOR.'bootstrap.php');          
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms.php');          
        include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_cms_url.php');          
        
        //================== DEFINING CMS CONSTANTS (and defaults) ===================        

        //defining translation files
            $objTranslationCMS = new dr\classes\locale\TTranslation();//will be autoloaded when needed
            $objTranslationCMS->setFileName(APP_PATH_LANGUAGES.DIRECTORY_SEPARATOR.'cms_translation_'.APP_LOCALE_DEFAULT.'.csv');        


        //================== CHECK INSTALLATION ===================   
        /* the installer is pretty secure, so we disable the message below
        if (is_dir(APP_PATH_CMS_INSTALLER)) //dir exists, assuming the framework isn't installed yet
        {
            if ((APP_DEBUGMODE === false) && (APP_INSTALLER_ENABLED === false)) //ignore when in debug-mode
            {
                echo '<h1>'.APP_APPLICATIONNAME.' installer found!</h1><br>';
                echo 'You need to remove the installer first before you can use '.APP_APPLICATIONNAME.'.<br>';
                echo 'The installer allows you (and everybody else) to install and remove data without a password or any other protection.';
                echo '<br>';
                echo '<h2>If you have NOT installed '.APP_APPLICATIONNAME.' yet</h2>';
                echo '<ol>';
                echo '<li>Install '.APP_APPLICATIONNAME.'. <a href="'.APP_URL_ADMIN_INSTALLERSCRIPT.'">click here to install</a></li>';
                echo '<li>Remove the "installer" directory</li>';
                echo '</ol>';        
                echo '<h2>If you already have installed '.APP_APPLICATIONNAME.'</h2>';
                echo '<ol>';
                echo '<li>You need to remove the "installer" directory, and this message will dissappear</li>';
                echo '<li>If you';
                echo '  <ul>';
                echo '      <li>don\'t care about your data being deleted by everybody with an internet connection</li>';
                echo '      <li>don\'t care about your hosting account being abused by hackers</li>';
                echo '      <li>don\'t care about your hosting account shut down by your hosting provider because of abuse</li>';
                echo '      <li>like get hacked yourself</li>';
                echo '      <li>suffer from brain damage</li>';
                echo '  </ul>';
                echo 'you can ignore these warnings and <a href="'.APP_ADMIN_FIRSTPAGECONTROLLER.'">log in here</a><br>';
                echo 'But that won\'t make this message dissappear</li>';
                echo '</ol>';
                echo '<br>';
    
                // echo '<br>';
                
                //header('Location: '.APP_URL_ADMIN_INSTALLSCRIPT);
                die();
            }
        }
        */
        

        //================== RATE LIMIT CMS ======================

        if (APP_ADMIN_RATELIMITER_ENABLE)
            rateLimiter(APP_PATH_CMS_CACHE_RATELIMITER_PERDAY, getIPAddressClient('_'), APP_ADMIN_RATELIMITER_REQUESTS, APP_ADMIN_RATELIMITER_PERSECS, true);

        //================== CMS5 MODULES ==========================
            
            //the module array is called $arrSysModules AND NOT $arrModules due to compatibility with cms2/4 (those modules are in $arrModules)
            // $arrSysModules = array(); //loaded from database (installed modules are inserted in db), if loaded from file system: getModuleFolders(); --> removed 13-11-2020 it wasn't used anywhere
            
            //DEFINE MODULE NAME
            define('APP_ADMIN_CURRENTMODULE', getModuleFromURL());

            //DEFINE MODULE OBJECT
            $objCurrentModule = null;
            if (APP_ADMIN_CURRENTMODULE != '')
            {   
                $sTempModClass = '';
                $sTempModClass = getModuleFullNamespaceClass(APP_ADMIN_CURRENTMODULE);
                $objCurrentModule = new $sTempModClass;                
                unset($sTempModClass);
            }
            
            
            //==== LOAD CMS 5 modules from database  ===> 15-08-2025 replaced by faster cached menu from TSysCMSMenu            
            /*
            // starttest('loadmodulesdb');
            $objSysModulesDB = new TSysModules();
            $objTempModCat = new TSysModulesCategories();                            
            $objSysModulesDB->select(array(TSysModules::FIELD_NAMEINTERNAL, TSysModules::FIELD_NAMENICETRANSDEFAULT, TSysModules::FIELD_VISIBLECMS, TSysModules::FIELD_ICONSVG));
            $objSysModulesDB->select(array(TSysModulesCategories::FIELD_NAME), $objTempModCat);
            $objSysModulesDB->selectAlias(TSysModulesCategories::FIELD_ID, 'iCategoryID', $objTempModCat);                            
            $objSysModulesDB->sort(TSysModulesCategories::FIELD_POSITION, SORT_ORDER_ASCENDING, $objTempModCat::getTable());
            $objSysModulesDB->sort(TSysModules::FIELD_POSITION);
            $objSysModulesDB->loadFromDB(true);

            //put everything in an associative array:
            //category1
            //|-module1
            //|-module2
            //category2
            //|-module 3
            $arrCats = array();
            while($objSysModulesDB->next())
            {
                if ($objSysModulesDB->getVisibleCMS())
                {
                    //$arrCats[$objSysModulesDB->get(TSysModulesCategories::FIELD_NAME, $objTempModCat::getTable())][] = $objSysModulesDB->getNameInternal();
                    $arrCats[$objSysModulesDB->get(TSysModulesCategories::FIELD_NAME, $objTempModCat::getTable())][] = $objSysModulesDB->getRecordPointer();
                    // echo $objSysModulesDB->getNameInternal();
                }     
            }   
            // stoptest('loadmodulesdb');
            */
        //================== DEFINING ARRAYS FOR JS & CSS ===================   

            //$arrIncludeJS[] = //array of absolute paths with javascript files to include in views/templates
            $arrIncludeCSS[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'style.css';
            // $arrIncludeCSS[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'style.css';
            // $arrIncludeCSS[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-spinner'.DIRECTORY_SEPARATOR.'style.css';

            $arrIncludeJSDOMEnd[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-components-lib.js';
            $arrIncludeJSDOMEnd[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'dr-popover.js'; 
            // $arrIncludeJSDOMEnd[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js';
            $arrIncludeJSDOMEnd[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-spinner'.DIRECTORY_SEPARATOR.'dr-icon-spinner.js';

            $arrIncludeJSDOMEnd[] = APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'lib_footer.js'; 
            // $arrIncludeJSDOMEnd[] = APP_PATH_CMS_VIEWS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'cms_footer.js'; //disabled because of bug in tabsheets

            
        //================= MISC =================================
            $arrTabsheets = array(); //declare tabsheets array
                 
?>