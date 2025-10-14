<?php
namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_permissionscountries extends TCRUDListController
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
        $objTempCountries = new TSysCountries();  
        $objModel->select(array(
            TSysCountries::FIELD_ID
                                    ));
        $objModel->select(array(TSysCountries::FIELD_COUNTRYNAME), $objTempCountries);               


        $this->executeDB(true);
        
        
        //===show what?
        $arrTableColumnsShow = array(
            array($objTempCountries::getTable(), TSysCountries::FIELD_COUNTRYNAME, transm(APP_ADMIN_CURRENTMODULE, 'overview_column_'.TSysCountries::FIELD_COUNTRYNAME, 'country')),
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
        return new TSysCMSPermissionsCountries();
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
        return Mod_Sys_CMSUsers::PERM_CAT_PERMISSIONSCOUNTRIES;
    }
    

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_permissionscountries';
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
        return transm(APP_ADMIN_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_permissionscountries', 'Which countries are permitted to use the system?');
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