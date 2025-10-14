
<?php 
/**
 * dr-input-text.js
 *
 * class as alternative to <input type="text">
 * 
 * FEATURES
 * -getDirty
 * -character counter
 * -whitelist allowed characters (doesn't allow user to fill in potentially dangerous characters)
 * -auto complete
 * -css class "invalid" when input is not valid (could not be filtered, like minlength)
 * -todo: checkmark/cross for invalid/valid input
 * -todo: mask "nnnn ll" (postal code) https://docwiki.embarcadero.com/Libraries/Sydney/en/System.MaskUtils.TEditMask
 * 
 * WARNING:
 * - use whitelist over blacklist for security reasons.
 * - whitelist characters supersede priority over blacklist characters if backlist and whitelist contradict. In <dr-input-text blacklist="b" whitelist="abc"> entering "b" is still allowed
 * - character counter shows always when minlength or maxlength is not met, even when "charcounter" = false
 * 
 * DISPATCHES EVENT:
 * - "update" when anything changes in editbox (on each keypress for example)
 * - "change" when user leaves editbox and is changed
 * 
 * ATTRIBUTES
 * "whitelist"      - string    - allows you to filter characters based on a whitelist
 * "blacklist"      - string    - allows you to filter characters based on a blacklist (whitelist overrules blacklist when contradicting)
 * "maxlength"      - integer   - maximum amount of characters allowed
 * "minlength"      - integer   - minimum amount of characters needed
 * "charcounter"    - boolean   - always shows character counter. Character counter shows always when minlength or maxlength is not met, even when "charcounter" = false
 * "placeholder"    - string    - default text in textbox that is removed when the user starts typing
 * 
 * 
 * DEPENDENCIES:
 * - DRComponentsLib
 * - <dr-popover>
 *  
 * EXAMPLES:
 * <dr-input-text whitelist="abc"></dr-input-text>
 * <dr-input-text blacklist="abc"></dr-input-text>
 * <dr-input-text maxlength="10"></dr-input-text>
 * <dr-input-text minlength="10"></dr-input-text>
 * <dr-input-text charcounter></dr-input-text>
 * or:
 * <dr-input-text>
 *   <!-- auto complete items, also settable via array with setAutoCompleteItems() -->
 *   <div>beer</div>
 *   <div>muis</div>
 *   <div>mus</div>
 *   <div>olifant</div>
 *   <div>giraffe</div>
 * </dr-input-text>
 * 
 * @author Dennis Renirie
 * 4+5 jun 2025 dr-input-text.js created
 * 26 sept 2025 dr-input-text.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 */
?>


class DRInputText extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
                box-sizing: border-box;
                border-width: 1px;
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputtext-border, rgb(42, 42, 42)), var(--darkmode-color-drinputtext-border, rgb(232, 232, 232)));
                background-color: light-dark(var(--lightmode-color-drinputtext-background, rgb(255, 255, 255)), var(--darkmode-color-drinputtext-background, rgb(71, 71, 71)));
                height: 24px;
                display: flex;
                width: 100%;
                border-radius: 5px;
            }             

            input
            {
                border-width: 0px;
                width: 100%;
                flex-grow: 1;
                border-radius: 5px; /* needs to have border radius, otherwise cuts off parent in corner */
                background-color: inherit;
            }

            #charcounter
            {
                color: light-dark(var(--lightmode-color-drinputtext-counter, rgb(144, 144, 144)), var(--darkmode-color-drinputtext-counter, rgb(180, 180, 180)));
                font-size: 9px;
                margin-right: 5px;
                margin-left: 5px;
                bottom: 0px;
            }

            #charcounter.warning
            {
                color: light-dark(var(--lightmode-color-drinputtext-counter, rgb(255, 141, 1)), var(--darkmode-color-drinputtext-counter, rgb(255, 223, 142)));
            }            
                
            #charcounter.invalid
            {
                color: light-dark(var(--lightmode-color-drinputtext-counter, rgb(255, 0, 0)), var(--darkmode-color-drinputtext-counter, rgb(255, 142, 142)));
            }         
            
            dr-popover div
            {
                cursor: pointer;
            }

            dr-popover div:hover
            {
                background-color: light-dark(var(--lightmode-color-drinputtext-bubble-item-hover-background, rgb(142, 142, 142)), var(--darkmode-color-drinputtext-bubble-item-hover-background, rgb(177, 177, 177)));
                color: light-dark(var(--lightmode-color-drinputtext-bubble-item-hover, rgb(71, 71, 71)), var(--darkmode-color-drinputtext-bubble-item-hover, rgb(64, 64, 64)));
            }

            dr-popover div.selected
            {
                background-color: light-dark(var(--lightmode-color-drinputtext-bubble-item-selected-background, rgb(32, 84, 254)), var(--darkmode-color-drinputtext-bubble-item-selected-background, rgb(0, 117, 235)));
                color: light-dark(var(--lightmode-color-drinputtext-bubble-item-selected, rgb(255, 255, 255)), var(--darkmode-color-drinputtext-bubble-item-selected, rgb(255, 255, 255)));
            }
            
                
        </style>
        <input type="text">
        <dr-popover showcloseicon="false" showtitle="false" anchorpos="bottom">
            <!-- items are added here -->
        </dr-popover>        
        <label id="charcounter"></label><!-- can be removed by attribute -->
    `;

    sWhitelistCharsTemplate = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890., ~`!@#$%^&*()_+=-[]\\|{}\"':;<>?/";
    sWhitelistCharsTemplateSafe = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890., ~!@#$%^*()_+=-[]|{}:?/"; //safe whitelist that doesn't allow characters used in SQL, XSS, HTML, MultiByte injections

    sSVGCheck = '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M417.4,224H288V94.6c0-16.9-14.3-30.6-32-30.6c-17.7,0-32,13.7-32,30.6V224H94.6C77.7,224,64,238.3,64,256  c0,17.7,13.7,32,30.6,32H224v129.4c0,16.9,14.3,30.6,32,30.6c17.7,0,32-13.7,32-30.6V288h129.4c16.9,0,30.6-14.3,30.6-32  C448,238.3,434.3,224,417.4,224z"/></svg>';
    sSVGCross = '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M417.4,224H94.6C77.7,224,64,238.3,64,256c0,17.7,13.7,32,30.6,32h322.8c16.9,0,30.6-14.3,30.6-32  C448,238.3,434.3,224,417.4,224z"/></svg>';
    
    #bDisabled = false;
    #sValue = "";//internal value
    #sValueInit = "";//initial value. This way we know if the value in the textbox has changed (needed for getDirty())
    #sPlaceholder = "";//value that is removed when the user starts typing
    #iMaxLength = 0;//maximum number of characters allowed                          --> 0 = no limit
    #iMinLength = 0;//min number of characters needed                               --> 0 = no minimum number
    #sWhitelistChars = "";//whitelist of characters allowed in textbox              --> empty = whitelist disabled
    #sBlacklistChars = "";//blacklist of characters that aren't allowed in textbox  --> empty = blacklist disabled
    #arrInvalidityErrors = [];//array of errors which invalidates this editbox (like minlength)
    #bShowCharCounter = false; //always show character counter (otherwise only when minlength or maxlength are exceeded)
    #arrAutoCompleteItems = [];//array with all auto complete items STORED IN LOWERCASE to make case insentive search fast
    #iAutoCompleteItemsMaxResults = 100; //show this maximum number of search results as auto complete items (to save system resources)

    sTransErrorInvalidTitle = "Please correct input:";
    sTransErrorInvalidMinLength = "Minimum length not met";

    #objFormInternals = null;
    #objAbortController = null; //abort controller for the GUI of this component itself (like keypresses)
    #objAbortControllerItems = null; //abort controller for the items in the auto complete bubble
    #objEditBox = null;
    #objBubbleAutoComplete = null; //a dr-popover with auto complete items
    #objCharCounter = null;

    #bConnectedCallbackHappened = false;    

    static formAssociated = true;        

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objFormInternals = this.attachInternals();           
        this.#objAbortController = new AbortController();
        this.#objAbortControllerItems = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputText.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
    }    

    #readAttributes()
    {
        this.#sValue = DRComponentsLib.attributeToString(this, "value", this.#sValue);
        if (!this.#bConnectedCallbackHappened) //update init value
            this.#sValueInit = this.#sValue;
        this.#sValueInit = this.#sValue;
        this.#iMaxLength = DRComponentsLib.attributeToInt(this, "maxlength", this.#iMaxLength);
        this.#iMinLength = DRComponentsLib.attributeToInt(this, "minlength", this.#iMinLength);
        this.#sWhitelistChars = DRComponentsLib.attributeToString(this, "whitelist", this.#sWhitelistChars);
        this.#sBlacklistChars = DRComponentsLib.attributeToString(this, "blacklist", this.#sBlacklistChars);
        this.#bShowCharCounter = DRComponentsLib.attributeToBoolean(this, "charcounter", this.#bShowCharCounter);
        this.#sPlaceholder = DRComponentsLib.attributeToString(this, "placeholder", this.#sPlaceholder);
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", this.#bDisabled);

        //make focussable
        this.setAttribute("tabindex", 0);   
    }


    populate()
    {
        this.#objEditBox = this.shadowRoot.querySelector("input");
        // console.log("popuilate111111111111111 editbox", this.#objEditBox);
        // this.#objEditBox.setAttribute("tabindex", -1);
        this.#objCharCounter = this.shadowRoot.querySelector("label");
        this.#objBubbleAutoComplete = this.shadowRoot.querySelector("dr-popover");
        if (this.#objBubbleAutoComplete)
        {
            this.#objBubbleAutoComplete.anchorobject = this;
        }

        //copy items from DOM to internal auto-complete-array
        const iLenChilds = this.children.length;
        if (iLenChilds > 0)
        {
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++)
                this.#arrAutoCompleteItems.push(this.children[iIndex].innerHTML.toLowerCase());
        }


        //update UI
        this.updateUI();

        //update form value
        this.#objFormInternals.setFormValue(this.getValue());        
    }


    /**
     * attach event listenres
     */
    addEventListeners()
    {
        //KEYDOWN
        this.#objEditBox.addEventListener("keydown", (objEvent)=>
        {
            if (this.#correctInvalidInputKey(objEvent, this.#objEditBox))
            {    
                //if everthing was ok
                return;
            }

            //when things are wrong
            objEvent.preventDefault();

        }, { signal: this.#objAbortController.signal });


        //KEYUP
        this.#objEditBox.addEventListener("keyup", (objEvent)=>
        {
            if (objEvent.key == "ArrowUp")
            {
                this.#focusNextItem(false, false);
                return; //cancel search
            }
            else if (objEvent.key == "ArrowDown")
            {
                this.#focusNextItem(true, false);
                return;//cancel search
            }
           else if (objEvent.key == "Enter")
            {
                if (this.#objBubbleAutoComplete.isShowing())
                {
                    //find selected item
                    let iSelectedIndex = -1;
                    for (let iIndex = 0; iIndex < this.#objBubbleAutoComplete.children.length; iIndex++)
                    {
                        if (this.#objBubbleAutoComplete.children[iIndex].classList.contains("selected"))
                            iSelectedIndex = iIndex;
                    }

                    if (iSelectedIndex >= 0)
                    {
                        this.#updateValue(this.#objBubbleAutoComplete.children[iSelectedIndex].textContent);
                        this.updateUI();

                        //remove bubble
                        this.#objBubbleAutoComplete.hide();

                    }
                }
                return;//cancel search
            }            

            //Continue search
            this.#updateValue(this.#objEditBox.value);

            if (!this.#checkValidity())
            {
                this.classList.add("invalid");
                
            }
            else
            {
                this.classList.remove("invalid");
                // this.#objBubbleErrors.hide();
            }

            this.updateUICharCounter();
            this.#populateItems();

            this.#dispatchEventKeyUp(this.#objEditBox, 'keyup in editbox');
        }, { signal: this.#objAbortController.signal });     
              

        //KEYBOARD SCROLL DISABLE
        //using the up, down and space keys results in the page scrolling, which makes the bubble go away
        document.addEventListener("keydown", (objEvent)=> 
        {          
            if (this.#objBubbleAutoComplete.isShowing())
            {
                switch(objEvent.key)
                {
                    case "ArrowUp":
                    case "ArrowDown":
                        objEvent.preventDefault();
                        break;
                    case " ": //spacebar also prevents spacebar in search
                        if (this.shadowRoot.activeElement !== this.#objEditBox) //avoid search bar
                            objEvent.preventDefault();

                }
            }
        }, { signal: this.#objAbortController.signal });           


        //CHANGE event: editbox
        this.#objEditBox.addEventListener("change", (objEvent)=>
        {  
            this.dispatchEvent(new CustomEvent("change",
            {
                bubbles: true,
                detail:
                {
                    source: this
                }
            }));
        }, { signal: this.#objAbortController.signal });  
    }


    /**
     * attach event listeners to the auto complete items
     */
    addEventListenersItems(objPapa)
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            //MOUSE CLICK
            objPapa.children[iIndex].addEventListener("click", (objEvent)=>
            {
                this.#updateValue(objPapa.children[iIndex].textContent);

                this.updateUI();

                //remove bubble
                this.#objBubbleAutoComplete.hide();

            }, { signal: this.#objAbortControllerItems.signal });       
        

            //KEYBOARD events: navigating items in list and enter-key
            objPapa.children[iIndex].addEventListener("keyup", (objEvent)=>
            {   
                switch (objEvent.key)
                {
                    case "ArrowUp":
                        this.#focusNextItem(false, true);
                        break;
                    case "ArrowDown":
                        this.#focusNextItem(true, true);
                        break;
                    case "Enter":
                        this.#updateValue(objPapa.children[iIndex].textContent);
                        this.updateUI();

                        //remove bubble
                        this.#objBubbleAutoComplete.hide();

                        break;
                }
            }, { signal: this.#objAbortControllerItems.signal });                      


           //recursively calling children
            //this.addEventListenersItems(objPapa.children[iIndex]); ==> for now we only support 1 level
        }
    }


    #updateValue(sValue)
    {
        this.#objEditBox.value = sValue;
        this.#sValue = sValue;
        this.#objFormInternals.setFormValue(sValue);       
        if (this.getDirty())   
            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
    }

    /**
     * focusses next or previous item in itemlist
     * 
     * @param {boolean} bNext true=next, false=previous
     */
    #focusNextItem(bNext)
    {
        //declarations
        let iCurrentIndex = -1;
        let iNewIndex = -1;

        //if list is empty: exit
        if (this.#objBubbleAutoComplete === null)
            return;

        if (this.#objBubbleAutoComplete.isHidden())
            return;

        if ((this.#objBubbleAutoComplete.children.length == 0))
            return;


        //find currently selected item
        for (let iIndex = 0; iIndex < this.#objBubbleAutoComplete.children.length; iIndex++)
        {
            if (this.#objBubbleAutoComplete.children[iIndex].classList.contains("selected")) 
            {
                iNewIndex = iIndex;
                iIndex = this.#objBubbleAutoComplete.children.length; //quit loop
            }
        }

        //add one or subtract (based in next or previous)
        if (bNext)
            iNewIndex += 1;//note: if iCurrentIndex == -1 (not found) then +1 will make it index 0
        else
            iNewIndex -= 1;

        
        //keep index within array bounds
        if (iNewIndex >= this.#objBubbleAutoComplete.children.length) //wrap around to beginning
            iNewIndex = 0;
        if (iNewIndex < 0) //wrap around to end
            iNewIndex = this.#objBubbleAutoComplete.children.length -1; 


        //remove all "selected" classes
        for (let iIndex = 0; iIndex < this.#objBubbleAutoComplete.children.length; iIndex++)
            this.#objBubbleAutoComplete.children[iIndex].classList.remove("selected");

        //highlight new item: add "selected" class
        this.#objBubbleAutoComplete.children[iNewIndex].classList.add("selected");

        //update value
        this.#sValue = this.#objBubbleAutoComplete.children[iNewIndex].textContent;
        this.#objEditBox.value = this.#objBubbleAutoComplete.children[iNewIndex].textContent;
    }

    /**
     * validate key presses for each input box on onKeyDown-event
     * 
     * this can't check for valid values, because it's too early
     * (the keypress isn't complete yet, so we don't have the completed value yet)
     * 
     * @param {Event} objEvent 
     * @param {HTMLElement} objEdtSource editbox that triggered the event
     * @param {integer} iMaxValue max value allowed. 0 = do not check ==> this checks ONLY for up and down key
     * @returns boolean true = valid, false = invalid 
     */
    #correctInvalidInputKey(objEvent, objEdtSource)
    {

        //allow normal operations
        switch (objEvent.key)
        {
            case "ArrowUp":
            case "ArrowDown":
            case "ArrowLeft":
            case "ArrowRight":
            case "Home":
            case "End":
            case "Enter":
            case "Backspace":
            case "Delete":
            case "Tab":
                return true;
        }


        //allow "paste"
        if ((objEvent.ctrlKey || objEvent.metaKey) && (objEvent.key == "v"))
        {
            return true;
        }           

        //allow "copy"
        if ((objEvent.ctrlKey || objEvent.metaKey) && (objEvent.key == "c"))
        {
            return true;
        }           

        //allow select-all
        if ((objEvent.ctrlKey || objEvent.metaKey) && (objEvent.key == "a"))
        {
            return true;
        }   

        //whitelist
        if (this.#sWhitelistChars !== "")
        {
            //check key against each element in whitelist
            for (let iIndex = 0; iIndex < this.#sWhitelistChars.length; iIndex++)
            {
                if (this.#sWhitelistChars[iIndex] === objEvent.key)
                    return true;
            }

            return false;
        }



        //blacklist ==> last in the list to check
        if (this.#sBlacklistChars !== "")
        {
            //check key against each element in whitelist
            for (let iIndex = 0; iIndex < this.#sBlacklistChars.length; iIndex++)
            {
                if (this.#sBlacklistChars[iIndex] === objEvent.key)
                    return false;
            }
            return true;
        }
        else
            return true;
   

        return false;
    }

    /**
     * checks validy of edit box
     * 
     * @return bool true=ok, false=bad
     */
    #checkValidity()
    {
        this.#arrInvalidityErrors = [];

        if (this.#iMinLength > 0)
        {
            if (this.#objEditBox.value.length < this.#iMinLength)
            {
                this.#arrInvalidityErrors.push(this.sTransErrorInvalidMinLength + ": " + this.#iMinLength);
                return false;
            }
        }

        return true;
    }

    /**
     * dispatch event that value has changed
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventInputUpdated(objSource, sDescription)
    {
        //probably something changed, thus update the form value
        this.#objFormInternals.setFormValue(this.getValue());
        // console.log("setformvalue dispatchEventInputUpdated",this.getValue());
            
        this.dispatchEvent(new CustomEvent("update",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription
            }
        }));
    }    

    /**
     * dispatch keyup event 
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventKeyUp(objSource, sDescription)
    {
        //probably something changed, thus update the form value
        this.#objFormInternals.setFormValue(this.getValue());
            
        // console.log("dispatchEventKeyUp(): dispatch keyup");

        this.dispatchEvent(new CustomEvent("keyup",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription
            }
        }));
    }    

    updateUI()
    {
        // console.log("updateUI1111111111 editbox", this.#objEditBox, this.#bConnectedCallbackHappened);
        if (this.#objEditBox)//when editbox exists
        {
            this.#objEditBox.value = this.#sValue;
            if (this.#iMaxLength > 0)
                this.#objEditBox.maxLength = this.#iMaxLength;
            if (this.#iMinLength > 0)
                this.#objEditBox.minLength = this.#iMinLength;
            if (this.#sPlaceholder)
                this.#objEditBox.placeholder = this.#sPlaceholder;
            
            //handle disabled
            if (this.#bDisabled)
            {
                this.#objEditBox.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
            }
            else
            {
                this.#objEditBox.removeAttribute("disabled");
            }

            this.updateUICharCounter();
        }
    }

    /**
     * update the character counter
     * 
     * Async function to not choke typing experience while updating
     */
    async updateUICharCounter()
    {
        if (this.#objCharCounter) //can be not found or disabled
        {
            let sHTMLCounter = "";
            sHTMLCounter = this.#sValue.length.toString();

            //add "/"
            if ((this.#iMinLength > 0) || (this.#iMaxLength > 0)) 
                sHTMLCounter += "/";

            //show min length
            if (this.#iMinLength > 0)
                sHTMLCounter += this.#iMinLength.toString();

            //if both min and maxlength then add "-"
            if ((this.#iMinLength > 0) && (this.#iMaxLength > 0)) 
                sHTMLCounter += "-"

            //show max length
            if (this.#iMaxLength > 0)
                sHTMLCounter += this.#iMaxLength.toString();
            
            this.#objCharCounter.innerHTML = sHTMLCounter;            
            

            //==== check length limits + add css class "invalid"
            let bInvalid = false;
            let bWarning = false;
            if (this.#iMinLength > 0)
            {
                if (this.#objEditBox.value.length < this.#iMinLength)
                    bInvalid = true;

                //warn when min length is near
                if (this.#objEditBox.value.length == this.#iMinLength)                
                    bWarning = true;

            }

            if (this.#iMaxLength > 0)
            {
                if (this.#objEditBox.value.length > this.#iMaxLength - 1) //-1 is technically not invalid, but just to communicate the user reached the end
                    bInvalid = true;

                //warn 5 characters before limit
                if (this.#objEditBox.value.length > this.#iMaxLength - 5) 
                    bWarning = true;
            }

            //show error/warning in UI
            if (bInvalid || bWarning)
            {
                //add character counter when not showing
                if (this.shadowRoot.querySelector("#charcounter") === null)
                    this.shadowRoot.append(this.#objCharCounter);
            }
            else
            {

                //remove charcounter when not always showing
                if (!this.#bShowCharCounter)
                {
                    if (this.shadowRoot.querySelector("#charcounter") !== null)
                        this.shadowRoot.removeChild(this.#objCharCounter);
                }
            }

            if (bInvalid)
                this.#objCharCounter.classList.add("invalid");
            else
                this.#objCharCounter.classList.remove("invalid");

            if (bWarning)
                this.#objCharCounter.classList.add("warning");
            else
                this.#objCharCounter.classList.remove("warning");

        }

        
    }
    
    /**
     * deals with searching the auto complete items and showing the info bubble 
     * 
     * ==>ASYC: BE AWARE OF THE RACE CONDITION:
     * if search results are inconsistent, cancel the previous promise
     */
    async #populateItems()
    {
        const sSearch = this.#objEditBox.value.toLowerCase(); //case insensitive search
        const iLenItems = this.#arrAutoCompleteItems.length; //I cache length because this array can be big
        const arrSearchResults = [];
        let sHTML = "";

        //actual search
        if (sSearch.length > 1) //need more than 2 characters to search
        {
            for (let iIndex = 0; iIndex < iLenItems; iIndex++)
            {
                if (this.#arrAutoCompleteItems[iIndex].indexOf(sSearch) >= 0)
                    arrSearchResults.push(this.#arrAutoCompleteItems[iIndex]);
            }
        }


        //display results
        if (arrSearchResults.length > 0)
        {
            for (let iIndex = 0; iIndex < arrSearchResults.length; iIndex++)
            {
                sHTML += "<div>" + arrSearchResults[iIndex] + "</div>";//build html (we don't want to render the html for each item)
                // console.log("asdffffffffffffffffffffffffffffff");

                //quit loop when max amount of results is reached (to save resources)
                if (iIndex >= this.#iAutoCompleteItemsMaxResults -1)
                    iIndex = arrSearchResults.length;
            }
            this.#objBubbleAutoComplete.innerHTML = sHTML; //innerHTML renders
            this.#objBubbleAutoComplete.show();
        }
        else
            this.#objBubbleAutoComplete.hide();

        // console.log("search results:", arrSearchResults);



        //CHILDREN: add event listeners to all items
        this.removeEventListenersItems();
        this.#objAbortControllerItems = new AbortController;
        this.addEventListenersItems(this.#objBubbleAutoComplete);                
    }

    getValue()
    {
        return this.#sValue;
    }

    setValue(sValue)
    {
        this.#sValue = sValue;
        this.#objFormInternals.setFormValue(sValue); 
        this.updateUI();
    }

    getDirty()
    {
        return (this.#sValue !== this.#sValueInit);
    }


    getMaxLength()
    {
        return this.#iMaxLength;
    }

    setMaxLength(iLength)
    {
        this.#iMaxLength = iLength;
        this.updateUI();
    }

    getMinLength()
    {
        return this.#iMinLength;
    }

    setMinLength(iLength)
    {
        this.#iMinLength = iLength;
        this.updateUI();
    }

    setCharCounter(bShow)
    {
        this.#bShowCharCounter = bShow;
    }

    getCharCounter()
    {
        return this.#bShowCharCounter;
    }

    /**
     * returns internal array with auto complete itesm
     * @returns Array
     */
    getAutoCompleteItems()
    {
        return this.#arrAutoCompleteItems;
    } 

    /**
     * replaces internal array with array of auto complete items
     * 
     * @param {Array} arrItems 
     */
    setAutoCompleteItems(arrItems)
    {
        //convert to lowercase
        const iLenItems = arrItems.length;
        for (let iIndex = 0; iIndex < iLenItems; iIndex++)
        {
            arrItems[iIndex] = arrItems[iIndex].toLowerCase();
        }

        //set value
        this.#arrAutoCompleteItems = arrItems;
    }

    /**
     * sets placeholder value, a value that is removed when the user starts typing
     * @param {string} sValue 
     */
    setPlaceHolder(sValue)
    {
        this.#sPlaceholder = sValue;
    }

    getPlaceHolder()
    {
        return this.#sPlaceholder;
    }

    setWhitelist(sValue)
    {
        this.#sWhitelistChars = sValue;
    }

    getWhitelist()
    {
        return this.#sWhitelistChars;
    }


    /** 
     * get disabled
    */
    get disabled()
    {
        return DRComponentsLib.boolToStr(this.#bDisabled);
    }    

    /** 
     * set disabled
    */
    set disabled(bValue)
    {
        this.#bDisabled = DRComponentsLib.strToBool(bValue);
    }

    /**
     * return content of this edit box
     */
    get value()
    {
        return this.#sValue;
    }

    /**
     * returns if user changed contents of this box
     */
    get dirty()
    {
        return this.getDirty();
    }

    /** 
     * set internal value 
    */
    set value(sValue)
    {
        this.setValue(sValue);
    }


    get maxlength()
    {
        return this.getMaxLength();
    }

    set maxlength(iLength)
    {
        this.setMaxLength(iLength);
    }

    get minlength()
    {
        return this.getMinLength();
    }

    set minlength(iLength)
    {
        this.setMinLength(iLength);
    }

    get charcounter()
    {
        return this.getCharCounter();
    }

    set charcounter(bShow)
    {
        this.setCharCounter(bShow);
    }

    get placeholder()
    {
        return this.getPlaceHolder();
    }

    set placeholder(sValue)
    {
        this.setPlaceHolder(sValue);
    }

    get whitelist()
    {
        return this.getWhitelist();
    }

    set whitelist(sCharacters)
    {
        this.setWhitelist(sCharacters);
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
        this.removeEventListenersItems();
    }

    /**
     * removes event listeners from auto-complete items
     */
    removeEventListenersItems()
    {
        this.#objAbortControllerItems.abort();
    }    


    static get observedAttributes() 
    {
        return ["disabled", "value", "maxlength", "minlength", "charcounter", "placeholder"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "disabled":
                this.disabled = sNewVal;
                break;
            case "value":
                this.value = sNewVal;
                break;
            case "maxlength":
                this.maxlength = sNewVal;
                break;
            case "minlength":
                this.minlength = sNewVal;
                break;
            case "charcounter":
                this.charcounter = sNewVal;
                break;
            case "placeholder":
                this.placeholder = sNewVal;
                break;
        }

        if (this.#bConnectedCallbackHappened)
            this.updateUI();        
    }   

    /** 
     * When added to DOM
     */
    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //read attributes
            this.#readAttributes();
            this.#objFormInternals.setFormValue(this.getValue()); 
            // console.log("setformvalue connectedCallback",this.getValueAsString("."));

            //render
            this.populate();
        }

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();
        // if (this.#objAbortControllerItems.signal.aborted) => this.#populateItems() takes care of this
        //     this.#objAbortControllerItems = new AbortController();        

        //event
        this.addEventListeners();


        this.#bConnectedCallbackHappened = true;           
   }

    /** 
     * remove from DOM 
     **/
    disconnectedCallback()
    {
        this.removeEventListeners();
    }


}


/**
 * make component available in HTML
 */
customElements.define("dr-input-text", DRInputText);