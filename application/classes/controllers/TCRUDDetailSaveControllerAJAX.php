<?php
namespace dr\classes\controllers;

/**
 * Description of TFormControllerCMS
 * CMS specific implementation for TCRUDDetailSaveControllerAJAX
 * 
 * Create Read Update (Delete)
 * A controller for a detail & save screen that is saved with AJAX.
 * This controller submits a form via Fetch, and returns a JSON response
 * 
 * This class creates a default structure for these screens to program quickly a detail screen:
 * -TSysModel record loading and saving is implemented by default
 * -handling behaviour of buttonpresses: reloads of the same screen (save button) and closing (save & close button)
 * -authorisation is checked automatically
 * -formgenerator is implemented for quick programming and checking of valid values (is valid int, is valid date, no longer than x chars etc)
 * 
 * You need to inherit this class to use it's functionality
 * 
 * this controller assumes if an id is in the $_POST[ACTION_VARIABLE_ID] (only save) and $_GET[ACTION_VARIABLE_ID] (for everything else) 
 * then it is an EDIT-record, if not an id in $_POST or $_GET, it is a NEW-record
 * 
 * the goal of this class is to keep it lightweight due to OOP performance issues 
 * in PHP, so no parent class (it needs to be as flat as possible when it comes to parent classes depth)
 * 
 * checkin/checkout is implemented leightweight because it can lead to more annoyances than actual safety
 * 
 * JSON RESPONSE with json_encode():
 * 
 * TO CONVERT NON-AJAX ==> AJAX controller:
 * -inherit abstract methods:
 *      initModel()
 *      getUploadSubDirectoryName()
 *      getAuthCreate
 *      getAuthView
 *      getAuthChange
 *      getAuthDelete
 *      onLoadPre
 * -remove showTabs()
 * -onSavePre() returns array instead of boolean
 * -onSavePost() returns array instead of boolean
 * -rename onLoad() to onLoadPost()
 * -getTemplatePath() returns now: return APP_PATH_CMS_TEMPLATES.DIRECTORY_SEPARATOR.'tpl_modeldetailsaveajax.php';
 * -for each field add:
 *      $this->objEditLocale->setOnchange("validateField(this, true)");
 * -$this->getModule() ==> CMS_CURRENTMODULE
 * 
 * 
 * @author drenirie
 * 
 * 13 jan 2025: TCRUDDetailSaveControllerAJAX: created
 */

 use dr\classes\models\TSysModel;

 use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;

abstract class TCRUDDetailSaveControllerAJAX extends TAJAXFormController
{
    protected $objModel = null;
    private $sReturnURL = '';
    protected $bstopHandlingURLParams = false; //stops handling url parameters. Could happen when components (like dr-file-upload) already handled their own stuff.
    

    /**
     * 
     */
    public function __construct()
    {
        global $objLocalisation;  
        
        //WE DO NOT INHERIT THE PARENT!!!! INTENTIONALLY!!!!!
        //parent::__construct();        

        $this->objModel = $this->getNewModel();
        $this->sReturnURL = $this->getReturnURL();

        // includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'style.css');
        includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js');

        if ($objLocalisation)
        {
            $this->sDefaultDateFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT);
            $this->sDefaultTimeFormat = $objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
            $this->sDefaultDateTimeFormat = $objLocalisation->getSetting(TLocalisation::DATEFORMAT_SHORT).' '.$objLocalisation->getSetting(TLocalisation::TIMEFORMAT_SHORT);
        }              


        //on creation
        $this->onCreate();

        //create components for form
        $this->populateParent();

        //stop handling url parameters? ==> could happen when components (like dr-file-upload) already handled their own stuff.
        if ($this->bstopHandlingURLParams)
            return;

        //HANDLE: VALIDATE FIELD
        if (isset($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD]))
        {
            if ($_GET[TAJAXFormController::ACTION_VARIABLE_VALIDATEFIELD] != '') //is cancel button pressed?
            {
                $this->handleValidateField();
                return;
            }
        }

        //HANDLE: CHECK-IN
        if (isset($_GET[ACTION_VARIABLE_CHECKIN]))
        {
            if ($_GET[ACTION_VARIABLE_CHECKIN] != '') //is cancel button pressed?
            {
                $this->handleCheckIn();
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
        else //HANDLE: LOAD record
        {
            $this->onLoadPre();
            $this->handleCreateLoad(); //load and create record
            $this->onLoadPost();
            $this->modelToView();       

            //default: show the page     
            $this->render();            
        }


    }

    /**
     * stops further handling of url parameters
     */
    protected function stopHandlingURLParams()
    {
        $this->bstopHandlingURLParams = true;
    }
    
    /**
     * returns whether to stop handling of url parameters
     */
    protected function getstopHandlingURLParams()
    {
        return $this->bstopHandlingURLParams;
    }

    /**
     * handle check-in
     */
    protected function handleCheckIn()
    {
        //checkout system: CHECKIN
        if (!$this->isNewRecord()) //no ID? no need to check in (new records don't have an ID yet)
        {
            if ($this->getUseCheckinout()) 
                $this->objModel->checkinNowDB($_GET[ACTION_VARIABLE_ID]);
        }
        header('Location: '.$this->sReturnURL);
        die();
    }


    
    /**
     * handle saving
     * returns JSON response to screen
     * 
     * @return bool submit success?
     */
    protected function handleSubmit()
    {
        global $objDBConnection;

        if (!parent::handleSubmit())
            return false;


        // //define default
        // $arrJSONResponse = array(
        //                             TAJAXFormController::JSON_SAVERESPONSE_MESSAGE => '',
        //                             JSONAK_RESPONSE_ERRORCODE => TAJAXFormController::JSONAK_RESPONSE_OK,
        //                             JSONAK_RESPONSE_ERRORCOUNT => 0,
        //                             TAJAXFormController::JSON_SAVERESPONSE_RECORDID => -1,
        //                             JSONAK_RESPONSE_ERRORS => array()
        //                         );


        // //==== DETECT MAX # FIELDS  ====
        // if ((int)ini_get('max_input_vars') ==  count($_POST))  
        // {
        //     logError(__CLASS__.':'.__LINE__, 'WARNING: $_POST array ('.count($_POST).') is bigger than max_input_vars ('.ini_get('max_input_vars').') in php.ini. Record not saved, user received error');

        //     $arrJSONResponse[TAJAXFormController::JSON_SAVERESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_maxinputvars_exceeded', 'RECORD NOT SAVED!!!! Too much data is being sent. Didn\'t save to prevent loss of data integrity');
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_MAXINPUTVARSEXCEEDED;
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;

        //     header(JSONAK_RESPONSE_HEADER);
        //     echo json_encode($arrJSONResponse);               
        //     return; //stop further execution to display the error
        // }
            
        // //==== DETECT CSRF (cross site request forgery)  ====
        // if (!$this->objFormGenerator->isAntiCSRFTokenValid())  
        // {
        //     logError(__CLASS__.':'.__LINE__, 'Cross Site Forgery Request token not valid');

        //     $arrJSONResponse[TAJAXFormController::JSON_SAVERESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_csrftokeninvalid', 'Form verification failed');
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_ANTICSRFTOKENFAILED;
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;

        //     header(JSONAK_RESPONSE_HEADER);
        //     echo json_encode($arrJSONResponse);               
        //     return; //stop further execution to display the error
        // }        

        // //==== DETECT FIELD ERRORS ====
        // $arrErrors = array();
        // $arrErrors = $this->detectErrorSubmit();
        // if (count($arrErrors) > 0) //if NOT empty, errors occured
        // {
        //     $arrJSONResponse[TAJAXFormController::JSON_SAVERESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_inputerror', 'Input error encountered, please correct mistakes');
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_INPUTERROR;
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = count($arrErrors);
        //     $arrJSONResponse[JSONAK_RESPONSE_ERRORS] = $arrErrors;


        //     header(JSONAK_RESPONSE_HEADER);
        //     echo json_encode($arrJSONResponse);            
        //     return;
        // }
 
        //===== LOADING RECORD ======
        //we load record, for 2 reasons:
        //1. so we can overwrite it later with the values from the fields
        //2. if we need to verify ownership with auth(), we can only do that AFTER the record is loaded (because the ownership is stored in the record)
        if (isset($_POST[ACTION_VARIABLE_ID]))
        {
            if (!is_numeric($_POST[ACTION_VARIABLE_ID]))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Record ID "'.$_POST[ACTION_VARIABLE_ID].'" is invalid');

                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_invalidrecordid', 'Invalid record id: "[id]"', 'id', $_POST[ACTION_VARIABLE_ID]);
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_INVALIDRECORDID;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
    
                header(JSONAK_RESPONSE_HEADER);
                echo json_encode($this->arrJSONResponse);               
                return false; //stop further execution to display the error                  
            }

            if (!$this->objModel->loadFromDBByID($_POST[ACTION_VARIABLE_ID]))
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Load of record ID "'.$_POST[ACTION_VARIABLE_ID].'" FAILED');

                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_loadfailed', 'Loading record with id: [id] failed', 'id', $_POST[ACTION_VARIABLE_ID]);
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_LOADFAILED;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
    
                header(JSONAK_RESPONSE_HEADER);
                echo json_encode($this->arrJSONResponse);               
                return false; //stop further execution to display the error                    
            }            
        }


        //===== PERMISSIONS =====
        //we can only now check permissions because we need to load the record first
        if (!$this->getAuthChange())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->getAuthChange() failed for record with ID "'.$_POST[ACTION_VARIABLE_ID].'"');

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_authorisation_notallowed_change', 'You don\'t have permission to save this record');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_AUTHORISATIONFAILED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error            
        }

        //===== PRE SAVE CHECKS =====
        $arrErrors = array();
        $arrErrors = $this->onSavePre();
        if (is_array($arrErrors))
        {
            if (count($arrErrors) > 0)
            {
                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_presaveconditionsfailed', 'An error has occured');
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_PRESAVECONDITIONSFAILED;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = count($arrErrors);
                $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();                
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORS][] = $arrErrors;
    
                header(JSONAK_RESPONSE_HEADER);
                echo json_encode($this->arrJSONResponse);               
                return false; //stop further execution to display the error                     
            }
        }
        else
        {        
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->onSavePre() is not an array. Save FAILED for record with ID "'.$_POST[ACTION_VARIABLE_ID].'"');        

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_onsavepre_notarray', 'Sorry, programming error occured');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_PRESAVENOTARRAY;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error            
        }

        //===== START DATABASE TRANSACTION ====
        if (!$objDBConnection->startTransaction())
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'start DB transaction FAILED');

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_databasetransaction_failed', 'Sorry, save error');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_DB_TRANSACTIONFAILED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error
        }


        //===== ACTUALLY SAVING ======
        $bSaveSuccess = true;
        $this->viewToModel();
        
        $bSaveSuccess = $this->objModel->saveToDBAll(true, false); //===== ACTUAL SAVE HERE!!!!!

        if (!$bSaveSuccess)
        {
            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_failed', 'Sorry, save FAILED!');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_SAVEFAILED;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();                

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error                 
        }

        //===== POST SAVE CHECKS =====
        $arrErrors = array();
        $arrErrors = $this->onSavePost($bSaveSuccess);
        if (is_array($arrErrors))
        {
            if (count($arrErrors) > 0)
            {
                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_nosuccess_postsaveconditionsfailed', 'An error has occured');
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_POSTSAVECONDITIONSFAILED;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = count($arrErrors);
                $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();                
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORS][] = $arrErrors;

                //we don't send the message yet, because we need to rollback the database transaction
            }
        }
        else
        {        
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->onSavePost() is not an array. Save FAILED for record with ID "'.$_POST[ACTION_VARIABLE_ID].'"');        

            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_onsavepost_notarray', 'Sorry, programming error occured');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_POSTPOSTSAVENOTARRAY;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();

            //we don't send the message yet, because we need to rollback the database transaction            
        }



        //====> COMMIT/ROLLBACK DB TRANSACTION <====
        if ($bSaveSuccess)
        {
            //COMMIT DB TRANSACTION
            if (!$objDBConnection->commit())
            {
                $bSaveSuccess = false;                
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'commit DB transaction FAILED');

                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_save_commitfailed', 'Sorry save error occured');
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_DB_COMMITFAILED;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
                $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();  
                
                //we don't send the message yet, because we need to rollback the database transaction                      
            }
        }
        else
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'bSaveSuccess() == false. rolling back DB transaction');

            //ROLLBACK DB TRANSACTION
            if (!$objDBConnection->rollback())
            {
                $bSaveSuccess = false;
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'commit DB transaction FAILED');

                $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_error_save_commitfailed', 'Sorry save error occured');
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = TAJAXFormController::JSON_ERRORCODE_DB_ROLLBACKFAILED;
                $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 1;
                $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();                  
            }                        
        }    


        //==== HANDLE OK MESSAGE ====
        if ($bSaveSuccess)        
        {
            $this->arrJSONResponse[JSONAK_RESPONSE_MESSAGE] = transg('tcruddetailsavecontrollerajax_message_save_ok', 'Save succesful');
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCODE] = JSONAK_RESPONSE_OK;
            $this->arrJSONResponse[JSONAK_RESPONSE_ERRORCOUNT] = 0;
            $this->arrJSONResponse[JSONAK_RESPONSE_RECORDID] = $this->objModel->getID();

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return true; //stop further execution to display the ok message       
        }
        else
        {
            //the error messages are prepared above, now we are going to send them

            header(JSONAK_RESPONSE_HEADER);
            echo json_encode($this->arrJSONResponse);               
            return false; //stop further execution to display the error                   
        }

    }        


    /**
     * load record, if new, then create one
     */
    protected function handleCreateLoad()
    {

        //==== CREATE OR LOAD RECORD
        if ($this->isNewRecord()) //==== CREATE
        {
            //authorisation to create record
            if (!$this->getAuthCreate())
            {
                showAccessDenied(transcms('tcruddetailsavecontrollerajax_message_error_noaccess_viewrecords', 'you don\'t have permission to create a new record'));
                die();
            }

            //save
            $this->objModel->newRecord();
            $this->initModel();
            if ($this->getUseCheckinout()) 
                $this->objModel->checkout($this->getCheckoutSource());   
            $this->objModel->saveToDB(); //create an empty record, so we have always a record
        }
        else // ==== LOAD
        {
            //authorisation to view record
            if (!$this->getAuthView())
            {
                showAccessDenied(transcms('tcruddetailsavecontrollerajax_message_error_noaccess_vieweditrecord', 'you don\'t have permission to view this record'));
                die();            
            }

            //load
            if (!$this->objModel->loadFromDBByID($_GET[ACTION_VARIABLE_ID]))
            {
                error_log(__CLASS__.': loadFromDB() error with record id: '.$_GET[ACTION_VARIABLE_ID]);
                sendMessageError(transg('tcruddetailsavecontrollerajax_message_error_load_failed', 'Failed to load record with id: '.$_GET[ACTION_VARIABLE_ID]));
                return;
            }

            //check if record is checked out
            if ($this->getAuthChange()) //only if you have rights to save: check in. otherwise when saving you need to checkin again
            {        
                if ($this->getUseCheckinout()) 
                {            
                    $objNow = new TDateTime(time());
                    if ($this->objModel->getCheckoutExpires()->isLater($objNow))
                    {
                        error_log(__CLASS__.': loadFromDB() record checked out: '.$_GET[ACTION_VARIABLE_ID].' by '.$this->objModel->getCheckoutSource());
                        showAccessDenied(transg('tcruddetailsavecontrollerajax_message_error_recordcheckedout', 'This record is checked out by "[checkoutsource]".<br>The other user should exit this record before you can view/edit it', 'id', $_GET[ACTION_VARIABLE_ID], 'checkoutsource', $this->objModel->getCheckoutSource()));
                        die();
                    }
                    else //not checked out? Then check-out
                    {
                        $this->objModel->checkoutNowDB($_GET[ACTION_VARIABLE_ID], $this->getCheckoutSource()); 
                    }
                }
            }                    

            //check lock
            if ($this->objModel->getTableUseLock())
            {
                if ($this->objModel->getLocked())
                {
                    error_log(__CLASS__.': loadFromDB() record locked: '.$_GET[ACTION_VARIABLE_ID].' by '.$this->objModel->getLockedSource());
                    showAccessDenied(transg('tcruddetailsavecontrollerajax_message_error_recordlocked', 'Record with id [id] is locked by "[lockedsource]".<br>The other user should unlock this record before you are able to view/edit it.', 'id', $_GET[ACTION_VARIABLE_ID], 'lockedsource', $this->objModel->getLockedSource()));
                    die();
                } 
            }

            //check checksum
            if (!$this->objModel->isChecksumValid())
            {
                logError(__FILE__.' '.__LINE__, 'checksum of record id ('.$_GET[ACTION_VARIABLE_ID].') NOT valid!');
                sendMessageError(transg('tcruddetailsavecontrollerajax_message_error_checksuminvalid', 'Checksum failed for record with ID '.$_GET[ACTION_VARIABLE_ID].'.<br>\nThe contents is changed outside this application and NOT TO BE TRUSTED.<br>\nCheck contents before saving!<br>\nWhen saving a new checksum will be calculated,<br>which re-validates the record'));
            }            
        }


        //if id not exists, treat it as a new record
        if ($this->objModel->count() == 0)
        { 
            //temp store id, so we can produce error message
            $iTempID = 0; 
            $iTempID = $_GET[ACTION_VARIABLE_ID];

            unset($_GET[ACTION_VARIABLE_ID]);
            $this->objModel->saveToDB(); //create an empty record, so we have always a record    
            logError(__FILE__.' '.__LINE__, 'record id ('.$iTempID.') doesnt exist. treat as new record');
            sendMessageError(transg('tcruddetailsavecontrollerajax_message_error_recorddoesnotexist', 'Record with ID '.$iTempID.' does not exist. Created new one'));
        }
    }   
    
    
    /**
     * handle cancel button click
     */
    protected function handleCancel()
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
    

    /**
     * render shissle to screen
     *
     * @param $arrVars extra variables to add to the render (you can call this method in one of the child classes)
     * @return void
     */
    public function render($arrVars = array())
    {
        includeJS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'ajaxform.js');
        
        $arrVars['sUploadSubDirectoryName'] = $this->getUploadDirWithID();
        $arrVars['iRecordID'] = $this->objModel->getID();       
        parent::render($arrVars);
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
     * 
     * 
     * @return TSysModel
     */
    public function getModel()
    {       
        return $this->objModel;
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

        if (isset($_POST[ACTION_VARIABLE_ID]))
        {
            if (is_numeric($_POST[ACTION_VARIABLE_ID]))
                return false;
        }

        return true;
    }
   
    /**
     * returns subdirectory with id.
     */
    public function getUploadDirWithID()
    {
        return generatePrettyURLSafeURL($this->objModel->getID().'-'.$this->getUploadDir());
    }

    /**
     * returns string with subdirectory within module directory for uploadfilemanager
     * it is a directoryname (i.e. 'how-to-tie-a-not'), not a full path (/etc/httpd/... etc)
     * Use this wise for SEO!
     * It does not include a record id
     * 
     * @return string
     */
    abstract public function getUploadDir();

}
