
<?php 
/**
 * dr-input-number.js
 *
 * class to display numbers and decimals to users.
 * the goal was to emulate as much behavior from <input type="number"> as possible (hence the name)
 * but extend it with a few essentials like handling decimals for different notations and get rid of the minuscule plus and minus buttons
 * 
 * FEATURES
 * - this class supports decimal separators like comma (,) instead of dot (.) for user input
 * - this class also supports different precisions for decimals. i.e. 2 digits after decimal separator for money
 * - this class support padding out zeros when user omitted them. Useful for handling money
 * - keeps track if the user changed the contents (use function .getDirty())
 * 
 * WARNING:
 * -the decimal separator for setting and getting value from this class is always a dot (.)!!! This way input and output is always consistent. Decimal separator ONLY used for visual representation in UI
 * -the thousand separator isn't used for setting and getting values, only visual representation in UI
 * 
 * FIRES EVENT: 
 * - "update" when anything changes in editbox (on each keypress for example)
 * - "change" when user leaves editbox and is changed
 * - "dr-input-number-zero" when has become zero (useful for shopping carts in webshop)
 * 
 * EXAMPLE:
 * <dr-input-number value="2.6" precision="2" padzero="4" decimalseparator="," min="0" max="5"></dr-input-number>
 * <dr-input-number zerotrashcan></dr-input-number> minus-icon becomes trashcan
 * 
   @todo align value left right center
   @todo thousand separator moet gefilterd worden
 * 
 * @author Dennis Renirie
 * 
 * 30 mrt 2025 dr-input-number.js created
 * 3 april 2025 dr-input-number.js when min and max == 0 dan was de plus en min icon disabled op ongewenste momenten
 * 3 april 2025 dr-input-number.js filters thousand separator
 * 7 may 2025 dr-input-number.js complete rewrite of class because of huge bug. voor en na decimaal karakter werd opgeslagen als int, wat problemen gaf met waardes 0,005 hetgeen 0,5 werd
 * 8 may 2025 dr-input-number.js complete rewrite of class because of huge bug. voor en na decimaal karakter werd opgeslagen als int, wat problemen gaf met waardes 0,005 hetgeen 0,5 werd
 * 30 may 2025 dr-input-number.js scroll-to-add or subtract only works when element in focussed
 * 30 may 2025 dr-input-number.js home and end key work
 * 4 jun 2025 dr-input-number.js defaults changed in readAttributes
 * 4 jun 2025 dr-input-number.js can now return dirty
 * 26 sept 2025 dr-input-number.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 */
?>


class DRInputNumber extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
                box-sizing: border-box;
                border-width: 1px;
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputnumber-border, rgb(42, 42, 42)), var(--darkmode-color-drinputnumber-border, rgb(232, 232, 232)));
                background-color: light-dark(var(--lightmode-color-drinputnumber-background, rgb(255, 255, 255)), var(--darkmode-color-drinputnumber-background, rgb(71, 71, 71)));
                height: 24px;
                display: flex;
                width: 100%;
                border-radius: 5px;
            }             

            button
            {
                padding: 5px;

                border-width: 0px;
                background-color: light-dark(var(--lightmode-color-drinputnumber-background, rgb(255, 255, 255)), var(--darkmode-color-drinputnumber-background, rgb(71, 71, 71)));
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

            input
            {
                border-width: 0px;
                width: 100%;
                flex-grow: 1;
                border-radius: 5px; /* needs to have border radius, otherwise cuts off parent in corner */
            }
                
        </style>
        <button class="min"></button>
        <input type="text">
        <button class="plus"></button>
    `;

    sSVGPlus = '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M417.4,224H288V94.6c0-16.9-14.3-30.6-32-30.6c-17.7,0-32,13.7-32,30.6V224H94.6C77.7,224,64,238.3,64,256  c0,17.7,13.7,32,30.6,32H224v129.4c0,16.9,14.3,30.6,32,30.6c17.7,0,32-13.7,32-30.6V288h129.4c16.9,0,30.6-14.3,30.6-32  C448,238.3,434.3,224,417.4,224z"/></svg>';
    sSVGMinus = '<svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M417.4,224H94.6C77.7,224,64,238.3,64,256c0,17.7,13.7,32,30.6,32h322.8c16.9,0,30.6-14.3,30.6-32  C448,238.3,434.3,224,417.4,224z"/></svg>';
    // sSVGTrashcan = '<svg viewBox="0 0 1024 1024" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M704 128H448c0 0 0-24.057 0-32 0-17.673-14.327-32-32-32s-32 14.327-32 32c0 17.673 0 32 0 32H128c-35.346 0-64 28.654-64 64v64c0 35.346 28.654 64 64 64v576c0 35.346 28.654 64 64 64h448c35.346 0 64-28.654 64-64V320c35.346 0 64-28.654 64-64v-64C768 156.654 739.346 128 704 128zM640 864c0 17.673-14.327 32-32 32H224c-17.673 0-32-14.327-32-32V320h64v480c0 17.673 14.327 32 32 32s32-14.327 32-32l0.387-480H384v480c0 17.673 14.327 32 32 32s32-14.327 32-32l0.387-480h64L512 800c0 17.673 14.327 32 32 32s32-14.327 32-32V320h64V864zM704 240c0 8.837-7.163 16-16 16H144c-8.837 0-16-7.163-16-16v-32c0-8.837 7.163-16 16-16h544c8.837 0 16 7.163 16 16V240z"/></svg>';
    sSVGTrashcan = '<svg viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g><path d="M-214.4,400h-9.8l-0.4-1.1c-0.2-0.6-0.8-1-1.5-1h-13.8c-0.7,0-1.3,0.4-1.5,1.1l-0.4,1.1h-9.8c0,0-3.6,0-3.7,7   c0,0.2,0.2,0.5,0.4,0.5h43.6c0.2,0,0.4-0.2,0.4-0.5C-210.9,400-214.4,400-214.4,400z"/><path d="M-214.2,410.6h-37.5c-0.2,0-0.3,0.2-0.3,0.3l3,31.5c0.1,0.8,0.8,1.4,1.6,1.4h28.9c0.8,0,1.5-0.6,1.6-1.4l3-31.5   C-213.9,410.8-214.1,410.6-214.2,410.6z M-226.6,420.2h-12.7V417h12.7V420.2z"/></g></svg>';
    #objEditBox = null;
    #objBtnPlus = null;
    #objBtnMin = null;
    #iValue = 0;//internal number value. 64 bit int (BigInt). What is represents depends on the decimal precision. if decimal precision is 4, then value 10000 represents 1
    #iValueInit = 0;
    #iDecimalPrecision = 0; //the amount of digits AFTER the decimal separator. This sets the precision of the value in this box. Otherwise known as fractals: https://en.wikipedia.org/wiki/Decimal
    #sDecimalSeparator = ",";
    #sThousandSeparator = ".";
    #iPadEndDecimalZeros = 0; //how many zeros should be padded out after the decimal separator? with 2 then 24 becomes 24,00. When 0 nothing happens
    #bZeroMinusTrashcan = false; //when value == 1 then the minus icon becomes trashcan
    #sType = ""; //see arrTypes for values
    arrTypes = {plain: "plain", buttons: "buttons"} //plain is a regular textbox without buttons
    arrTextAlign = {left: "left", right: "right", center: "center"} //the text in the input
    #iMinValue = 0; //the precision is stored in #iDecimalPrecision
    #iMaxValue = 0; //the precision is stored in #iDecimalPrecision

    #objFormInternals = null;
    #objAbortController = null;
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

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputNumber.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

    }    

    #readAttributes()
    {
        this.#iDecimalPrecision = DRComponentsLib.attributeToInt(this, "precision", this.#iDecimalPrecision);//FIRST READ PRECISION!!!! then value (otherwise we don't know how to set the values)
        this.#sDecimalSeparator = DRComponentsLib.attributeToString(this, "decimalseparator", this.#sDecimalSeparator);
        this.#sThousandSeparator = DRComponentsLib.attributeToString(this, "thousandseparator", this.#sThousandSeparator);
        this.setMinValueAsString(DRComponentsLib.attributeToString(this, "min", "0.0"), this.#sDecimalSeparator, this.#sThousandSeparator); //first min, so we can check setting internal value against it
        this.setMaxValueAsString(DRComponentsLib.attributeToString(this, "max", "0.0"), this.#sDecimalSeparator, this.#sThousandSeparator); //first max, so we can check setting internal value against it
        this.setValueAsString(DRComponentsLib.attributeToString(this, "value", "0.0"), this.#sDecimalSeparator, this.#sThousandSeparator); //first read precision, then value. (comma (,) strips thousand separator)
        if (!this.#bConnectedCallbackHappened) //update init value
            this.#iValueInit = this.iValue;
        this.#iPadEndDecimalZeros = DRComponentsLib.attributeToInt(this, "padzero", this.#iPadEndDecimalZeros);
        this.#sType = DRComponentsLib.attributeToString(this, "type", this.arrTypes.buttons);
        this.#bZeroMinusTrashcan = DRComponentsLib.attributeToBoolean(this, "zerotrashcan", this.#bZeroMinusTrashcan);
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", this.#bDisabled);

        // //add attributes when nessesary
        // if (!this.hasAttribute('tabindex')) 
        // {
        //     this.setAttribute('tabindex', 0);
        // }             
    }

    /**
     * sets the internal value
     * 
     * @param {int} iValue the value
     * @param {int} iDecimalPrecision what is the precision of the value you are trying to set? when precision is 4 then value 10000 means a value of 1
     */
    setInternalValue(iValue, iDecimalPrecision)
    {
        this.#iValue = this.#convertToInternalPrecision(iValue, iDecimalPrecision);

        //setter can not exceed max or min
        if (this.isMinMaxCheckEnabled())
        {
            if (this.#iValue < this.#iMinValue)
                this.#iValue = this.#iMinValue;
            if (this.#iValue > this.#iMaxValue)
                this.#iValue = this.#iMaxValue;             
        }

        //update init value
        if (!this.#bConnectedCallbackHappened)
            this.#iValueInit = this.iValue;        
    }

    /**
     * sets the internal value by supplying a string
     * 
     * @param {string} sValue part before decimal separator
     * @param {string} sDecimalSeparator dot (.) or comma (,)
     */
    setValueAsString(sValue, sDecimalSeparator = ".", sThousandSeparator = ",")
    {
        this.#iValue = this.#convertStringToValue(sValue, sDecimalSeparator, sThousandSeparator);

        //setter can not exceed max or min
        if (this.isMinMaxCheckEnabled())
        {        
            if (this.#iValue < this.#iMinValue)
                this.#iValue = this.#iMinValue;
            if (this.#iValue > this.#iMaxValue)
                this.#iValue = this.#iMaxValue;
        }        

        //update init value
        if (!this.#bConnectedCallbackHappened)
            this.#iValueInit = this.iValue;
    }    


    /**
     * gets string of internal value where decimals are separated by decimal-separator
     * 
     * @param {string} sDecimalSeparator 
     */
    getValueAsString(sDecimalSeparator = ".", sThousandSeparator = ",")
    {      
        return this.#convertValueToString(this.#iValue, sDecimalSeparator, sThousandSeparator);
    }

    /**
     * has the user changed the value of this box?
     * 
     * @returns boolean
     */
    getDirty()
    {
        return (this.#iValue !== this.#iValueInit);
    }

    /**
     * sets the minimum value
     * 
     * @param {int} iValueInt part before decimal separator
     * @param {int} iDecimalPrecision with precision 4 a value of 1 is stored as 10000
     */
    setMinValue(iValueInt, iDecimalPrecision)
    {        
        this.#iMinValue = this.#convertToInternalPrecision(iValueInt, iDecimalPrecision);

        if (this.#iValue < this.#iMinValue)
            this.#iValue = this.#iMinValue;
    }    

    /**
     * sets the internal min value by supplying a string
     * 
     * @param {int} sValue part before decimal separator
     * @param {int} sDecimalSeparator dot (.) or comma (,)
     * @param {int} sThousandSeparator dot (.) or comma (,)
     */
    setMinValueAsString(sValue, sDecimalSeparator = ".", sThousandSeparator = ",")
    {
        // if (sValue == "")
        // {
        //     this.setMinValue(0,0);
        //     return;
        // }

        // const arrValue = sValue.split(sDecimalSeparator);
        // if (arrValue.length == 1) //no fractionals
        //     this.setMinValue(this.parseIntBetter(sValue), 0);
        // else //detected fractional
        //     this.setMinValue(this.parseIntBetter(arrValue[0]), this.parseIntBetter(arrValue[1]));
        this.#iMinValue = this.#convertStringToValue(sValue, sDecimalSeparator, sThousandSeparator);

        if (this.#iValue < this.#iMinValue)
            this.#iValue = this.#iMinValue;
    }    

    /**
     * gets string of internal min value where decimals are separated by decimal-separator
     * 
     * @param {string} sDecimalSeparator 
     * @param {string} sThousandSeparator 
     */
    getMinValueAsString(sDecimalSeparator = ".", sThousandSeparator = ",")
    {
        return this.#convertValueToString(this.#iMinValue, sDecimalSeparator, sThousandSeparator);
    }    

    /**
     * sets the max value
     * 
     * @param {int} iValueInt part before decimal separator
     * @param {int} iDecimalPrecision what decimal precision is this value?
     */
    setMaxValue(iValueInt, iDecimalPrecision)
    {
        this.#iMaxValue = this.#convertToInternalPrecision(iValueInt, iDecimalPrecision);

        if (this.#iValue > this.#iMaxValue)
            this.#iValue = this.#iMaxValue;        
    }    

    /**
     * sets the internal max value by supplying a string
     * 
     * @param {int} sValue part before decimal separator
     * @param {int} sDecimalSeparator dot (.) or comma (,)
     * @param {int} sThousandSeparator dot (.) or comma (,)
     */
    setMaxValueAsString(sValue, sDecimalSeparator = ".", sThousandSeparator = ",")
    {
        // if (sValue == "")
        // {
        //     this.setMaxValue(0,0);
        //     return;
        // }

        // const arrValue = sValue.split(sDecimalSeparator);
        // if (arrValue.length == 1) //no fractionals
        //     this.setMaxValue(this.parseIntBetter(sValue), 0);
        // else //detected fractional
        //     this.setMaxValue(this.parseIntBetter(arrValue[0]), this.parseIntBetter(arrValue[1]));
        this.#iMaxValue = this.#convertStringToValue(sValue, sDecimalSeparator, sThousandSeparator);

        if (this.#iValue > this.#iMaxValue)
            this.#iValue = this.#iMaxValue;       
    }    

    /**
     * gets string of internal max value where decimals are separated by decimal-separator
     * 
     * @param {string} sDecimalSeparator 
     */
    getMaxValueAsString(sDecimalSeparator = ".", sThousandSeparator = ",")
    {
        return this.#convertValueToString(this.#iMaxValue, sDecimalSeparator, sThousandSeparator);
    }        

    /**
     * allows you to set the internal value from user-friendly user input
     * This takes things into account like decimal separator
     * 
     * @param {string} sUserInput
     */
    setValueAsUserInput(sUserInput)
    {
        this.setValueAsString(sUserInput, this.#sDecimalSeparator, this.#sThousandSeparator)
    }

    /**
     * returns a user friendly representation of the internal value
     * This takes things into account like decimal separator
     * 
     * @return {string}
     */
    getValueAsUserOutput()
    {
        //pad zeros
        if (this.#iPadEndDecimalZeros > 0)
        {
            const sValue = this.getValueAsString(this.#sDecimalSeparator, this.#sThousandSeparator);
            const iPosDecSep = sValue.indexOf(this.#sDecimalSeparator);
            const iDigitsUsed = sValue.length - iPosDecSep -1; //-1 because of the decimal separator
            const iZerosToPad = this.#iPadEndDecimalZeros - iDigitsUsed;
            return sValue.padEnd(sValue.length + iZerosToPad, 0);
        }
        else //no padding allowed
        {
            if (this.#iDecimalPrecision == 0)
                return this.#iValue;
            else
                return this.getValueAsString(this.#sDecimalSeparator, this.#sThousandSeparator);
        }
    }


    /**
     * logs internal value and precision to console
     */
    logInternalValue()
    {
        console.log("internal value:", this.#iValue, this.#iDecimalPrecision);
    }

    /**
     * a better integer parser than parseInt():
     * -converts NaN into 0
     * -converts "" into 0
     * -converts max exceeded values into max values
     * -converts min exceeded values into min values
     * 
     * @param {string} sShouldBeInt 
     * @param {int} iMinValue 
     * @param {int} iMaxValue 
     * @returns {int} 
     */
    parseIntBetter(sShouldBeInt, iMinValue = 0, iMaxValue = 0)
    {
        if (sShouldBeInt == "")
            return 0;
        
        let mResult = parseInt(sShouldBeInt);

        if (isNaN(mResult))
            return 0;
        
        if (iMaxValue > 0)
            if (mResult > iMaxValue)
                return iMaxValue;

        if (iMinValue < 0)
            if (mResult < iMinValue)
                return iMinValue;

        return mResult;
    }       


    /**
     * get value in same precision as internal value
     */
    #convertToInternalPrecision(iValue, iDecimalPrecision)
    {
        let iReturn = iValue;

        if ((iValue == Number.MAX_SAFE_INTEGER) || (iValue == Number.MIN_SAFE_INTEGER))
            console.error("DRInputNumber: convertToInternalPrecision(): Maximum or Minimum value of integer (" + iValue + ") reached");


        //convert to same decimal precision as internal precision
        const iDecPrecDiff = this.#iDecimalPrecision - iDecimalPrecision; //calculate difference in decimal precision --> this helps to set the right value

        if (iDecPrecDiff != 0)
        {
            let iMultFact = 10 ** iDecPrecDiff; //multiply factor
            iReturn = Math.round(iValue * iMultFact);

            if ((iReturn == Number.MAX_SAFE_INTEGER) || (iReturn == Number.MIN_SAFE_INTEGER))
                console.error("DRInputNumber: convertToInternalPrecision(): Maximum or Minimum value of integer (" + iValue + ") reached");
        }

        return iReturn;
    }

    /**
     * converts a string to an internal value (can be the internal value, min or max) and converts it into a value with the right precision
     * 
     * @param {string} sValue 
     * @param {string} sDecimalSeparator 
     * @param {string} sThousandSeparator 
     * @returns 
     */
    #convertStringToValue(sValue, sDecimalSeparator = this.#sDecimalSeparator, sThousandSeparator = this.#sThousandSeparator)
    {
        let sNakedValue = ""; //no secimal separator, no thousand separator
        if (sValue == "")
        {
            return 0;
        }

        //get rid of thousand separator
        sValue = sValue.replace(sThousandSeparator, "");
        sNakedValue = sValue.replace(sDecimalSeparator, "");
        const arrValue = sValue.split(sDecimalSeparator);

        if (arrValue.length == 1) //no fractionals
            return this.#convertToInternalPrecision(this.parseIntBetter(sNakedValue), 0);
        else //detected fractional
            return this.#convertToInternalPrecision(this.parseIntBetter(sNakedValue), arrValue[1].length);
    }

    /**
     * converts an internal value (can be the internal value, min or max) to string
     * this takes the decimal precision into account!
     * 
     * @param {integer} iValue 
     * @param {string} sDecimalSeparator 
     * @param {string} sThousandSeparator 
     * @returns 
     */
    #convertValueToString(iValue, sDecimalSeparator = this.#sDecimalSeparator, sThousandSeparator = this.#sThousandSeparator)
    {
        let sBeforeDec = ""; //before decimal
        let sAfterDec = ""; //after decimal



        //convert value
        const sTempVal = iValue.toString();
        if (sTempVal.length - this.#iDecimalPrecision >= 0)//prevent from becoming negative slice() starts counting from the backside of the array instead of the front. This can happen with for example 0.06 (=internal 600) when decimalprecision is 4
        {
            sBeforeDec = sTempVal.slice(0, sTempVal.length - this.#iDecimalPrecision);     
            sAfterDec = sTempVal.slice(sTempVal.length - this.#iDecimalPrecision, sTempVal.length);  
        }
        else
        {
            // debugger
            sAfterDec = sTempVal.padStart(this.#iDecimalPrecision, 0);
            sBeforeDec = "0";            
        }


        if (sBeforeDec == "") //prevent dot without integer (.0) from happening
            sBeforeDec = "0";
        if (sAfterDec == "") //prevent dot without integer (.0) from happening
            sAfterDec = "0";

        //add thousand separators every 3 characters
        if (sBeforeDec.length > 3)
        {
            let iDigitCounter = 0; //the digit counter starts counting RIGHT to LEFT the amount of digits. it is a modulo 3 number.
            let sNewBeforeDec = "";
            for (let iIndex = sBeforeDec.length -1; iIndex >= 0; iIndex--) //start counting from RIGHT to LEFT (not left to right)
            {       
                if (iDigitCounter == 3) //every 3: add 1 character and separator
                {
                    sNewBeforeDec = sBeforeDec[iIndex] + sThousandSeparator + sNewBeforeDec;
                }
                else //just copy 1 character
                {
                    sNewBeforeDec = sBeforeDec[iIndex] + sNewBeforeDec;
                }
                
                //update registration
                iDigitCounter = iDigitCounter % 3;                    
                iDigitCounter++;
            }
            sBeforeDec = sNewBeforeDec;
        }
        
        //get rid of trailing zeros
        let iTrailZeros = 0;
        if (sAfterDec.length > 0)
        {
            for (let iIndex = sAfterDec.length -1; iIndex >= 1; iIndex--)
            {
                if (sAfterDec[iIndex] == "0")    
                    iTrailZeros++;
                else
                    iIndex = 0; //exit loop
            }
            // console.log("trail zeros L:", iTrailZeros, sAfterDec.length);

            //max to remove is the this.#iPadEndDecimalZeros
            if (iTrailZeros > this.#iPadEndDecimalZeros)
                iTrailZeros = this.#iPadEndDecimalZeros;

            sAfterDec = sAfterDec.slice(0, sAfterDec.length - iTrailZeros);
        }

        return sBeforeDec + sDecimalSeparator + sAfterDec;
    }    

    /**
     * mathematical function: add integer value to internal value
     * 
     * @param {int} iAdd add this number to internal value 
     * @param {int} iDecPrecision decimal precision
     */
    addInt(iAdd, iDecPrecision)
    {
        iAdd = this.#convertToInternalPrecision(iAdd, iDecPrecision);

        //add value to internal value
        this.#iValue += iAdd;
    }


    /**
     * mathematical function substract integer value from internal value
     * 
     * @param {int} iSubtract 
     * @param {int} iDecPrecision decimal precision
     */
    subtractInt(iSubtract, iDecPrecision)
    {
        iSubtract = this.#convertToInternalPrecision(iSubtract, iDecPrecision);

        //subtract value from internal value
        this.#iValue -= iSubtract;
    }
 
    populate()
    {
        this.#objEditBox = this.shadowRoot.querySelector("input");
        this.#objBtnMin = this.shadowRoot.querySelector(".min");
        this.#objBtnPlus = this.shadowRoot.querySelector(".plus");

        this.#objBtnMin.innerHTML = this.sSVGMinus;
        this.#objBtnPlus.innerHTML = this.sSVGPlus;

        if (this.#sType == this.arrTypes.plain)
        {
            this.shadowRoot.removeChild(this.#objBtnMin);
            this.shadowRoot.removeChild(this.#objBtnPlus);
        }

        //update UI
        this.updateUI();

        //update form value
        this.#objFormInternals.setFormValue(this.getValueAsString("."));        
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
            this.setValueAsUserInput(this.#objEditBox.value);
            this.#objFormInternals.setFormValue(this.getValueAsString("."));
            console.log("setformvalue KEYUP",this.getValueAsString("."));            
            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });     
        
        //BUTTON +
        this.#objBtnPlus.addEventListener("mousedown", (objEvent)=>
        {   
            this.addInt(1, 0);
            this.correctBoundariesInput();
            this.updateUI();
            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });

        //BUTTON -
        this.#objBtnMin.addEventListener("mousedown", (objEvent)=>
        {   
            this.subtractInt(1, 0);
            this.correctBoundariesInput();
            this.updateUI();

            if (this.#iValue == 0)
                this.#dispatchEventInputZero();
            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });

        //FOCUSOUT: correct input so user knows what time is recognized
        this.#objEditBox.addEventListener("focusout", (objEvent)=>
        {
            this.correctBoundariesInput();
            this.#objEditBox.value = this.getValueAsUserOutput();
        }, { signal: this.#objAbortController.signal });    
            
    
        //WHEEL: mousewheel adds or subtracts integers
        this.#objEditBox.addEventListener("wheel", (objEvent)=>
        {
            // console.log("focussed element:", document.activeElement, this, (document.activeElement == this));
        
            if (document.activeElement == this) //only when focus is on current element
            {
                if (objEvent.deltaY > 0)   
                    this.subtractInt(1, 0);
                if (objEvent.deltaY < 0)   
                    this.addInt(1, 0);

                this.correctBoundariesInput();
                this.updateUI();

                if (this.#iValue == 0)
                    this.#dispatchEventInputZero();
                this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");

                //prevent scrolling
                objEvent.preventDefault();
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

        //is a number
        if (!isNaN(objEvent.key))
        {
            return true;
        }

        if (objEvent.key == "ArrowUp")
        {
            this.addInt(1,0);
            this.correctBoundariesInput();

            this.updateUI();

            if (this.#iValue == 0)
                this.#dispatchEventInputZero();

            return true;
        }

        if (objEvent.key == "ArrowDown")
        {  
            this.subtractInt(1,0);
            this.correctBoundariesInput()
    
            this.updateUI();            
    
            if (this.#iValue == 0)
                this.#dispatchEventInputZero();
            
            return true;
        }

        if ((objEvent.key == "ArrowLeft") || (objEvent.key == "ArrowRight") || (objEvent.key == "Home") || (objEvent.key == "End"))
        {
            return true;
        }
    

        if ((objEvent.key == "Enter") || (objEvent.key == "Backspace") || (objEvent.key == "Delete") || (objEvent.key == "Tab"))
        {
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

        //decimal separator
        if ((objEvent.key == this.#sDecimalSeparator))
        {
            //only allow 1 separator
            let arrOccurrences = this.#objEditBox.value.split(this.#sDecimalSeparator);
            if ((arrOccurrences.length == 0) || (arrOccurrences.length == 1))
                return true
            else
                return false;
        }

        //thousand separator
        if (objEvent.key == this.#sThousandSeparator)
        {
            return true
        }

        //negative
        if (objEvent.key == "-")
        {
            //only allow 1
            let arrOccurrences = this.#objEditBox.value.split("-");
            if ((arrOccurrences.length == 0) || (arrOccurrences.length == 1))
                return true
            else
                return false;
        }

        return false;
    }

    /**
     * after input is done, corrects value to stay within boundaries
     * 
     * @returns {boolean} true=applied correction, false=didn't apply correction
     */
    correctBoundariesInput()
    {
        if (this.isMinMaxCheckEnabled())
        {
            //check min
            if (this.#iValue < this.#iMinValue)
            {
                this.#iValue = this.#iMinValue;
                return true;          
            }
                
            //check max
            if (this.#iValue > this.#iMaxValue)
            {
                this.#iValue = this.#iMaxValue;
                return true;
            }
        }

        return false;
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
        this.#objFormInternals.setFormValue(this.getValueAsString("."));
        console.log("setformvalue dispatchEventInputUpdated",this.getValueAsString("."));
            
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
     * dispatch event that value has become zero
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventInputZero(objSource, sDescription)
    {
        //probably something changed, thus update the form value
        this.#objFormInternals.setFormValue(this.getValueAsString("."));
        console.log("setformvalue dispatchEventInputZero",this.getValueAsString("."));
            
        this.dispatchEvent(new CustomEvent("dr-input-number-zero",
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
     * returns if this class should account for min and max values
     * 
     * @returns {bopl}
     */
    isMinMaxCheckEnabled()
    {
        if ((this.#iMinValue == 0) && (this.#iMaxValue == 0))
            return false;
        
        return true;        
    }
    
    /**
     * function to ease comparison
     * 
     * @param {int} iValueAInteger 
     * @param {int} iDecPrecA  decimal precision A
     * @param {int} iValueBInteger 
     * @param {int} iDecPrecB decimal precision B
     */
    isGreaterThan(iValueAInteger, iDecPrecA, iValueBInteger, iDecPrecB)
    {
        iValueAInteger = this.#convertToInternalPrecision(iValueAInteger, iDecPrecA);
        iValueBInteger = this.#convertToInternalPrecision(iValueBInteger, iDecPrecB);

        return (iValueAInteger > iValueBInteger);
    }

    /**
     * function to ease comparison
     * 
     * @param {int} iValueAInteger 
     * @param {int} iDecPrecA decimal precision A
     * @param {int} iValueBInteger 
     * @param {int} iDecPrecB decimal precision B
     */
    isLessThan(iValueAInteger, iDecPrecA, iValueBInteger, iDecPrecB)
    {
        iValueAInteger = this.#convertToInternalPrecision(iValueAInteger, iDecPrecA);
        iValueBInteger = this.#convertToInternalPrecision(iValueBInteger, iDecPrecB);

        return (iValueAInteger < iValueBInteger);
    }

    /**
     * function to ease comparison
     * 
     * @param {int} iValueAInteger 
     * @param {int} iDecPrecA decimal precision A
     * @param {int} iValueBInteger 
     * @param {int} iDecPrecB decimal precision B
     */
    isEqual(iValueAInteger, iDecPrecA, iValueBInteger, iDecPrecB)
    {
        iValueAInteger = this.#convertToInternalPrecision(iValueAInteger, iDecPrecA);
        iValueBInteger = this.#convertToInternalPrecision(iValueBInteger, iDecPrecB);

        return (iValueAInteger == iValueBInteger);
    }

    updateUI()
    {
        this.#objEditBox.value = this.getValueAsUserOutput();

        //turns minus icon into trashcan icon 
        if (this.#bZeroMinusTrashcan)
        {
            if (this.#iValue == 1)
            {
                this.#objBtnMin.innerHTML = this.sSVGTrashcan;
            }
            else
            {
                this.#objBtnMin.innerHTML = this.sSVGMinus;
            }
        }

        if (this.isMinMaxCheckEnabled())
        { 
            let iValueMinusOne = this.#iValue - this.#convertToInternalPrecision(1, 0);
            if (iValueMinusOne < this.#iMinValue)
                this.#objBtnMin.classList.add("disabled");
            else
                this.#objBtnMin.classList.remove("disabled");

            let iValuePlusOne = this.#iValue + this.#convertToInternalPrecision(1, 0);
            if (iValuePlusOne > this.#iMaxValue)
                this.#objBtnPlus.classList.add("disabled");
            else
                this.#objBtnPlus.classList.remove("disabled");
        }

        //handle disabled
        if (this.#bDisabled)
        {
            this.#objBtnMin.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
            this.#objEditBox.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
            this.#objBtnPlus.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
        }
        else
        {
            this.#objBtnMin.removeAttribute("disabled");
            this.#objEditBox.removeAttribute("disabled");
            this.#objBtnPlus.removeAttribute("disabled");
        }
    }


    /** 
     * set number of decimals.
     * i.e. 4 = 4 decimal precision after decimal separator
    */
    set precision(iDecimalPrecision)
    {
        this.#iDecimalPrecision = this.parseIntBetter(iDecimalPrecision);
        this.updateUI();
    }

    /** 
     * get number of decimals.
     * i.e. 4 = 4 decimal precision after decimal separator
    */
    get precision()
    {
        return this.#iDecimalPrecision;
    }


    /** 
     * set decimal separator
     * i.e. dot (.)
    */
    set decimalseparator(sSeparator)
    {
        this.#sDecimalSeparator = sSeparator;
        this.updateUI();
    }

    /** 
     * get decimal separator
     * i.e. dot (.)
    */
    get decimalseparator()
    {
        return this.#sDecimalSeparator;
    }


    /** 
     * set thousandseparator separator
     * i.e. dot (.)
    */
    set thousandseparator(sSeparator)
    {
        this.#sThousandSeparator = sSeparator;
        this.updateUI();
    }

    /** 
     * get thousandseparator separator
     * i.e. dot (.)
    */
    get thousandseparator()
    {
        return this.#sThousandSeparator;
    }

    /** 
     * set pad zeros at the end after decimal separator
     * 0=don't pad
     * 2=2 decimals after decimal separator
     * 4=4 decimals after decimal separator
     * 
     * @param {int} iPadEndDecimalZeros number of zeros to pad out
    */
    set padzero(iPadEndDecimalZeros)
    {
        this.#iPadEndDecimalZeros = iPadEndDecimalZeros;
        this.updateUI();
    }

    /** 
     * get pad zeros at the end after decimal separator
     * 0=don't pad
     * 2=2 decimals after decimal separator
     * 4=4 decimals after decimal separator
    */
    get padzero()
    {
        return this.#iPadEndDecimalZeros;
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
        this.setValueAsString(sValue, ".", ",");
    }

    /** 
     * get internal value as string with decimal separator dot(.)
    */
    get value()
    {
        return this.getValueAsString(".")
    }

    /**
     * returns if user changed the contents of this box
     */
    get dirty()
    {
        return this.getDirty();
    }

    /** 
     * set internal min value as string with decimal separator dot(.)
    */
    set min(sValue)
    {
        this.setMinValueAsString(sValue, ".")
    }

    /** 
     * get internal min value as string with decimal separator dot(.)
    */
    get min()
    {
        return this.getMinValueAsString(".")
    }
    
    /** 
     * set internal max value as string with decimal separator dot(.)
    */
    set max(sValue)
    {
        this.setMaxValueAsString(sValue, ".")
    }

    /** 
     * get internal max value as string with decimal separator dot(.)
    */
    get max()
    {
        return this.getMaxValueAsString(".")
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
     * when true turns minus icon into trashcan
    */
    set zerotrashcan(bTrashMinus)
    {
        this.#bZeroMinusTrashcan = bTrashMinus;
        this.updateUI();
    }

    /** 
     * when true turns minus icon into trashcan
    */
    get zerotrashcan()
    {
        return this.#bZeroMinusTrashcan;
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
    }



    static get observedAttributes() 
    {
        return ["disabled", "value"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number", sAttrName, sNewVal);
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
            this.#objFormInternals.setFormValue(this.getValueAsString(".")); //default is 0
            console.log("setformvalue connectedCallback",this.getValueAsString("."));

            //render
            this.populate();
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
customElements.define("dr-input-number", DRInputNumber);