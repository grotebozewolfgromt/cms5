<?php
use dr\modules\Mod_PageBuilder\controllers\pagebuilder;

/**
 * pagebuilder-global.js
 *
 * This Javascript file contains the javascript for the pagebuilder
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
 * 2 mei 2024 pagebuilder-global.js created
 */

?>
/**
 * global JS vars
 */
// var arrDraggablesGlobal = null; //array of draggable objects in the whole pagebuilder, EXCEPT DESIGNOBJECTS IN DESIGNER AND STRUCTURE-TAB (they have their own event handlers)
// var arrDroppablesGlobal = null; //array of drag target objects in the whole pagebuilder, EXCEPT DESIGNOBJECTS IN DESIGNER AND STRUCTURE-TAB (they have their own event handlers)
var sLastSavedStateDesignerData = ""; //stores html from designer on each save, so we can compare if something has changed
var arrUndoStates = []; //undo states stack. Before every destructive action a snapshot of the designer data is saved to this array, so we can undo this action
var iPreviousUndoStatePointer = 0; //points to the undo state in the undo array (undo == -1, redo == +1)
// var bDirtyDocument = false; //needs a save?
var bIgnoreInput = false; //disables input temporarily. i.e. saving happens async. but we need to wait until is finished before we can handle a new save.
var arrDesignObjectsLibrary = []; //defines a list of designobjects that are POTENTIALLY available to use in designer. This is a list of instantiated DesignObject classes (the array key is getClassName() of DesignObject). This list is used by the new panel to show all design objects. an instance of each DesignObject occurs only ONCE in this array. This is not a shadow register of all the Design Objects in the designer
var objDivDesignerContainer = null; //HTML element of the designer container (assigned on page load) 
var iLastIDDOInDesigner = -1;//holds last DesignObject ID in Designer. 1. makes it quicker because it is caching the ID (instead of looking at all DesignObjects). 2. allows us to create multiple DesignObjects at once before being added to the designer (otherwise the returned ID is the same because they are not added to the designer yet). This variable is used by the function getNewDesignObjectID(). -1 means: not initialized.

//classes and Ids
const CSSCLASS_DESIGNOBJECT_CREATETAB = "designobject-createtab"; //objects in the left side of the screen (create tab)
const CSSCLASS_DESIGNOBJECT_DESIGNER = "designobject-designer"; //what is the CSS class that marks a HTML element as Design Object
const CSSCLASS_DESIGNOBJECT_STRUCTURE = "designobject-structure"; //what is the CSS class that marks a HTML element as Design Object
const CSSCLASS_INVISIBLE_ELEMENT = "invisible-element"; //what is the CSS class that marks a HTML element as Design Object
const CSSID_RESIZEVIEWPORT_CONTAINER = "designviewport-resizecontainer"; //what is the CSS id that marks the HTML element that is the viewport-container for resizing
const CSSID_DESIGNOBJECT_DESIGNER_PREFIX = "designobject-indesigner-"; //what is the prefix if the DesignObject html id
                                        

//cursormode
const CURSORMODE_TEXTEDITOR = 0; //represents cursormode: Edit
const CURSORMODE_SELECTION = 1; //represents cursormode: Selection
var iCursorMode = CURSORMODE_TEXTEDITOR; //stores current cursormode - i.e. if (iCursorMode == iCursorModeEdit) { alert('edit mode') }

//abort controllers that keeps track of event listeners to clean up. See https://stackoverflow.com/questions/4950115/removeeventlistener-on-anonymous-functions-in-javascript
var objElementTabAbortController = new AbortController(); // the element-details tab on the right side of the screen
var objToolbarAbortController = new AbortController(); // the toolbar on top of the designer
// var arrDOInDesignerAbortControllers = []; //defines a list of AbortController() objects for designobjects in the designer (the array key is id of the HTML element in the designer). 
var arrDOInStructureTabAbortControllers = []; //defines a list of AbortController() objects for designobjects in the structuretab (the array key is HTML data attribute: data-corresponding-design-object-id). 
var objDragDropGlobalAbortController = new AbortController();


//dragging and dropping
// var objDivDropCursorHorizontal = document.createElement("div"); //indicates where user is dropping an HTML element
// objDivDropCursorHorizontal.className = DRAGDROP_CSSCLASS_ISDROPCURSOR_HORIZONTAL; 
// var objDivDropCursorVertical = document.createElement("div"); //indicates where user is dropping an HTML element
// objDivDropCursorVertical.className = DRAGDROP_CSSCLASS_ISDROPCURSOR_VERTICAL; 
objDragDrop = new PagebuilderDragDrop(); //drag drop helper class


/** 
 * objDOCategories
 * every designobject lives in 1 or more categories 
 * 
 * There is a big overlap between types and categories.
 * While they can be the same, they don't nessarily have to be.
 */
const objDOCategories = 
{
    all: "<?php echo transm($sModule, 'pagebuilder_category_all_title', '-- All objects --') ?>",
    pagebuilderdocs: "<?php echo transm($sModule, 'pagebuilder_category_pagebuilderdocs_title', 'Other pagebuilder documents') ?>",
    //skeletons: "<?php echo transm($sModule, 'pagebuilder_category_skeletons_title', 'Skeletons') ?>",
    layouts: "<?php echo transm($sModule, 'pagebuilder_category_layouts_title', 'Layouts') ?>",
    blocks: "<?php echo transm($sModule, 'pagebuilder_category_blocks_title', 'Blocks') ?>",
    forms: "<?php echo transm($sModule, 'pagebuilder_category_forms_title', 'Forms') ?>",
    allelements: "<?php echo transm($sModule, 'pagebuilder_subcategory_allelements_title', 'All elements') ?>",
    textelements: "<?php echo transm($sModule, 'pagebuilder_subcategory_textelements_title', 'Text elements') ?>",
    multimediaelements: "<?php echo transm($sModule, 'pagebuilder_subcategory_multimedia_title', 'Multi media elements') ?>",
    variables: "<?php echo transm($sModule, 'pagebuilder_category_variables_title', 'Variables') ?>", 
    emojis : "<?php echo transm($sModule, 'pagebuilder_category_emojis_title', 'Emojis') ?>",
    favorites: "<?php echo transm($sModule, 'pagebuilder_category_favorites_title', 'Favorites') ?>"
};


/**
 * objDOTypes:
 * 
 * every designobject is of a type.
 * A type is: a structure, block, element or variable
 * same types may consist within other types, but not others.
 * A structure can be dragged into an empty space, not in a block or element.
 * But a block or element can be dragged into a structure.
 * A block can not be dragged into an element, but an element can be dragged into a block.
 */
const objDOTypes = 
{
    skeleton: "skeleton",
    layout: "layout",
    form: "form",
    block: "block",
    element: "element",
    variable: "variable"
};


/**
 * INIT PAGEBUILDER main()
 * 
 * sets all the initial states in the pagebuilder when document loads
 */
window.addEventListener("load", function main(objEvent)
{
    //variable assignments
    objDivDesignerContainer = document.getElementById("designer-root"); //needs to be done before inits, because they use it

    //functions
    initGlobalVars();
    initPanels(); //in pagebuilder-panels.js
    initTabs(); //in pagebuilder-tabs.js
    // attachEventHandlersDesigner();//in pagebuilder-viewports.js
    initDragDrop();
    initSaveStates();
    addUndoState();
    registerShortcutKeys();
    updateButtonStates();
    updateStructureTab();

    setCursorModeTextEditor();//set default cursor mode

    // testFunction();
    // testFunction2();    
});


/**
 * WHEN WINDOW RESIZES
 */
window.addEventListener("resize", (objEvent) => 
{
    updateButtonStates();
});        

/**
 * initialize global variables
 * (json objects from server-side business logic)
 */
function initGlobalVars()
{        
 
<?php 
    
        $arrDOs = $objController->getDesignObjects();
        $arrAKDO = array_keys($arrDOs);
        foreach($arrAKDO as $sTag)
        {
            echo '    customElements.define("'.$sTag.'", '.$arrDOs[$sTag].');'."\n";
            echo '    arrDesignObjectsLibraryPush(new '.$arrDOs[$sTag].');'."\n";
            echo "\n";
        }
    ?>

}

/**
 * initialize tracking if document is changed
 */
function initSaveStates()
{
    sLastSavedStateDesignerData = objDivDesignerContainer.innerHTML;
    bDirtyRecord = false;
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
async function updateButtonStates()
{
    updateButtonStatesHeader();
    updateButtonStatesPanels();
}

/**
 * defines all shortcut keys
 */
async function registerShortcutKeys()
{
    //===== KEYS TO IGNORE =====
    document.addEventListener('keydown', e => 
    {
        //==== SAVE ====
        if (e.ctrlKey && e.key === 's') 
        {            
            e.preventDefault() // Prevent the Save dialog to open
        }
    
    });

    //===== ADD FUNCTIONALITY TO IGNORE =====
    document.addEventListener('keyup', e => 
    {
        //==== SAVE ====
        if (e.ctrlKey && e.key === 's') 
        {            
            handleSavePagebuilder();
        }

        //==== DELETE ====
        if (document.activeElement.tagName == "BODY")
        {
            if ((e.key === 'Delete') || (e.key === 'Backspace')) 
            {                            
                const objDODes = objDivDesignerContainer.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_DESIGNER + ".selected");

                addUndoState();
                for (let objEl of objDODes)
                {
                    if  (
                            ((!arrDesignObjectsLibrary[objEl.dataset.designobjectjsclassname].bRespondToDeleteKeyPress) && (iCursorMode == CURSORMODE_TEXTEDITOR))
                            ||
                            ((iCursorMode == CURSORMODE_SELECTION))
                        )
                    {
                        //remove event listeners
                        objDragDrop.removeDraggable(objEl);
                        objDragDrop.removeDroppable(objEl);

                        //actual delete
                        objDivDesignerContainer.removeChild(objEl);

                        //update UI
                        updateStructureTab();
                        updateToolbarAndElementTab();

                        console.log("item deleted by pressing delete or backspace");
                    }
                    console.log("item deleted by pressing delete or backspaceasdasdasdasd");
                }
            }
        }

        //==== UNDO/REDO ====
        if (e.ctrlKey && e.key === 'z') 
        {            
            // e.preventDefault(); // Prevent the Save dialog to open
            handleUndo();
        }        
        if (e.ctrlKey && e.key === 'x') 
        {            
            // console.log( "redo triugger");
            // e.preventDefault(); // Prevent the Save dialog to open
            handleRedo();
        }        
    });
}


/**
 * when clicked on the exit button 
*/
/*
function handleExitPagebuilder()
{
    const objDlg = document.getElementById("dlgExitPagebuilder");
    const objBtnX = document.getElementById("btnDialogExitpagebuilderX");
    const objBtnExitNoSave = document.getElementById("btnDialogExitPagebuilderDoExitNoSave");
    // const objBtnExitSave = document.getElementById("btnDialogExitPagebuilderDoExitSave");
    const objBtnStay = document.getElementById("btnDialogExitPagebuilderDoStay");
    const objProgressbar = document.getElementById("progressbar");

    //=== nothing changed? Just exit!
    if ((bDirtyDocument == false) && (objDivDesignerContainer.innerHTML == sLastSavedStateDesignerData))
    {
        window.location.href="<?php echo $sURLReturn ?>";
        return;
    }

    //=== if something changed:
    objDlg.showModal();

    //add eventlisteners to buttons
    objBtnX.addEventListener("mousedown", function exitpagebuilderxmousedown(e)
    {
        console.log("pressed X");
        objDlg.close(false);        
        this.removeEventListener("mousedown", exitpagebuilderxmousedown);          
    });

    objBtnExitNoSave.addEventListener("mousedown", function exitpagebuildernosavemousedown(e)
    {
        objDlg.close(true);  
        objProgressbar.classList.add("visible");
        window.location.href="<?php echo $sURLReturn ?>";
        this.removeEventListener("mousedown", exitpagebuildernosavemousedown);               
    });

    
    // objBtnExitSave.addEventListener("mousedown", function exitpagebuildersavemousedown(e)
    // {
    //     handleSavePagebuilder();
    //     objDlg.close(true);  
    //     objProgressbar.classList.add("visible");
    //     window.location.href="<?php echo $sURLReturn ?>";
    //     this.removeEventListener("mousedown", exitpagebuildersavemousedown);              
    // });    
    
    
    objBtnStay.addEventListener("mousedown", function exitpagebuilderstaymousedown(e)
    {
        console.log("pressed Stay");
        objDlg.close(false);
    });
        
}
*/

/**
 * when clicked on the save button 
*/
function handleSavePagebuilder()
{
    //update additional fields, so we can save them
    arrAdditionalFields["<?php echo pagebuilder::FIELD_DATA; ?>"] = objDivDesignerContainer.innerHTML.trim();
    arrAdditionalFields["<?php echo pagebuilder::FIELD_HTMLRENDERED; ?>"] = renderHTML();

    handleSaveAFC(null, document.querySelector('#btnSave dr-icon-spinner'));
}

/**
 * exit pagebuilder:
 * we have an extra field to worry about: the actual data of the designer (Which is not a form element)
 */
function handleExitPagebuilder()
{
    //update additional fields, so we can save them (exit dialog has save feature)
    arrAdditionalFields["<?php echo pagebuilder::FIELD_DATA; ?>"] = objDivDesignerContainer.innerHTML;
    arrAdditionalFields["<?php echo pagebuilder::FIELD_HTMLRENDERED; ?>"] = renderHTML();

    if (objDivDesignerContainer.innerHTML != sLastSavedStateDesignerData)
        setDirtyRecord();

    handleExitAFC(exitAfterSave, document.querySelector('#btnExit dr-icon-spinner'));
}

/**
 * when clicked on button: preview
 */
function handlePreview()
{
    let sURL = "<?php echo $sURLPreview ?>";

    if (iRecordID >= 0)
    {
        sURL = addVariableToURL(sURL, "<?php echo ACTION_VARIABLE_ID ?>", iRecordID);
    }

    openInNewTab(sURL);
}

/**
 * return rendered HTML for frontpage by reading the DOM
 * 
 * explanation solution:
 * We only want DIRECT children of the container,
 * not simply obtain all elements with classname "designobject-indesigner"
 * because this gives problems with nested elements where the DesignObject 
 * class is responsible for rendering all its children.
 * We just want to trigger the right DesignObject classes to render the html
 * 
 * @returns {string}
 */
function renderHTML()
{
    let sHTML = "";

    for (let objChild of objDivDesignerContainer.children) 
    {
        if (objChild instanceof DesignObject)
            sHTML += objChild.renderHTML()+"\n";
    }

    return sHTML;
}


/**
 * set up and initialize dragging & dropping
 */
function initDragDrop()
{

    //==== populate drag & drop helper class
    //droppables
    objDragDrop.addDroppable(document.getElementById("panel-add-dragtarget"));
    objDragDrop.addDroppable(document.getElementById("tab-content-details-structure-desktop"));
    objDragDrop.addDroppable(document.getElementById("tab-content-details-structure-mobile"));
    objDragDrop.addDroppable(objDivDesignerContainer);

    //draggables
    objDragDrop.addDraggableArray([...document.querySelectorAll("." + objDragDrop.sCSSClassDraggable + "." + CSSCLASS_DESIGNOBJECT_CREATETAB)]); //get all draggables in create tab
}

/**
 * handles the drop from a source on a target
 * 
 * @param {Event} objEvent
 * @param {Array} arrDragSources the elements that are dragged
 * @param {HTMLElement} objTarget the
 * @param {Array} arrPotentialDropTargets all possible drop targets (needed to determine order) 
 * @returns 
 */
/*
function handleDrop(objEvent, arrDragSources, objTarget, arrPotentialDropTargets)
{       
    const objDivAddPanelDragtarget = document.getElementById("panel-add-dragtarget");
    const iLenDragSources = arrDragSources.length;
    const objNearestElement = getDragDropNearestElement(arrPotentialDropTargets, objEvent.clientX, objEvent.clientY);
    let sDOJSClassName = "";
    let objDO = null;

    //handle drop on add panel
    if (objTarget == objDivAddPanelDragtarget)
    {
        addDesignObjectToDesigner(arrDragSources[0]); //0 because you can only drag 1 at a time
        console.log("handled drop from " + arrDragSources[0].dataset.designobjectjsclassname);
    }

    //handle drops in designer container
    
    if (objTarget == objDivDesignerContainer)
    {

        for (let iIndex = 0; iIndex < iLenDragSources; iIndex++)
        {
            objDO = null; //default

            //determine where drag came from (create tab or designer itself)
            // console.log(" asdfasdfasdfasdfasdf "+ arrDragSources[iIndex].className);
            if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_CREATETAB))
            {
                sDOJSClassName = arrDragSources[iIndex].dataset.designobjectjsclassname;
                objDO = arrDesignObjectsLibrary[sDOJSClassName].createElement();
            }
            if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER))
            {
                objDO = arrDragSources[iIndex];
            }
            if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_STRUCTURE))
            {
                objDO = arrDragSources[iIndex];
            }

            //add to designer
            if (objNearestElement)
            {
                if (getDragDropBeforeElement(objNearestElement, objEvent.clientX, objEvent.clientY))
                {
                    objNearestElement.before(objDO);
                }
                else
                {
                    objNearestElement.after(objDO);
                }

                //remove the drop cursors
                objDivDropCursorHorizontal.remove();
                objDivDropCursorVertical.remove();                
            }    

            //console.log("handled drop on DESIGNER " + sDOJSClassName);
        }
        
    }
        

    
    // console.log(objSource.dataset.type);
    // article.dataset.indexNumber; // "12314"
    // article.dataset.parent; // "cars"
    
    // console.log('handle drop from '+ objSource + ' with from DO "' + objSource.dataset.designobjectjsclassname + '" on ' + objTarget);
}
*/

/**
 * adds designobject to designer at the bottom
 * it wraps the html in a div so we can recognize it later
 * @param {HTMLElement} objDivDesignObject the div that has done the dropping
 */
function addDesignObjectToDesigner(objDivDesignObject)
{
    let sDOJSClass = objDivDesignObject.dataset.designobjectjsclassname;
    let objNewElement = arrDesignObjectsLibrary[sDOJSClass].cloneNode(true);
    objNewElement.renderDesigner();

    //make exact replica
    if (objDivDesignObject.classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER) || objDivDesignObject.classList.contains(CSSCLASS_DESIGNOBJECT_STRUCTURE))
    {
        // arrDesignObjectsLibrary[sDOJSClass].copyElement(objDivDesignObject, objNewElement, true);
        objNewElement.classList.remove(objDragDrop.sCSSClassSelected); //remove selected
    }

    objDivDesignerContainer.appendChild(objNewElement);

    if (iCursorMode == CURSORMODE_SELECTION)
        setCursorModeSelection();
    if (iCursorMode == CURSORMODE_TEXTEDITOR)
        setCursorModeTextEditor();
    

    console.log("addDesignObjectToDesigner: from " + sDOJSClass + ": add HTML do designer ");
}



/**
 * returns a new id for a designobject
 * 
 * <div id="designobject-indesigner-1234"> 
 */
function getNewIdDesignObject()
{
    if (iLastIDDOInDesigner == -1) //if uninitialized, then figure it out
    {
        var iCurrId = 0;
        const objDO = document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER); //DO = DesignObject
        const iLenDO = objDO.length;
        var arrDOIds = [];
    
        for(let iIndex = 0; iIndex < iLenDO; iIndex++)
        {
            arrDOIds = objDO[iIndex].id.split("-");        

            iCurrId = parseInt(arrDOIds[2]); //3 element in id (separated by -)
            if (iCurrId > iLastIDDOInDesigner)
                iLastIDDOInDesigner = iCurrId;
        }    

        iLastIDDOInDesigner++
        return CSSID_DESIGNOBJECT_DESIGNER_PREFIX + iLastIDDOInDesigner;
    }
    else
    {
        iLastIDDOInDesigner++;
        return CSSID_DESIGNOBJECT_DESIGNER_PREFIX + iLastIDDOInDesigner;
    }
}

/**
 * sets the state of this document to dirty,
 * so we know we need to save
 * (this isn't nessesary for designobjects because 
 * they are compared to the previously saved version)
 */
// function setDirtyDocument()
// {
//     bDirtyDocument = true;
//     console.log("set document to dirty");
// }


/**
 * is called by eventlistener for changes made in details-element-tab
 * 
 * @param {string} sVariable 
 * @param {string} sValue 
 */
function changeStyleDOInDesigner(sVariable, sValue)
{
    const objDOSelected = objDivDesignerContainer.getElementsByClassName("selected");
    const iLenSelected = objDOSelected.length;

    for (let iIndex = 0; iIndex < iLenSelected; iIndex++)
    {
        objDOSelected[iIndex].style.setProperty(sVariable, sValue);
    }
}

/**
 * add element to arrDesignObjectsLibrary
 * but the key is getClassName() of the designobject
 *
 * function just for easy coding
 * 
 * @param {DesignObject} objDesignObject 
 */
function arrDesignObjectsLibraryPush(objDesignObject)
{
    arrDesignObjectsLibrary[objDesignObject.getClassName()] = objDesignObject;
}


/**
 * add state that can be undone
 */
function addUndoState()
{

// debugger

    //don't register if the same
    if (arrUndoStates[iPreviousUndoStatePointer] == objDivDesignerContainer.innerHTML)
    {
        console.log("ignored add undo-state. because it's the same as the last one");
        return;
    }

    //remove states when halfway the stack
    iIndicesToRemove = (arrUndoStates.length - 1) - iPreviousUndoStatePointer;
    if (iIndicesToRemove > 0) 
    {
        debugger
        arrUndoStates.splice(iPreviousUndoStatePointer, iIndicesToRemove);
    }
    arrUndoStates.push(objDivDesignerContainer.innerHTML);


    if (arrUndoStates.length <= 1)//don't update pointer on first element (because array length=1, but points to first element at index 0)
        iPreviousUndoStatePointer = 0;
    else
        iPreviousUndoStatePointer++;    

    //@todo window van maximaal 100 undo levels
    //@todo als pointer middenin de array, dan rest verwijderen omdat deze overschreven moet worden

    console.log("added undo state");
}

/**
 * revert to previous undo state
 */
function handleUndo()
{ 
    
    debugger
    
    //add current state, so we can REDO this step
    addUndoState();
    iPreviousUndoStatePointer--; //undo the add state  

    //go
    if ((iPreviousUndoStatePointer) >= 0)
    {
        objDivDesignerContainer.innerHTML = arrUndoStates[iPreviousUndoStatePointer];
        deselectAllDOInDesigner(); //if they were selected, deselect to prevent weird behavior
        attachEventHandlersDesigner(); //reattach event handlers --> TODO: remove old event handlers first

        iPreviousUndoStatePointer--;  
                
        console.log("handled UNDO");
    }
    else
    {
        console.log("nothing to undo");
    }
}

/**
 * redo the undo
 * @todo
 */
function handleRedo()
{
    debugger

    if (arrUndoStates.length > (iPreviousUndoStatePointer + 1))
    {
        iPreviousUndoStatePointer++;

        objDivDesignerContainer.innerHTML = arrUndoStates[iPreviousUndoStatePointer];
        deselectAllDOInDesigner(); //if they were selected, deselect to prevent weird behavior
        attachEventHandlersDesigner(); //reattach event handlers

        console.log("handled REDO");        
    }
    else
    {
        console.log("nothing to redo");
    }    

}

/**
 * deselect all DesignObjects in designer
 */
function deselectAllDOInDesigner()
{
    const arrAllDO = document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_DESIGNER);
    const objTabDesktop = document.getElementById("tab-content-details-element-desktop");
    const objTabMobile = document.getElementById("tab-content-details-element-mobile");

    for (let objDO of arrAllDO)
    {
        if (objDO.classList.contains("selected"))
            objDO.classList.remove("selected");
    }

    objTabDesktop.innerHTML = "<?php echo transm($sModule, 'pagebuilder_panel_detail_defaulttext_selectelement', 'Select element in designer'); ?>";
    objTabMobile.innerHTML = "<?php echo transm($sModule, 'pagebuilder_panel_detail_defaulttext_selectelement', 'Select element in designer'); ?>";
}

/**
 * removes all input error CSS classes from elements
 */
// function removeAllInputErrors()
// {
//     const arrErrFields = document.getElementsByClassName("inputerror");
//     const iLenFields = arrErrFields.length;

//     for (let iIndex = 0; iIndex < iLenFields; iIndex++)
//     {        
//         arrErrFields[0].classList.remove("inputerror");
//     }
// }


/**
 * Updates the title in <title>-tags but also the title-label above the toolbar
 * 
 * @param {string} sValue 
 */
function updateTitle(sNewTitle)
{
    objLblTitle = document.getElementById("lblTitleHeader");
    objLblTitle.innerHTML = sNewTitle;
    document.title = sNewTitle;
}


/**
* keyup event of edtNameInternal
 */
function onNameInternalChange()
{
    if (objNameInternal = document.getElementById("edtNameInternal"))
    {
        if (objHTMLTitle = document.getElementById("edtHTMLTitle"))
        {
            if (objHTMLTitle.value == "")
            {
                objHTMLTitle.value = objNameInternal.value;
                updateTitle(objNameInternal.value);
            }
        }       
        
        if (objPrettyURL = document.getElementById("edtURLSlug"))
        {
            if (objPrettyURL.value == "")
            {
                objPrettyURL.value = generatePrettyURL(objNameInternal.value);
            }            
        }
    }
}

/**
 * keyup event of edtHTMLTitle
 */
function onHTMLTitleChange()
{
    if (objHTMLTitle = document.getElementById("edtHTMLTitle"))
    {
        if (objNameInternal = document.getElementById("edtNameInternal"))
        {
            if (objNameInternal.value == "")
            {
                objNameInternal.value = objHTMLTitle.value;            
                updateTitle(objHTMLTitle.value);
            }
        }

        if (objPrettyURL = document.getElementById("edtURLSlug"))
        {
            if (objPrettyURL.value == "")
            {
                objPrettyURL.value = generatePrettyURL(objHTMLTitle.value);
            }            
        }
    
    }
    
}

/**
 * get the first DesignObject HTML element of a HTML element. 
 * THIS CAN BE THE CURRENT HTML ELEMENT!!!
 * it traverses the html tree recursively until it finds the first parent
 * 
 * use case example:
 * when user clicks on an element, I need to assign the "selected" css class.
 * but the click-source is a <p> or <h1>. 
 * So, I need to get the parent "designobject-indesigner" to assign "selected" class
 * 
 * @param {HTMLElement} objHTMLElementChild 
 * @return {HTMLElement} objHTMLElementChild returns null if no parent found
 */
function getFirstDesignObjectParent(objHTMLElementChild)
{
    //stop searching when designer root element found
    if (objHTMLElementChild === objDivDesignerContainer) 
        return null;

    if (objHTMLElementChild instanceof DesignObject)
    {
        return objHTMLElementChild;
    }
    else
    {
        //shouldn't be happening but just in case you reach document root
        if (objHTMLElementChild.parentElement == null) 
            return null;

        return getFirstDesignObjectParent(objHTMLElementChild.parentElement);
    }
    
    return null; //you should notbe able to get here, but just in case
}

/**
 * just a function to temporarily test stuff
 */
function testFunction()
{
     /*
    //bold
    objBtn = document.createElement("button");
    objDivDesignerContainer.appendChild(objBtn);  
    objBtn.innerHTML = "testclick";
    objBtn.addEventListener("mousedown", function hitest(e) 
    {
        console.log("test " + e.target);
    }, {signal: objElementTabAbortController.signal});         

    objElementTabAbortController.abort();
    objElementTabAbortController = new AbortController();

    //remove
    objBtn2 = document.createElement("button");
    objDivDesignerContainer.appendChild(objBtn2);  
    objBtn2.innerHTML = "remove listener btn 1";
    objBtn2.addEventListener("mousedown", function hitest2(e) 
    {
        // objBtn.removeEventListener("mousedown", objBtn.hitest);
        // objElementTabAbortController.abort();
        console.log("remove listener " + e.target);
    }, {signal: objElementTabAbortController.signal});         


    //3
    objBtn3 = document.createElement("button");
    objDivDesignerContainer.appendChild(objBtn3);  
    objBtn3.innerHTML = "remove listener from btn4";
    objBtn3.addEventListener('click', function handler() {
        objElementTabAbortController.abort();
        console.log('remove listener btn4: ');
    });
    // objBtn3.removeEventListener('click', objBtn3.handler);  
    
    //4
    objBtn4 = document.createElement("button");
    objDivDesignerContainer.appendChild(objBtn4);  
    objBtn4.innerHTML = "click 4 times to remove listener ";

    // var controller = new AbortController();
    let click = 0;

    objBtn4.addEventListener('click', function handlerieeee() {
      click++;
      if (click === 4) {
        // Cancel and clean up the event using our controller:
        console.log('click reached and abort: '+click);
        objElementTabAbortController.abort();
      } else {
        console.log('clicks '+click);
      }
    }, {
      // Tie our controller to this event binding:
      signal: objElementTabAbortController.signal
    });    

    objBtn4.addEventListener("mouseover", () => 
        {
          console.log('mouse overtttttt');
      }, {
        // Tie our controller to this event binding:
        signal: objElementTabAbortController.signal
      });  
      */
}

function testFunction2()
{
/*
    // objElementTabAbortController.abort();
    // objElementTabAbortController = null;
    //console.log("test functio2 "+objElementTabAbortController);

    var rect = document.getElementById("designviewport-container").getBoundingClientRect();
    // console.log(rect.top, rect.right, rect.bottom, rect.left);

    //https://medium.com/variance-digital/interactive-rectangular-selection-on-a-responsive-image-761ebe24280c
    const canvas = document.getElementById("canvas");
    canvas.style.left = rect.left;
    canvas.style.right = rect.right;
    canvas.style.top = rect.top;
    canvas.style.bottom = rect.bottom;

    const ctx = canvas.getContext("2d");
    ctx.beginPath(); // Start a new path
    ctx.rect(10, 0, 150, 100); // Add a rectangle to the current path
    ctx.fill(); // Render the path
*/
}