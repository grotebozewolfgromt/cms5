<?php
namespace dr\modules\Mod_Sys_Security\controllers;

use dr\classes\controllers\TCRUDListController;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
use dr\modules\Mod_Sys_Security\Mod_Sys_Security;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


class list_loginipblackwhitelist extends TCRUDListController
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
            TSysCMSLoginIPBlackWhitelist::FIELD_ID, 
            TSysCMSLoginIPBlackWhitelist::FIELD_STARTDATE, 
            TSysCMSLoginIPBlackWhitelist::FIELD_ENDDATE, 
            TSysCMSLoginIPBlackWhitelist::FIELD_IPADDRESS, 
            TSysCMSLoginIPBlackWhitelist::FIELD_NOTES,
            TSysCMSLoginIPBlackWhitelist::FIELD_ENABLED,
            TSysCMSLoginIPBlackWhitelist::FIELD_BLACKLISTED,
            TSysCMSLoginIPBlackWhitelist::FIELD_WHITELISTED,
            TSysCMSLoginIPBlackWhitelist::FIELD_RECORDCREATED,
            TSysCMSLoginIPBlackWhitelist::FIELD_RECORDCHANGED,
                                    ));        
                
        $this->executeDB(true);
        
            
        //===show what?
        $arrTableColumnsShow = array(
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_RECORDCHANGED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_RECORDCHANGED, 'Changed')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_STARTDATE, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_STARTDATE, 'Starts')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_ENDDATE, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_ENDDATE, 'Ends')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_IPADDRESS, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_IPADDRESS, 'IP')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_NOTES, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_NOTES, 'Notes')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_ENABLED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_ENABLED, 'Enable')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_BLACKLISTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_BLACKLISTED, 'Black')),
            array('', TSysCMSLoginIPBlackWhitelist::FIELD_WHITELISTED, transm(CMS_CURRENTMODULE, 'overview_column_'.TSysCMSLoginIPBlackWhitelist::FIELD_WHITELISTED, 'White')),
                );
        
        if (!APP_CMS_LOGINONLYWHITELISTEDIPS)
            sendMessageNotification(transm(CMS_CURRENTMODULE, 'blackwhitelist_notification_whitelist_disabled', 'Whitelist is DISABLED in config file.<br>So, whitelist items are ignored'));
     
        
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
        return new TSysCMSLoginIPBlackWhitelist();
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
        return Mod_Sys_Security::PERM_CAT_LOGINIPBLACKWHITELIST;
    }

     /**
     * returns the url for the detailpage for the browser to go to
     *
     * @return string
     */
    public function getDetailPageURL()
    {
        return 'detailsave_loginipblackwhitelist';
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
        return transm(CMS_CURRENTMODULE, 'ipblackwhitelist_list_title', 'IP address login blacklist &amp whitelist');
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