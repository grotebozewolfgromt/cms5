<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_CMSUsers\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputPassword;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\dom\tag\form\InputTime;
use dr\classes\dom\tag\form\Label;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
use dr\classes\types\TDateTime;


//don't forget ;)
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
use  dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of detailsave_permissionscountries
 *
 * whitelist of permitted countries to use the system
 * 
 * @author drenirie
 */
class detailsave_permissionscountries extends TCRUDDetailSaveController
{
    private $objOptCountry = null;//dr\classes\dom\tag\form\Select     
    
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        //country
        $this->objOptCountry = new Select();
        $this->objOptCountry->setNameAndID('optCountry');
        $this->getFormGenerator()->add($this->objOptCountry, '', transm($this->getModule(), 'form_field_country', 'country'));        
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_CMSUsers::PERM_CAT_PERMISSIONSCOUNTRIES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //country
        $this->getModel()->set(TSysCMSPermissionsCountries::FIELD_COUNTRYID, $this->objOptCountry->getValueSubmittedAsInt());
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //country
        $objCountries = new \dr\modules\Mod_Sys_Localisation\models\TSysCountries();
        $objCountries->sort(\dr\modules\Mod_Sys_Localisation\models\TSysCountries::FIELD_COUNTRYNAME);
        $objCountries->loadFromDB();
        $objCountries->generateHTMLSelect($this->getModel()->get(TSysCMSPermissionsCountries::FIELD_COUNTRYID), $this->objOptCountry);
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
        //if username not unique
        if ($this->getModel()->doesCountryExistDB($this->objOptCountry->getValueSubmittedAsInt()))
        {
            sendMessageError(transm($this->getModule(), 'message_countryalreadyexists', 'Country NOT SAVED, country already exists'));
            return false;
        }
        
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
        return new TSysCMSPermissionsCountries(); 
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
        return 'list_permissionscountries';
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
        //global APP_ADMIN_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_permissionscountries_new', 'Add new country');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_permissionscountries_edit', 'Change country');   
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
