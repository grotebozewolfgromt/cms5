<?php
namespace dr\modules\Mod_Sys_Modules\controllers;

use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
use dr\modules\Mod_Sys_Modules\Mod_Sys_Modules;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_uninstalledmodules extends TCRUDListController
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
    
        //deleting modules from disk
        if (isset($_GET[ACTION_VARIABLE_ID]) && isset($_GET[ACTION_VARIABLE_DELETE]))
        {
            if ($_GET[ACTION_VARIABLE_DELETE] == '1')
            {
                //check if valid module (to prevent any kind of removal on the webserver)
                $arrAllMods = getModuleFolders();
                if (in_array($_GET[ACTION_VARIABLE_ID], $arrAllMods))
                {
                    if (rmdirrecursive(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$_GET[ACTION_VARIABLE_ID]))
                    {
                        sendMessageSuccess(transm(CMS_CURRENTMODULE, 'uninstalledmodules-delete-success', 'module deleted successfully'));
                        error_log('delete module '.$_GET[ACTION_VARIABLE_ID].' from disk FAILED');
                    }
                    else
                    {
                        sendMessageError(transm(CMS_CURRENTMODULE, 'uninstalledmodules-delete-error', 'module not deleted'));
                    }
                    
                }
            }
        }



        //load modules from database
        $objModel = $this->objModel;
        // $objTempModCat = new TSysModulesCategories();  
        $objModel->select(array(TSysModules::FIELD_ID, 
            TSysModules::FIELD_NAMEINTERNAL, 
            TSysModules::FIELD_CHECKOUTEXPIRES, 
            TSysModules::FIELD_CHECKOUTSOURCE, 
            TSysModules::FIELD_LOCKED,
            TSysModules::FIELD_LOCKEDSOURCE));
        // $objModel->select(array(TSysModulesCategories::FIELD_NAME), $objTempModCat);
        // $objModel->selectAlias(TSysModulesCategories::FIELD_ID, 'iCategoryID', $objTempModCat); 

        $objModel->loadFromDB();

        //get modules from disk
        $arrModDirsFromDisk = null;
        $arrModDirsFromDisk = getFileFolderArray(APP_PATH_MODULES, true, false);

        //compare db entries with entries on disk
        //in other words: find directories that are not in the database
        $arrUninstalledModules = array();
        foreach($arrModDirsFromDisk as $sCurrUninstalledModDir)
        {
            $bModFound = false;
            $objModel->resetRecordPointer();
            while($objModel->next() && (!$bModFound))
            {            
                if ($objModel->get(TSysModules::FIELD_NAMEINTERNAL) == $sCurrUninstalledModDir)
                    $bModFound = true;
            }    

            if ($bModFound == false)
                $arrUninstalledModules[] = $sCurrUninstalledModDir;
        }
        unset($arrModDirsFromDisk);




        return get_defined_vars();    
    }


    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        //global CMS_CURRENTMODULE;
        return getPathModuleTemplates(CMS_CURRENTMODULE, true).'tpl_list_uninstalledmodules.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.CMS_CURRENTMODULE.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_list_uninstalledmodules.php';
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
        return Mod_Sys_Modules::PERM_CAT_MODULESUNINSTALLED;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'uploadinstall_module';
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