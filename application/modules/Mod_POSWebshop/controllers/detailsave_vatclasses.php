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
use dr\modules\Mod_POSWebshop\Mod_POSWebshop;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;
use dr\modules\Mod_Sys_CMSUsers\Mod_Sys_CMSUsers;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_vatclasses extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objEdtDescription = null;//dr\classes\dom\tag\form\InputText
    private $objChkDefault = null; //dr\classes\dom\tag\form\webcomponents\DRInputCheckbox
    
    private $objVATCountries = null; //TVATClassesCountries --> countries that are associated yet
    private $objCountries = null; //TSysCountries

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
        // $this->objEdtName->setOnchange("validateField(this, true)");
        // $this->objEdtName->setOnkeyup("setDirtyRecord()");        
        $this->objEdtName->setClass('fullwidthtag');   
        $this->objEdtName->setRequired(true);   
        $this->objEdtName->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtName, '', transm(CMS_CURRENTMODULE, 'form_field_name', 'Name'));

            //description
        $this->objEdtDescription = new InputText();
        $this->objEdtDescription->setNameAndID('edtDescription');        
        // $this->objEdtDescription->setOnchange("validateField(this, true)");
        // $this->objEdtDescription->setOnkeyup("setDirtyRecord()");        
        $this->objEdtDescription->setClass('fullwidthtag');           
        $this->objEdtDescription->setRequired(false);   
        $this->objEdtDescription->setMaxLength(255);
        $objValidator = new TMaximumLength(255);
        $this->objEdtDescription->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtDescription->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtDescription, '', transm(CMS_CURRENTMODULE, 'form_field_description', 'Description'));  

            //default
        $this->objChkDefault = new DRInputCheckbox();
        $this->objChkDefault->setNameAndID('chkDefault');
        // $this->objChkDefault->setOnchange("validateField(this, true)");
        // $this->objChkDefault->setOnkeyup("setDirtyRecord()");        
        $this->objChkDefault->setLabel(transm(CMS_CURRENTMODULE, 'form_field_default', 'Is default'));
        $this->getFormGenerator()->add($this->objChkDefault);  
    
    }

    private function countryToVariableName($iCountryID, $sField)
    {
        return sanitizeHTMLTagAttribute('country_'.$sField.'_'.$iCountryID);
        
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_POSWebshop::PERM_CAT_VATCLASSES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getModel()->set(TVATClasses::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        //description
        $this->getModel()->set(TVATClasses::FIELD_DESCRIPTION, $this->objEdtDescription->getValueSubmitted());         
               
        //default
        $this->getModel()->set(TVATClasses::FIELD_ISDEFAULT, $this->objChkDefault->getCheckedSubmitted());         
        
        //the rest with the countries is done in onSavePost() because we need an ID
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getModel()->get(TVATClasses::FIELD_NAME));

        //description
        $this->objEdtDescription->setValue($this->getModel()->get(TVATClasses::FIELD_DESCRIPTION));

        //default
        $this->objChkDefault->setChecked($this->getModel()->get(TVATClasses::FIELD_ISDEFAULT));

        //vat countries
        $objVATCtr = $this->objVATCountries;
        $objVATCtr->resetRecordPointer();
        while($objVATCtr->next())
        {
            $objInput = new DRInputNumber();
            $objInput->setNameAndID($this->countryToVariableName($objVATCtr->getID(), 'vatpercent'));//we can't have spaces in variable names
            $objInput->setPrecision(4);
            $objInput->setPadZero(2);
            $objInput->setValueAsTDecimal($objVATCtr->getVATPercent());
            $this->getFormGenerator()->add($objInput, $objVATCtr->get(TSysCountries::FIELD_COUNTRYNAME, TSysCountries::getTable()), transm(CMS_CURRENTMODULE, 'form_field_vatpercent', 'VAT percentage'));

            if ($objVATCtr->get(TSysCountries::FIELD_ISEEA, TSysCountries::getTable()))
            {
                $objInput = new DRInputNumber();
                $objInput->setNameAndID($this->countryToVariableName($objVATCtr->getID(), 'vatpercentintraeeu'));//we can't have spaces in variable names
                $objInput->setPrecision(4);
                $objInput->setPadZero(2);
                $objInput->setValueAsTDecimal($objVATCtr->getVATPercentIntraEEA());
                $this->getFormGenerator()->add($objInput, $objVATCtr->get(TSysCountries::FIELD_COUNTRYNAME, TSysCountries::getTable()), transm(CMS_CURRENTMODULE, 'form_field_vatpercentintraeea', 'VAT percentage inside European Economic Area (EEA)'));
            }
        }              
                 

    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoadPost()
    {
        //make sure that all countries are present
        $this->objVATCountries->createMissingCountries($this->objModel->getID());

        //load all VAT countries as well
        $this->objVATCountries->newQuery();
        $this->objVATCountries->limitNone();//no limit
        $this->objVATCountries->select(array(
            TVATClassesCountries::FIELD_ID, 
            TVATClassesCountries::FIELD_COUNTRYID,
            TVATClassesCountries::FIELD_VATPERCENT,
            TVATClassesCountries::FIELD_VATPERCENTINTRAEEA
                                    ));
        $this->objVATCountries->select(array(
            TSysCountries::FIELD_COUNTRYNAME,
            TSysCountries::FIELD_ISEEA
        ), $this->objCountries);        
        $this->objVATCountries->find(TVATClassesCountries::FIELD_VATCLASSESID, $this->objModel->getID());
        $this->objVATCountries->orderBy(TSysCountries::FIELD_COUNTRYNAME, SORT_ORDER_ASCENDING, TSysCountries::getTable());
        $this->objVATCountries->loadFromDB(true);
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
        
        //vat countries
        $objVATCtr = new TVATClassesCountries();//speed things up
        $objVATCtr->limitNone();
        $objVATCtr->find(TVATClassesCountries::FIELD_VATCLASSESID, $this->objModel->getID());
        $objVATCtr->loadFromDB(true);
        $objVATCtr->resetRecordPointer();
        while($objVATCtr->next())
        {
            $objInput = new DRInputNumber();
            $objInput->setNameAndID($this->countryToVariableName($objVATCtr->getID(), 'vatpercent'));//we can't have spaces in variable names

            $objVATCtr->setVATPercent($objInput->getValueSubmittedAsTDecimal(Form::METHOD_POST, 4));

            if ($objVATCtr->get(TSysCountries::FIELD_ISEEA, TSysCountries::getTable()))
            {
                $objInput = new DRInputNumber();
                $objInput->setNameAndID($this->countryToVariableName($objVATCtr->getID(), 'vatpercentintraeeu'));//we can't have spaces in variable names
                $objVATCtr->setVATPercentIntraEEA($objInput->getValueSubmittedAsTDecimal(Form::METHOD_POST, 4));
            }
        }    
        $objVATCtr->saveToDBAll();         

        return array(); 
    }
    

    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        $this->objVATCountries = new TVATClassesCountries();
        $this->objCountries = new TSysCountries();
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
        return new TVATClasses(); 
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
        return 'list_vatclasses';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_vatclasses_new', 'Create new VAT type class');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_vatclasses_edit', 'Edit VAT class: [name]', 'name', $this->getModel()->getName());   
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
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_VATCLASSES, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_VATCLASSES, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_VATCLASSES, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(CMS_CURRENTMODULE, Mod_POSWebshop::PERM_CAT_VATCLASSES, TModuleAbstract::PERM_OP_DELETE);
    }



}
