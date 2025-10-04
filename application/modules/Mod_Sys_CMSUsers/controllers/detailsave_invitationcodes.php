<?php

/*
 */

namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\validator\Characterwhitelist;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Onlynumeric;
use dr\classes\dom\validator\Required;
use dr\classes\dom\validator\Time;
//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSInvitationCodes;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');


/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_invitationcodes extends TCRUDDetailSaveController
{   
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtCode = null;//dr\classes\dom\tag\form\InputText
    private $objEdtRedeems = null;//dr\classes\dom\tag\form\InputNumber
    private $objEdtMaxRedeems = null;//dr\classes\dom\tag\form\InputNumber
    private $objEdtStartDate = null;//dr\classes\dom\tag\form\InputDate
    private $objEdtStartTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtEndDate = null;//dr\classes\dom\tag\form\InputDate
    private $objEdtEndTime = null;//dr\classes\dom\tag\form\InputTime
    private $objChkEnabled = null;//dr\classes\dom\tag\form\InputCheckbox

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        global $objAuthenticationSystem;

            //name
        $this->objEdtName = new InputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');         
        $this->objEdtName->setRequired(true);   
        $this->objEdtName->setMaxLength(100);                
        $objValidator = new Maximumlength(100);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new Required();
        $this->objEdtName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtName, '', transm($this->getModule(), 'form_field_namecode', 'name (only you can see it)'));

            //code
        $this->objEdtCode = new InputText();
        $this->objEdtCode->setNameAndID('edtCode');
        $this->objEdtCode->setClass('fullwidthtag');                 
        $this->objEdtCode->setRequired(true); 
        $this->objEdtCode->setMaxLength(100);    
        $objValidator = new Maximumlength(100);
        $this->objEdtCode->addValidator($objValidator);  
        $objValidator = new Required();
        $this->objEdtCode->addValidator($objValidator);       
        $objValidator = new Characterwhitelist(TSysCMSInvitationCodes::ALLOWEDCHARSCODE);
        $this->objEdtCode->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtCode, '', transm($this->getModule(), 'form_field_invitationcode', 'Invitation code (allowed: [allowed])','allowed',  TSysCMSInvitationCodes::ALLOWEDCHARSCODE)); 
   
            //current redeems
        $this->objEdtRedeems = new InputNumber();
        $this->objEdtRedeems->setNameAndID('edtRedeems');
        $this->objEdtRedeems->setRequired(true); 
        $this->objEdtRedeems->setMaxLength(10);    
        $objValidator = new Maximumlength(10);
        $this->objEdtRedeems->addValidator($objValidator);  
        $objValidator = new Required();
        $this->objEdtRedeems->addValidator($objValidator);       
        $objValidator = new Onlynumeric();
        $this->objEdtRedeems->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtRedeems, '', transm($this->getModule(), 'form_field_currentredeems', 'Amount of times code is redeemed')); 
        
            //max redeems
        $this->objEdtMaxRedeems = new InputNumber();
        $this->objEdtMaxRedeems->setNameAndID('edtMaxRedeems');
        $this->objEdtMaxRedeems->setRequired(true); 
        $this->objEdtMaxRedeems->setMaxLength(10);    
        $objValidator = new Maximumlength(10);
        $this->objEdtMaxRedeems->addValidator($objValidator);  
        $objValidator = new Required();
        $this->objEdtMaxRedeems->addValidator($objValidator);       
        $objValidator = new Onlynumeric();
        $this->objEdtMaxRedeems->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtMaxRedeems, '', transm($this->getModule(), 'form_field_maxredeems', 'Limit redemptions (0=unlimited)')); 
    
            //==== start date+time
            // //date
            // $this->objEdtStartDate = new InputDate($this->getDateFormatDefault());
            // $this->objEdtStartDate->setNameAndID('edtStartDate');   
            // // $objDateMin = new TDateTime(time());
            // // $objValidator = new DateMin(transcms($objDateMin->getDateAsString($this->getDateFormatDefault())), $objDateMin, $this->getDateFormatDefault(),  true);
            // // $this->objEdtStartDate->addValidator($objValidator);                                    
            
            // //time
            // $this->objEdtStartTime = new InputTime($this->getTimeFormatDefault());
            // $this->objEdtStartTime->setNameAndID('edtStartTime');
            // $objValidator = new Time($this->getTimeFormatDefault(), true);
            // $this->objEdtStartTime->addValidator($objValidator);                        
            
            // $this->getFormGenerator()->addArray(array($this->objEdtStartDate, $this->objEdtStartTime), '', transm($this->getModule(), 'form_field_invitationcodes_startdatetime', 'Start date'));        
          	$this->objEdtStartDate = new DRInputDateTime();
  			$this->objEdtStartDate->setNameAndID('edtStartDate');
  			$this->objEdtStartDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
  			$this->objEdtStartDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
  			$this->objEdtStartDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
  			$this->objEdtStartDate->setAllowEmptyDateTime(true);
  			$this->getFormGenerator()->add($this->objEdtStartDate, '', transm($this->getModule(), 'form_field_invitationcodes_startdatetime', 'Start date')); 

            //==== end date+time
            // //date
            // $this->objEdtEndDate = new InputDate($this->getDateFormatDefault());
            // $this->objEdtEndDate->setNameAndID('edtEndDate');   
            // // $objDateMin = new TDateTime(time());
            // // $objValidator = new DateMin(transcms('form_error_dateneedstobebefore', 'Date needs to be later than [date]', 'date', $objDateMin->getDateAsString($this->getDateFormatDefault())), $objDateMin, $this->getDateFormatDefault(),  true);
            // // $this->objEdtEndDate->addValidator($objValidator);                                    
            
            // //time
            // $this->objEdtEndTime = new InputTime($this->getTimeFormatDefault());
            // $this->objEdtEndTime->setNameAndID('edtEndTime');
            // $objValidator = new Time($this->getTimeFormatDefault(), true);
            // $this->objEdtEndTime->addValidator($objValidator);                        
            
            // $this->getFormGenerator()->addArray(array($this->objEdtEndDate, $this->objEdtEndTime), '', transm($this->getModule(), 'form_field_invitationcodes_enddatetime', 'End date'));        
            $this->objEdtEndDate = new DRInputDateTime();
            $this->objEdtEndDate->setNameAndID('edtEndDate');
            $this->objEdtEndDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
            $this->objEdtEndDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
            $this->objEdtEndDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
            $this->objEdtEndDate->setAllowEmptyDateTime(true);
            $this->getFormGenerator()->add($this->objEdtEndDate, '', transm($this->getModule(), 'form_field_invitationcodes_enddatetime', 'End date')); 



            //is enabled
        $this->objChkEnabled = new InputCheckbox();
        $this->objChkEnabled->setNameAndID('chkEnabled');
        $this->getFormGenerator()->add($this->objChkEnabled, '', transm($this->getModule(), 'form_field_invitationcode_isenabled', 'Enabled'));           
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_CMSUsers::PERM_CAT_INVITATIONCODES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysCMSInvitationCodes::FIELD_CODENAME, $this->objEdtName->getValueSubmitted());
        $this->getModel()->set(TSysCMSInvitationCodes::FIELD_REDEMPTIONCODE, $this->objEdtCode->getValueSubmitted());
        $this->getModel()->set(TSysCMSInvitationCodes::FIELD_CURRENTREDEEMS, $this->objEdtRedeems->getValueSubmitted());
        $this->getModel()->set(TSysCMSInvitationCodes::FIELD_MAXREDEEMS, $this->objEdtMaxRedeems->getValueSubmitted());

        //start date+time
            //we set the time first so it defaults to 0:00 if the field is empty, but the date is not. the date & time are also empty when you leave the date field empty
            // $this->getModel()->setTimeAsString(TSysCMSInvitationCodes::FIELD_DATESTART, $this->objEdtStartTime->getValueSubmitted(), $this->getTimeFormatDefault());        
            // $this->getModel()->setDateAsString(TSysCMSInvitationCodes::FIELD_DATESTART, $this->objEdtStartDate->getValueSubmitted(), $this->getDateFormatDefault());                    
            $this->getModel()->set(TSysCMSInvitationCodes::FIELD_DATESTART, $this->objEdtStartDate->getValueSubmittedAsTDateTimeISO());

        //end date+time
            //we set the time first so it defaults to 0:00 if the field is empty, but the date is not. the date & time are also empty when you leave the date field empty
            // $this->getModel()->setTimeAsString(TSysCMSInvitationCodes::FIELD_DATEEND, $this->objEdtEndTime->getValueSubmitted(), $this->getTimeFormatDefault());        
            // $this->getModel()->setDateAsString(TSysCMSInvitationCodes::FIELD_DATEEND, $this->objEdtEndDate->getValueSubmitted(), $this->getDateFormatDefault());                    
            $this->getModel()->set(TSysCMSInvitationCodes::FIELD_DATEEND, $this->objEdtEndDate->getValueSubmittedAsTDateTimeISO());

        $this->getModel()->set(TSysCMSInvitationCodes::FIELD_ISENABLED, $this->objChkEnabled->getValueSubmittedAsBool());                
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {         
        $this->objEdtName->setValue($this->getModel()->get(TSysCMSInvitationCodes::FIELD_CODENAME));
        $this->objEdtCode->setValue($this->getModel()->get(TSysCMSInvitationCodes::FIELD_REDEMPTIONCODE));
        $this->objEdtRedeems->setValue($this->getModel()->get(TSysCMSInvitationCodes::FIELD_CURRENTREDEEMS));
        $this->objEdtMaxRedeems->setValue($this->getModel()->get(TSysCMSInvitationCodes::FIELD_MAXREDEEMS));

        //start date
        // $this->objEdtStartDate->setValue($this->getModel()->getDateAsString(TSysCMSInvitationCodes::FIELD_DATESTART, $this->getDateFormatDefault())); 
        // $this->objEdtStartTime->setValue($this->getModel()->getDateAsString(TSysCMSInvitationCodes::FIELD_DATESTART, $this->getTimeFormatDefault())); 
        $this->objEdtStartDate->setValueAsTDateTime($this->getModel()->get(TSysCMSInvitationCodes::FIELD_DATESTART));

        //end date
        // $this->objEdtEndDate->setValue($this->getModel()->getDateAsString(TSysCMSInvitationCodes::FIELD_DATEEND, $this->getDateFormatDefault())); 
        // $this->objEdtEndTime->setValue($this->getModel()->getDateAsString(TSysCMSInvitationCodes::FIELD_DATEEND, $this->getTimeFormatDefault())); 
        $this->objEdtEndDate->setValueAsTDateTime($this->getModel()->get(TSysCMSInvitationCodes::FIELD_DATEEND));


        $this->objChkEnabled->setChecked($this->getModel()->get(TSysCMSInvitationCodes::FIELD_ISENABLED));
    }


    
    /**
     * is called when a record is loaded
     */
    public function onLoad() 
    {        
        
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
     * @return boolean returns true on success otherwise false
     */
    public function onSavePost($bWasSaveSuccesful){ return true; }
    
    
    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
    }      

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
        return new TSysCMSInvitationCodes(); 
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
        return 'list_invitationcodes';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_invitationcodes_new', 'Create new invitation code');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_invitationcodes_edit', 'Edit invitation code: [name]', 'name', $this->getModel()->getCodeName());           
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
