<?php
namespace dr\modules\Mod_Sys_Localisation\controllers;

use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_Localisation\Mod_Sys_Localisation;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_languages extends TCRUDListController
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
        // global $arrTabsheets;        


        $objModel = $this->objModel;
        $objModel->select(array(
            TSysLanguages::FIELD_ID, 
            TSysLanguages::FIELD_CHECKOUTEXPIRES,
            TSysLanguages::FIELD_CHECKOUTSOURCE,
            TSysLanguages::FIELD_LOCKED,
            TSysLanguages::FIELD_LOCKEDSOURCE,
            TSysLanguages::FIELD_LOCALE,
            TSysLanguages::FIELD_LANGUAGE,
            TSysLanguages::FIELD_ISCMSLANGUAGE,
            TSysLanguages::FIELD_ISFAVORITE,
            TSysLanguages::FIELD_ISDEFAULT
                                    ));
      
        $this->executeDB();
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysLanguages::FIELD_LANGUAGE, transm(APP_ADMIN_CURRENTMODULE, 'languages_overview_column_'.TSysLanguages::FIELD_LANGUAGE, 'Language')),
            array('', TSysLanguages::FIELD_LOCALE, transm(APP_ADMIN_CURRENTMODULE, 'languages_overview_column_'.TSysLanguages::FIELD_LOCALE, 'Locale')),
            array('', TSysLanguages::FIELD_ISCMSLANGUAGE, transm(APP_ADMIN_CURRENTMODULE, 'languages_overview_column_'.TSysLanguages::FIELD_ISCMSLANGUAGE, 'CMS lang')),
            array('', TSysLanguages::FIELD_ISFAVORITE, transm(APP_ADMIN_CURRENTMODULE, 'languages_overview_column_'.TSysLanguages::FIELD_ISFAVORITE, 'Favorite')),
            array('', TSysLanguages::FIELD_ISDEFAULT, transm(APP_ADMIN_CURRENTMODULE, 'languages_overview_column_'.TSysLanguages::FIELD_ISDEFAULT, 'Default'))        
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
        return new TSysLanguages();
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
        return Mod_Sys_Localisation::PERM_CAT_LANGUAGES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_languages';
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
        return transm(APP_ADMIN_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_languages', 'All system languages');
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