<?php
namespace dr\modules\Mod_Sys_Security\controllers;

use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect;
use dr\modules\Mod_Sys_Security\Mod_Sys_Security;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_loginfloodlogs extends TCRUDListController
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
        $objTempLang = new TSysCMSUsersFloodDetect();  
        $objModel->select(array(
            TSysCMSUsersFloodDetect::FIELD_RANDOMID, 
            TSysCMSUsersFloodDetect::FIELD_DATEATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_IPADDRESS, 
            TSysCMSUsersFloodDetect::FIELD_USERNAMEENCRYPTED, 
            TSysCMSUsersFloodDetect::FIELD_ISFAILEDLOGINATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_ISSUCCEEDEDLOGINATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_ISPASSWORDRESET, 
            TSysCMSUsersFloodDetect::FIELD_ISCREATEACCOUNTATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_ISDIRECTORYTRAVERSALATTEMPT, 
            TSysCMSUsersFloodDetect::FIELD_USERAGENT, 
                                    ));        
                
        $this->executeDB(true);
        
            
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCMSUsersFloodDetect::FIELD_DATEATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_DATEATTEMPT, 'Date')),
            array('', TSysCMSUsersFloodDetect::FIELD_IPADDRESS, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_IPADDRESS, 'IP addr')),
            array('', TSysCMSUsersFloodDetect::FIELD_USERNAMEENCRYPTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_USERNAMEENCRYPTED, 'User')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISFAILEDLOGINATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISFAILEDLOGINATTEMPT, 'Login fail')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISSUCCEEDEDLOGINATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISSUCCEEDEDLOGINATTEMPT, 'Login succ')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISPASSWORDRESET, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISPASSWORDRESET, 'Pwd rst')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISCREATEACCOUNTATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISCREATEACCOUNTATTEMPT, 'Cr acnt')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT, 'dpl acnt')),
            array('', TSysCMSUsersFloodDetect::FIELD_ISDIRECTORYTRAVERSALATTEMPT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_ISDIRECTORYTRAVERSALATTEMPT, 'dir trav')),
            array('', TSysCMSUsersFloodDetect::FIELD_USERAGENT, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSUsersFloodDetect::FIELD_USERAGENT, 'User Agent')),
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
        return new TSysCMSUsersFloodDetect();
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
        return Mod_Sys_Security::PERM_CAT_LOGINFLOODLOGS;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return '';
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
        return transm(CMS_CURRENTMODULE, 'floodlogs_list_title', 'Flood detection logs');
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