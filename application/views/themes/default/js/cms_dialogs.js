<?php 
/**
 * cms_dialogs.js
 *
 * This Javascript file contains the javascript for dialogs for the CMS 
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
 * 06 jan 2025 cms_dialogs.js created
 */
?>

/**
 * asks if you want to log out
 * 
 * @returns null
 */
function confirmLogout()
{
    /*
    const objDlg = document.getElementById("dlgExitCMS");
    const objBtnX = document.getElementById("btnDialogExitX");
    const objBtnExit = document.getElementById("btnDialogExitDoExit");
    const objBtnStay = document.getElementById("btnDialogExitDoStay");
    const objProgressbar = document.getElementById("progressbar");
    
    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function confirmlogoutxmousedown(e)
    {
        objDlg.close(false);    
        this.removeEventListener("mousedown", confirmlogoutxmousedown);               
    });

    objBtnExit.addEventListener("mousedown", function confirmlogoutexitmousedown(e)
    {
        objDlg.close(true);        
        objProgressbar.classList.add("visible");
        location.href = "<?php 
                            if (isset($objAuthenticationSystem)) //it can be null outside the cms, for example with cronjob or creating an account
                                echo $objAuthenticationSystem->getURLLogOut(); ?>";

        this.removeEventListener("mousedown", confirmlogoutexitmousedown);                              
    });
    
    objBtnStay.addEventListener("mousedown", function confirmlogoutstaymousedown(e)
    {
        objDlg.close(false);
        this.removeEventListener("mousedown", confirmlogoutstaymousedown);             
    });
            
    */

    const objDialog = new DRDialog();
    objDialog.populate();
    objDialog.setTitle("<?php echo transg('cms_dialog_surelogout_title', 'Quit?') ?>");
    objDialog.setBody("<?php echo transcms('cms_dialog_surelogout_body','Want to log out of [applicationname]?', 'applicationname', APP_CMS_APPLICATIONNAME) ?>");


    //cancel button
    const objBtnCancel = document.createElement("button");
    objBtnCancel.innerHTML = "<?php echo transcms('cms_dialog_surelogout_button_stay', 'Stay') ?>";
    objBtnCancel.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnCancel);  
    
    
    //exit button
    const objBtnExecute = document.createElement("button");
    objBtnExecute.innerHTML = "<?php echo transcms('cms_dialog_surelogout_button_exit', 'Log out') ?>";
    objBtnExecute.classList.add("default");
    objBtnExecute.autofocus = true;
    objBtnExecute.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
        location.href = "<?php 
                            if (isset($objAuthenticationSystem)) //it can be null outside the cms, for example with cronjob or creating an account
                                echo $objAuthenticationSystem->getURLLogOut(); ?>";        
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnExecute);


    objDialog.showModal();  
}

/**
 * asks if you want to execute bulk action
 * (old non-ajax screens)
 * 
 * @returns null
 */
function confirmBulkAction(sIDOptionBox)
{
    /*
    const objSelect = document.getElementById(sIDOptionBox);
    const objOption = getSelectedOption(objSelect);
    const objDlg = document.getElementById("dlgBulkActionConfirm");
    const objBtnX = document.getElementById("btnDialogBulkActionConfirmX");
    const objBtnExecute = document.getElementById("btnDialogBulkActionConfirmDoExecute");
    const objBtnCancel = document.getElementById("btnDialogBulkActionConfirmDontExecute");
    const objProgressbar = document.getElementById("progressbar");
    

    objBtnExecute.innerHTML = objOption.value;
    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function confirmbulkactionxmousedown(e)
    {
        objDlg.close(false);      
        this.removeEventListener("mousedown", confirmbulkactionxmousedown);              
    });

    objBtnExecute.addEventListener("mousedown", function confirmbulkactionexecutemousedown(e)
    {
        objDlg.close(true);
        objProgressbar.classList.add("visible");
        document.getElementById('frmBulkActions').submit();
        this.removeEventListener("mousedown", confirmbulkactionexecutemousedown);           
    });
    
    objBtnCancel.addEventListener("mousedown", function confirmbulkactioncancelmousedown(e)
    {
        objDlg.close(false);
        this.removeEventListener("mousedown", confirmbulkactioncancelmousedown);          
    });    
    */
    const objSelect = document.getElementById(sIDOptionBox);
    const objOption = getSelectedOption(objSelect);

    const objDialog = new DRDialog();
    objDialog.populate();
    objDialog.setTitle("<?php echo transg('cms_dialog_bulkaction_title', 'Execute bulk action?') ?>");
    objDialog.setBody("<?php echo transcms('cms_dialog_bulkaction_sureexecute_body','Want to execute bulk-action on selected items?<br>This is permanent and can not be undone.') ?>");


    //cancel button
    const objBtnCancel = document.createElement("button");
    objBtnCancel.innerHTML = "<?php echo transcms('cms_dialog_bulkaction_button_dontexecute', 'Don\'t execute') ?>";
    objBtnCancel.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnCancel);  
    
    
    //execute button
    const objBtnExecute = document.createElement("button");
    objBtnExecute.innerHTML = objOption.value;
    objBtnExecute.classList.add("default");
    objBtnExecute.autofocus = true;
    objBtnExecute.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
        document.getElementById('frmBulkActions').submit();                                

    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnExecute);


    objDialog.showModal();      
}        

/**
 * asks if you want to execute bulk action in modellist_ajax template
 * (improved version of confirmBulkActionAJAX())
 * 
 * @param {HTMLElement} objSource the source that triggered this action
 * @param {string} sIDOptionBox html id of <option>-box
 * @returns null
 */
function confirmBulkActionAJAX(objSource, sIDOptionBox)
{
    /*
    const objSelect = document.getElementById(sIDOptionBox);
    const objOption = getSelectedOption(objSelect);
    const objDlg = document.getElementById("dlgBulkActionConfirm");
    const objBtnX = document.getElementById("btnDialogBulkActionConfirmX");
    const objBtnExecute = document.getElementById("btnDialogBulkActionConfirmDoExecute");
    const objBtnCancel = document.getElementById("btnDialogBulkActionConfirmDontExecute");
    const objProgressbar = document.getElementById("progressbar");

    //if nothing selected: don't show dialog
    if (objOption.value == "")
    {
        console.log("no bulk action selected to execute");
        return;    
    }
    

    objBtnExecute.innerHTML = objOption.value;

    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function confirmbulkactionxmousedown(e)
    {
        objDlg.close(false);      
        this.removeEventListener("mousedown", confirmbulkactionxmousedown);              
    });

    objBtnExecute.addEventListener("mousedown", function confirmbulkactionexecutemousedown(e)
    {
        objDlg.close(true);
        executeBulkAction(objSource);//in modellist.js
        this.removeEventListener("mousedown", confirmbulkactionexecutemousedown);           
    });
    
    objBtnCancel.addEventListener("mousedown", function confirmbulkactioncancelmousedown(e)
    {
        objDlg.close(false);
        this.removeEventListener("mousedown", confirmbulkactioncancelmousedown);          
    });    
    */


    const objSelect = document.getElementById(sIDOptionBox);
    const objOption = getSelectedOption(objSelect);

    const objDialog = new DRDialog();
    objDialog.populate();
    objDialog.setTitle("<?php echo transg('cms_dialog_bulkaction_title', 'Execute bulk action?') ?>");
    objDialog.setBody("<?php echo transcms('cms_dialog_bulkaction_sureexecute_body','Want to execute bulk-action on selected items?<br>This is permanent and can not be undone.') ?>");


    //cancel button
    const objBtnCancel = document.createElement("button");
    objBtnCancel.innerHTML = "<?php echo transcms('cms_dialog_bulkaction_button_dontexecute', 'Don\'t execute') ?>";
    objBtnCancel.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnCancel);  
    
    
    //execute button
    const objBtnExecute = document.createElement("button");
    objBtnExecute.innerHTML = objOption.value;
    objBtnExecute.classList.add("default");
    objBtnExecute.autofocus = true;
    objBtnExecute.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
        executeBulkAction(objSource);//in modellist.js                              
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnExecute);


    objDialog.showModal();    

}        


/**
 * change website
*/
function handleChangeWebsite()
{
    /*
    const objSelect = document.getElementById("selChangeWebsite");
    // const objOption = getSelectedOption(objSelect);
    const objDlg = document.getElementById("dlgChangeWebsiteConfirm");
    const objDlgBody = document.getElementById("dlgChangeWebsiteConfirmBody");
    const objBtnX = document.getElementById("btnDialogChangeWebsiteConfirmX");
    const objBtnChange = document.getElementById("btnDialogChangeWebsiteDoChange");
    const objBtnCancel = document.getElementById("btnDialogChangeWebsiteDontChange");
    const objProgressbar = document.getElementById("progressbar");
    

    // var bChange;
    // bChange = window.confirm("<?php echo transcms('skin_loggedin_message_surechangewebsite','Do you want to change website? Unsaved progress will be lost') ?>");
    // if (bChange)
    // {
    //     location.href = addVariableToURL("<?php echo getURLCMSDashboard(); ?>", "<?php echo GETARRAYKEY_SELECTEDSITEID; ?>", iID);
    // }


    objDlgBody.innerHTML = "<?php echo transcms('cms_dialog_changewebsite_sureexecute_body','Do you want to change to website:') ?><br>" + objSelect.options[objSelect.selectedIndex].text + "?<br>Unsaved progress will be lost.";
    
    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function changewebsitexmousedown(e)
    {
        objDlg.close(false);    
        this.removeEventListener("mousedown", changewebsitexmousedown);            
    });

    objBtnChange.addEventListener("mousedown", function changewebsitechangemousedown(e)
    {
        objDlg.close(true);
        objProgressbar.classList.add("visible");
        location.href = addVariableToURL("<?php echo getURLCMSDashboard(); ?>", "<?php echo GETARRAYKEY_SELECTEDSITEID; ?>", objSelect.value);
        this.removeEventListener("mousedown", changewebsitechangemousedown);
    });
    
    objBtnCancel.addEventListener("mousedown", function changewebsitecancelmousedown(e) 
    {
        objDlg.close(false);
        this.removeEventListener("mousedown", changewebsitecancelmousedown); 
    });    
    */
    const objSelect = document.getElementById("selChangeWebsite");

    const objDialog = new DRDialog();
    objDialog.populate();
    objDialog.setTitle("<?php echo transcms('cms_dialog_changewebsite_title', 'Change website?') ?>");
    objDialog.setBody("<?php echo transcms('cms_dialog_changewebsite_sureexecute_body_wanttochange','Do you want to change to website:') ?><br>" + objSelect.options[objSelect.selectedIndex].text + "?<br><br><?php echo transcms('cms_dialog_changewebsite_sureexecute_body_changeslost','WARNING: Unsaved changes will be lost') ?>");


    //cancel button
    const objBtnCancel = document.createElement("button");
    objBtnCancel.innerHTML = "<?php echo transcms('cms_dialog_changewebsite_button_dontchange', 'Don\'t change') ?>";
    objBtnCancel.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnCancel);  
    
    
    //change button
    const objBtnChange = document.createElement("button");
    objBtnChange.innerHTML = "<?php echo transcms('cms_dialog_changewebsite_button_change', 'Change site') ?>";
    objBtnChange.classList.add("default");
    objBtnChange.autofocus = true;
    objBtnChange.addEventListener("mousedown", (objEvent)=>
    {
        objDialog.close();
        location.href = addVariableToURL("<?php echo getURLCMSDashboard(); ?>", "<?php echo GETARRAYKEY_SELECTEDSITEID; ?>", objSelect.value);
    }, { signal: objDialog.getAbortController().signal });  
    objDialog.addButton(objBtnChange);


    objDialog.showModal();  
}


