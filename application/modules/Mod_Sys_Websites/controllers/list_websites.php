<?php
namespace dr\modules\Mod_Sys_Websites\controllers;

use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
use dr\modules\Mod_Sys_Websites\Mod_Sys_Websites;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_websites extends TCRUDListController
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
        // global $arrTabsheets;        


        $objModel = $this->objModel;
        $objTempLang = new TSysLanguages();  
        $objModel->select(array(
            TSysWebsites::FIELD_ID, 
            TSysWebsites::FIELD_WEBSITENAME, 
            TSysWebsites::FIELD_URL, 
            TSysWebsites::FIELD_CHECKOUTEXPIRES,
            TSysWebsites::FIELD_CHECKOUTSOURCE,
            TSysWebsites::FIELD_LOCKED,
            TSysWebsites::FIELD_LOCKEDSOURCE
                                    ));
        $objModel->select(array(TSysLanguages::FIELD_LANGUAGE), $objTempLang);
        
                
        $this->executeDB(true);
        
            
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysWebsites::FIELD_WEBSITENAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysWebsites::FIELD_WEBSITENAME, 'website name')),
            array('', TSysWebsites::FIELD_URL, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysWebsites::FIELD_URL, 'url')),
            array($objTempLang::getTable(), TSysLanguages::FIELD_LANGUAGE, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysLanguages::FIELD_LANGUAGE, 'default')),
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
        return new TSysWebsites();
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
        return Mod_Sys_Websites::PERM_CAT_WEBSITES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_websites';
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
        return transm(CMS_CURRENTMODULE, TRANS_MODULENAME_TITLE, 'Websites');
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