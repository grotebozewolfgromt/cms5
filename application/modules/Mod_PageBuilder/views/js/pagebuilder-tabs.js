
/**
 * pagebuilder-tabs.js
 *
 * The Javascript regarding to tabs
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
 * 13 apr 2024 pagebuilder-tabs.js created
 */

/**
 * global vars
 */




/**
 * initialize tabs
 * -copy the desktop panels to the mobile panels
 * -hook the functionality of the tab-heads/select to tab-contents
 */
function initTabs()
{
    // ==== NEW PANELS ====

    //populate categories in <select> AND create and populate tabs
    createDivTabContents("selNewDesktopCategory", "tabscontentdesktop", "desktop");
    createDivTabContents("selNewMobileCategory", "tabscontentmobile", "mobile");

    //bind tab-content divs to <select>
    //desktop
    var objTabsNewPanelDesktop = new TDivTabsheets("", "selNewDesktopCategory");
    for (let sCategory in objDOCategories) 
    {
        objTabsNewPanelDesktop.addTabsheetSelect("tab-content-new-" + sanitizeHTMLNodeIdName(sCategory) + "-" + "desktop");
    }
    //mobile
    var objTabsNewPanelDesktop = new TDivTabsheets("", "selNewMobileCategory");
    for (let sCategory in objDOCategories)     
    {
        objTabsNewPanelDesktop.addTabsheetSelect("tab-content-new-" + sanitizeHTMLNodeIdName(sCategory) + "-" + "mobile");
    }

    // ==== DETAIL PANELS ====
    objTabsDetailsPanelDesktop = new TDivTabsheets("selected");
    objTabsDetailsPanelDesktop.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'document', 'desktop'); ?>", "<?php echo $objController->getTabContentId('details', 'document', 'desktop'); ?>");
    objTabsDetailsPanelDesktop.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'structure', 'desktop'); ?>", "<?php echo $objController->getTabContentId('details', 'structure', 'desktop'); ?>");
    objTabsDetailsPanelDesktop.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'element', 'desktop'); ?>", "<?php echo $objController->getTabContentId('details', 'element', 'desktop'); ?>");

    objTabsDetailsPanelMobile = new TDivTabsheets("selected");
    objTabsDetailsPanelMobile.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'document', 'mobile'); ?>", "<?php echo $objController->getTabContentId('details', 'document', 'mobile'); ?>");
    objTabsDetailsPanelMobile.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'structure', 'mobile'); ?>", "<?php echo $objController->getTabContentId('details', 'structure', 'mobile'); ?>");
    objTabsDetailsPanelMobile.addTabsheetDiv("<?php echo $objController->getTabHeadId('details', 'element', 'mobile'); ?>", "<?php echo $objController->getTabContentId('details', 'element', 'mobile'); ?>");

    updateTitle("<?php echo $sNameInternal; ?>");
}


/**
 * 1. populates categories <select>
 * 2. create tabs based on categories
 * 
 * @param {object} sIdSelCategories id of <select>
 * @param {object} sIdParentDivTabs id of parent div object of tabs
 */

function createDivTabContents(sIdSelCategories, sIdParentDivTabs, sDesktopOrMobile)
{
    const objSelCategories = document.getElementById(sIdSelCategories);    
    const objDivTabsContent = document.getElementById(sIdParentDivTabs);    

    for (let sCategory in objDOCategories) 
    {
        //add categories to <select>
        let objOpt = document.createElement("option");
        objOpt.value = "tab-content-new-" + sanitizeHTMLNodeIdName(sCategory) + "-" + sDesktopOrMobile;
        objOpt.text = objDOCategories[sCategory]; 
        objSelCategories.appendChild(objOpt);


        //create div-tabs
        let objDivTabContent = document.createElement("div");
        objDivTabContent.id = "tab-content-new-" + sanitizeHTMLNodeIdName(sCategory) + "-" + sDesktopOrMobile;
        objDivTabContent.className = "designobject-grid-" + sDesktopOrMobile;

        createDivDesignObjectGrid(objDivTabContent, sCategory);

        objDivTabsContent.appendChild(objDivTabContent);          
    }

}

/**
 * create grid-div for DesignObjects
 * exists in: new panel desktop, new panel mobile, favorites (bottom of content)
 * 
 * @param {HTMLElement} objDivTabContent <div>
 * @param {string} sCategoryKey internal category name
 */
function createDivDesignObjectGrid(objDivTabContent, sCategoryKey)
{    
     //grid with design objects
    let objDivDOGrid = document.createElement("div");
    objDivDOGrid.className = "designobject-grid";

    //loop through Design Objects
    //look if object belongs in category    
    for (let sDOJSClass in arrDesignObjectsLibrary) 
    {
        let arrCatsInDO = arrDesignObjectsLibrary[sDOJSClass].arrCategories;
        let ilengCatsInDO = arrCatsInDO.length;

        //for each category
        for (let iCatIndex = 0; iCatIndex < ilengCatsInDO; iCatIndex++)
        {
            // sTemp1 = arrCatsInDO[iCatIndex];
            // sTemp2 = objDOCategories[sCategoryKey];

            //if DO is in category
            if (arrCatsInDO[iCatIndex] === objDOCategories[sCategoryKey]) //compared on language aware name level, instead of internal name
            {
                let objDO = createDivDesignObjectForInspectorGrid(arrDesignObjectsLibrary[sDOJSClass]);
                objDivDOGrid.appendChild(objDO);
            }
        }
        // console.log("cat: " + sCategoryKey + " check: " + arrDesignObjectsAll[iDOIndex].sDOJSClass);
    }
    objDivTabContent.appendChild(objDivDOGrid); 

}

/**
 * create a DesignObject div for the inspector (panel new desktop, panel new mobile, panel add)
 * @param {object} DesignObject instantiated
 * @return {object} div
 */
function createDivDesignObjectForInspectorGrid(objDesignObject)
{
    const objDODiv = document.createElement("div");
    objDODiv.classList.add(CSSCLASS_DESIGNOBJECT_CREATETAB);
    objDODiv.classList.add(objDragDrop.sCSSClassDraggable);
    objDODiv.draggable = true;
    objDODiv.dataset.designobjectjsclassname = objDesignObject.getClassName();
    objDODiv.innerHTML = objDesignObject.sIconSVG;
    
    objDODiv.addEventListener("mousedown", (event) =>
    {
        let objDOInspector = document.getElementsByClassName(CSSCLASS_DESIGNOBJECT_CREATETAB);
        let iLenDOInspector = objDOInspector.length; //we cache length because there are a lot, so we dont have to call .length on each loop iteration
        
        //remove "selected" classes from all objects
        for (let iIndex = 0; iIndex < iLenDOInspector; iIndex++)
            objDOInspector[iIndex].classList.remove(objDragDrop.sCSSClassSelected);
    
        //add "selected" class div
        objDODiv.classList.add("selected");    
    });
    objDODiv.addEventListener("dblclick", (event) =>
    {
        // console.log("doubleclick");
        addDesignObjectToDesigner(objDODiv);
    });
    

    const objDOTitleDiv = document.createElement("div");
    objDOTitleDiv.className = CSSCLASS_DESIGNOBJECT_CREATETAB + "-text";
    objDOTitleDiv.innerHTML = objDesignObject.sTitle + "";
    objDODiv.appendChild(objDOTitleDiv);
    

    return objDODiv;
}

/**
 * update toolbar (top of screen) and details-tab for element (right of the screen)
 * i.e. when clicked on element
 */
function updateToolbarAndElementTab()
{
    const objArrSelected = objDivDesignerContainer.getElementsByClassName("selected");
    const iLenSelected = objArrSelected.length;
    const objTabDesktop = document.getElementById("tab-content-details-element-desktop");
    const objTabMobile = document.getElementById("tab-content-details-element-mobile");
    const objToolbar = document.getElementsByClassName("toolbar")[0];
    var objSelectedElement = null; //HTMLElement

    let bSameDOs = false; //want to know if selected are all the same
    // console.log("selected elements: " + objSelected.length);

    //remove old eventlisteners first before attaching new in render-functions
    objElementTabAbortController.abort();   
    objElementTabAbortController = new AbortController();    
    objToolbarAbortController.abort();   
    objToolbarAbortController = new AbortController();    

    //empty tab 
    objTabDesktop.innerHTML = "";
    objTabMobile.innerHTML = "";

    //nothing selected
    if (objArrSelected.length === 0)
    {
        objTabDesktop.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_nothingselected', 'Nothing selected');?>";
        objTabMobile.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_nothingselected', 'Nothing selected');?>";
        objToolbar.innerHTML = "";
        return;
    }

    //show how much elements selected if more than 1
    if (objArrSelected.length > 1)
    {
        objTabDesktop.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_noelementsselected', 'Elements selected');?>: " + objArrSelected.length;
        objTabMobile.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_noelementsselected', 'Elements selected');?>: " + objArrSelected.length;
    }

    //figure out if all selected are of the same type designobject
    bSameDOs = true;
    for (let iIndex = 0; iIndex < iLenSelected; iIndex++)
    {
        if (iIndex > 0) //skip first
        {
            if (objSelectedElement != objArrSelected[iIndex]) //previous same as current?
            {
                bSameDOs = false;
                break;
            }
        }
        objSelectedElement = objArrSelected[iIndex];
    }

    //show only tab-content of 1 designobject when multiple of the same type are selected
    if (bSameDOs)
    {
        const objDivGridTabElementDesktop = document.createElement("div");
        const objDivGridTabElementMobile = document.createElement("div");
        objDivGridTabElementDesktop.className = "detailpanel-grid";
        objDivGridTabElementMobile.className = "detailpanel-grid";
        objTabDesktop.appendChild(objDivGridTabElementDesktop); 
        objTabMobile.appendChild(objDivGridTabElementMobile); 

        //render
        objSelectedElement.renderElementTab(objDivGridTabElementDesktop, objArrSelected); 
        objSelectedElement.renderElementTab(objDivGridTabElementMobile, objArrSelected); 
        objSelectedElement.renderToolbar(objToolbar, objArrSelected); 
    }
}

/**
 * updates the structure
 */
async function updateStructureTab()
{
    // debugger

    const objTabHeadDesktop = document.getElementById("tab-head-details-structure-desktop");
    const objTabContentDesktop = document.getElementById("tab-content-details-structure-desktop");
    const objTabHeadMobile = document.getElementById("tab-head-details-structure-mobile");
    const objTabContentMobile = document.getElementById("tab-content-details-structure-mobile");
    const arrDOOldStructure = document.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_STRUCTURE);
    var objTabTarget = null;

    //don't update if tab is not showing (saving system resources)
    if ((!objTabHeadDesktop.classList.contains("selected")) && (!objTabHeadMobile.classList.contains("selected")))
        return;

    //targeting which tab structure tab? Desktop or mobile?
    if (objTabHeadDesktop.classList.contains("selected"))
        objTabTarget = objTabContentDesktop;

    if (objTabHeadMobile.classList.contains("selected"))
        objTabTarget = objTabContentMobile;


    //remove existing event listeners (needs to be done, before wiping the tab)
    // removeEventListenersFromChildElements("click", objTabTarget, 100);
    //removeEventListenersAbortControllerArray(arrDOInStructureTabAbortControllers);
    const iLenDOOld = arrDOOldStructure.length;
    for (let iIndex = 0; iIndex < iLenDOOld; iIndex++)
    {
        objDragDrop.removeDraggable(arrDOOldStructure[iIndex]);
        objDragDrop.removeDroppable(arrDOOldStructure[iIndex]);
    }


    //reset content tabs
    objTabContentDesktop.innerHTML = ''; 
    objTabContentMobile.innerHTML = ''; 


    //create <UL>
    if (objDivDesignerContainer.childElementCount > 0) //only create when there are elements in the designer
        objTabTarget.appendChild(createNewUL(objDivDesignerContainer));

    //update "selected" css classes in Designer
    //updateDesignerSelected();

    /**
     * creates <ul> element with children.
     * This is called recursively.
     * @param {HTMLElement} objParentDesignerContainer 
     * @returns HTMLElement UL
     */
    function createNewUL(objParentDesignerContainer)
    {
        let objParentUL = null; //<ul>
        let objChildUL = null;
        let objLI = null; //child <li>
        let sTempName = ''; //temp name, which is also the index in associative array arrDesignObjectsLibrary
        let sDisplayText = '';//text in li-list
        let bItemsExist = false;
           
        objParentUL = document.createElement('ul');
        for (const objDOInDesigner of objParentDesignerContainer.children)  //DO = DesignObject
        {
            if (objDOInDesigner.classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER))  //only direct childs with classname
            {
                bItemsExist = true;
                objLI = document.createElement('li');
                sTempName = objDOInDesigner.dataset.designobjectjsclassname;

                //create AbortController
                arrDOInStructureTabAbortControllers[objDOInDesigner.id] = new AbortController();

                //dataset
                objLI.dataset.correspondingDesignObjectId = objDOInDesigner.id;
                objLI.dataset.designobjectjsclassname = objDOInDesigner.dataset.designobjectjsclassname;


                //class
                objLI.className = CSSCLASS_DESIGNOBJECT_STRUCTURE;

                //icon
                objLI.innerHTML = arrDesignObjectsLibrary[sTempName].sIconSVG;

                //show text of item            
                if (arrDesignObjectsLibrary[sTempName] instanceof DOText) //show text from text object
                {
                    sDisplayText = strip_tags(objDOInDesigner.textContent).trim();
                    objLI.innerHTML += "\"" + sDisplayText.substring(0, 50) + "\"";
                }
                else //otherwise just show name
                    objLI.innerHTML += arrDesignObjectsLibrary[sTempName].sTitle;

                //add event listener
                objLI.addEventListener("mousedown", e => 
                {                           
                    unselectAllDesignObjects();
                    unselectAllDesignObjectsStructureTab(objTabTarget);
                    // objDOInDesigner.classList.add("selected"); --> we do this in separate function, because it also selects all the parents

                    e.target.classList.add("selected");
                    updateDesignerSelected(objTabTarget);
                    updateToolbarAndElementTab();
                },{signal: arrDOInStructureTabAbortControllers[objDOInDesigner.id].signal});

                //selected?
                if (objDOInDesigner.classList.contains("selected"))
                    objLI.classList.add("selected");

                //add to drag-drop class (eventlisteners are in here)
                objDragDrop.addDraggable(objLI);                

                //recursively call itself
                objChildUL = createNewUL(objDOInDesigner);
                if (objChildUL)
                {                                    
                    //ALWAYS droppable when there are children (eventlisteners are in here)    
                    objDragDrop.addDroppable(objLI); 

                    objLI.appendChild(objChildUL);
                }
                else
                {
                    //also when there are no children, it can be a droppable when the DesignObject class itself indicates it is droppable (eventlisteners are in here)
                    if (arrDesignObjectsLibrary[sTempName].bDropTarget)
                        objDragDrop.addDroppable(objLI);     
                }

                //add
                objParentUL.appendChild(objLI);                
            }//end: ="designobject-indesigner"
        } //end: for

        if (bItemsExist)
            return objParentUL;
        else 
            return null;
    }//end createUL();
    
    /**
     * updates "selected" css classes in designer based on the "selected" classes of the structure
     */
    function updateDesignerSelected(objParentContainer)
    {
        const objDOSelected = objParentContainer.getElementsByClassName("selected");
        const iLenDoSelected = objDOSelected.length;
    
        for(let iIndex = 0; iIndex < iLenDoSelected; iIndex++)
        {
            sID = objDOSelected[iIndex].dataset.correspondingDesignObjectId;

            objHTMLElementInDesigner = document.getElementById(sID);
            if (objHTMLElementInDesigner) //when users clicks in the middle of 2 items, instead of ON an element, there is no element
                objHTMLElementInDesigner.classList.add("selected");            
        }
    }
  
}

/**
 * remove all "selected" classes from all objects in Structure Tab
 */
function unselectAllDesignObjectsStructureTab(objParentElement)
{
    removeClassFromChildElements("selected", objParentElement, "li", 50);
}

