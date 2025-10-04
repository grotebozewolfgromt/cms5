
/**
 * pagebuilder-viewports.js
 *
 * This Javascript code regards to adjusting viewsport (desktop, tablet, mobile)
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
 * 3 apr 2024 pagebuilder-viewports.js created
 */

/**
 * global vars
 */


/**
 * initialise stuff in designer
 */
// function attachEventHandlersDesigner()
// {
//     const arrDO = [...document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER)]; //DOVP = Design Objects in ViewPort
//     const iLenDO = arrDO.length;
//     let sDOJSClass = "";

//     for (let iIndex = 0; iIndex < iLenDO; iIndex++)
//     {
//         sDOJSClass = arrDO[iIndex].dataset.designobjectjsclassname;
//         arrDesignObjectsLibrary[sDOJSClass].addEventListeners(arrDO[iIndex]);
//     }
// }


/**
 * remove all "selected" classes from all DesignObjects
 */
function unselectAllDesignObjects()
{
    const objDOAll = objDivDesignerContainer.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const iLenDoALL = objDOAll.length;

    //remove all old "selected"
    for(let iIndex = 0; iIndex < iLenDoALL; iIndex++)
        objDOAll[iIndex].classList.remove("selected");    
}

/**
 * Set all HTML elements that are DesignObjects to contentEditable=true or false (depending on parameter)
 * 
 * @param {bool} bEditable 
 * @param {HTMLElement} objSkipHTMLElement this element is skipped, meaning that it isn't set to false or true (thus untouched)
 */
function setAllDOContentEditable(bEditable, objSkipHTMLElement = null)
{
    const objDOAll = objDivDesignerContainer.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const iLenDoALL = objDOAll.length;
    let iLenChilds = 0;
    let iChildIndex = 0;

    for(let iDOIndex = 0; iDOIndex < iLenDoALL; iDOIndex++)
    {
        if (objSkipHTMLElement != objDOAll[iDOIndex]) //skip this element
        {
            if (objDOAll[iDOIndex].bContentEditable) //check first if editable at all
            {
                iLenChilds = objDOAll[iDOIndex].childElementCount;
                for (iChildIndex = 0; iChildIndex < iLenChilds; iChildIndex++)
                {
                    objDOAll[iDOIndex].children[iChildIndex].contentEditable = bEditable;    
                }
            }
        }
    }
}

/**
 * Set mousecursor on all HTML elements that are DesignObjects to [parameter]
 * The parameter are the cursor names in CSS, like pointer, crosshair, wait, help, not-allowed, zoom-in, grab etc
 * the parameter is not checked for validity
 * 
 * @param {string} sCursorStyle unset, pointer, crosshair, wait, help, not-allowed, zoom-in, grab etc
 */
function setAllDOCursor(sCursorStyle)
{
    const objDOAll = objDivDesignerContainer.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const iLenDoALL = objDOAll.length;
    // let sDOJSClass = ""

    for(let iIndex = 0; iIndex < iLenDoALL; iIndex++)
    {
        // sDOJSClass = objDOAll[iIndex].dataset.designobjectjsclassname
        objDOAll[iIndex].style.cursor = sCursorStyle;    
    }
}

/**
 * Set user-select css property on all HTML elements that are DesignObjects
 * The parameter are the cursor names in CSS, like pointer, crosshair, wait, help, not-allowed, zoom-in, grab etc
 * the parameter is not checked for validity
 * 
 * @param {bool} bSelectable true-> removes "user-select", false -> adds "user-select"
 */
function setAllDOUserSelect(bSelectable)
{
    const objDOAll = objDivDesignerContainer.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const iLenDoALL = objDOAll.length;

    for(let iIndex = 0; iIndex < iLenDoALL; iIndex++)
    {
        if (bSelectable)
        {
            if (this.bUserSelectText)
                objDOAll[iIndex].style.userSelect = "";
            else
                objDOAll[iIndex].style.userSelect = "none";
        }
        else
        {
            objDOAll[iIndex].style.userSelect = "none";
        }
    }
}

/**
 * Set draggable property on all HTML elements that are DesignObjects
 * i.e. <img src="img_logo.gif" draggable="true">
 * 
 * @param {bool} bDraggable true-> draggable="true", false-> draggable="false"
 */
function setAllDODraggable(bDraggable)
{
    const objDOAll = objDivDesignerContainer.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const iLenDoALL = objDOAll.length;

    for(let iIndex = 0; iIndex < iLenDoALL; iIndex++)
    {
        objDOAll[iIndex].draggable = bDraggable;
    }
}
