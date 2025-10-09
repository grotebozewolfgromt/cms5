<?php 
/**
 * dr-dragdrop.js
 *
 * class to ease dragging and dropping with javascript
 * 
 * TERMINOLOGY:
 * - draggables: html elements that can be dragged
 * - droppables: html elements that are potential drop areas for a draggable to be dropped onto
 * - drag cursor: an html element that is created when dragging to indicate where elements will be dropped when releasing the mouse button
 * 
 * HOW TO USE:
 * - all draggables and droppables must exist within <dr-drag-drop> tag
 *      --> if you want to add draggables and droppables manually, use addDraggable() and addDroppable();
 * - add css class "draggable" to each element to be draggable, like <div class="draggable">foo</div> (the classname is changeable by changing sCSSClassDraggable)
 *      (this class will add the "draggable=true" html-tag-attribute automatically)
 * - add css class "droppable" to each element that can be used as a drop area, like <div class="droppable"></div> (the classname is changeable by changing sCSSClassDroppable)
 * - this class will adds css class "dragging" when an element is currently being dragged automatically (the classname is changeable by changing sCSSClassDragging)
 * - to allow only some draggables only on certain droppables, inherit this class and override isDropElementAllowed()
 * - an element can be both draggable and droppable (this way you can drag elements onto other elements)
 * 
 * ATTRIBUTES
 * - "type". the type of drag and drop. either "div" (=default) or "table"
 * - "dropbehavior" the drop behavior. either "child" (=default), "after" or "none"
 * 
 * EVENTS
 * Dispatches the following CustomEvent():
 * - "dr-drop" (it's called "dr-drop", because "drop" already exists)
 * 
 * WARNING:
 * -when draggable/droppable components are changed inside this html tag (i.e. added) you need to call reloadDraggablesDroppables(), for example in an event listener
 * -only draggables and droppables INSIDE this html tag are recognized automatically
 *      1. for performance reasons 
 *      2. this way you can have multiple <dr-drag-drop> tags with each their own draggables and droppables in one document
 * 
 * EXAMPLE:
 * <dr-drag-drop type="div">
 *      <div class="droppable" >
 *          <div class="draggable">1=====</div>
 *          <div class="draggable">2=====</div>
 *          <div class="draggable">3=====</div>
 *          <div class="draggable">4=====</div>
 *      </div>
 * </dr-drag-drop>
 * 
 * EXAMPLE 2: table where rows can be dropped ON and AFTER other rows
 * <dr-drag-drop type="table" dropbehavior="after">
 *      <table>
 *          <tbody class="droppable">
 *              <tr class="draggable droppable">
 *                  <td>line A cell 1</td>
 *                  <td>line A cell 2</td>
 *              </tr>
 *              <tr class="draggable droppable">
 *                  <td>line B cell 1</td>
 *                  <td>line B cell 2</td>
 *              </tr>
 *          </tbody>
 * </dr-drag-drop>
 *  
 * 
 * 
 * @author Dennis Renirie
 * 
 * 
 * 18 july 2025 dr-dragdrop.js created
 * 19 july 2025 dr-dragdrop.js added comments
 * 19 july 2025 dr-dragdrop.js changed updateUI() -> reloadDraggablesDroppables
 * 19 july 2025 dr-dragdrop.js ONLY uses draggables and droppables inside the html tag <dr-drag-drop>
 * 19 july 2025 dr-dragdrop.js isDropElementAllowed() function and functionality added
 * 25 july 2025 dr-dragdrop.js bugfix: met drop-drop eerste element wanneer draggable ook droppable is
 * 25 july 2025 dr-dragdrop.js attribute "behavior" now called "dropbehavior"
 * 
 */
?>


class DRDragDrop extends HTMLElement
{
    static sTemplate = `
        <style>
        </style>
        <slot></slot>
    `;

    #bConnectedCallbackHappened = false;
    #objAbortController = null;
    #arrDraggables = [];                //internal administration of draggables
    #arrDroppables = [];                //internal administration of droppables
    sCSSClassDraggable = "draggable";   //css class that marks an html element as DRAGGABLE
    sCSSClassDroppable = "droppable";   //css class that marks an html element as DROPPABLE
    sCSSClassDragging = "dragging";     //css class that marks an html element as CURRENTLY BEING DRAGGED
    sCSSClassDragCursor = "dragcursor"; //css class that marks an html element as DRAG CURSOR
    sCSSClassDropTarget = "droptarget"; //css class that marks an html element as DROP TARGET
    #objDragCursor = null;              //the object of the element that represents the drag cursor. this only exists when currently dragging, otherwise it's null
    arrTypes = { div: "div", table: "table"}; //is it a div or table which we are going to drag and drop
    #sType = this.arrTypes.div;
    arrDropBehaviors = { child: "child", after: "after", none: "none"}; //CHILD=insert as child element of drop target, AFTER=insert after current drop target element, NONE=do nothing (used for assigning your own custom behavior via event listener)
    #sDropBehavior = this.arrDropBehaviors.none;         
    #objDropOnElement = null;            //the object created when dragging that indicates where is being dropped ON when releasing mouse button (ONLY when draggable is also droppable)     
    #objDropAfterElement = null;         //the object created when dragging that indicates where is being dropped AFTER on when releasing mouse button                                       
    #fBorderMargin = 8.0;                //when dragging & dropping this border margin is taken into account. 5px margin means that within 5 px from the border it doesn't see it as intersecting. This makes it easier for a user to drag AFTER/BEFORE an element when dragging ON an element is also possible (you don't have to be super precise when dragging & dropping)

    /**
     * 
     */
    constructor()
    {
        super();

        this.#objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRDragDrop.sTemplate;

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);        
    }    

    #readAttributes()
    {
        this.#sType = DRComponentsLib.attributeToString(this, "type", this.#sType);
        this.#sDropBehavior = DRComponentsLib.attributeToString(this, "dropbehavior", this.#sDropBehavior);
    }

    populate()
    {
    }


    updateUI()
    {
    }

    /**
     * when components are changed (like: added, replaced etc) you need to run reloadDraggablesDroppables().
     * this ensures this class OVERWRITES (!!!) its internal administration of draggables and droppables
     * You can do this by attaching event listers to components that call this function
     * 
     * WARNING: draggables and droppables are refreshed. If you added them manually, you need to add them again!
     */    
    reloadDraggablesDroppables()
    {
        this.#arrDraggables = [...this.querySelectorAll("." + this.sCSSClassDraggable)];
        this.#arrDroppables = [...this.querySelectorAll("." + this.sCSSClassDroppable)];

        //remove event listeners (they will be added later)
        this.#objAbortController.abort();
        this.#objAbortController = new AbortController();        

        //add draggables
        this.#arrDraggables.forEach(objDraggable => 
        {
            // objDraggable.setAttribute("draggable", true);
            this.addDraggable(objDraggable);
        });

        this.#arrDroppables.forEach(objDroppable => 
        {
            // objDraggable.setAttribute("draggable", true);
            this.addDroppable(objDroppable);
        });
    }

    /**
     * adds a draggable object manually
     * 
     * @param {HTMLElement} objDraggable
     */
    addDraggable(objDraggable)
    {
        objDraggable.setAttribute("draggable", true);
        this.addEventListenersDraggable(objDraggable);

        this.#arrDraggables.push(objDraggable);
    }

    /**
     * adds a droppable object manually
     * 
     * @param {HTMLElement} objDroppable
     */
    addDroppable(objDroppable)
    {
        this.addEventListenersDroppable(objDroppable);

        this.#arrDroppables.push(objDroppable);
    }


    /**
     * attach event listeners for THIS object
     *
     */
    // addEventListeners()
    // {
            //these are added on a draggable/droppable basis on addDroppable() and addDraggable()
    // }

    /**
     * adds event listeners for 1 draggable
     * 
     * @param {HTMLElement} objDraggable 
     */
    addEventListenersDraggable(objDraggable)
    {
        //start dragging
        objDraggable.addEventListener("dragstart", (objEvent)=>
        {
            objDraggable.classList.add(this.sCSSClassDragging);

            //add dragcursor
            this.#objDragCursor = this.createDragCursor();

        }, { signal: this.#objAbortController.signal });  

        //end dragging
        objDraggable.addEventListener("dragend", (objEvent)=>
        {
            objDraggable.classList.remove(this.sCSSClassDragging);

            //remove css class droptarget
            this.#removeCSSDropTarget();

            //remove dragcursor
            if (this.#objDragCursor !== null)
                this.#objDragCursor.remove();
            this.#objDragCursor = null;


        }, { signal: this.#objAbortController.signal });     
        
        //when the draggable is ALSO a droppable (in other words: you can drop elements onto other elements)
        // if (objDraggable.classList.contains(this.sCSSClassDroppable))
        // {
        //     objDraggable.addEventListener("dragover", (objEvent)=>
        //     {
        //         console.log("dropcursor: drop onto other element");

        //     }, { signal: this.#objAbortController.signal });    
        // }
    }

    /**
     * adds event listeners for 1 droppable
     * 
     * @param {HTMLElement} objDroppable 
     */
    addEventListenersDroppable(objDroppable)
    {
        //dragover
        objDroppable.addEventListener("dragover", (objEvent)=>
        {
            const arrDraggingElements   = [...this.querySelectorAll("." + this.sCSSClassDragging)];
            let bDropAllowed            = false;
            this.#objDropOnElement       = null;
            this.#objDropAfterElement    = this.#getElementAfter(this.#arrDraggables, objEvent.clientX, objEvent.clientY);

            //determine drop-on element
            if (objDroppable.classList.contains(this.sCSSClassDraggable)) //can drop on other draggables?
            {
                if (this.#getElementDraggableDropTarget(objDroppable, objEvent.clientX, objEvent.clientY))
                    this.#objDropOnElement = objDroppable;
            }


            //prevent double triggering "dragover" with other elements that are nested 
            objEvent.stopPropagation();
            
            //check if drop is allowed to at least 1 item
            arrDraggingElements.forEach(objDraggingElement => 
            {       
                if (this.isDropElementAllowed(objDraggingElement, objDroppable))
                    bDropAllowed = true;
            });


     
            //drop allowed? Proceed by adding drag cursor
            if (bDropAllowed)
            {
                objEvent.preventDefault(); //prevents showing "cancel"-icon when allowed

                if (this.#objDropOnElement !== null) //dropping on element?
                {
                    //remove drag cursor
                    this.#objDragCursor.remove();
                    
                    //remove css class droptarget
                    this.#removeCSSDropTarget();

                    //add css class droptarget                    
                    objDroppable.classList.add(this.sCSSClassDropTarget);
                }
                else //insert after
                {
                    //remove css class droptarget
                    this.#removeCSSDropTarget();

                    if (this.#objDropAfterElement == null) //if objAfterElement NOT exists
                    {
                        // objDroppable.insertBefore(this.#objDragCursor, objDroppable.firstChild);//insert as first element  --> this doesn't work when the first draggable is also a droppable
                        this.#arrDroppables[0].parentNode.insertBefore(this.#objDragCursor, this.#arrDroppables[0]);//we take the first droppable in the list and insert it before
                    }
                    else //if objAfterElement exists
                    {
                        this.#objDropAfterElement.after(this.#objDragCursor); //add after existing element
                    }
                }
            }
            // console.log("dragovert", objAfterElement, objValidDraggables);
        }, { signal: this.#objAbortController.signal });    



        //drop it like it's hot
        objDroppable.addEventListener("drop", (objEvent)=>
        {
            // const this.#objDropAfterElement = this.#getElementAfter(this.#arrDraggables, objEvent.clientX, objEvent.clientY);
            const arrDraggingElements = [...this.querySelectorAll("." + this.sCSSClassDragging)];

            //prevent double triggering "drop" with other elements that are nested 
            objEvent.stopPropagation();

            arrDraggingElements.forEach(objDraggingElement => 
            {
                if (this.#objDropOnElement) //drop ON element
                {
                    if (this.#sDropBehavior == this.arrDropBehaviors.after)
                    {
                        // console.log("drop ON AFTER", this.#objDropOnElement);
                        this.#objDropOnElement.after(objDraggingElement);
                    }
                    else if (this.#sDropBehavior == this.arrDropBehaviors.child)
                    {
                        // console.log("drop ON CHILD", this.#objDropOnElement);
                        this.#objDropOnElement.appendChild(objDraggingElement);
                    }
                    else //default: do nothing  this.arrDropBehaviors.none
                    {
                        // console.log("drop ON, do nothing", this.#objDropOnElement);
                    }

                    //defaults to none (this.arrDropBehaviors.none)
                }
                else //drop AFTER element
                {
                    if (this.#objDropAfterElement == null) //if this.#objDropAfterElement NOT exists
                    {
                        if (this.isDropElementAllowed(objDraggingElement, objDroppable))
                        {
                            this.#arrDroppables[0].insertBefore(objDraggingElement, this.#arrDroppables[0].firstChild);

                            //move element to beginning of droppables array --> otherwise you can do the this.#arrDroppables[0] trick only once.
                            // let iIndexOfDraggingElement = this.#arrDroppables.indexOf(objDraggingElement);
                            // if (iIndexOfDraggingElement > -1)  // only splice array when item is found
                            // {
                            //     this.#arrDroppables.splice(iIndexOfDraggingElement, 1); //delete index from array (2nd parameter means remove one item only)
                            //     this.#arrDroppables.unshift(objDraggingElement); //add element at beginning of array
                            // }
                            
                            // console.log("drop FIRST 123");
                        }    
                    }
                    else //if this.#objDropAfterElement exists
                    {
                        if (this.isDropElementAllowed(objDraggingElement, objDroppable))
                        {
                            this.#objDropAfterElement.after(objDraggingElement); //add after existing element
                            // console.log("drop AFTER 123", this.#objDropAfterElement);
                        }                        
                    }
                }

            }, { signal: this.#objAbortController.signal });   

            this.#dispatchEventDrop(arrDraggingElements, this.#objDropOnElement, this.#objDropAfterElement, "dropped element");
        }, { signal: this.#objAbortController.signal });
    }

    /**
     * removes CSS class droptarget
     */
    #removeCSSDropTarget()
    {
        this.#arrDroppables.forEach(objDroppable => 
        {
            objDroppable.classList.remove(this.sCSSClassDropTarget);
        });
    }

    /**
     * returns HTML element after the future droptarget 
     * (determines where it needs to drop the element)
     * 
     * the reason I used AFTER instead of BEFORE is:
     * I can return null for the first element (there is always a first element) to add it as first in the list
     * I don't know what the last element is, it could be 10 or 163.
     * 
     * @param {Array} arrDraggableHTMLElements of HTMLElements. available elements that can be nearest (to determine order in between the other existing elements)
     * @param {double} fXMousePosition in pixels
     * @param {double} fYMousePosition in pixels
     * @return {HTMLElement} returns null when no element to drag after. This will happen for the first element. This way you know that you need to add it at the beginning
     */
    #getElementAfter(arrDraggableHTMLElements, fXMousePosition, fYMousePosition)
    {
        let iLenEl = arrDraggableHTMLElements.length;
        let objAfterElement = null;
        let objFirstElement = null;
        let iXCurrMouseDiff = 0;
        let iYCurrMouseDiff = 0;
        let iXSmallest = Number.POSITIVE_INFINITY;
        let iYSmallest = Number.POSITIVE_INFINITY;
        let iXSmallestDiff = Number.POSITIVE_INFINITY;
        let iYSmallestDiff = Number.POSITIVE_INFINITY;
        let iXCenterCurrEl = 0;//center point of current element
        let iYCenterCurrEl = 0;//center point of current element
        let rctCurrRect = null;
        let bUpperPart = false; //is mouse cursor on the upper part or the lower part of the draggable element
        let bLeftPart = false; //is mouse curor on the left or right part of the draggable element


        if (fXMousePosition === undefined)
        {
            console.log("getElementAfter(): parameter fXMousePosition is undefined");
            return null;
        }
        if (fYMousePosition === undefined)
        {
            console.log("getElementAfter(): parameter fYMousePosition is undefined");
            return null;
        }

        const iXMousePosition = Math.round(fXMousePosition);
        const iYMousePosition = Math.round(fYMousePosition);    

        if (iLenEl > 0)
        {
            for (let iIndex = 0; iIndex < iLenEl; iIndex++)
            {
                if (arrDraggableHTMLElements[iIndex] != this.objDivDropCursorHorizontal) //exclude the cursor
                {
                    rctCurrRect = arrDraggableHTMLElements[iIndex].getBoundingClientRect();

                    //determine the center coordinates
                    iXCenterCurrEl = Math.round(rctCurrRect.left + (rctCurrRect.width / 2));
                    iYCenterCurrEl = Math.round(rctCurrRect.top + (rctCurrRect.height / 2));

                    //determine difference
                    iXCurrMouseDiff = iXMousePosition - iXCenterCurrEl;
                    bLeftPart = (iXCurrMouseDiff < 0); //negative number from center is the left part
                    if (iXCurrMouseDiff < 0) //if negative number, convert to positive --> only compare integers, not floats
                        iXCurrMouseDiff = iXCurrMouseDiff * -1;
                    iYCurrMouseDiff = iYMousePosition - iYCenterCurrEl;
                    bUpperPart = (iYCurrMouseDiff < 0); //negative number from center is the upper part
                    if (iYCurrMouseDiff < 0) //if negative number, convert to positive --> only compare integers, not floats
                        iYCurrMouseDiff = iYCurrMouseDiff * -1;

                    //if current difference is smaller than known smallest difference, update smallest difference
                    if (bUpperPart === false) //only lower parts, since it's the element AFTER we are looking for
                    {
                        if (iYCurrMouseDiff <= iYSmallestDiff) //first look at Y-coordinate --> only compare integers, not floats
                        {
                            if (iXCurrMouseDiff <= iXSmallestDiff) //then look at X-coordinate --> only compare integers, not floats
                            {
                                iXSmallestDiff = iXCurrMouseDiff;
                                iYSmallestDiff = iYCurrMouseDiff;
                                objAfterElement = arrDraggableHTMLElements[iIndex];
                            }
                        }    
                    }     
                    
                    //look for smallest Y to determine first element
                    if (iYMousePosition < iYSmallest)
                    {
                        if (iXMousePosition <= iXSmallest) //then look at X-coordinate --> only compare integers, not floats
                        {
                            iXSmallest = iXMousePosition;
                            iYSmallest = iYMousePosition;
                            objFirstElement = arrDraggableHTMLElements[iIndex];
                        }
                    }
                }
            }
        }

        return objAfterElement;
    }

    /**
     * returns HTML element that is under the mouse cursor (possible future drop target)
     * This function determines where the mouse cursor intersects the rectangle of droppables, 
     * but with a border margin taken into account. 5px margin means that within 5 px from the border it doesn't see it as intersecting.
     * This allows us to use getElementAfter() after this function returns null, 
     * so we can combine dropping and changing the order of elements
     * 
     * 
     * @param {HTMLElement} objDroppable of HTMLElements. available elements that can be nearest ==> the order is important! it stops looping arrDroppableHTMLElements when it found an intersecting element
     * @param {double} fXMousePosition in pixels
     * @param {double} fYMousePosition in pixels
     * @return {HTMLElement} returns null when no element to drag after. This will happen for the first element. This way you know that you need to add it at the beginning
     */
    #getElementDraggableDropTarget(objDroppable, fXMousePosition, fYMousePosition)
    {
        let fRightX = 0.0;
        let fBottomY = 0.0;
        let rctCurrRect = null;

        if (objDroppable != this.objDivDropCursorHorizontal) //exclude the cursor
        {
            rctCurrRect = objDroppable.getBoundingClientRect();

            fRightX = (rctCurrRect.left + rctCurrRect.width);
            fBottomY = (rctCurrRect.top + rctCurrRect.height);

            if (
                    (fXMousePosition > (rctCurrRect.left + this.#fBorderMargin)) && 
                    (fXMousePosition < (fRightX - this.#fBorderMargin)) &&
                    (fYMousePosition > (rctCurrRect.top + this.#fBorderMargin)) && 
                    (fYMousePosition < (fBottomY - this.#fBorderMargin))
                )
            {
                // console.log("1111interserects", objDroppable);
                return true;
            }
        }
        return false;
    }


    /**
     * returns if a objDraggable is allowed to drop on a objDroppable
     * 
     * to make use of this function:
     * override this function by inheritance and write a customized function!
     * 
     * @param {HTMLElement} objDraggable 
     * @param {HTMLElement} objDroppable 
     * @return {boolean} 
     */
    isDropElementAllowed(objDraggable, objDroppable)
    {
        // console.log("isDropElementAllowed(", objDraggable, objDroppable);
        return true;
    }

    /**
     * creates a drop cursor
     * 
     * @return {HTMLElement} 
     */
    createDragCursor()
    {
        let objCursor = null;

        if (this.#sType == this.arrTypes.table)
        {
            objCursor = document.createElement("tr");
            const objTD = document.createElement("td");
            objTD.setAttribute("colspan", 100); //to span all columns. regretfully doesn't work with a fixed table style
            // objTD.innerHTML = "&nbsp;";
            objCursor.appendChild(objTD);
        }
        else //defaults to <div>
        {
            objCursor = document.createElement("div");
        }

        objCursor.classList.add(this.sCSSClassDragCursor);
        return objCursor;
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
    }


    /** 
     * When added to DOM
     */
    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //first read
            this.#readAttributes();      

            //then render
            this.populate();
            this.updateUI();
            this.reloadDraggablesDroppables();
        }
        
        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
           this.#objAbortController = new AbortController();


        //event
        this.reloadDraggablesDroppables();

        
        this.#bConnectedCallbackHappened = true;                
    }

    /** 
     * remove from DOM 
     **/
    disconnectedCallback()
    {
        this.removeEventListeners();
    }

    /**
     * dispatch "dr-drop" event when a drop has occurred
     * 
     * @param {Array} arrDraggingElements array of HTMLElements that are being dragged
     * @param {HTMLElement} objDroppedOnElement the element that the draggable is being dropped on
     * @param {HTMLElement} objDropAfterElement the element that is closest
     * @param {*} sDescription 
     */
    #dispatchEventDrop(arrDraggingElements, objDroppedOnElement, objDropAfterElement, sDescription)
    {            

        this.dispatchEvent(new CustomEvent("dr-drop", //the reason the event is called "dr-drop", is because "drop" already exists on a droppable, which results in "drop"-event being dispatched twice
        {
            bubbles: true,
            detail:
            {
                draggingElements: arrDraggingElements,
                dropOnElement: objDroppedOnElement,
                dropAfterElement: objDropAfterElement,
                description: sDescription
            }
        }));

    }        

    attributeChangedCallback(sAttrName, sPreviousVal, sNextVal) 
    {
        switch(sAttrName)
        {
            case "type":
                this.#sType = sNextVal;
                // if (this.#bConnectedCallbackHappened)
                //     this.updateUI();                
                break;
            case "dropbehavior":
                this.#sDropBehavior = sNextVal;
                // if (this.#bConnectedCallbackHappened)
                //     this.updateUI();                
                break;
        }

    }
      
    static get observedAttributes() 
    {
        return ["type", "dropbehavior"];
    }
  

}


/**
 * make component available in HTML
 */
customElements.define("dr-drag-drop", DRDragDrop);