<?php
namespace dr\modules\Mod_Sys_CMSUsers;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;

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
class Mod_Sys_CMSUsers extends TModuleAbstract
{
    const PERM_CAT_USERS = 'users';
    const PERM_CAT_USERROLES = 'userroles';
    const PERM_CAT_ORGANIZATIONS = 'organizations';
    const PERM_CAT_PERMISSIONSCOUNTRIES = 'permissionscountries';
    const PERM_CAT_INVITATIONCODES = 'invitationcodes';


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
        // return array(
        //     'list_users.php' => 'users', 
        //     'list_usersgroups.php' => 'user groups'            
        //     ) ;
        return array(
            array('list_users', Mod_Sys_CMSUsers::PERM_CAT_USERS, 'Users', 'Users of the cms'),
            array('list_organizations', Mod_Sys_CMSUsers::PERM_CAT_ORGANIZATIONS, 'Organizations', 'When '.APP_APPLICATIONNAME.' is deployed as a web-application service (like MailChimp, Shopify, Calendly etc) it can have multiple organizations/companies using '.APP_APPLICATIONNAME.'<br>An organisation can have 0, 1 or multiple employees (with each their own username and password) logging in on behalf of the organisation.<br>Every organisation gets 1 bill for their associated users.<br>The amount of users per organization can be restricted via \'roles & permissions\'.<br>'),
            array('list_usersroles', Mod_Sys_CMSUsers::PERM_CAT_USERROLES, 'Roles & permissions', 'Manage user roles and permissions'),
            array('list_permissionscountries', Mod_Sys_CMSUsers::PERM_CAT_PERMISSIONSCOUNTRIES, 'Permitted countries', 'Manage countries with access to the system'),
            array('list_invitationcodes', Mod_Sys_CMSUsers::PERM_CAT_INVITATIONCODES, 'Invite codes', 'Manage access invite codes for account creation in the system')
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
        $bResult = true;

        //delete old login sessions from database
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleting old login sessions from database ...');
        echo 'deleting old login sessions from database ... <br>';
        $objUsersSessions = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions();
        if (!$objUsersSessions->deleteOldSessionsFromDB(APP_COOKIE_EXPIREDAYS))
            $bResult = false;

        //delete old login history from database
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleting old user login history from database ...');
        echo 'deleting old user login history from database ... <br>';
        $objUsersHistory = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersHistory();
        if (!$objUsersHistory->deleteOldHistoryFromDB())
            $bResult = false;

        //delete old login attempts from database
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cleaning up user-flood logs from database ...');
        echo 'cleaning up login-flood logs from database ... <br>';
        $objUsersAttempts = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect();
        if (!$objUsersAttempts->deleteOldLogsFromDB())
            $bResult = false;


        //updating permissions
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'updating usergroup permissions');
        echo 'updating usergroup permissions ... <br>';
        $objPermissions = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions();
        if (!$objPermissions->updatePermissions())
            $bResult = false;
        unset($objPermissions);

        //delete expired email tokens
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'delete expired email tokens');
        echo 'deleting expired email tokens from database ... <br>';
        $objUsers = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers();
        if (!$objUsers->deleteEmailTokensExpired())
            $bResult = false;
        unset($objUsers);

        //delete expired users
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'delete users that were scheduled for deletion');
        echo 'removing users that were scheduled for deletion ... <br>';
        $objUsers = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers();
        if (!$objUsers->deleteUsersExpired())
            $bResult = false;
        unset($objUsers);

        //deleting user accounts (including all users in them)
        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'delete user accounts (including deleting all users in those accounts) that were scheduled for deletion');
        echo 'removing user accounts (including deleting all users in those accounts) that were scheduled for deletion ... <br>';
        $objAccounts = new TSysCMSOrganizations();
        if (!$objAccounts->deleteDBUserAccountsExpired())
            $bResult = false;
        unset($objAccounts);


        return $bResult;
    }

    /**
     * does all the stuff related to demo mode
     * (it's called in the cronjob function of lib_sys_framework)
     */
    public function handleCronJobDemoMode()
    {
        $bSuccess = true;
        $objPermissions = new \dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions();

        logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'demo mode: executing actions to set/reset roles, users and permissions');
        echo 'demo mode active: executing actions to set/reset roles, users and permissions <br>';


        //==== USER ROLE ====
        $objRole = new TSysCMSUsersRoles();
        if (!$objRole->recordExistsTableDB(TSysCMSUsersRoles::FIELD_ROLENAME, TSysCMSUsersRoles::ROLENAME_DEFAULT_DEMO))//create userrole
        {
            $objRole->setRoleName(TSysCMSUsersRoles::ROLENAME_DEFAULT_DEMO); //default username            
            $objRole->setDescription('Users using the demo version'); 
            $objRole->setMaxUsersInAccount(1);
            $objRole->setIsSystemRole(false);
            if (!$objRole->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new role '.TSysCMSUsersRoles::ROLENAME_DEFAULT_DEMO);
                $bSuccess = false;
            }                  

            //create permissions for role when added
            if (!$objPermissions->updatePermissions())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': updating permissions went wrong');
                $bSuccess = false;   
            }

            //==== SET PERMISSION for the demo role ====
            if (!$objPermissions->setPermissionsDemoMode($objRole))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': setting permissions demo mode went wrong');
                $bSuccess = false;   
            }            
        }
        else //load userrole (we need info later)
        {
            $objRole->find(TSysCMSUsersRoles::FIELD_ROLENAME, TSysCMSUsersRoles::ROLENAME_DEFAULT_DEMO);
            $objRole->limitOne();
            if (!$objRole->loadFromDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loading role failed '.TSysCMSUsersRoles::ROLENAME_DEFAULT_DEMO);
                return true;//EXIT, because we need a role id to create a new user
            }                   
        }



        //==== CONTACTS ====
        $objContact = new TSysContacts();
        $objContact->resetDemoContactsDB();


        //==== CMS ORGANISATION ====
        $objOrganisation = new TSysCMSOrganizations();
        if (!$objOrganisation->recordExistsTableDB(TSysCMSOrganizations::FIELD_CUSTOMID, TSysCMSOrganizations::DEFAULT_ORGANIZATIONIDENTIFIER_DEMO))
        {     
            $objOrganisation->setCustomIdentifier(TSysCMSOrganizations::DEFAULT_ORGANIZATIONIDENTIFIER_DEMO); //default username            
            $objOrganisation->setLoginEnabled(true); 
            $objOrganisation->setContactID($objContact->getID()); 
            if (!$objOrganisation->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new organisation '.TSysCMSOrganizations::DEFAULT_ORGANIZATIONIDENTIFIER_DEMO);
                $bSuccess = false;
            }                  
        }
        else //load organisation, we need it later
        {
            $objOrganisation->find(TSysCMSOrganizations::FIELD_CUSTOMID, TSysCMSOrganizations::DEFAULT_ORGANIZATIONIDENTIFIER_DEMO);
            $objOrganisation->limitOne();
            if (!$objOrganisation->loadFromDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loading organisation failed '.TSysCMSOrganizations::DEFAULT_ORGANIZATIONIDENTIFIER_DEMO);
                return true;//EXIT, because we need a role id to create a new user
            }                   
        }        


        //==== USER ====        
        $objUser = new TSysCMSUsers();
        if (!$objUser->recordExistsTableDB(TSysCMSUsers::FIELD_USERNAME, TSysCMSUsers::USERNAME_DEFAULT_DEMO))
        {
            $objUser->setUsername(TSysCMSUsers::USERNAME_DEFAULT_DEMO); //default username            
            $objUser->setUsernamePublic(TSysCMSUsers::USERNAME_DEFAULT_DEMO); //default username            
            $objUser->setPasswordDecrypted(TSysCMSUsers::PASSWORD_DEFAULT_DEMO); //default password            
            $objUser->setUserRoleID($objRole->getID()); //we need the role id to be loaded
            $objUser->setCMSOrganisationsID($objOrganisation->getID()); //we need the role id to be loaded
            $objUser->setLoginEnabled(true); //we need the role id to be loaded

            if (!$objUser->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new user '.TSysCMSUsers::USERNAME_DEFAULT_DEMO);
                $bSuccess = false;
            }                 
        }


        return $bSuccess;
    }

    /**
     * return permissions array
     *
     * @return array
     */
    public function getPermissions()
    {
        return array(
            Mod_Sys_CMSUsers::PERM_CAT_USERS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_USERROLES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE,
                                                TModuleAbstract::PERM_OP_LOCKUNLOCK,
                                                TModuleAbstract::PERM_OP_CHECKINOUT,
                                                TModuleAbstract::PERM_OP_CHANGEORDER
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_ORGANIZATIONS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE
                                                ),                                                
            Mod_Sys_CMSUsers::PERM_CAT_PERMISSIONSCOUNTRIES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_INVITATIONCODES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                TModuleAbstract::PERM_OP_DELETE,
                                                TModuleAbstract::PERM_OP_CHANGE,
                                                TModuleAbstract::PERM_OP_CREATE
                                                )                                                

            );    
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
            Mod_Sys_CMSUsers::PERM_CAT_USERS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_USERROLES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_ORGANIZATIONS => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                ),                                                
            Mod_Sys_CMSUsers::PERM_CAT_PERMISSIONSCOUNTRIES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                ),
            Mod_Sys_CMSUsers::PERM_CAT_INVITATIONCODES => array (  
                                                TModuleAbstract::PERM_OP_VIEW,
                                                )                                                

            );   
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
        return 'Users + permissions';
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
        return '<svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M13 20V18C13 15.2386 10.7614 13 8 13C5.23858 13 3 15.2386 3 18V20H13ZM13 20H21V19C21 16.0545 18.7614 14 16 14C14.5867 14 13.3103 14.6255 12.4009 15.6311M11 7C11 8.65685 9.65685 10 8 10C6.34315 10 5 8.65685 5 7C5 5.34315 6.34315 4 8 4C9.65685 4 11 5.34315 11 7ZM18 9C18 10.1046 17.1046 11 16 11C14.8954 11 14 10.1046 14 9C14 7.89543 14.8954 7 16 7C17.1046 7 18 7.89543 18 9Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
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
        return 'cmsusers';
    }       
}
