<?php
namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_users extends TCRUDListController
{
    
    /**
     * executes the controller
     * this function is ONLY called on a cache miss
     * to generate new content for the cache and to 
     * display to the screen
     *
     * 
     * @return array with variables, use: "return get_defined_vars();" to use all variables declared in the execute() function
     */
    public function execute()
    {
        // global $objCurrentModule;
        //global APP_ADMIN_CURRENTMODULE;            

        $objModel = $this->objModel;
        $objTempLang = new TSysLanguages();  
        $objTempGroups = new TSysCMSUsersRoles();
        $objTempAccounts = new TSysCMSOrganizations();
        $objModel->select(array(
            TSysCMSUsers::FIELD_ID, 
            TSysCMSUsers::FIELD_USERNAME, 
            TSysCMSUsers::FIELD_USERNAMEPUBLIC, 
            TSysCMSUsers::FIELD_EMAILADDRESSENCRYPTED, 
            TSysCMSUsers::FIELD_LOGINENABLED, 
            TSysCMSUsers::FIELD_LOGINEXPIRES, 
            TSysCMSUsers::FIELD_LASTLOGIN,
            TSysCMSUsers::FIELD_CHECKOUTEXPIRES,
            TSysCMSUsers::FIELD_CHECKOUTSOURCE,
            TSysCMSUsers::FIELD_LOCKED,
            TSysCMSUsers::FIELD_LOCKEDSOURCE
                                    ));
        $objModel->select(array(TSysLanguages::FIELD_LANGUAGE), $objTempLang);
        $objModel->select(array(TSysCMSUsersRoles::FIELD_ROLENAME), $objTempGroups);
        $objModel->select(array(TSysCMSOrganizations::FIELD_CUSTOMID), $objTempAccounts);
    //    $objModel->selectAlias(TSysLanguages::FIELD_ID, 'iLangIDAlias', $objTempLang);     
               
       
        $this->executeDB(true);
        
        
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCMSUsers::FIELD_USERNAME, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_USERNAME, 'username')),
            array('', TSysCMSUsers::FIELD_USERNAMEPUBLIC, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_USERNAMEPUBLIC, 'public')),
            array('', TSysCMSUsers::FIELD_EMAILADDRESSENCRYPTED, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_EMAILADDRESSENCRYPTED, 'email')),
            array('', TSysCMSUsers::FIELD_LOGINENABLED, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_LOGINENABLED, 'enabled')),
            array('', TSysCMSUsers::FIELD_LOGINEXPIRES, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_LOGINEXPIRES, 'expires')),
            array('', TSysCMSUsers::FIELD_LASTLOGIN, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsers::FIELD_LASTLOGIN, 'last login')),
            array(TSysLanguages::getTable(), TSysLanguages::FIELD_LANGUAGE, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysLanguages::FIELD_LANGUAGE, 'language')),
            array(TSysCMSUsersRoles::getTable(), TSysCMSUsersRoles::FIELD_ROLENAME, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSUsersRoles::FIELD_ROLENAME, 'role')),
            array(TSysCMSOrganizations::getTable(), TSysCMSOrganizations::FIELD_CUSTOMID, transm(APP_ADMIN_CURRENTMODULE, 'users_overview_column_'.TSysCMSOrganizations::FIELD_CUSTOMID, 'account'))
                                    );
    
    
        // $bNoRecordsToDisplay = false;
        // if ($objModel != null)
        // {
        //     if ($objModel->count() == 0)
        //             $bNoRecordsToDisplay = true;
        // }
        
        return get_defined_vars();    
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modellist.php';
    }

    /**
     * return path of the skin template
     * 
     * return '' if no skin
     *
     * @return string
     */
    public function getSkinPath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'skin_withmenu.php';
    }

    /**
     * return new TSysModel object
     * 
     * @return TSysModel;
     */
    public function getNewModel()
    {
        return new TSysCMSUsers();
    }

    /**
     * return permission category 
     * =class constant of module class
     * 
     * for example: Mod_Sys_CMSUsers::PERM_CAT_USERS
     *
     * @return string
     */
    public function getAuthorisationCategory()
    {
        return Mod_Sys_CMSUsers::PERM_CAT_USERS;
    }
    

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_users';
    }

    /**
     * return page title
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    function getTitle()
    {
        //global APP_ADMIN_CURRENTMODULE;
        return transm(APP_ADMIN_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_usercms', 'Users');
    }

    /**
     * show tabsheets on top?
     *
     * @return bool
     */
    public function showTabs()
    {
        return true;
    }   

}