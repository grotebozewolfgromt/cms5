
/**
 * pagebuilder-toolbar.js
 *
 * This Javascript code regards to the header
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
 * 14 dec 2024 pagebuilder-header.js created
 */

/**
 * global vars
 */

const sViewportDesktopCSSClass = "viewport-desktop";
const sViewportTabletCSSClass = "viewport-tablet";
const sViewportMobileCSSClass = "viewport-mobile";


/**
 * set desktop viewport in designer
 */          
function setDesktopViewport()
{
    var objContent = document.getElementById(CSSID_RESIZEVIEWPORT_CONTAINER);

    //first remove everything
    objContent.classList.remove(sViewportDesktopCSSClass);
    objContent.classList.remove(sViewportTabletCSSClass);
    objContent.classList.remove(sViewportMobileCSSClass);

    //set the right viewport
    objContent.classList.add(sViewportDesktopCSSClass);

    //update buttons
    updateButtonStatesHeader();
}

/**
 * set mobile viewport in designer
 */          
function setTabletViewport()
{
    var objContent = document.getElementById(CSSID_RESIZEVIEWPORT_CONTAINER);

    //first remove everything
    objContent.classList.remove(sViewportDesktopCSSClass);
    objContent.classList.remove(sViewportTabletCSSClass);
    objContent.classList.remove(sViewportMobileCSSClass);

    //set the right viewport
    objContent.classList.add(sViewportTabletCSSClass);

    //update buttons
    updateButtonStatesHeader();
}

/**
 * set mobile viewport in designer
 */          
function setMobileViewport()
{
    var objContent = document.getElementById(CSSID_RESIZEVIEWPORT_CONTAINER);

    //first remove everything
    objContent.classList.remove(sViewportDesktopCSSClass);
    objContent.classList.remove(sViewportTabletCSSClass);
    objContent.classList.remove(sViewportMobileCSSClass);

    //set the right viewport
    objContent.classList.add(sViewportMobileCSSClass);

    //update buttons
    updateButtonStatesHeader();
}

/**
 * set cursor-mode to: edit
 */    
function setCursorModeTextEditor()
{
    iCursorMode = CURSORMODE_TEXTEDITOR;

    updateButtonStatesHeader();

    setAllDOContentEditable(true);
    setAllDOUserSelect(true);
    setAllDODraggable(false);
    // setAllDOCursor("unset"); //back to default, that's fine
}

/**
 * set cursor-mode to: selection
 */    
function setCursorModeSelection()
{
    iCursorMode = CURSORMODE_SELECTION;
    updateButtonStatesHeader();

    setAllDOContentEditable(false);    
    setAllDOUserSelect(false);
    setAllDODraggable(true);
    // setAllDOCursor("move");
}

/**
 * This function "reads" the UI and determines which buttons are pressed or not and 
 * assigns them the "pressed" css class
 * 
 * this function can be called:
 * - after a button press
 * - when the window resizes
 * - when ui is rendered
 * */
function updateButtonStatesHeader()
{
    let objContentViewport = document.getElementById(CSSID_RESIZEVIEWPORT_CONTAINER);    
    let objbtnDesktopViewport = document.getElementById("btnDesktopViewport");
    let objbtnTabletViewport = document.getElementById("btnTabletViewport");
    let objbtnMobileViewport = document.getElementById("btnMobileViewport");
    let btnCursorModeEdit = document.getElementById("btnCursorModeEdit");
    let btnCursorModeSelection = document.getElementById("btnCursorModeSelection");


    
    //===design viewport
    //we traverse in reverse order, so we can default to desktop as last
    if (objContentViewport.classList.contains(sViewportMobileCSSClass))        
    {
        // console.log('')
        objbtnDesktopViewport.classList.remove("pressed");
        objbtnTabletViewport.classList.remove("pressed");
        objbtnMobileViewport.classList.add("pressed");
    }
    else if (objContentViewport.classList.contains(sViewportTabletCSSClass))
    {
        objbtnDesktopViewport.classList.remove("pressed");
        objbtnTabletViewport.classList.add("pressed");
        objbtnMobileViewport.classList.remove("pressed");           
    }
    else //default: desktop
    {
        objbtnDesktopViewport.classList.add("pressed");
        objbtnTabletViewport.classList.remove("pressed");
        objbtnMobileViewport.classList.remove("pressed");
    }

    
    //==== Cursor mode
    if (iCursorMode == CURSORMODE_TEXTEDITOR)
    {
        btnCursorModeEdit.classList.add("pressed");
        btnCursorModeEdit.classList.add("pressed");
        btnCursorModeEdit.classList.add("pressed");
        btnCursorModeSelection.classList.remove("pressed");
    }
    else if (iCursorMode == CURSORMODE_SELECTION)
    {
        btnCursorModeEdit.classList.remove("pressed");
        btnCursorModeSelection.classList.add("pressed");
    }
 
}

