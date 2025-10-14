<?php
namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;



include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');


class list_usersroles extends TCRUDListController
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
    
        
        $this->executeDB(true);
        
        
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCMSUsersRoles::FIELD_ROLENAME, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersRoles::FIELD_ROLENAME, 'Role')),      
            array('', TSysCMSUsersRoles::FIELD_DESCRIPTION, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersRoles::FIELD_DESCRIPTION, 'Description')),      
            array('', TSysCMSUsersRoles::FIELD_MAXUSERSINACCOUNT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersRoles::FIELD_MAXUSERSINACCOUNT, 'Max users')),      
            array('', TSysCMSUsersRoles::FIELD_ISANONYMOUS, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersRoles::FIELD_ISANONYMOUS, 'Anonymous')),      
            array('', TSysCMSUsersRoles::FIELD_ISSYSTEMROLE, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersRoles::FIELD_ISSYSTEMROLE, 'System'))      
                                    );
    
    

        return get_defined_vars();    
    }



    /**
     * execute bulk actions
     * 
     * remove all user permissions before delete
     */
    /*
    protected function executeBulkActions()
    {
        $bBulkSuccess = true;
        $objPerm = new TSysCMSPermissions();

        if (isset($_GET[BULKACTION_VARIABLE_SELECT_ACTION]) && isset($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID]))
        {                
            $iCountIDs = count($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID]);

            foreach($_GET[BULKACTION_VARIABLE_CHECKBOX_RECORDID] as $iID)
            {
                if (is_numeric($iID))
                {

                    if ($_GET[BULKACTION_VARIABLE_SELECT_ACTION] == BULKACTION_VALUE_DELETE)
                    {
                        //this is done in TSysCMSPermissions by overloading deleteFromDB()
                        //delete can fail (for example when is system table)
                        // if (auth($this->getModule(), $this->getAuthorisationCategory(), AUTH_OPERATION_DELETE))
                        // {
                        //     //remove all user permissions before delete
                        //     $objPerm->newQuery();
                        //     if (!$objPerm->deletePermissionsForUsergroup($iID))
                        //         $bBulkSuccess = false;
                        // }

                    }
                }
            }


            $sRefURL = '';
            $sRefURL = removeVariableFromURL(getURLThisScript(), BULKACTION_VARIABLE_SELECT_ACTION);
            $sRefURL = removeVariableFromURL($sRefURL, urlencode(BULKACTION_VARIABLE_CHECKBOX_RECORDID.'[]'));

            if ($bBulkSuccess)
            {
                $sRefURL = addVariableToURL ($sRefURL, 'cmsmessage', transcms('overview_bulkactions_deletepermissions_failed', 'removing permissions failed'));
            }

            header('Location: '.$sRefURL);
        }

        //if removal of permissions was successful, then also delete the record
        if ($bBulkSuccess)
            parent::executeBulkActions();

    }
*/


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
        return new TSysCMSUsersRoles();
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
        return Mod_Sys_CMSUsers::PERM_CAT_USERROLES;
    }
  

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_usersroles';
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
        return transm(CMS_CURRENTMODULE, TRANS_MODULENAME_TITLE.'_userroles', 'User roles and permissions');
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