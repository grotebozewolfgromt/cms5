<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\controllers\TCRUDDetailSaveController_org;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\tag\form\InputDatetime;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\Script;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use  dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersAccounts;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\models\TSysUsersAbstract;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_organizations extends TCRUDDetailSaveController
{
    private $objEdtCustomIdentifier = null;//dr\classes\dom\tag\form\InputText
    private $objSelContactID = null;//dr\classes\dom\tag\form\Select     
    private $objChkLoginEnabled = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objEdtLoginExpiresDate = null;//dr\classes\dom\tag\form\InputText
    private $objEdtLoginExpiresTime = null;//dr\classes\dom\tag\form\InputTime
    private $objEdtDeleteAfterDate = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDeleteAfterTime = null;//dr\classes\dom\tag\form\InputTime

  
    // private $objLblHintSessions1 = null;//Label
    // private $objLblHintSessions2 = null;//Label
    // private $objUserSessions = null;//TSysCMSUserSessions
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        global $objAuthenticationSystem;
      
                
            //custom identifier
        $this->objEdtCustomIdentifier = new InputText();
        $this->objEdtCustomIdentifier->setNameAndID('edtCustomIdentifier');
        $this->objEdtCustomIdentifier->setClass('fullwidthtag');   
        $this->objEdtCustomIdentifier->setRequired(true);   
        $this->objEdtCustomIdentifier->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtCustomIdentifier->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtCustomIdentifier->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtCustomIdentifier, '', transm($this->getModule(), 'form_FIELD_CUSTOMID', 'Label (used to identify this account just to you)'));

        //contact id
        $this->objSelContactID = new Select();
        $this->objSelContactID->setNameAndID('optContactID');
        $this->getFormGenerator()->add($this->objSelContactID, '', transm($this->getModule(), 'form_field_contactid', 'Contact'));
        
        //login enabled
        $this->objChkLoginEnabled = new InputCheckbox();
        $this->objChkLoginEnabled->setNameAndID('edtLoginEnabled');
        $this->getFormGenerator()->add($this->objChkLoginEnabled, '', transm($this->getModule(), 'form_field_enabled', 'able to log in (users in this account)'));         


        //login expires      
            $this->objEdtLoginExpiresDate = new DRInputDateTime();
            $this->objEdtLoginExpiresDate->setNameAndID('edtLoginExpiresDate');
            $this->objEdtLoginExpiresDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
            $this->objEdtLoginExpiresDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
            $this->objEdtLoginExpiresDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
            $this->objEdtLoginExpiresDate->setAllowEmptyDateTime(true);
            $this->getFormGenerator()->add($this->objEdtLoginExpiresDate, '', transm($this->getModule(), 'form_field_loginexpires', 'Login expires after (users in account can\'t log in after this date, empty = no expiration)')); 


        //scheduled for deletion after
            $this->objEdtDeleteAfterDate = new DRInputDateTime();
            $this->objEdtDeleteAfterDate->setNameAndID('edtDeleteAfter');
            $this->objEdtDeleteAfterDate->setPHPDateFormat($objAuthenticationSystem->getUsers()->getDateFormatLong()); //reads user preferences date
            $this->objEdtDeleteAfterDate->setPHPTimeFormat($objAuthenticationSystem->getUsers()->getTimeFormatLong()); //reads user preferences time
            $this->objEdtDeleteAfterDate->setFirstDayOfWeek($objAuthenticationSystem->getUsers()->getWeekStartsOn()); //reads user preferences first day
            $this->objEdtDeleteAfterDate->setAllowEmptyDateTime(true);
            $this->getFormGenerator()->add($this->objEdtDeleteAfterDate, '', transm($this->getModule(), 'form_field_deleteafter', 'Auto delete account after (also deleting ALL users in account, empty = no deletion)')); 


        //users in account
        //done in modelToView()
        
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_CMSUsers::PERM_CAT_ORGANIZATIONS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //custom identifier
        $this->getModel()->set(TSysCMSOrganizations::FIELD_CUSTOMID, $this->objEdtCustomIdentifier->getValueSubmitted());

        //contactid
        $this->getModel()->set(TSysCMSOrganizations::FIELD_CONTACTID, $this->objSelContactID->getValueSubmittedAsInt());

        //login enabled
        $this->getModel()->set(TSysCMSOrganizations::FIELD_LOGINENABLED, $this->objChkLoginEnabled->getValueSubmittedAsBool());        

        //login expires
        $this->getModel()->set(TSysCMSOrganizations::FIELD_LOGINEXPIRES, $this->objEdtLoginExpiresDate->getValueSubmittedAsTDateTimeISO());
            
        //scheduled delete after
        $this->getModel()->set(TSysCMSOrganizations::FIELD_DELETEAFTER, $this->objEdtDeleteAfterDate->getValueSubmittedAsTDateTimeISO());

        //users in account
        //@todo able add users from this screen in the future
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //custom identifier
        $this->objEdtCustomIdentifier->setValue($this->getModel()->get(TSysCMSOrganizations::FIELD_CUSTOMID));

        //contact id
        $objContacts = new TSysContacts();
        $objContacts->sort(TSysContacts::FIELD_CUSTOMID);
        $objContacts->limitNone(); //not very fun, but hope to have a better solution in the future
        $objContacts->loadFromDB();
        $objContacts->generateHTMLSelect($this->getModel()->get(TSysCMSOrganizations::FIELD_CONTACTID), $this->objSelContactID);

        //login enabled
        $this->objChkLoginEnabled->setChecked($this->getModel()->get(TSysCMSOrganizations::FIELD_LOGINENABLED));   

        //login expires
            // $this->objEdtLoginExpiresDate->setValue($this->getModel()->getDateAsString(TSysCMSOrganizations::FIELD_LOGINEXPIRES, $this->getDateFormatDefault())); 
            // $this->objEdtLoginExpiresTime->setValue($this->getModel()->getDateAsString(TSysCMSOrganizations::FIELD_LOGINEXPIRES, $this->getTimeFormatDefault())); 
            $this->objEdtLoginExpiresDate->setValueAsTDateTime($this->getModel()->get(TSysCMSOrganizations::FIELD_LOGINEXPIRES));

        //scheduled delete after
            // $this->objEdtDeleteAfterDate->setValue($this->getModel()->getDateAsString(TSysCMSOrganizations::FIELD_DELETEAFTER, $this->getDateFormatDefault())); 
            // $this->objEdtDeleteAfterTime->setValue($this->getModel()->getDateAsString(TSysCMSOrganizations::FIELD_DELETEAFTER, $this->getTimeFormatDefault())); 
            $this->objEdtDeleteAfterDate->setValueAsTDateTime($this->getModel()->get(TSysCMSOrganizations::FIELD_DELETEAFTER));

        //sessions
        $this->populateUsers();

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
        // $this->objUserSessions = new TSysCMSOrganizations();
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
        return true;
    }    


    /**
     * populate sessions
     * we need to call them twice: once on form load and once when clicked save and sessions are deleted
     */
    private function populateUsers()
    {
        // $this->objLblHintUsersInAccount = new Label();  
        $sTransSectionUsersInAccount = '';

        $sTransSectionUsersInAccount = transm($this->getModule(), 'section_useraccounts_detail_usersinaccount_title', 'Users in this account');

        

        if ($this->getModel()->getNew()) //existing record
        {
            // $this->objLblHintUsersInAccount->setText(transm($this->getModule(), 'section_useraccounts_detail_usersinaccount_newrecordnousers', 'Create account first before you can assign users'));        
            // $this->getFormGenerator()->add($this->objLblHintUsersInAccount, $sTransSectionUsersInAccount);        
        }
        else
        {
            //load from db
            $objUsers = new TSysCMSUsers();
            $objUsers->limitNone();
            $objUsers->find(TSysCMSUsers::FIELD_CMSORGANISATIONSID, $this->getModel()->getID());
            $objUsers->loadFromDB();
    

            // $this->objLblHintUsersInAccount->setText(transm($this->getModule(), 'section_useraccounts_detail_usersinaccount_explanation', '[amount] user(s) are part of this account:', 'amount', $objUsers->count()));        
            // $this->getFormGenerator()->add($this->objLblHintUsersInAccount, $sTransSectionUsersInAccount);        



            //display users
            while($objUsers->next())
            {             
                $objLblUserLine = new Label();
                $objLblUserLine->setText('-'.$objUsers->getUsername());        
                $this->getFormGenerator()->add($objLblUserLine, $sTransSectionUsersInAccount);        
    

            }   
            
        }
    }



   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysCMSOrganizations(); 
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
        return 'list_organizations';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_useraccount_new', 'Create new user account');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_useraccount_edit', 'Edit user account: [identifier]', 'identifier', $this->getModel()->getCustomIdentifier());   
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
