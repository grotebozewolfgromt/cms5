<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace dr\modules\Mod_Sys_Modules\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\form\InputCheckbox;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\TMaximumLength;
use dr\classes\dom\validator\TRequired;

//don't forget ;)
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\modules\Mod_Sys_Modules\Mod_Sys_Modules;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');



/**
 * Description of TCRUDDetailSaveLanguages
 *
 * @author drenirie
 */
class detailsave_installedmodules extends TCRUDDetailSaveController
{
    private $objEdtName = null;//dr\classes\dom\tag\form\InputText --> only for consistency, it's a readonly editbox
    // private $objOptCategory = null;//dr\classes\dom\tag\form\Select
    private $objChkVisibleCMS = null;//dr\classes\dom\tag\form\InputCheckbox
    private $objChkVisibleFrontEnd = null;//dr\classes\dom\tag\form\InputCheckbox
    
        
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //module name
        $this->objEdtName = new InputText();
        $this->objEdtName->setNameAndID('edtName');
        $this->objEdtName->setClass('fullwidthtag');                         
        $this->objEdtName->setReadOnly(true);
        $this->getFormGenerator()->add($this->objEdtName, '', transm($this->getModule(), 'form_field_internalname', 'internal name (read-only)'));
        
            //category
        // $this->objOptCategory = new Select();
        // $this->objOptCategory->setNameAndID('optCategory');
        // $this->getFormGenerator()->add($this->objOptCategory, '', transm($this->getModule(), 'form_field_category', 'category'));

            //is visible backend
        $this->objChkVisibleCMS = new InputCheckbox();
        $this->objChkVisibleCMS->setNameAndID('chkVisibleCMS');
        $this->getFormGenerator()->add($this->objChkVisibleCMS, '', transm($this->getModule(), 'form_field_isvisiblecms', 'is visible in cms (in menus etc)'));   

            //is visible frontend
        $this->objChkVisibleFrontEnd = new InputCheckbox();
        $this->objChkVisibleFrontEnd->setNameAndID('chkVisibleFrontEnd');
        $this->getFormGenerator()->add($this->objChkVisibleFrontEnd, '', transm($this->getModule(), 'form_field_isvisiblefrontend', 'is visible in frontend (in menus etc)'));   
        
    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        return Mod_Sys_Modules::PERM_CAT_MODULESINSTALLED;
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
//        $this->getModel()->set(TSysModules::FIELD_NAMEINTERNAL, $this->objEdtName->getValueSubmitted()); --> read only editbox
        // $this->getModel()->set(TSysModules::FIELD_MODULECATEGORYID, $this->objOptCategory->getValueSubmittedAsInt());
        $this->getModel()->set(TSysModules::FIELD_VISIBLECMS, $this->objChkVisibleCMS->getValueSubmittedAsBool());                        
        $this->getModel()->set(TSysModules::FIELD_VISIBLEFRONTEND, $this->objChkVisibleFrontEnd->getValueSubmittedAsBool());                        
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //name
        $this->objEdtName->setValue($this->getModel()->get(TSysModules::FIELD_NAMEINTERNAL));
                
        //categories
        // $objCategories = new \dr\modules\Mod_Sys_Modules\models\TSysModulesCategories();
        // $objCategories->loadFromDB();
        // $objCategories->generateHTMLSelect($this->getModel()->get(TSysModules::FIELD_MODULECATEGORYID), $this->objOptCategory);

        //visible cms
        $this->objChkVisibleCMS->setChecked($this->getModel()->get(TSysModules::FIELD_VISIBLECMS));

        //visible backend
        $this->objChkVisibleFrontEnd->setChecked($this->getModel()->get(TSysModules::FIELD_VISIBLEFRONTEND));
            
    }
    
    /**
     * is called when a record is loaded
     */
    public function onLoad()
    {}
    
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
     */
    public function onSavePost($bWasSaveSuccesful){ return true; }

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
        return new TSysModules(); 
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
        return 'list_installedmodules';
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
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_installedmodules_new', 'Create new module');
        else
            return transm(CMS_CURRENTMODULE, 'pagetitle_detailsave_installedmodules_edit', 'Edit module: [modulename]', 'modulename', $this->getModel()->getNameInternal());           
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
