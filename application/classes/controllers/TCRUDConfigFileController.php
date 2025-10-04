<?php
namespace dr\classes\controllers;

/**
 * Description of TCRUDConfigFileController
 * Create Read Update (Delete)
 * A controller for a detail & save screen for config files
 * 
 * This class creates a default structure for these screens to program quickly a detail screen
 * 
 * You need to inherit this class to use it's functionality
 * 
 * 
 * the goal of this class is to keep it lightweight due to OOP performance issues 
 * in PHP, so no parent class (it needs to be as flat as possible when it comes to parent classes depth)
 * 
 * 
 * 
 * 
 * @author drenirie
 * 
 * 3 jan 2025: TCRUDConfigFileController: create
 */

use dr\classes\models\TSysModel;

use dr\classes\dom\FormGenerator;    
use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\InputSubmit;
use dr\classes\dom\tag\form\InputButton;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\InputText;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\validator\Maximumlength;
use dr\classes\locale\TLocalisation;
use dr\classes\patterns\TModuleAbstract;




abstract class TCRUDConfigFileController
{
    private $objConfigFile = null;
    private $sModule = '';
    private $sReturnURL = '';
    // private $sURLThisScript = '';
    private $arrCommandPanel = array();//array of elements to put in form generator. We use it twice: once on the top and once on the bottom
    
    
    private $objFormGenerator = null;//dr\classes\dom\FormGenerator
    public $objSubmit = null;//dr\classes\dom\tag\form\InputSubmit
    // private $objSubmitClose = null;//dr\classes\dom\tag\form\InputSubmit
    public $objCancel = null;//dr\classes\dom\tag\form\InputButton
   
    
    private $sDefaultDateFormat = 'm/d/Y'; //default php date format
    private $sDefaultTimeFormat = 'h:i A'; //default php date format
    private $sDefaultDateTimeFormat = 'm/d/Y h:i A'; //default php date format

        
    /**
     * 
     */
    public function __construct()
    {
        global $objLocalisation;     

        $this->sModule = getModuleFromURL();
        if ($this->sModule == '')
        {
            $this->sModule = AUTH_MODULE_CMS;
        }

        $this->objConfigFile = $this->getNewConfigFile();
        $this->sReturnURL = $this->getReturnURL();
        // $this->sURLThisScript = getURLThisScript();

        //is user allowed to view this screen?
        if (auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_VIEW))
        {
        
            if ($objLocalisation)
            {
                $this->sDefaultDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT);
                $this->sDefaultTimeFormat = $objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
                $this->sDefaultDateTimeFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT).' '.$objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
            }            


            $this->onCreate();
            $this->handleCancel();
            $this->handleNewEditRecord();
            $this->onLoad();
            $this->populateInternal(); //--> populate here so you can change the contents of the form according to the contents of the record (i.e. we want to display other text with the password when it's a new record)        
            if ($this->objFormGenerator->isFormSubmitted())           
                $this->handleSubmitted(); //-> + viewToModel() is called here
            $this->modelToView();     
            
            //add commandpanel at the bottom
            $this->objFormGenerator->addArray($this->arrCommandPanel, 'commands_bottom');  
            $this->objFormGenerator->assignCSSClassSection('commands_bottom', 'div_commandpanel');               

            //render
            $this->render();
        } 
        else
        {         
            showAccessDenied(transcms('message_noaccess_vieweditrecord', 'you don\'t have permission to view this record'));
            die();
        }
    }


    /**
     * render shissle to screen
     *
     * @return void
     */
    public function render()
    {
        global $objCurrentModule;

        $sHTMLContentSkinWithTemplate = '';
        $arrVars = array();

        //variables for the template
        $arrVars['sTitle'] = $this->getTitle();
        $arrVars['sHTMLTitle'] = $arrVars['sTitle'];
        $arrVars['sHTMLMetaDescription'] = $arrVars['sTitle'];
        $arrVars['objConfigFile'] = $this->getConfigFile();
        $arrVars['objCRUD'] = $this;
        $arrVars['objFormGenerator'] = $this->getFormGenerator();
        $arrVars['objController'] = $this;
        if ($this->showTabs())
            $arrVars['arrTabsheets'] = $objCurrentModule->getTabsheets(); 
        $arrVars = array_merge($GLOBALS, $arrVars); //ORDER OF PARAMETERS IS IMPORTANT -> we pick $GLOBALS as base and overwrite them with the variables from execute()

        //render templates
        if ($this->getTemplatePath() != '') //only render if exists
            $arrVars['sHTMLContentMain'] = renderTemplate($this->getTemplatePath(), $arrVars); //add content template to the variables for the skin                        
        else
            $arrVars['sHTMLContentMain'] = '';


        if ($this->getSkinPath() != '') //only render if exists    
            $sHTMLContentSkinWithTemplate = renderTemplate($this->getSkinPath(), $arrVars);    
        else
            $sHTMLContentSkinWithTemplate = '';


        //output to screen
        echo $sHTMLContentSkinWithTemplate;
    }
    
    /**
     * set config file object
     * @param TPHPConstrantsConfigFile $objConfigFile
     */
    public function setConfigFile($objConfigFile)
    {
        $this->objConfigFile = $objConfigFile;
    }
    
    /**
     * get module object
     * @return TPHPConstrantsConfigFile
     */
    public function getConfigFile()
    {       
        return $this->objConfigFile;
    }
    

    
    /**
     * set model object
     * @param TSysModel $objModel
     */
    public function setModule($sModule)
    {
        $this->sModule = $sModule;
    }
    
    /**
     * get module object
     * @return TSysModel
     */
    public function getModule()
    {
        return $this->sModule;
    }
    
    /**
     * caching function for function getURLThisScript()
     * internal variable set in constructor by calling getURLThisScript();
     */
    // protected function getURLThisScript()
    // {
    //     return $this->sURLThisScript;
    // }
    
    /**
     * 
     * @return dr\classes\dom\FormGenerator
     */
    public function getFormGenerator()
    {
        return $this->objFormGenerator;
    }
    
    /**
     * retrieve default date format (PHP date() format)
     * 
     * @return string
     */
    public function getDateFormatDefault()
    {
        return $this->sDefaultDateFormat;
    }
    
    /**
     * retrieve default time format (PHP date() format)
     * 
     * @return string
     */    
    public function getTimeFormatDefault()
    {
        return $this->sDefaultTimeFormat;
    }
    
    /**
     * retrieve default date + time format (PHP date() format)
     * 
     * @return string
     */    
    public function getDateTimeFormatDefault()
    {
        return $this->sDefaultDateFormat.' ' .$this->sDefaultTimeFormat;
    }
    


    
    
    /**
     * define the fields that are in the detail screen
     * this function calls the abstract function populate();
     */
    protected function populateInternal()
    {
        $this->objFormGenerator = new FormGenerator('detailsave', APP_URLTHISSCRIPT);
        $bAllowedChange = auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE);
        $bAllowedCreate = auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CREATE);        
    
        //commandpanel first (later again)
            //submit
        $this->objSubmit = new InputSubmit();    
        $this->objSubmit->setValue(transcms('form_button_save', 'save'));
        $this->objSubmit->setName('btnSubmit');

            //submit & close
        // $this->objSubmitClose = new InputSubmit();    
        // $this->objSubmitClose->setValue(transcms('form_button_save_and_close', 'save & close'));
        // $this->objSubmitClose->setName('btnSubmitClose');
        
        //add submit & submitclose to panel
        if ($bAllowedChange || $bAllowedCreate)
        {
            $this->arrCommandPanel[] = $this->objSubmit;
            // $this->arrCommandPanel[] = $this->objSubmitClose;
        }
    

            //cancel
        $this->objCancel = new InputButton();    
        $this->objCancel->setValue(transcms('form_button_exit', 'exit'));
//        $this->objCancel->setValue();
        $this->objCancel->setName('btnClose');    
        $this->objCancel->setClass('button_cancel');    
        $this->objCancel->setOnclick('window.location.href = \''.addVariableToURL($APP_URLTHISSCRIPT, ACTION_VARIABLE_CANCEL, ACTION_VALUE_CANCEL).'\';');    
        $this->arrCommandPanel[] = $this->objCancel;

        //removed command panel at the top on 1-6-2023
        //$this->objFormGenerator->addArray($this->arrCommandPanel, 'commands_top');
        //$this->objFormGenerator->assignCSSClassSection('commands_top', 'div_commandpanel');          
        
        
        //populate child class
        $this->populate();
        
        
        //add another command panel on the bottom
        //done after modelToView()
    }
    
    /**
     * handle cancel button click
     */
    protected function handleCancel()
    {
        //HANDLE: cancel
        if (isset($_GET[ACTION_VARIABLE_CANCEL]))
        {
            if ($_GET[ACTION_VARIABLE_CANCEL] == ACTION_VALUE_CANCEL) //is cancel button pressed?
            {
                header('Location: '.$this->sReturnURL);
                die();
            }
        }        
    }
    
    /**
     * handle db loading or creating a new record
     */
    protected function handleNewEditRecord()
    {
        global $objAuthenticationSystem;
        
        if (!$this->objConfigFile->loadFile($this->getFilePathConfigFile()))
        {
            error_log(__CLASS__.': loadFile() error loading config file: '.$this->getFilePathConfigFile());
            sendMessageError(transcms('message_loadconfigfile_failed', 'Failed loading config file. Does file exist?'));
        }

     
    }

    /**
     * handle form submission
     * what needs to happen when form is submitted
     */
    protected function handleSubmitted()
    {
        global $objDBConnection;

        //check for max_vars
        if ((int)ini_get('max_input_vars') ==  count($_POST))  
        {
            error_log('WARNING: $_POST array ('.count($_POST).') is bigger than max_input_vars ('.ini_get('max_input_vars').') in php.ini. Config file not saved, user received error');

            //return to return url
            header('Location: '.addVariableToURL($this->sReturnURL, GETARRAYKEY_CMSMESSAGE_ERROR, transcms('message_save_configfile_nosuccess_maxinputvars_exceeded', 'CONFIG FILE NOT SAVED!!!! Too much data is being sent. Didn\'t save to prevent loss of data integrity')));
            die(); //stop further execution to display the record                
        }
    
        $this->viewToModel(); //--> if there is an error on the form we want to be able to correct it with all the values filled in
            
        if ($this->objFormGenerator->isValid()) //========== SAVE to database =========
        {


            //==== SAVE PRE
            if ($this->onSavePre())
            {               
                $bSaveSuccess = false;


                //==== SAVE the CONFIG file
                $bSaveSuccess = false;
                if (auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE))
                {
                    $bSaveSuccess = $this->objConfigFile->saveFile($this->getFilePathConfigFile());
                }
                else
                {
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'auth() for saving config file FAILED');                
                }

                //==== SAVE POST
                if (!$this->onSavePost($bSaveSuccess)) 
                {
                    $bSaveSuccess = false;
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'onSavePost() FAILED');
                }                  

                //====handle button presses
                if ($bSaveSuccess)
                {
                    /*
                    //BUTTON save & close clicked
                    // if (!$this->objSubmitClose->getContentsSubmitted(Form::METHOD_POST)->isEmpty())//"submit & close" has a value if clicked
                    // {
                    //     //return to return url
                    //     header('Location: '.addVariableToURL($this->sReturnURL, GETARRAYKEY_CMSMESSAGE_SUCCESS, transcms('message_save_success', 'Record save successful')));
                    //     die(); //stop further execution to display the record
                    // }
                    // else //BUTTON save clicked
                    {
                        //if it is a new record that is just saved, we need to reload the page as a record to edit (otherwise the TSysModel will always try add the record instead of updating it)
                        if (!$this->isNewRecord())
                        {
                            $sURL = '';
                            $sURL = $this->getURLThisScript();
                            $sURL = addVariableToURL($sURL, GETARRAYKEY_CMSMESSAGE_SUCCESS, transcms('message_saverecord_success', 'Record save successful'));
                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $this->objModel->getID());

                            header('Location: '.$sURL);
                            die(); //stop further execution to display the record                                
                        }

                        //continue displaying the record
                        sendMessageSuccess(transcms('message_saverecord_success', 'Record save successful'));
                    }
                    */
                    sendMessageSuccess(transcms('message_saveconfig_success', 'Config save successful'));                    
                }
                else
                {
                    sendMessageError(transcms('message_saveconfig_error', 'Save error: config file NOT saved!!'));
                    logError($this->sModule.':'.$this->getAuthorisationCategory(), $this->getAuthorisationCategory().' save error config file '. $this->getFilePathConfigFile());                
                } 
            }//end: onSave()
            else 
            {                    
                logError($this->sModule.':'.$this->getAuthorisationCategory(), $this->getAuthorisationCategory().': onSavePre() failed: save error config file '. $this->getFilePathConfigFile());                
            }
        }//end: isValid()
        else
        {
            sendMessageError(transcms('message_saveconfig_inputerror', 'Input error: config file NOT saved'));
            logError($this->sModule.':'.$this->getAuthorisationCategory(),': config file '. $this->getFilePathConfigFile(). ' not saved due to input errors, objFormGenerator->isValid() failed');            
        }

        //====== putting submitted data in the form
    //    $this->objFormGenerator->setSubmittedValuesAsValues();

    }


    /**
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    public function getReturnURL()
    {
        if (isset($_GET[ACTION_VARIABLE_RETURNURL]))
            return $_GET[ACTION_VARIABLE_RETURNURL];
        else
            return getURLCMSDashboard();
    }
    
    
    /**
     * what is the file path of the config file
     *
     * @return string
     */
    public function getFilePathConfigFile()
    {
        return getPathModuleConfigFile($this->getModule());
    }

//=====================================================================================

	// ************************************************
	// ====== ONLY ABSTRACT FUNCTIONS BELOW ==========
	//
	// *   for easy copy/pasting in child classes     *
	// ************************************************
	
//=====================================================================================    
    
    /**
     * define the fields that are in the detail screen
     * 
     */    
    abstract protected function populate();
 
    /**
     * what is the category that the auth() function uses?
     */
    abstract protected function getAuthorisationCategory();
    
    /**
     * transfer form elements to database
     */
    abstract protected function viewToModel();
    
    /**
     * transfer database elements to form
     */
    abstract protected function modelToView();
    
    /**
     * is called when a record is loaded
     */
    abstract public function onLoad();
    
    
    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    abstract public function onCreate();    
    
    /**
     * is called BEFORE a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN TRUE, OTHERWISE IT WILL NOT SAVE!!
     * 
     * @return boolean true = save, false = no save
     */
    abstract public function onSavePre();
    
  
    /**
     * is called AFTER a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN TRUE, OTHERWISE IT WILL NOT SAVE!!
     * 
     * @param boolean $bWasSaveSuccesful did saveToDB() return false or true?
     * @return boolean true = save, false = no save
     */
    abstract public function onSavePost($bWasSaveSuccesful);    

    /**
     * returns a new config file object
     *
     * @return TPHPConstantsConfigFile
     */
    abstract public function getNewConfigFile();

    /**
     * return path of the page template
     *
     * @return string
     */
    abstract public function getTemplatePath();

    /**
     * return path of the skin template
     * 
     * return '' if no skin
     *
     * @return string
     */
    abstract public function getSkinPath();

    /**
     * return page title
     * This title is different for creating a new record and editing one.
     * It returns in the translated text in the current language of the user (it is not translated in the controller)
     * 
     * for example: "create a new user" or "edit user John" (based on if $objModel->getNew())
     *
     * @return string
     */
    abstract public function getTitle();

    /**
     * show tabsheets on top?
     *
     * @return bool
     */
    abstract public function showTabs();    

    
}
