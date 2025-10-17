<?php


namespace dr\modules\Mod_Sys_Settings\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveControllerAJAX;
use dr\classes\locale\TLocalisation;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\FormInputAbstract;
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
use dr\classes\dom\tag\webcomponents\DRInputCombobox;
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
use dr\classes\models\TTreeModel;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions;
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Settings\Mod_Sys_Settings;
use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;
use dr\modules\Mod_Sys_Settings\models\TSysSettings;

include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of detailsave_cmsmenu
 *
 * @author drenirie
 */
class detailsave_cmsmenu extends TCRUDDetailSaveControllerAJAX
{
    private $objEdtNameDefault = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtModule = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtController = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtURLCustom = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objEdtSVGIcon = null;//dr\classes\dom\tag\webcomponents\DRInputText
    private $objChkFavorite = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
    private $objEdtPermissionResource = null; //dr\classes\dom\tag\webcomponents\DRInputText
    private $objChkVisibleMenu = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
    private $objChkVisibleToolbar = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
    private $objChkNewTab = null; //dr\classes\dom\tag\webcomponents\DRCheckbox
   
    private $objPermissions = null;
    private $objModules = null;

    public function initModel()
    {        

    }


    /**
     * render shissle to screen
     *
     * @param $arrVars extra variables to add to the render (you can call this method in one of the child classes)
     * @return void
     */
    // public function render($arrVars = array())
    // {
    //     $arrVars['objEdtName'] = $this->objEdtName;
    //     $arrVars['objEdtURLSlug'] = $this->objEdtURLSlug;
    //     $arrVars['objCbxParent'] = $this->objCbxParent;

    //     parent::render($arrVars);
    // }

    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
        // name
        $this->objEdtNameDefault = new DRInputText();
        $this->objEdtNameDefault->setNameAndID('edtNameDefault');
        $this->objEdtNameDefault->setClass('fullwidthtag');   
        // $this->objEdtNameDefault->setValue('testvalue1');   
        // $this->objEdtNameDefault->setPlaceholder('full product name');   
        // $this->objEdtNameDefault->setOnchange("validateField(this, true)");
        // $this->objEdtNameDefault->setOnkeyup("setDirtyRecord()");
        $this->objEdtNameDefault->setShowCharCounter(false);        
        $this->objEdtNameDefault->setRequired(true);  
        $this->objEdtNameDefault->setMinLength(5);         
        $this->objEdtNameDefault->setMaxLength(50);
        $objValidator = new TMaximumLength(50);
        $this->objEdtNameDefault->addValidator($objValidator);    
        $objValidator = new TRequired();
        $this->objEdtNameDefault->addValidator($objValidator);    
        $this->objEdtNameDefault->setWhitelist(WHITELIST_SAFE);
        // $this->getFormGenerator()->add($this->objEdtNameDefault, '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_nameinit', 'Default name (will be overwritten by language files of module)'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtNameDefault, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_nameinit_description', 'Name'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_nameinit_iconinfo', 'This is the default text for the menu item.<br>Be aware that this name will be overwritten by contents of a language file of a module when a translation is available'),
        );
        
        // module
        $this->objEdtModule = new DRInputCombobox();
        $this->objEdtModule->setNameAndID('edtModule');
        $this->objEdtModule->setClass('fullwidthtag');   
        // $this->objEdtModule->setValue('testvalue1');   
        // $this->objEdtModule->setPlaceholder('full product name');   
        // $this->objEdtModule->setOnchange("validateField(this, true)");
        // $this->objEdtModule->setOnkeyup("setDirtyRecord()");
        // $this->objEdtModule->setMaxLength(100);
        // $objValidator = new TMaximumLength(100);
        // $this->objEdtModule->addValidator($objValidator);    
        // $this->objEdtModule->setWhitelist(WHITELIST_SAFE);
        // $this->getFormGenerator()->add($this->objEdtModule, '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_module', 'Module'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtModule, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_module_description', 'Module'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_module_infoicon', 'The module you want to link to in menu.<br>The module is ignored when specifying a custom URL'),
        );

        

        //module controller
        $this->objEdtController = new DRInputText();
        $this->objEdtController->setNameAndID('edtModuleController');
        $this->objEdtController->setClass('fullwidthtag');   
        // $this->objEdtController->setRequired(true); 
        // $this->objEdtController->setValue('testvalue2');   
        // $this->objEdtController->setPlaceholder('URL slug');   
        // $this->objEdtController->setOnchange("validateField(this, true)");
        // $this->objEdtController->setOnkeyup("setDirtyRecord()");
        // $this->objEdtController->setShowCharCounter(true);
        // $this->objEdtController->setMinLength(5);
        $this->objEdtController->setMaxLength(100);
        $objValidator = new TMaximumLength(100);
        $this->objEdtController->addValidator($objValidator);    
        // $objValidator = new TRequired();
        // $this->objEdtController->addValidator($objValidator);    
        $this->objEdtController->setWhitelist(WHITELIST_URLSLUG);
        // $this->getFormGenerator()->add($this->objEdtController, '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_modulecontroller', 'URL slug in module'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtController, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_modulecontroller_description', 'URL slug'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_modulecontroller_infoicon', 'This URL slug points to page in the module you set above.<br>A URL slug is the last part of a URL.'),
        );

        //custom url
        $this->objEdtURLCustom = new DRInputText();
        $this->objEdtURLCustom->setNameAndID('edtURLCustom');
        $this->objEdtURLCustom->setClass('fullwidthtag');   
        // $this->objEdtURLCustom->setRequired(true); 
        // $this->objEdtURLCustom->setValue('testvalue2');   
        // $this->objEdtURLCustom->setPlaceholder('URL slug');   
        // $this->objEdtURLCustom->setOnchange("validateField(this, true)");
        // $this->objEdtURLCustom->setOnkeyup("setDirtyRecord()");
        // $this->objEdtURLCustom->setShowCharCounter(true);
        // $this->objEdtURLCustom->setMinLength(5);
        $this->objEdtURLCustom->setMaxLength(100);
        $objValidator = new TMaximumLength(100);
        $this->objEdtURLCustom->addValidator($objValidator);    
        // $objValidator = new TRequired();
        // $this->objEdtURLCustom->addValidator($objValidator);    
        $this->objEdtURLCustom->setWhitelist(WHITELIST_URL);        
        // $this->getFormGenerator()->add($this->objEdtURLCustom, '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_customurl', 'Custom URL (leave empty when using Module and Module-url-slug. Custom URL overwrites Module and Module-url-slug)'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtURLCustom, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_customurl_description', 'Custom URL'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_customurl_infoicon', 'Instead of linking to a module, you can link also link to a custom url (starting with http://).<br>When you submit a URL here, the system completely ignores the "module" and the "module-url-slug" you set above.<br>In other words: leave this field empty when you want to link to an internal module'),
        );

        //permission resource
        $this->objEdtPermissionResource = new DRInputCombobox();
        $this->objEdtPermissionResource->setNameAndID('edtPermissionResource');
        $this->objEdtPermissionResource->setClass('fullwidthtag');   
        // $this->objEdtPermissionResource->setRequired(true); 
        // $this->objEdtPermissionResource->setValue('testvalue2');   
        // $this->objEdtPermissionResource->setPlaceholder('URL slug');   
        // $this->objEdtPermissionResource->setOnchange("validateField(this, true)");
        // $this->objEdtPermissionResource->setOnchange("setDirtyRecord()");
        // $this->objEdtPermissionResource->setOnkeyup("setDirtyRecord()");
        // $this->objEdtPermissionResource->setShowCharCounter(true);
        // $this->objEdtPermissionResource->setMinLength(5);
        // $this->objEdtPermissionResource->setMaxLength(255);
        // $objValidator = new TMaximumLength(255);
        // $this->objEdtPermissionResource->addValidator($objValidator);    
        // $objValidator = new TRequired();
        // $this->objEdtPermissionResource->addValidator($objValidator);  
        $this->objEdtURLCustom->setWhitelist(WHITELIST_URL);        
        // $this->getFormGenerator()->add($this->objEdtPermissionResource, '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_permissionresource', 'Use permission resource (you can edit permissions in users module)'));
        $this->getFormGenerator()->addQuick(
            $this->objEdtPermissionResource, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_permissionresource_description', 'Permission resource'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_permissionresource_infoicon', 'Depending on the permissions of the user, the system shows only certain menu items to users who have the proper permissions.<br>Which permission resource would you like to use to show this menu item?<br>You can assign/retract permissions to users in the user module under "roles and permissions"'),
        );

        //SVG icon
        $this->objEdtSVGIcon = new DRInputText();
        $this->objEdtSVGIcon->setNameAndID('edtSVGIcon');
        $this->objEdtSVGIcon->setClass('fullwidthtag');   
        // $this->objEdtSVGIcon->setRequired(true); 
        // $this->objEdtSVGIcon->setValue('testvalue2');   
        // $this->objEdtSVGIcon->setPlaceholder('URL slug');   
        // $this->objEdtSVGIcon->setOnchange("validateField(this, true)");
        // $this->objEdtSVGIcon->setOnkeyup("setDirtyRecord()");
        // $this->objEdtSVGIcon->setShowCharCounter(true);
        // $this->objEdtSVGIcon->setMinLength(5);
        // $this->objEdtSVGIcon->setMaxLength(255);
        // $objValidator = new TMaximumLength(255);
        // $this->objEdtSVGIcon->addValidator($objValidator);    
        // $objValidator = new TRequired();
        // $this->objEdtSVGIcon->addValidator($objValidator);    
        // $this->objEdtSVGIcon->setWhitelist(DRInputText::WHITELIST_DISABLED);       
        $this->objEdtSVGIcon->setWhitelist(WHITELIST_HTML);       
        $this->getFormGenerator()->addQuick(
            $this->objEdtSVGIcon, 
            '', 
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_svgicon_description', 'SVG icon'),
            transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_svgicon_infoicon', 'Use the full HTML tag, like &lt;SVG&gt;&lt;CIRCLE&gt; etc...'),
        );
    // public function add(HTMLTag $objDOMElement, $sSection = '', $sDescription = '', $bDescriptionVisible = true, $sPostDescription = '', $bMarkAsRequiredField = false, $sTextInfoIcon = '')
            //favorite
        $this->objChkFavorite = new DRInputCheckbox();
        $this->objChkFavorite->setNameAndID('chkFavorite');
        // $this->objChkFavorite->setOnchange("setDirtyRecord()");
        // $this->objChkFavorite->setOnkeyup("setDirtyRecord()");        
        $this->objChkFavorite->setLabel(transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_favorite', 'Is favorite<dr-icon-info>When an item is favorited, it will show up at the top of the menu so you can quickly find it.<br><br>It might take 1 hour for this change to take effect.</dr-icon-info>'));
        $this->getFormGenerator()->add($this->objChkFavorite); 

            //visible menu
        $this->objChkVisibleMenu = new DRInputCheckbox();
        $this->objChkVisibleMenu->setNameAndID('chkVisibleMenu');
        // $this->objChkVisibleMenu->setOnchange("setDirtyRecord()");
        // $this->objChkVisibleMenu->setOnkeyup("setDirtyRecord()");        
        $this->objChkVisibleMenu->setLabel(transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_visiblemenu', 'Is visible in menu<dr-icon-info>The menu is on the left side of the screen.<br><br>It might take 1 hour for this change to take effect.</dr-icon-info>'));
        $this->getFormGenerator()->add($this->objChkVisibleMenu);  
        // $this->getFormGenerator()->addQuick($this->objChkVisibleMenu, '', '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_visiblemenu_iconinfo', 'It might take 1 hour for this change to take effect.<br>The menu is cached to ensure fast page loads.<br>This cached is renewed every hour.'));

            //visible toolbar
        $this->objChkVisibleToolbar = new DRInputCheckbox();
        $this->objChkVisibleToolbar->setNameAndID('chkVisibleToolbar');
        // $this->objChkVisibleToolbar->setOnchange("setDirtyRecord()");
        // $this->objChkVisibleToolbar->setOnkeyup("setDirtyRecord()");        
        $this->objChkVisibleToolbar->setLabel(transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_visibletoolbar', 'Is visible in toolbar<dr-icon-info>The toolbar is on top of the screen.<br><br>It might take 1 hour for this change to take effect.</dr-icon-info>'));
        $this->getFormGenerator()->add($this->objChkVisibleToolbar);          
        // $this->getFormGenerator()->addQuick($this->objChkVisibleToolbar, '', '', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_visibletoolbar_iconinfo', ''));

            //open new tab
        $this->objChkNewTab = new DRInputCheckbox();
        $this->objChkNewTab->setNameAndID('chkOpenNewTab');
        // $this->objChkNewTab->setOnchange("setDirtyRecord()");
        // $this->objChkNewTab->setOnkeyup("setDirtyRecord()");        
        $this->objChkNewTab->setLabel(transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_field_opennewtab', 'Open link in new browser tab'));
        $this->getFormGenerator()->add($this->objChkNewTab);  

    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Settings::PERM_CAT_CMSMENU;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //name
        $this->getModel()->set(TSysCMSMenu::FIELD_NAMEDEFAULT, $this->objEdtNameDefault->getValueSubmitted());

        //module
        $this->getModel()->set(TSysCMSMenu::FIELD_MODULENAMEINTERNAL, $this->objEdtModule->getValueSubmitted());

        //module controller
        $this->getModel()->set(TSysCMSMenu::FIELD_CONTROLLER, $this->objEdtController->getValueSubmitted());

        //custom url
        $this->getModel()->set(TSysCMSMenu::FIELD_URL, $this->objEdtURLCustom->getValueSubmitted());

        //permission resource
        $this->getModel()->set(TSysCMSMenu::FIELD_PERMISSIONRESOURCE, $this->objEdtPermissionResource->getValueSubmitted());

        //svg icon
        $this->getModel()->set(TSysCMSMenu::FIELD_SVGICON, $this->objEdtSVGIcon->getValueSubmitted(Form::METHOD_POST, FormInputAbstract::GETVALUESUBMITTED_RETURN_ALLOWHTMLSVG));

        //favorite
        $this->getModel()->set(TSysCMSMenu::FIELD_ISFAVORITE, $this->objChkFavorite->getValueSubmittedAsBool());             

        //visible menu
        $this->getModel()->set(TSysCMSMenu::FIELD_ISVISIBLEMENU, $this->objChkVisibleMenu->getValueSubmittedAsBool());             

        //visible toolbar
        $this->getModel()->set(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, $this->objChkVisibleToolbar->getValueSubmittedAsBool());             

        //new tab
        $this->getModel()->set(TSysCMSMenu::FIELD_OPENNEWTAB, $this->objChkNewTab->getValueSubmittedAsBool());             

    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtNameDefault->setValue($this->getModel()->get(TSysCMSMenu::FIELD_NAMEDEFAULT));

        //module
        $this->objEdtModule->setValue($this->getModel()->get(TSysCMSMenu::FIELD_MODULENAMEINTERNAL));

        //controller
        $this->objEdtController->setValue($this->getModel()->get(TSysCMSMenu::FIELD_CONTROLLER));

        //custom url
        $this->objEdtURLCustom->setValue($this->getModel()->get(TSysCMSMenu::FIELD_URL));

        //permission resource
        $this->objEdtPermissionResource->setValue($this->getModel()->get(TSysCMSMenu::FIELD_PERMISSIONRESOURCE));

        //SVG
        $this->objEdtSVGIcon->setValue($this->getModel()->get(TSysCMSMenu::FIELD_SVGICON));

        //favorite
        $this->objChkFavorite->setChecked($this->getModel()->get(TSysCMSMenu::FIELD_ISFAVORITE));   

        //visible menu
        $this->objChkVisibleMenu->setChecked($this->getModel()->get(TSysCMSMenu::FIELD_ISVISIBLEMENU));   

        //visible toolbar
        $this->objChkVisibleToolbar->setChecked($this->getModel()->get(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR));   

        //new tab
        $this->objChkNewTab->setChecked($this->getModel()->get(TSysCMSMenu::FIELD_OPENNEWTAB));   

    }
    
   /**
     * is called after a record is loaded
     */
    public function onLoadPost()
    {
        //load permission resources
        $this->objPermissions->clear(true);
        $this->objPermissions->distinct(TSysCMSPermissions::FIELD_RESOURCE);
        $this->objPermissions->loadFromDB();
        $this->objEdtPermissionResource->addItem('', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_permissionresource_empty', '[EVERYONE]'));
        while($this->objPermissions->next())            
            $this->objEdtPermissionResource->addItem($this->objPermissions->getResource(), $this->objPermissions->getResource());

        //load modules
        $this->objModules->clear(true);
        $this->objModules->loadFromDB();
        // $this->objEdtModule->addItem('', transm(APP_ADMIN_CURRENTMODULE, 'form_cmsmenu_module_empty', '[NO MODULE]'));
        while($this->objModules->next())            
            $this->objEdtModule->addItem($this->objModules->getNameInternal(), $this->objModules->getNameDefault());

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
    public function onSavePost($bWasSaveSuccesful)
    { 
        return array();
    }
    

    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    public function onCreate() 
    {
        $this->objPermissions = new TSysCMSPermissions();
        $this->objModules = new TSysModules();
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
        return new TSysCMSMenu(); 
    }

    /**
     * return path of the page template
     *
     * @return string
     */
    public function getTemplatePath()
    {
        return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsaveajax.php';
        // return APP_PATH_MODULES.DIRECTORY_SEPARATOR.APP_ADMIN_CURRENTMODULE.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.'templates'.DIRECTORY_SEPARATOR.'tpl_detailsave_products.php';
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
        return 'list_cmsmenu';
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
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_cmsmenu_new_title', 'Create new menu item');
        else
            return transm(APP_ADMIN_CURRENTMODULE, 'pagetitle_detailsave_cmsmenu_edit_title', 'Edit menu item');   
    }


    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd etc)
     * 
     * @return string
     */
    public function getUploadDir()
    {
        return 'cmsmenu';
    }    

   /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    public function getAuthCreate()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_CMSMENU, TModuleAbstract::PERM_OP_CREATE);
    }

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    public function getAuthView()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_CMSMENU, TModuleAbstract::PERM_OP_VIEW);
    }


    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    public function getAuthChange()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_CMSMENU, TModuleAbstract::PERM_OP_CHANGE);
    }


    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    public function getAuthDelete()
    {
        return auth(APP_ADMIN_CURRENTMODULE, Mod_Sys_Settings::PERM_CAT_CMSMENU, TModuleAbstract::PERM_OP_DELETE);
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
