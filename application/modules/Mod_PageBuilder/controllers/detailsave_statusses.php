<?php


namespace dr\modules\Mod_PageBuilder\controllers;

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
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;
use dr\classes\dom\validator\TEmailAddress;
use dr\classes\dom\validator\Date;
use dr\classes\dom\validator\DateMin;
use dr\classes\dom\validator\DateMax;
use dr\classes\dom\validator\DateTime;
use dr\classes\dom\validator\Time;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)

use dr\modules\Mod_PageBuilder\Mod_PageBuilder;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class detailsave_statusses extends TCRUDDetailSaveController
{
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objChkDefault = null;//dr\classes\dom\tag\form\InputCheck
    
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
                 
            //name
        $this->objEdtName = new InputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');   
        $this->objEdtName->setRequired(true);   
        $this->objEdtName->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtName->addValidator($objValidator);    
        $this->getFormGenerator()->add($this->objEdtName, '', transm($this->getModule(), 'form_field_name', 'Name'));
           
            //is default
        $this->objChkDefault = new InputCheckbox();
        $this->objChkDefault->setNameAndID('chkIsDefault');
        $this->getFormGenerator()->add($this->objChkDefault, '', transm($this->getModule(), 'form_field_isdefault', 'Is default'));   
    
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_PageBuilder::PERM_CAT_STATUSSES;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getModel()->set(TPageBuilderDocumentsStatusses::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        //default
        $this->getModel()->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, $this->objChkDefault->getValueSubmittedAsBool());
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getModel()->get(TPageBuilderDocumentsStatusses::FIELD_NAME));

        //default
        $this->objChkDefault->setChecked($this->getModel()->get(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT));
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
        return new TPageBuilderDocumentsStatusses(); 
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
        return 'list_statusses';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_statusses_new', 'Create new status');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_statusses_edit', 'Edit status: [name]', 'name', $this->getModel()->getName());   
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
