<?php
namespace dr\modules\Mod_Sys_Modules\controllers;

use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
use dr\modules\Mod_Sys_Modules\Mod_Sys_Modules;
use dr\classes\dom\tag\form\Option;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_installedmodules extends TCRUDListController
{
    const BULKACTION_VALUE_UNINSTALL = 'uninstall'; //html variable
    
    /**
     * 
     * @param TSysModel $objModel
     */
    public function  __construct()
    {
        $this->setUseBulkDelete(false); //we do an uninstall, not a delete

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
        // global $objCurrentModule;
        //global CMS_CURRENTMODULE;        
        // global $arrTabsheets;        
    
    
    
        $objModel = $this->objModel;
        // $objTempModCat = new TSysModulesCategories();  
        $objModel->select(array(TSysModules::FIELD_ID, 
            TSysModules::FIELD_NAMEINTERNAL, 
            TSysModules::FIELD_NAMENICETRANSDEFAULT, 
            TSysModules::FIELD_MODULETYPE, 
            TSysModules::FIELD_VISIBLECMS,
            TSysModules::FIELD_VISIBLEFRONTEND,
            TSysModules::FIELD_CHECKOUTEXPIRES, 
            TSysModules::FIELD_CHECKOUTSOURCE, 
            TSysModules::FIELD_LOCKED,
            TSysModules::FIELD_LOCKEDSOURCE));
        // $objModel->select(array(TSysModulesCategories::FIELD_NAME), $objTempModCat);
        // $objModel->selectAlias(TSysModulesCategories::FIELD_ID, 'iCategoryID', $objTempModCat); 

        
        $this->executeDB(true);
         
        
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysModules::FIELD_NAMENICETRANSDEFAULT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModules::FIELD_NAMENICETRANSDEFAULT, 'Default name')),
            array('', TSysModules::FIELD_NAMEINTERNAL, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModules::FIELD_NAMEINTERNAL, 'internal name')),
            array('', TSysModules::FIELD_MODULETYPE, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModules::FIELD_MODULETYPE, 'type')),
            array('', TSysModules::FIELD_VISIBLECMS, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModules::FIELD_VISIBLECMS, 'cms')),
            array('', TSysModules::FIELD_VISIBLEFRONTEND, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModules::FIELD_VISIBLEFRONTEND, 'front')),
            // array($objTempModCat::getTable(), TSysModulesCategories::FIELD_NAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysModulesCategories::FIELD_NAME, 'category'))
                                    );    
        
        
        // $bNoRecordsToDisplay = false;
        // if ($objModel != null)
        // {
        //     if ($objModel->count() == 0)
        //             $bNoRecordsToDisplay = true;
        // }  
                
        $sURLUploadModule = 'uploadinstall_module';
        
          
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

        $objUninstall = new Option();
        $objUninstall->setValue(list_installedmodules::BULKACTION_VALUE_UNINSTALL);
        $objUninstall->setText(transcms('overview_bulkactions_uninstall', 'uninstall'));
        if (auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_DELETE)) //I temporarily take the delete value since it is standardized and has rougly the same meaning
            $objSelectBulkActions->appendChild($objUninstall);    
        unset($objUninstall);        
        
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

                    if ($_GET[BULKACTION_VARIABLE_SELECT_ACTION] == list_installedmodules::BULKACTION_VALUE_UNINSTALL)
                    {
                        if (auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_DELETE))
                        {
                            $sCurrModuleNameInternal = '';
                            $sTempModClass = '';
                            $objCurrentModule = null;

                            //lookup the internal name of the module
                            $this->getModel()->clear(true);
                            $this->getModel()->findID($iID);
                            $this->getModel()->select(array(TSysModules::FIELD_NAMEINTERNAL));
                            if ($this->getModel()->loadFromDB())
                            {
                                $sCurrModuleNameInternal = $this->getModel()->get(TSysModules::FIELD_NAMEINTERNAL);
                            }
                            
                            //instantiate module class
                            $sTempModClass = getModuleFullNamespaceClass($sCurrModuleNameInternal);
                            $objCurrentModule = new $sTempModClass;  

                            //uninstall
                            if ($objCurrentModule->uninstallModule()) //check on system module is done in this function                            
                                $bBulkSuccess = true;

                        }

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
        //global CMS_CURRENTMODULE;
        return getPathModuleTemplates(CMS_CURRENTMODULE, true).'tpl_list_installedmodules.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.CMS_CURRENTMODULE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_list_installedmodules.php';
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
        return new TSysModules();
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
        return Mod_Sys_Modules::PERM_CAT_MODULESINSTALLED;
    }
  

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_installedmodules';
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
        return transm(CMS_CURRENTMODULE, TRANS_MODULENAME_TITLE, 'modules');
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