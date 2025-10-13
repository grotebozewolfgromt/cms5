<?php 
/**
 * ajaxform.js
 *
 * helper javascript for AJAX for TAJAXFormController class
 * 
 * abbreviation AFC = TAJAXFormController
 * 
 *************************************************************************
 * WARNING:
 *************************************************************************
 * this file uses PHP so it can ONLY be used by including it with PHP!
 * 
 *************************************************************************
 * 
 * @author Dennis Renirie
 * 
 * 16 jan 2024 ajaxform.js created
 */
?>

/**
 * global JS vars
 */
var bDirtyRecord = false; //needs a save?    
var iRecordID = <?php echo $iRecordID ?>; //-1 means: no ID assigned
//var arrFormHTMLElementIds = <?php echo json_encode($arrFormHTMLElementIds) ?>; //array of fields to be submitted when saved
var bIgnoreInput = false; //ignore all input, for example when saving
var bIgnorePageExit = false; //we always want to warn the user
var arrAdditionalFields = []; //associative array where a key-value pair represents htmlfieldid (=key) and value (=value). format: arrAdditionalFields['edtFirstName'] = 'Hank'; 
var objAbortController = new AbortController();
var objAbortControllerForm = new AbortController();

/**
 * attach event listeners
 */
window.addEventListener("beforeunload", onPageAFCExit, { signal: this.objAbortController.signal });
window.addEventListener("load", onPageLoad, { signal: this.objAbortController.signal });

/**
 * prevent user from leaving when page is dirty
 */
function onPageAFCExit(objEvent)
{
    if (bIgnorePageExit)
        return;

    if (bDirtyRecord)
    {
        objEvent.preventDefault();//stop exiting page
    }
    else //exit page
    {
        objAbortController.abort();
        objAbortControllerForm.abort();
    }


};


/**
 * is called when page is fully loaded
 */
function onPageLoad(objEvent)
{
    let objElement = null;
    let sElementValue = "";
    
    //==== attach events to form elements
    // Construct a FormData instance
    const objFormElement = document.getElementsByName("<?php echo $sFormName; ?>")[0];
    const objFormData = new FormData(objFormElement);

    //form elements: loop the key/value pairs
    for (const arrFormEntries of objFormData.entries()) 
    {
        objElement = document.getElementsByName(arrFormEntries[0])[0];
        sElementValue = arrFormEntries[1];

        // console.log(objElement, sElementValue, arrFormEntries);
        if (objElement != null)
        {
            //KEYUP: special treatment text input elements
            switch(objElement.type)
            {
                case "text":
                    console.log("attach keyup", objElement);
                    objElement.addEventListener("keyup", ()=> 
                    {
                        console.log("Auto keyup-event was triggered");
                        setDirtyRecord(); //mark record as dirty
                    }, { signal: this.objAbortControllerForm.signal });          
                    break;
                case "checkbox":
                    //do nothing: we deal with them later
                    //it prevents us from going to default
                    break;
                default:
                    if (objElement.tagName.toLocaleLowerCase() == "textarea") //textarea has no type
                    {
                        console.log("attach keyup", objElement);
                        objElement.addEventListener("keyup", ()=> 
                        {
                            console.log("Auto keyup-event was triggered");
                            setDirtyRecord(); //mark record as dirty
                        }, { signal: this.objAbortControllerForm.signal });   
                    }
                    else //all other elements
                    {
                        objElement.addEventListener("change", ()=> 
                        {
                            console.log("Auto change-event was triggered");
                            setDirtyRecord(); //mark record as dirty
                        }, { signal: this.objAbortControllerForm.signal });
                    }
            }   
        }
        else
            console.warn("onPageLoad(): objElement is null", arrFormEntries[0], arrFormEntries[1], arrFormEntries);
 
    }

    //checkboxes
    //checkboxes only 'exist' in form when they are checked, hence event listeners are not attached when requesting form elements, so we have to add them explicitly
    const arrCheckboxes = [...document.querySelectorAll("input[type='checkbox']")];
    const iLenCheck = arrCheckboxes.length;
    let objClosest = null;
    for (let iIndex = 0; iIndex < iLenCheck; iIndex++)
    {
        objClosest = arrCheckboxes[iIndex].closest("form[name='<?php echo $sFormName; ?>']");
        if (objClosest !== null)
        {
            //CHANGE: checkbox
            arrCheckboxes[iIndex].addEventListener("change", ()=> 
            {
                console.log("checkbox change-event was triggered");
                setDirtyRecord(); //mark record as dirty
            }, { signal: this.objAbortControllerForm.signal });    
        }
    }

    
};



/**
 * when clicked on the exit button 
 * 
 * @param {function} fnCallbackAfterSave callback function that is executed after save is finished
 * @param {DRIconSpinner} objSpinner <dr-icon-spinner> object
*/
function handleExitAFC(fnCallbackAfterSave = null, objSpinner = null)
{
    /*
    const objDlg = document.getElementById("dlgExitAFC");
    const objBtnX = document.getElementById("btnDialogExitAFCX");
    const objBtnExitNoSave = document.getElementById("btnDialogExitAFCDoExitNoSave");
    const objBtnExitSave = document.getElementById("btnDialogExitAFCDoExitSave");
    const objBtnStay = document.getElementById("btnDialogExitAFCDoStay");
    const objProgressbar = document.getElementById("progressbar");


    if (objSpinner !== null)
        objSpinner.start();

    //=== nothing changed? Just exit!
    if (bDirtyRecord == false)
    {
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";

        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHECKIN ?>", "<?php echo ACTION_VALUE_CHECKIN ?>");
        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);

        window.location.href= sURL;

        //no need to stop the spinner, because the page is refreshed anyway
        // if (objSpinner !== null)
        //     objSpinner.stop();    

        return;
    }

    //=== if something changed:
    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function exitpageafcxmousedown(e)
    {
        //stop spinner
        if (objSpinner !== null)
            objSpinner.stop();            

        bIgnorePageExit = false;            
        console.log("pressed X");
        objDlg.close(false);        
        this.removeEventListener("mousedown", exitpageafcxmousedown);          
    });

    objBtnExitNoSave.addEventListener("mousedown", function exitpageafcnosavemousedown(e)
    {
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";
        bIgnorePageExit = true;            

        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHECKIN ?>", "<?php echo ACTION_VALUE_CHECKIN ?>");
        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);

        objDlg.close(true);  
        objProgressbar.classList.add("visible");
        window.location.href = sURL;
        this.removeEventListener("mousedown", exitpageafcnosavemousedown);         
        
        //no need to stop the spinner, because the page is refreshed anyway
        // if (objSpinner !== null)
        //     objSpinner.stop();            
    });

    
    objBtnExitSave.addEventListener("mousedown", function exitafcsavemousedown(e)
    {
        handleSaveAFC(fnCallbackAfterSave, document.querySelector('#btnSave dr-icon-spinner'));  

        objDlg.close(true);  
        objProgressbar.classList.add("visible");        
        this.removeEventListener("mousedown", exitafcsavemousedown);  
        
        //stop spinner
        if (objSpinner !== null)
            objSpinner.stop();            
    });    
    
    
    objBtnStay.addEventListener("mousedown", function exitpageafcstaymousedown(e)
    {
        bIgnorePageExit = false;
        console.log("pressed Stay");
        objDlg.close(false);
        this.removeEventListener("mousedown", exitpageafcstaymousedown);    
        
        //stop spinner
        if (objSpinner !== null)
            objSpinner.stop();                    
    });
    */

    if (objSpinner !== null)
        objSpinner.start();

    //=== nothing changed? Just exit!
    if (bDirtyRecord == false)
    {
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";

        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHECKIN ?>", "<?php echo ACTION_VALUE_CHECKIN ?>");
        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);

        window.location.href= sURL;

        //no need to stop the spinner, because the page is refreshed anyway
        // if (objSpinner !== null)
        //     objSpinner.stop();    

        return;
    }

    //=== if something changed:
    const objDialog = new DRDialog();
    objDialog.populate();
    objDialog.setTitle("<?php echo transcms('cms_dialog_exitajaxformcontroller_title', 'Exit screen') ?>");
    objDialog.setBody("<?php echo transcms('cms_dialog_exitajaxformcontroller_body', 'There are unsaved changes.<br>Are you sure you want to exit this screen?') ?>");


    //cancel button
    const objBtnCancel = document.createElement("button");
    objBtnCancel.innerHTML = "<?php echo transcms('cms_dialog_exitajaxformcontroller_button_stay', 'Stay') ?>";
    objBtnCancel.addEventListener("mousedown", (objEvent)=>
    {
        bIgnorePageExit = false;
        console.log("pressed Stay");
        objDialog.close();
        
        //stop spinner
        if (objSpinner !== null)
            objSpinner.stop();  
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnCancel);  
    
    
    //exit + no save
    const objBtnExitNoSave = document.createElement("button");
    objBtnExitNoSave.innerHTML = "<?php echo transcms('cms_dialog_exitajaxformcontroller_button_exitnosave', 'Exit & DON\'T save') ?>";
    objBtnExitNoSave.addEventListener("mousedown", (objEvent)=>
    {
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";
        bIgnorePageExit = true;            

        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHECKIN ?>", "<?php echo ACTION_VALUE_CHECKIN ?>");
        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);

        objDialog.close();
        window.location.href = sURL;

        //no need to stop the spinner, because the page is refreshed anyway
        // if (objSpinner !== null)
        //     objSpinner.stop();      

    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnExitNoSave);


    //exit + save
    const objBtnExitSave = document.createElement("button");
    objBtnExitSave.innerHTML = "<?php echo transcms('cms_dialog_exitajaxformcontroller_button_exitsave', 'Exit & save') ?>";
    objBtnExitSave.classList.add("default");
    objBtnExitSave.autofocus = true;
    objBtnExitSave.addEventListener("mousedown", (objEvent)=>
    {
        handleSaveAFC(fnCallbackAfterSave, document.querySelector('#btnSave dr-icon-spinner'));  

        objDialog.close(); 
        
        
        //stop spinner
        if (objSpinner !== null)
            objSpinner.stop();   

    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnExitSave);

    objDialog.showModal();      
    
}

/**
 * when clicked on the save button 
 * 
 * @param {function} fnCallbackAfterSave callback function that is executed after save is finished
 * @param {DRIconSpinner} objSpinner spinner-icon object that will be stopped once completed
 **/
async function handleSaveAFC(fnCallbackAfterSave = null, objSpinner = null)
{
    const objSaveButton = document.getElementById("btnSave");
    const objProgressbar = document.getElementById("progressbar");
    // const sIdIconNormal = "svgSaveIcon";
    // const sIdIconSpinner = "svgSaveIconSpinner";


    if (bIgnoreInput === true)
    {
        console.log("Easy cowboy. Still busy ...");
        return;//don't handle save
    }
    bIgnoreInput = true; //temp don't handle new requests

    // toggleSpinnerIcon(sIdIconNormal, sIdIconSpinner, true);
    if (objSpinner)
        objSpinner.start();
    objSaveButton.disabled = true;
    objProgressbar.classList.add("visible");
    console.log("start save");



    // Construct a FormData instance
    const objFormElement = document.getElementsByName("<?php echo $sFormName; ?>")[0];

    const objFormData = new FormData(objFormElement);
    objFormData.append('<?php echo ACTION_VARIABLE_ID; ?>', iRecordID);
    objFormData.append('<?php echo ACTION_VARIABLE_ANTICSRFTOKEN; ?>', document.getElementById('<?php echo ACTION_VARIABLE_ANTICSRFTOKEN; ?>').value);

    // debugger

    //loop form elements
    /* 19-9-2025 verwijdert omdat new FormData(objFormElement) dit vervangt
    let objTempElement = null;
    for (let iIndex = 0; iIndex < arrFormHTMLElementIds.length; iIndex++)
    {
        debugger

        objTempElement = document.getElementById(arrFormHTMLElementIds[iIndex]);
        if (objTempElement)
        {            
            //some boxes like <select> don't have a type
            if (objTempElement.getAttribute('type') === null)
            {
                objFormData.append(arrFormHTMLElementIds[iIndex], objTempElement.value); //get value from element
            }
            else
            {
                //checkboxes and radioboxes always have a value, which does not represent if they are checked
                if ((objTempElement.getAttribute('type').toLowerCase() == 'checkbox') || (objTempElement.getAttribute('type').toLowerCase() == 'radio'))
                {
                    if (objTempElement.checked)
                        objFormData.append(arrFormHTMLElementIds[iIndex], objTempElement.value);
                    else
                        objFormData.append(arrFormHTMLElementIds[iIndex], '');
                }   
                else //get value from element
                {
                    objFormData.append(arrFormHTMLElementIds[iIndex], objTempElement.value);
                }                 
            }
        }
        else
        {
            console.error("handleSaveAFC(): html element with id '" + arrFormHTMLElementIds[iIndex] + "' not found")
        }
    }
    */

    //loop additional data
    if (arrAdditionalFields != null)
    {
        if (Reflect.ownKeys(arrAdditionalFields).length > 0) //associative array, so .length doesn't work
        {
            for(let objField in arrAdditionalFields)
            {
                objFormData.append(objField, arrAdditionalFields[objField]);
            }
        }
    }

    //start request
    const objRequest = new Request("<?php echo addVariableToURL($sSaveURL, ACTION_VARIABLE_RENDERVIEW, ACTION_VALUE_RENDERVIEW_JSONDATA); ?>",
    {
        method: "POST",
        credentials: "same-origin",
        body: objFormData      
    });

    
    fetch(objRequest)
    .then((objResponse) => objResponse.text())
    .then((objResponseText) => 
    {
        // console.log(objResponse);
        <?php 
            if (APP_DEBUGMODE)
                echo "console.log(objResponseText);";
        ?>
        // if (objData.errorcode > 0)
        //debugger
        const objData = JSON.parse(objResponseText);


        //update ACT
        document.getElementById('<?php echo ACTION_VARIABLE_ANTICSRFTOKEN; ?>').value = objData.<?php echo ACTION_VARIABLE_ANTICSRFTOKEN; ?>;

        if (objData.errorcode > 0) //NOT successful
        {
            let objField = null;
            let objFormLine = null;
            let objErrorList = null;
            let objLI = null;


            //restore to normal
            // toggleSpinnerIcon(sIdIconNormal, sIdIconSpinner, false);
            if (objSpinner)
                objSpinner.stop();
            objSaveButton.disabled = false;
            objProgressbar.classList.remove("visible");

            //notify user
            sendNotification("<?php echo transg('detailsavetemplate_error_save_failed_title', 'Save FAILED!!!');?>", objData.message, "error");

            //point user to proper fields
            removeAllInputErrors();


            if (objData.errors) //if fatal error occured, it might not exist
            {
                for (iIndex = 0; iIndex < objData.errors.length; iIndex++)
                {
                    if (objData.errors[iIndex].htmlfieldid)
                    {
                        objField = document.getElementById(objData.errors[iIndex].htmlfieldid);

                        //mark error in form line
                        objFormLine = getFirstParentElementWithClass(objField, "formsection-line", 10);
                        objFormLine.classList.add("formsection-line-error");

                        //display errors
                        objErrorList = objFormLine.getElementsByClassName("formsection-line-errorlist")[0];
                        objLI = document.createElement("li");
                        objLI.appendChild(document.createTextNode(objData.errors[iIndex].message));
                        objErrorList.appendChild(objLI);

                        console.error(objData.errors[iIndex].htmlfieldid + ": " + objData.errors[iIndex].message);
                    }
                    
                }
            }

            //re-enable input
            bIgnoreInput = false;

            return; //no further execution     
        }

        //==== WHEN SUCCESSFUL

        //update id
        iRecordID = objData.recordid;

        //update save states
        bDirtyRecord = false;

        //restore to normal
        // toggleSpinnerIcon(sIdIconNormal, sIdIconSpinner, false);        
        if (objSpinner)
            objSpinner.stop();        
        objSaveButton.disabled = false;
        objProgressbar.classList.remove("visible");

        //notify user
        removeAllInputErrors();
        // sendNotification("<?php echo transg('detailsave_message_save_sucess_title', 'Saving');?>", "<?php echo transm(CMS_CURRENTMODULE, 'detailsave_message_save_sucess_message', 'Your record was saved with id: ');?>"+iRecordID, "notification");        
        <?php 
        //send notifications?
        if (APP_CMS_SAVESUCCESSNOTIFICATION)
        { 
            ?>
                sendNotification("<?php echo transg('detailsave_message_save_sucess_title', 'Saving');?>", "<?php echo transm(CMS_CURRENTMODULE, 'detailsave_message_save_sucess_message', 'Your record was saved with id: ');?>"+iRecordID, "notification");        
            <?php
        }
        ?>
        console.log("save success!");

        //re-enable input
        bIgnoreInput = false;

        //execute callback
        if (fnCallbackAfterSave != null)
            fnCallbackAfterSave();
    })
    .catch((objError) => 
    {
        //restore to normal
        // toggleSpinnerIcon(sIdIconNormal, sIdIconSpinner, false);    
        if (objSpinner)
            objSpinner.stop();    
        objSaveButton.disabled = false;
        objProgressbar.classList.remove("visible");   

        //notify user
        sendNotification("<?php echo transg('detailsave_message_save_fetchrequestfailed', 'Save FAILED!!!');?>", objError.toString() ,"error");
        console.error(objError);

        //re-enable input
        bIgnoreInput = false;        
    });
    
}    

/**
 * will be executed after save has finished
 * (it's a callback function called in fetch promise)
 */
function exitAfterSave()
{
    let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";

    sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHECKIN ?>", "<?php echo ACTION_VALUE_CHECKIN ?>");
    sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);

    console.log("exitAfterSave() -> " + sURL);

    window.location.href=sURL;
}

/**
 * validate field
 * 
 * @param {HTMLElement} objHTMLElement 
 * @param {bool} bReplaceByFilteredValue replaces value in edit box by calling .value on HTML object
 */
function validateField(objHTMLElement, bReplaceByFilteredValue = false)
{
    const sValidateURL = addVariableToURL("<?php echo $sValidateFieldURL; ?>", "<?php echo $sValidateFieldURLVariable; ?>", objHTMLElement.id);

    //just an extra precaution when programmer forgets to add dirty on onkeyup-event
    setDirtyRecord();


    // Construct a FormData instance
    const objFormData = new FormData();
    objFormData.append(objHTMLElement.id, objHTMLElement.value);        


    const objRequest = new Request(sValidateURL,
    {
        method: "POST",
        credentials: "same-origin",
        body: objFormData      
    });



    //===temp
    // fetch(objRequest)
    // .then((response) => response.text())
    // .then((objHTMLRes) => {
    //     alert(objHTMLRes);        
    //     objSaveButton.disabled = false;
    // })
    // .catch((error) => {
    //     console.warn(error);
    // });
    // return;
    //===temp

    
    fetch(objRequest)
    .then((objResponse) => objResponse.json())
    .then((objData) => 
    {
        let objField = null;
        let objFormLine = null;
        let objErrorList = null;
        let objLI = null;
      
        objField = document.getElementById(objData.htmlfieldid);

        removeAllInputErrors();         

        if (objData.errors.length > 0) 
        {               
            for (iIndex = 0; iIndex < objData.errors.length; iIndex++)
            {
                console.error(objHTMLElement.id + ": " + objData.errors[iIndex].message);

                //point user to proper fields
                if (objField)
                {
                    //mark error in form line
                    objFormLine = getFirstParentElementWithClass(objField, "formsection-line", 10);
                    objFormLine.classList.add("formsection-line-error");

                    //display errors
                    objErrorList = objFormLine.getElementsByClassName("formsection-line-errorlist")[0];
                    objLI = document.createElement("li");
                    objLI.appendChild(document.createTextNode(objData.errors[iIndex].message));
                    objErrorList.appendChild(objLI);
                }                        
            }
        }

        //correct input of user
        if (bReplaceByFilteredValue)
        {
            //unescape html (workaround)
            const objTemp = document.createElement('div');
            objTemp.innerHTML = objData.filteredfieldvalue;

            objField.value = objTemp.textContent; //unescaped text
        }

    })
    .catch((objError) => 
    {
        console.error(objError);
    });
}

/**
 * removes all input error CSS classes from elements
 */
function removeAllInputErrors()
{
    // const arrErrFields = document.getElementsByClassName("formsection-line");
    const arrErrFields = document.getElementsByClassName("formsection-line-error");
    const arrErrLists = document.getElementsByClassName("formsection-line-errorlist");
    const iLenFields = arrErrFields.length;
    const iLenLists= arrErrLists.length;

    //form lines
    for (let iIndex = 0; iIndex < iLenFields; iIndex++)
    {        
        arrErrFields[0].classList.remove("formsection-line-error");
    }

    //clear errorlist
    for (let iIndex = 0; iIndex < iLenLists; iIndex++)
    {        
        arrErrLists[iIndex].innerHTML = "";
    }        
}    

/**
 * set document as dirty, so it will ask to save on leave
 */
function setDirtyRecord()
{
    bDirtyRecord = true;
    console.log("set document to dirty");
}

