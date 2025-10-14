<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Localisation\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\form\InputNumber;
use dr\classes\dom\tag\webcomponents\DRInputNumber;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TOnlyAlphabetical;
use dr\classes\dom\validator\TOnlyNumeric;
use dr\classes\dom\validator\TRequired;

//don't forget ;)
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_Sys_Localisation\models\TSysCurrencies;
use dr\modules\Mod_Sys_Localisation\Mod_Sys_Localisation;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_currencies extends TCRUDDetailSaveController
{   
    private $objEdtCurrencyName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtCurrencySymbol = null;//dr\classes\dom\tag\form\InputText
    private $objEdtISOAlphabetic = null;//dr\classes\dom\tag\form\InputText
    private $objEdtISONumeric = null;//dr\classes\dom\tag\form\InputNumber
    private $objEdtDecimalPrecision = null;//dr\classes\dom\tag\form\InputNumber
    private $objChkIsSystemDefault = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkFavorite = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objEdtExchangeRate = null;//dr\classes\dom\tag\form\webcomponents\DRInputNumber

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //currency
        $this->objEdtCurrencyName = new InputText();
        $this->objEdtCurrencyName->setNameAndID('edtCurrencyName');
        $this->objEdtCurrencyName->setClass('fullwidthtag');         
        $this->objEdtCurrencyName->setRequired(true);   
        $this->objEdtCurrencyName->setMaxLength(100);                
        $objValidator = new TMaximumLength(100);
        $this->objEdtCurrencyName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtCurrencyName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtCurrencyName, '', transm($this->getModule(), 'currenciesdetail_form_field_currencyname', 'Name'));

            //currency symbol
        $this->objEdtCurrencySymbol = new InputText();
        $this->objEdtCurrencySymbol->setNameAndID('edtCurrencySymbol');
        $this->objEdtCurrencySymbol->setClass('fullwidthtag');         
        $this->objEdtCurrencySymbol->setRequired(true);   
        // $this->objEdtCurrencySymbol->setMaxLength(3);                
        $objValidator = new TMaximumLength(3);
        $this->objEdtCurrencySymbol->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtCurrencySymbol->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtCurrencySymbol, '', transm($this->getModule(), 'currenciesdetail_form_field_currencysymbol', 'Symbol'));
    

            //ISO code alphabetic
        $this->objEdtISOAlphabetic = new InputText();
        $this->objEdtISOAlphabetic->setNameAndID('edtISOAlphabetic');
        // $this->objEdtISOAlphabetic->setClass('fullwidthtag');                 
        $this->objEdtISOAlphabetic->setRequired(true); 
        $this->objEdtISOAlphabetic->setMaxLength(3);    
        $objValidator = new TMaximumLength(3);
        $this->objEdtISOAlphabetic->addValidator($objValidator);  
        $objValidator = new TOnlyAlphabetical();
        $this->objEdtISOAlphabetic->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEdtISOAlphabetic->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtISOAlphabetic, '', transm($this->getModule(), 'currenciesdetail_form_field_isoalphabetic', 'Alphabetical ISO code')); 

            //ISO numeric
        $this->objEdtISONumeric = new InputNumber();
        $this->objEdtISONumeric->setNameAndID('edtISONumeric');
        // $this->objEdtISONumeric->setClass('fullwidthtag');                 
        $this->objEdtISONumeric->setRequired(true); 
        $this->objEdtISONumeric->setMaxLength(3);    
        $objValidator = new TMaximumLength(3);
        $this->objEdtISONumeric->addValidator($objValidator);  
        $objValidator = new TOnlyNumeric();
        $this->objEdtISONumeric->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEdtISONumeric->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtISONumeric, '', transm($this->getModule(), 'currenciesdetail_form_field_isonumeric', 'Numeric ISO code')); 
    
            //minor unit (decimals after decimal separator)
        $this->objEdtDecimalPrecision = new InputNumber();
        $this->objEdtDecimalPrecision->setNameAndID('edtDecimalPrecision');
        // $this->objEdtISONumeric->setClass('fullwidthtag');                 
        $this->objEdtDecimalPrecision->setRequired(true); 
        $this->objEdtDecimalPrecision->setMaxLength(3);    
        $objValidator = new TMaximumLength(3);
        $this->objEdtDecimalPrecision->addValidator($objValidator);  
        $objValidator = new TOnlyNumeric();
        $this->objEdtDecimalPrecision->addValidator($objValidator);  
        $objValidator = new TRequired();
        $this->objEdtDecimalPrecision->addValidator($objValidator);       
        $this->getFormGenerator()->add($this->objEdtDecimalPrecision, '', transm($this->getModule(), 'currenciesdetail_form_field_minorunit', 'Decimals after separator')); 

            //is default
        $this->objChkIsSystemDefault = new InputCheckbox();
        $this->objChkIsSystemDefault->setNameAndID('chkSystemDefault');
        $this->getFormGenerator()->add($this->objChkIsSystemDefault, '', transm($this->getModule(), 'currenciesdetail_form_field_issystemdefault', 'Is system default'));           

            //is favorite
        $this->objChkFavorite = new InputCheckbox();
        $this->objChkFavorite->setNameAndID('chkVisible');
        $this->getFormGenerator()->add($this->objChkFavorite, '', transm($this->getModule(), 'currenciesdetail_form_field_isfavorite', 'Is favorite (favorites are shown in GUI elements where only a selected number of currencies are shown)'));           
                
            //exchange rate
        $this->objEdtExchangeRate = new DRInputNumber();
        $this->objEdtExchangeRate->setNameAndID('edtExchangeRate');
        $this->objEdtExchangeRate->setDecimalPrecision(4);
        $this->objEdtExchangeRate->setPadZero(2);
        $this->getFormGenerator()->add($this->objEdtExchangeRate, '', transm($this->getModule(), 'currenciesdetail_form_field_exchangerate', 'Exchange rate (Exchange rates are calculated from a unit of a store\'s default currency)'));           
                
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Localisation::PERM_CAT_CURRENCIES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        $this->getModel()->set(TSysCurrencies::FIELD_CURRENCYNAME, $this->objEdtCurrencyName->getValueSubmitted());
        $this->getModel()->set(TSysCurrencies::FIELD_CURRENCYSYMBOL, $this->objEdtCurrencySymbol->getValueSubmitted());
        $this->getModel()->set(TSysCurrencies::FIELD_ISOALPHABETIC, $this->objEdtISOAlphabetic->getValueSubmitted());
        $this->getModel()->set(TSysCurrencies::FIELD_ISONUMERIC, $this->objEdtISONumeric->getValueSubmitted());
        $this->getModel()->set(TSysCurrencies::FIELD_DECIMALPRECISION, $this->objEdtDecimalPrecision->getValueSubmitted());
        $this->getModel()->set(TSysCurrencies::FIELD_ISDEFAULT, $this->objChkIsSystemDefault->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysCurrencies::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());                
        $this->getModel()->set(TSysCurrencies::FIELD_EXCHANGERATE, $this->objEdtExchangeRate->getValueSubmittedAsTDecimal(Form::METHOD_POST, 4));                
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {         
        $this->objEdtCurrencyName->setValue($this->getModel()->get(TSysCurrencies::FIELD_CURRENCYNAME));
        $this->objEdtCurrencySymbol->setValue($this->getModel()->get(TSysCurrencies::FIELD_CURRENCYSYMBOL));
        $this->objEdtISOAlphabetic->setValue($this->getModel()->get(TSysCurrencies::FIELD_ISOALPHABETIC));
        $this->objEdtISONumeric->setValue($this->getModel()->get(TSysCurrencies::FIELD_ISONUMERIC));
        $this->objEdtDecimalPrecision->setValue($this->getModel()->get(TSysCurrencies::FIELD_DECIMALPRECISION));
        $this->objChkIsSystemDefault->setChecked($this->getModel()->get(TSysCurrencies::FIELD_ISDEFAULT));
        $this->objChkFavorite->setChecked($this->getModel()->get(TSysCurrencies::FIELD_ISFAVORITE));
        $this->objEdtExchangeRate->setValueAsTDecimal($this->getModel()->get(TSysCurrencies::FIELD_EXCHANGERATE));
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
        return new TSysCurrencies(); 
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
        return 'list_currencies';
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
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_currencies_new', 'Create new currency');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_currencies_edit', 'Edit currency: [currency]', 'currency', $this->getModel()->getCurrencyName());           
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
