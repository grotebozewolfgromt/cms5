<?php


namespace dr\modules\Mod_POSWebshop\controllers;

use dr\classes\models\TSysModel;
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
use dr\classes\dom\tag\webcomponents\DRInputCheckbox;
use dr\classes\dom\tag\webcomponents\DRInputNumber;
use dr\classes\dom\tag\webcomponents\DRInputText;
use dr\classes\dom\validator\ColorHex;
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
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

includeJSWebcomponent(); //dependency of <dr-tabsheets>
includeJSWebcomponent('dr-popover'); //dependency of <dr-tabsheets>
includeJSWebcomponent('dr-tabsheets');

/**
 * Description of detailssave_products
 *
 * @author drenirie
 */
class detailsave_products extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtName = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtNameShort = null;//dr\classes\dom\tag\webcomponents\DRInputText
    // private $objEdtDescription = null;//dr\classes\dom\tag\form\InputText
    // private $objChkDefault = null; //dr\classes\dom\tag\form\webcomponents\DRInputCheckbox
    
    private $objVATClasses = null; //TVATClasses
    private $objCountries = null; //TSysCountries

    public function initModel()
    {
        $this->objCountries->select(array(TSysModel::FIELD_ID));
        $this->objCountries->loadFromDBByIsUnknown();
        $this->objModel->setManufacturerCountryID($this->objCountries->getID());

        $this->objVATClasses->select(array(TSysModel::FIELD_ID));
        $this->objVATClasses->loadFromDBByIsDefault();
        $this->objModel->setVATClassesID($this->objVATClasses->getID());
    }


    /**
     * render shissle to screen
     *
     * @param $arrVars extra variables to add to the render (you can call this method in one of the child classes)
     * @return void
     */
    public function render($arrVars = array())
    {
        $arrVars['objEdtName'] = $this->objEdtName;
        $arrVars['objEdtNameShort'] = $this->objEdtNameShort;

        parent::render($arrVars);
    }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        //product name
        $this->objEdtName = new DRInputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');   
        $this->objEdtName->setValue('testvalue1');  
        // $this->objEdtName->setOnchange("validateField(this, true)");
        // $this->objEdtName->setOnkeyup("setDirtyRecord()");        
        $this->objEdtName->setPlaceholder('full product name');   
        $this->objEdtName->setShowCharCounter(false);        
        $this->objEdtName->setRequired(true);  
        $this->objEdtName->setMinLength(5);         
        $this->objEdtName->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtName->addValidator($objValidator);    

        //product name short
        $this->objEdtNameShort = new DRInputText();
        $this->objEdtNameShort->setNameAndID('edtNameShort');
        $this->objEdtNameShort->setClass('fullwidthtag');   
        $this->objEdtNameShort->setRequired(true); 
        $this->objEdtNameShort->setValue('testvalue2');  
        // $this->objEdtNameShort->setOnchange("validateField(this, true)");
        // $this->objEdtNameShort->setOnkeyup("setDirtyRecord()");             
        $this->objEdtNameShort->setPlaceholder('short product name');   
        $this->objEdtNameShort->setShowCharCounter(true);
        $this->objEdtNameShort->setMinLength(5);
        $this->objEdtNameShort->setMaxLength(20);
        $objValidator = new TMaximumLength(20);
        $this->objEdtNameShort->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtNameShort->addValidator($objValidator);    
    
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_POSWebshop::PERM_CAT_PRODUCTS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        // //name
        // $this->getModel()->set(TVATClasses::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        // //description
        // $this->getModel()->set(TVATClasses::FIELD_DESCRIPTION, $this->objEdtDescription->getValueSubmitted());         
               
        // //default
        // $this->getModel()->set(TVATClasses::FIELD_ISDEFAULT, $this->objChkDefault->getCheckedSubmitted());         

    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        // //name
        // $this->objEdtName->setValue($this->getModel()->get(TVATClasses::FIELD_NAME));

        // //description
        // $this->objEdtDescription->setValue($this->getModel()->get(TVATClasses::FIELD_DESCRIPTION));

        // //default
        // $this->objChkDefault->setChecked($this->getModel()->get(TVATClasses::FIELD_ISDEFAULT));

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
    public function onSavePost($bWasSaveSuccesful)
    { 

    }
    

    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        $this->objCountries = new TSysCountries();
        $this->objVATClasses = new TVATClasses();
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
        return new TProducts(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_MODULES.DIRECTORY_SEPARATOR.APP_ADMIN_CURRENTMODULE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_detailsave_products.php';
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
        return 'list_products';
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
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_products_new_title', 'Create new product');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_products_edit_title', 'Edit product');   
            // return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_products_edit_title', 'Edit VAT class: [name]', 'name', $this->getModel()->getName());   
    }


    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return 'products';
    }    

   /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    public function getAuthCreate()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTS, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTS, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTS, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_PRODUCTS, TModuleAbstract::PERM_OP_DELETE);
    }



}
