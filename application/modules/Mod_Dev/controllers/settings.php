<?php


namespace dr\modules\Mod_Dev\controllers;

use dr\classes\controllers\TCRUDConfigFileController;
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
use dr\classes\dom\validator\Time;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsStatusses;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\classes\types\TDateTime;


//don't forget ;)

use dr\modules\Mod_PageBuilder\Mod_PageBuilder;
use dr\modules\Mod_PageBuilder\models\TConfigFilePagebuilder;
use dr\modules\Mod_Dev\Mod_Dev;
use dr\modules\Mod_Dev\models\TConfigFileDev;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_cms_auth.php');

/**
 * Description of TCRUDDetailSaveCMSUsers
 *
 * @author drenirie
 */
class settings extends TCRUDConfigFileController
{
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText
    private $objChkDefault = null;//dr\classes\dom\tag\form\InputCheck
    
    const FIELD_NAME = 'DEV_NAME';
    const FIELD_ISDEFAULT = 'DEV_ISDEFAULT';

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
        $objValidator = new Maximumlength(50);
        $this->objEdtName->addValidator($objValidator);    
        $objValidator = new Required();
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
        return Mod_Dev::PERM_CAT_SETTINGS;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getConfigFile()->set(settings::FIELD_NAME, $this->objEdtName->getValueSubmitted());

        //default
        $this->getConfigFile()->set(settings::FIELD_ISDEFAULT, $this->objChkDefault->getValueSubmittedAsBool());
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getConfigFile()->get(settings::FIELD_NAME));

        //default
        $this->objChkDefault->setChecked($this->getConfigFile()->getAsBool(settings::FIELD_ISDEFAULT));
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

        return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_settings', 'Settings dev mod');   
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


    /**
     * returns a new config file object
     *
     * @return TPHPConstantsConfigFile
     */
    public function getNewConfigFile()
    {
        return new TConfigFileDev();
    }

}
