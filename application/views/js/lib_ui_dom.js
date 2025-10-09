<?php 
/**
 * lib_ui_dom.js
 *
 * Javascript library for AJAX stuff, DOM manipulation etc
 * like page loading in <div>s etc, loaded in header
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
 * 7 june 2024 lib_ajax.js created
 */
?>

/**
 * global vars
 */
var arrAjaxLibPageloadQueue = [];//used by: loadPageInHTMLElement(); --> keeps track of the loaded pages in case we need to cancel earlier requests before current

/**
 * load the contents of page into the innerHTML of a HTML element, 
 * most likely a <div>
 *
 * The delay can be useful for handling onkeydown-events when searching: 
 * you wait until the user stopped typing, all requests in the meantime can be ignored
 * you can cancel earlier requests when delay has not passed yet
 * This can be handy when handling onkeydown-events when searching for example
 * 
 * 
 * WARNING:
 * URL must be located on the same domain for security reasons!
 * 
 * @param {string} sURL 
 * @param {string} sDivElementId <div id="poop">
 * @param {bool} bReplaceHTML true=replaces everything inside element, false=adds to element
 * @param {bool} bCancelPreviousRequests true=will ignore previous requests
 * @param {int} iDelayMS delay in Milliseconds
 * @param {string} sLoadingHTML for example: <img src="spinner.gif"> (animate it yourself) or the text "loading ..."
 */
async function loadPageInHTMLElement(sURL, sElementId, bReplaceHTML = true, bCancelPreviousRequests = false, iDelayMS = 0, sLoadingHTML = "")
{   
    if (bCancelPreviousRequests)
        arrAjaxLibPageloadQueue.push(sURL+sElementId); //make it unique


    setTimeout(function()
    {
        const objDiv = document.getElementById(sElementId);

        //cancel
        if (bCancelPreviousRequests)
        {
            const iLenQueue = arrAjaxLibPageloadQueue.length;

            //cancel when more than one in queue
            if (iLenQueue > 1) 
            {
                arrAjaxLibPageloadQueue.shift();
                return;                    
            }
        }

        //continue        
        if (bReplaceHTML)
            if (sLoadingHTML != "")
                objDiv.innerHTML = sLoadingHTML; //show user something is happening before request is finished

        //do request
        const objRequest = new Request(sURL,
        {
            method: "GET",
            credentials: "same-origin"
        });

        //process response
        fetch(objRequest)
        .then((response) => response.text())
        .then((objHTMLRes) => 
        {
            if (bReplaceHTML)
                objDiv.innerHTML = objHTMLRes;
            else
                objDiv.innerHTML+= objHTMLRes;
            
            console.log("Page (" + sURL+ ") loaded in element: " + sElementId);
            arrAjaxLibPageloadQueue.shift();
        })
        .catch((error) => 
        {
            console.warn(error);
        });
    }, iDelayMS); 

    return;
}

/**
 * retrieves the first parent HTMLElement with CSS class sCSSClass
 * This can be the current element!
 * 
 * @param {HTMLElement} objHTMLElement 
 * @param {string} sCSSClass 
 * @param {int} iMaxDepth how far do you want to search down the DOM tree?
 * @return {HTMLElement} objHTMLElementChild returns null if no parent found within iMaxDepth
 */
function getFirstParentElementWithClass(objHTMLElement, sCSSClass, iMaxDepth = 10)
{
    if (iMaxDepth <= 0)
        return null;

    if (objHTMLElement.classList.contains(sCSSClass))
    {
        return objHTMLElement;
    }
    else
    {
        //shouldn't be happening but just in case you reach document root
        if (objHTMLElement.parentElement == null) 
            return null;

        return getFirstParentElementWithClass(objHTMLElement.parentElement, sCSSClass, iMaxDepth-1);
    }
    
    return null; //you should notbe able to get here, but just in case
}


/**
 * returns a new unique id for a HTMLElement
 * 
 * <div id="myid"> returns: <div id="myid-1">
 * <div id="myid-2"> returns: <div id="myid-3">
 * 
 * @param string sCurrentIDInHTMLDocument this is "myid" in <div id="myid">
 * @param int iMaxTries trying to find a unique id this amount of times
 * @returns string new id, will return "" when max amount of tries exceeded
 */
function getNewHTMLId(sCurrentIDInHTMLDocument, iMaxTries = 1000)
{
    const objCurrentElement = document.getElementById(sCurrentIDInHTMLDocument);
    let sNewId = "";
    let iEnumerator = 0;
    let arrIdParts = []; //parts of the id separated by dash (-), including the enumerator
    let arrIdBase = []; //arrIdParts minus the enumerator

    //is not found?
    if (objCurrentElement === null)
    {
        console.warn("getNewHTMLId(): HTML element width id '"+ sCurrentIDInHTMLDocument + "' not found. Returning: '" + getNewHTMLId + "'");
        return sCurrentIDInHTMLDocument;
    }

    //looping to find a unique id
    for (let iIndex = 0; iIndex < iMaxTries; iIndex++)
    {
        arrIdParts = sCurrentIDInHTMLDocument.split("-"); //index 0 is base, index 1 is enumerator  

        //determine the base
        if (arrIdParts.length == 1) //when no enumerator is present
            arrIdBase = arrIdParts;
        else
            arrIdBase = arrIdParts.slice(0, arrIdParts.length - 1); //everything except the last element (=enumerator)

        //determine enumerator
        iEnumerator = parseInt(arrIdParts[arrIdParts.length -1]);
        if (Number.isInteger(iEnumerator))
            iEnumerator++;
        else
            iEnumerator = 1;

        //construct new id
        sNewId = arrIdBase.join("-") + "-" + iEnumerator.toString();

        //check if exists
        if (document.getElementById(sNewId) === null) //not exists, it is unique, thus end function
            return sNewId;
    }

    return ""; //max tries exhausted
}    