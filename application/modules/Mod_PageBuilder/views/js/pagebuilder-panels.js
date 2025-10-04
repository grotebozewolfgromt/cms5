
/**
 * pagebuilder-panels.js
 *
 * This Javascript code regards to hiding and showing panels desktop, menus etc
 * 
 * Panels are:
 * -desktop new
 * -desktop details
 * -mobile new
 * -mobile details
 * -add-panel at bottom of content
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
 * 3 apr 2024 pagebuilder-panels.js created
 */

/**
 * global vars
 */

const sNewPanelWidth = "250px"; //--> is also set in CSS!!
const sDetailPanelWidth = "250px"; //--> is also set in CSS!!
const sNewPanelDesktopVisibleCSSClass = "panelNewDesktopVisible";
const sDetailsPanelDesktopVisibleCSSClass = "panelDetailsDesktopVisible";
const sNewPanelMobileVisibleCSSClass = "panelNewMobileVisible";
const sDetailsPanelMobileVisibleCSSClass = "panelDetailsMobileVisible";
const sPanelDesktopNotVisible = "panelDesktopNotVisible";
const sPanelMobileNotVisibleCSSClass = "panelMobileNotVisible";

/**
 * initialize panels
 */
function initPanels()
{  
    //add designobjects to add-pabel in the middle of the screen
    objAddFavorites = document.getElementById('panel-add-designobjects');
    createDivDesignObjectGrid(objAddFavorites, "favorites");
}

/**
 * switch on/off the left panel with new elements for desktop
 */
function toggleNewPanelDesktop()
{
    var objDivNewDesktop = document.getElementsByClassName('pagecolumn-panel-new-desktop')[0];

    if ((objDivNewDesktop.classList.contains(sNewPanelDesktopVisibleCSSClass)))
    {
        //make not-visible
        objDivNewDesktop.classList.remove(sNewPanelDesktopVisibleCSSClass);
        objDivNewDesktop.classList.add(sPanelDesktopNotVisible);
    }          
    else
    {
        //make visible
        objDivNewDesktop.classList.add(sNewPanelDesktopVisibleCSSClass);
        objDivNewDesktop.classList.remove(sPanelDesktopNotVisible);
    }


    updateButtonStatesPanels();
}

/**
 * switch on/off the right panel with details for desktop
 */        
function toggleDetailsPanelDesktop()
{
    var objDivDetailsDesktop = document.getElementsByClassName('pagecolumn-panel-details-desktop')[0];
    var objDivDetailsDocumentDesktop = document.getElementById('tab-content-details-document-desktop');
    var objDivDetailsDocumentMobile = document.getElementById('tab-content-details-document-mobile');

    if ((objDivDetailsDesktop.classList.contains(sDetailsPanelDesktopVisibleCSSClass)))
    {
        //=== make not-visible
        objDivDetailsDesktop.classList.remove(sDetailsPanelDesktopVisibleCSSClass);
        objDivDetailsDesktop.classList.add(sPanelDesktopNotVisible);
    }          
    else
    {
        //=== make visible
        objDivDetailsDesktop.classList.add(sDetailsPanelDesktopVisibleCSSClass);
        objDivDetailsDesktop.classList.remove(sPanelDesktopNotVisible);

        //=== move desktop tabs to mobile
        if (objDivDetailsDocumentDesktop.innerHTML.trim() == "")
        {
            objDivDetailsDocumentDesktop.innerHTML = objDivDetailsDocumentMobile.innerHTML;
            objDivDetailsDocumentMobile.innerHTML = "";
        }        
    }

    updateButtonStatesPanels();
}   

/**
 * switch on/off the fullscreen panel with new elements on mobile
 */
function toggleNewPanelMobile()
{
    var objDivNewMobile = document.getElementsByClassName('panel-new-mobile')[0];
    var objDivDetailsMobile = document.getElementsByClassName('panel-details-mobile')[0];

    if ((objDivNewMobile.classList.contains(sNewPanelMobileVisibleCSSClass)))
    {
        //=== make not-visible
        objDivNewMobile.classList.remove(sNewPanelMobileVisibleCSSClass);
        objDivNewMobile.classList.add(sPanelMobileNotVisibleCSSClass);
    }          
    else
    {
        //=== make visible

        //check existence details panel and remove it if it does
        if ((objDivDetailsMobile.classList.contains(sDetailsPanelMobileVisibleCSSClass)))
            toggleDetailsPanelMobile();

        //show the new panel
        objDivNewMobile.classList.add(sNewPanelMobileVisibleCSSClass);
        objDivNewMobile.classList.remove(sPanelMobileNotVisibleCSSClass);
    }    

    updateButtonStatesPanels();
}


/**
 * switch on/off the fullscreen panel with details on mobile
 */        
function toggleDetailsPanelMobile()
{            
    var objDivNewMobile = document.getElementsByClassName('panel-new-mobile')[0];
    var objDivDetailsMobile = document.getElementsByClassName('panel-details-mobile')[0];
    var objDivDetailsDocumentDesktop = document.getElementById('tab-content-details-document-desktop');
    var objDivDetailsDocumentMobile = document.getElementById('tab-content-details-document-mobile');

    if ((objDivDetailsMobile.classList.contains(sDetailsPanelMobileVisibleCSSClass)))
    {
        //=== make not-visible
        objDivDetailsMobile.classList.remove(sDetailsPanelMobileVisibleCSSClass);
        objDivDetailsMobile.classList.add(sPanelMobileNotVisibleCSSClass);
    }          
    else
    {
        //=== make visible

        //check existence new panel and remove it if it does
        if ((objDivNewMobile.classList.contains(sNewPanelMobileVisibleCSSClass)))
            toggleNewPanelMobile();

        //show the details panel
        objDivDetailsMobile.classList.add(sDetailsPanelMobileVisibleCSSClass);
        objDivDetailsMobile.classList.remove(sPanelMobileNotVisibleCSSClass);

        //=== move desktop tabs to mobile
        if (objDivDetailsDocumentMobile.innerHTML.trim() == "")
        {
            objDivDetailsDocumentMobile.innerHTML = objDivDetailsDocumentDesktop.innerHTML;
            objDivDetailsDocumentDesktop.innerHTML = "";
        }

    }        

    updateButtonStatesPanels();
}       


/**
 * updates buttons states of panels
 */
function updateButtonStatesPanels()
{
    var objDivNewDesktop = document.getElementsByClassName('pagecolumn-panel-new-desktop')[0];
    var objBtnNewDesktop = document.getElementById('btnNewDesktop');

    var objDivDetailsDesktop = document.getElementsByClassName('pagecolumn-panel-details-desktop')[0];
    var objBtnDetailsDesktop = document.getElementById('btnDetailsDesktop');

    var objDivNewMobile = document.getElementsByClassName('panel-new-mobile')[0];
    var objBtnNewMobile = document.getElementById('btnNewMobile');

    var objDivDetailsMobile = document.getElementsByClassName('panel-details-mobile')[0];
    var objBtnDetailsMobile = document.getElementById('btnDetailsMobile');

    //new panel desktop showing?            
    if (objDivNewDesktop.classList.contains(sNewPanelDesktopVisibleCSSClass))
        objBtnNewDesktop.classList.add("pressed");
    else
        objBtnNewDesktop.classList.remove("pressed");

    //details panel desktop showing?
    if (objDivDetailsDesktop.classList.contains(sDetailsPanelDesktopVisibleCSSClass))
        objBtnDetailsDesktop.classList.add("pressed");
    else
        objBtnDetailsDesktop.classList.remove("pressed");

    //new panel mobile showing?
    if (objDivNewMobile.classList.contains(sNewPanelMobileVisibleCSSClass))
        objBtnNewMobile.classList.add("pressed");
    else
        objBtnNewMobile.classList.remove("pressed");        
    
    //details panel mobile showing?
    if (objDivDetailsMobile.classList.contains(sDetailsPanelMobileVisibleCSSClass))
        objBtnDetailsMobile.classList.add("pressed");
    else
        objBtnDetailsMobile.classList.remove("pressed");     
}



/**
 * responds to a click on a design object in the inspector
 * (one of the panels, not on the design canvas)
 * 
 * @param {string} sClassNameDesignObject 
 * @param {div} objSender
 */
function designObjectClickInInspector(objSender)
{
    const objAllDesignObjects = document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_CREATETAB);
    const iSizeDesignObjects = objAllDesignObjects.length; //we cache length because there are a lot, so we dont have to call .length on each loop iteration

    if (!objSender)
    {
        console.log("designObjectClickInInspector(): design object not found");
        return;
    }

    //remove selected classes from all objects
    for (let iIndex = 0; iIndex < iSizeDesignObjects; iIndex++)
        objAllDesignObjects[iIndex].classList.remove("selected");

    //add selected class to objSender
    objSender.classList.add("selected");    
}

/**
 * search for designobjects
 * 
 * @param {HTMLElement} objSender editbox that the user the seach command
 */
async function searchForDesignObjects(objSearchBox)
{
    const objAllDesignObjects = document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_CREATETAB);
    const iSizeDesignObjects = objAllDesignObjects.length; //we cache length because there are a lot, so we dont have to call .length on each loop iteration
    let sSearchTags = "";
    let arrSearchTags = [];
    let iSizeSearchTags = 0; //we cache length because there can be a lot, so we dont have to call .length on each loop iteration
    let iPos = 0;

    //don't even try to search (this is faster than searching and finding nothing)
    if (objSearchBox.value == "")
    {
        for (let iIndexObjects = 0; iIndexObjects < iSizeDesignObjects; iIndexObjects++)
            objAllDesignObjects[iIndexObjects].classList.remove("hiddenbysearch");

        console.log("no search query, reset");
        return;
    }

    //loop though all designobjects
    for (let iIndexObjects = 0; iIndexObjects < iSizeDesignObjects; iIndexObjects++)
    {
        let sDOJSClass = objAllDesignObjects[iIndexObjects].dataset.designobjectjsclassname;
        // sSearchTags = objAllDesignObjects[iIndexObjects].dataset.searchlabelscsv;
        sSearchTags = arrDesignObjectsLibrary[sDOJSClass].sSearchLabelsCSV;
        arrSearchTags = sSearchTags.split(",");
        iSizeSearchTags = arrSearchTags.length; //cache size

        //first remove class searchvisible of all designobjects
        objAllDesignObjects[iIndexObjects].classList.add("hiddenbysearch");

        //iterate through search tags of each individual designobject
        //to find one with matching search tags
        for (let iTagIndex = 0; iTagIndex < iSizeSearchTags; iTagIndex++)
        {
            iPos = arrSearchTags[iTagIndex].indexOf(objSearchBox.value);
            //console.log("search '" + arrSearchTags[iTagIndex]+"' for '"+objSearchBox.value+"'. result: "+iPos);
            
            //found match searchtag
            if (iPos >= 0)
            {
                //console.log("MATCH! break");
                objAllDesignObjects[iIndexObjects].classList.remove("hiddenbysearch");
                break;
            }
        }
    }
}

/**
 * change the contents of the add panel at the bottom of the viewport
 */
function toggleAddPanelContents()
{
    objDivDragTarget = document.getElementById('panel-add-dragtarget');
    objDivDesignObjects = document.getElementById('panel-add-designobjects');

    if (objDivDragTarget.classList.contains('visible'))
    {
        objDivDragTarget.classList.remove('visible');
        objDivDesignObjects.classList.add('visible');
    }
    else
    {
        objDivDragTarget.classList.add('visible');
        objDivDesignObjects.classList.remove('visible');
    }
}