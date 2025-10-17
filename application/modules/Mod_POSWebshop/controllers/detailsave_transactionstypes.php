<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\controllers\TCRUDDetailSaveController_org;
use dr\classes\controllers\TCRUDDetailSaveControllerAJAX;
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
use dr\classes\dom\validator\TColorHex;
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
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TTransactionsTypes;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_transactionstypes extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDescription = null;//dr\classes\dom\tag\form\InputText
    private $objChkAvailStockAdd = null;//dr\classes\dom\tag\form\InputCheckbox     
    private $objChkAvailStockSubtract = null;//dr\classes\dom\tag\form\InputCheckbox     
    private $objChkReservedStockAdd = null;//dr\classes\dom\tag\form\InputCheckbox     
    private $objChkReservedStockSubtract = null;//dr\classes\dom\tag\form\InputCheckbox     
    private $objChkFinancialAdd = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkFinancialSubtract = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkDefaultSelected = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkFavorite = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkDefaultInvoice = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkDefaultOrder = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objEdtColorForeground = null;//dr\classes\dom\tag\form\InputColor
    private $objEdtColorBackground = null;//dr\classes\dom\tag\form\InputColor
    private $objEdtNewNumber = null;//dr\classes\dom\tag\form\InputNumber
    private $objEdtPaymentDays = null;//dr\classes\dom\tag\form\InputNumber
        

    public function initModel()
    {

    }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
                 
            //custom identifier
        $this->objEdtName = new InputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');   
        // $this->objEdtName->setOnchange("validateField(this, true)");
        // $this->objEdtName->setOnkeyup("setDirtyRecord()");        
        $this->objEdtName->setRequired(true);   
        $this->objEdtName->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
        $this->objEdtName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtName, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_name', 'Name'));

        //description
        $this->objEdtDescription = new InputText();
        $this->objEdtDescription->setNameAndID('edtDescription');
        $this->objEdtDescription->setClass('fullwidthtag');   
        // $this->objEdtDescription->setOnchange("validateField(this, true)");
        // $this->objEdtDescription->setOnkeyup("setDirtyRecord()");        
        $this->objEdtDescription->setRequired(false);   
        $this->objEdtDescription->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtDescription->addValidator($objValidator);    
        $objValidator = new TRequired(transcms('form_error_requiredfield', 'This is a required field'));
        $this->objEdtDescription->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtDescription, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_description', 'Description'));

 
        //CURRENT stock +
        $this->objChkAvailStockAdd = new InputCheckbox();
        $this->objChkAvailStockAdd->setNameAndID('chkAvailStockAdd');
        // $this->objChkAvailStockAdd->setOnchange("setDirtyRecord()");
        // $this->objChkAvailStockAdd->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkAvailStockAdd, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_availablestockadd', 'Add AVAILABLE stock (available stock reduced or increased when transaction completed)'));        

        //CURRENT stock -
        $this->objChkAvailStockSubtract = new InputCheckbox();
        $this->objChkAvailStockSubtract->setNameAndID('chkAvailStockSubtract');
        // $this->objChkAvailStockSubtract->setOnchange("setDirtyRecord()");
        // $this->objChkAvailStockSubtract->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkAvailStockSubtract, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_availablestocksubtract', 'Subtract AVAILABLE stock (available stock reduced or increased when transaction completed)'));        

        //RESERVED stock +
        $this->objChkReservedStockAdd = new InputCheckbox();
        $this->objChkReservedStockAdd->setNameAndID('chkReservedStockAdd');
        // $this->objChkReservedStockAdd->setOnchange("setDirtyRecord()");
        // $this->objChkReservedStockAdd->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkReservedStockAdd, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_reservedstockadd', 'Add RESERVED stock (reserved stock reduced or increased when transaction completed)'));         
        
        //RESERVED stock -
        $this->objChkReservedStockSubtract = new InputCheckbox();
        $this->objChkReservedStockSubtract->setNameAndID('chkReservedStockSubtract');
        // $this->objChkReservedStockSubtract->setOnchange("setDirtyRecord()");
        // $this->objChkReservedStockSubtract->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkReservedStockSubtract, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_reservedstocksubtract', 'Subtract RESERVED stock (reserved stock reduced or increased when transaction completed)'));         

        //FINANCIAL transaction +
        $this->objChkFinancialAdd = new InputCheckbox();
        $this->objChkFinancialAdd->setNameAndID('chkFinancialAdd');
        // $this->objChkFinancialAdd->setOnchange("setDirtyRecord()");
        // $this->objChkFinancialAdd->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkFinancialAdd, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_financialadd', 'Add money'));         

        //FINANCIAL transaction -
        $this->objChkFinancialSubtract = new InputCheckbox();
        $this->objChkFinancialSubtract->setNameAndID('chkFinancialSubtract');
        // $this->objChkFinancialSubtract->setOnchange("setDirtyRecord()");
        // $this->objChkFinancialSubtract->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkFinancialSubtract, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_financialsubtract', 'Subtracts money'));         


        //default selected
        $this->objChkDefaultSelected = new InputCheckbox();
        $this->objChkDefaultSelected->setNameAndID('chkIsDefaultSelected');
        // $this->objChkDefaultSelected->setOnchange("setDirtyRecord()");
        // $this->objChkDefaultSelected->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkDefaultSelected, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_isdefaultselected', 'Is selected by default (in user interface elements)'));

        //is favorite
        $this->objChkFavorite = new InputCheckbox();
        $this->objChkFavorite->setNameAndID('chkIsFavorite');
        // $this->objChkFavorite->setOnchange("setDirtyRecord()");
        // $this->objChkFavorite->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkFavorite, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_isfavorite', 'Is favorite (in some GUI elements only favorites are shown)'));         


        //default invoice
        $this->objChkDefaultInvoice = new InputCheckbox();
        $this->objChkDefaultInvoice->setNameAndID('chkIsDefaultInvoice');
        // $this->objChkDefaultInvoice->setOnchange("setDirtyRecord()");
        // $this->objChkDefaultInvoice->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkDefaultInvoice, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_isdefaultinvoice', 'Is default invoice (i.e. when an invoice in webshop is needed, this type is used)'));         


        //default order
        $this->objChkDefaultOrder = new InputCheckbox();
        $this->objChkDefaultOrder->setNameAndID('chkIsDefaultOrder');
        // $this->objChkDefaultOrder->setOnchange("setDirtyRecord()");
        // $this->objChkDefaultOrder->setOnkeyup("setDirtyRecord()");        
        $this->getFormGenerator()->add($this->objChkDefaultOrder, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_isdefaultorder', 'Is default order (i.e. when an order in webshop is needed, this type is used)'));         
        
        
            //foreground color
        $this->objEdtColorForeground = new InputColor();
        $this->objEdtColorForeground->setNameAndID('edtColorForeground');
        // $this->objEdtColorForeground->setOnchange("setDirtyRecord(); validateField(this, true);");
        // $this->objEdtColorForeground->setOnkeyup("setDirtyRecord()");          
        // $this->objEdtColorForeground->setClass('fullwidthtag');   
        $this->objEdtColorForeground->setRequired(false);   
        $this->objEdtColorForeground->setMaxLength(8);
        $objValidator = new TMaximumLength(8);
        $this->objEdtColorForeground->addValidator($objValidator);    
        $objValidator = new TColorHex(true, true);
        $this->objEdtColorForeground->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtColorForeground, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_colorforeground', 'Foreground color (hexadecimal value from #00000 to #FFFFFF) (without #-sign)'));
    
            //background color
        $this->objEdtColorBackground = new InputColor();
        $this->objEdtColorBackground->setNameAndID('edtColorBackground');
        // $this->objEdtColorBackground->setOnchange("setDirtyRecord(); validateField(this, true);");
        // $this->objEdtColorBackground->setOnkeyup("setDirtyRecord()");          
        // $this->objEdtColorBackground->setClass('fullwidthtag');   
        $this->objEdtColorBackground->setRequired(false);   
        $this->objEdtColorBackground->setMaxLength(8);
        $objValidator = new TMaximumLength(8);
        $this->objEdtColorBackground->addValidator($objValidator);    
        $objValidator = new TColorHex(true, true);
        $this->objEdtColorBackground->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtColorBackground, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_colorbackground', 'Background color (hexadecimal value from #00000 to #FFFFFF) (without #-sign)'));
            

            //new incremented number
        $this->objEdtNewNumber = new InputNumber();
        $this->objEdtNewNumber->setNameAndID('edtNewIncrementNumber');
        // $this->objEdtNewNumber->setOnchange("validateField(this, true)");
        // $this->objEdtNewNumber->setOnkeyup("setDirtyRecord()");          
        // $this->objEdtLastNumber->setClass('fullwidthtag');   
        $this->objEdtNewNumber->setRequired(true);   
        $this->objEdtNewNumber->setMaxLength(10);
        $objValidator = new TMaximumLength(10);
        $this->objEdtNewNumber->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtNewNumber->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtNewNumber, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_newincrementednumber', 'New transaction starts at number'));

            //payment within days
        $this->objEdtPaymentDays = new InputNumber();
        $this->objEdtPaymentDays->setNameAndID('edtPaymentDays');
        // $this->objEdtPaymentDays->setOnchange("validateField(this, true)");
        // $this->objEdtPaymentDays->setOnkeyup("setDirtyRecord()");           
        // $this->objEdtPaymentDays->setClass('fullwidthtag');   
        // $this->objEdtPaymentDays->setRequired(true);   
        $this->objEdtPaymentDays->setMaxLength(4);
        $objValidator = new TMaximumLength(4);
        $this->objEdtPaymentDays->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtPaymentDays->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtPaymentDays, '', transm(APP_ADMIN_CURRENTMODULE, 'form_field_paymentwithindays', 'Must be payed within (amount of days)'));

    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getModel()->set(TTransactionsTypes::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        //description
        $this->getModel()->set(TTransactionsTypes::FIELD_DESCRIPTION, $this->objEdtDescription->getValueSubmitted());

        //current stock +
        $this->getModel()->set(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, $this->objChkAvailStockAdd->getValueSubmittedAsBool());        

        //current stock -
        $this->getModel()->set(TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, $this->objChkAvailStockSubtract->getValueSubmittedAsBool());        

        //reserved stock + 
        $this->getModel()->set(TTransactionsTypes::FIELD_RESERVEDSTOCKADD, $this->objChkReservedStockAdd->getValueSubmittedAsBool());        

        //reserved stock -
        $this->getModel()->set(TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, $this->objChkReservedStockSubtract->getValueSubmittedAsBool());        

        //financial +
        $this->getModel()->set(TTransactionsTypes::FIELD_FINANCIALADD, $this->objChkFinancialAdd->getValueSubmittedAsBool());        

        //financial -
        $this->getModel()->set(TTransactionsTypes::FIELD_FINANCIALSUBTRACT, $this->objChkFinancialSubtract->getValueSubmittedAsBool());        

        //default selected
        $this->getModel()->set(TTransactionsTypes::FIELD_ISDEFAULT, $this->objChkDefaultSelected->getValueSubmittedAsBool());        

        //favorite
        $this->getModel()->set(TTransactionsTypes::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());        

        //default invoice type
        $this->getModel()->set(TTransactionsTypes::FIELD_ISDEFAULTINVOICE, $this->objChkDefaultInvoice->getValueSubmittedAsBool());        

        //default order type
        $this->getModel()->set(TTransactionsTypes::FIELD_ISDEFAULTORDER, $this->objChkDefaultOrder->getValueSubmittedAsBool());        

        //foreground color
        $this->getModel()->set(TTransactionsTypes::FIELD_COLORFOREGROUND, substr($this->objEdtColorForeground->getValueSubmitted(), 1)); //without #-sign

        //background color
        $this->getModel()->set(TTransactionsTypes::FIELD_COLORBACKGROUND, substr($this->objEdtColorBackground->getValueSubmitted(), 1)); //without #-sign

        //new number increment
        $this->getModel()->set(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, $this->objEdtNewNumber->getValueSubmitted());

        //payment within days
        $this->getModel()->set(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, $this->objEdtPaymentDays->getValueSubmitted());

    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getModel()->get(TTransactionsTypes::FIELD_NAME));

        //description
        $this->objEdtDescription->setValue($this->getModel()->get(TTransactionsTypes::FIELD_DESCRIPTION));

        //current stock +
        $this->objChkAvailStockAdd->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_AVAILABLESTOCKADD));   

        //current stock -
        $this->objChkAvailStockSubtract->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT));   

        //reserved stock +
        $this->objChkReservedStockAdd->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_RESERVEDSTOCKADD));   

        //reserved stock -
        $this->objChkReservedStockSubtract->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT));   

        //financial +
        $this->objChkFinancialAdd->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_FINANCIALADD));   

        //financial -
        $this->objChkFinancialSubtract->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_FINANCIALSUBTRACT));   

        //default selected
        $this->objChkDefaultSelected->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_ISDEFAULT));   

        //favorite
        $this->objChkFavorite->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_ISFAVORITE));   

        //default invoice
        $this->objChkDefaultInvoice->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_ISDEFAULTINVOICE));   

        //default order
        $this->objChkDefaultOrder->setChecked($this->getModel()->get(TTransactionsTypes::FIELD_ISDEFAULTORDER));   

        //foreground color
        $this->objEdtColorForeground->setValue('#'.$this->getModel()->get(TTransactionsTypes::FIELD_COLORFOREGROUND)); 

        //background color
        $this->objEdtColorBackground->setValue('#'.$this->getModel()->get(TTransactionsTypes::FIELD_COLORBACKGROUND));

        //new number increment
        $this->objEdtNewNumber->setValue($this->getModel()->get(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT));

        //payment within days
        $this->objEdtPaymentDays->setValue($this->getModel()->get(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS));

    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoadPost()
    {
               
    }
    
   /**
     * is called when a record is loaded
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
        return false;
    }    



   /**
     * returns a new model object
     *
     * @return TSysModel
     */
    public function getNewModel()
    {
        return new TTransactionsTypes(); 
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
        return 'list_transactionstypes';
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
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_transactionstypes_new', 'Create transaction type');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_transactionstypes_edit', 'Edit transaction type: [name]', 'name', $this->getModel()->getName());   
    }

    /**
     * show tabsheets on top of the page?
     *
     * @return bool
     */
    // public function showTabs()
    // {
    //     return false;
    // }    


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
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_TRANSACTIONSTYPES, TModuleAbstract::PERM_OP_DELETE);
    }

    /**
     * returns a new model object or null
     * 
     * When getNewModelTranslation() != null the parent CRUD controller will do the following:
     * 1. instantiates $objLanguagesTranslations
     * 2. fills $objModelTranslation
     * 3. shows a combobox with translations on top of the page $objCbxLanguagesTranslations
     * 4. loads list of favorited languages from database in $objLanguagesTranslations
     * 5. loads translation record from exteral table for current record
     * 6. saves translation as well when user hits 'save'
     * 
     * @return TSysModel object or null when using no translations
     */
    public function getNewModelTranslation()
    {
        return null;
    }     

}
