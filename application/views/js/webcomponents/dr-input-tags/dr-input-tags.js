<?php 
/**
 * dr-input-tags.js
 *
 * class to create tags as chips
 * The <div> around a tag text I call a chip. 
 * Each tag has a chip
 * 
 * FILTER-CHANGE EVENT:
 * object fires event "change" when tags changed (text changed, added or deleted):
 * window.addEventListener('change', (e) => { console.log(e)})
 * 
 * WARNING:
 * - tags are assumed to be unique. You can't add 2 tags that are the same.
 * 
 * DEPENDENCIES:
 * 
 * @todo click to change value
 * @todo maxlength
 * @todo values oppakken vanuit slot
 * @todo remove all
 * @todo copy + paste
 * @todo input comma to add multiple tags
 * @todo maximum and minimum number of tags
 * 
 * @author Dennis Renirie
 * 
 * 15 okt 2025 dr-input-tags.js created
 */
?>


class DRInputTags extends HTMLElement
{

    static sTemplate = `
        <style>
            :host
            {
                display: flex;
                flex-wrap: wrap;
                flex-direction: row;
                gap: 5px;
                user-select: none;
                font-family: inherit;

                
                box-sizing: border-box;
                border-width: 1px;
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputtext-border, rgb(42, 42, 42)), var(--darkmode-color-drinputtext-border, rgb(232, 232, 232)));
                background-color: light-dark(var(--lightmode-color-drinputtext-background, rgb(255, 255, 255)), var(--darkmode-color-drinputtext-background, rgb(71, 71, 71)));
                min-height: 40px;
                display: flex;
                width: 100%;
                border-radius: 5px;

                padding: 5px;                
            }

            .chip
            {
                background-color: light-dark(var(--lightmode-color-drdbfilters-chips-background, rgb(236, 236, 236)), var(--darkmode-color-drdbfilters-chips-background, rgb(105, 105, 105)));
                color: light-dark(var(--lightmode-color-drdbfilters-chips, rgb(39, 39, 39)), var(--darkmode-color-drdbfilters-chips, rgb(234, 234, 234)));
                flex-grow: 0;
                display: inline-flex;
                border-radius: 25px;
                height: 1.9rem;

                line-height: 1.2rem;
                font-size: 0.65rem;
                cursor: pointer;
            }

            .highlight:hover
            {
                background-color: light-dark(var(--lightmode-color-drdbfilters-chips-highlight-background-hover, rgb(216, 216, 216)), var(--darkmode-color-drdbfilters-chips-highlight-background-hover, rgb(124, 124, 124)));
            }

            .highlightinside:hover
            {
                background-color: light-dark(var(--lightmode-color-drdbfilters-chips-highlightinside-background-hover, rgb(196, 196, 196)), var(--darkmode-color-drdbfilters-chips-highlightinside-background-hover, rgb(152, 152, 152)));
            }

            .chipinnertext
            {
                padding: 5px;
                padding-left: 15px;

                text-overflow: ellipsis;
                max-width: 150px;
                overflow: hidden; 
                white-space: nowrap;
            }


            .chipinnertext:hover
            {
                border-radius: 25px; repeat of .chip  
            }


            .chipicon
            {
                border-radius: 20px;
                margin: 0px;
                height: 1.3rem;
                width: 1.3rem;
                padding: 5px;
            }


            .chipinput
            {
                flex-grow: 1;    
            }

            .chipinput [type=text]
            {
                box-sizing: border-box;

                width: 100%;
                background-color: #00000000; /* transpararent */
                border-width: 0px;
                padding: 0px;
                padding-bottom: 5px;
                padding-top: 5px;
                margin: 0px;
                margin-left: 5px;
                font-size: 0.8rem;
                font-family: inherit;
            }

        </style>

    `;

    sSVGIconRemove = '<svg stroke="currentColor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-2 -2 28 28" stroke-width="1.5" ><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
    
    #objFormInternals = null;    
    #arrChipsAbortControllers = []; //array with abort controllers for filter chips themselves. Each item in array is a AbortController object for a chip
    #objAbortController = null;
    #bConnectedCallbackHappened = false;
    #objInputText = null;
    #objAddNewTagChip = null;
    
    #sValueSeparator = ",";
    #sValue = "";//form value. Values are separated by #sValueSeparator
    sTransEditboxPlaceholder = "Enter new tag";
    #bDisabled = false;
    #sWhiteListChars = ""; //there characters are allowed in a chip. When empty, whitelist feature is disabled

    static formAssociated = true;      

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objFormInternals = this.attachInternals();          
        this.#objAbortController = new AbortController();

        this.attachShadow({mode: "open"});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputTags.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);
    }    

    #readAttributes()
    {
        this.#sValue = DRComponentsLib.attributeToString(this, "value", this.#sValue);
        this.#sValueSeparator = DRComponentsLib.attributeToString(this, "separator", this.#sValueSeparator);
        this.#sWhiteListChars = DRComponentsLib.attributeToString(this, "whitelist", this.#sWhiteListChars);
        this.sTransEditboxPlaceholder = DRComponentsLib.attributeToString(this, "placeholder", this.sTransEditboxPlaceholder);
    }

    #populate()
    {
        //=== add div and edit-box element to enter new tag
        this.#objAddNewTagChip = document.createElement("div");
        this.#objAddNewTagChip.classList.add("chipinput");
        this.#objInputText = document.createElement("input");
        this.#objInputText.type = "text";
        this.#objInputText.placeholder = this.sTransEditboxPlaceholder;
        this.#objAddNewTagChip.appendChild(this.#objInputText);
        this.shadowRoot.appendChild(this.#objAddNewTagChip);     
        
        //update UI
        this.updateUI();

        //update form value
        this.#objFormInternals.setFormValue(this.getValue());        
    }



    /**
     * add event listeners to component 
     */
    addEventListeners()
    {         
        this.#objInputText.addEventListener("keydown", (objEvent) => 
        {
            if (!objEvent.repeat)
            {
                if ((objEvent.key == "Enter")  || (objEvent.key == this.#sValueSeparator))
                {
                    //don't accept value separator as input
                    if (objEvent.key == this.#sValueSeparator)
                        objEvent.preventDefault(); 

                    if (this.#objInputText.value.length >= 1) //must have at least 1 character
                    {
                        //filter white list
                        let sCleanValue = DRComponentsLib.sanitizeWhitelist(this.#objInputText.value, this.#sWhiteListChars);
                        sCleanValue = sCleanValue.replace(this.#sValueSeparator, "");

                        if (!this.chipExists(sCleanValue)) //add when unique
                        {                   
                            //update values         
                            if (this.#sValue === "") //empty value: no separator
                                this.#sValue+= sCleanValue;
                            else
                                this.#sValue+= this.#sValueSeparator + sCleanValue;
                            this.#objFormInternals.setFormValue(this.#sValue);

                            console.log("dr-input-tags: add: this.#sValue == ", this.#sValue);

                            this.updateUI();
                            this.#dispatchEventTagsChanged(this.#objInputText, "Enter on keyboard");
                            this.#objInputText.value = "";
                        }
                        else
                        {
                            DRComponentsLib.beep();
                            console.error("dr-input-tags: value not accepted, because tag already exists");                            
                        }
                    }
                }
            }
        }, { signal: this.#objAbortController.signal });  
    }


    /**
     * add event listeners to chip
     * 
     * @param {HTMLElement}
     */
    #addEventListenersChip(objDivChip)
    {
        const objCtrl = new AbortController();
        const objBtnRemove = objDivChip.querySelector(".remove");
        const objDivText = objDivChip.querySelector(".chipinnertext");

        //==== text
        if (!this.#bDisabled)
        {
            objDivText.addEventListener("mousedown", (objEvent) => 
            {            

                //@todo edit text on chip

            }, { signal: objCtrl.signal });   
        }

        //===== remove
        if ((objBtnRemove) && (!this.#bDisabled))
        {
            objBtnRemove.addEventListener("mousedown", (objEvent) => 
            {
                console.log("button removeeeeeee", objBtnRemove);

                //remove event listener
                let sChipText = objDivText.textContent;
                if (objDivText)
                {
                    this.#arrChipsAbortControllers[sChipText].abort();
                }

                //remove from DOM
                this.shadowRoot.removeChild(objDivChip);

                //update values
                this.#sValue = this.#chipsToValue(); //update internal value
                this.#objFormInternals.setFormValue(this.#sValue);   //update form value

                console.log("dr-input-tags: remove: this.#sValue == ", this.#sValue);

                //dispatch filter-changed-event based on "disabled" attribute --> AFTER being removed from DOM
                if (!DRComponentsLib.attributeToBoolean(objDivChip, this.#bDisabled, false))                
                    this.#dispatchEventTagsChanged(this.#objInputText, "Clicked remove chip");
            }, { signal: objCtrl.signal });      
        }   

        //add controllers to internal array
        this.#arrChipsAbortControllers[objDivText.textContent] = objCtrl;         
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        //remove all on parent abort controller
        this.#objAbortController.abort();

        //remove all on filter chips
        for (let sKey in this.#arrChipsAbortControllers) 
        {
            // console.log("removed", sKey);
            this.#arrChipsAbortControllers[sKey].abort();
        }
    }

    /**
     * add tag to filters
     * 
     * @param {string} sText text on tag
     */
    addTagUI(sText)
    {
        const objChip = document.createElement("div"); //create new revamped chip
        objChip.classList.add("chip");
        objChip.classList.add("highlight");

        //add text to div
        const objText = document.createElement("div");
        objText.classList.add("chipinnertext");
        objText.innerHTML = sText;
        objChip.appendChild(objText);         

        //add X icon div
        const objXButton = document.createElement("div");  
        objXButton.classList.add("chipicon");
        objXButton.classList.add("highlightinside");
        objXButton.classList.add("remove");
        objXButton.innerHTML = this.sSVGIconRemove;  
        objChip.appendChild(objXButton);    


        //determine the right position to add to DOM: before the chipt with the textbox
        this.#objAddNewTagChip.before(objChip);
        
        //respond to user
        this.#addEventListenersChip(objChip);

        return objChip;
    }

    /**
     * check if text already exists on a chip
     * 
     * @param {string} sText 
     * @returns {boolean} true=exists false=doesnt exist
     */
    chipExists(sText)
    {
        const arrTexts = [...this.shadowRoot.querySelectorAll(".chipinnertext")];
        const iLenChips = arrTexts.length;

        for (let iIndex = 0; iIndex < iLenChips; iIndex++)
        {
            if (arrTexts[iIndex].textContent == sText)
                return true;
        }

        return false;
    }

    /**
     * creates chips in the UI based on the string value
     * the values in the string are separated by the internal value sepator
     */
    #valueToChips(sValuesSeparatedByValueSeparator)
    {
        const arrValues = sValuesSeparatedByValueSeparator.split(this.#sValueSeparator);
        const iLenValues = arrValues.length;
        let sCleanValue = "";

        for (let iIndex = 0; iIndex < iLenValues; iIndex++)
        {
            sCleanValue = DRComponentsLib.sanitizeWhitelist(arrValues[iIndex], this.#sWhiteListChars); //filter white list
            sCleanValue = sCleanValue.replace(this.#sValueSeparator, "");

            if (sCleanValue != "") //empty not allowed
                if (!this.chipExists(sCleanValue)) //add when unique
                    this.addTagUI(sCleanValue);   
        }
    }

    /**
     * converts the text of the chips into a string where values are separated by the value separator
     * @returns {string}
     */
    #chipsToValue()
    {
        const arrTextDivs = [...this.shadowRoot.querySelectorAll(".chipinnertext")];
        const iLenChips = arrTextDivs.length;
        const arrValues = [];

        for (let iIndex = 0; iIndex < iLenChips; iIndex++)
            arrValues.push(arrTextDivs[iIndex].textContent);

        return arrValues.join(this.#sValueSeparator);        
    }

    /**
     * update User Interface
     */
    updateUI()
    {
        this.#valueToChips(this.#sValue);
    }


    /** 
     * set value separator
     * i.e. comma (,)
    */
    setSeparator(sSeparator)
    {
        this.#sValueSeparator = sSeparator;
        this.updateUI();
    }

    /** 
     * get value separator
     * i.e. dot (.)
    */
    getSeparator()
    {
        return this.#sValueSeparator;
    }

    /** 
     * set values separated b value separator
    */
    setValue(sValues)
    {
        this.#sValue = sValues;
        this.#objFormInternals.setFormValue(sValues);         
        this.updateUI();
    }

    /** 
     * get value where values are separated by value separator
    */    
    getValue()
    {
        return this.#sValue;
    }

    /** 
     * set disabled
    */
    setDisabled(bValue)
    {
        this.#bDisabled = bValue;
        // this.#objFormInternals.setFormValue(sValues);         
        this.updateUI();
    }

    /** 
     * get value where values are separated by value separator
    */    
    getDisabled()
    {
        return this.#bDisabled;
    }

    /** 
     * set whitelist characters that are allowed in tag
    */
    setWhitelist(sCharsWhitelist)
    {
        this.#sWhiteListChars = sCharsWhitelist;
        // this.#objFormInternals.setFormValue(sValues);         
        // this.updateUI();
    }

    /** 
     * get whitelist characters that are allowed in tag
    */    
    getWhitelist()
    {
        return this.#sWhiteListChars;
    }


    /** 
     * set value separator
     * i.e. comma (,)
    */
    set separator(sSeparator)
    {
        this.setSeparator(sSeparator);
    }

    /** 
     * get value separator
     * i.e. dot (.)
    */
    get separator()
    {
        return this.getSeparator();
    }

    /** 
     * set values separated by value separator
    */
    set value(sValues)
    {
        this.setValue(sValues);
    }

    /** 
     * get values separated by value separator
    */
    get value()
    {
        return this.getValue();
    }

    /** 
     * set disabled
    */
    set disabled(bDisabled)
    {
        this.setDisabled(bDisabled);
    }

    /** 
     * get disabled
    */
    get disabled()
    {
        return this.getDisabled();
    }

    /** 
     * set whitleist
    */
    set whitelist(sWhitelistChars)
    {
        this.setWhitelist(sWhitelistChars);
    }

    /** 
     * get whitleist
    */
    get whitelist()
    {
        return this.getWhitelist();
    }

 
    /**
     * broadcasts "change" event
     * 
     * @param {HTMLElement} objSource 
     * @param {string} sDescription 
     */
    #dispatchEventTagsChanged(objSource, sDescription)
    {
        // console.log("dispatch event", sDescription);
        this.#objFormInternals.setFormValue(this.getValue());

        this.dispatchEvent(new CustomEvent("change",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription
            }
        }));
    }

    static get observedAttributes() 
    {
        return ["separator", "value", "disabled", "whitelist"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in combobox", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "disabled":
                this.#bDisabled = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "separator":
                this.#sValueSeparator = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "value":
                this.#sValue = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "whitelist":
                this.#sWhiteListChars = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
        }
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

            //render
            this.#populate();
        }

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();

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
customElements.define("dr-input-tags", DRInputTags);

