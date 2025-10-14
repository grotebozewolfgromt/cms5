<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Websites\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\webcomponents\DRInputCheckbox;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;

//don't forget ;)
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;
use dr\classes\TConfigFileWebsite;
use dr\modules\Mod_Sys_Websites\Mod_Sys_Websites;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_websites extends TCRUDDetailSaveController
{
    private $objEdtWebsiteName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtURL = null;//dr\classes\dom\tag\form\InputText
    private $objOptDefaultLanguage = null;//dr\classes\dom\tag\form\Select  
    private $objChkSystemDefault = null;//dr\classes\dom\tag\form\Checkbox  

    private $objEdtPathDomain = null;//dr\classes\dom\tag\form\InputText
    private $objEdtPathLocal = null;//dr\classes\dom\tag\form\InputText
    private $objEdtPathWWW = null;//dr\classes\dom\tag\form\InputText
    private $objSelTheme = null;//dr\classes\dom\tag\form\Select 

    private $objConfigFile = null;//TConfigFileWebsite


    public function __construct()
    {
        $this->objConfigFile = new TConfigFileWebsite();

        parent::__construct();
    }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        $sTransSectionDatasebase = transm($this->getModule(), 'detailsave_websites_sectiontitle_database', 'Database:');
        $sTransSectionConfigFile = transm($this->getModule(), 'detailsave_websites_sectiontitle_configfile', 'Configuration file: [filename]', 'filename', getPathConfigWebsite($this->getModel()->getWebsiteName(), $_SERVER['SERVER_NAME']));


        //=====DATABASE

            //website name
        $this->objEdtWebsiteName = new InputText();
        $this->objEdtWebsiteName->setNameAndID('edtWebsiteName');
        $this->objEdtWebsiteName->setClass('fullwidthtag');         
        $this->objEdtWebsiteName->setRequired(true);   
        $this->objEdtWebsiteName->setMaxLength(100);
        $objValidator = new TMaximumLength(100);
        $this->objEdtWebsiteName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtWebsiteName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtWebsiteName, $sTransSectionDatasebase, transm($this->getModule(), 'detailsave_websites_form_field_websitename', 'website name'));

            //URL
        $this->objEdtURL = new InputText();
        $this->objEdtURL->setNameAndID('edtURL');
        $this->objEdtURL->setClass('fullwidthtag');                 
        $this->objEdtURL->setRequired(true); 
        $this->objEdtURL->setMaxLength(100);    
        $objValidator = new TMaximumLength(100);
        $this->objEdtURL->addValidator($objValidator);        
        $objValidator = new TRequired();
        $this->objEdtURL->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtURL, $sTransSectionDatasebase, transm($this->getModule(), 'detailsave_websites_form_field_url', 'url')); 
        
        //language
        $this->objOptDefaultLanguage = new Select();
        $this->objOptDefaultLanguage->setNameAndID('optDefaultLanguage');
        $this->objOptDefaultLanguage->setClass('quarterwidthtag');           
        $this->getFormGenerator()->add($this->objOptDefaultLanguage, $sTransSectionDatasebase, transm($this->getModule(), 'detailsave_websites_form_field_defaultlanguage', 'default language'));        

        //default
        $this->objChkSystemDefault = new DRInputCheckbox();
        $this->objChkSystemDefault->setNameAndID('chkSystemDefault');       
        $this->objChkSystemDefault->setLabel(transm($this->getModule(), 'detailsave_websites_form_field_systemdefault', 'Is system default'));       
        $this->getFormGenerator()->add($this->objChkSystemDefault, $sTransSectionDatasebase);        


        //==== CONFIG FILE

            //domain
        $this->objEdtPathDomain = new InputText();
        $this->objEdtPathDomain->setNameAndID('edtPathDomain');
        $this->objEdtPathDomain->setClass('fullwidthtag');                 
        // $this->objEdtPathDomain->setRequired(true); 
        $this->objEdtPathDomain->setMaxLength(255);    
        $objValidator = new TMaximumLength(255);
        $this->objEdtPathDomain->addValidator($objValidator);        
        // $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
        // $this->objEdtPathDomain->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtPathDomain, $sTransSectionConfigFile, transm($this->getModule(), 'detailsave_websites_form_field_pathdomain', 'Domain (i.e. mywebsite.com)')); 
    
            //local path
        $this->objEdtPathLocal = new InputText();
        $this->objEdtPathLocal->setNameAndID('edtPathLocal');
        $this->objEdtPathLocal->setClass('fullwidthtag');                 
        // $this->objEdtPathLocal->setRequired(true); 
        $this->objEdtPathLocal->setMaxLength(255);    
        $objValidator = new TMaximumLength(255);
        $this->objEdtPathLocal->addValidator($objValidator);        
        // $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
        // $this->objEdtPathLocal->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtPathLocal, $sTransSectionConfigFile, transm($this->getModule(), 'detailsave_websites_form_field_pathlocal', 'Local path (i.e. C:\xampp\htdocs)')); 

            //www path
        $this->objEdtPathWWW = new InputText();
        $this->objEdtPathWWW->setNameAndID('edtPathWWW');
        $this->objEdtPathWWW->setClass('fullwidthtag');                 
        // $this->objEdtPathWWW->setRequired(true); 
        $this->objEdtPathWWW->setMaxLength(255);    
        $objValidator = new TMaximumLength(255);
        $this->objEdtPathWWW->addValidator($objValidator);        
        // $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
        // $this->objEdtPathWWW->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtPathWWW, $sTransSectionConfigFile, transm($this->getModule(), 'detailsave_websites_form_field_pathwww', 'Website URL (i.e. https://www.mywebsite.com)')); 

            //theme
        $this->objSelTheme = new Select();
        $this->objSelTheme->setNameAndID('optWebsiteTheme');
        $this->objSelTheme->setClass('quarterwidthtag');   
        $this->getFormGenerator()->add($this->objSelTheme, $sTransSectionConfigFile, transm($this->getModule(), 'detailsave_websites_form_field_theme', 'Theme'));         
    
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Websites::PERM_CAT_WEBSITES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //==== DATABASE
        $this->getModel()->set(TSysWebsites::FIELD_WEBSITENAME, $this->objEdtWebsiteName->getValueSubmitted());
        $this->getModel()->set(TSysWebsites::FIELD_URL, $this->objEdtURL->getValueSubmitted());
        //language
        $this->getModel()->set(TSysWebsites::FIELD_DEFAULTLANGUAGEID, $this->objOptDefaultLanguage->getValueSubmittedAsInt());
        //system default
        $this->objChkSystemDefault->getValueSubmittedAsBool();
        $this->getModel()->setIsDefault($this->objChkSystemDefault->getValueSubmittedAsBool());
        

        //==== CONFIG FILE

        //domain
        $this->objConfigFile->set('WEBSITE_PATH_DOMAIN', $this->objEdtPathDomain->getValueSubmitted());
        //local path
        $this->objConfigFile->set('WEBSITE_PATH_LOCAL', $this->objEdtPathLocal->getValueSubmitted());
        //www path
        $this->objConfigFile->set('WEBSITE_PATH_WWW', $this->objEdtPathWWW->getValueSubmitted());
        //website id, set default users can't change
        $this->objConfigFile->set("WEBSITE_DB_SITEID", $this->getModel()->getID());
        //theme
        if (file_exists(APP_PATH_WEBSITE_VIEWS_THEMES.DIRECTORY_SEPARATOR.$this->objSelTheme->getValueSubmitted()))//double check directory exists, because when it goes wrong the whole UI is gone!
            $this->objConfigFile->set('WEBSITE_THEME', $this->objSelTheme->getValueSubmitted());
        else
        {
            sendMessageError(transm($this->getModule(), 'errormessage_themes_dirnotfound', 'Themes directory "[directory]" not found. Setting NOT saved!', 'directory', $this->objSelTheme->getValueSubmitted()));
            logError(__FILE__.__LINE__, 'CMS theme directory "'.$this->objSelTheme->getValueSubmitted().'" does not exist. setting not saved');
        }

    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //==== DATABASE
        $this->objEdtWebsiteName->setValue($this->getModel()->get(TSysWebsites::FIELD_WEBSITENAME));
        $this->objEdtURL->setValue($this->getModel()->get(TSysWebsites::FIELD_URL));
        
        //language
        $objLangs = new \dr\modules\Mod_Sys_Localisation\models\TSysLanguages();
        $objLangs->sort(\dr\modules\Mod_Sys_Localisation\models\TSysLanguages::FIELD_LANGUAGE);
        $objLangs->loadFromDBByIsFavorite();
        $objLangs->generateHTMLSelect($this->getModel()->get(TSysWebsites::FIELD_DEFAULTLANGUAGEID), $this->objOptDefaultLanguage);
        
        //system default
        $this->objChkSystemDefault->setChecked($this->getModel()->getIsDefault());

        //==== CONFIG FILE

        //domain
        $this->objEdtPathDomain->setValue($this->objConfigFile->get('WEBSITE_PATH_DOMAIN'));
        //path local
        $this->objEdtPathLocal->setValue($this->objConfigFile->get('WEBSITE_PATH_LOCAL'));
        //path www
        $this->objEdtPathWWW->setValue($this->objConfigFile->get('WEBSITE_PATH_WWW'));
        //theme
        $arrThemeDirs = getFileFolderArray(APP_PATH_WEBSITE_VIEWS_THEMES, true, false);
        if ($arrThemeDirs !== false)
        {
            $this->objSelTheme->clear();
            foreach($arrThemeDirs as $sDir)
            {
                $this->objSelTheme->addOption($sDir, $sDir, ($sDir == $this->objConfigFile->get('WEBSITE_THEME')));
            }
        }

    }

   /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
        if (file_exists(getPathConfigWebsite($this->getModel()->getWebsiteName(), $_SERVER['SERVER_NAME'])))
        {
            $this->objConfigFile->loadFile(getPathConfigWebsite($this->getModel()->getWebsiteName(), $_SERVER['SERVER_NAME']));
        }

    }
    
    /**
     * is called when a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * @return boolean it will NOT SAVE
     */
    public function onSavePre()
    {
        return $this->objConfigFile->saveFile(getPathConfigWebsite($this->getModel()->getWebsiteName(), $_SERVER['SERVER_NAME']));
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
        return true;
    }    


   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysWebsites(); 
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
        return 'list_websites';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_websites_new', 'Create new website');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_websites_edit', 'Edit website: [website]', 'website', $this->getModel()->getWebsiteName());           
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
