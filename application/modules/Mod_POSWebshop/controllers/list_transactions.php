<?php

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TTransactions;
// use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_transactions extends TCRUDListController
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
        $objTempContacts = new TSysContacts();
        $objModel->select(array(
            TTransactions::FIELD_ID, 
            TTransactions::FIELD_CHECKOUTEXPIRES,
            TTransactions::FIELD_CHECKOUTSOURCE,
            TTransactions::FIELD_LOCKED,
            TTransactions::FIELD_LOCKEDSOURCE,
            TTransactions::FIELD_DATEFINALIZED,            
            TTransactions::FIELD_INCREMENTNUMBER,
            TTransactions::FIELD_META_TOTALPRICEINCLVAT,
                                    ));
       $objModel->select(array(
            TSysContacts::FIELD_LASTNAME, 
            TSysContacts::FIELD_COMPANYNAME, 
            TSysContacts::FIELD_EMAILADDRESSENCRYPTED, 
                               ), $objTempContacts);
        $this->executeDB(1);
      

        //===show what?
        $arrTableColumnsShow = array(
            array('', TTransactions::FIELD_DATEFINALIZED, transm(CMS_CURRENTMODULE, 'overview_column_'.TTransactions::FIELD_DATEFINALIZED, 'Date')),
            array('', TTransactions::FIELD_INCREMENTNUMBER, transm(CMS_CURRENTMODULE, 'overview_column_'.TTransactions::FIELD_INCREMENTNUMBER, 'Inc No')),
            // array(TSysContacts::getTable(), TSysContacts::FIELD_COMPANYNAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_FIRSTNAMEINITALS, 'Company')),
            // array(TSysContacts::getTable(), TSysContacts::FIELD_LASTNAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_LASTNAME, 'Name')),
            // array(TSysContacts::getTable(), TSysContacts::FIELD_EMAILADDRESSENCRYPTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysContacts::FIELD_EMAILADDRESSENCRYPTED, 'Email')),
            array('', TTransactions::FIELD_META_TOTALPRICEINCLVAT, transm(CMS_CURRENTMODULE, 'overview_column_'.TTransactions::FIELD_META_TOTALPRICEINCLVAT, 'Total'))
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
        return new TTransactions();
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
        return Mod_POSWebshop::PERM_CAT_TRANSACTIONS;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_transactions';
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
        return transm(CMS_CURRENTMODULE, 'tab_title_transactions', 'Transactions');
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