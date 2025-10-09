<?php
namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_organizations extends TCRUDListController
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
        //global CMS_CURRENTMODULE;            

        $objModel = $this->objModel;
        $objTempContacts = new TSysContacts();  
        $objModel->select(array(
            TSysCMSOrganizations::FIELD_ID, 
            TSysCMSOrganizations::FIELD_CUSTOMID,
            TSysCMSOrganizations::FIELD_LOGINENABLED
                               ));
        $objModel->select(array(TSysContacts::FIELD_COMPANYNAME), $objTempContacts);        
    //    $objModel->selectAlias(TSysLanguages::FIELD_ID, 'iLangIDAlias', $objTempLang);     
               
       
        $this->executeDB(1);
        
        
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCMSOrganizations::FIELD_CUSTOMID, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSOrganizations::FIELD_CUSTOMID, 'Label identifier')),
            array('', TSysCMSOrganizations::FIELD_LOGINENABLED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSOrganizations::FIELD_LOGINENABLED, 'Enabled')),
            array($objTempContacts::getTable(), TSysContacts::FIELD_COMPANYNAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_COMPANYNAME, 'Company')),
                                    );
    
    
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
        return new TSysCMSOrganizations();
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
        return Mod_Sys_CMSUsers::PERM_CAT_ORGANIZATIONS;
    }
    

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_organizations';
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
        //global CMS_CURRENTMODULE;
        return transm(CMS_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_cmsuseraccounts', 'User accounts');
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