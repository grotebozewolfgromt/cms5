
<?php 
/**
 * dr-checkbox.js
 *
 * class to create a checkbox
 * 
 * the goal was to emulate as much behavior from <input type="checkbox"> as possible
 * 
 * EVENT:
 * this class dispatches an event: dr-input-checkbox-changed
 * 
 * EXAMPLE:
 * <dr-input-checkbox label="check mij" value="1a" valueunchecked="0a" name="customcheckbox"></dr-input-checkbox>
 * <dr-input-checkbox type="radio"></dr-input-checkbox>
 * <dr-input-checkbox type="checkbox"></dr-input-checkbox>
 * 
 * @author Dennis Renirie
 * 
 * 20 mrt 2025 dr-checkbox.js created
 * 16 mei 2025 dr-checkbox.js focussable en met space en enter de waarde te veranderen
 * 12 jun 2025 dr-checkbox.js BUGFIXES
 * 26 sept 2025 dr-checkbox.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de checkboxen niet gesaved
 */
?>


class DRInputCheckbox extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {     
                /* position: relative; */
                display: inline-block;
                user-select: none;
                width: max-content;
                cursor: pointer;                
            }      
  
            
            .drinputcheckbox-box
            {
                height: var(--value-drinputcheckbbox-box-height, 10px);
                width: var(--value-drinputcheckbbox-box-height, 10px);
                display: inline-block;

                border-width: 2px;                
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputcheckbox-box-border, rgb(98, 98, 98)), var(--darkmode-color-drinputcheckbox-box-border, rgb(151, 151, 151))); 

                border-radius: 3px;
                overflow: hidden;

            }            

            .drinputcheckbox-radio
            {
                height: var(--value-drinputcheckbbox-radio-height, 14px);
                width: var(--value-drinputcheckbbox-radio-height, 14px);
                display: inline-block;

            }

            .drinputcheckbox-label
            {
                line-height: 14px;
                margin: 0px;
                padding: 0px;
                padding-left: 5px;
                margin-top: -14px;
                cursor: pointer;
            }
                
           

        </style>
        <slot></slot>
    `;

    sSVGCheckboxChecked = '<svg viewBox="6 6 12 16" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"/></svg>';
    sSVGRadioChecked = '<svg viewBox="0 0 20 20"  xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="currentColor" id="Core" transform="translate(-338.000000, -338.000000)"><g id="radio-button-on" transform="translate(338.000000, 338.000000)"><path d="M10,5 C7.2,5 5,7.2 5,10 C5,12.8 7.2,15 10,15 C12.8,15 15,12.8 15,10 C15,7.2 12.8,5 10,5 L10,5 Z M10,0 C4.5,0 0,4.5 0,10 C0,15.5 4.5,20 10,20 C15.5,20 20,15.5 20,10 C20,4.5 15.5,0 10,0 L10,0 Z M10,18 C5.6,18 2,14.4 2,10 C2,5.6 5.6,2 10,2 C14.4,2 18,5.6 18,10 C18,14.4 14.4,18 10,18 L10,18 Z" id="Shape"/></g></g></g></svg>';
    sSVGRadioUnChecked = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="currentColor" id="Core" transform="translate(-296.000000, -338.000000)"><g id="radio-button-off" transform="translate(296.000000, 338.000000)"><path d="M10,0 C4.5,0 0,4.5 0,10 C0,15.5 4.5,20 10,20 C15.5,20 20,15.5 20,10 C20,4.5 15.5,0 10,0 L10,0 Z M10,18 C5.6,18 2,14.4 2,10 C2,5.6 5.6,2 10,2 C14.4,2 18,5.6 18,10 C18,14.4 14.4,18 10,18 L10,18 Z" id="Shape"/></g></g></g></svg>';
    #objCheckbox = null;
    #objLabel = null;
    #bCheckedValue = false;//internal value if checkbox is checked (or not)
    #sValueWhenChecked = "on"; //--> how <input type="checkbox"> behaves
    #sValueWhenUnChecked = "";
    #sLabelValue = "";//internal text value of label

    #sType = ""; //see arrTypes for values
    arrTypes = {checkbox: "checkbox", radio: "radio", switch: "switch"}

    #objFormInternals = null;
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
        this.objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: false });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputCheckbox.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

        //default form value
        this.#objFormInternals.setFormValue(""); //default is nothing        
    }    

    #readAttributes()
    {
        //read label
        this.#sLabelValue = this.innerHTML;
        this.innerHTML = "";

        //read attributes
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", false);
        this.#bCheckedValue = DRComponentsLib.attributeToBoolean(this, "checked", this.#bCheckedValue);
        // console.log("checkedvalue112312", this.#bCheckedValue);
        this.#sValueWhenChecked = DRComponentsLib.attributeToString(this, "value", this.#sValueWhenChecked);
        this.#sValueWhenUnChecked = DRComponentsLib.attributeToString(this, "valueunchecked", this.#sValueWhenUnChecked);
        this.#sType = DRComponentsLib.attributeToString(this, "type", this.arrTypes.checkbox);

        //add attributes when nessesary
        if (!this.hasAttribute('tabindex')) 
        {
            this.setAttribute('tabindex', 0);
        }             
    }


    populate()
    {
        if (this.#sType == this.arrTypes.checkbox)
        {
            this.#objCheckbox = document.createElement("span");
            this.#objCheckbox.className = "drinputcheckbox-box";
            this.shadowRoot.appendChild(this.#objCheckbox);
        }
        else if (this.#sType == this.arrTypes.radio)
        {
            this.#objCheckbox = document.createElement("span");
            this.#objCheckbox.className = "drinputcheckbox-radio";

            if (this.#bCheckedValue)
                this.#objCheckbox.innerHTML = this.sSVGRadioChecked;
            else
                this.#objCheckbox.innerHTML = this.sSVGRadioUnChecked;

            this.shadowRoot.appendChild(this.#objCheckbox);            
        }

        //add label
        this.#objLabel = document.createElement("label");
        this.#objLabel.className = "drinputcheckbox-label";
        this.#objLabel.innerHTML = this.#sLabelValue;
        this.shadowRoot.appendChild(this.#objLabel);

        //UI
        this.updateUI();

        //update form value
        if (this.#bCheckedValue)
            this.#objFormInternals.setFormValue(this.#sValueWhenChecked);
        else
            this.#objFormInternals.setFormValue(this.#sValueWhenUnChecked);
    }


    /**
     * attach event listenres
     */
    addEventListeners()
    {
        //KEYUP
        this.addEventListener("keyup", (objEvent)=>
        {
            console.log("KEY PRESS CHECKBOX");

            if (objEvent.key == " ") //spacebar
            {
                if (!this.#bDisabled)
                {
                    //invert value
                    this.#bCheckedValue = !this.#bCheckedValue;
                    objEvent.preventDefault(); //prevent scrolling of web page
                    this.updateUI();

                    if (this.#bCheckedValue)
                        this.#objFormInternals.setFormValue(this.#sValueWhenChecked);
                    else
                        this.#objFormInternals.setFormValue(this.#sValueWhenUnChecked);
                }  
            }            
        }, { signal: this.objAbortController.signal });        

        //CLICK (checkbox)
        this.addEventListener("mousedown", (objEvent)=>
        {
            if (!this.#bDisabled)
            {
                this.checked = !this.checked;
                this.updateUI();

                if (this.#bCheckedValue)
                    this.#objFormInternals.setFormValue(this.#sValueWhenChecked);
                else
                    this.#objFormInternals.setFormValue(this.#sValueWhenUnChecked);

            }

        }, { signal: this.objAbortController.signal });               

    }

    updateUI()
    {

        //CHECKBOX
        if (this.#sType == this.arrTypes.checkbox)
        {
            if (this.#bCheckedValue)
                this.#objCheckbox.innerHTML = this.sSVGCheckboxChecked;
            else
                this.#objCheckbox.innerHTML = "";
        } //RADIO BOX
        else if (this.#sType == this.arrTypes.radio)
        {
            // console.log("log value peoppppe", this, this.#bCheckedValue);
            if (this.#bCheckedValue)
                this.#objCheckbox.innerHTML = this.sSVGRadioChecked;
            else
                this.#objCheckbox.innerHTML = this.sSVGRadioUnChecked;
        }


        //label
        if ((this.innerHTML !== "") && (this.innerHTML !== this.#sLabelValue)) //if innertext changed, take that and wipe it
        {
            this.#sLabelValue = this.innerHTML;
            this.innerHTML = "";
        }

        this.#objLabel.innerHTML = this.#sLabelValue;
        

        // this.setAttribute("checked", this.#bCheckedValue);

        //handle disabled
        if (this.#bDisabled)
        {
        }
        else
        {
        }              
    }

    /** 
     * set internal value and dispatches an event
    */
    set checked(bValue)
    {
        this.setChecked(bValue, true);
    }

    /**
     * same as set checked(bValue), but allows you to purge the dispatch event
     * @param {*} bValue 
     * @param {*} bDispatchEvent 
     */
    setChecked(bValue, bDispatchEvent = true)
    {
        this.#bCheckedValue = bValue;

        //update form
        if (bValue)
        {
            this.#objFormInternals.setFormValue(this.#sValueWhenChecked);
        }
        else
        {
            this.#objFormInternals.setFormValue(this.#sValueWhenUnChecked);
        }

        // this.updateUI();
        // console.log("changed checked to:" + this.#bCheckedValue);

        if (bDispatchEvent)
        {
            this.dispatchEvent(new CustomEvent("change",
            {
                bubbles: true
            }));    
        }
    }

    /** 
     * get internal value 
    */
    get checked()
    {
        return this.#bCheckedValue;
    }

    /** 
     * set internal value 
    */
    set label(sValue)
    {
        this.#sLabelValue = sValue;
        this.updateUI();
    }

    /** 
     * get internal value 
    */
    get label()
    {
        return this.#sLabelValue;
    }

    /** 
     * set internal value 
    */
    set value(sValue)
    {
        // console.log("set value checked", sValue);
        this.#sValueWhenChecked = sValue;
    }

    /** 
     * get internal value 
    */
    get value()
    {
        return this.#sValueWhenChecked;
    }
    
    /** 
     * set internal value 
    */
    set valueunchecked(sValue)
    {
        // console.log("set value unchecked", sValue);
        this.#sValueWhenUnChecked = sValue;
    }

    /** 
     * get internal value 
    */
    get valueunchecked()
    {
        return this.#sValueWhenUnChecked;
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
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
    }


    static get observedAttributes() 
    {
        return ["disabled", "value", "checked"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number");
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
            case "checked":
                this.setChecked(DRComponentsLib.strToBool(sNewVal));
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
            this.#objFormInternals.setFormValue(this.#sValueWhenUnChecked); //default checkbox is unchecked, so we need to explicitly set formvalue to be unchecked value

            //render
            this.populate();
        }

        //reattach abortcontroller when disconnected
        if (this.objAbortController.signal.aborted)
            this.objAbortController = new AbortController();

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
customElements.define("dr-input-checkbox", DRInputCheckbox);