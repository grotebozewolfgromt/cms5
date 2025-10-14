<?php

namespace dr\modules\Mod_PageBuilder\controllers;


use dr\classes\controllers\TCRUDListController;
use dr\classes\controllers\TPageBuilderControllerAbstract;
use dr\classes\dom\tag\form\Option;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses;
use dr\classes\models\TSysModel;
use dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
use dr\classes\models\TWebpageBuilderPages;
use dr\modules\Mod_PageBuilder\Mod_PageBuilder;


// use dr\modules\Mod_Sys_Contacts\Mod_Sys_Contacts;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_pages extends TCRUDListController
{
    public function __construct()
    {
        $this->setUseBulkDelete(false);

        parent::__construct();
    }
    
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
        //global CMS_CURRENTMODULE;
        // global $arrTabsheets;        


        /**********************************************************
         * WARNING
         **********************************************************
         * We DON'T (!!!) join with TSysModules because if the pagebuilder is question is NOT installed
         * we can't join.
         * But we still want to show the pages, EVEN if the pagebuilder is not installed.
         * This way the user can install the pagebuilder again, another version of a pagebuilder, 
         * or a completely different one that is compatible
        */

        $objModel = $this->objModel;
        $objTempLang = new TSysLanguages();  
        $objTempWebsite = new TSysWebsites();
        $objTempStatus = new TPageBuilderDocumentsStatusses();
        // $objTempModule = new TSysModules(); --> we explicitly don't join modules. 
        $objModel->select(array(
            TPageBuilderWebpages::FIELD_URLSLUG, 
            TPageBuilderWebpages::FIELD_NAMEINTERNAL,
            TPageBuilderWebpages::FIELD_META_MODULENAMENICE,
            TPageBuilderWebpages::FIELD_MODULEVERSIONNUMBER,
            TPageBuilderWebpages::FIELD_HTML_TITLE,
            TPageBuilderWebpages::FIELD_PUBLISHDATE,
            TPageBuilderWebpages::FIELD_VISIBILITY,
            TPageBuilderWebpages::FIELD_RECORDCHANGED,
            TPageBuilderWebpages::FIELD_CHECKOUTEXPIRES,
            TPageBuilderWebpages::FIELD_CHECKOUTSOURCE,
            TPageBuilderWebpages::FIELD_LOCKED,
            TPageBuilderWebpages::FIELD_LOCKEDSOURCE
                                    ));
        $objModel->select(array(TSysLanguages::FIELD_LANGUAGE), $objTempLang);
        $objModel->select(array(TSysWebsites::FIELD_WEBSITENAME), $objTempWebsite);     
        $objModel->select(array(TPageBuilderDocumentsStatusses::FIELD_NAME), $objTempStatus);     
        // $objModel->select(array(TSysModules::FIELD_NAMENICETRANSDEFAULT), $objTempModule);  --> we explicitly don't join modules.     

        $objModel->where(TPageBuilderWebpages::FIELD_WEBSITEID, APP_WEBSITEID_SELECTEDINCMS); //only for current site
        $this->executeDB(1);
      
        //===show what?
        $arrTableColumnsShow = array(
            array('', TPageBuilderWebpages::FIELD_URLSLUG, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_URLSLUG, 'URL slug')),
            array('', TPageBuilderWebpages::FIELD_NAMEINTERNAL, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_NAMEINTERNAL, 'Name')),
            // array(TSysModules::getTable(), TSysModules::FIELD_NAMENICETRANSDEFAULT, transm(CMS_CURRENTMODULE, 'list_column_module'.TSysModules::FIELD_NAMENICETRANSDEFAULT, 'PageBuilder')),  --> we explicitly don't join modules. 
            array('', TPageBuilderWebpages::FIELD_META_MODULENAMENICE, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_META_MODULENAMENICE, 'Pagebuilder')),
            array('', TPageBuilderWebpages::FIELD_MODULEVERSIONNUMBER, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_MODULEVERSIONNUMBER, 'Ver')),
            array(TSysLanguages::getTable(), TSysLanguages::FIELD_LANGUAGE, transm(CMS_CURRENTMODULE, 'list_column_'.TSysLanguages::FIELD_LANGUAGE, 'Language')),
            array(TSysWebsites::getTable(), TSysWebsites::FIELD_WEBSITENAME, transm(CMS_CURRENTMODULE, 'list_column_'.TSysWebsites::FIELD_WEBSITENAME, 'Website')),
            array('', TPageBuilderWebpages::FIELD_VISIBILITY, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_VISIBILITY, 'Visible')),
            array('', TPageBuilderWebpages::FIELD_PUBLISHDATE, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_PUBLISHDATE, 'Published')),
            array(TPageBuilderDocumentsStatusses::getTable(), TPageBuilderDocumentsStatusses::FIELD_NAME, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderDocumentsStatusses::FIELD_NAME, 'Status')),
            array('', TPageBuilderWebpages::FIELD_RECORDCHANGED, transm(CMS_CURRENTMODULE, 'list_column_'.TPageBuilderWebpages::FIELD_RECORDCHANGED, 'Changed')),
                );
        
             
        return get_defined_vars();    
    }


   /**
     * returns the html select tag with all the bulk items
     * 
     * @return Select
     */
    public function getBulkSelect()
    {
        $objSelectBulkActions = parent::getBulkSelect();
        $bAuth = false;

        $objDelete = new Option();
        $objDelete->setValue(BULKACTION_VALUE_DELETE);
        $objDelete->setText(transcms('overview_bulkactions_delete', 'delete'));
       
        if (auth(CMS_CURRENTMODULE, $this->getAuthorisationCategory(), Mod_PageBuilder::PERM_OP_DELETE) || auth(CMS_CURRENTMODULE, $this->getAuthorisationCategory(), Mod_PageBuilder::PERM_OP_DELETE_OWN))
            $objSelectBulkActions->appendChild($objDelete);          
        
        return $objSelectBulkActions;        
    }

    /**
     * execute bulk actions
     */
    protected function executeBulkActions()
    {
        global $objAuthenticationSystem;
        $bBulkSuccess = false;

        if (isset($_GET[BULKACTION_VARIABLE_SELECT_ACTION]) && isset($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID]))
        {                
            $iCountIDs = count($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID]);

            foreach($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID] as $iID)
            {
                if (is_numeric($iID))
                {

                    //we rewrite bulk delete function, because we need to separate auth for all users and for own
                    if ($_GET[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_DELETE)
                    {
                        $this->objModel->clear(true);

                        if ($this->objModel->getTableUseIDField())
                            $this->objModel->findID($iID);

                        if ($this->objModel->getTableUseLock())
                            $this->objModel->find(TSysModel::FIELD_LOCKED, false);
                            
                        if ($this->objModel->getTableUseCheckout())
                        {
                            //@todo check if record is checked-out or checkout-date expired
                        }
                        
                        //check authorization
                        $this->objModel->loadFromDB(); //we need to load record from database to check authorid
                        $bAuth = false;
                        if (auth(CMS_CURRENTMODULE, $this->getAuthorisationCategory(), Mod_PageBuilder::PERM_OP_DELETE))
                            $bAuth = true;
                        $bTemp1 = auth(CMS_CURRENTMODULE, $this->getAuthorisationCategory(), Mod_PageBuilder::PERM_OP_DELETE_OWN);
                        $bTemp2 = $this->objModel->getAuthorUserID();
                        $bTemp3 =  $objAuthenticationSystem->getUsers()->getID();
                        if (auth(CMS_CURRENTMODULE, $this->getAuthorisationCategory(), Mod_PageBuilder::PERM_OP_DELETE_OWN) && ($this->objModel->getAuthorUserID() == $objAuthenticationSystem->getUsers()->getID()))
                            $bAuth = true;
                        
                        if($bAuth)
                        {
                            if ($this->objModel->deleteFromDB(true, true))
                                $bBulkSuccess = true;
                            else
                                error_log('delete record with id '.$iID.' failed for '.$this->objModel::getTable());
                        }
                        else
                            error_log('auth() failed for bulk deleting records');


                    }
                }
            }


            $sRefURL = '';
            $sRefURL = removeVariableFromURL(getURLThisScript(), BULKACTION_VARIABLE_SELECT_ACTION);
            $sRefURL = removeVariableFromURL($sRefURL, urlencode(BULKACTION_VARIABLE_CHECKBOX_RECORDID.'[]'));

            if ($bBulkSuccess)
            {
                if ($_GET[BULKACTION_VARIABLE_SELECT_ACTION] == '')                
                    $sRefURL = addVariableToURL ($sRefURL, 'cmsmessage', transcms('overview_bulkactions_empty', 'not selected a bulk action'));
                else
                    $sRefURL = addVariableToURL ($sRefURL, 'cmsmessage', transcms('overview_bulkactions_success', 'bulk actions completed succesfully'));
            }

            header('Location: '.$sRefURL);
        }

        if (!$bBulkSuccess)
            parent::executeBulkActions();

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
        return new TPageBuilderWebpages();
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
        return Mod_PageBuilder::PERM_CAT_WEBPAGES;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        //only include the return url
        return  addVariableToURL(APP_URL_CMS_PAGEBUILDERROUTER, 
                                ACTION_VARIABLE_RETURNURL, 
                                removeVariableFromURL(getURLThisScript()));         
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
        return transm(CMS_CURRENTMODULE, 'tab_title_pages', 'Pages');
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