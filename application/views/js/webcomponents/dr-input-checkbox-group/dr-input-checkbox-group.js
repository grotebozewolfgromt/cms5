<?php 
/**
 * dr-input-checkbox-group.js
 *
 * class to to group multiple <dr-iput-checkbox>-es to consolidate them to 1 value in a form
 * This group can make <dr-iput-checkbox>-es behave as radio buttons, 
 * where only one of the radio buttons can be selected (selecting 1, unchecks the others)
 * 
 * 20 mrt 2025 dr-input-checkbox-group.js created
 * 
 * WARNING:
 * this class can auto correc 
 *  
 * EXAMPLE:
<dr-input-checkbox-group name="roepieroepie" id="groupsky" valueseparator="," minselected="1" maxselected="2" correctuserinput>
    <dr-input-checkbox label="check mij" value="1checked" valueunchecked="1" name="customcheckbox4"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="2checked" valueunchecked="2" name="customcheckbox5"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="3checked" valueunchecked="3" name="customcheckbox6"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="4checked" valueunchecked="4" name="customcheckbox7"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="54checked" valueunchecked="5" name="customcheckbox8"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="6checked" valueunchecked="6" name="customcheckbox9"></dr-input-checkbox>
    <dr-input-checkbox label="check mij" value="7checked" valueunchecked="7" name="customcheckbox10"></dr-input-checkbox>
 </dr-input-checkbox-group> 
 * 
 * CSS CLASSES:
 * .inputerror
 * .inputerrormin  ==> minimum not met
 * .inputerrormax  ==> max exceeded
 * 
 * 
 * @todo do correction based on the checkboxes that the user touched, not based on the order of which the elements are checked (=more intuitive)
 * @todo html form error gives error that cannot focus on element (when submitting)
 * @todo when changing attributes minselected/maxselected, de nieuwe waarden worden niet gebruikt voor validatie
 * @todo versie voor buttons ipv checkboxes
 */
?>


class DRInputCheckboxGroup extends HTMLElement
{

    static sTemplate = `
        <style>
        
            :host 
            {
                padding: 0px;
                margin: 0px;
            } 

            ::slotted(dr-input-checkbox.inputerrormin)
            {     
                outline-width: 1px;                
                outline-style: solid;
                outline-color: light-dark(var(--lightmode-color-drinputcheckboxgroup-inputerrormin-border, rgb(179, 0, 0)), var(--darkmode-color-drinputcheckboxgroup-inputerrormin-border, rgb(249, 93, 93))); 
                background-color: light-dark(var(--lightmode-color-drinputcheckboxgroup-inputerrormin-background, rgba(252, 7, 215, 0.15)), var(--darkmode-color-drinputcheckboxgroup-inputerrormin-background, rgba(249, 93, 93, 0.33))); 
            }                 

            ::slotted(dr-input-checkbox.inputerrormax)
            {     
                outline-width: 1px;                
                outline-style: solid;
                outline-color: light-dark(var(--lightmode-color-drinputcheckboxgroup-inputerrormax-border, rgb(179, 0, 0)), var(--darkmode-color-drinputcheckboxgroup-inputerrormax-border, rgb(249, 93, 93))); 
                background-color: light-dark(var(--lightmode-color-drinputcheckboxgroup-inputerrormax-background, rgba(179, 0, 0, 0.14)), var(--darkmode-color-drinputcheckboxgroup-inputerrormax-background, rgba(249, 93, 93, 0.33))); 
            }                   



        </style>
        <slot></slot>
        <div id="errorid"><div>
    `;

    #objFormInternals = null;
    #arrCheckBoxes = []; //array with checkbox objects in this group
    #arrSelectedCheckboxesStack = []; //LIFO stack with objects of selected checkboxes. This array registers the order in which checkboxes are checked by user. this helps us to correct user input in a natural way
    #sValueSeparator = ","; //when value is requested and multiple checkboxes are checked, how are the values separated?
    #iMinSelected = 0; //at a minimum 0 checkboxes have to be checked. 0 means: no minimum. -1,-2,-3 etc means: all but 1,2 or 3
    #iMaxSelected = 0; //at a maximum 0 checkboxes have to be checked. 0 means: no maximum. -1,-2,-3 etc means: all but 1,2 or 3
    #bCorrectUserInput = true; //allows this class to correct user input, otherwise it only detects errors with CSS classes (and console error)
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

        this.attachShadow({mode: "open", delegatesFocus: true});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputCheckboxGroup.sTemplate;

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);        
         
    }    

    /**
     * returns checkboxes inside this group
     */
    getCheckboxesFromDOM()
    {
        return [...this.querySelectorAll("dr-input-checkbox")];
    }

    #readAttributes()
    {
        this.valueseparator = DRComponentsLib.attributeToString(this, "valueseparator", this.#sValueSeparator);
        this.minselected = DRComponentsLib.attributeToInt(this, "minselected", this.#iMinSelected);
        this.maxselected = DRComponentsLib.attributeToInt(this, "maxselected", this.#iMaxSelected);
        this.correctuserinput = DRComponentsLib.attributeToBoolean(this, "correctuserinput", this.#bCorrectUserInput);

        if (!this.hasAttribute('tabindex')) {
            this.setAttribute('tabindex', 0);
        }             
    }


    /**
     * populate the group with stuff
     */
    #populate()
    {
        
    }

    /**
     * get value(s) of checked checkboxes in group
     * 
     * @return {string} values are separated by sValueSeparator
     */
    get value()
    {
        const arrValues = this.getValueAsArray();
        return arrValues.join(this.#sValueSeparator);
    }


    /**
     * get value(s) of checked checkboxes in group
     * 
     * @return {array} values are separated by sValueSeparator
     */
    getValueAsArray()
    {
        let arrReturn = [];


        //retrieve values of all checked checkboxes
        for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
        {
            if (this.#arrCheckBoxes[iIndex].checked)
                arrReturn.push(this.#arrCheckBoxes[iIndex].value);
        }

        return arrReturn;
    }

    /**
     * return how checkbox values are separated
     * for example with a comma: ,
     */
    get valueseparator()
    {
        return this.#sValueSeparator;
    }

    /**
     * set how checkbox values are separated
     * for example with a comma: ,
     */
    set valueseparator(sSeparator)
    {
        this.#sValueSeparator = sSeparator;
    }

    /**
     * is this class allowed to correct user input?
     * it can be quite annoying, hence you can switch it off
     */
    get correctuserinput()
    {
        return this.#bCorrectUserInput;
    }

    /**
     * set how checkbox values are separated
     * for example with a comma: ,
     */
    set correctuserinput(bValue)
    {
        this.#bCorrectUserInput = bValue;
    }    


    /**
     * return many checkboxes need to be selected at a minimum
     */
    get minselected()
    {
        return this.#iMinSelected;
    }

    /**
     * set many checkboxes need to be selected at a minimum
     */
    set minselected(iMin)
    {
        this.#iMinSelected = iMin;
        if (this.#iMinSelected > this.#arrCheckBoxes.length)
            this.#iMinSelected = this.#arrCheckBoxes.length;
    }

    /**
     * return many checkboxes can to be selected at a maximum
     */
    get maxselected()
    {
        return this.#iMaxSelected;
    }

    /**
     * set many checkboxes can to be selected at a maximum
     */
    set maxselected(iMax)
    {
        this.#iMaxSelected = iMax;
        if (this.#iMaxSelected > this.#arrCheckBoxes.length)
            this.#iMaxSelected = this.#arrCheckBoxes.length;        
    }

    /**
     * help the user by correcting their input
     * 
     * @param {Event} objEvent
     */
    correctUserInput(objEvent = null)
    {
        let iCheckedCount = this.#arrSelectedCheckboxesStack.length;

        //==== return on error
        if (this.#iMinSelected > this.#iMaxSelected)
        {
            console.error("<dr-input-checkbox-group>: minimum is bigger than maximum amount of selected checkboxes");
            return;
        }
        if ((this.#iMinSelected == 0) && (this.#iMaxSelected == 0))
        {
            return; //nothing to do
        }

        //==== correct the selected-stack, based on min and max selected
        //minimum not met: add checkboxes
        // let iToAddChecks = this.#iMinSelected - iCheckedCount;
        if ((iCheckedCount < this.#iMinSelected) && (this.#iMinSelected != 0))
        {
            //take the extra checkboxes from the checkbox array
            for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
            {
                if (!this.#arrCheckBoxes[iIndex].checked)
                {
                    this.#arrCheckBoxes[iIndex].setChecked(true, false);
                    this.#arrSelectedCheckboxesStack.push(this.#arrCheckBoxes[iIndex]);
                    iCheckedCount++;
                }

                //quit loop when we reached minimum
                if (iCheckedCount >= this.#iMinSelected)
                    iIndex = this.#arrCheckBoxes.length;
            }
        }

        //maximum exceeded: remove checkboxes
        let iToRemoveChecks = iCheckedCount - this.#iMaxSelected;
        if ((iCheckedCount > this.#iMaxSelected) && (this.#iMaxSelected != 0))
        {
            for (let iRemove = 0; iRemove < iToRemoveChecks; iRemove++)
            {
                this.#arrSelectedCheckboxesStack.shift(); //remove
            }
        }

        //==== match the selected checkboxes with the selected-stack
        //check or uncheck each checkbox
        let bFound = false;
        for (let iIndexAll = 0; iIndexAll < this.#arrCheckBoxes.length; iIndexAll++)
        {
            bFound = false;
            for (let iIndexChecked = 0; iIndexChecked < this.#arrSelectedCheckboxesStack.length; iIndexChecked++)
            {
                if (this.#arrCheckBoxes[iIndexAll] == this.#arrSelectedCheckboxesStack[iIndexChecked])
                {
                    bFound = true;
                }
            }

            this.#arrCheckBoxes[iIndexAll].setChecked(bFound, false);
        }
    }

    /**
     * detects (min/max selected) input error and adds class when error
     */
    detectInputError()
    {
        let bError = false;
        let iCheckedCount = 0;

        //==== INPUT ERROR
        if (this.#iMinSelected > this.#iMaxSelected)
        {
            console.error("<dr-input-checkbox-group>: minimum is bigger than maximum amount of selected checkboxes");
            return;
        }



        //==== RESET form validity
        this.#objFormInternals.setValidity({ valueMissing: false });


        //==== HOW MANY CHECKED? determine how many checkboxes are selected
        for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
        {
            if (this.#arrCheckBoxes[iIndex].checked)
                iCheckedCount++;
        }


        //==== MINIMUM
        let iToCheck = this.#iMinSelected - iCheckedCount;

        //remove all min-error classes
        for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
            this.#arrCheckBoxes[iIndex].classList.remove("inputerrormin");
    
        //detect min-errors and add min-error classes
        if ((this.minselected != 0) && (iToCheck > 0))
        {
            bError = true;
        
            //log error
            console.log("DRInputCheckbox: minimum amount of checked checkboxes", this.#iMinSelected , "not met. User needs to check", iToCheck, "more");

            //add error to checkboxes that are not selected
            for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
            {
                if (!this.#arrCheckBoxes[iIndex].checked)
                    this.#arrCheckBoxes[iIndex].classList.add("inputerrormin");
            }            

            // set form validity
            this.#objFormInternals.setValidity({ valueMissing: true }, "Minimum amount of checked checkboxes not met. Min " + this.#iMaxSelected + " needed");
        }
 

        //==== MAXIMUM
        let iToRemove = iCheckedCount - this.#iMaxSelected;

        //remove all max-error classes
        for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
            this.#arrCheckBoxes[iIndex].classList.remove("inputerrormax");
               
        //detect max-errors and add max-error classes
        if ((this.maxselected != 0) && (iToRemove > 0))
        {        
            bError = true;

            //log error
            console.log("DRInputCheckbox: maximum amount of checked checkboxes", this.#iMaxSelected, "exceeded. User needs to select", iToRemove, "less");

            //add error to checkboxes that are not selected
            for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
            {
                if (this.#arrCheckBoxes[iIndex].checked)
                    this.#arrCheckBoxes[iIndex].classList.add("inputerrormax");
            }    

            // set form validity
            this.#objFormInternals.setValidity({ valueMissing: true }, "Maximum amount of checked checkboxes exceeded. Max " + this.#iMaxSelected + " allowed");
        }


        //cast general error
        this.classList.toggle("inputerror", bError);


        return bError;
    }
    
    addEventListeners()
    {

        //attach listeners to all checkboxes
        for (let iIndex = 0; iIndex < this.#arrCheckBoxes.length; iIndex++)
        {
            this.#arrCheckBoxes[iIndex].addEventListener("change", (objEvent)=>
            {
                // console.log("input-checkbox-group: BEFORE shift and push", objEvent.target, this.#arrSelectedCheckboxesStack, this.#iMaxSelected);

                if (this.#arrCheckBoxes[iIndex].checked)
                    this.#arrSelectedCheckboxesStack.push(objEvent.target);

                // console.log("input-checkbox-group: after shift and push", objEvent.target, this.#arrSelectedCheckboxesStack, this.#iMaxSelected);

                if (this.#bCorrectUserInput)
                    this.correctUserInput(objEvent);

                this.detectInputError();

                this.#objFormInternals.setFormValue(this.value);    
            }, { signal: this.objAbortController.signal });
        }

        //do a check on page load 
        window.addEventListener("load", (objEvent)=> 
        {
            //we can't do this check earlier, because the checkboxes probably haven't been created yet
            if (this.#bCorrectUserInput)
                this.correctUserInput();  
        }, { once:true }); //==> event listener is auto removed
    }

    /**
     * checks checkbox and updates arrSelectedCheckboxesStack
     * @param {DRInputCheckbox} objCheckbox
     */
    #checkCheckbox(objCheckbox)
    {

    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
    }


    checkValidity() 
    {
        return this.#objFormInternals.checkValidity();
    }
      
    reportValidity()
    {
        return this.#objFormInternals.reportValidity();
    }

    get validity()
    {
        return this.#objFormInternals.validity;
    }

    get validationMessage()
    {
        return this.#objFormInternals.validationMessage;
    }

    /** 
     * When added to DOM
     */
    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //register checkboxes
            this.#arrCheckBoxes = this.getCheckboxesFromDOM();

            //first read
            this.#readAttributes();

            //populate
            this.#populate();
        }

        //reattach abortcontroller when disconnected
        if (this.objAbortController.signal.aborted)
            this.objAbortController = new AbortController();


        //at last: events    
        this.addEventListeners();    

        //after eventlisteners added,        
        // if (this.#bCorrectUserInput)
        //     this.correctUserInput();  
        // this.detectInputError();      

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
customElements.define("dr-input-checkbox-group", DRInputCheckboxGroup);