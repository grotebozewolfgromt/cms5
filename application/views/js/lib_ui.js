<?php 
/**
 * lib_ui.js
 *
 * UI Javascript library
 * This Javascript file contains the standard javascript for the entire framework, 
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
 * 6 may 2024 lib_global_ui.js created
 * 31 aug 2024 lib_global_ui.js added: checkedToNumeric
 * 2 jan 2025 lib_ui.js rename
 */
?>


/**
 * converts the checked value of a checkbox to a number:
 * checked = 1
 * unchecked = 0
 * 
 * @param {HTMLElement} objCheckbox 
 * @returns {int}
 */
function checkedToNumeric(objCheckbox)
{
    if (objCheckbox)
    {
        if (objCheckbox.checked === true)
            return 1;
        else
            return 0;
    }
    else
    {
        error.log("checkedToNumeric(): Checkbox object is empty");
    }
}



/**
 * copy all children of a <div> to another <div>
 * 
 * it copies ONLY the children, NOT the <div> itself!!
 * (otherwise .cloneNode(true) would suffice)
 * 
 * @param {string} sDivIdSource id of the 
 * @param {string} sDivIdTarget 
 * @param {boolean} bRemoveAllChildrenOfTargetBeforeCopying 
 */
function copyDivContents(sDivIdSource, sDivIdTarget, bRemoveAllChildrenOfTargetBeforeCopying = true)
{
    const objSource = document.getElementById(sDivIdSource);
    let objTarget = document.getElementById(sDivIdTarget);

    //clean target first
    if (bRemoveAllChildrenOfTargetBeforeCopying)
        objTarget.textContent = '';           

    //copying
    for (const objChild of objSource.children) 
    {      
        objTarget.appendChild(objChild.cloneNode(true));
    }
}



/**
 * copy the content of a contenteditable html element to <input type="hidden"> so you can submit it in a form
 */
function copyContentEditableToHidden(sContentEditableID, sHiddenID)
{
    document.getElementById(sHiddenID).value = document.getElementById(sContentEditableID).innerHTML;
    return true;                
}

/**
* open url in new tab 
* for example:
* <div onclick="openInNewTab('www.test.com');">Something To Click On</div>
    */
function openInNewTab(url) 
{
    var win = window.open(url, '_blank');
    win.focus();
}


/**
 * get option of selected html SELECT tag.
 * 
 * @param {type} sel
 * @returns {getSelectedOption.opt}
 */
function getSelectedOption(sel) 
{
    var opt;
    for ( var i = 0, len = sel.options.length; i < len; i++ ) {
        opt = sel.options[i];
        if ( opt.selected === true ) {
            break;
        }
    }
    return opt;
}


/**
 * copy the contents of an editbox to clipboard
 * 
 * @param string sElementID
 * @returns null
 */
function copyToClipboardEditBox(sElementID) 
{
    /* Get the text field */
    var objEditBox = document.getElementById(sElementID);

    if (objEditBox != null)
    {
        /* Select the text field */
        objEditBox.select(); 
        objEditBox.setSelectionRange(0, 99999); /*For mobile devices*/

        /* Copy the text inside the text field */
        document.execCommand("copy");

        /* Alert the copied text */
        alert("Copied to clipboard:\n" + objEditBox.value);
    }
    else
        console.log("COULDNT FIND TEXTBOX ELEMENT WITH ID: " + sElementID);
}     


/**
 * show or hide spinner icon
 * 
 * 2 SVGs are in the same place 
 * 1 is visible, the other not
 * 
 * this function either
 * 1. shows the spinner, hides the normal icon
 * 2. shows the normal icon, hides the spinner
 *
 * @param {string} sIdIconNormal id of the normal icon, leave empty if you don't have a normal icon
 * @param {string} sIdIconSpinner id of the spinner icon
 * * @param {boolean} bShowSpinner show or hide spinner
 */
async function toggleSpinnerIcon(sIdIconNormal = "", sIdIconSpinner, bShowSpinner)
{
    if (sIdIconNormal != "")
        objIconNormal = document.getElementById(sIdIconNormal);
    objIconSpinner = document.getElementById(sIdIconSpinner);

    if (bShowSpinner)
    {
        if (sIdIconNormal != "")
            objIconNormal.style.display = 'none';
        objIconSpinner.style.display = 'inline-block';
    }
    else
    {
        if (sIdIconNormal != "")
            objIconNormal.style.display = 'inline-block';
        objIconSpinner.style.display = 'none';
    }
}



/**
 * removes CSS class from all child elements
 * 
 * @param {string} sClassName CSS class name
 * @param {object} objParentElement for example a <DIV></DIV>
 * @param {string} sTagName html-tag name for example "li" or "div". if empty than not searching for tag name
 * @param {int} iRecursionLevel how many layers of children? 100 = 100, 0=only current level children, -1=unlimited
 * @return {void}
 */
function removeClassFromChildElements(sClassName, objParentElement, sTagName = "", iRecursionLevel = 0)
{   
    // debugger

    if (sTagName != "")
        sTagName = sTagName.toUpperCase(); //.tagName gives uppercase so we convert it already

    if (objParentElement.children) //check if there are children at all
    {
        let iLen = objParentElement.children.length;
        for(let iIndex = 0; iIndex < iLen; iIndex++)
        {
            if ((sTagName == "") || (sTagName == objParentElement.children[iIndex].tagName))
            {
                objParentElement.children[iIndex].classList.remove(sClassName);  
            }

            if (iRecursionLevel > 0)  
                removeClassFromChildElements(sClassName, objParentElement.children[iIndex], sTagName, (iRecursionLevel - 1));
            else if (iRecursionLevel == -1)  
                removeClassFromChildElements(sClassName, objParentElement.children[iIndex], sTagName, -1);    
        }

    }
}

/**
 * removes eventListeners from all child elements
 * 
 * @param {string} sEventListener for example: "click" or "mousedown"
 * @param {object} objParentElement for example a <DIV></DIV>
 * @param {int} iRecursionLevel how many layers of children? 100 = 100, 0=only current level children, -1=unlimited
 * @return {void}
 */
function removeEventListenersFromChildElements(sEventListener, objParentElement, iRecursionLevel = 0)
{
    if (objParentElement.children) //check if there are children at all
    {
        let iLen = objParentElement.children.length;
        for(let iIndex = 0; iIndex < iLen; iIndex++)
        {
            objParentElement.children[iIndex].removeEventListener(sEventListener, objParentElement.children[iIndex]);
            if (iRecursionLevel > 0)  
                removeEventListenersFromChildElements(sEventListener, objParentElement.children[iIndex], (iRecursionLevel - 1));
            else if (iRecursionLevel == -1)  
                removeEventListenersFromChildElements(sEventListener, objParentElement.children[iIndex], -1);
        }
    }


    // for (const objChildUL of objTabTarget.children) 
    // {
    //     for (const objChildLI of objChildUL.children) 
    //     {
    //         objChildLI.removeEventListener("click", objChildLI);
    //     }
    // }
}

/**
 * remove all event listeners by calling AbortController.abort() on objects in array
 * 
 * @param {array} arrAbortControllers 1d associative array with AbortController objects
 */
function removeEventListenersAbortControllerArray(arrAbortControllers)
{
    for (let sKey in arrAbortControllers) 
    {
        arrAbortControllers[sKey].abort();
    }
}

/**
 * copy html tag attributes from one tag to another
 * (this includes id, which must be unique on a page)
 * 
 * @param {HTMLElement} objSource 
 * @param {HTMLElement} objTarget 
 */
function copyAttributesHTMLElement(objSource, objTarget) 
{
    [...objSource.attributes].forEach( attr => { objTarget.setAttribute(attr.nodeName ,attr.nodeValue) })
}


/**
 * copy text to clipboard
 */
async function copyToClipboard(sText)
{
    try 
    {
      await navigator.clipboard.writeText(sText);
      console.log("Copied text to clipboard: '" + sText + "'");
    } 
    catch (a) 
    {
      console.error("Error when trying to use navigator.clipboard.writeText()", a);
    }

}
