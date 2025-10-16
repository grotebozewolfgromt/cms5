
<?php 
/**
 * dr-input-combobox.js
 * replaces the html <select><option></option></select>
 * The actual contents doesn't matter as long as individual items have a "value"-attribute that can will be returned with the getValue() function
 * 
 * FEATURES:
 * - allows HTML in items
 * - proper searching
 * - select multiple items
 * - placeholder
 * 
 * WARNING:
 * - A html tag is regarded an item when it has the attribute: "value". If it doesn't have it, it won't be found with search and no checkboxes will be added in multiselect-mode
 * 
 * REASONING FOR SEARCH BAR:
 * The search bar is on top of the list items.
 * The reason for this is: if the list is long (like on mobile), the bubble could overlap the input field and the user can't see its input anymore
 * 
 * DISPATCHES EVENT:
 * - "change" when value in editbox has changed
 * 
 * DEFINITIONS:
 * - item: list of items shown in combobox
 * 
 * DEPENDENCIES:
 * -dr-popover.js
 * 
 * EXAMPLE:
 * <dr-input-combobox placeholder="select an item" value="1,2,3" valueseparator="," type="selectmultiple" placeholdersearch="Zoeken">
    <div value="1">item 1</div> <!-- needs to have "value"-attribute!!!! -->
    <div value="2">item 2</div>
    <div value="3">item 3</div>
 * </dr-input-combobox>
 *
 *
 * @todo fuzzy search: https://www.youtube.com/watch?v=xM45BxB8ZfE
 *  
 * @author Dennis Renirie
 * 
 * 15 may 2025 dr-input-combobox.js created
 * 20 may 2025 dr-input-combobox.js zoeken mogelijk zonder selectie te verwijderen
 * 21 may 2025 dr-input-combobox.js componenten vanuit constructor verplaatsen naar connectedCallback()
 * 23 may 2025 dr-input-combobox.js werkt nu via connected callback met eventlisteners items over te plaatsen
 * 23 may 2025 dr-input-combobox.js alwaysreturnvalue"  attribute toegevoegd
 * 23 may 2025 dr-input-combobox.js getValue() en labelvalue geeft nu defaults weer (eerste geselecteerde item)
 * 4 jun 2025 dr-input-combobox.js setValue() en readAttributes() checken op bConnectedCallbackHappened
 * 17 jun 2025 dr-input-combobox.js checks if there are any items with value attribute
 * 18 jun 2025 dr-input-combobox.js scrolls only itemlist when too big, not the searchbar
 * 18 jun 2025 dr-input-combobox.js better selected and hover colors
 * 18 jun 2025 dr-input-combobox.js stores old value, which is dispatched as parameter with "change" event
 * 18 jun 2025 dr-input-combobox.js css: items don't wrap
 * 26 sept 2025 dr-input-combobox.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 16 okt 2025 dr-input-combobox.js rename sValueOld => sValuePrevious
 * 16 okt 2025 dr-input-combobox.js add: getValuePrevious(); en get valueprevious()
 * 16 okt 2025 dr-input-combobox.js bugfixes: valuePrevious and valueInit wordt nu geset bij het kopieren naar shadowRoot
 */
?>


class DRInputCombobox extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
                box-sizing: border-box;
                border-width: 1px;
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputcombobox-border, rgb(42, 42, 42)), var(--darkmode-color-drinputcombobox-border, rgb(232, 232, 232)));
                background-color: light-dark(var(--lightmode-color-drinputcombobox-background, rgb(255, 255, 255)), var(--darkmode-color-drinputcombobox-background, rgb(71, 71, 71)));
                height: 24px;
                display: flex;
                width: 200px;
                border-radius: 5px;                
            }             

            button
            {
                padding: 5px;

                border-width: 0px;
                background-color: light-dark(var(--lightmode-color-drinputcombobox-button-background, rgb(255, 255, 255)), var(--darkmode-color-drinputcombobox-button-background, rgb(71, 71, 71)));
                cursor: pointer;
                flex-grow: 1;
                border-radius: 5px; /* needs to have border radius, otherwise cuts off parent in corner */
            }

            button svg
            {   
                width: 12px;
                height: 12px;
            }

            button.disabled
            {   
                opacity: 0.2;
            }            

            /* search box */
            input
            {
                border-width: 0px;
                width: 100%;
                box-sizing: border-box;
                border-radius: 5px; /* needs to have border radius, otherwise cuts off parent in corner */
                margin-top: 5px;
                margin-bottom: 8px;
                height: 25px;
                padding-left: 5px;
                padding-right: 5px;

                background-color: light-dark(var(--lightmode-color-drinputcombobox-search-background, rgb(233, 233, 233)), var(--darkmode-color-drinputcombobox-search-background, rgb(85, 85, 85)));

            }
            
            /* text that shows which element is selected */
            label
            {
                padding: 2px;
                padding-left: 5px;
                border-width: 0px;
                width: 100%;
                flex-grow: 1;
                cursor: pointer;
                user-select: none;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }
                
            dr-popover 
            {
                padding: 10px;
                user-select: none;
            }

            #itemlist
            {
                overflow-x: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                overflow-y: scroll;
                height: calc(100% - 40px); /* height because of the scrolling. -40px because of the search box height */
            }

            #itemlist *
            {
                padding-top: 2px;
                padding-bottom: 2px;
                padding-left: 10px;
                padding-right: 10px;               
            }            
       

            #itemlist dr-input-checkbox,
            #itemlist dr-input-checkbox-group
            {
                padding:0px;
            }                    

            #itemlist [value]:hover 
            {
                cursor: pointer;
                border-radius: 5px;                
                background-color: light-dark(var(--lightmode-color-drinputcombobox-itemhover-background, rgba(188, 188, 188, 0.38)), var(--darkmode-color-drinputcombobox-itemhover-background, rgba(117, 117, 117, 0.36)));
            }
            
              
            #itemlist [value].selected
            {
                color: light-dark(var(--lightmode-color-drinputcombobox-itemselected, rgb(255, 255, 255)), var(--darkmode-color-drinputcombobox-itemselected, rgb(250, 250, 250)));
                background-color: light-dark(var(--lightmode-color-drinputcombobox-itemselected-background, rgb(94, 165, 252)), var(--darkmode-color-drinputcombobox-itemselected-background, rgb(0, 128, 248)));
                border-radius: 5px;                
            }
            
            button svg
            {
                transition-duration: 0.1s;
                transition-property: transform;
            }

            button svg.open
            {
                transform: rotate(180deg);
            }


        </style>
        <label></label>
        <button class="btnPullDown"></button>
        <dr-popover showcloseicon="false" showtitle="false">
            <input type="text">
            <div id="itemlist">
                <!-- items are added here -->
            </div>
        </dr-popover>
    `;

    sSVGPulldown = '<svg viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M256,298.3L256,298.3L256,298.3l174.2-167.2c4.3-4.2,11.4-4.1,15.8,0.2l30.6,29.9c4.4,4.3,4.5,11.3,0.2,15.5L264.1,380.9  c-2.2,2.2-5.2,3.2-8.1,3c-3,0.1-5.9-0.9-8.1-3L35.2,176.7c-4.3-4.2-4.2-11.2,0.2-15.5L66,131.3c4.4-4.3,11.5-4.4,15.8-0.2L256,298.3  z"/></svg>';
    #objEdtSearch = null;
    #objLblText = null;
    #objBtnPulldown = null;
    #objSVGPulldown = null; //object of SVG
    #objBubblePulldown = null;//pull down menu
    #objCheckboxGroup = null; //<dr-input-checkbox-group>
    #objDivItemList = null; //<div> with all the items

    arrTypes = {selectone: "selectone", selectmultiple: "selectmultiple"} //select only one item or select multiple items
    #sType = this.arrTypes.selectone; //see arrTypes for values
    #sValue = ""; //selects automatically the item with this value
    #sValueInit = "";//initial value. This is used to know if a value is changed by the user so we determine the dirty-parameter which tells us if the user has changed anything so we can ask to save
    #sValuePrevious = "";// the previous value. This way we can dispatch an event with the old and new value (useful for language comboboxes to save text of last language to database before loading texts for the new language)
    #bAlwaysReturnValue = true; //Selects the first value in the list which is returned when the user doesnt select anything (WARNING: overwrites the placeholder). TRUE = default behavior of <select> also. 
    #sPlaceholder = ""; //initial text to show when no item is selected
    #sValueSeparator = ",";// separates values that are being set() and get() from this component. this only is used when selecting multiple items
    #sPlaceHolderSearch = "Search";

    #objFormInternals = null;
    #objAbortController = null;
    #objAbortControllerItems = null; //abort controller for all items in the combobox
    #bDisabled = false;
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

        this.attachShadow({mode: "open", delegatesFocus: false });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputCombobox.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

        this.#objFormInternals.setFormValue(""); //default is nothing        

        // console.log("constructorrrrtttt", this.isConnected);

    }    

    #readAttributes()
    {
        // console.log("readattributesssssss", this.isConnected);

        this.#sType = DRComponentsLib.attributeToString(this, "type", this.arrTypes.selectone);
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", false);
        this.#sValue = DRComponentsLib.attributeToString(this, "value", "");
        if (!this.#bConnectedCallbackHappened)//first time running
        {
            this.#sValueInit = this.#sValue; //so we are able to determine the dirty-flag
            this.#sValuePrevious = this.#sValue; //so we are able to dispatch event with old and new value
            console.log("asdfasdfasdfasdf poiepert", this.#sValue);
        }
        this.#sPlaceholder = DRComponentsLib.attributeToString(this, "placeholder", "");
        this.#sValueSeparator = DRComponentsLib.attributeToString(this, "valueseparator", ",");
        this.#sPlaceHolderSearch = DRComponentsLib.attributeToString(this, "placeholdersearch", this.#sPlaceHolderSearch);
        this.#bAlwaysReturnValue = DRComponentsLib.attributeToString(this, "alwaysreturnvalue", this.#bAlwaysReturnValue);

        // //add attributes when nessesary
        // if (!this.hasAttribute('tabindex')) 
        // {
        //     this.setAttribute('tabindex', 0);
        // }             
    }


 
    populate()
    {
        // console.log("populateeeeeeeeeeeeeeeee", this.isConnected);
        this.#objEdtSearch = this.shadowRoot.querySelector("input"); //create but not add to DOM yet        
        this.#objEdtSearch.placeholder = this.#sPlaceHolderSearch;
        this.#objLblText = this.shadowRoot.querySelector("label");        
        this.#objBtnPulldown = this.shadowRoot.querySelector("button");
        this.#objBtnPulldown.innerHTML = this.sSVGPulldown;
        this.#objBtnPulldown.setAttribute("tabindex", -1);
        this.#objSVGPulldown =  this.#objBtnPulldown.querySelector("svg");        
        this.#objBubblePulldown = this.shadowRoot.querySelector("dr-popover");
        this.#objBubblePulldown.anchorobject = this;
        this.#objBubblePulldown.anchorpos = this.#objBubblePulldown.iPosBottom; 
        this.#objDivItemList = this.#objBubblePulldown.querySelector("#itemlist");


        //copy items from DOM to ShadowDOM
        this.#populateListItems();   

        //make focussable
        this.setAttribute("tabindex", 0);

        //update UI
        this.updateUI();

        //update form value
        this.#objFormInternals.setFormValue(this.getValue());
    }

    /**
     * marks items as selected when 1 of the values from arrSelectedValues matches.
     * This function gives items the class "selected" when in arrSelectedValues, 
     * and removes class "selected" when not found in arrSelectedValues
     * 
     * note: recursive function
     * 
     * @param {HTMLElement} objPapa 
     * @param {Array} arrValuesToSelect array with selected values
     * @param {boolean} bFocus also set focus to item?
     */
    #selectItems(objPapa, arrValuesToSelect, bFocus = false)    
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {
                //check against array
                if (arrValuesToSelect.includes(objPapa.children[iIndex].getAttribute("value")))
                {
                    objPapa.children[iIndex].classList.add("selected");

                    if (bFocus)
                    {
                        // console.log("focusson:", objPapa.children[iIndex]);
                        objPapa.children[iIndex].focus();
                    }
                }
                else
                {
                    objPapa.children[iIndex].classList.remove("selected");
                }
            }

            //recursive call children
            this.#selectItems(objPapa.children[iIndex], arrValuesToSelect, bFocus);
        }
    }

    /**
     * bring items from DOM to ShadowDOM in the bubble
     * 
     * we let the items exist in the DOM, because when the user is searching the shadowdom items are destoyed
     */
    #cloneComboboxItemsFromDOMToShadow()
    {
        for (let iIndex = 0; iIndex < this.children.length; iIndex++)
        {
            //the first one is the default value for the combobox (for now, will be overwritten once we've found at a "selected"-attribute)
            if (iIndex == 0) 
            {
                this.#sValueInit = this.children[iIndex].getAttribute("value");
                this.#sValuePrevious = this.#sValueInit;
                this.#sValue = this.#sValueInit;
                console.log("init value", this.#sValue);
            }

            //we've found a new selected value
            if (DRComponentsLib.attributeToBoolean(this.children[iIndex], "selected", false))
            {
                this.#sValueInit = this.children[iIndex].getAttribute("value");
                this.#sValuePrevious = this.#sValueInit;
                this.#sValue = this.#sValueInit;
            }

            this.#objDivItemList.appendChild(this.children[iIndex].cloneNode(true));             
        }
    }

    /**
     * returns all items to see if there is at least 1 with the "value" attribute
     */
    #checkHasValueAttribute(objPapa)
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value")) 
            {
                return true;
            }        
            else //recursively call children
            {
                if (this.#checkHasValueAttribute(objPapa.children[iIndex]))
                    return true;
            }
        }
        return false;
    }

    /**
     * bring items from DOM to ShadowDOM in the bubble
     * 
     * we let the items exist in the DOM, because when the user is searching the shadowdom items are destoyed
     * 
     * @param {HTMLElement} objPapa
     * @param {string} sSearch
     * @return boolean found something? true = yes, false = no
     */
    #cloneComboboxItemsFromDOMToShadowSearch(objPapa, sSearch)
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value")) 
            {
                if (objPapa.children[iIndex].textContent.toUpperCase().indexOf(sSearch.toUpperCase()) >= 0)
                {
                    this.#objDivItemList.appendChild(objPapa.children[iIndex].cloneNode(true)); //note: the hierarchy is gone      
                }
            }
            else
            {
                //recursively search children
                if (this.#cloneComboboxItemsFromDOMToShadowSearch(objPapa.children[iIndex], sSearch))
                {
                    this.#objDivItemList.appendChild(objPapa.children[iIndex].cloneNode(true)); //note: the hierarchy is gone      
                }
            }
        }

        return false; //return nothing found
    }

    /**
     * make items focussable that have "value"-attribute
     * 
     * @param {HTMLElement} objPapa
     */
    #makeItemsFocussable(objPapa)
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {
                objPapa.children[iIndex].setAttribute("tabindex", "0");
            }

            //recursively call children
            this.#makeItemsFocussable(objPapa.children[iIndex]); 
        }
    }

    /**
     * recursively adds item checkboxes to each item that has a "value" attribute
     * 
     * @param {HTMLElement} objPapa 
     * @param {Array} arrSelectedValues array with selected values
     */
    #insertItemCheckboxes(objPapa, arrSelectedValues)
    {
        let objCheckbox = null;

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
                objPapa.children[iIndex].innerHTML = "<dr-input-checkbox>" + objPapa.children[iIndex].innerHTML + "</dr-input-checkbox>"; //document.createElement() gives error, that's why this convoluted way
            objCheckbox = objPapa.children[iIndex].querySelector("dr-input-checkbox"); 

            if (objCheckbox)
            {
                for (let iArrIndex = 0; iArrIndex < arrSelectedValues.length; iArrIndex++)
                {
                    if (arrSelectedValues[iArrIndex] == objPapa.children[iIndex].getAttribute("value"))
                        objCheckbox.checked = true;
                }                
            }

            this.#insertItemCheckboxes(objPapa.children[iIndex], arrSelectedValues);
        }        
    }

    /**
     * returns first combobox item that has the attribute "value" with contents sValue
     * 
     * this is a recursive function
     * 
     * @param {HTMLElement} objPapa 
     * @param {string} sValue 
     * @returns {HTMLElement} is null when not found
     */
    #getItemWithValue(objPapa, sValue)
    {
        let objReturn = null;

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {
                if (objPapa.children[iIndex].getAttribute("value") == sValue) 
                    return objPapa.children[iIndex];
            }

            //recursive call children
            objReturn = this.#getItemWithValue(objPapa.children[iIndex], sValue);
            if (objReturn !== null)
                return objReturn;
        }

        return null;
    }

    /**
     * returns all values in the item list in <div id="itemlist">
     * it goes over all items and adds the attribute value (like in <div value="1">) to array
     * 
     * NOTE: when searching: this list is smaller
     * NOTE: this is a recursive function
     * 
     * @param {HTMLElement} objPapa parent object
     * @param {Array} arrValues adds values to this array. must be null when calling outside this function (needed for recursiveness)
     * @returns {Array}
     */
    #getAllValues(objPapa)
    {
        let arrReturn = [];

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {
                arrReturn.push(objPapa.children[iIndex].getAttribute("value"));
            }

            arrReturn = arrReturn.concat(this.#getAllValues(objPapa.children[iIndex]));
        }

        return arrReturn;
    }

    /**
     * attach event listenres
     */
    addEventListeners()
    {
        
        //LABEL text
        this.#objLblText.addEventListener("click", (objEvent)=>
        {   
            this.#objBubblePulldown.toggle();
            if (this.#objBubblePulldown.isShowing())
                this.#objEdtSearch.focus();

            this.updateUI();

            //highlight the selected item
            if (this.#objBubblePulldown.isShowing())
                this.#selectItems(this.#objDivItemList, this.#sValue.split(this.#sValueSeparator), false);    
        }, { signal: this.#objAbortController.signal });


        //BUTTON pulldown
        this.#objBtnPulldown.addEventListener("click", (objEvent)=>
        {   
            this.#objBubblePulldown.toggle();
            if (this.#objBubblePulldown.isShowing())
                this.#objEdtSearch.focus();

            this.updateUI();

            //highlight the selected item
            if (this.#objBubblePulldown.isShowing())
                this.#selectItems(this.#objDivItemList, this.#sValue.split(this.#sValueSeparator), false);    
        }, { signal: this.#objAbortController.signal });
    

        //KEYBOARD events: respond to Space and Enter
        this.addEventListener("keyup", (objEvent)=>
        {
            if (this.shadowRoot.activeElement == null)//must be focussed on the combobox itself (not on the shadowDom because there is the search field where you can input a Space and Enter)
            {
                if ((objEvent.key == " ") || (objEvent.key == "Enter"))
                {
                    this.#objBubblePulldown.toggle();
                    if (this.#objBubblePulldown.isShowing())
                        this.#objEdtSearch.focus();                    

                    this.updateUI();         

                    //highlight the selected item
                    this.#selectItems(this.#objDivItemList, this.#sValue.split(this.#sValueSeparator), false);    
                }
            }
        }, { signal: this.#objAbortController.signal });
        

        //BUBBLE: update UI when bubble is exited (the arrow is turning the right way)
        this.#objBubblePulldown.addEventListener("dr-popover-hiding", (objEvent)=>
        {
            this.updateUI();         
        }, { signal: this.#objAbortController.signal });


        //KEYBOARD SCROLL DISABLE
        //using the up, down and space keys results in the page scrolling, which makes the bubble go away
        document.addEventListener("keydown", (objEvent)=> 
        {          
            if (this.#objBubblePulldown.isShowing())
            {
                switch(objEvent.key)
                {
                    case "ArrowUp":
                    case "ArrowDown":
                        objEvent.preventDefault();
                        break;
                    case " ": //spacebar also prevents spacebar in search
                        if (this.shadowRoot.activeElement !== this.#objEdtSearch) //avoid search bar
                            objEvent.preventDefault();

                }
            }
        }, { signal: this.#objAbortController.signal });           


        //KEYBOARD events: search
        this.#objEdtSearch.addEventListener("keyup", (objEvent)=>
        {
            if (objEvent.key == "ArrowUp")
                this.#focusNextItem(false, false);
            else if (objEvent.key == "ArrowDown")
                this.#focusNextItem(true, false);
            else
                this.#populateListItems(this.#objEdtSearch.value);

            // this.updateUI();         
        }, { signal: this.#objAbortController.signal });   
    

        //CHILDREN: add event listeners to all items in combobox ==> is done in this.populateListItems()
        // this.removeEventListenersItems();
        // this.#objAbortControllerItems = new AbortController;
        // if (this.#sType === this.arrTypes.selectone)
        //     this.addEventListenersChildrenSelectOne(this.#objBubblePulldown);                
        // if (this.#sType === this.arrTypes.selectmultiple)
        //     this.addEventListenersChildrenSelectMultiple(this.#objBubblePulldown);                
    }

    /**
     * focusses next or previous item in itemlist
     * 
     * @param {boolean} bNext true=next, false=previous
     * @param {boolean} bIncludeSearchBox include search box when focussing
     */
    #focusNextItem(bNext, bIncludeSearchBox = false)
    {
        //declarations
        let iCurrentIndex = -1;
        let iNewIndex = -1;

        //get all itemlist values
        const arrValues = this.#getAllValues(this.#objDivItemList);

        if (arrValues.length == 0) //if list is empty: exit
            return;

        //determine current value
        const sValueCurrentHighlight = this.#getValueSelectedItem(this.#objDivItemList);

        //where is current-item in list?
        iCurrentIndex = arrValues.indexOf(sValueCurrentHighlight);

        //add one or subtract (based in next or previous)
        if (bNext)
            iNewIndex = iCurrentIndex + 1;
        else
            iNewIndex = iCurrentIndex - 1;

        
        if (bIncludeSearchBox) //cycle including search box
        {
            if (iNewIndex >= arrValues.length) //cycle to search box
                this.#objEdtSearch.focus();
            else if (iNewIndex < 0) //cycle to search box
                this.#objEdtSearch.focus();
            else //highlight new item                        
                this.#selectItems(this.#objDivItemList, [arrValues[iNewIndex]], true);

        }
        else //cycle only items, don't include search box
        {
            //keep index within array bounds
            if (iNewIndex >= arrValues.length) //wrap around to beginning
                iNewIndex = 0;
            if (iNewIndex < 0) //wrap around to end
                iNewIndex = arrValues.length -1; 

            //highlight new item
            this.#selectItems(this.#objDivItemList, [arrValues[iNewIndex]], true);
        }


    }

    /**
     * look for the selected item in the list (has css class "selected")
     * and return its value (contents from attribute "value")
     */
    #getValueSelectedItem(objPapa)
    {
        let sValueNode = "";

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {
                if (objPapa.children[iIndex].classList.contains("selected"))
                    return objPapa.children[iIndex].getAttribute("value");
            }

            //recursive call children
            sValueNode = this.#getValueSelectedItem(objPapa.children[iIndex]);
            if (sValueNode !== "")
                return sValueNode;
        }

        return "";
    }


    /**
     * attaches eventListeners when selecting one item at a time
     * (only in selectone-mode)
     * 
     * @param {HTMLElement} objPapa 
     */
    addEventListenersChildrenSelectOne(objPapa)
    {
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value") == true) //only where there is an attribute value
            {
                //MOUSE CLICK
                objPapa.children[iIndex].addEventListener("click", (objEvent)=>
                {
                    //remove bubble
                    this.#objBubblePulldown.hide();

                    //update value
                    this.#sValuePrevious = this.#sValue; //store last value
                    this.#sValue = objPapa.children[iIndex].getAttribute("value");
                    if (this.getDirty())
                        this.#dispatchEventInputChanged(objPapa.children[iIndex], "clicked on value");

                    //update label text
                    // this.#objLblText.innerHTML = objPapa.children[iIndex].innerHTML;
                    this.updateUI();
                    
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
                            //update value
                            this.#sValuePrevious = this.#sValue; //store last value
                            this.#sValue = objPapa.children[iIndex].getAttribute("value");
                            if (this.getDirty())
                                this.#dispatchEventInputChanged(objPapa.children[iIndex], "clicked on value");

                            //update label text
                            //this.#objLblText.innerHTML = objPapa.children[iIndex].innerHTML;
                            this.updateUI();

                            //remove bubble
                            this.#objBubblePulldown.hide();

                            break
                    }
                }, { signal: this.#objAbortControllerItems.signal });                      
           }

           //recursively calling children
           this.addEventListenersChildrenSelectOne(objPapa.children[iIndex]);
        }
    }

    /**
     * attaches eventListeners
     * (only in selectmultiple-mode)
     * 
     * @param {HTMLElement} objPapa 
     */
    addEventListenersChildrenSelectMultiple(objPapa)
    {
    
        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            //CHECKBOX-LEVEL
            if (objPapa.children[iIndex].tagName.toUpperCase() == "DR-INPUT-CHECKBOX") //only where there is an attribute value
            {
                //CHECK changed
                objPapa.children[iIndex].addEventListener("change", (objEvent)=>
                {
                    let arrValues = [];
                    if (this.#sValue !== "")
                        arrValues = this.#sValue.split(this.#sValueSeparator);
                    let sCurrVal = objPapa.getAttribute("value"); //---- assuming that objPapa is the parent with attribute "value"

                    //update value
                    if (objPapa.children[iIndex].checked) //add to value when checked
                    {
                        arrValues.push(sCurrVal);

                        //filter array for duplicates double items (convert to a Set)
                        let objSet = new Set(arrValues);
                        arrValues = [...objSet];                              
                    }
                    else //remove from value when unchecked
                    {
                        let iDelIndex = arrValues.indexOf(sCurrVal);
                        arrValues.splice(iDelIndex, 1);
                    
                    }
                    this.#sValuePrevious = this.#sValue; //store last value
                    this.#sValue = arrValues.join(this.#sValueSeparator);                        
                    
                    if (this.getDirty())
                        this.#dispatchEventInputChanged(objPapa.children[iIndex], "selected new value");

                    this.updateUI();                    
                }, { signal: this.#objAbortControllerItems.signal });                            
            }

            //ITEM-LEVEL
            if (objPapa.children[iIndex].hasAttribute("value") == true)
            {
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
                        case " ":
                            let objCheckbox = objPapa.children[iIndex].querySelector("dr-input-checkbox");
                            if (objCheckbox !== null)
                                objCheckbox.checked = !objCheckbox.checked;
                            break;
                        case "Enter":
                            //remove bubble
                            this.#objBubblePulldown.hide();

                            break;
                    
                    }
                }, { signal: this.#objAbortControllerItems.signal });
            }              

           //recursively calling children
           this.addEventListenersChildrenSelectMultiple(objPapa.children[iIndex]);
        }
    }

    /**
     * returns text of all checked items
     * (only in selectmultiple-mode)
     * 
     * 
     * @param {HTMLElement} objPapa parent element
     * @param {Array} arrValues sValue.split()
     * @returns {Array}
     */
    #getAllSelectedText(objPapa, arrValues)
    {
        let arrReturn = [];

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].hasAttribute("value"))
            {                
                //check HTMLElement against arrValues
                for (let iArrIndex = 0; iArrIndex < arrValues.length; iArrIndex++)
                {
                    if (objPapa.children[iIndex].getAttribute("value") == arrValues[iArrIndex])
                    {
                        arrReturn.push(objPapa.children[iIndex].textContent); 
                    }
                }
            }

            arrReturn = arrReturn.concat(this.#getAllSelectedText(objPapa.children[iIndex], arrValues));
        }


        return arrReturn;
    }

    /**
     * returns values of all checked items
     * (only in selectmultiple-mode)
     * 
     * @returns {Array}
     */
    #getValuesSelectedAll(objPapa)
    {
        let arrReturn = [];

        for (let iIndex = 0; iIndex < objPapa.children.length; iIndex++)
        {
            if (objPapa.children[iIndex].tagName.toUpperCase() === "DR-INPUT-CHECKBOX")
            {
                if (objPapa.children[iIndex].checked)
                {
                    //the parent has the value (this is done to be compatible with the selectone-mode that stores the value in an item)
                    if (objPapa.hasAttribute("value"))
                    {    
                        arrReturn.push(objPapa.getAttribute("value"));
                    }
                }
            }

            arrReturn = arrReturn.concat(this.#getValuesSelectedAll(objPapa.children[iIndex]));
        }

        return arrReturn;
    }


    /**
     * dispatch event that value has changed
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventInputChanged(objSource, sDescription)
    {
        //probably something changed, thus update the form value

        this.#objFormInternals.setFormValue(this.getValue());
            
        this.dispatchEvent(new CustomEvent("change",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription,
                valueold: this.#sValuePrevious,
                valuenew: this.#sValue,
            }
        }));
    }    

    /**
     * copies list items from DOM to shadowDOM
     * 
     * @param {string} sSearch
     */
    #populateListItems(sSearch = "")
    {
        //clear items
        this.#objDivItemList.innerHTML = "";

        //copy items from DOM
        if (sSearch != "")
            this.#cloneComboboxItemsFromDOMToShadowSearch(this, sSearch)
        else
            this.#cloneComboboxItemsFromDOMToShadow();

        //able to focus
        this.#makeItemsFocussable(this.#objDivItemList);

        //insert checkboxes
        if (this.#sType == this.arrTypes.selectmultiple)
        {   
            let arrValues = this.#sValue.split(this.#sValueSeparator);

            //document.createElement() gives "operation not allowed" error, so we do it via innerHTML
            this.#objDivItemList.innerHTML = "<dr-input-checkbox-group>" + this.#objDivItemList.innerHTML + "</dr-input-checkbox-group>"
            this.#objCheckboxGroup = this.#objDivItemList.querySelector("dr-input-checkbox-group");
            this.#insertItemCheckboxes(this.#objCheckboxGroup, arrValues);
        }

        //CHILDREN: add event listeners to all items in combobox
        this.removeEventListenersItems();
        this.#objAbortControllerItems = new AbortController;
        if (this.#sType === this.arrTypes.selectone)
            this.addEventListenersChildrenSelectOne(this.#objBubblePulldown);                
        if (this.#sType === this.arrTypes.selectmultiple)
            this.addEventListenersChildrenSelectMultiple(this.#objBubblePulldown);  

        //check if items with value attribute exist
        if (!this.#checkHasValueAttribute(this))
            console.warn("DRInputCombobox: there are no items in combobox with a 'value'-attribute. The form value therefore will be empty.");
    }

    /**
     * update user interface
     */
    updateUI()
    {
        // console.log("updateuiiiiiiiiiiiiiiiiiiiiiii",this.#objEdtSearch, this.#objBtnPulldown, this.#objBubblePulldown, this.#objBubblePulldown.isConnected);

        //handle disabled
        if (this.#bDisabled)
        {
            this.#objEdtSearch.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
            this.#objBtnPulldown.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
        }
        else
        {
            this.#objEdtSearch.removeAttribute("disabled");
            this.#objBtnPulldown.removeAttribute("disabled");
        }

        //update 
        if (this.#objBubblePulldown.isShowing())
        {
            this.#objSVGPulldown.classList.add("open");
            this.#objSVGPulldown.classList.remove("close");
        }
        else
        {
            this.#objSVGPulldown.classList.add("close");
            this.#objSVGPulldown.classList.remove("open");
        }       
        
        //update label text: selectone
        if (this.#sType === this.arrTypes.selectone)
        {
            let objItem = null;
           
            if (this.#sValue !== "")
                objItem = this.#getItemWithValue(this.#objDivItemList, this.#sValue);

            if (objItem === null)
                objItem = this.querySelector("[selected]");

            if (objItem === null) //if no item, then use placeholder 
                if (this.#bAlwaysReturnValue) //show first item
                    objItem = this.querySelector("[value]");                    

            //display result on label
            if (objItem)
            {
                this.#objLblText.innerHTML = objItem.innerHTML;
            }
            else
            {
                if (this.#sPlaceholder !== "")
                    this.#objLblText.textContent = this.#sPlaceholder;
            }

        }

        //update label text: selectmultiple
        if (this.#sType === this.arrTypes.selectmultiple)
        {
            // let arrSelText = this.#getAllSelectedText(this.#objCheckboxGroup);
            let arrSelText = this.#getAllSelectedText(this, this.#sValue.split(this.#sValueSeparator));
            if (arrSelText.length === 0)
            {
                if (this.#sPlaceholder !== "")
                    this.#objLblText.innerHTML = this.#sPlaceholder;
                else
                    this.#objLblText.innerHTML = "";
            }
            else
            {
                this.#objLblText.innerHTML = arrSelText.join(", ");
            }        
        }
        

    
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
     * set internal value as string with decimal separator dot(.)
    */
    set value(sValue)
    {
        this.setValue(sValue);
    }

    /** 
     * get internal value as string with decimal separator dot(.)
    */
    get value()
    {
        // console.log("get value 111111111111111111111111111111111111122222222222222a", this.getValue());
        return this.getValue();
    }

    /** 
     * get last internal value 
    */
    get valueprevious()
    {
        return this.getValuePrevious();
    }


    /**
     */
    get type()
    {
        return this.#sType;
    }

    /**
     */
    set type(sStyle)
    {
        this.#sType = sStyle;
    }   

    /**
     */
    get valueseparator()
    {
        return this.#sValueSeparator;
    }

    /**
     */
    set valueseparator(sSeparator)
    {
        this.#sValueSeparator = sSeparator;
    }    
    
    /**
     */
    get dirty()
    {
        return this.getDirty();
    }    

    /**
     * set value (and selects therefore the item with that value)
     * 
     * @param {string} sValue 
     */
    setValue(sValue)
    {
        this.#sValuePrevious = this.#sValue; //store last value
        this.#sValue = sValue;
        if (!this.#bConnectedCallbackHappened)
            this.#sValueInit = sValue;
    }

    /** 
     * get value of selected item
    */
    getValue()
    {
        if (!this.getDirty() || this.#sValue === "") //hunt for value when it isn't changed and it's empty
        {
            let objItem = null;
            
            if (objItem === null)
                objItem = this.querySelector("[selected]");

            if (objItem === null) //if no item, then use placeholder 
                if (this.#bAlwaysReturnValue) //show first item
                    objItem = this.querySelector("[value]");                    

            //item found, then return value
            if (objItem)
            {
                return objItem.getAttribute("value");
            }
        }

        return this.#sValue;
    }

    /**
     * get previous value before the value was changed
     */
    getValuePrevious()
    {
        return this.#sValuePrevious;
    }

    /**
     * determines if the user has changed anything 
     * 
     * @returns boolean
     */
    getDirty()
    {
        return (this.#sValueInit !== this.sValue);
    }

    getValueSeparator()
    {
        return this.#sValueSeparator;
    }

    setValueSeparator(sSeparator = ",")
    {
        this.#sValueSeparator = sSeparator;
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
        this.removeEventListenersItems();
    }

    removeEventListenersItems()
    {
        this.#objAbortControllerItems.abort();
    }



    static get observedAttributes() 
    {
        return ["disabled", "value"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        console.log("attribute changed in combobox", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "disabled":
                this.disabled = sNewVal;
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

            //render
            this.populate();
        }

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();
        // if (this.#objAbortControllerItems.signal.aborted) => this.#populateListItems() takes care of this
        //     this.#objAbortControllerItems = new AbortController();

        //event
        this.addEventListeners();

        //create items and event listeners for those items
        this.#populateListItems();

        
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
customElements.define("dr-input-combobox", DRInputCombobox);