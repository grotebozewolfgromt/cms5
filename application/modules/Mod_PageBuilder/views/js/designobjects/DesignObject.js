<?php
/**
 * All DesignObjects inherit from this class
 * 
 **********************
 * TYPES VS CATEGORIES
 **********************
 * There is a big overlap between types and categories.
 * While they can be the same, they don't nessarily have to be.
 * 
 * Categories   - used for the user to put DesignObjects into a logical structure to find them
 *                A DesignObject can live in MULTIPLE categories
 * Types        - used to determine where the user is allowed to drag&drop DesignElements
 *                A DesignObject is only of ONE type
 * 
 *************************************************************************
 * WARNING:
 *************************************************************************
 * this file uses PHP so it can ONLY be used by including it with PHP!
 * 
 *************************************************************************
 * @author Dennis Renirie
 * 
 * 20 dec 2024 file created
 */
?>

  /**
  * @todo DesignObjects
  * -<blockquote>
  * -<address>
  * -<tel>
  * -<code>
  * -<form> + form elements
  */

class DesignObject extends HTMLElement
{
    sTitle = ""; //language aware title shown to user
    sIconSVG = ""; //icon
    sSearchLabelsCSV = '';//language aware searchlabels separated by comma (,)
    sType = objDOTypes.block; //type
    arrCategories = [objDOCategories.all];
    bContentEditable = false; //default! override when you have text you want to edit
    bRespondToDeleteKeyPress = true; //detault! override when you DON'T want to respond to delete key press (in contenteditable=true situation this is undesireable)
    bVisible = true; //default! override when you don't want the Design Object to be rendered (but only visible in editor)
    bDropTarget = false; //default! override when you want drop stuff (other DesignObjects, images, text) inside when dragging and dropping?	
    bUserSelectText = false; //default! override when you want the user to be able to select text. (this is only useful with text related DesignObjects)
    // objShadowDom = null; //ShadowDOM object of this designobject
    objDesignerAbortController = null; //AbortController for this element in the Designer, this DOES NOT INCLUDE elements on the property tab (right side screen) and the toolbar (top of the screen), these are managed by the global

    constructor()
    {
        super();
        
        this.objDesignerAbortController = new AbortController();
        // this.objShadowDom = this.attachShadow({mode: 'open'});

        //add standard properties
        this.className = CSSCLASS_DESIGNOBJECT_DESIGNER;
        this.id = getNewIdDesignObject();   
        this.dataset.designobjectjsclassname = this.getClassName();       
        if (this.bDropTarget)
            this.classList.add(objDragDrop.sCSSClassDroppable);
        
        if (this.bContentEditable)
        {
            if (iCursorMode == CURSORMODE_TEXTEDITOR)
                this.contentEditable = true;
            if (iCursorMode == CURSORMODE_SELECTION)
                this.contentEditable = false;
        }

        // const heading = document.createElement('h2');
        // heading.textContent = 'hello world 123';

        // shadow.appendChild(heading);
        // debugger;
        
    }

    /**
     * when element is inserted into the document
     * adds event listeners
     */
    connectedCallback()
    {
      
        /**
         * ADD EVENT LISTENERS 
        */
        this.addEventListener("dblclick", (objEvent) => this.handleDblClick(objEvent),{signal: this.objDesignerAbortController.signal});        
        this.addEventListener("focusout", (objEvent) => this.handleFocusout(objEvent),{signal: this.objDesignerAbortController.signal});     
    
        this.addEventListener("mousedown", function doclick(objEvent)  //not mousedown because that gives problems with dragging
        {   
            let objDO = getFirstDesignObjectParent(objEvent.target);
    
            //attach event listener from DesignObject instance
            this.handleClick(objEvent);        
    
    
            //SHIFT
            if (objEvent.shiftKey) 
            {
                let iIndexStart = iLenDoALL; //assume most extreme max
                let iIndexEnd = -1; //assum most extreme min
    
                // debugger
    
                //find Start (=already selected) and End (=e.target) index
                for(let iIndex = 0; iIndex < iLenDoALL; iIndex++)
                {
                    if (objDOAll[iIndex].classList.contains("selected") || (objDOAll[iIndex] === objDO))
                    {
                        if (iIndex < iIndexStart)
                            iIndexStart = iIndex;
                        if (iIndex > iIndexEnd )
                            iIndexEnd = iIndex;
                    }
                }
    
                //when begin > end then switch values (otherwise the loop below goes wrong)
                if (iIndexStart > iIndexEnd)
                {
                    let iTempIndex = iIndexEnd;
                    iIndexEnd = iIndexStart;
                    iIndexStart = iTempIndex;
                }
    
                //attach "selected" classes
                if ((iIndexStart != -1) && (iIndexEnd != -1))
                {
                    unselectAllDesignObjects();
    
                    //attach "select" to indexes between start and end
                    for(let iIndex = iIndexStart; iIndex <= iIndexEnd; iIndex++)
                        objDOAll[iIndex].classList.add("selected");
            
                    updateToolbarAndElementTab();   
                    updateStructureTab();             
                    return;
                }   
    
                return;
            }
    
    
            //CTRL
            if (objEvent.ctrlKey)
            {
                if (objDO.classList.contains("selected"))            
                    objDO.classList.remove("selected");
                else
                    objDO.classList.add("selected");
    
                updateToolbarAndElementTab();   
                updateStructureTab();
                return;
            }
    
    
            //normal
            unselectAllDesignObjects();
            if (!objDO.classList.contains("selected"))
                objDO.classList.add("selected");
            updateToolbarAndElementTab();     
            updateStructureTab();
    
        },{signal: this.objDesignerAbortController.signal});      
    

        //add to drag-drop class (eventlisteners are in there)
        objDragDrop.addDraggable(this);
        if (this.bDropTarget)
            objDragDrop.addDroppable(this);


    }

    /**
     * when element is removed from document
     * remove event listeners
     */
    disconnectedCallback()
    {
        this.objDesignerAbortController.abort();
    }

    

    /**
     * renders element in designer
     */
    renderDesigner()
    {
        this.appendChild(document.createElement("div"));
    }

    /**
     * copy all properties from another DesignObject to this one
     * used to transform one DesignObject into another
     * i.e. from <h1> to <p>.
     * 
     * this method exists because you can't clone a node and then change the tag name
     * 
     * @param {HTMLElement} objDOFrom 
     * @param {HTMLElement} objDOTo 
     * @param {boolean} bAssignNewId assigns new element id 
     */
    copyFromElement(objDOFrom, bAssignNewId = true)
    {
        //tag attributes
        copyAttributesHTMLElement(objDOFrom, this);        
        if (bAssignNewId)
            objDOTo.id = getNewIdDesignObject();   

        //contents
        //I do this regretfully: this.innerHTML = objDOFrom.innerHTML;    
        if ((this.childElementCount > 0) && (objDOFrom.childElementCount > 0))
        {
            for (let iIndex = 0; iIndex < objDOFrom.childElementCount; iIndex++)
            {
                if (iIndex < this.childElementCount)
                {
                    copyAttributesHTMLElement(objDOFrom.children[iIndex], this.children[iIndex]);
                    this.children[iIndex].innerHTML = objDOFrom.children[iIndex].innerHTML;
                }
            }
        }
    }

    /**
     * render the detail tab
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {HTMLElement} objTabGridContainer --> container <div> of detailtab
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     */
    renderElementTab(objTabGridContainer, arrDOInDesigner)
    {
        //==== sizing
        objTabGridContainer.appendChild(this.renderPropertyWidthHeightContainer(arrDOInDesigner)); 
        
        //==== padding
        objTabGridContainer.appendChild(this.renderPropertyPaddingContainer(arrDOInDesigner)); 

        //==== margin
        objTabGridContainer.appendChild(this.renderPropertyMarginContainer(arrDOInDesigner)); 

        //==== border
        objTabGridContainer.appendChild(this.renderPropertyBorderContainer(arrDOInDesigner));         

        //==== visibility
        objTabGridContainer.appendChild(this.renderPropertyVisibility(arrDOInDesigner)); 
          
        //==== move
        objTabGridContainer.appendChild(this.renderPropertyMove(arrDOInDesigner)); 
      
        //==== delete
        objTabGridContainer.appendChild(this.renderPropertyDelete(arrDOInDesigner)); 

    }

    /**
     * renders padding-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyWidthHeightContainer(arrDOInDesigner)
    {
        let objDivCol = null; //div column in grid
        let objLabel = null;

        //container
        const objDivSizingContainer = document.createElement("div");
        objDivSizingContainer.id = "widthheightcontainer";

        //label
        const objLblWidthHeight = document.createElement("label");
        objLblWidthHeight.classList.add("tab-element-property-header");
        objLblWidthHeight.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_widthheight', 'Sizing') ?>";
        objDivSizingContainer.appendChild(objLblWidthHeight);

        //grid
        const objDivSizingGrid = document.createElement("div"); //all 4 padding properties
        objDivSizingGrid.style.display = "grid";
        objDivSizingGrid.style.gridTemplateColumns = "1fr 1fr";

            //==== width
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_width', 'Width (px)') ?>";

            const objWidth = document.createElement("input");        
            objWidth.type = "number";
            // console.log(sanitizeStringNumber(arrDOInDesigner[0].style.width, false, true));
            objWidth.value = sanitizeStringNumber(arrDOInDesigner[0].style.width, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objWidth);
            objDivSizingGrid.appendChild(objDivCol);

            objWidth.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.width = objWidth.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objWidth.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.width = objWidth.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     

            //==== height
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_height', 'Height (px)') ?>";

            const objHeight = document.createElement("input");        
            objHeight.type = "number";
            objHeight.value = sanitizeStringNumber(arrDOInDesigner[0].style.height, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objHeight);
            objDivSizingGrid.appendChild(objDivCol);

            objHeight.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.height = objHeight.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objHeight.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.height = objHeight.value + "px";
                }
            },{signal: objElementTabAbortController.signal});        

            //==== max width
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_maxwidth', 'Max Width (px)') ?>";

            const objMaxWidth = document.createElement("input");        
            objMaxWidth.type = "number";
            objMaxWidth.value = sanitizeStringNumber(arrDOInDesigner[0].style.maxWidth, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMaxWidth);
            objDivSizingGrid.appendChild(objDivCol);

            objMaxWidth.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.maxWidth = objMaxWidth.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objMaxWidth.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.maxWidth = objMaxWidth.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     
        
            //==== max height
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_maxheight', 'Max Height (px)') ?>";

            const objMaxHeight = document.createElement("input");        
            objMaxHeight.type = "number";
            objMaxHeight.value = sanitizeStringNumber(arrDOInDesigner[0].style.objMaxHeight, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMaxHeight);
            objDivSizingGrid.appendChild(objDivCol);

            objMaxHeight.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.maxHeight = objMaxHeight.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objMaxHeight.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.maxHeight = objMaxHeight.value + "px";
                }
            },{signal: objElementTabAbortController.signal});                
    

        objDivSizingContainer.appendChild(objDivSizingGrid)//add 4 sizing properties        
        return objDivSizingContainer;
    }

    /**
     * renders padding-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyPaddingContainer(arrDOInDesigner)
    {
        let objDivCol = null; //div column in grid
        let objLabel = null;

        //container
        const objDivPaddingContainer = document.createElement("div");
        objDivPaddingContainer.id = "paddingcontainer";

        //label
        const objLblPadding = document.createElement("label");
        objLblPadding.classList.add("tab-element-property-header");
        objLblPadding.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_padding', 'Padding (px)') ?>";
        objDivPaddingContainer.appendChild(objLblPadding);

        //grid
        const objDivPaddingGrid = document.createElement("div"); //all 4 padding properties
        objDivPaddingGrid.style.display = "grid";
        objDivPaddingGrid.style.gridTemplateColumns = "1fr 1fr 1fr 1fr";

            //==== padding top
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_paddingtop', 'Top') ?>";

            const objPaddingTop = document.createElement("input");        
            objPaddingTop.type = "number";
            objPaddingTop.value = sanitizeStringNumber(arrDOInDesigner[0].style.paddingTop, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objPaddingTop);
            objDivPaddingGrid.appendChild(objDivCol);

            objPaddingTop.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingTop = objPaddingTop.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objPaddingTop.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingTop = objPaddingTop.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     

            //==== padding right
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_paddingright', 'Right') ?>";

            const objPaddingRight = document.createElement("input");        
            objPaddingRight.type = "number";
            objPaddingRight.value = sanitizeStringNumber(arrDOInDesigner[0].style.paddingRight, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objPaddingRight);
            objDivPaddingGrid.appendChild(objDivCol);

            objPaddingRight.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingRight = objPaddingRight.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objPaddingRight.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingRight = objPaddingRight.value + "px";
                }
            },{signal: objElementTabAbortController.signal});        

            //==== padding bottom
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_paddingbottom', 'Bottom') ?>";

            const objPaddingBottom = document.createElement("input");        
            objPaddingBottom.type = "number";
            objPaddingBottom.value = sanitizeStringNumber(arrDOInDesigner[0].style.paddingBottom, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objPaddingBottom);
            objDivPaddingGrid.appendChild(objDivCol);

            objPaddingBottom.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingBottom = objPaddingBottom.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objPaddingBottom.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingBottom = objPaddingBottom.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     
        
            //==== padding left
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_paddingleft', 'Left') ?>";

            const objPaddingLeft = document.createElement("input");        
            objPaddingLeft.type = "number";
            objPaddingLeft.value = sanitizeStringNumber(arrDOInDesigner[0].style.paddingLeft, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objPaddingLeft);
            objDivPaddingGrid.appendChild(objDivCol);

            objPaddingLeft.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingLeft = objPaddingLeft.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objPaddingLeft.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.paddingLeft = objPaddingLeft.value + "px";
                }
            },{signal: objElementTabAbortController.signal});                


        objDivPaddingContainer.appendChild(objDivPaddingGrid)//add 4 padding properties        
        return objDivPaddingContainer;
    }

    /**
     * renders Margin-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyMarginContainer(arrDOInDesigner)
    {
        let objDivCol = null; //div column in grid
        let objLabel = null;

        //container
        const objDivMarginContainer = document.createElement("div");
        objDivMarginContainer.id = "margincontainer";

        //label
        const objLblMargin = document.createElement("label");
        objLblMargin.classList.add("tab-element-property-header");
        objLblMargin.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_margin', 'Margin (px)') ?>";
        objDivMarginContainer.appendChild(objLblMargin);

        //grid
        const objDivMarginGrid = document.createElement("div"); //all 4 margin properties
        objDivMarginGrid.style.display = "grid";
        objDivMarginGrid.style.gridTemplateColumns = "1fr 1fr 1fr 1fr";

            //==== margin top
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_margintop', 'Top') ?>";

            const objMarginTop = document.createElement("input");        
            objMarginTop.type = "number";
            objMarginTop.value = sanitizeStringNumber(arrDOInDesigner[0].style.marginTop, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMarginTop);
            objDivMarginGrid.appendChild(objDivCol);

            objMarginTop.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginTop = objMarginTop.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objMarginTop.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginTop = objMarginTop.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     

            //==== margin right
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_marginright', 'Right') ?>";

            const objMarginRight = document.createElement("input");        
            objMarginRight.type = "number";
            objMarginRight.value = sanitizeStringNumber(arrDOInDesigner[0].style.marginRight, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMarginRight);
            objDivMarginGrid.appendChild(objDivCol);

            objMarginRight.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginRight = objMarginRight.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objMarginRight.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginRight = objMarginRight.value + "px";
                }
            },{signal: objElementTabAbortController.signal});        

            //==== margin bottom
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_marginbottom', 'Bottom') ?>";

            const objMarginBottom = document.createElement("input");        
            objMarginBottom.type = "number";
            objMarginBottom.value = sanitizeStringNumber(arrDOInDesigner[0].style.marginBottom, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMarginBottom);
            objDivMarginGrid.appendChild(objDivCol);

            objMarginBottom.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginBottom = objMarginBottom.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objMarginBottom.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginBottom = objMarginBottom.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     
        
            //==== padding left
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_marginleft', 'Left') ?>";

            const objMarginLeft = document.createElement("input");        
            objMarginLeft.type = "number";
            objMarginLeft.value = sanitizeStringNumber(arrDOInDesigner[0].style.marginLeft, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objMarginLeft);
            objDivMarginGrid.appendChild(objDivCol);

            objMarginLeft.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginLeft = objMarginLeft.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objMarginLeft.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.marginLeft = objMarginLeft.value + "px";
                }
            },{signal: objElementTabAbortController.signal});                


        objDivMarginContainer.appendChild(objDivMarginGrid)//add 4 margin properties        
        return objDivMarginContainer;
    }    

    /**
     * renders Margin-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyBorderContainer(arrDOInDesigner)
    {
        let objDivCol = null; //div column in grid
        let objLabel = null;

        //container
        const objDivBorderContainer = document.createElement("div");
        objDivBorderContainer.id = "bordercontainer";

        //label chapter
        const objLblBorder = document.createElement("label");
        objLblBorder.classList.add("tab-element-property-header");
        objLblBorder.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_border', 'Border') ?>";
        objDivBorderContainer.appendChild(objLblBorder);

        //label chapter
        const objLblBorderRadius = document.createElement("label");
        objLblBorderRadius.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_borderradius', 'Rounded corners (px)') ?>";
        objDivBorderContainer.appendChild(objLblBorderRadius);

        //grid
        const objDivBorderRadiusGrid = document.createElement("div"); //all 4 margin properties
        objDivBorderRadiusGrid.style.display = "grid";
        objDivBorderRadiusGrid.style.gridTemplateColumns = "1fr 1fr 1fr 1fr";

            //==== border radius top left
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_borderradiustopleft', 'Tp L') ?>";

            const objBorderTopLeftRadius = document.createElement("input");        
            objBorderTopLeftRadius.type = "number";
            objBorderTopLeftRadius.value = sanitizeStringNumber(arrDOInDesigner[0].style.borderTopLeftRadius, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objBorderTopLeftRadius);
            objDivBorderRadiusGrid.appendChild(objDivCol);

            objBorderTopLeftRadius.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderTopLeftRadius = objBorderTopLeftRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objBorderTopLeftRadius.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderTopLeftRadius = objBorderTopLeftRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     

            //==== border radius top right
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_borderradiustopright', 'Tp R') ?>";

            const objBorderTopRightRadius = document.createElement("input");        
            objBorderTopRightRadius.type = "number";
            objBorderTopRightRadius.value = sanitizeStringNumber(arrDOInDesigner[0].style.borderTopRightRadius, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objBorderTopRightRadius);
            objDivBorderRadiusGrid.appendChild(objDivCol);

            objBorderTopRightRadius.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderTopRightRadius = objBorderTopRightRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal}); 
          
            objBorderTopRightRadius.addEventListener("change", e =>  //"change" so the updown buttons make it change directly
            {
                addUndoState();                
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderTopRightRadius = objBorderTopRightRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});        

            //==== margin bottom
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_borderradiusbottomright', 'Bt R') ?>";

            const objBorderBottomRightRadius = document.createElement("input");        
            objBorderBottomRightRadius.type = "number";
            objBorderBottomRightRadius.value = sanitizeStringNumber(arrDOInDesigner[0].style.borderBottomRightRadius, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objBorderBottomRightRadius);
            objDivBorderRadiusGrid.appendChild(objDivCol);

            objBorderBottomRightRadius.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderBottomRightRadius = objBorderBottomRightRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objBorderBottomRightRadius.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderBottomRightRadius = objBorderBottomRightRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});     
        
            //==== padding left
            objDivCol = document.createElement("div");

            objLabel = document.createElement("label");
            objLabel.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_borderradiusbottomleft', 'Bt L') ?>";

            const objBottomLeftRadius = document.createElement("input");        
            objBottomLeftRadius.type = "number";
            objBottomLeftRadius.value = sanitizeStringNumber(arrDOInDesigner[0].style.borderBottomLeftRadius, false, true);//take value of first element

            objDivCol.appendChild(objLabel);
            objDivCol.appendChild(objBottomLeftRadius);
            objDivBorderRadiusGrid.appendChild(objDivCol);

            objBottomLeftRadius.addEventListener("keyup", e =>  //"keyup" so it responds directly
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderBottomLeftRadius = objBottomLeftRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});    
                
            objBottomLeftRadius.addEventListener("change", e =>   //"change" so the updown buttons make it change directly  
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {
                    objDO.style.borderBottomLeftRadius = objBottomLeftRadius.value + "px";
                }
            },{signal: objElementTabAbortController.signal});                


        objDivBorderContainer.appendChild(objDivBorderRadiusGrid)//add border properties        
        return objDivBorderContainer;
    }    

    /**
     * renders Visibility-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyVisibility(arrDOInDesigner)
    {
        //container
        const objDivVisibilityContainer = document.createElement("div");
        objDivVisibilityContainer.id = "visibilitycontainer";  
        
        //label
        const objLblVisibility = document.createElement("label");
        objLblVisibility.classList.add("tab-element-property-header");
        objLblVisibility.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_visibility', 'Visibility') ?>";
        objDivVisibilityContainer.appendChild(objLblVisibility);        

        //checkbox itself
        const objDivChk = document.createElement("div");        
        const objChkVisible = document.createElement("input");
        objChkVisible.type = "checkbox";
        objChkVisible.id = "chkVisibleElement";
        objChkVisible.checked = !arrDOInDesigner[0].classList.contains(CSSCLASS_INVISIBLE_ELEMENT); //take value of first element

        //label with checkbox description
        const objLabelChk = document.createElement("label");
        objLabelChk.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_visibleonpage', 'Visible on page') ?>";
        objLabelChk.htmlFor = "chkVisibleElement";
        
        objDivChk.appendChild(objChkVisible);        
        objDivChk.appendChild(objLabelChk);        
        objDivVisibilityContainer.appendChild(objDivChk);        

        //events
        objChkVisible.addEventListener("click", e => 
        {
            addUndoState();
            for(let objDO of arrDOInDesigner)
            {
                if (objChkVisible.checked)
                    objDO.classList.remove(CSSCLASS_INVISIBLE_ELEMENT);
                else
                    objDO.classList.add(CSSCLASS_INVISIBLE_ELEMENT);                
            }
        },{signal: objElementTabAbortController.signal});  


        return objDivVisibilityContainer;
    }

    /**
     * renders Move-chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyMove(arrDOInDesigner)
    {
        let objDivCol = null;
        let objSwap = null; //to swap nods

        //container
        const objDivMoveContainer = document.createElement("div");
        objDivMoveContainer.id = "movecontainer";  
        
        //label
        const objLblVisibility = document.createElement("label");
        objLblVisibility.classList.add("tab-element-property-header");
        objLblVisibility.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_move', 'Move') ?>";
        objDivMoveContainer.appendChild(objLblVisibility);        

        //grid--> I use a grid to align the buttons in the middle in the same line with the delete button
        const objDivButtonGrid = document.createElement("div"); //4 columns: 2 outer for margin 2 inner for buttons
        objDivButtonGrid.style.display = "grid";
        objDivButtonGrid.style.gridTemplateColumns = "1fr 1fr 1fr 1fr";

            //==== empty
            objDivCol = document.createElement("div");
            objDivButtonGrid.appendChild(objDivCol);

            //==== move up
            objDivCol = document.createElement("div");

            const objBtnUp = document.createElement("button");        
            objBtnUp.innerHTML = '<svg class="iconchangefill" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M256,213.7L256,213.7L256,213.7l174.2,167.2c4.3,4.2,11.4,4.1,15.8-0.2l30.6-29.9c4.4-4.3,4.5-11.3,0.2-15.5L264.1,131.1  c-2.2-2.2-5.2-3.2-8.1-3c-3-0.1-5.9,0.9-8.1,3L35.2,335.3c-4.3,4.2-4.2,11.2,0.2,15.5L66,380.7c4.4,4.3,11.5,4.4,15.8,0.2L256,213.7  z"/></svg>';

            objDivCol.appendChild(objBtnUp);
            objDivButtonGrid.appendChild(objDivCol);

            objBtnUp.addEventListener("mousedown", e =>  //"mousedown" so it responds immediately
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {               
                    objSwap = objDO.previousElementSibling;
                    if (objSwap != null)
                    {
                        objDO.parentElement.removeChild(objSwap);
                        objDO.after(objSwap);
                    }
                }
            },{signal: objElementTabAbortController.signal}); 

            //==== move down
            objDivCol = document.createElement("div");

            const objBtnDown = document.createElement("button");        
            objBtnDown.innerHTML = '<svg class="iconchangefill" style="enable-background:new 0 0 512 512;" version="1.1" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M256,298.3L256,298.3L256,298.3l174.2-167.2c4.3-4.2,11.4-4.1,15.8,0.2l30.6,29.9c4.4,4.3,4.5,11.3,0.2,15.5L264.1,380.9  c-2.2,2.2-5.2,3.2-8.1,3c-3,0.1-5.9-0.9-8.1-3L35.2,176.7c-4.3-4.2-4.2-11.2,0.2-15.5L66,131.3c4.4-4.3,11.5-4.4,15.8-0.2L256,298.3  z"/></svg>';

            objDivCol.appendChild(objBtnDown);
            objDivButtonGrid.appendChild(objDivCol);

            objBtnDown.addEventListener("mousedown", e =>  //"mousedown" so it responds immediately
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {                           
                    objSwap = objDO.nextElementSibling;
                    if (objSwap != null)
                    {
                        objDO.parentElement.removeChild(objSwap);
                        objDO.before(objSwap);
                    }
                }
            },{signal: objElementTabAbortController.signal});             

            //==== empty
            objDivCol = document.createElement("div");
            objDivButtonGrid.appendChild(objDivCol);            

        objDivMoveContainer.appendChild(objDivButtonGrid);
        return objDivMoveContainer;
    }


    /**
     * renders actions chapter for element-properties tab
     * 
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     * @returns {HTMLElement} Div
     */
    renderPropertyDelete(arrDOInDesigner)
    {
        let objDivCol = null;

        //container
        const objDivDelContainer = document.createElement("div");
        objDivDelContainer.id = "deletecontainer";  
        
        //label
        const objLblVisibility = document.createElement("label");
        objLblVisibility.classList.add("tab-element-property-header");
        objLblVisibility.innerHTML = "<?php echo transm($sModule, 'pagebuilder_tab_details_label_delete', 'Delete') ?>";
        objDivDelContainer.appendChild(objLblVisibility);        

        //grid --> I use a grid to align the button in the middle in the same line with the move up-down buttons
        const objDivButtonGrid = document.createElement("div"); //4 columns: 2 outer for margin 1 inner for button
        objDivButtonGrid.style.display = "grid";
        objDivButtonGrid.style.gridTemplateColumns = "1fr 2fr 1fr";

            //==== empty
            objDivCol = document.createElement("div");
            objDivButtonGrid.appendChild(objDivCol);

            //==== delete buton
            objDivCol = document.createElement("div");
            objDivCol.classList.add("tab-element-property-delete-btnDelete")

            const objBtnDel = document.createElement("button");        
            objBtnDel.innerHTML = '<svg class="iconchangestroke" version="1.1" viewBox="0 0 14 18" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="currentColor" id="Core" transform="translate(-299.000000, -129.000000)"><g id="delete" transform="translate(299.000000, 129.000000)"><path d="M1,16 C1,17.1 1.9,18 3,18 L11,18 C12.1,18 13,17.1 13,16 L13,4 L1,4 L1,16 L1,16 Z M14,1 L10.5,1 L9.5,0 L4.5,0 L3.5,1 L0,1 L0,3 L14,3 L14,1 L14,1 Z" id="Shape"/></g></g></g></svg>' + "<?php echo transm($sModule, 'pagebuilder_tab_details_button_delete', 'Delete') ?>";

            objDivCol.appendChild(objBtnDel);
            objDivButtonGrid.appendChild(objDivCol);

            objBtnDel.addEventListener("mousedown", e =>  //"mousedown" so it responds immediately
            {
                addUndoState();
                for(let objDO of arrDOInDesigner)
                {                           

                    //remove all eventlisteners from DO in Designer
                    //arrDOInDesignerAbortControllers[objDO.id].abort();
                    objDragDrop.removeDraggable(objDO);
                    objDragDrop.removeDroppable(objDO);

                    // --> structure tab is not visible
                    //if (arrDOInStructureTabAbortControllers.length > 0) //the contents of the structure tab can be empty, because it's only filled when tab is clicked
                    //    arrDOInStructureTabAbortControllers[objDO.id].abort();//remove all eventlisteners from DO in Structure tab
                    
                    //actual delete
                    objDivDesignerContainer.removeChild(objDO);

                    //update UI
                    // updateStructureTab(); --> is not visible
                    updateToolbarAndElementTab();                    
                }
            },{signal: objElementTabAbortController.signal}); 

            //==== empty
            objDivCol = document.createElement("div");
            objDivButtonGrid.appendChild(objDivCol);


        objDivDelContainer.appendChild(objDivButtonGrid);
        return objDivDelContainer;
    }    

    /**
     * render the toolbar on top of the screen
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * By default it empties the toolbar
     * 
     * @param {HTMLElement} objToolbarContainer --> container <div> of toolbar
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     */
    renderToolbar(objToolbarContainer)
    {
        console.log("DesignObject parent class: function renderToolbar() not implemented yet by child: "+ this.getClassName());
        objToolbarContainer.appendChild(document.createTextNode(""));
    }


    /**
     * handles a mouse click event
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDOInDesigner
     */
    handleClick(objEvent)
    {
        console.log("DesignObject parent class: function handleClick() not implemented yet by child: "+ this.getClassName());
    }    


    /**
     * handles a double click event
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Event} e 
     * @param {HTMLElement} objDOInDesigner 
     */
    handleDblClick(objEvent)
    {
        console.log("DesignObject parent class: function handleDblClick() not implemented yet by child: "+ this.getClassName());
    }   
    
    /**
     * handles a focusout (when mouse cursor leaves field)
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Event} e 
     * @param {HTMLElement} objDOInDesigner 
     */
    handleFocusout(objEvent)
    {
        console.log("DesignObject parent class: function handleFocusout() not implemented yet by child: "+ this.getClassName());
    }    

    /**
     * handles a drop (from drag-and-drop)
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objDOInDesigner
     */
    // handleDrop(objEvent, objDOInDesigner)
    // {
    //     console.log("DesignObject parent class: function handleDrop() not implemented yet by child: "+ this.getClassName());
    // }    
    

    /**
     * returns true or false whether drop is allowed
     * (only makes sense if this.bDropTarget == true)
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Array} of HTMLElement objects that are currently dragging
     * @param {HTMLElement} objDroppable droppable object from arrDroppables
     * @param {HTMLElement} objTarget actual target that mouse is dragging onto (objDroppable and objTarget can be the same, but often it's not because you are technically dragging onto a sibling element to determine whether to drop before or after)
     * @returns {bool}
     */
    isDropAllowed(arrDraggableElements, objDroppable, objTarget)
    {
        console.log("DesignObject parent class: function isDropAllowed() not implemented yet by child: "+ this.getClassName());

        return ((this.bDropTarget == true) && (objTarget.classList.contains(objDragDrop.sCSSClassDroppable) == true));
    }       

    /**
     * PUBLIC FUNCTION
     * rendering the final html for the front-end for this DesignObject
     * 
     * we clean up on a copy of the node
     * because when we do it on the node itself, 
     * it will destoy the functionality of the designer.
     * 
     * @param {HTMLElement} objHTMLElement Div element of design object in designer
     */
    renderHTML() 
    {
        const objCopy = this.childNodes[0];

        this.cleanDOForRenderHTML(objCopy);

        return objCopy.outerHTML;
    }

    /**
     * PROTECTED FUNCTION
     * 
     * cleans a node for rendering HTML.
     * DON'T USE THIS FUNCTION ON ACTUAL DESIGNOBJECTS IN DESIGNER, ALWAYS ON CLONED NODES
     * OTHERWISE IT WILL KILL FUNCTIONALITY OF THE DESIGNER!!!
     * 
     * it calls all children to clean up too
     * we want each Design Object to be able to clean up its own node,
     * so,
     * OVERLOAD (AND INHERIT with super.cleanDOForRenderHTML()) THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {HTMLElement} objHTMLElement Div element of design object in designer
     * @param {HTMLElement} objHTMLElementParent Div element of design object in designer
     */
    cleanDOForRenderHTML(objHTMLElement)
    {
        let sDOJSClassName = "";//name of the DesignObject child class

        //only render visible elements. Regretfully, you can't remove yourself with .remove, otherwise I would have done that
        if (objHTMLElement.classList.contains(CSSCLASS_INVISIBLE_ELEMENT)) 
        {
            objHTMLElement.innerHTML = "<!-- invisible element removed -->";
            objHTMLElement.classList.remove(CSSCLASS_INVISIBLE_ELEMENT);
        }

        //recursively call children to clean up 
        if (objHTMLElement.hasChildNodes)
        {
            for (let iIndex = 0; iIndex < objHTMLElement.childElementCount; iIndex++) //I don't cache childElementCount because it can change on each child call (because invisible elements delete themselves)
            {        
                //clean child
                objHTMLElement.children[iIndex].removeAttribute("contentEditable");

                //looking for more designobjects as children
                if (objHTMLElement.children[iIndex].classList.contains(CSSCLASS_DESIGNOBJECT_DESIGNER)) //can be other objects like <br> and <svg> too
                {
                    sDOJSClassName = objHTMLElement.children[iIndex].dataset.designobjectjsclassname;
                    arrDesignObjectsLibrary[sDOJSClassName].cleanDOForRenderHTML(objHTMLElement.children[iIndex]); //recursive call clean on proper DesignObject class ==> I can't use "this." !!!!!!!!!
                }
            }
        }

    }
    


    /**
     * returns class name
     * 
     * @returns string
     */
    getClassName()
    {
        return this.constructor.name;
    }


    
}
