<?php
namespace dr\classes\patterns;

use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
use dr\modules\Mod_Sys_Settings\models\TSysSettings;
use dr\classes\models\TTableVersions;
use dr\classes\models\TSysTableVersions;
use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;

/**
 * Description of TModuleAbstract
 *
 * This is the business logic of a module
 * The idea behind this class is that you can ask certain properties, like: is it a system module.
 * THE CLASS IS ONLY INSTANTIATED WHEN USED SPECIFICALLY!
 * So it is NOT instantiated when displaying a list of modules in the cms.
 * This is done to keep the (system performance) costs down: no unnessary classes instantiated, only when absolutely needed
 * 
 * Don't confuse this class with TSysModules, which represents the database model of modules
 * 
 * ========================
 * MODULE TYPE
 * ========================
 * There are different types of modules: a regular module, a pagebuilder module and a system module
 * 
 * 
 * ========================
 * START
 * ========================
 * the first hook of a module to the system is always the index.php. This file is ALWAYS present to access a module!
 * index.php should header redirect to proper controller
 * 
 * 12 juli 2012: TModule: getPathLocalLanguages() toegevoegd
 * 13 juli 2012: TModule: translation object toegevoegd
 * 17 juli 2012: TModule: loadLibrary() toegevoegd
 * 17 juli 2012: TModule: getPathLocalLanguageFiles
 * 18 juli 2012: TModule: loadTranslation() toegevoegd
 * 23 juli 2012: TModule: tabsheets object toegevoegd
 * 25 juli 2012: TModule: rename getPathLocalViewsSystem() -> getPathLocalViewsCMS()
 * 27 sept 2012: TModule: aanpassingen voor nieuwe manier met prepared statement
 * 2 mrt 2014: TModule: added install(), uninstall() en upgrade()
 * 14 mrt 2014: TModule: tabsheets verwijderd (zitten standaard in de controller, dus ook de controller van deze module)
 * 31 jul 2014: TModule: install() aangepast: roept de install van TRecordList aan
 * 31 jul 2014: TModule: uninstall() aangepast: roept de install van TRecordList aan
 * 1 aug 2014: TModule: update() aangepast: roept de uninstall van TRecordList aan
 * 12 aug 2014: TModule: viewsCMS verwijderd
 * 9 mei 2019: aanpasssingen voor de cms 4 reboot
 * 9 mei 2019: TModule -> TModuleAbstract
 * 25 nov 2023: 	
 *      getAuthor() is not abstract anymore 
 *      getVersion() is not abstract anymore
 *      getIsVisible() ==> isVisibleCMS
 *      abstract getSettingsURLCMS() added
 *      add PERM_OP_CHANGESETTINGS
 * 
 * @author d. renirie
 */
abstract class TModuleAbstract
{   
    //default module categories
    const CATEGORYDEFAULT_SYSTEM = 'System management';
    const CATEGORYDEFAULT_WEBSITE = 'Website';
    const CATEGORYDEFAULT_TOOLS = 'Tools';
    const CATEGORYDEFAULT_POS = 'POS';

    //permission category (create your own in the child class) in authorisation resource (i.e. Mod_Books/books/view)
    //const PERM_CAT_BOOKS   = 'books'; --> when it concerns books, but it can also be users, categories or any other database record
    
    //default permissions for authorisation system (add your own in the child class)
    //(these are just here out of consistency with the rest of the module operations)
    const PERM_OP_DELETE        = AUTH_OPERATION_DELETE;
    const PERM_OP_CREATE        = AUTH_OPERATION_CREATE;
    const PERM_OP_CHANGE        = AUTH_OPERATION_CHANGE;
    const PERM_OP_VIEW          = AUTH_OPERATION_VIEW;
    const PERM_OP_CHECKINOUT    = AUTH_OPERATION_CHECKINOUT;
    const PERM_OP_LOCKUNLOCK    = AUTH_OPERATION_LOCKUNLOCK;
    const PERM_OP_CHANGEORDER   = AUTH_OPERATION_CHANGEPOSITION;
    // const PERM_OP_CHANGESETTINGS = AUTH_OPERATION_CHANGESETTINGS;

    //module types
    const MOD_TYPE_SYSTEM       = 'system'; //same as regular, but you can't remove a system module (because it is an integral part of the framework)
    const MOD_TYPE_REGULAR      = 'regular'; //normal module
    const MOD_TYPE_PAGEBUILDER  = 'pagebuilder'; //page builder

    //array keys menu items
    const AK_CMSMENUITEM_CONTROLLER = 'controller';//controller (=url in module directory)
    const AK_CMSMENUITEM_PERMISSIONCATEGORY = 'permissioncategory';
    const AK_CMSMENUITEM_NAMEDEFAULT = 'namedefault';
    const AK_CMSMENUITEM_SVGICON = 'svgicon';
    const AK_CMSMENUITEM_ISVISIBLEMENU = 'isvisiblemenu'; //boolean: is it visible in cms menu on left side
    const AK_CMSMENUITEM_ISVISIBLETOOLBAR = 'isvisibletoolbar'; //boolean: is it visible in cms toolbar on top


    // public function __construct()
    // {}
    

    
    /**
     * This function is called to update the module for all websites
     * (i.e. creating database tables, directories, files etc)
     * 
     * @var array $arrPreviousDependenciesModelClasses
     * @return bool success ?
     */
    public function updateModels($arrPreviousDependenciesModelClasses, TSysTableVersions $objTableVersionsFromDB)
    {
    	$arrModels = $this->getModelObjects();
    	
    	//call update on each mode
    	if ($arrModels)
    	{
	    	foreach($arrModels as $objModel)
	    	{
	    		if (!$objModel->refactorDB($arrPreviousDependenciesModelClasses, $objTableVersionsFromDB))
	    			return false;
	    	}
    	}
    	
    	return true;
    }    

    /**
     * 
     * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
     * 
     * @param array $arrPreviousDependenciesModelClasses array with class names
     * @return bool succes?
     */
    private function installModels($arrPreviousDependenciesModelClasses = null)
    {
    	$arrModels = $this->getModelObjects();
                
    	//call install on each mode
    	if ($arrModels)
    	{
	    	foreach($arrModels as $objModel)
	    	{	    
                if ($objModel) //can be empty array
                {
                    if ($objModel instanceof TSysModel)
                    {
    	    		    if (!$objModel->install($arrPreviousDependenciesModelClasses))
	        			    return false;
                    }
                    else
                        return false;
                }
	    	}
    	}
    	
    	return true;
    }

    
    /**
     * uninstall all tables from database
     * 
     * @param array $arrPreviousDependenciesModelClasses
     * @return boolean succes?
     */
    private function uninstallModels($arrPreviousDependenciesModelClasses = null)
    {
    	$arrModels = $this->getModelObjects();
        $arrModels = array_reverse($arrModels);//reverse because of dependencies
    	 
    	//call install on each mode
    	if ($arrModels)
    	{
    		foreach($arrModels as $objModel)
    		{
    			if (!$objModel->uninstallDB($arrPreviousDependenciesModelClasses))
    				return false;
    		}
    	}
    	 
    	return true;
    }

    /**
     * returns if a module is a framework-system module
     * when true, the module can not be removed from the framework
     *
     * when not explicitly set, the default value is false!
     *
     * @return bool
     */    
    public function getIsSystemModule()
    {
        return ($this->getModuleType() == TModuleAbstract::MOD_TYPE_SYSTEM);
    }
    
    /**
     * returns list of instantiated models that are used
     * 
     * THE ORDER IN WHICH IT RETURNS IS IMPORTANT
     * First the dependencies, than the objects itself
     *
     * system modules are done in the system, so they return: array()
     * 
     * @return array 1d with TSysModel objects
     */    
    abstract public function getModelObjects();
    
      
   

    /**
     * returns the type of the module
     * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
     * if you don't know, return MOD_TYPE_REGULAR
     * 
     * @return string
     */
    abstract public function getModuleType();

    
    /**
     * return the (untranslated) module-category name.
     * this is the default module-category which is used to put the module in when installing the module
     * 
     * on the left side of the screen are categories displayed:
     * -WEBSITE (=category
     * |- blog (module)
     * |- web pages (module)
     * -WEBSHOP (=category)
     * |- product catalog (module)
     * |- vat (module)
     * |- invoices (module)
     * -SYSTEM (=category)
     * |- users (module)
     * |- languages (module)
     * 
     * for system modules, use const TModuleAbstract::CATEGORYDEFAULT_SYSTEM
     * 
     */
    abstract public function getCategoryDefault();
    
    /**
     * returns the tabsheets for this module
     *
     * when not overridden, it returns 'index' by default
     * 
     * specify array with filename, permission-category, name, description like this:
     *         return array(
     *                     array('list_posts.php', Mod_Blog::PERM_CAT_BLOG, 'blog posts', 'explanation about blog posts'),
     *                     array('list_authors.php', Mod_Blog::PERM_CAT_AUTHORS, 'blog authors', 'explanation about authors for blog posts')
     *                  )
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */   
    abstract public function getTabsheets();

    /**
     * returns the menu items for this module (shown on the left side of the screen in the CMS)
     * the items from this function are stored in TSysCMSMenu on module installation.
     * in TSysCMSMenu the user is able to edit, remove, move position these menu items
     * 
     * you can return and empty array when you don't want extra items under the module in the menu
     *
     * specify array with filename, permission-category, name, svg-icon like this:
     *         return array (
     *                      array   (
     *                                  AK_CMSMENUITEM_CONTROLLER => 'list_blog.php',
     *                                  AK_CMSMENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_BLOG,
     *                                  AK_CMSMENUITEM_NAMEDEFAULT => 'blog posts',
     *                                  AK_CMSMENUITEM_SVGICON => '<svg></circle></svg>',
     *                                  AK_CMSMENUITEM_ISVISIBLEMENU => true,
     *                                  AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
     *                              ), 
     *                      array   (
     *                                  AK_CMSMENUITEM_CONTROLLER => 'list_authors.php',
     *                                  AK_CMSMENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_AUTHORS,
     *                                  AK_CMSMENUITEM_NAMEDEFAULT => 'blog authors',
     *                                  AK_CMSMENUITEM_SVGICON => '<svg></circle></svg>'
     *                                  AK_CMSMENUITEM_ISVISIBLEMENU => true,
     *                                  AK_CMSMENUITEM_ISVISIBLETOOLBAR => false,
     *                              ), 
     *                      );
     *          
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */ 
    abstract public function getMenuItems();




    /**
     * returns the html for the help
     * @return string
     */
    //public function getHTMLHelp();

    /**
     * returns the html results for the search command
     * @return string
     */
    //abstract public function getHTMLQuickSearch($sSearchCommand);


    /**
     * return contribution of this module for the dashboard/today screen 
     * @return string
     */
    //abstract public function getHTMLDashboard();


    
    /**
     * this function deletes a module from the framework, wich means:
     * 1) delete database tables
     * 2) delete module directory (optional)
     * 3) delete rights in userrights table 
     * 4) delete registration in module table
     *
     * if a module is marked as system module, it can not be deleted
     * 
     * @param bool $bPreventDeletionSystemModules can not delete system modules if true
     * @param bool $bDeleteFromDisk delete module directory
     * @return bool
     */
    // public function deleteModuleFromFramework($bPreventDeletionSystemModules = true)
    public function uninstallModule($bPreventDeletionSystemModules = true, $bDeleteFromDisk = false)
    {
        $bSuccess = true;

        error_log('TModuleAbstract->uninstallModule(): start uninstalling module '.get_class_short($this));

        if (($this->getIsSystemModule()) && ($bPreventDeletionSystemModules))
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'TModuleAbstract: This is a system module. System modules can not be removed.', $this);
            return false;
        }
        else
        {
            //delete permissions
            $objPermissions = new TSysCMSPermissions();
            if (!$objPermissions->deletePermissionsForModule(get_class_short($this)))
            {
                $bSuccess = false;
                error_log('TModuleAbstract->uninstallModule(): $this->deletePermissionsForModule() failed for'.$this->getNameDefault());
            }
            unset($objPermissions);

            //delete settings from database
            $objSettings = new TSysSettings();
            if (!$objSettings->deleteSettingsDBForModule(get_class_short($this)))
            {
                $bSuccess = false;
                error_log('TModuleAbstract->uninstallModule(): $this->deleteSettingsDBForModule() failed for '.$this->getNameDefault());
            }
            unset($objSettings);

            //delete module registration in database
            $objSysModules = new TSysModules();
            $objSysModules->find(TSysModules::FIELD_NAMEINTERNAL, get_class_short($this));
            $objSysModules->deleteFromDB(true);
            unset($objSysModules);

            //delete database table
            if (!$this->uninstallModels())
            {
                $bSuccess = false;
                error_log('TModuleAbstract->uninstallModule(): $this->uninstallModels() failed for '.$this->getNameDefault());
            }


            //delete directory
            if ($bSuccess && $bDeleteFromDisk)
            {
                if (!rmdirrecursive(APP_PATH_MODULES.DIRECTORY_SEPARATOR.get_class_short($this)))
                {
                    $bSuccess = false;
                    error_log('TModuleAbstract->uninstallModule(): rmdirrecursive() failed for '.$this->getNameDefault());
                }
            }


            //remove module from menu
            $objMenu = new TSysCMSMenu();
            if (!$objMenu->removeMenuItemsForModuleDB($this))
            {
                $bSuccess = false;
                error_log('TModuleAbstract->uninstallModule(): deleteFromDB() failed for '.$this->getNameDefault());
            }

        }

        return $bSuccess;
    }

    /**
     * Install module in framework
     *
     * @return bool
     */
    public function installModule()
    {
        $iCatID = 0;

        error_log('start install module: '. get_class_short($this));

        
        //==== request default category to put the module in 
        /*
        $objTempCat = new TSysModulesCategories();  
        $objTempCat->find(TSysModulesCategories::FIELD_NAME, $this->getCategoryDefault());
        $objTempCat->limitOne(); //we need just one record to be returned
        if (!$objTempCat->loadFromDB())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'TSysModules: $objTempCat->loadFromDB() failed in TModuleAbstract->installModule()');
            return false;
        }

        //if default category exists
        if ($objTempCat->count()> 0) 
        {
            $iCatID = $objTempCat->getID();
        }
        else //if default category NOT EXISTS
        {
            //take first record in table
            $objTempCat->newQuery();
            $objTempCat->clear();
            $objTempCat->limitOne();

            if (!$objTempCat->loadFromDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'TSysModules: $objTempCat->loadFromDB() failed in TModuleAbstract->installModule()');
                return false;
            }       
            $iCatID = $objTempCat->getID();    
        }

        unset($objTempCat);
        */
        
        //==== add module registration to database
        $objTempNewMod = new TSysModules();

        //if already exist: don't install
        if ($objTempNewMod->recordExistsTableDB(TSysModules::FIELD_NAMEINTERNAL, get_class_short($this)))
            return true;//EXIT

        error_log('add module to module db table: '.get_class_short($this));
        $objTempNewMod->newRecord();
        $objTempNewMod->setNameInternal(get_class_short($this));
        $objTempNewMod->setModuleType($this->getModuleType());
        // $objTempNewMod->setCategoryID($iCatID);
        $objTempNewMod->setVisibleCMS($this->isVisibleCMS());
        $objTempNewMod->setVisibleFrontEnd($this->isVisibleFrontEnd());
        $objTempNewMod->setNameDefault($this->getNameDefault());
        $objTempNewMod->setIconSVG($this->getIconSVG());
        if (!$objTempNewMod->saveToDB())
            return false;


        //==== create models
        error_log('creating models for: '. get_class_short($this));
        if (!$this->installModels())
            return false;



        //add permissions to database table
        $objPermissions = new TSysCMSPermissions();
        if (!$objPermissions->createPermissionsForModule(get_class_short($this)))
        {   
            error_log('create permissions for module '.get_class_short($this).' FAILED!!');
            return false;
        }
        unset($objPermissions);


        //add settings to database
        $objSettings = new TSysSettings();
        if (!$objSettings->createSettingsDBByBLArr(get_class_short($this), $this->getSettingsEntries()))
        {   
            error_log('create settings for module '.get_class_short($this).' FAILED!!');
            return false;
        }
        unset($objSettings);        

        //add module to CMS menu
        $objMenu = new TSysCMSMenu();
        if (!$objMenu->createMenuItemsForModuleDB($this))
        {
            error_log('creating menu items for '.get_class_short($this).' FAILED!!');
            return false;
        }

        return true;
    }
  

    /**
     * generate data to put in database
     * 
     * for example: languages:
     * you want to install a default language in install process
     * and propagate all languages in the world in the propagate function
     * 
     * @return bool true=ok, false=error
     */
    public function propagateDataModule()
    {
    	$arrModels = $this->getModelObjects();
                
    	//call install on each mode
    	if ($arrModels)
    	{
	    	foreach($arrModels as $objModel)
	    	{	    
                if ($objModel) //can be empty array
                {
                    if ($objModel instanceof TSysModel)
                    {
    	    		    if (!$objModel->propagateData())
	        			    return false;
                    }
                    else
                        return false;
                }
	    	}
    	}
    	
    	return true;
    }

    public function getNameInternal()
    {
        return get_class_short($this);
    }

    /**
     * returns svg icon
     * 
     * example: return '<svg><path="blabla"></svg>';
     * 
     * please override this to return your own icon
     * 
     * @return string
     */
    public function getIconSVG()
    {
        return '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                    <path d="M31.89,8.75a.85.85,0,0,0-.17-.25l0,0a1.28,1.28,0,0,0-.22-.17L16.48.12a1,1,0,0,0-1,0L.57,8.28s0,.05-.08.06a1.3,1.3,0,0,0-.23.18,1.25,1.25,0,0,0-.08.12,1,1,0,0,0-.12.23s0,0,0,.06a.92.92,0,0,0,0,.16s0,.06,0,.09V22.81A.86.86,0,0,0,0,23l0,.14a1,1,0,0,0,.48.59l15,8.18.06,0,.08,0A1,1,0,0,0,16,32a1.09,1.09,0,0,0,.35-.07l.07,0,.06,0,15-8.18a1,1,0,0,0,.52-.88V9.18a1,1,0,0,0-.07-.34A.36.36,0,0,0,31.89,8.75ZM15,29.32,2,22.22V10.87L15,18Zm1-13.1-12.91-7L16,2.14l12.91,7Zm14,6-13,7.1V18l13-7.09Z" stroke="currentColor"/>
                </svg>';
    }

    /**
     * handle all the actions when a cron job is called
     * 
     * Cronjob is an action that is automaticall performed once a day or once a 
     * week, without the user triggerig it manually
     * @return boolean true if successful
     */
    abstract public function handleCronJob();
    

    /**
     * the permissions that this module uses
     * 
     * Based on what this function returns, permissions are created 
     * when installing this module and deleted when uninstalling.
     * these permissions are used for the auth() function
     * 
     * if you forget to add a permission this way, the auth() function
     * will return false when you request authorisation
     * 
     * example with a module to register 'books' and 'authors':
     * return array(
     *       TModuleAbstract::PERM_CAT_BOOKS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                 TModuleAbstract::PERM_OP_DELETE,
     *                                                 TModuleAbstract::PERM_OP_CHANGE,
     *                                                 TModuleAbstract::PERM_OP_CREATE,
     *                                                 TModuleAbstract::PERM_OP_LOCKUNLOCK,
     *                                                 TModuleAbstract::PERM_OP_CHECKINOUT)
     *       TModuleAbstract::PERM_CAT_AUTHORS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                 TModuleAbstract::PERM_OP_DELETE,
     *                                                 TModuleAbstract::PERM_OP_CHANGE)
     *      ) ;
     * 
     * 
     * @return array 2d
     */
    abstract public function getPermissions();

    /**
     * the permissions of a module that are allowed in the demo mode
     * This function returns the same array structure as getPermissions(),
     * BUT has ONLY the permissions in it that are allowed in the demo mode.
     * 
     * The easiest way to create this function is to copy/paste the array of getPermissions()
     * and delete the items you dont need
     * 
     * example with a module to register 'books' and 'authors':
     * return array(
     *       TModuleAbstract::PERM_CAT_BOOKS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                 // TModuleAbstract::PERM_OP_DELETE, dont allow deletion
     *                                                 TModuleAbstract::PERM_OP_CHANGE,
     *                                                 TModuleAbstract::PERM_OP_CREATE,
     *                                                 TModuleAbstract::PERM_OP_LOCKUNLOCK,
     *                                                 TModuleAbstract::PERM_OP_CHECKINOUT)
     *       TModuleAbstract::PERM_CAT_AUTHORS => array (TModuleAbstract::PERM_OP_VIEW,
     *                                                // TModuleAbstract::PERM_OP_DELETE, dont delete
     *                                                 //TModuleAbstract::PERM_OP_CHANGE dont change
     *                                                  )
     *      ) ;
     * 
     * 
     * @return array 2d
     */
    abstract public function getPermissionsDemoModeAllowed();



    /**
     * return an array with all settings for the cms
     *
     * this will return an array in this format:
     *         return array(
     *       SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER => array ('0', TP_BOOL) //default, type
     *       );   
     * 
     * @return array
     */
    abstract public function getSettingsEntries();

    /**
     * who made it?
     * @return string
     */
    abstract public function getAuthor();
  
    /**
     * versi0n 1,2,3 etc needed for database refactoring.
     * when you are doing a database structur change, increment the version number by 1
     * (we use integers for fast, easy and reliable comparing between version numbers)
     * 
     * @return int
     */
    abstract public function getVersion();


    /**
     * is module visible in CMS menus?
     *
     * @return boolean 
     */
    abstract public function isVisibleCMS();

    /**
     * is module visible in menus in the frontend of the site?
     *
     * @return boolean 
     */
    abstract public function isVisibleFrontEnd();    

    /**
     * get the default (non-internal) name for the module.
     * This is de DEFAULT ENGLISH translation as it is passed to the
     * transm() function
     *
     * @return string
     */
    abstract public function getNameDefault();


    /**
     * returns the url to the settings page in the cms
     * when '' is returned the setting screen is assumed not to exist
     * 
     * @return string
     */
    abstract public function getURLSettingsCMS();


    /**
     * returns the url of the website of the author
     * when '' is returned the site is assumed not to exist
     * 
     * @return string
     */
    abstract public function getURLAuthor();

    /**
     * returns the url of the support part of the website of the author
     * when '' is returned the url is assumed not to exist
     * 
     * @return string
     */
    abstract public function getURLSupport();


    /**
     * returns a subdirectory for the uploadfilemanager to put files in
     * return '' when root upload dir is ok, or you don't want to use the uploadfilemanager
     * 
     * @return string
     */
    abstract public function getUploadDir();    

}
?>
