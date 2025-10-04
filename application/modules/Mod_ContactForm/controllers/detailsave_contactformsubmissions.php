<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_ContactForm\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputColor;
use dr\classes\dom\tag\form\Textarea;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\tag\form\InputDatetime;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\Script;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\ColorHex;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\dom\validator\Required;
use dr\classes\dom\validator\Emailaddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\IPAddress;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)

use dr\modules\Mod_ContactForm\Mod_ContactForm;
use dr\modules\Mod_ContactForm\models\TContactFormCategories;
use dr\modules\Mod_ContactForm\models\TContactFormSubmissions;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_contactformsubmissions extends TCRUDDetailSaveController
{
    private $objSelCategory = null;//dr\classes\dom\tag\form\Option
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtEmailAddress = null;//dr\classes\dom\tag\form\InputText
    private $objEdtTopic = null;//dr\classes\dom\tag\form\InputText
    private $objEdtMessage = null;//dr\classes\dom\tag\form\TextArea
    private $objEdtNotesInternal = null;//dr\classes\dom\tag\form\TextArea
    private $objEdtIPAddress = null;//dr\classes\dom\tag\form\InputText
    private $objEdtSpamLikelyHood = null;//dr\classes\dom\tag\form\InputText --> likelyhood detected automatically
    private $objChkSpamManual = null;//dr\classes\dom\tag\form\InputCheck --> spam manually marked as spam


    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //category
        $this->objSelCategory = new Select();
        $this->objSelCategory->setNameAndID('optCategory');
        $this->getFormGenerator()->add($this->objSelCategory, '', transm($this->getModule(), 'contactformsubmissions_form_field_category', 'Category'));        

            //name
        $this->objEdtName = new InputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');     
        $this->objEdtName->setMaxLength(100);
        $objValidator = new Maximumlength(100);
        $this->objEdtName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtName, '', transm($this->getModule(), 'form_field_name', 'Name'));
   
            //email address
        $this->objEdtEmailAddress = new InputText();
        $this->objEdtEmailAddress->setNameAndID('edtEmail');
        $this->objEdtEmailAddress->setClass('fullwidthtag');   
        $this->objEdtEmailAddress->setMaxLength(100);
        $objValidator = new Maximumlength(100);
        $this->objEdtEmailAddress->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtEmailAddress, '', transm($this->getModule(), 'form_field_emailaddress', 'Email address'));
        
            //topic
        $this->objEdtTopic = new InputText();
        $this->objEdtTopic->setNameAndID('edtTopic');
        $this->objEdtTopic->setClass('fullwidthtag');     
        $this->objEdtTopic->setMaxLength(100);
        $objValidator = new Maximumlength(100);
        $this->objEdtTopic->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtTopic, '', transm($this->getModule(), 'form_field_topic', 'Topic'));
    
            //message
        $this->objEdtMessage = new Textarea();
        $this->objEdtMessage->setNameAndID('edtMessage');
        $this->objEdtMessage->setClass('fullwidthtag');     
        $this->getFormGenerator()->add($this->objEdtMessage, '', transm($this->getModule(), 'form_field_message', 'Message'));

            //notes
        $this->objEdtNotesInternal = new Textarea();
        $this->objEdtNotesInternal->setNameAndID('edtNotes');
        $this->objEdtNotesInternal->setClass('fullwidthtag');   
        $this->getFormGenerator()->add($this->objEdtNotesInternal, '', transm($this->getModule(), 'form_field_notesinternal', 'Internal notes (not seen by sender)'));
    
            //ip address
        $this->objEdtIPAddress = new InputText();
        $this->objEdtIPAddress->setNameAndID('edtIPAdress');
        $this->objEdtIPAddress->setClass('fullwidthtag');   
        $this->objEdtIPAddress->setMaxLength(LENGTH_STRING_IPV6);
        $objValidator = new IPAddress();
        $this->objEdtIPAddress->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtIPAddress, '', transm($this->getModule(), 'form_field_ipaddress', 'IP address'));


            //spam likelyhood
        $this->objEdtSpamLikelyHood = new InputText();
        $this->objEdtSpamLikelyHood->setNameAndID('edtSpamLikely');
        $this->objEdtSpamLikelyHood->setContentEditable(false);
        // $this->objEdtSpamLikelyHood->setClass('fullwidthtag');   
        $this->getFormGenerator()->add($this->objEdtSpamLikelyHood, '', transm($this->getModule(), 'form_field_spamlikelyhoodpercent', 'Spam likelyhood % (auto detected)'));

           //manually marked as spam
        $this->objChkSpamManual = new InputCheckbox();
        $this->objChkSpamManual->setNameAndID('edtSpamManual');
        $this->getFormGenerator()->add($this->objChkSpamManual, '', transm($this->getModule(), 'form_field_spammanual', 'Mark as Spam (manually)'));         
       
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_ContactForm::PERM_CAT_CONTACTFORMSUBMISSIONS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //category        
        $this->getModel()->setContactFormCategoryID($this->objSelCategory->getValueSubmittedAsInt());

        //name
        $this->getModel()->setNameDecrypted($this->objEdtName->getValueSubmitted());

        //email
        $this->getModel()->setEmailAddressDecrypted($this->objEdtEmailAddress->getValueSubmitted());

        //topic
        $this->getModel()->setTopic($this->objEdtTopic->getValueSubmitted());

        //message
        $this->getModel()->setMessage($this->objEdtMessage->getValueSubmitted());

        //notes
        $this->getModel()->setNotesInternal($this->objEdtNotesInternal->getValueSubmitted());    
        
        //ip address
        $this->getModel()->setIPAddressDecrypted($this->objEdtIPAddress->getValueSubmitted());    

        //spam likelyhood %
        $this->getModel()->setSpamLikelyHood($this->objEdtSpamLikelyHood->getValueSubmitted());    

        //spam manual
        $this->getModel()->setSpamMarkedManually($this->objChkSpamManual->getValueSubmittedAsBool());
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //category
        $objCats = new TContactFormCategories();
        $objCats->sort(TContactFormCategories::FIELD_POSITION);
        $objCats->limitNone();
        $objCats->loadFromDB();
        $objCats->generateHTMLSelect($this->getModel()->get(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID), $this->objSelCategory);
        unset($objCats);

        //name
        $this->objEdtName->setValue($this->getModel()->getNameDecrypted());

        //emailadress
        $this->objEdtEmailAddress->setValue($this->getModel()->getEmailAddressDecrypted());

        //topic
        $this->objEdtTopic->setValue($this->getModel()->get(TContactFormSubmissions::FIELD_TOPIC));
        
        //message
        $this->objEdtMessage->setValue($this->getModel()->get(TContactFormSubmissions::FIELD_MESSAGE));        

        //notes
        $this->objEdtNotesInternal->setValue($this->getModel()->get(TContactFormSubmissions::FIELD_NOTESINTERNAL));        

        //ip address
        $this->objEdtIPAddress->setValue($this->getModel()->getIPAddressDecryped());        

        //spam likelyhood
        $this->objEdtSpamLikelyHood->setValue($this->getModel()->getSpamLikelyHood());        

        //spam likelyhood
        $this->objChkSpamManual->setChecked($this->getModel()->getSpamMarkedManually()); 
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
        return new TContactFormSubmissions(); 
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
        return 'list_contactformsubmissions';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_contactformsubmissions_new', 'Create new submissions');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_contactformsubmissions_edit', 'Edit submission by [name]', 'name', $this->getModel()->getNameDecrypted());   
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
