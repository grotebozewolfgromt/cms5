<?php


namespace dr\classes\controllers;

use dr\classes\models\TSysModel;
use dr\classes\controllers\TCRUDDetailSaveController;
use dr\classes\dom\tag\form\Select;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsAbstract;
use dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages;
//don't forget ;)
use dr\modules\Mod_Sys_Modules\models\TSysModules;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_PageBuilder\controllers\pagebuilder;


include_once(APP_PATH_CMS.DIRECTORY_SEPARATOR.'bootstrap_admin_auth.php');

/**
 * Description of pagebuilderrouter
 *
 * This class routes the user to the right pagebuilder.
 * It can show a screen to choose the right pagebuilder.
 * 
 * Every time you want to use a pagebuilder somewhere in your module (or somewhere else):
 * Just link to this router with <a href="APP_URL_ADMIN_PAGEBUILDERROUTER"></a> and this class takes 
 * the user to the right pagebuilder.
 * What the right pagebuilder is depends on the module that is stored in the TPageBuilderDocumentAbstract::getTable()
 * 
 * Cases:
 * A. NEW RECORD:
 *      1. if no pagebuilder installed (modules == 0): error
 *      2. if 1 pagebuilder installed (modules == 1): redirect
 *      3. if more than 1 pagebuilder installed (modules > 1): choose pagebuilder
 * B. EXISTING RECORD:
 *      1. if no pagebuilder installed (modules == 0): error
 *      2. if 1 CORRECT (compare modulename) pagebuilder installed (modules >= 1), redirect
 *      3. if none correct pagebuilders: choose pagebuilder
 * 
 * 
 * @author drenirie
 * 
 * 27 apr 2024 pagebuilderrouter created
 */
class pagebuilderrouter extends TCRUDDetailSaveController
{
    //const PERM_CAT_PAGEBUILDER = 'pagebuilder'; --> is defined in lib_sys_typedef.php

    private $objSelModule = null;//dr\classes\dom\tag\form\Select

    private $objModules = null;//TSysModules

    public function __construct()
    {       
        $this->objModules = new TSysModules();
        
        //everything is handled here, so we need to declare variables here
        parent::__construct();
    }
    
    /**
     * define the fields that are in the detail screen
     * 
     */ 
    protected function populate() 
    {
            //module
        $this->objSelModule = new Select();
        $this->objSelModule->setNameAndID('selModule');
        $this->objSelModule->setClass('quarterwidthtag');   
        $this->getFormGenerator()->add($this->objSelModule, '', transg('form_field_pagebuildermodule', 'Select your page builder'));

    }

    /**
     * what is the category that the auth() function uses?
     */
    protected function getAuthorisationCategory() 
    {
        // return pagebuilderrouter::PERM_CAT_PAGEBUILDER;
        return AUTH_CATEGORY_PAGEBUILDER; //--> defined in lib_sys_typedef
    }
    
    /**
     * transfer form elements to database
     */
    protected function viewToModel()
    {
        //module
        $this->getModel()->set(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL, $this->objSelModule->getValueSubmitted());        
    }
    
    /**
     * transfer database elements to form
     */
    protected function modelToView()
    {  
        //module
        $this->objModules->generateHTMLSelect(
            $this->getModel()->get(TPageBuilderDocumentsAbstract::FIELD_META_MODULENAMEINTERNAL), 
            $this->objSelModule, 
            TSysModules::FIELD_NAMEINTERNAL, 
            TSysModules::FIELD_NAMENICETRANSDEFAULT 
        );        
    }
    
   /**
     * is called when a record is loaded
     */
    public function onLoad()
    {
        global $objAuthenticationSystem;
        $sRedirectToPagebuilderModule = '';//the correct pagebuilder to start                

        //load modules from db
        $this->objModules->select(array(TSysModules::FIELD_ID, TSysModules::FIELD_NAMEINTERNAL, TSysModules::FIELD_NAMENICETRANSDEFAULT));
        $this->objModules->where(TSysModules::FIELD_MODULETYPE, TModuleAbstract::MOD_TYPE_PAGEBUILDER);
        $this->objModules->sort(TSysModules::FIELD_NAMENICETRANSDEFAULT);
        $this->objModules->loadFromDB();

        //check if pagebuilders installed
        if ($this->objModules->count() == 0)
        {
            sendMessageError(transcms('message_nopagebuildersinstalled', 'Can not start page builder.<br>There are no page builder modules installed'));            
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'No page builders installed at all', $objAuthenticationSystem->getUsers()->getUsername());
            return;            
        }     

        //look for the right pagebuilder
        if (!$this->isNewRecord()) //existing record
        {
            while ($this->objModules->next())            
                if ($this->objModules->getNameInternal() == $this->getModel()->getMetaModuleNameInternal())
                    $sRedirectToPagebuilderModule = $this->objModules->getNameInternal();

            //if didn't find the right pagebuilder
            if ($sRedirectToPagebuilderModule == '')
            {
                sendMessageError(transcms('message_notrightpagebuilder', 'Can not find the page builder [module].<br>Select another one, but no guarantee it will work', 'module', $this->getModel()->getMetaModuleNameInternal()));                
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'The right page builder module "'.$this->getModel()->getMetaModuleNameInternal().'" is NOT installed. Prompted user to try another one.', $objAuthenticationSystem->getUsers()->getUsername());
                return;
            }          
            
            //wrong version number module?
            $sRedirectToPagebuilderModule = filterDirectoryTraversal($sRedirectToPagebuilderModule);
            $sFullNamespaceModule = getModuleFullNamespaceClass($sRedirectToPagebuilderModule);
            $objMod = new $sFullNamespaceModule();
            if ($objMod->getVersion() < $this->getModel()->getModuleVersionNumber())
            {
                sendMessageError(transcms('message_incorrectversionnumber', '[module] document version ([documentversion]) is higher than page builder version ([moduleversion]).<br>You can still try to start it, but no guarantee it will work', 'module', $this->getModel()->getMetaModuleNameNice(), 'documentversion', $this->getModel()->getModuleVersionNumber(), 'moduleversion', $objMod->getVersion()));                
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Wrong pagebuilder version. Module "'.$this->getModel()->getMetaModuleNameInternal().'" (version '.$objMod->getVersion().') and document (version '.$this->getModel()->getModuleVersionNumber().'). Prompted user to try it anyway', $objAuthenticationSystem->getUsers()->getUsername());
                return;            
            }            
        }
        else //new record
        {
            if ($this->objModules->count() == 1) //only one installed, take that one
                $sRedirectToPagebuilderModule = $this->objModules->getNameInternal();


            //select right pagebuilder
            if ($sRedirectToPagebuilderModule == '')
            {
                return; //dont redirect
            }                      
        }
          


        //redirect to the right pagebuilder
        $sURL = '';
        $sURL = getURLPagebuilder($sRedirectToPagebuilderModule);    
        if (!$this->isNewRecord())
            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $_GET[ACTION_VARIABLE_ID]);
        if (isset($_GET[ACTION_VARIABLE_RETURNURL]))
            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_RETURNURL, $_GET[ACTION_VARIABLE_RETURNURL]);

        header('location: '.$sURL);                        
        die();
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
        return new TPageBuilderWebpages(); 
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
        //take the return url param, otherwise the first cms page
        if (isset($_GET[ACTION_VARIABLE_RETURNURL]))
            return $_GET[ACTION_VARIABLE_RETURNURL];
        else
            return getURLCMSDashboard();
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
        ////global APP_ADMIN_CURRENTMODULE;

        if ($this->getModel()->getNew())   
            return transcms('pagetitle_detailsave_transactionstypes_new', 'Create new document');
        else
            return transcms('pagetitle_detailsave_transactionstypes_edit', 'Edit document: [name]', 'name', $this->getModel()->getNameInternal());   
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
     * override from parent
     */
    protected function handleSubmitted()
    {        
        $sURL = '';
        $sURL = getURLPagebuilder($this->objSelModule->getValueSubmitted());
        if (isset($_GET[ACTION_VARIABLE_ID]))
            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $_GET[ACTION_VARIABLE_ID]);
        if (isset($_GET[ACTION_VARIABLE_RETURNURL]))
            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_RETURNURL, $_GET[ACTION_VARIABLE_RETURNURL]);
        header('location: '.$sURL);
        die();
    }

}
