<?php
namespace dr\modules\Mod_Sys_Security;

use dr\classes\patterns\TModuleAbstract;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of security module
 *
 * @author drenirie
 */
class Mod_Sys_Security extends TModuleAbstract
{
    const PERM_CAT_LOGINFLOODLOGS = 'Login Flood logs';
    const PERM_CAT_LOGINIPBLACKWHITELIST = 'Login IP black-whitelist';

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
            array('list_loginfloodlogs', Mod_Sys_Security::PERM_CAT_LOGINFLOODLOGS, 'Login flood logs', 'Log of the access attempts of the system.<br>This way you gain some security insight whether your system is possibly under attack or not.'),
            array('list_loginipblackwhitelist', Mod_Sys_Security::PERM_CAT_LOGINFLOODLOGS, 'Login IP black- &amp; whitelist', 'Blacklisted and whitelisted IP addresses for logging in.<br><b>Whitelisted</b> means that specified IP address is allowed to log in.<br><b>Blacklisted</b> means that specified address is <b>NOT allowed</b> to log in.<br>Blacklisted and whitelisted IP address can contradict.<br>When this happens <b>BLACKLISTED WILL TAKE PRIORITY</b> over whitelisted IPs for security reasons.<br>A user might whitelist an IP address, but when that user misbehaves, the system must be able to blacklist the user.<br>Whether whitelists are enabled depends on the setting bCMSLoginOnlyWhitelistedIPs in config file.')
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
     *                                 	TModuleAbstract:: AK_MENUITEM_NAMEDEFAULT => 'blog authors',
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
            Mod_Sys_Security::PERM_CAT_LOGINFLOODLOGS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE
                                                ),
            Mod_Sys_Security::PERM_CAT_LOGINIPBLACKWHITELIST => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CREATE,
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
        return array() ;       
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
        return 'Security';
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
        return '<svg enable-background="new 0 0 24 24" id="Layer_1" version="1.0" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M20,14.5c0,0-2.3,5.5-8,5.5s-8-5.5-8-5.5s4.1,1,7.9,1S20,14.5,20,14.5z"/><path d="M21.3,8.5C17.1,7.2,13.8,4,12,4c-1.8,0-5.1,3.2-9.3,4.5c-0.7,0.2-1,1-0.6,1.6C3,11.3,4,11.4,4,12.8c0,0,4,1,8,1s8-1,8-1  c0-1.4,1-1.5,1.8-2.8C22.2,9.5,21.9,8.7,21.3,8.5z M12,12c-1.1,0-2-1.9-2-3c0,0,0.9-1,2-1s2,1,2,1C14,10.1,13.1,12,12,12z"/></svg>';
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
