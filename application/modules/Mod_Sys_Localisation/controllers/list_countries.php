<?php
namespace dr\modules\Mod_Sys_Localisation\controllers;

use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_Localisation\Mod_Sys_Localisation;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_countries extends TCRUDListController
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
        $objModel->select(array(
            TSysCountries::FIELD_ID, 
            TSysCountries::FIELD_COUNTRYNAME,
            TSysCountries::FIELD_COUNTRYCODEPHONE,
            TSysCountries::FIELD_ISO2,
            TSysCountries::FIELD_ISO3,
            TSysCountries::FIELD_ISEEA,            
            TSysCountries::FIELD_ISDEFAULT,
            TSysCountries::FIELD_ISFAVORITE,
            TSysCountries::FIELD_ISUNKNOWN
                                    ));
      
        $this->executeDB();
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCountries::FIELD_COUNTRYNAME, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_COUNTRYNAME, 'Country')),
            array('', TSysCountries::FIELD_COUNTRYCODEPHONE, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_COUNTRYCODEPHONE, 'Phone')),
            array('', TSysCountries::FIELD_ISO2, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISO2, 'ISO 2')),
            array('', TSysCountries::FIELD_ISO3, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISO3, 'ISO 3')),
            array('', TSysCountries::FIELD_ISEEA, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISEEA, 'In EU')),
            array('', TSysCountries::FIELD_ISDEFAULT, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISDEFAULT, 'Default')),
            array('', TSysCountries::FIELD_ISFAVORITE, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISFAVORITE, 'Favorite')),
            array('', TSysCountries::FIELD_ISUNKNOWN, transm(CMS_CURRENTMODULE, 'countries_overview_column_'.TSysCountries::FIELD_ISUNKNOWN, 'Unknown'))
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
        return new TSysCountries();
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
        return Mod_Sys_Localisation::PERM_CAT_COUNTRIES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_countries';
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
        return transm(CMS_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_countries', 'All system Countries');
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