/**
 * parent class for all text related items, like:
 * paragraph, h1, h2, pre, code etc
 */
class DOText extends DesignObject
{   
    sPlaceHolder = "<?php echo transm($sModule, 'pagebuilder_designobject_text_placeholder', 'Enter text ...') ?>";
    bContentEditable = true;
    bRespondToDeleteKeyPress = false; 
    bUserSelectText = true; //users must be able to select text

    /**
     * handles a click event
     * 
     * @param {Event} objEvent
     */
    handleClick(objEvent)
    {
        

    }    

    /**
     * handles a double click event
     * 
     * @param {Event} objEvent 
     */
    handleDblClick(objEvent)
    {
        // debugger
        if (iCursorMode == CURSORMODE_SELECTION) //only in selection mode doubleclick (in edit mode 1click is enough)
        {            
            if (this.bContentEditable)
            {
                let iLenChilds = this.childElementCount;
                for (let iIndex = 0; iIndex < iLenChilds; iIndex++)
                {
                    this.children[iIndex].contentEditable = true;
                }
            }
        }
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
        if (iCursorMode == CURSORMODE_SELECTION) //only in selection mode doubleclick (in edit mode 1click is enough)
        {          
            if (this.bContentEditable)  
            {
                let iLenChilds = this.childElementCount;
                for (let iIndex = 0; iIndex < iLenChilds; iIndex++)
                {
                    this.children[iIndex].contentEditable = false;
                }
            }
        }
    }   


    /**
     * renders element in designer
     */
    renderDesigner()
    {
        let objPre = document.createElement("pre");
        objPre.appendChild(document.createTextNode(this.sPlaceHolder));
        this.appendChild(objPre);
    }  

    /**
     * render the toolbar on top of the screen
     * 
     * 
     * @param {HTMLElement} objToolbarContainer --> container <div> of toolbar
     * @param {Array} arrDOInDesigner --> array of selected <div>s in designer
     */
    renderToolbar(objToolbarContainer, arrDOInDesigner)
    {
        let objBtn = null;
        let objSVG = null;

        objToolbarContainer.innerHTML = ""; //clear toolbar


        //bold
        objBtn = document.createElement("button");
        objToolbarContainer.appendChild(objBtn);  
        objSVG = document.getElementById("icon-bold");
        objBtn.innerHTML = objSVG.outerHTML;
        objBtn.addEventListener("mousedown", e => 
        {
            addUndoState();
            document.execCommand('bold');              
        },{signal: objToolbarAbortController.signal});         


        //italic
        objBtn = document.createElement("button");
        objToolbarContainer.appendChild(objBtn);  
        objSVG = document.getElementById("icon-italic");
        objBtn.innerHTML = objSVG.outerHTML;     
        objBtn.addEventListener("mousedown", e => 
        {
            addUndoState();
            document.execCommand('italic');              
        },{signal: objToolbarAbortController.signal});               
        
        //strikethrough
        objBtn = document.createElement("button");
        objToolbarContainer.appendChild(objBtn);  
        objSVG = document.getElementById("icon-strikethrough");
        objBtn.innerHTML = objSVG.outerHTML;         
        objBtn.addEventListener("mousedown", e => 
        {
            addUndoState();
            document.execCommand('strikethrough');              
        },{signal: objToolbarAbortController.signal});          
        
        //link: unlinked
        objBtn = document.createElement("button");
        objToolbarContainer.appendChild(objBtn);  
        objSVG = document.getElementById("icon-link-unlinked");
        objBtn.innerHTML = objSVG.outerHTML;     
        objBtn.addEventListener("mousedown", e => 
        {
            this.openHyperlinkDialog();
        },{signal: objToolbarAbortController.signal});         

        
        //menu
        objBtn = document.createElement("button");
        objToolbarContainer.appendChild(objBtn);  
        objSVG = document.getElementById("icon-menu");
        objBtn.innerHTML = objSVG.outerHTML;          
        
        //select box with types of text
        let objSel = document.createElement("select");
        objSel.addEventListener("change", e => 
        {
            const arrSelected = objDivDesignerContainer.getElementsByClassName("selected"); 
            const iLenDO = arrSelected.length;
            let objNewEl = null;//HTML Element
            let objElParent = null;//HTML Element old element

            //undo
            addUndoState();

            //add new elements, and remove old
            for (let iIndex = 0; iIndex < iLenDO; iIndex++)
            {
                objElParent = arrSelected[iIndex].parentElement;

                //cleanup old event listeners first before attaching new                
                // arrDOInDesignerAbortControllers[arrSelected[iIndex].id].abort(); //==> we dont have to create new AbortController() because element is going to be deleted
                // if (arrDOInStructureTabAbortControllers.length > 0)//structure tab can be empty, because its only filled when visible
                //     arrDOInStructureTabAbortControllers[arrSelected[iIndex].id].abort(); //==> we dont have to create new AbortController() because element is going to be deleted

                //new element: copy from old
                objNewEl = arrDesignObjectsLibrary[objSel.value].cloneNode(true);
                objNewEl.renderDesigner();
                objNewEl.copyFromElement(arrSelected[iIndex], false);
                // arrDesignObjectsLibrary[objSel.value].copyElement(arrSelected[iIndex], objNewEl, false);
                // arrDesignObjectsLibrary[objSel.value].addEventListeners(objNewEl);

                objElParent.replaceChild(objNewEl, arrSelected[iIndex]);
            }    
        },{signal: objToolbarAbortController.signal});      
        objToolbarContainer.appendChild(objSel);
        objSel.style = "width:inherit"; //prevent 100% width

        let objOptText = document.createElement("option");
        objSel.appendChild(objOptText);
        objOptText.value = DOParagraph.name;
        objOptText.text = arrDesignObjectsLibrary[DOParagraph.name].sTitle;


        let objOptH1 = document.createElement("option");
        objSel.appendChild(objOptH1);
        objOptH1.value = DOH1.name;
        objOptH1.text = arrDesignObjectsLibrary[DOH1.name].sTitle;

        let objOptH2 = document.createElement("option");
        objSel.appendChild(objOptH2);
        objOptH2.value = DOH2.name;
        objOptH2.text = arrDesignObjectsLibrary[DOH2.name].sTitle;

        let objOptH3 = document.createElement("option");
        objSel.appendChild(objOptH3);
        objOptH3.value = DOH3.name;
        objOptH3.text = arrDesignObjectsLibrary[DOH3.name].sTitle;

        let objOptH4 = document.createElement("option");
        objSel.appendChild(objOptH4);
        objOptH4.value = DOH4.name;
        objOptH4.text = arrDesignObjectsLibrary[DOH4.name].sTitle;

        let objOptH5 = document.createElement("option");
        objSel.appendChild(objOptH5);
        objOptH5.value = DOH5.name;
        objOptH5.text = arrDesignObjectsLibrary[DOH5.name].sTitle;

        let objOptH6 = document.createElement("option")
        objSel.appendChild(objOptH6);
        objOptH6.value = DOH6.name;
        objOptH6.text = arrDesignObjectsLibrary[DOH6.name].sTitle;

        //select current one
        objSel.value = this.getClassName();
    }    

    /**
     * open the hyperlink dialog
     * 
     */    
    openHyperlinkDialog()
    {
        const objDlg = document.getElementById("dlgHyperlink");
        const objBtnX = document.getElementById("btnDialogHyperlinkX");
        const objBtnApply = document.getElementById("btnDialogHyperlinkApply");
        const objBtnCancel = document.getElementById("btnDialogHyperlinkCancel");
        const edtDescription = document.getElementById("edtHyperlinkDialogDescription");
        const edtURL = document.getElementById("edtHyperlinkDialogURL");
        const chkOpenTab = document.getElementById("chkHyperlinkDialogOpenTab");
        const chkNoFollow = document.getElementById("chkHyperlinkDialogNoFollow");
        const chkSponsored = document.getElementById("chkHyperlinkDialogSponsored");
        let objRange = null;  
        let objSelection = null;  
        let objSelectionAnchor = null;  
        let iStartPos = 0;
        let iEndPos = 0;

        objDlg.showModal();
    
        objSelection = document.getSelection();
        if (objSelection.anchorOffset > objSelection.focusOffset)
        {
            iStartPos = objSelection.anchorOffset; //start
            iEndPos = objSelection.focusOffset; //end
        }
        else
        {
            iStartPos = objSelection.focusOffset;
            iEndPos = objSelection.anchorOffset;
        }        
        objSelectionAnchor = objSelection.anchorNode;


        //add eventlisteners to buttons
        objBtnX.addEventListener("mousedown", (e) =>
        {
            objDlg.close(false);        
        },{signal: objToolbarContainer.signal}); 
    
        objBtnApply.addEventListener("mousedown", (e) =>
        {                  
            //restore selection from range
            objRange = document.createRange();
            objRange.setStart(objSelectionAnchor, 5);
            objRange.setEnd(objSelectionAnchor, 10);
            document.getSelection().addRange(objRange);
            // console.log(objSelectionAnchor);            
            // objSelection = window.getSelection();
            // objSelection.removeAllRanges();
            // objSelection.addRange(objRange);
    
            // document.execCommand('createlink', '', edtURL.value);

                // objSelection.anchorNode.parentElement.innerText = edtDescription.value; //replace with new text
            
            if (chkOpenTab.checked)
                objSelection.anchorNode.parentElement.target = '_blank';
            if (chkNoFollow.checked)
                objSelection.anchorNode.parentElement.rel = 'nofollow';
            if (chkSponsored.checked)
                objSelection.anchorNode.parentElement.rel = 'sponsored';

            // replaceSelectedText(edtDescription.value);

            objDlg.close(true);  
        },{signal: objToolbarContainer.signal}); 
        
        objBtnCancel.addEventListener("mousedown", (e) =>
        {
            objDlg.close(false);
        },{signal: objToolbarContainer.signal});         
    }

    replaceSelectedText(replacementText) 
    {
        var sel, range;
        if (window.getSelection) {
            sel = window.getSelection();
            if (sel.rangeCount) {
                range = sel.getRangeAt(0);
                range.deleteContents();
                range.insertNode(document.createTextNode(replacementText));
            }
        }
    }    
}
