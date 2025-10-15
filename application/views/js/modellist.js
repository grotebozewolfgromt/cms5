<?php 
/**
 * recordlist.js
 *
 * 
 * @author Dennis Renirie
 * 
 * 7 mrt 2025 recordlist.js created
 */
?>

var objAbortControllerTablePages = null; //AbortController of "previous page" and "next page" in table
var objJSONTable = null;
var objDRDBFilters = null;
var objPaginator = null;



/**
 * BOOT PAGE JS
 * fires when document is loaded
 * // window.onload = (e) => 
 */
window.addEventListener("load", (objEvent)=>
{ 
    //retrieve and push data to table
    objJSONTable = <?php echo $sJSONTable ?>;
    showDataInTable(objJSONTable, true);

    //listen to filters changing
    objDRDBFilters = document.querySelector("dr-db-filters");
    objDRDBFilters.addEventListener("change", (objEvent) => 
    { 

        // console.log("catchieeeeeeeeeeeeeee");
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";
        if (objJSONTable)    
            sURL = objJSONTable.newrequesturl;    

        fetchJSONTable(sURL, true);
    });

    //listen to paginator change
    objPaginator = document.querySelector("dr-paginator");
    objPaginator.addEventListener('change', (objEvent) => 
    { 
        let sURL = "<?php echo APP_URLTHISSCRIPT; ?>";
        if (objJSONTable)    
            sURL = objJSONTable.newrequesturl;

        fetchJSONTable(
            addVariableToURL(sURL, "page", objEvent.detail.page), 
            true);
    });



}, { once: true });




/**
 * retrieves JSON data and builds table
 * 
 * @param {string} sURL url to retrieve data from
 * @param {boolean} bReplaceContent append or replace data in table
 */

function fetchJSONTable(sURL, bReplaceContent = true) 
{
    //build form data to post
    const objFormData = new FormData();

    //add to form:
    var objDBFilters = document.querySelector("dr-db-filters");
    objFormData.append('<?php echo $sVarfilters; ?>', JSON.stringify(objDBFilters.getFiltersAsJSON()));
    // objFormData.append('sortcolumns', JSON.stringify(objDBFilters.getFiltersAsJSON()));

    //prepare URL
    sURL = addVariableToURL(sURL, "<?php echo $sVarRenderView ?>", "<?php echo $sValRenderViewJSONData ?>");    
    //@todo sort order
    //@todo paginator page

    //start request
    const objRequest = new Request(sURL,
    {
        method: "POST",
        credentials: "same-origin",
        body: objFormData      
    });    


    //===temp DEBUG
    // fetch(objRequest)
    // .then((objResponse) => objResponse.text())
    // .then((objHTMLRes) => {
    //     console.log("debuggie result", objHTMLRes);
    //     console.log(JSON.parse(objHTMLRes));
    // })
    // .catch((error) => {
    //     console.error(error);
    // });
    // return;
    //===END: temp DEBUG



    fetch(objRequest)
    .then((objResponse) => objResponse.json())
    .then((objTableData) => 
    {
        //==== NOT successful
        if (objTableData.errorcode > 0) 
        {
            console.error("Error occured:", objTableData.errorcode, objTableData.message);
            sendNotification("Error " + objTableData.errorcode + " occured", objTableData.message ,"error");
            return;
        }

        //==== WHEN SUCCESSFUL
        objJSONTable = objTableData;
        showDataInTable(objTableData, bReplaceContent);        
    })
    .catch((objError) => 
    {
        //notify user
        sendNotification("<?php echo transg('recordlist_message_fetchrequestfailed', 'Retrieving data FAILED!!!');?>", objError.toString() ,"error");
        console.error(objError);
    });
    return;

}

/**
 * shows data in table
 * 
 * @param {Object} JSON object
 * @param {boolean} bReplaceContent append or replace data in table
 */
async function showDataInTable(objJSON, bReplaceContent = true)
{
    const objDivTable = document.querySelector(".overview_table_background");
    const objDRDragDrop = document.querySelector(".dragdroprecordlist"); //<dr-dragdrop> webcomponent object
    let objTable = null;
    let objTHead = null;
    let objTBody = null;
    let objTR = null;
    let objTD = null;
    let objTH = null;
    let objCheckbox = null;
    let iRowCount = 0;
    let iColCount = 0;
    let iColspan = 0;

    
    //==== does data exist?
    if (objJSON === null)
    {
        error.log("objJSON is null. nothing to show");
        return; //nothing to show
    }

    //-- determine colspan (it's different on mobile than on desktop because column count changes)
    iColspan = objJSON.tableheader.length -1;


    //==== create table
    if (objDivTable !== null)
    {
        if (!bReplaceContent) //add to table, then find table
            objTable = document.querySelector(".overview_table_background table");

        
        if (objTable === null) //table not found, then create one
        {
            objTable = document.createElement("table");
            objTable.classList.add("overview_table");
        }    

        //-- table header
        if (bReplaceContent) //only create header when replacing data
        {
            objTHead = document.createElement("thead");
            objTR = document.createElement("tr");

            iColCount = objJSON.tableheader.length;
            for (let iIndex = 0; iIndex < iColCount; iIndex++)
            {
                objTH = document.createElement("th");
                if (objJSON.tableheader[iIndex].cssclass !== "") //can be empty
                    objTH.classList.add(objJSON.tableheader[iIndex].cssclass);
                objTH.innerHTML = objJSON.tableheader[iIndex].value;
                objTR.appendChild(objTH);
            }

            objTHead.appendChild(objTR);
            objTable.appendChild(objTHead);
        }

        //-- table body
        iRowCount = objJSON.tablebody.length;
        if (bReplaceContent) //add to table, then find table
            objTBody = document.createElement("tbody");
        else
            objTBody = document.querySelector(".overview_table_background tbody");

        //make droppable
        objTBody.classList.add("droppable");

        //iterate rows
        for (let iIndex = 0; iIndex < iRowCount; iIndex++)
        {
            objTR = document.createElement("tr");
            objTR.className = objJSON.cssclassrow;
            //objTR.dataset.recordid = "1";

            //iterate columns
            for (let sKey in objJSON.tablebody[iIndex]) 
            {       

                switch (sKey) 
                {
                    case "recordid":
                        objTR.dataset.recordid = objJSON.tablebody[iIndex][sKey];
                        // console.log("recordid found");
                        break;
                    case "randomid":
                        objTR.dataset.randomid = objJSON.tablebody[iIndex][sKey];
                        // console.log("randomid found");
                        break;
                    case "uniqueid":
                        objTR.dataset.uniqueid = objJSON.tablebody[iIndex][sKey];
                        // console.log("uniqueid found");
                        break;
                    default: //not any of the predefined keys, then it is a regular cell with data
                        objTD = document.createElement("td");
                        if (objJSON.tablebody[iIndex][sKey].cssclass !== "")
                            objTD.className = objJSON.tablebody[iIndex][sKey].cssclass;
                   
                        objTD.innerHTML = objJSON.tablebody[iIndex][sKey].value;
                        objTR.appendChild(objTD);
                }

            }
            objTBody.appendChild(objTR);
        }
    
        //no items
        if (iRowCount == 0)
        {
            objTR = document.createElement("tr");
            objTD = document.createElement("td");
            objTD.colSpan = iColspan;
                    
            let objNoRecords = document.createElement("div");
            objNoRecords.className = "norecords";
            objNoRecords.innerHTML = "<?php echo transg('modellist_norecords_message', 'No records found'); ?>"

            objTD.appendChild(objNoRecords);
            objTR.appendChild(objTD);
            objTBody.appendChild(objTR);          
        }    


        //push data to DOM when replacing data (when appending data, the DOM is automatically updated already)
        if (bReplaceContent)
        {
            objDivTable.innerHTML = "";
            objTable.appendChild(objTBody);
            objDivTable.appendChild(objTable);
        }

    }
    else
        error.log("<div> with table not found");


    //==== update "page up" and "page down" in table

    //--abort controller
    if (objAbortControllerTablePages)
        objAbortControllerTablePages.abort(); //clean up previous eventlisteners buttons
    objAbortControllerTablePages = new AbortController();


    //-- table: go to previous page     
    if (!objJSON.paginator.isfirstpage)
    {
        objTR = document.createElement("tr");
        objTD = document.createElement("td");
        objTD.colSpan = iColspan;
                
        let objBtnPrevious = document.createElement("div");
        objBtnPrevious.className = "btnTablePaginatorPage";
        objBtnPrevious.innerHTML = '<dr-icon-spinner><svg fill="currentColor" viewBox="0 -40 120 120" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg></dr-icon-spinner>';
        objBtnPrevious.innerHTML += "&nbsp;<?php echo transg('paginator_table_loadpreviousresults', 'Load previous [x] results', 'x', getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE));?>&nbsp;";
        objBtnPrevious.innerHTML += "(" + objJSON.paginator.previouspagenumber + "/" + objJSON.paginator.pagecount + ") &nbsp;";
        objBtnPrevious.innerHTML += '<dr-icon-spinner><svg fill="currentColor" viewBox="0 -40 120 120" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg></dr-icon-spinner>';

        objBtnPrevious.addEventListener("mousedown", (objEvent)=> 
        {             
            const arrSpinners = objBtnPrevious.querySelectorAll("dr-icon-spinner");
            for (let iIndex = 0; iIndex < arrSpinners.length; iIndex++)
            {
                arrSpinners[iIndex].start();
            }

            //load new data
            fetchJSONTable(addVariableToURL(objJSON.newrequesturl, 'page', objJSON.paginator.previouspagenumber), true);

        }, { signal: objAbortControllerTablePages.signal });            

        objTD.appendChild(objBtnPrevious);
        objTR.appendChild(objTD);
        objTBody.insertBefore(objTR, objTBody.firstChild);        
    }

    //-- table: go to next page     
    if ((!objJSON.paginator.islastpage) && (objJSON.paginator.totalresults > 0))
    {
        objTR = document.createElement("tr");
        objTD = document.createElement("td");
        objTD.colSpan = iColspan;
                
        let objBtnNext = document.createElement("div");
        objBtnNext.className = "btnTablePaginatorPage";
        objBtnNext.innerHTML = '<dr-icon-spinner><svg fill="currentColor" style="transform: rotate(180deg)" viewBox="-20 15 120 120" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg></dr-icon-spinner>';
        objBtnNext.innerHTML += "&nbsp;<?php echo transg('paginator_table_loadnextresults', 'Load next [x] results', 'x', getSetting(SETTINGS_MODULE_CMS, SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE));?>&nbsp;";
        objBtnNext.innerHTML += "(" + objJSON.paginator.nextpagenumber + "/" + objJSON.paginator.pagecount + ") &nbsp;";
        objBtnNext.innerHTML += '<dr-icon-spinner><svg fill="currentColor" style="transform: rotate(180deg)" viewBox="-20 15 120 120" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg></dr-icon-spinner>';


        objBtnNext.addEventListener("mousedown", (objEvent)=> 
        {             
            const arrSpinners = objBtnNext.querySelectorAll("dr-icon-spinner");
            for (let iIndex = 0; iIndex < arrSpinners.length; iIndex++)
            {
                arrSpinners[iIndex].start();
            }

            //load new data
            fetchJSONTable(addVariableToURL(objJSON.newrequesturl, 'page', objJSON.paginator.nextpagenumber), true);

        }, { signal: objAbortControllerTablePages.signal });    

        objTD.appendChild(objBtnNext);
        objTR.appendChild(objTD);
        objTBody.appendChild(objTR);        
    }


    //==== update paginator
    let objPaginator = document.querySelector("dr-paginator");
    if (objPaginator)
    {
        objPaginator.previouspagenumber = objJSON.paginator.previouspagenumber;
        objPaginator.nextpagenumber = objJSON.paginator.nextpagenumber;
        objPaginator.currentpage = objJSON.paginator.currentpage;
        objPaginator.totalresults = objJSON.paginator.totalresults;
        objPaginator.recordsperpage = objJSON.paginator.recordsperpage;
        objPaginator.pagecount = objJSON.paginator.pagecount;
        objPaginator.updateUI();
    }
    else
        console.warn("showDataInTable(): objPaginator is null");


    //==== update drag drop
    if (objDRDragDrop)
        objDRDragDrop.reloadDraggablesDroppables();
    else
        console.warn("showDataInTable(): objDRDragDrop is null");


}


/**
 * when clicked in the button "New" to create a new record
 * @param {HTMLElement} objSource
 */
function onNewRecordClick(objSource)
{
    const objSpinner = objSource.querySelector("dr-icon-spinner");
    objSpinner.start();

    window.location.href = '<?php echo $sURLDetailPage ?>';
}

/**
 * when clicked in the icon "Edit" to edit an existing record
 */
function onEditRecordClick(objSource, sURL)
{
    objSource.parentElement.start(); //activate spinner
    window.location.href = sURL;
}

/**
 * change order of 1 record with up/down arrows
 * (old method)
 * 
 * @param {integer} iID 
 * @param {boolean} bUp up or down. up = true, down = false; 
 * @param {string} sSortOrder '', DESC or ASC
 */
function changeOrderRecord(objSource, sURL)
{
    objSource.parentElement.start(); //activate spinner

    // sURL = "<?php echo APP_URLTHISSCRIPT; ?>";
    // sURL = addVariableToURL(sURL, "<?php echo $sVarRenderView; ?>", "<?php echo $sValRenderViewJSONData; ?>");
    // sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID; ?>", iID);
    // sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ORDERONEUPDOWN; ?>", "<?php echo ACTION_VALUE_ORDERONEUPDOWN ?>");
    // if (bUp)
    //     sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ORDERONEUP; ?>", "<?php echo ACTION_VALUE_ORDERONEUP ?>");
    // else
    //     sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ORDERONEUP; ?>", "<?php echo ACTION_VALUE_ORDERONEDOWN ?>");
    // sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_SORTORDER; ?>", sSortOrder);

    fetchJSONTable(sURL, true);
}


/**
 * change the sort order of all records: either ASCENDING or DESCENDING
 * 
 * @param {HTMLElement} objSource element that clicked
 * @param {string} sURL 
 */
function changeSortOrder(objSource, sURL)
{
    const objSpinner = objSource.parentElement.querySelector("dr-icon-spinner");
    
    if (objSpinner !== null)
        objSpinner.start();

    fetchJSONTable(sURL, true);
}

/**
 * executes a bulk action
 * (called from confirmation dialog in cms_dialogs.js)
 * 
 * @param {HTMLElement} objSource element that clicked
 */
function executeBulkAction(objSource)
{
    const objSpinner = objSource.querySelector("dr-icon-spinner");

    if (objSpinner !== null)
        objSpinner.start();


    //build form data to post
    const objFormData = new FormData();

    //add to form: checkboxes
    const arrCheckboxes = document.getElementsByName("chkRecordID[]");
    if (arrCheckboxes !== null)
    {
        for (let iIndex = 0; iIndex < arrCheckboxes.length; iIndex++)
        {
            if (arrCheckboxes[iIndex].checked == true)
                objFormData.append('chkRecordID[]', arrCheckboxes[iIndex].value);
        }
    }

    //add to form: action to take
    const objBulkActionSelect = document.getElementsByName("<?php echo BULKACTION_VARIABLE_SELECT_ACTION ?>");
    objFormData.append('<?php echo BULKACTION_VARIABLE_SELECT_ACTION ?>', objBulkActionSelect[0].value);

    //prepare URL
    sURL = "<?php echo APP_URLTHISSCRIPT; ?>";    
    sURL = addVariableToURL(sURL, "<?php echo $sVarRenderView ?>", "<?php echo $sValRenderViewJSONData ?>");    
    

    //start request
    const objRequest = new Request(sURL,
    {
        method: "POST",
        credentials: "same-origin",
        body: objFormData      
    });    


    //===temp DEBUG
    // fetch(objRequest)
    // .then((objResponse) => objResponse.text())
    // .then((objHTMLRes) => {
    //     console.log("debuggie result", objHTMLRes);
    //     console.log(JSON.parse(objHTMLRes));
    // })
    // .catch((error) => {
    //     console.error(error);
    // });
    // return;
    //===END: temp DEBUG



    fetch(objRequest)
    .then((objResponse) => objResponse.json())
    .then((objTableData) => 
    {
        //==== NOT successful
        if (objTableData.errorcode > 0) 
        {
            console.error("Error occured:", objTableData.errorcode, objTableData.message);
            sendNotification("Error " + objTableData.errorcode + " occured", objTableData.message ,"error");
            return;
        }

        //==== WHEN SUCCESSFUL
        showDataInTable(objTableData, true);        
        objSpinner.stop();
    })
    .catch((objError) => 
    {
        objSpinner.stop();

        //notify user
        sendNotification("<?php echo transg('recordlist_message_fetchrequestfailed', 'Retrieving data FAILED!!!');?>", objError.toString() ,"error");
        console.error(objError);
        
    });
    return;    
    
}

//==== DRAG AND DROP rows to change db record position ====
let bBusyDropping = false;//boolean to track if database action is currently executing. This to prevent a race condition error with promise

/**
 * Fires when element dropped in record table.
 * This function manipulates the data
 */    
window.addEventListener("load", (objEvent)=>
{ 
    const objDRDragDrop = document.querySelector(".dragdroprecordlist"); //<dr-drag-drop> webcomponent object

    if (objDRDragDrop)
    {
        objDRDragDrop.addEventListener('dr-drop', (objEvent) => 
        { 
            if (objEvent.detail.draggingElements[0].dataset.recordid === undefined)
            {
                sendNotification("Error occured", "Can not change position in database: record id not found on dragging element" ,"error");
                console.error("Can not change position in database: record id not found on dragging element");
                return;
            }

            //preventing race condition
            if (bBusyDropping)
            {
                sendNotification("Error occured", "Still executing previous drop, please wait.<br>Current drop is NOT executed in database" ,"error");
                console.error("Can not change position in database: Dropping still executing, please wait. Current drop is NOT executed in database, while the UI is updated");
                return;
            }
            else
                bBusyDropping = true;

            //activate spinners
            const objSpinner = objEvent.detail.draggingElements[0].querySelector("dr-icon-spinner"); //take the first spinner
            if (objSpinner)
                objSpinner.start();



            //prepare URL
            sURL = "<?php echo APP_URLTHISSCRIPT; ?>";    
            sURL = addVariableToURL(sURL, "<?php echo $sVarRenderView ?>", "<?php echo $sValRenderViewJSONData ?>");    
            sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHANGEPOSITION; ?>", "<?php echo ACTION_VALUE_CHANGEPOSITION; ?>");
            sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID; ?>", objEvent.detail.draggingElements[0].dataset.recordid); //temp: only take the first item of all dragged elements


            //dropped ON element
            if (objEvent.detail.dropOnElement !== null)
            {
                console.log("dropppedddddddd1111 on element in modellist.js", objEvent.detail.dropOnElement, objEvent.detail.dropOnElement.dataset.recordid);
                sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHANGEPOSITION_ONID; ?>", objEvent.detail.dropOnElement.dataset.recordid);
            }
            else //dropped AFTER element
            { 

                //drop at beginning
                if (objEvent.detail.dropAfterElement === null)
                {
                    sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHANGEPOSITION_AFTERID; ?>", "0");
                    // console.log("dropppedddddddd1111 BEGINNING in modellist.js", objEvent.detail.dropAfterElement);
                }
                else //drop NOT at the beginning
                {
                    sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_CHANGEPOSITION_AFTERID; ?>", objEvent.detail.dropAfterElement.dataset.recordid);
                    // console.log("dropppedddddddd1111 AFTER element in modellist.js", objEvent.detail.dropAfterElement, objEvent.detail.dropAfterElement.dataset.recordid);
                }
            }

            //===== DATABASE REQUEST ====

            //start request
            const objRequest = new Request(sURL,
            {
                method: "POST",
                credentials: "same-origin"     
            });    

            // console.log("url request change position", sURL);

            //===temp DEBUG
            // fetch(objRequest)
            // .then((objResponse) => objResponse.text())
            // .then((objHTMLRes) => {
            //     console.log("debuggie result", objHTMLRes);
            //     console.log(JSON.parse(objHTMLRes));
            // })
            // .catch((error) => {
            //     console.error(error);
            // });
            // return;
            //===END: temp DEBUG


            fetch(objRequest)
            .then((objResponse) => objResponse.json())
            .then((objTableData) => 
            {
                //==== NOT successful
                if (objTableData.errorcode > 0) 
                {
                    console.error("Error occured:", objTableData.errorcode, objTableData.message);
                    sendNotification("Error " + objTableData.errorcode + " occured", objTableData.message ,"error");

                    if (objTableData.haltonerror == true)
                        return;
                }

                //==== WHEN SUCCESSFUL
                showDataInTable(objTableData, true);        
            })
            .catch((objError) => 
            {
                //notify user
                sendNotification("<?php echo transg('recordlist_message_fetchrequestfailed', 'Retrieving data FAILED!!!');?>", objError.toString() ,"error");
                console.error(objError);   
            })
            .finally(() => 
            {
                bBusyDropping = false; //reset boolean to prevent race condition
                if (objSpinner)
                    objSpinner.stop();
            });            
            
        });
    }
}, { once: true });



