<?php
namespace dr\modules\Support;

use dr\classes\patterns\TModuleAbstract;


/**
 * Description of Support module
 *
 * @author drenirie
 * created 11 mrt 2022
 */
class Support extends TModuleAbstract
{
    const PERM_CAT_CONTACTFORM = 'contactform';

    /**
     * returns the type of the module
     * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
     * if you don't know, return MOD_TYPE_REGULAR
     * 
     * @return string
     */    
    public function getModuleType() 
    {
        return TModuleAbstract::MOD_TYPE_REGULAR; 
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
        // return array(
        //     'overview.php' => 'overview'
        //     ) ;
        return array(
            array('contactform', Support::PERM_CAT_CONTACTFORM, 'Contact form', 'Send an email')
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
            Support::PERM_CAT_CONTACTFORM => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_CREATE
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
        return 'Support';
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
        return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
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
