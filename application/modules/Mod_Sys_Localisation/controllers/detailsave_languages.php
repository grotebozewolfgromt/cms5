<?php

namespace dr\modules\Mod_Sys_Localisation\controllers;

use dr\classes\controllers\TCRUDDetailSaveControllerAJAX;
use dr\classes\models\TSysModel;
use dr\classes\controllers\TAJAXFormController;
use dr\classes\controllers\TFormControllerCMS;
use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\TCharacterWhitelist;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\Dutchzipcode;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\TIPAddress;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TMinimumLength;
use dr\classes\dom\validator\Onlyalfanumeric;
use dr\classes\dom\validator\TOnlyAlphabetical;
use dr\classes\dom\validator\TOnlyNumeric;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\Time;
use dr\classes\dom\validator\URL;
//don't forget ;)
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
use dr\modules\Mod_Sys_Localisation\models\TSysActiveLanguagesPerSite;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_Localisation\Mod_Sys_Localisation;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_languages extends TCRUDDetailSaveControllerAJAX
{
    CONST ACTIVESITESCHECKBOXNAME = 'edtActiveSite';
    
    private $objEditLocale = null;//dr\classes\dom\tag\form\InputText
    private $objEditLanguage = null;//dr\classes\dom\tag\form\InputText
    private $objChkCMSLanguage = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkFavorite = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkDefaultSystem = null;//dr\classes\dom\tag\form\InputCheckbox
    
    
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //locale
        $this->objEditLocale = new InputText();
        $this->objEditLocale->setNameAndID('edtLocale');
        // $this->objEditLocale->setClass('fullwidthtag');         
        $this->objEditLocale->setRequired(true);   
        // $this->objEditLocale->setMaxLength(11);                
        // $this->objEditLocale->setOnchange("validateField(this, true)");
        // $this->objEditLocale->setOnkeyup("setDirtyRecord()");
        $objValidator = new TMaximumLength(11);
        $this->objEditLocale->addValidator($objValidator);    
        $objValidator = new TMinimumLength(2);
        $this->objEditLocale->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEditLocale->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEditLocale, '', transm(CMS_CURRENTMODULE, 'languages_form_field_locale', 'Locale code (format: AA-AA)'));

            //language
        $this->objEditLanguage = new InputText();
        $this->objEditLanguage->setNameAndID('edtLanguage');
        $this->objEditLanguage->setClass('fullwidthtag');                 
        $this->objEditLanguage->setRequired(true); 
        $this->objEditLanguage->setMaxLength(100);    
        // $this->objEditLanguage->setOnchange("validateField(this, true)");        
        // $this->objEditLanguage->setOnkeyup("setDirtyRecord()");        
        $objValidator = new TMaximumLength(100);
        $this->objEditLanguage->addValidator($objValidator);  
        $objValidator = new TMinimumLength(3);
        $this->objEditLanguage->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEditLanguage->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEditLanguage, '', transm(CMS_CURRENTMODULE, 'languages_form_field_language', 'Language name (in English)')); 

            //is cms language
        $this->objChkCMSLanguage = new InputCheckbox();
        $this->objChkCMSLanguage->setNameAndID('chkCMSLanguage');
        // $this->objChkCMSLanguage->setOnchange("setDirtyRecord()");
        $this->getFormGenerator()->add($this->objChkCMSLanguage, '', transm(CMS_CURRENTMODULE, 'languages_form_field_iscmslanguage', 'is CMS language (makes CMS available in this language, creates language files)'));   
        
            //is shown in selectboxes
        $this->objChkFavorite = new InputCheckbox();
        $this->objChkFavorite->setNameAndID('chkCMSSelect');
        // $this->objChkFavorite->setOnchange("setDirtyRecord()");
        $this->getFormGenerator()->add($this->objChkFavorite, '', transm(CMS_CURRENTMODULE, 'languages_form_field_isfavorite', 'is favorite (only favorites are shown in User Interface elements where not all languages are shown, prevents having 400 languages to scroll through)'));         

            //is shown in selectboxes
        $this->objChkDefaultSystem = new InputCheckbox();
        $this->objChkDefaultSystem->setNameAndID('chkDefaultSystem');
        // $this->objChkDefaultSystem->setOnchange("setDirtyRecord()");
        $this->getFormGenerator()->add($this->objChkDefaultSystem, '', transm(CMS_CURRENTMODULE, 'languages_form_field_isdefaultsystem', 'Is system default (system assumes this language by default, only 1 language can be default)'));         

//            //active languages per site
//        $objChkActLangSite = null;
//        $stransgection = transm($this->getModule(), 'form_group_activelanguagesites', 'active language on website:');
//        
//        $arrCheckedSitesInDB = null; //we use a temp array (with siteid as key) to ensure fast searching for checked sites
//        while($this->objActLangSites->next())
//        {
//            $arrCheckedSitesInDB[$this->objActLangSites->getWebsiteID()] = 'checked';
//        }
//                
//        while($this->objWebsites->next()) //show all websites
//        {
//            $objChkActLangSite = new InputCheckbox();
//            $objChkActLangSite->setNameAndID(TCRUDDetailSaveLanguages::ACTIVESITESCHECKBOXNAME.$this->objWebsites->getID());            
//            $objChkActLangSite->setChecked(isset($arrCheckedSitesInDB[$this->objWebsites->getID()])); //here comes the fast searching for arraykeys into play
//            $this->getFormGenerator()->add($objChkActLangSite, $stransgection, $this->objWebsites->getWebsiteName());     
//        }
    }

    public function initModel()
    {

    }


    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysLanguages::FIELD_LOCALE, $this->objEditLocale->getValueSubmitted());
        $this->getModel()->set(TSysLanguages::FIELD_LANGUAGE, $this->objEditLanguage->getValueSubmitted());
        $this->getModel()->set(TSysLanguages::FIELD_ISCMSLANGUAGE, $this->objChkCMSLanguage->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysLanguages::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysLanguages::FIELD_ISDEFAULT, $this->objChkDefaultSystem->getValueSubmittedAsBool());                
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        if ($this->getModel()->getLocale() == APP_LOCALE_DEFAULT)
        {
            $this->objEditLocale->setReadOnly(true); 
            sendMessageNotification(transm(CMS_CURRENTMODULE, 'form_notification_defaultlocale_cantchangelocale', 'This is the default locale of the system, you cant change the locale itself'));
        }
        
        $this->objEditLocale->setValue($this->getModel()->get(TSysLanguages::FIELD_LOCALE));
        $this->objEditLanguage->setValue($this->getModel()->get(TSysLanguages::FIELD_LANGUAGE));
        $this->objChkCMSLanguage->setChecked($this->getModel()->get(TSysLanguages::FIELD_ISCMSLANGUAGE));
        $this->objChkFavorite->setChecked($this->getModel()->get(TSysLanguages::FIELD_ISFAVORITE));
        $this->objChkDefaultSystem->setChecked($this->getModel()->get(TSysLanguages::FIELD_ISDEFAULT));
    }


    
    /**
     * is called after a record is loaded
     */
    public function onLoadPost() 
    {        
        
    }
    
    /**
     * is called before a record is loaded
     */
    public function onLoadPre() 
    {        
        
    }
    
    /**
     * is called BEFORE a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @return array, empty array = no errors
     */
    public function onSavePre() { return array(); }    

    /**
     * is called AFTER a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return array, empty array = no errors
     */
    public function onSavePost($bWasSaveSuccesful){ return array(); }
    
    
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
        return true;
    }    

    /**
     * returns the nice-name of the controller that is using this record + the user name
     * 
     * @return string
     */
    public function getCheckoutSource()
    {
        global $objAuthenticationSystem;
        return $objAuthenticationSystem->getUsers()->getUserName().' on '.getIPAddressClient();
    }

   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysLanguages(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsaveajax.php';
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
        return 'list_languages';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_languages_new', 'Create new language');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_languages_edit', 'Edit language: [language]', 'language', $this->getModel()->getLanguage());           
    }

    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return;
    }    

   /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    public function getAuthCreate()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Localisation::PERM_CAT_LANGUAGES, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Localisation::PERM_CAT_LANGUAGES, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Localisation::PERM_CAT_LANGUAGES, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(CMS_CURRENTMODULE, Mod_Sys_Localisation::PERM_CAT_LANGUAGES, TModuleAbstract::PERM_OP_DELETE);
    }




}
