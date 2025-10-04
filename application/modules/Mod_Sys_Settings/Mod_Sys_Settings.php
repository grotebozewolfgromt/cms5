<?php
namespace dr\modules\Mod_Sys_Settings;

use dr\modules\Mod_Sys_Settings\models\TSysSettings;
use dr\classes\patterns\TModuleAbstract;


/**
 * Description of Mod_Sys_Settings
 * 
 * settings of the framework
 *
 * @author drenirie
 */
class Mod_Sys_Settings extends TModuleAbstract
{
    const PERM_CAT_USERSETTINGS = 'user settings';
    const PERM_CAT_GLOBALSETTINGS = 'global settings';
    // const PERM_CAT_THEMES = 'themes';
    const PERM_CAT_MAINTENANCE  = 'maintenance';
    const PERM_CAT_CMSMENU = 'cmsmenu';

    const PERM_OP_EXECUTE = 'execute';

    /**
     * returns the type of the module
     * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
     * if you don't know, return MOD_TYPE_REGULAR
     * 
     * @return string
     */    
    public function getModuleType() 
    {
        return TModuleAbstract::MOD_TYPE_SYSTEM; 
    }

    public function getModelObjects() 
    {
        return array();
    }

   /**
     * returns the tabsheets for this module
     *
     * dwhen not overridden, it returns index by default
     * 
     * specify array with filename, permission-category, description like this:
     *         return array(
     *                     array('overview_blog.php', Mod_Blog::PERM_CAT_BLOG, 'blog posts', 'explanation about blog posts'),
     *                     array('overview_authors.php', Mod_Blog::PERM_CAT_AUTHORS, 'blog authors', 'explanation about authors for blog posts')
     *                  )
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */   
    public function getTabsheets()
    {
        return array(
            array('settings_user', Mod_Sys_Settings::PERM_CAT_USERSETTINGS, 'User settings', 'settings that apply only to current user'),
            array('settings_global', Mod_Sys_Settings::PERM_CAT_GLOBALSETTINGS, 'Global settings', 'settings that apply to the whole system'),
            // array('settings_themes_overview', Mod_Sys_Settings::PERM_CAT_THEMES, 'Themes', 'View and change themes'),
            array('settings_maintenance', Mod_Sys_Settings::PERM_CAT_MAINTENANCE, 'Maintainance', 'Execute a cron job'),
            array('list_cmsmenu', Mod_Sys_Settings::PERM_CAT_MAINTENANCE, 'CMS Menu + toolbar', 'Edit menu and toolbar items of CMS')
        );
    } 

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
     *                                  TModuleAbstract::AK_MENUITEM_CONTROLLER => 'list_blog.php',
     *                                  TModuleAbstract::AK_MENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_BLOG,
     *                                  TModuleAbstract::AK_MENUITEM_NAMEDEFAULT => 'blog posts',
     *                                  TModuleAbstract::AK_MENUITEM_SVGICON => '<svg></circle></svg>'
     *                              ), 
     *                      array   (
     *                                  TModuleAbstract::AK_MENUITEM_CONTROLLER => 'list_authors.php',
     *                                  TModuleAbstract::AK_MENUITEM_PERMISSIONCATEGORY =>  Mod_Blog::PERM_CAT_AUTHORS,
     *                                 	TModuleAbstract::AK_MENUITEM_NAMEDEFAULT => 'blog authors',
     *                                  TModuleAbstract::AK_MENUITEM_SVGICON => '<svg></circle></svg>'
     *                              ), 
     *                      )
     *          
     * 
     * the tab names and descriptions are translated with the transm() function, so don't return translated tabnames and descriptions
     * 
     * @return array
     */  
    public function getMenuItems()
    {
        return array();
    }  

    public function getCategoryDefault()
    {
        return TModuleAbstract::CATEGORYDEFAULT_SYSTEM;
    }

    /**
     * handles cron job
     *
     * @return bool
     */
    public function handleCronJob() 
    {
        $bResult = true;

        //updating permissions
        error_log('updating settings');
        echo 'updating settings ... <br>';
        $objSettings = new \dr\modules\Mod_Sys_Settings\models\TSysSettings();
        if (!$objSettings->updateSettingsDB())
            $bResult = false;
        unset($objSettings);

        return $bResult;
    }

    /**
     * return permissions array
     *
     * @return array
     */
    public function getPermissions()
    {
        return array(
            Mod_Sys_Settings::PERM_CAT_USERSETTINGS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE
                                                ),
            Mod_Sys_Settings::PERM_CAT_GLOBALSETTINGS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CHANGE
                                                ),
            Mod_Sys_Settings::PERM_CAT_MAINTENANCE => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                Mod_Sys_Settings::PERM_OP_EXECUTE
                                                ),
            // Mod_Sys_Settings::PERM_CAT_THEMES => array (  
            //                                     TModuleAbstract::PERM_OP_VIEW,
            //                                     TModuleAbstract::PERM_OP_CHANGE
            //                                     )  
            Mod_Sys_Settings::PERM_CAT_CMSMENU => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                                TModuleAbstract::PERM_OP_CHANGEORDER,
                                            ),                                                                                                                             
            
            
            ) ;    
    }     

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
    public function getPermissionsDemoModeAllowed()
    {
        return array();      
    }

    /**
     * get the default (non-internal) name for the module.
     * This is de DEFAULT ENGLISH translation as it is passed to the
     * transm() function
     *
     * @return void
     */
    public function getNameDefault()
    {
        return 'settings';
    }     
    
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
    public function getSettingsEntries()
    {
        return array();
    }    

    /**
     * who made it?
     * @return string
     */
    public function getAuthor()
    {
        return 'Dennis Renirie';
    }

    /**
     * versi0n 1,2,3 etc needed for database refactoring.
     * when you are doing a database structur change, increment the version number by 1
     * (we use integers for fast, easy and reliable comparing between version numbers)
     * 
     * @return int
     */
    public function getVersion()
    {
         return 1;
    }

    /**
     * returns the url to the settings page in the cms
     * when '' is returned the setting screen is assumed not to exist
     * 
     * @return string
     */
    public function getURLSettingsCMS()
    {
        return '';
    }


    /**
     * is module visible in CMS menus?
     *
     * @return boolean 
     */
    public function isVisibleCMS()
    {
        return false;
    }


    /**
     * is module visible in menus in the frontend of the site?
     *
     * @return boolean 
     */
    public function isVisibleFrontEnd()
    {
        return false;
    }    

    /**
     * returns the url of the website of the author
     * when '' is returned the site is assumed not to exist
     * 
     * @return string
     */
    public function getURLAuthor()
    {
        return '';
    }

    /**
     * returns the url of the support part of the website of the author
     * when '' is returned the url is assumed not to exist
     * 
     * @return string
     */
    public function getURLSupport()
    {
        return '';
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
        return '<svg xmlns="http://www.w3.org/2000/svg"  viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>';
    }       

    /**
     * returns a subdirectory for the uploadfilemanager to put files in
     * return '' when root upload dir is ok, or you don't want to use the uploadfilemanager
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return '';
    }       
}
