
<?php
/**
 * pagebuilder-dragdrop.js
 *
 * The Javascript regarding to drag and drop
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
 * 1 jan 2025 pagebuilder-dragdrop.js created
 */
?>

 class PagebuilderDragDrop
 {

    arrDraggables = []; //array with items you want to be able to drag
    arrDroppables = []; //the drop containers where you can drop your draggables
    objDivDropCursorHorizontal = null; //the dropcursor indicates where an element is dropped when user drags
    
    sCSSClassSelected = "selected"; //indicates where the an element is going to be dropped
    sCSSClassDroppable = "droppable"; //indicates whether user is able to drop on this element
    sCSSClassDraggable = "draggable"; //indicates whether user is able to drag this element
    sCSSClassDragging = "dragging"; //indicates whether this element is currently dragged by the user
    sCSSClassAcceptDragging = "acceptdragging"; //indicates whether droppable is accepting dragging (then you can change the color with CSS)
    sCSSClassPlaceholder = "placeholder"; //indicates whether an element is a placeholder. A placeholder is something to ignore when dropping (it's not an element to determine order for, because it basically represents nothing)
    sCSSClassDropCursorHorizontal = "dropcursor-horizontal";
    sCSSClassDropCursorVertical = "dropcursor-vertical";

    //dragging status: make sure drag and and drop have exactly the same settings, so the dragging behavior always matches dropping behavior
    bDraggingDropOrInsert = false; //default = insert
    bDraggingInsertBeforeOrAfter = false; //default = before
    objDraggingNearestElement = null; //


    constructor()
    {
        this.objDivDropCursorHorizontal = document.createElement("div");
        this.objDivDropCursorHorizontal.className = this.sCSSClassDropCursorHorizontal;

        this.init();
    }

    /**
     * overload in child
     */
    init()
    {}

    /**
     * PUBLIC FUNCTION
     * 
     * @param {HTMLElement} objHTMLElement 
     */
    addDraggable(objHTMLElement)
    {
        //prepare for use in this class
        objHTMLElement.draggable = true;  
        objHTMLElement.classList.add(this.sCSSClassDraggable);   

        //add event listeners
        objHTMLElement.addEventListener("dragstart",    (objEvent) => this.handleDragStart(objEvent, objHTMLElement));  //() => lexically bind context, so "this" refers to original context   
        objHTMLElement.addEventListener("touchstart",   (objEvent) => this.handleDragStart(objEvent, objHTMLElement));   //() => lexically bind context, so "this" refers to original context  
        objHTMLElement.addEventListener("dragend",      (objEvent) => this.handleDragEnd(objEvent, objHTMLElement));    //() => lexically bind context, so "this" refers to original context        
        objHTMLElement.addEventListener("touchend",     (objEvent) => this.handleDragEnd(objEvent, objHTMLElement));    //() => lexically bind context, so "this" refers to original context        

        //add to internal array
        this.arrDraggables.push(objHTMLElement);
    }

    /**
     * PUBLIC FUNCTION
     * addDraggable() but accepts array with HTMLElement objects as parameter
     * 
     * @param {Array} arrHTMLElements of HTMLElement
     */
    addDraggableArray(arrHTMLElements)
    {
        const iLenArr = arrHTMLElements.length;

        for (let iIndex = 0; iIndex < iLenArr; iIndex++)
        {
            this.addDraggable(arrHTMLElements[iIndex]);
        }
    }

        

    /**
     * PUBLIC FUNCTION
     * 
     * @param {objHTMLElement} objHTMLElement 
     */
    addDroppable(objHTMLElement)
    {
        //prepare for use in this class
        // objHTMLElement.draggable = false;  //some droppables can be draggable too. containers for example
        objHTMLElement.classList.add(this.sCSSClassDroppable);

        //add event listeners
        objHTMLElement.addEventListener("dragover",    (objEvent) => this.handleDragging(objEvent, objHTMLElement));   //() => lexically bind context, so "this" refers to original context  
        objHTMLElement.addEventListener("touchmove",   (objEvent) => this.handleDragging(objEvent, objHTMLElement));     //() => lexically bind context, so "this" refers to original context
        objHTMLElement.addEventListener("drop",        (objEvent) => this.handleDrop(objEvent, objHTMLElement));   //() => lexically bind context, so "this" refers to original context
        
        //add to internal array
        this.arrDroppables.push(objHTMLElement);
    }    

    /**
     * PUBLIC FUNCTION
     * 
     * addDroppable() but accepts array with HTMLElement objects as parameter
     * 
     * @param {Array} arrHTMLElements of HTMLElement
     */
    addDroppableArray(arrHTMLElements)
    {
        const iLenArr = arrHTMLElements.length;

        for (let iIndex = 0; iIndex < iLenArr; iIndex++)
        {
            this.addDroppable(arrHTMLElements[iIndex]);
        }
    }

    /**
     * remove draggable from internal array and remove event listeners
     * 
     * @param {HTMLElement} objHTMLElement 
     */
    removeDraggable(objHTMLElement)
    {
        const iIndex = this.getIndexOfObject(objHTMLElement, this.arrDraggables);

        if (iIndex >= 0)
        {
            this.arrDraggables[iIndex].removeEventListener("dragstart",   (objEvent) => this.handleDragStart(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context
            this.arrDraggables[iIndex].removeEventListener("touchstart",  (objEvent) => this.handleDragStart(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context
            this.arrDraggables[iIndex].removeEventListener("dragend",     (objEvent) => this.handleDragEnd(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context
            this.arrDraggables[iIndex].removeEventListener("touchend",    (objEvent) => this.handleDragEnd(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context

            //recursively delete draggables and droppables from children
            let iLenChilds = objHTMLElement.childElementCount;
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++) 
                this.removeDraggable(objHTMLElement.children[iIndex]);
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++) //if there are droppables delete them too, because we have no way of keeping track of them after we've deleted them from the internal array
                this.removeDroppable(objHTMLElement.children[iIndex]);

            delete this.arrDraggables[iIndex];            
        }
    }

    /**
     * remove droppable from internal array and remove event listeners
     * 
     * @param {HTMLElement} objHTMLElement 
     */
    removeDroppable(objHTMLElement)
    {
        const iIndex = this.getIndexOfObject(objHTMLElement, this.arrDroppables);

        if (iIndex >= 0)
        {
            this.arrDroppables[iIndex].removeEventListener("dragover",  (objEvent) => this.handleDragging(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context
            this.arrDroppables[iIndex].removeEventListener("touchmove", (objEvent) => this.handleDragging(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context
            this.arrDroppables[iIndex].removeEventListener("drop",      (objEvent) => this.handleDrop(objEvent, this.arrDraggables[iIndex])); //() => lexically bind context, so "this" refers to original context

            //recursively delete draggables and droppables from children
            let iLenChilds = objHTMLElement.childElementCount;
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++) //if there are draggables delete them too, because we have no way of keeping track of them after we've deleted them from the internal array
                this.removeDraggable(objHTMLElement.children[iIndex]);
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++)
                this.removeDroppable(objHTMLElement.children[iIndex]);

            delete this.arrDroppables[iIndex];        
        }
    }




    /**
     * when dragging starts
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDraggable item that has the event listener
     */
    handleDragStart(objEvent, objDraggable)
    {        
        //declarations
        const arrElements = this.getSelectedElementsToDrag(objDraggable);        
        const iLenEl = arrElements.length;  


        //assign dragging CSS class
        for (let iIndex = 0; iIndex < iLenEl; iIndex++)
        {            
            arrElements[iIndex].classList.add(this.sCSSClassDragging);       
            // console.log("assign CSS dragging class to " + arrElements[iIndex] + ":"); 
            // console.log(arrElements[iIndex])
        }

        // console.log("handleDragStart class: start =================");
    }

    /**
     * when dragging ends (can be dropping or cancelled)
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDraggable 
     */    
    handleDragEnd(objEvent, objDraggable)
    {
        //declarations
        const arrElements = [...document.getElementsByClassName(this.sCSSClassDragging)];
        const iLenEl = arrElements.length;
        // const iLenDroppables = this.arrDroppables.length;
 

        //remove indication that target accepts drop or not
        for (let iIndex = 0; iIndex < iLenEl; iIndex++)
        {            
            arrElements[iIndex].classList.remove(this.sCSSClassDragging); //remove dragging CSS class        
        }

        //remove indication accept drag color CSS class 
        this.removeCSSClassAcceptDraggingFromDroppables();
        objEvent.target.classList.remove(this.sCSSClassAcceptDragging);

        
        //remove cursor from droppables hovering over with the mouse
        this.objDivDropCursorHorizontal.remove();


        console.log("DragDrop class: handleDragEnd ===============1234");
    }

    /**
     * when dragging occurs
     * 
     * objEvent.target and objDroppable can be the same, but not necessarily.
     * I can drag on another sibling element. This will change the target, but not the droppable
     * 
     * the big challenge is to overcome is the CSS margins on elements (like: margin-top:10px):
     * ==> When dragging on the margin of an element, the objEvent.target is the parent droppable
     * ==> Witout margins (thus hovering over sibling elements), will make the objEvent.target the sibling element
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDroppable
     */
    handleDragging(objEvent, objDroppable)
    {        
        //cancel parent drops, only continue with the children
        //this method is on this position (above declarations) for perfomance reasons, 
        //we waste cpu cycles by questioning the DOM
        //are there children that are droppable? than cancel this function, because the child needs to handle the drop
        // const objChildrenDroppable = objDroppable.querySelectorAll('.'+ this.sCSSClassDroppable);
        // console.log('objChildrenDroppable:');
        // console.log(objChildrenDroppable);
        // if (objChildrenDroppable.length > 0)
        //     return;


        //declarations
        const objTargetDroppable = this.getFirstParentDroppable(objEvent.target, true); //avoid dropping on "+" for example
        
        

        //declarations
        const arrDragSources = [...document.querySelectorAll('.'+ this.sCSSClassDragging)];            
        const arrSiblingDraggables = this.getSilblingDraggables(objDroppable);

        //update dragging status
        this.bDraggingDropOrInsert = this.getDropOrInsertElement(objEvent, objDroppable, arrDragSources);
        this.objDraggingNearestElement = this.getNearestElement(arrSiblingDraggables, objEvent.clientX, objEvent.clientY);
        this.bDraggingInsertBeforeOrAfter = this.getDraggingInsertBeforeOrAfter(this.objDraggingNearestElement, objEvent.clientX, objEvent.clientY);


        if (this.bDraggingDropOrInsert)
        {           
            //indicate with CSS that target accepts drop or not
            this.removeCSSClassAcceptDraggingFromDroppables();
            objTargetDroppable.classList.add(this.sCSSClassAcceptDragging);

            //remove cursor from droppables hovering over with the mouse
            this.objDivDropCursorHorizontal.remove();
            
            // console.log("this.bDraggingDropOrInsert DROP ================ #childs:" + objTargetDroppable.childElementCount);
            // console.log(objTargetDroppable);

            objEvent.preventDefault(); //change cursor

        }
        else //some elements can be both draggable and droppable, but the droppable takes PRECEDENCE OVER draggable. when it's not a droppable, we going to look at draggables (this way we exclude the ones that are draggables AND droppables at the same time)
        {

            //indicate with CSS that parent droppable accepts drop or not
            this.removeCSSClassAcceptDraggingFromDroppables(); 
            if ((arrDragSources[0].parentElement != objEvent.target.parentElement) && (arrDragSources[0].parentElement != objEvent.target)) //ignore indication when dropping on 1. it is NOT a sibling (=same parent) (no margins) 2. when target is NOT parent (with margins)
                objDroppable.classList.add(this.sCSSClassAcceptDragging); 


            if (this.objDraggingNearestElement)
            {
                if (this.bDraggingInsertBeforeOrAfter)
                {
                    this.objDraggingNearestElement.before(this.objDivDropCursorHorizontal);
                }
                else
                {
                    this.objDraggingNearestElement.after(this.objDivDropCursorHorizontal);
                }
            }    

            objEvent.preventDefault(); //change cursor
        
            // console.log("this.bDraggingDropOrInsert INSERT ==============ABC");
        }


        // console.log("DragDrop class: handleDragging ==============ABC");
    }


    /**
     * when dropping occurs
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDroppable 
     */    
    handleDrop(objEvent, objDroppable)
    {
        //declarations
        const objTargetDroppable = this.getFirstParentDroppable(objEvent.target, true);

        //cancel parent drops, only continue with the first parent of objEvent.target
        //this method is on this position (above declarations) for perfomance reasons, 
        //we waste cpu cycles by questioning the DOM
        // if (objDroppable != objTargetDroppable)
        //     return;

        //rest of declarations
        const arrDragSources = [...document.querySelectorAll('.'+ this.sCSSClassDragging)];            
        const iLenDragSources = arrDragSources.length;
        const objDivAddPanelDragtarget = document.getElementById("panel-add-dragtarget");
        const objDivStructureDesktop = document.getElementById("tab-content-details-structure-desktop");
        const objDivStructureMobile = document.getElementById("tab-content-details-structure-mobile");
        let sDOJSClassName = "";
        let iDivID = "";
        let objDO = null;
        let arrSiblingDraggables = this.getSilblingDraggables(objDroppable); //sibling elements of draggable to determine order
        let objNearestElement = null;

        // console.log("handleDrop(): arrDragSourcesarrDragSourcesarrDragSourcesarrDragSources:");
        // console.log(arrDragSources);

        
        //====handle drop on add panel (always accessible without getDropOrInsertElement() deciding)
        if (objDroppable == objDivAddPanelDragtarget)
        {
            for (let iIndex = 0; iIndex < iLenDragSources; iIndex++)
            {            
                addDesignObjectToDesigner(arrDragSources[iIndex]);
            }

            updateStructureTab();
            console.log("handled drop from " + arrDragSources[0].dataset.designobjectjsclassname);
            return; //SKIP THE REST!!!!
        }
    

        
        //drop on container
        if (this.bDraggingDropOrInsert)
        {

            //remove children placeholders in droppable (only at the direct parent level, not deeper because otherwise you can remove all placeholders in whole document when droppable is designerContainer)
            let iLenPlaceholders = objTargetDroppable.childElementCount;
            for (let iIndex = 0; iIndex < iLenPlaceholders; iIndex++)
            {
                if (objTargetDroppable.children[iIndex].classList.contains(this.sCSSClassPlaceholder))
                {
                    objTargetDroppable.children[iIndex].remove();
                    iLenPlaceholders--;
                }
            }

            //add draggables
            for (let iIndex = 0; iIndex < iLenDragSources; iIndex++)
            {            
                objTargetDroppable.appendChild(arrDragSources[iIndex]);
            }

            // console.log("DragDrop class: handleDrop() child: drop ===============");
            updateStructureTab();
        }
        else //or insert, then we need to figure out the nearest element
        {

            
            //====handle drops on designer container            
            if (objDroppable == objDivDesignerContainer)
            {
                for (let iIndex = 0; iIndex < iLenDragSources; iIndex++)
                {
                    objDO = null; //default

                    //determine where drag came from (create tab, designer or structure tab)
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_CREATETAB)) //CREATE TAB
                    {
                        sDOJSClassName = arrDragSources[iIndex].dataset.designobjectjsclassname;
                        objDO = arrDesignObjectsLibrary[sDOJSClassName].cloneNode(true);
                        objDO.renderDesigner();                        
                    }
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER)) //DESIGNER ITSELF
                    {
                        objDO = arrDragSources[iIndex];
                    }
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_STRUCTURE)) //STRUCTURE TAB
                    {
                        iDivID = arrDragSources[iIndex].dataset.correspondingDesignObjectId;
                        // console.log(arrDragSources[iIndex]);
                        // console.log(iDivID);
                        objDO = document.getElementById(iDivID);
                    }

                    //do actions in designer, we update the structure tab later
                    if (this.objDraggingNearestElement)
                    {
                        if (this.getDraggingInsertBeforeOrAfter(this.objDraggingNearestElement, objEvent.clientX, objEvent.clientY))
                        {
                            this.objDraggingNearestElement.before(objDO);
                        }
                        else
                        {
                            this.objDraggingNearestElement.after(objDO);
                        }
                
                    }    

                    // console.log("handled drop on DESIGNER " + sDOJSClassName);
                }

                updateStructureTab();    
                
                //make editable if need be
                if (iCursorMode == CURSORMODE_SELECTION)
                    setCursorModeSelection();
                if (iCursorMode == CURSORMODE_TEXTEDITOR)
                    setCursorModeTextEditor();                      
            }
            

            //====handle drop on structure tab
            if ((objDroppable == objDivStructureDesktop) || (objDroppable == objDivStructureMobile))
            {

                for (let iIndex = 0; iIndex < iLenDragSources; iIndex++)
                {
                    objDO = null; //default

                    //determine where drag came from (create tab, designer or structure tab)
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_CREATETAB)) //CREATE TAB
                    {
                        sDOJSClassName = arrDragSources[iIndex].dataset.designobjectjsclassname;
                        objDO = arrDesignObjectsLibrary[sDOJSClassName].cloneNode(true);
                        objDO.renderDesigner();
                    }
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER)) //FROM DESIGNER
                    {
                        iDivID = arrDragSources[iIndex].id;
                        objDO = document.getElementById(iDivID);
                    }
                    if (arrDragSources[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_STRUCTURE)) //STRUCTURE TAB ITSELF
                    {
                        iDivID = arrDragSources[iIndex].dataset.correspondingDesignObjectId;
                        objDO = document.getElementById(iDivID);
                        
                    }

                    //do actions in designer, we update the structure tab later
                    if (this.objDraggingNearestElement)
                    {
                        iDivID = this.objDraggingNearestElement.dataset.correspondingDesignObjectId; //nearest element is in structure tab, not in designer
                        const objNearestElementInDesigner = document.getElementById(iDivID);

                        if (objNearestElementInDesigner)
                        {
                            if (this.bDraggingInsertBeforeOrAfter)
                            {
                                objNearestElementInDesigner.before(objDO);
                            }
                            else
                            {
                                objNearestElementInDesigner.after(objDO);
                            }
                        }
            
                    }    

                    // console.log("handled: drop on structure tab");
                }

                updateStructureTab();

                //make editable if need be
                if (iCursorMode == CURSORMODE_SELECTION)
                    setCursorModeSelection();
                if (iCursorMode == CURSORMODE_TEXTEDITOR)
                    setCursorModeTextEditor();                
            }    

            // console.log("DragDrop class: handleDrop() child: insert ===============");
        }

        console.log("DragDrop class: handleDrop() child ===============");
    }    
        

    /**
     * returns HTML element which mouse cursor is closest to
     * 
     * @param {HTMLElement} objDroppable
     * @param {Array} arrHTMLElements of HTMLElements. available elements that can be nearest (to determine order in between the other existing elements)
     * @param {int} fXMousePosition in pixels
     * @param {int} fYMousePosition in pixels
     * @return {HTMLElement}
     */
    getNearestElement(arrHTMLElements, fXMousePosition, fYMousePosition)
    {
        let iLenDo = arrHTMLElements.length;
        let objNearestElement = null;
        let iXCurrMouseDiff = 0;
        let iYCurrMouseDiff = 0;
        let iXSmallestDiff = Number.POSITIVE_INFINITY;
        let iYSmallestDiff = Number.POSITIVE_INFINITY;
        let iXCenterCurrEl = 0;//center point of current element
        let iYCenterCurrEl = 0;//center point of current element
        let rctCurrRect = null;
        let arrChildren = [];
        let iLenChildren = 0;

        if (fXMousePosition === undefined)
        {
            console.log("getNearestElement(): parameter fXMousePosition is undefined");
            return null;
        }
        if (fYMousePosition === undefined)
        {
            console.log("getNearestElement(): parameter fYMousePosition is undefined");
            return null;
        }

        const iXMousePosition = Math.round(fXMousePosition);
        const iYMousePosition = Math.round(fYMousePosition);    

        if (iLenDo > 0)
        {
            objNearestElement = arrHTMLElements[0];//default assume first element, so we can compare

            for (let iIndex = 0; iIndex < iLenDo; iIndex++)
            {
                if (arrHTMLElements[iIndex] != this.objDivDropCursorHorizontal) //exclude the cursor
                {
                    rctCurrRect = arrHTMLElements[iIndex].getBoundingClientRect();

                    //determine the center coordinates
                    iXCenterCurrEl = Math.round(rctCurrRect.left + (rctCurrRect.width / 2));
                    iYCenterCurrEl = Math.round(rctCurrRect.top + (rctCurrRect.height / 2));

                    //determine difference
                    iXCurrMouseDiff = iXMousePosition - iXCenterCurrEl;
                    if (iXCurrMouseDiff < 0) //if negative number, convert to positive --> only compare integers, not floats
                        iXCurrMouseDiff = iXCurrMouseDiff * -1;
                    iYCurrMouseDiff = iYMousePosition - iYCenterCurrEl;
                    if (iYCurrMouseDiff < 0) //if negative number, convert to positive --> only compare integers, not floats
                        iYCurrMouseDiff = iYCurrMouseDiff * -1;

                    //if current difference is smaller than known smallest difference, update smallest difference
                    if (iYCurrMouseDiff <= iYSmallestDiff) //first look at Y-coordinate --> only compare integers, not floats
                    {
                        if (iXCurrMouseDiff <= iXSmallestDiff) //then look at X-coordinate --> only compare integers, not floats
                        {
                            iXSmallestDiff = iXCurrMouseDiff;
                            iYSmallestDiff = iYCurrMouseDiff;
                            objNearestElement = arrHTMLElements[iIndex];
                        }
                    }                    
                    
                    //check children recursively 
                    /*
                    arrChildren = this.getChildDraggables(objNearestElement);
                    iLenChildren = arrChildren.length;
                    if (iLenChildren > 0)
                    {
                        console.log("child draggables:     sdfsdfsdfs count:"+arrChildren.length+ " parentid:" + objNearestElement.dataset.correspondingDesignObjectId);
                        console.log(arrChildren);                       
                        objNearestElement = this.getNearestElement(arrChildren, fXMousePosition, fYMousePosition);
                    }
                    */
                }
            }
        }

        return objNearestElement;
    }



    /**
     * Determines whether to drop before or after objNearestElement
     * (it looks at the Y position of the mouse compared to the center of objNearestElement)
     * 
     * @param {HTMLElement} objNearestElement 
     * @param {int} iXMousePosition 
     * @param {int} iYMousePosition 
     * @return {bool} true=before, false=after
     */
    getDraggingInsertBeforeOrAfter(objNearestElement, fXMousePosition, fYMousePosition)
    {
        if ((objNearestElement === undefined) || (objNearestElement === null))
        {
            console.log("getDragDropBeforeElement(): parameter objNearestElement is undefined or null");
            return null;
        }
        if (fXMousePosition === undefined)
        {
            console.log("getDragDropBeforeElement(): parameter fXMousePosition is undefined");
            return null;
        }
        if (fYMousePosition === undefined)
        {
            console.log("getDragDropBeforeElement(): parameter fYMousePosition is undefined");
            return null;
        }

        
        const rctCurrRect = objNearestElement.getBoundingClientRect();
        // const iXMousePosition = Math.round(fXMousePosition);
        const iYMousePosition = Math.round(fYMousePosition);

        //determine the center coordinates
        // const iXCenterCurrEl = rctCurrRect.left + (rctCurrRect.width / 2);
        const iYCenterCurrEl = rctCurrRect.top + (rctCurrRect.height / 2);    

        if (Math.round(iYCenterCurrEl) < Math.round(iYMousePosition)) //don't compare floats
            return false;
        else
            return true;

        return true;
    }
        

    /**
     * PRIVATE FUNCTION
     * 
     * helper function to find indexes of objects 
     * 
     * @param {Object} objObject 
     * @param {Array} arrArray 
     * @returns {integer} index of object. -1 means: not found
     */
    getIndexOfObject(objObject, arrArray)
    {
        const iLenArr = arrArray.length;

        for (let iIndex = 0; iIndex < iLenArr; iIndex++)
        {
            if (arrArray[iIndex] == objObject)
            {
                return iIndex;
            }
        }

        return -1;
    }  

    /**
     * is user allowed to drop?
     * 
     * please override this function in child to define conditions
     * by default the drop is always allowed
     * 
     * @param {Event} objEvent
     * @param {Array} of HTMLElement objects that are currently dragging
     * @param {HTMLElement} objDroppable droppable object from arrDroppables
     * @returns {bool}
     */
    isDropAllowed(objEvent, arrDraggableElements, objDroppable)
    {

        let sDOName = "";
        const objTargetDroppable = this.getFirstParentDroppable(objEvent.target, true);
        // console.log("objTargetDroppable handleklaphoerheer");
        // console.log(objTargetDroppable);


        // console.log("check PagebuilderDragDrop.isDropAllowed() =====================****");        

        if (objTargetDroppable == objDivDesignerContainer)
        {
            // console.log("check PagebuilderDragDrop.isDropAllowed(): (objTarget == objDivDesignerContainer) =====================****");        
            return true;
        }
        else        
        {


            sDOName = objTargetDroppable.dataset.designobjectjsclassname;
            // console.log("check PagebuilderDragDrop.isDropAllowed(): objTarget not designercontainer " + sDOName);
            // console.log(objDroppable);

            return arrDesignObjectsLibrary[sDOName].isDropAllowed(arrDraggableElements, objDroppable, objTargetDroppable);
        }

        return false;
    }


    /**
     * returns array of HTMLElement to drag
     * 
     * please override this function in child to define more complex conditions
     * by default this function selects elements that have css class "selected"
     * 
     * @param {HTMLElement} objDraggable item that has the eventlistener (this way you can determine the type or origin)
     * @returns {Array} of HTMLElement
     */
    getSelectedElementsToDrag(objDraggable)
    {
        if (objDraggable.classList.contains(CSSCLASS_DESIGNOBJECT_CREATETAB))
            return [...document.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_CREATETAB + ".selected")];
        if (objDraggable.classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER))
            return [...document.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_DESIGNER + ".selected")];
        if (objDraggable.classList.contains(CSSCLASS_DESIGNOBJECT_STRUCTURE))
            return [...document.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_STRUCTURE + ".selected")];
    }

    /**
     * get the sibling draggables when dragging on objDroppable
     * only nessesary when multiple droppables
     * this is needed to prevent annoying dragcursor flickering between droppables
     * by default arrDraggables is returned
     * 
     * please override this function in child to specify details
     * 
     * @param {HTMLElement} objDroppable from arrDroppable
     * @return {Array} array of draggables
     */
    getSilblingDraggables(objDroppable)
    {
        const objDivAddPanelDragtarget = document.getElementById("panel-add-dragtarget");
        const objDivStructureDesktop = document.getElementById("tab-content-details-structure-desktop");
        const objDivStructureMobile = document.getElementById("tab-content-details-structure-mobile");

        //on add-panel
        if (objDroppable == objDivAddPanelDragtarget) 
            return [];

        //on designer
        if (objDroppable == objDivDesignerContainer) 
            return [...objDivDesignerContainer.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_DESIGNER + ":not(." + this.sCSSClassDragging + ")")]; //other elements

        //on structure tab
        if ((objDroppable == objDivStructureDesktop) || (objDroppable == objDivStructureMobile)) 
        {
            return [...document.querySelectorAll("." + CSSCLASS_DESIGNOBJECT_STRUCTURE + ":not(." + this.sCSSClassDragging + ")")]; //other elements
        }

        return [];
    }    


    /**
     * PRIVATE FUNCTION
     * 
     * removes CSS class this.sCSSClassAcceptDragging from all droppables
     */
    removeCSSClassAcceptDraggingFromDroppables()
    {
        const iLenDroppables = this.arrDroppables.length;
        for (let iIndex = 0; iIndex < iLenDroppables; iIndex++)
        {    
            this.arrDroppables[0].classList.remove(this.sCSSClassAcceptDragging);    
        }
    }

    /**
     * PRIVATE FUNCTION
     * 
     * get childElementCount
     * @param {HTMLElement} objHTMLElement
     */
    getChildElementCountWithoutPlaceholders(objHTMLElement)
    {
        if (objHTMLElement.childElementCount == 0) //no children? not necessary to check
            return 0;

        const iLenChildren = objHTMLElement.childElementCount;
        let iCountChildrenWithoutPlaceholders = 0

        for (let iIndex = 0; iIndex < iLenChildren; iIndex++)
        {
            if (!objHTMLElement.children[iIndex].classList.contains(this.sCSSClassPlaceholder))
                iCountChildrenWithoutPlaceholders++;
        }

        return iCountChildrenWithoutPlaceholders;
    }

    /**
     * PRIVATE FUNCTION
     * 
     * Drop on element, or insert it?
     * determine the logic whether to drop an element or insert it
     * 
     * objEvent.target and objDroppable can be the same, but not necessarily.
     * I can drag on another sibling element. This will change the target, but not the droppable
     * 
     * @param {Event} objEvent
     * @param {HTMLElement} objDroppable
     * @param {Array} array of HTMLElement objects
     * @return true = drop, false = insert
     */
    getDropOrInsertElement(objEvent, objDroppable, arrDragSources)
    {
        const objTargetDroppable = this.getFirstParentDroppable(objEvent.target, true);

        //it's a drop when:
        if (
            objTargetDroppable.classList.contains(this.sCSSClassDroppable) //it has droppable class
            &&
            (this.getChildElementCountWithoutPlaceholders(objTargetDroppable) == 0) //is has no elements (except placeholders)
            &&
            this.isDropAllowed(objEvent, arrDragSources, objDroppable) //drop is allowed            
        )        
        {
            return true;
        }
     

        return false; //do insert
    }


    /**
     * return the first parent element that is a droppable  (has droppable cSS class)
     * 
     * @param {HTMLElement} objHTMLElement 
     * @param {boolean} bIncludeCurrent also searches current node for droppable class 
     * @param {integer} iMaxDepth  (because of recursion limit depth)
     * @returns HTMLElement or null when max depth exceeded or no parents left
     */
    getFirstParentDroppable(objHTMLElement, bIncludeCurrent = true, iMaxDepth = 10)
    {
        //if depth exceeded, return null
        if (iMaxDepth <= 0)
            return null;
        
        //search current
        if (bIncludeCurrent)
        {
            if (objHTMLElement.classList.contains(this.sCSSClassDroppable))
            {
                return objHTMLElement;
            }
        }

        //parent null?
        if (objHTMLElement.parentElement == null)
            return null;

        //search parents
        if (objHTMLElement.parentElement.classList.contains(this.sCSSClassDroppable))
            return objHTMLElement.parentElement;
        else
            return this.getFirstParentDroppable(objHTMLElement.parentElement, false, iMaxDepth--);
    }
    
 }

