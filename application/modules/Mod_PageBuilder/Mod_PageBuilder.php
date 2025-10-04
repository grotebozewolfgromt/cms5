<?php
namespace dr\modules\Mod_PageBuilder;

use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_PageBuilder\models\TPageBuilderBlocks;
use dr\modules\Mod_PageBuilder\models\TPageBuilderStructures;
use dr\modules\Mod_PageBuilder\models\TPageBuilderElements;

/**
 * Description of Mod_Bob (Bob the pagebuilder)
 * 
 * Module for pagebuilder
 * 
 * @author drenirie
 * 
 */
class Mod_PageBuilder extends TModuleAbstract
{
    const PERM_CAT_WEBPAGES = 'webpages';
    const PERM_CAT_STATUSSES = 'statusses';
    const PERM_CAT_SETTINGS = 'settings';

    const PERM_OP_VIEW_OWN = 'view_own';
    const PERM_OP_DELETE_OWN = 'delete_own';
    const PERM_OP_CHANGE_OWN = 'change_own';
    const PERM_OP_CHANGE_AUTHOR = 'change_author';
    const PERM_OP_CHANGE_WEBSITE = 'change_website';
    const PERM_OP_CHANGE_VISIBILITY = 'change_visibility';
    const PERM_OP_CHANGE_PUBLISHDATE = 'change_publishdate';
    const PERM_OP_CHANGE_PAGEPASSWORD = 'change_password';
    const PERM_OP_CHANGE_STATUS = 'change_status';
    const PERM_OP_CHANGE_URLSLUG = 'change_urlslug';
    const PERM_OP_CHANGE_CANONICAL = 'change_canonical';
    const PERM_OP_CHANGE_301REDIRECT = 'change_301redirect';

    /**
     * returns the type of the module
     * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
     * if you don't know, return MOD_TYPE_REGULAR
     * 
     * @return string
     */    
    public function getModuleType() 
    {
        return TModuleAbstract::MOD_TYPE_PAGEBUILDER; 
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
    public function getModelObjects() 
    {
        return array(
            // new TPageBuilderStructures(),
            // new TPageBuilderBlocks(), 
            // new TPageBuilderElements()
        );
        // return array();
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
            array('list_pages', Mod_PageBuilder::PERM_CAT_WEBPAGES, 'Pages', 'Manage webpages'),           
            array('list_statusses', Mod_PageBuilder::PERM_CAT_STATUSSES, 'Statusses', 'Manage webpage statusses'),   
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
    public function getMenuItems()
    {
        return array();
    }   



    public function getCategoryDefault()
    {
        return TModuleAbstract::CATEGORYDEFAULT_WEBSITE;
    }

    /**
     * handles cron job
     *
     * @return bool
     */
    public function handleCronJob() 
    {
        return true;
    }

    /**
     * return permissions array
     *
     * @return array
     */
    public function getPermissions()
    {
        return array(
            Mod_PageBuilder::PERM_CAT_WEBPAGES => array (   
                                                            Mod_PageBuilder::PERM_OP_VIEW,
                                                            Mod_PageBuilder::PERM_OP_DELETE,
                                                            Mod_PageBuilder::PERM_OP_CHANGE,
                                                            Mod_PageBuilder::PERM_OP_CREATE,
                                                            Mod_PageBuilder::PERM_OP_VIEW_OWN,
                                                            Mod_PageBuilder::PERM_OP_DELETE_OWN,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_OWN,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_AUTHOR,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_WEBSITE,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_VISIBILITY,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_PUBLISHDATE,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_PAGEPASSWORD,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_STATUS,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_URLSLUG,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_CANONICAL,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_301REDIRECT,
                                                        ),                
            Mod_PageBuilder::PERM_CAT_STATUSSES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                            TModuleAbstract::PERM_OP_DELETE,
                                                            TModuleAbstract::PERM_OP_CHANGE,
                                                            TModuleAbstract::PERM_OP_CREATE
                                                        ),                                                                                   
            Mod_PageBuilder::PERM_CAT_SETTINGS => array (  TModuleAbstract::PERM_OP_VIEW,                                                            
                                                            TModuleAbstract::PERM_OP_CHANGE
                                                        )                                                                                     
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
        return array(
            Mod_PageBuilder::PERM_CAT_WEBPAGES => array (   
                                                            Mod_PageBuilder::PERM_OP_VIEW,
                                                            Mod_PageBuilder::PERM_OP_CHANGE,
                                                            Mod_PageBuilder::PERM_OP_CREATE,
                                                            Mod_PageBuilder::PERM_OP_VIEW_OWN,
                                                            Mod_PageBuilder::PERM_OP_DELETE_OWN,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_OWN,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_AUTHOR,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_WEBSITE,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_VISIBILITY,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_PUBLISHDATE,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_PAGEPASSWORD,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_STATUS,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_URLSLUG,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_CANONICAL,
                                                            Mod_PageBuilder::PERM_OP_CHANGE_301REDIRECT,
                                                        ),                
            Mod_PageBuilder::PERM_CAT_STATUSSES => array (  TModuleAbstract::PERM_OP_VIEW,
                                                            TModuleAbstract::PERM_OP_CREATE
                                                        ),                                                                                   
            Mod_PageBuilder::PERM_CAT_SETTINGS => array (  TModuleAbstract::PERM_OP_VIEW,                                                            
                                                        )                                                                                     
            ) ;       
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
        return 'Page builder';
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
        // return getURLModule($this->getNameInternal().'/settings'); //proof of concept
        return '';
    }


    /**
     * is module visible in CMS menus?
     *
     * @return boolean 
     */
    public function isVisibleCMS()
    {
        return true;
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
        return 'https://www.dexxterclark.com';
    }

    /**
     * returns the url of the support part of the website of the author
     * when '' is returned the url is assumed not to exist
     * 
     * @return string
     */
    public function getURLSupport()
    {
        return 'https://www.dexxterclark.com/business-inquiries';
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
        return '<svg viewBox="0 0 16 16" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
        <rect width="16" height="16" id="icon-bound" fill="none" />
        <path d="M14.706,4.206l-3.913-3.913C10.606,0.106,10.353,0,10.087,0H1v16h14V4.916C15,4.65,14.894,4.394,14.706,4.206z M13,14H3V2h7 v3h3V14z" />
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
        return 'webpages';
    }
}
