<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Security\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\validator\TIPAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
//don't forget ;)
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
use dr\modules\Mod_Sys_Security\Mod_Sys_Security;
use dr\modules\Mod_Sys_Websites\Mod_Sys_Websites;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_loginipblackwhitelist extends TCRUDDetailSaveController
{
    private $objEdtStartDate = null;//dr\classes\dom\tag\form\InputDate
    private $objEdtStartTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtEndDate = null;//dr\classes\dom\tag\form\InputDate
    private $objEdtEndTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtIPAdress = null;//dr\classes\dom\tag\form\InputText
    private $objEdtNotes = null;//dr\classes\dom\tag\form\InputText
    private $objChkEnabled = null;//dr\classes\dom\tag\form\InputCheck
    private $objChkBlacklisted = null;//dr\classes\dom\tag\form\InputCheck
    private $objChkWhitelisted = null;//dr\classes\dom\tag\form\InputCheck
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        global $objAuthenticationSystem;
            //====start date/time
        $this->objEdtStartDate = new DRInputDateTime();
        $this->objEdtStartDate->setNameAndID('edtStartDate');
        $this->objEdtStartDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
        $this->objEdtStartDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
        $this->objEdtStartDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objEdtStartDate->setAllowEmptyDateTime(true);
        $this->getFormGenerator()->add($this->objEdtStartDate, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_startdate', 'Start date (goes in effect on)')); 
       

            //====end date/time        
        $this->objEdtEndDate = new DRInputDateTime();
        $this->objEdtEndDate->setNameAndID('edtEndDate');
        $this->objEdtEndDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
        $this->objEdtEndDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
        $this->objEdtEndDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
        $this->objEdtEndDate->setAllowEmptyDateTime(true);
        $this->getFormGenerator()->add($this->objEdtEndDate, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_enddate', 'End date (stops on)')); 

            //ip address
        $this->objEdtIPAdress = new InputText();
        $this->objEdtIPAdress->setNameAndID('edtIPAddress');
        $this->objEdtIPAdress->setClass('fullwidthtag');         
        $this->objEdtIPAdress->setRequired(true);   
        $this->objEdtIPAdress->setMaxLength(LENGTH_STRING_IPV6);
        $objValidator = new TMaximumLength(LENGTH_STRING_IPV6);
        $this->objEdtIPAdress->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtIPAdress->addValidator($objValidator);    
        $objValidator = new TIPAddress();
        $this->objEdtIPAdress->addValidator($objValidator);    

        $this->getFormGenerator()->add($this->objEdtIPAdress, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_ipaddress', 'IP address (IPv4 or IPv6)'));

            //notes
        $this->objEdtNotes = new InputText();
        $this->objEdtNotes->setNameAndID('edtNotes');
        $this->objEdtNotes->setClass('fullwidthtag');                 
        $this->objEdtNotes->setMaxLength(255);    
        $objValidator = new TMaximumLength(255);
        $this->objEdtNotes->addValidator($objValidator);        
        $this->getFormGenerator()->add($this->objEdtNotes, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_notes', 'Notes (like: reason or who ip belongs to)')); 

            //enabled
        $this->objChkEnabled = new InputCheckbox();
        $this->objChkEnabled->setNameAndID('edtEnabled');
        $this->getFormGenerator()->add($this->objChkEnabled, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_enabled', 'Enabled'));         

            //blacklisted
        $this->objChkBlacklisted = new InputCheckbox();
        $this->objChkBlacklisted->setNameAndID('edtBlacklisted');
        $this->getFormGenerator()->add($this->objChkBlacklisted, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_blacklisted', 'Blacklisted - NOT allowed to log in'));         

            //whitelisted
        $this->objChkWhitelisted = new InputCheckbox();
        $this->objChkWhitelisted->setNameAndID('edtWhitelisted');
        $this->getFormGenerator()->add($this->objChkWhitelisted, '', transm($this->getModule(), 'loginipblackwhitelist_form_field_whitelisted', 'Whitelisted - allowed to log in'));         
            
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Security::PERM_CAT_LOGINIPBLACKWHITELIST;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_STARTDATE, $this->objEdtStartDate->getValueSubmittedAsTDateTimeISO());        
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_ENDDATE, $this->objEdtEndDate->getValueSubmittedAsTDateTimeISO());                
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_IPADDRESS, $this->objEdtIPAdress->getValueSubmitted());
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_NOTES, $this->objEdtNotes->getValueSubmitted());
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_ENABLED, $this->objChkEnabled->getValueSubmittedAsBool());        
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_BLACKLISTED, $this->objChkBlacklisted->getValueSubmittedAsBool());        
        $this->getModel()->set(TSysCMSLoginIPBlackWhitelist::FIELD_WHITELISTED, $this->objChkWhitelisted->getValueSubmittedAsBool());        
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        $this->objEdtStartDate->setValueAsTDateTime($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_STARTDATE));        
        $this->objEdtEndDate->setValueAsTDateTime($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_ENDDATE));        
        $this->objEdtIPAdress->setValue($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_IPADDRESS));
        $this->objEdtNotes->setValue($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_NOTES));
        $this->objChkEnabled->setChecked($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_ENABLED));   
        $this->objChkBlacklisted->setChecked($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_BLACKLISTED));   
        $this->objChkWhitelisted->setChecked($this->getModel()->get(TSysCMSLoginIPBlackWhitelist::FIELD_WHITELISTED));   
        
    }

   /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
        $sMessageTextWhitelistEnabledConfig = '';
        
        if (APP_CMS_LOGINONLYWHITELISTEDIPS)
            $sMessageTextWhitelistEnabledConfig = transm($this->getModule(), 'loginipblackwhitelistdetail_notification_whitelistenabledconfig_enabled', 'Note: whitelist is ENABLED in config file.');
        else
            $sMessageTextWhitelistEnabledConfig = transm($this->getModule(), 'loginipblackwhitelistdetail_notification_whitelistenabledconfig_disabled', 'Note: whitelist is DISABLED in config file.');

        //test rules with current ip address
        $objTestList = new TSysCMSLoginIPBlackWhitelist();
        $objTestList->loadFromDBByIPAddress($this->getModel()->getIPAddress());
        if ($objTestList->isAllowedLogic($this->getModel()->getIPAddress()))
            sendMessageNotification(transm($this->getModule(), 'loginipblackwhitelistdetail_notification_logiclistrulesresult_allowed', 'Based on all rules present in database:<br>[ip] is ALLOWED to log in.<br>[configwhitelistmessage]', 'ip', $this->getModel()->getIPAddress(), 'configwhitelistmessage', $sMessageTextWhitelistEnabledConfig));
        else
            sendMessageNotification(transm($this->getModule(), 'loginipblackwhitelistdetail_notification_logiclistrulesresult_notallowed', 'Based on all rules present in database:<br>[ip] is NOT ALLOWED to log in<br>[configwhitelistmessage]', 'ip', $this->getModel()->getIPAddress(), 'configwhitelistmessage', $sMessageTextWhitelistEnabledConfig));


        // vardump($this->getModel()->getIPAddress());
    }
    
    /**
     * is called when a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * @return boolean it will NOT SAVE
     */
    public function onSavePre()
    {
        return true;
    }    

    /**
     * is called AFTER a record is saved
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     */
    public function onSavePost($bWasSaveSuccesful) { return true; }   
    
    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() {}      
    
    /**
     * sometimes you don;t want to user the checkin checkout system, even though the model supports it
     * for example: the settings.
     * The user needs to be able to navigate through the tabsheets, without locking records
     * 
     * ATTENTION: if this method returns true and the model doesn't support it: the checkinout will NOT happen!
     * 
     * @return bool return true if you want to use the check-in/checkout-system
     */
    public function getUseCheckinout()
    {
        return false;
    }    


   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysCMSLoginIPBlackWhitelist(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsave.php';
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
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        return 'list_loginipblackwhitelist';
    }

    /**
     * return page title
     * This title is different for creating a new record and editing one.
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    public function getTitle()
    {
        //global CMS_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transm(CMS_CURRENTMODULE, 'loginipblackwhitelist_pagetitle_detailsave_record_new', 'Create new blacklist/whitelist rule');
        else
            return transm(CMS_CURRENTMODULE, 'loginipblackwhitelist_pagetitle_detailsave_record_edit', 'Edit rule: [ipaddress]', 'ipaddress', $this->getModel()->getIPAddress());           
    }

    /**
     * show tabsheets on top of the page?
     *
     * @return bool
     */
    public function showTabs()
    {
        return false;
    }    
  
}
