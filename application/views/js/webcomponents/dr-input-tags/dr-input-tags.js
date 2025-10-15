<?php 
/**
 * dr-input-tags.js
 *
 * class to create tags as chips
 * 
 * FILTER-CHANGE EVENT:
 * object fires event "change" when filter changed:
 * window.addEventListener('change', (e) => { console.log(e)})
 * 
 * WARNING:
 * 
 * DEPENDENCIES:
 * 
 * @todo click to change value
 * @todo click X to remove tag
 * @todo maxlength
 * @todo values oppakken vanuit slot
 * @todo values uit attribuut lezen
 * @todo waardes uit chips lezen en als values setten
 * @todo whitelist filteren als enter
 * @todo filter duplicates
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
            }

            .highlight:hover
            {
                cursor: pointer;
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
        this.#sValueSeparator = DRComponentsLib.attributeToString(this, "separator", this.#sValueSeparator);
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
                if (objEvent.key == "Enter")
                {
                    if (this.#objInputText.value.length >= 1) //must have at least 1 character
                    {
                        this.addTagUI(this.#objInputText.value);   
                        this.#objInputText.value = "";
                        this.#dispatchEventTagsChanged(this.#objInputText, "Hit enter on keyboard");
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
     * update User Interface
     */
    updateUI()
    {
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
        return ["separator", "value"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in combobox", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "separator":
                this.separator = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "value":
                this.value = sNewVal;
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

