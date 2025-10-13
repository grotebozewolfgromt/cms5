<?php
namespace dr\classes\controllers;

/**
 * Description of TAJAXFormController
 * 
 * A general controller for html forms that are submitted with AJAX.
 * This can be submitting contact forms or saving stuff to database.
 * 
 * Javascript will do a Fetch() request on submit, 
 * this class reponds with a JSON that Javascript has to interpret back to the user
 * 
 * This class can also check fields individual fields
 * 
 * You can use FormGenerator to create forms.
 * But you don't nessarily have to use FormGenerator, you can also create your own fields and submit
 * 
 * 
 * 
 * OUTPUT:
 * HTML: Load/create
 * JSON: save/validatefield
 * 
 ***********************************************************************
 * WARNING:
 * There is an accompanying ajaxform.js file for this class for the AJAX stuff!
 ***********************************************************************
 * 
 *  
 * @author drenirie
 * 
 * 13 jan 2025: TAJAXFormController: created
 */

use dr\classes\models\TSysModel;

use dr\classes\dom\FormGenerator;    




abstract class TAJAXFormController
{
    protected $arrFormHTMLElements = array();//format: array(objEdtFirstName, objEdtLastName, objEdtPhoneNumber); stores form element objects not used by form generator (used for looping validators). 
    
    protected $objFormGenerator = null;//dr\classes\dom\FormGenerator

    protected $arrJSONResponse = array();

    protected $sDefaultDateFormat = 'm/d/Y'; //default php date format
    protected $sDefaultTimeFormat = 'h:i A'; //default php date format
    protected $sDefaultDateTimeFormat = 'm/d/Y h:i A'; //default php date format

    //==== JSON constants ====

    //fields in JSON save-response:
    //1 per response:
    // const JSON_SAVERESPONSE_MESSAGE                     = 'message'; //general message, like: "Please correct input"
    // const JSON_SAVERESPONSE_ERRORCODE                   = 'errorcode'; //number of the error
    // const JSON_SAVERESPONSE_ERRORCOUNT                  = 'errorcount'; //how many error are there? 0 = good. this is the amount of lines of errors
    // const JSON_SAVERESPONSE_RECORDID                    = 'recordid'; //record id in database
    const JSON_SAVERESPONSE_ANTICSRFTOKEN               = ACTION_VARIABLE_ANTICSRFTOKEN; //Cross Site Request Forgery Token
    // const JSON_SAVERESPONSE_ERRORS                      = 'errors'; //array of specific errors encounted (generally per field basis)
    //0-N per response, included in JSON_SAVERESPONSE_ERRORS array:
    const JSON_SAVERESPONSE_FIELD_HTMLFIELDID           = 'htmlfieldid'; //HTML id of the field
    const JSON_SAVERESPONSE_FIELD_MESSAGE               = 'message'; //exact error message on field-level
    const JSON_SAVERESPONSE_FIELD_FILTEREDFIELDVALUE    = 'filteredfieldvalue'; //corrected field value. i.e. if there were alphabethic characters in a numbers-only field it returns only the numbers
    
    //fields in JSON validate-field-response:
    const JSON_VALIDATERESPONSE_FIELD_HTMLFIELDID           = 'htmlfieldid'; //HTML id of the field
    const JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE    = 'filteredfieldvalue'; //corrected field value. i.e. if there were alphabethic characters in a numbers-only field it returns only the numbers
    const JSON_VALIDATERESPONSE_ERRORS                      = 'errors'; //array of specific errors encounted (generally per field basis)
    //0-N per response, included in JSON_VALIDATERESPONSE_ERRORS array:
    const JSON_VALIDATERESPONSE_FIELD_MESSAGE               = 'message'; //exact error message on field-level
    const JSON_VALIDATEREQUEST_FIELD_OTHERFIELDID           = 'otherfieldid';
    const JSON_VALIDATEREQUEST_FIELD_OTHERFIELDVALUE        = 'otherfieldvalue';

    //JSON error codes
    // const JSONAK_RESPONSE_OK = 0;
    const JSON_ERRORCODE_SAVEFAILED = 1;
    const JSON_ERRORCODE_LOADFAILED = 2;
    const JSON_ERRORCODE_PRESAVECONDITIONSFAILED = 3;
    const JSON_ERRORCODE_PRESAVENOTARRAY = 4;
    const JSON_ERRORCODE_DB_TRANSACTIONFAILED = 5;
    const JSON_ERRORCODE_DB_COMMITFAILED = 6;
    const JSON_ERRORCODE_DB_ROLLBACKFAILED = 7;
    const JSON_ERRORCODE_POSTSAVECONDITIONSFAILED = 8;
    const JSON_ERRORCODE_POSTPOSTSAVENOTARRAY = 9;
    const JSON_ERRORCODE_AUTHORISATIONFAILED = 10;
    const JSON_ERRORCODE_INPUTERROR = 11;
    const JSON_ERRORCODE_MAXINPUTVARSEXCEEDED = 12;
    const JSON_ERRORCODE_INVALIDRECORDID = 13;
    const JSON_ERRORCODE_ANTICSRFTOKENFAILED = 14; //if Cross Site Request Forgery token is not valid
    const JSON_ERRORCODE_FORMFLOODDETECTED = 15; //if submitted too fast
    const JSONAK_RESPONSE_ERRORCODE_UNKNOWN = 999;

    const ACTION_VARIABLE_VALIDATEFIELD = 'validatefield';



    /**
     * 
     */
    public function __construct()
    {

        //on creation
        $this->onCreate();

        //create components for form
        $this->populateParent();

        //HANDLE: VALIDATE FIELD
        if (isset($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD]))
        {
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] != '') //is cancel button pressed?
            {
                $this->handleValidateField();
                return;
            }
        }

        //HANDLE: EXIT/CANCEL
        if (isset($_GET[ACTION_VARIABLE_CANCEL]))
        {
            if ($_GET[ACTION_VARIABLE_CANCEL] == ACTION_VALUE_CANCEL) //is cancel button pressed?
            {
                $this->handleCancel();
                return;
            }
        }

        //HANDLE: SAVE:  the save and die (=Fetch() request in background) 
        if (isset($_GET[ACTION_VARIABLE_SAVE]))
        {   
            if ($_GET[ACTION_VARIABLE_SAVE] == ACTION_VALUE_SAVE)
            {
                $this->viewToModel();
                $this->handleSubmit();
                return;
            }
        }

    }

    /**
     * render shissle to screen
     *
     * @param $arrVars extra variables to add to the render (you can call this method in one of the child classes)
     * @return void
     */
    public function render($arrVars = array())
    {
        //declare
        $sHTMLContentSkinWithTemplate = '';
        $arrAllFormElements = array();

        //init
        $arrAllFormElements = array_merge($this->convertFormGeneratorToHTMLIds(), $this->convertFormHTMLElementsToHTMLIds());


        //variables for the template
        $arrVars['sTitle'] = $this->getTitle();
        $arrVars['sHTMLTitle'] = $arrVars['sTitle'];
        $arrVars['sHTMLMetaDescription'] = $arrVars['sTitle'];
        // $arrVars['objModel'] = $this->getModel();
        // $arrVars['objCRUD'] = $this;
        $arrVars['objFormGenerator'] = $this->getFormGenerator();
        $arrVars['objController'] = $this;
        $arrVars['sReturnURL'] = $this->getReturnURL();
        $arrVars['sSaveURL'] = addVariableToURL(APP_URLTHISSCRIPT, ACTION_VARIABLE_SAVE, ACTION_VALUE_SAVE);
        $arrVars['sValidateFieldURL'] = APP_URLTHISSCRIPT;
        $arrVars['sValidateFieldURLVariable'] = TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD;
        $arrVars['arrFormHTMLElementIds'] = $arrAllFormElements;
        $arrVars['sFormName'] = $this->getFormGenerator()->getForm()->getName();
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
     * detect errors 
     * 
     * alias: checkerrors
     * 
     * @return array if empty = good, otherwise it's the error message
     */
    protected function detectErrorSubmit()
    {
        //declare
        $arrJSONResponse = array();
        $arrFGElements = array(); //FG = Form Generator
        
        //although the code of both arrays (internal + form generator) is the same, the array structure is NOT the same

        //==== INTERNAL ARRAY: call validators
        foreach ($this->arrFormHTMLElements as $objFormElement) 
        {
            $iCountVal = $objFormElement->countValidators();
            for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
            {
                $objValidator = $objFormElement->getValidator($iIndex);
                if (!$objValidator->isValid($objFormElement->getValueSubmitted())) //check validators of fields
                {
                    $arrJSONResponse[] = array
                    (
                        JSONAK_RESPONSE_ERRORCODE => TAJAXFormController::JSON_ERRORCODE_INPUTERROR,
                        JSONAK_RESPONSE_MESSAGE => transg('TAJAXFormController_error_inputerror', 'Input error: [error]', 'error', $objValidator->getErrorMessage()),
                        TAJAXFormController::JSON_SAVERESPONSE_FIELD_HTMLFIELDID => $objFormElement->getID(),
                        TAJAXFormController::JSON_SAVERESPONSE_FIELD_FILTEREDFIELDVALUE => $objValidator->filterValue($objFormElement->getValueSubmitted()),
                    );                       
                }
            }
        }

        //==== FORM GENERATOR: call validators
        $arrFGElements = $this->objFormGenerator->getElements();
        foreach ($arrFGElements as $objFormElement) 
        {
            $iCountVal = $objFormElement->countValidators();
            for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
            {
                $objValidator = $objFormElement->getValidator($iIndex);
                if (!$objValidator->isValid($objFormElement->getValueSubmitted())) //check validators of fields
                {
                    $arrJSONResponse[] = array
                    (
                        JSONAK_RESPONSE_ERRORCODE => TAJAXFormController::JSON_ERRORCODE_INPUTERROR,
                        JSONAK_RESPONSE_MESSAGE => transg('TAJAXFormController_error_inputerror', 'Input error: [error]', 'error', $objValidator->getErrorMessage()),
                        TAJAXFormController::JSON_SAVERESPONSE_FIELD_HTMLFIELDID => $objFormElement->getID(),
                        TAJAXFormController::JSON_SAVERESPONSE_FIELD_FILTEREDFIELDVALUE => $objValidator->filterValue($objFormElement->getValueSubmitted()),
                    );                       
                }
            }
        }

        return $arrJSONResponse;
    }
    

    /**
     * @return array format: array("edtField1", "edtField2");
     */
    protected function convertFormGeneratorToHTMLIds()
    {          
        return array_keys($this->getFormGenerator()->getElements());
    }

    /**
     * @return array format: array("edtField1", "edtField2");
     */
    protected function convertFormHTMLElementsToHTMLIds()
    {   
        $arrFieldIds = array();

        foreach ($this->arrFormHTMLElements as $objHTMLElement)
        {           
            $arrFieldIds[] = $objHTMLElement->getID();
        }

        return $arrFieldIds;
    }
        
    
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
    protected function populateParent()
    {
        $this->objFormGenerator = new FormGenerator('formcontroller', APP_URLTHISSCRIPT);
         
        //populate child class
        $this->populate();
    }
    

  

    /**
     * You can check a field if is valid
     * Then it looks at the validators
     */
    protected function handleValidateField()
    {
        //declare
        $arrErrors = array();
        $arrFGElements = array(); //FG = Form Generator
        $sFilteredValue = '';
        $iCountVal = 0;
        $arrJSONResponse = array();
        
        //although the code of both arrays (internal + form generator) is the same, the array structure is NOT the same

        //==== INTERNAL ARRAY: call validators
        foreach ($this->arrFormHTMLElements as $objFormElement) 
        {
            //only check one field
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] == $objFormElement->getID())
            {
                $iCountVal = $objFormElement->countValidators();
                $sFilteredValue = $objFormElement->getValueSubmitted(); //init value
                for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
                {
                    $objValidator = $objFormElement->getValidator($iIndex);
                    $sFilteredValue = $objValidator->filterValue($sFilteredValue);
                    if (!$objValidator->isValid($sFilteredValue)) //check validators of fields
                    {
                        $arrErrors[] = array
                        (
                            TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_MESSAGE => $objValidator->getErrorMessage(),
                        );       
                    }
                }
            }
        }

        //==== FORM GENERATOR: call validators
        $arrFGElements = $this->objFormGenerator->getElements();
        foreach ($arrFGElements as $objFormElement) 
        {
            //only check one field
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] == $objFormElement->getID())
            {
                $iCountVal = $objFormElement->countValidators();
                $sFilteredValue = $objFormElement->getValueSubmitted(); //init value
                for ($iIndex = 0; $iIndex < $iCountVal; ++$iIndex) 
                {
                    $objValidator = $objFormElement->getValidator($iIndex);
                    $sFilteredValue = $objValidator->filterValue($sFilteredValue);                    
                    if (!$objValidator->isValid($sFilteredValue)) //check validators of fields
                    {
                        $arrErrors[] = array
                        (
                            TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_MESSAGE => $objValidator->getErrorMessage(),
                        );                       
                    }
                }
            }
        }

        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_HTMLFIELDID] = $_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD];
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_FIELD_FILTEREDFIELDVALUE] = $sFilteredValue;                
        $arrJSONResponse[TAJAXFormController::JSON_VALIDATERESPONSE_ERRORS] = $arrErrors;
    
        header(JSONAK_RESPONSE_HEADER);
        echo json_encode($arrJSONResponse);               
        return; //stop further execution to display the error           
    }



   /**
     * handle saving
     * returns JSON response to screen
     * 
     * @return bool submit success?
     */
    protected function handleSubmit()
    {

        //define default
        $this->arrJSONResponse = array(
                                    JSONAK_RESPONSE_MESSAGE => '',
                                    JSONAK_RESPONSE_ERRORCODE => JSONAK_RESPONSE_OK,
                                    JSONAK_RESPONSE_ERRORCOUNT => 0,
                                    JSONAK_RESPONSE_RECORDID => -1,
                                    TAJAXFormController::JSON_SAVERESPONSE_ANTICSRFTOKEN => '',
                                    JSONAK_RESPONSE_ERRORS => array()
                                );


        //==== DETECT MAX # FIELDS  ====
        if ((int)ini_get('max_input_vars') ==  count($_POST))  
        {
            logError(__CLASS__.':'.__LINE__, 'WARNING: $_POST array ('.count($_POST).') is bigger than max_input_vars ('.ini_get('max_input_vars').') in php.ini. Record not saved, user received error');

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_maxinputvars_exceeded', 'RECORD NOT SAVED!!!! Too much data is being sent. Didn\'t save to prevent loss of data integrity');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_MAXINPUTVARSEXCEEDED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error
        }
            

        //==== DETECT CSRF (cross site request forgery)  ====
        if (!$this->objFormGenerator->isAntiCSRFTokenValid())  
        {
            logError(__CLASS__.':'.__LINE__, 'Cross Site Forgery Request token not valid');

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_csrftokeninvalid', 'Form validation failed');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_ANTICSRFTOKENFAILED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error
        }
        else //generate new ACT  (anti cross site request forgery token)
        {
            $this->arrJSONResponse[TAJAXFormController::JSON_SAVERESPONSE_ANTICSRFTOKEN] = $this->objFormGenerator->generateAndRegisterAntiCSRFToken();
        }
            
        //==== DETECT SUBMIT FLOOD ====
        if ($this->objFormGenerator->isFloodDetectedBetweenShowAndSubmitForm() || $this->objFormGenerator->isFloodDetectedBetweenFormSubmits())
        {
            logError(__CLASS__.':'.__LINE__, 'Form submit flood detected');

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_formsubmitflooddetected', 'Form submitted to quickly, please wait a few seconds before trying again');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_FORMFLOODDETECTED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error            
        }            


        //==== DETECT FIELD ERRORS ====
        $arrErrors = array();
        $arrErrors = $this->detectErrorSubmit();
        if (count($arrErrors) > 0) //if NOT empty, errors occured
        {
            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_inputerror', 'Input error encountered, please correct mistakes');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_INPUTERROR;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = count($arrErrors);
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORS] = $arrErrors;

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);            
            return false;
        }

        //no errors found
        return true;
    }        

    /**
     * returns the nice-name of the controller that is using this record + the user name
     * 
     * @return string
     */
    public function getCheckoutSource()
    {
        global $objAuthenticationSystem;
        return $objAuthenticationSystem->getUsers()->getUserName().' on '.getIPAddressClient();
    }    


//=====================================================================================

	// ************************************************
	// ====== ONLY ABSTRACT FUNCTIONS BELOW ==========
	//
	// *   for easy copy/pasting in child classes     *
	// ************************************************
	
//=====================================================================================    
    
 

    /**
     * load record, if new, then create one
     */
    abstract protected function handleCreateLoad();
    
    
    /**
     * handle cancel button click
     */
    abstract protected function handleCancel();


    /**
     * define the fields that are in the detail screen
     * 
     */    
    abstract protected function populate();
 
    /**
     * what is the category that the auth() function uses?
     */
    // abstract protected function getAuthorisationCategory();
    
    /**
     * transfer form elements to database
     */
    abstract protected function viewToModel();
    
    /**
     * transfer database elements to form
     */
    abstract protected function modelToView();

    
    /**
     * is called just before a record is loaded
     */
    abstract public function onLoadPre();


    /**
     * is called after a record is loaded
     */
    abstract public function onLoadPost();
    
    
    /**
     * is called when this controller is created,
     * so you can instantiate classes or initiate values for example 
     */
    abstract public function onCreate();    
    
    /**
     * is called BEFORE a record is saved
     * this method has to send the proper error messages to the user!!
     * 
     * THIS METHOD NEEDS TO RETURN ERROR ARRAY IN THE DEFINED JSON FORMAT (see header class), 
     * OTHERWISE IT WILL NOT SAVE!!
     * 
     * @return array, empty array = no errors
     */
    abstract public function onSavePre();
    
  
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
     * initialise a new model object.
     * This are the initial values
     *
     * @return TSysModel
     */
    abstract public function initModel();

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
    // abstract public function showTabs();    


    /**
     * is this user allowed to create this record?
     * 
     * CRUD: Crud
     */
    abstract public function getAuthCreate();

    /**
     * is this user allowed to view this record
     * 
     * CRUD: cRud
     */
    abstract public function getAuthView();

    /**
     * is this user allowed to update this record
     * 
     * CRUD: crUd
     */
    abstract public function getAuthChange();

    /**
     * is this user allowed to delete this record
     * 
     * CRUD: crUd
     */
    abstract public function getAuthDelete();    

}
