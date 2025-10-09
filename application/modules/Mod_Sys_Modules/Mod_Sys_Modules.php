<?php
namespace dr\modules\Mod_Sys_Modules;

use dr\classes\patterns\TModuleAbstract;



/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of SysLanguages
 *
 * @author drenirie
 */
class Mod_Sys_Modules extends TModuleAbstract
{
    const PERM_CAT_MODULESINSTALLED = 'modules installed';
    const PERM_CAT_MODULESUNINSTALLED = 'modules uninstalled';
    const PERM_CAT_MODULECATEGORIES = 'module categories';

    const PERM_OP_UNINSTALL = 'uninstall';
    const PERM_OP_INSTALL = 'install';
    const PERM_OP_UPLOAD = 'upload';

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

    public function getCategoryDefault()
    {
        return TModuleAbstract::CATEGORYDEFAULT_SYSTEM;
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
        // return array(
        //     'list_installedmodules.php' => 'installed',
        //     'list_uninstalledmodules.php' => 'not installed',
        //     'list_modulescategories.php' => 'module categories'
        // );
        return array(
            array('list_installedmodules', Mod_Sys_Modules::PERM_CAT_MODULESINSTALLED, 'installed', 'manage all available modules in system'),
            array('list_uninstalledmodules', Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED, 'not installed', 'manage modules that are not installed yet'),
            // array('list_modulescategories', Mod_Sys_Modules::PERM_CAT_MODULECATEGORIES, 'module categories', 'manage categories of modules')
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
            Mod_Sys_Modules::PERM_CAT_MODULESINSTALLED => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                Mod_Sys_Modules::PERM_OP_UNINSTALL,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                            ),
            Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                Mod_Sys_Modules::PERM_OP_INSTALL,
                                                Mod_Sys_Modules::PERM_OP_UPLOAD),
            Mod_Sys_Modules::PERM_CAT_MODULECATEGORIES => array (                  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT, 
                                                TModuleAbstract::PERM_OP_CHANGEORDER
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
            Mod_Sys_Modules::PERM_CAT_MODULESINSTALLED => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                            ),
            Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED => array (  
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
        return 'Modules';
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
        return '<svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                <path d="M.63,25.93l7.48,3h0a1,1,0,0,0,.74,0h0L16,26.08l7.11,2.84h0a1,1,0,0,0,.74,0h0l7.48-3A1,1,0,0,0,32,25V17.5a1,1,0,0,0-.07-.35.93.93,0,0,0-.05-.1.86.86,0,0,0-.13-.2l-.08-.08a.78.78,0,0,0-.24-.16s0,0-.05,0h0L24.5,13.82V7a1,1,0,0,0-.07-.35.47.47,0,0,0-.05-.1.86.86,0,0,0-.13-.2l-.08-.08a.78.78,0,0,0-.24-.16s0,0-.05,0h0l-7.5-3a1,1,0,0,0-.74,0l-7.5,3h0s0,0,0,0a.78.78,0,0,0-.24.16.27.27,0,0,0-.07.08.9.9,0,0,0-.14.2.93.93,0,0,0,0,.1A1,1,0,0,0,7.5,7v6.82L.63,16.57h0s0,0-.05,0a.78.78,0,0,0-.24.16.6.6,0,0,0-.08.08.86.86,0,0,0-.13.2l0,.1A1,1,0,0,0,0,17.5V25A1,1,0,0,0,.63,25.93ZM15,24.32l-5.5,2.2V21.18L15,19Zm7.5,2.2L17,24.32V19l5.5,2.2Zm7.5-2.2-5.5,2.2V21.18L30,19ZM28.31,17.5,23.5,19.42,18.69,17.5l4.81-1.92ZM22.5,13.82,17,16V10.68l5.5-2.2ZM16,5.08,20.81,7,16,8.92,11.19,7ZM9.5,8.48l5.5,2.2V16l-5.5-2.2Zm-1,7.1,4.81,1.92L8.5,19.42,3.69,17.5ZM2,19l5.5,2.2v5.34L2,24.32Z"/>
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
