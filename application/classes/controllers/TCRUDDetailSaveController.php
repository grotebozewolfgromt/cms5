<?php
namespace dr\classes\controllers;

/**
 * Description of TCRUDDetailSaveController
 * Create Read Update (Delete)
 * A controller for a detail & save screen
 * 
 * This class creates a default structure for these screens to program quickly a detail screen:
 * -TSysModel record loading and saving is implemented by default
 * -handling behaviour of buttonpresses: reloads of the same screen (save button) and closing (save & close button)
 * -authorisation is checked automatically
 * -formgenerator is implemented for quick programming and checking of valid values (is valid int, is valid date, no longer than x chars etc)
 * 
 * You need to inherit this class to use it's functionality
 * 
 * this controller assumes if an id is in the url $_GET[ACTION_VARIABLE_ID]) then it is an edit
 * if not an id in the url, it is a new record
 * 
 * the goal of this class is to keep it lightweight due to OOP performance issues 
 * in PHP, so no parent class (it needs to be as flat as possible when it comes to parent classes depth)
 * 
 * checkin/checkout is implemented leightweight because it can lead to more annoyances than actual safety
 * 
 * 
 * 
 * @author drenirie
 * 
 *  created 8 jan 2020
 * 10 jan 2020: TCRUDDetailSaveController: error message when database save gone wrong
 * 29 nov 2020: TCRUDDetailSaveController: the bottom control panel is added AFTER modelToForm() which allows for quicker programming with dynamic elements like user-sessions for users or permissions for userroles
 * 15 nov 2022: TCRUDDetailSaveController: "cancel" button is now "close"
 * 15 nov 2022: TCRUDDetailSaveController: functionality of "save & close" button removed
 * 17 nov 2023: TCRUDDetailSaveController: transactions integrated
 * 17 nov 2023: TCRUDDetailSaveController: isNewRecord() added
 * 28 apr 2024: TCRUDDetailSaveController: if modulename == '', then modulename is AUTH_MODULE_CMS
 * 28 apr 2024: TCRUDDetailSaveController: handleSubmitted() wordt nu alleen aangeroepen wanneer daadwerkelijk gesubmit is (ipv altijd)
 * 17 mei 2024: TCRUDDetailSaveController: fix: getDateTimeFormatDefault() gaf alleen tijd terug
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




abstract class TCRUDDetailSaveController
{
    private $objModel = null;
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

        $this->objModel = $this->getNewModel();
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
            
            // //add commandpanel at the bottom --> removed 1-5-2025
            // $this->objFormGenerator->addArray($this->arrCommandPanel, 'commands_bottom');  
            // $this->objFormGenerator->assignCSSClassSection('commands_bottom', 'div_commandpanel');               

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
        $arrVars['objModel'] = $this->getModel();
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
     * set model object
     * @param TSysModel $objModel
     */
    public function setModel($objModel)
    {
        $this->objModel = $objModel;
    }
    
    /**
     * get module object
     * @return TSysModel
     */
    public function getModel()
    {       
        return $this->objModel;
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
        if (
                ((!$this->objModel->getNew()) && $bAllowedChange) ||
                (($this->objModel->getNew()) && $bAllowedCreate)
                )         
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
        $this->objCancel->setOnclick('window.location.href = \''.addVariableToURL(APP_URLTHISSCRIPT, ACTION_VARIABLE_CANCEL, ACTION_VALUE_CANCEL).'\';');    
        $this->arrCommandPanel[] = $this->objCancel;

        //removed command panel at the top
        $this->objFormGenerator->addArray($this->arrCommandPanel, 'commands_top');
        $this->objFormGenerator->assignCSSClassSection('commands_top', 'div_commandpanel');          
        
        
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
                //checkout system: CHECKIN
                if (!$this->isNewRecord()) //no ID? no need to check in (new records don't have an ID yet)
                {
                    if ($this->objModel->getTableUseCheckout() && $this->getUseCheckinout()) 
                        $this->objModel->checkinNowDB($_GET[ACTION_VARIABLE_ID]);
                }
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
        
        //HANDLE: edit or create
        if (!$this->isNewRecord()) 
        {

            //checkout system: CHECKOUT:
            if (auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE)) //only if you have rights to save: check in. otherwise when saving you need to checkin again
            {        
                if ($this->objModel->getTableUseCheckout() && $this->getUseCheckinout()) 
                    $this->objModel->checkoutNowDB($_GET[ACTION_VARIABLE_ID], $this->sModule.': '.$this->getAuthorisationCategory().': database record edit screen by user: '.$objAuthenticationSystem->getUsers()->getUsername()); //@todo replace by user
            }

            $this->objModel->findID($_GET[ACTION_VARIABLE_ID]); //filters for numeric
            if (!$this->objModel->loadFromDB())
            {
                error_log(__CLASS__.': loadFromDB() error with record id: '.$_GET[ACTION_VARIABLE_ID]);
                sendMessageError(transcms('message_saveload_failed', 'Failed to load record with id '.$_GET[ACTION_VARIABLE_ID]));
            }
                              
        }
        else
            $this->objModel->newRecord();        
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
            error_log('WARNING: $_POST array ('.count($_POST).') is bigger than max_input_vars ('.ini_get('max_input_vars').') in php.ini. Record not saved, user received error');

            //return to return url
            header('Location: '.addVariableToURL($this->sReturnURL, GETARRAYKEY_CMSMESSAGE_ERROR, transcms('message_save_nosuccess_maxinputvars_exceeded', 'RECORD NOT SAVED!!!! Too much data is being sent. Didn\'t save to prevent loss of data integrity')));
            die(); //stop further execution to display the record                
        }
    
        $this->viewToModel(); //--> if there is an error on the form we want to be able to correct it with all the values filled in
            
        if ($this->objFormGenerator->isValid()) //========== SAVE to database =========
        {
            //====> START DB TRANSACTION <====
            if (!$objDBConnection->startTransaction())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'start DB transaction FAILED');
            }

            //==== SAVE PRE
            if ($this->onSavePre())
            {               
                $bSaveSuccess = false;


                //checkout system: check in again
                $this->objModel->resetRecordPointer();
                while ($this->objModel->next())
                {
                    if ($this->objModel->getTableUseCheckout() && $this->getUseCheckinout()) 
                    {
                        $this->objModel->setCheckoutExpires();
                        $this->objModel->setCheckoutSource();
                    }
                }


                //==== SAVE the MODEL
                //save if it is EXISTING record
            
                if (!$this->isNewRecord()) 
                {
                    // if ((!$this->objModel->getNewAll()) && auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE)) ==> removed 17-11-2023
                    if (auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CHANGE))
                        $bSaveSuccess = $this->objModel->saveToDBAll(true, false);
                    else
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'auth() for saving EXISTING record FAILED');
                }
                else //save if it is NEW record
                {                        
                    // if (($this->objModel->getNewAll()) && auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CREATE)) ==> removed 17-11-2023
                    if (auth($this->sModule, $this->getAuthorisationCategory(), TModuleAbstract::PERM_OP_CREATE))
                        $bSaveSuccess = $this->objModel->saveToDBAll(true, false);
                    else
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'auth() for saving NEW record FAILED');
                }


                //==== SAVE POST
                if (!$this->onSavePost($bSaveSuccess)) 
                {
                    $bSaveSuccess = false;
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'onSavePost() FAILED');
                }

                //====> COMMIT/ROLLBACK DB TRANSACTION <====
                if ($bSaveSuccess)
                {
                    //COMMIT DB TRANSACTION
                    if (!$objDBConnection->commit())
                    {
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'commit DB transaction FAILED');
                    }
                }
                else
                {
                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'bSaveSuccess() == false. rolling back DB transaction');

                    //ROLLBACK DB TRANSACTION
                    if (!$objDBConnection->rollback())
                    {
                        logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'commit DB transaction FAILED');
                    }                        
                }                    

                //====handle button presses
                if ($bSaveSuccess)
                {
                    
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
                            $sURL = APP_URLTHISSCRIPT;
                            if (APP_CMS_SAVESUCCESSNOTIFICATION)
                                $sURL = addVariableToURL($sURL, GETARRAYKEY_CMSMESSAGE_SUCCESS, transcms('message_saverecord_success', 'Record save successful'));
                            $sURL = addVariableToURL($sURL, ACTION_VARIABLE_ID, $this->objModel->getID());

                            header('Location: '.$sURL);
                            die(); //stop further execution to display the record                                
                        }

                        //continue displaying the record
                        if (APP_CMS_SAVESUCCESSNOTIFICATION)
                            sendMessageSuccess(transcms('message_saverecord_success', 'Record save successful'));
                    }
                }
                else
                {
                    sendMessageError(transcms('message_saverecord_error', 'Save error: record NOT saved!!'));
                    logError($this->sModule.':'.$this->getAuthorisationCategory(), $this->getAuthorisationCategory().' save error record with id '. $this->objModel->getID());                
                } 
            }//end: onSave()
            else 
            {                    
                logError($this->sModule.':'.$this->getAuthorisationCategory(), $this->getAuthorisationCategory().': onSavePre() failed: save error record with id '. $this->objModel->getID());                
            }
        }//end: isValid()
        else
        {
            sendMessageError(transcms('message_saverecord_inputerror', 'Please correct input error: record NOT saved'));
            logError($this->sModule.':'.$this->getAuthorisationCategory(),': record '. $this->objModel->getID(). ' not saved due to input errors, objFormGenerator->isValid() failed');            
        }

        //====== putting submitted data in the form
    //    $this->objFormGenerator->setSubmittedValuesAsValues();

    }

    /**
     * determine if this is a new record, or editing an existing one
     * 
     * we can see this by looking at the url, if it has an id it will return false, otherwise true
     */
    public function isNewRecord()
    {
        if (isset($_GET[ACTION_VARIABLE_ID]))
        {
            if (is_numeric($_GET[ACTION_VARIABLE_ID]))
                return false;
        }

        return true;
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
     * sometimes you don;t want to user the checkin checkout system, even though the model supports it
     * for example: the settings.
     * The user needs to be able to navigate through the tabsheets, without locking records
     * 
     * ATTENTION: if this method returns true and the model doesn't support it: the checkinout will NOT happen!
     * 
     * @return bool return true if you want to use the check-in/checkout-system
     */
    abstract public function getUseCheckinout();

    /**
     * returns a new model object
     *
     * @return TSysModel
     */
    abstract public function getNewModel();

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
     * returns the url to which the browser returns after closing the detailsave screen
     *
     * @return string
     */
    abstract public function getReturnURL();

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
