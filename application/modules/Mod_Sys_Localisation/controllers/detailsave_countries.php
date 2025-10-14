<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Localisation\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;

//don't forget ;)
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_Sys_Localisation\Mod_Sys_Localisation;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_countries extends TCRUDDetailSaveController
{   
    private $objEdtCountry = null;//dr\classes\dom\tag\form\InputText
    private $objEdtISO2 = null;//dr\classes\dom\tag\form\InputText
    private $objEdtISO3 = null;//dr\classes\dom\tag\form\InputText
    private $objChkInEU = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkDefault = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkFavorite = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkUnknown = null;//dr\classes\dom\tag\form\InputCheckbox

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //country
        $this->objEdtCountry = new InputText();
        $this->objEdtCountry->setNameAndID('edtCountry');
        $this->objEdtCountry->setClass('fullwidthtag');         
        $this->objEdtCountry->setRequired(true);   
        $this->objEdtCountry->setMaxLength(100);                
        $objValidator = new TMaximumLength(100);
        $this->objEdtCountry->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtCountry->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtCountry, '', transm($this->getModule(), 'form_field_country', 'country'));

            //ISO2
        $this->objEdtISO2 = new InputText();
        $this->objEdtISO2->setNameAndID('edtISO2');
        $this->objEdtISO2->setClass('fullwidthtag');                 
        $this->objEdtISO2->setRequired(true); 
        $this->objEdtISO2->setMaxLength(2);    
        $objValidator = new TMaximumLength(2);
        $this->objEdtISO2->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEdtISO2->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtISO2, '', transm($this->getModule(), 'form_field_iso2', 'alpha iso2 code')); 

            //ISO3
        $this->objEdtISO3 = new InputText();
        $this->objEdtISO3->setNameAndID('edtISO3');
        $this->objEdtISO3->setClass('fullwidthtag');                 
        $this->objEdtISO3->setRequired(true); 
        $this->objEdtISO3->setMaxLength(3);    
        $objValidator = new TMaximumLength(3);
        $this->objEdtISO3->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEdtISO3->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtISO3, '', transm($this->getModule(), 'form_field_iso3', 'alpha iso3 code')); 
    

            //is in european union
        $this->objChkInEU = new InputCheckbox();
        $this->objChkInEU->setNameAndID('chkEU');
        $this->getFormGenerator()->add($this->objChkInEU, '', transm($this->getModule(), 'form_field_isineea', 'in European Economic Area (thus enjoying the single market)'));           
        
            //is default
        $this->objChkDefault = new InputCheckbox();
        $this->objChkDefault->setNameAndID('chkDefault');
        $this->getFormGenerator()->add($this->objChkDefault, '', transm($this->getModule(), 'form_field_issystemdefault', 'Is system default'));           

            //is favorite
        $this->objChkFavorite = new InputCheckbox();
        $this->objChkFavorite->setNameAndID('chkFavorite');
        $this->getFormGenerator()->add($this->objChkFavorite, '', transm($this->getModule(), 'form_field_isfavorite', 'Is favorite (in GUI elements sometimes only favorites are shown)'));           

            //is unknown
        $this->objChkUnknown = new InputCheckbox();
        $this->objChkUnknown->setNameAndID('chkUnknown');
        $this->getFormGenerator()->add($this->objChkUnknown, '', transm($this->getModule(), 'form_field_isunknowncountry', 'Is unknown country (this record represents when no country is known or found)'));           
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Localisation::PERM_CAT_COUNTRIES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysCountries::FIELD_COUNTRYNAME, $this->objEdtCountry->getValueSubmitted());
        $this->getModel()->set(TSysCountries::FIELD_ISO2, $this->objEdtISO2->getValueSubmitted());
        $this->getModel()->set(TSysCountries::FIELD_ISO3, $this->objEdtISO3->getValueSubmitted());
        $this->getModel()->set(TSysCountries::FIELD_ISEEA, $this->objChkInEU->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysCountries::FIELD_ISDEFAULT, $this->objChkDefault->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysCountries::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysCountries::FIELD_ISUNKNOWN, $this->objChkUnknown->getValueSubmittedAsBool());                
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {         
        $this->objEdtCountry->setValue($this->getModel()->get(TSysCountries::FIELD_COUNTRYNAME));
        $this->objEdtISO2->setValue($this->getModel()->get(TSysCountries::FIELD_ISO2));
        $this->objEdtISO3->setValue($this->getModel()->get(TSysCountries::FIELD_ISO3));
        $this->objChkInEU->setChecked($this->getModel()->get(TSysCountries::FIELD_ISEEA));
        $this->objChkDefault->setChecked($this->getModel()->get(TSysCountries::FIELD_ISDEFAULT));
        $this->objChkFavorite->setChecked($this->getModel()->get(TSysCountries::FIELD_ISFAVORITE));
        $this->objChkUnknown->setChecked($this->getModel()->get(TSysCountries::FIELD_ISUNKNOWN));
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
        return true;
    }    



   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TSysCountries(); 
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
        return 'list_countries';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_countries_new', 'Create new country');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_countries_edit', 'Edit country: [country]', 'country', $this->getModel()->getCountryName());           
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
