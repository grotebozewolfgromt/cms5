<?php

namespace dr\modules\Mod_ContactForm\controllers;


use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_ContactForm\Mod_ContactForm;
use dr\modules\Mod_ContactForm\models\TContactFormCategories;
use dr\modules\Mod_ContactForm\models\TContactFormSubmissions;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_contactformsubmissions extends TCRUDListController
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

        //====select what?
        $objModel = $this->objModel;
        $objTempCats = new TContactFormCategories();
        $objModel->select(array(
            TContactFormSubmissions::FIELD_ID, 
            TContactFormSubmissions::FIELD_RANDOMID, 
            TContactFormSubmissions::FIELD_RECORDCREATED,
            TContactFormSubmissions::FIELD_SPAMLIKELYHOOD,
            TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY,
            TContactFormSubmissions::FIELD_NAMEENCRYPTED,
            TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED,
            TContactFormSubmissions::FIELD_TOPIC
                                ));
       $objModel->select(array(
            TContactFormCategories::FIELD_CATEGORYNAME
                               ), $objTempCats);
        $this->executeDB(1);
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TContactFormSubmissions::FIELD_RANDOMID, transm(CMS_CURRENTMODULE, 'overview_column_ticketid', 'Ticket #')),
            array('', TContactFormSubmissions::FIELD_RECORDCREATED, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_RECORDCREATED, 'Date')),
            array('', TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, 'Spam %')),
            array('', TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, 'Spam')),
            array(TContactFormCategories::getTable(), TContactFormCategories::FIELD_CATEGORYNAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormCategories::FIELD_CATEGORYNAME, 'Category')),
            array('', TContactFormSubmissions::FIELD_NAMEENCRYPTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_NAMEENCRYPTED, 'Sender')),
            array('', TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, 'Email')),
            array('', TContactFormSubmissions::FIELD_TOPIC, transm(CMS_CURRENTMODULE, 'overview_column_'.TContactFormSubmissions::FIELD_TOPIC, 'Topic'))
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
        return new TContactFormSubmissions();
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
        return Mod_ContactForm::PERM_CAT_CONTACTFORMSUBMISSIONS;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_contactformsubmissions';
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
        return transm(CMS_CURRENTMODULE, 'tab_title_submissions_nospam', 'Contact form submissions');
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