<?php 
/**
 * dr-input-time.js
 *
 * class to create a time picker
 * 
 * WARNING:
 * -this class accepts and returns a ISO 8601 as datetime format in "value" attribute, this means that you NEED to supply a date with the time (although the date isn't used)
 * 
 * 
 * EXAMPLE:
 * <dr-input-time phpformat="H:i:s" value="2025-03-23 18:40:25" allowemptytime></dr-input-time> <=== note that ISO 8601 INCLUDES THE DATE 
 * 
 * 
 * FIRES EVENT: 
 * - "update" when anything changes in editbox. You can see it as the equivalent of 'keyup' event for edit boxes
 * - "change" when user leaves editbox and is changed
 * 
 * @author Dennis Renirie
 * 
 * @todo select timezone (and time is automatically converted ==> could be backwards, so needs date as well)
 * @todo also AM/PM een button-up and button-down + scrollwheel
 * 
 * 22 mrt 2025 dr-input-time.js created
 * 5 apr 2025: dr-input-time.js: aparte eventlisteners voor editbox en clock
 * 5 apr 2025: dr-input-time.js: mousewheel update nu uren, minuten en seconden
 * 26 sept 2025: dr-input-time.js: BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 13 okt 2025 dr-input-time.js: ADD: focusout triggert een 'change'-event wanneer waarde gewijzigd
 * */
?>


class DRInputTime extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            { 
                display: inline-block;
                border-width: 1px;
                border-style: solid;
                border-color: light-dark(var(--lightmode-color-drinputdate-border, rgb(234, 234, 234)), var(--darkmode-color-drinputdate-border, rgb(71, 71, 71)));
            } 

            svg
            {
                width: 20px;
                height: 20px;
            }
            
            button
            {
                padding: 5px;
                padding-left: 20px;
                padding-right: 20px;
                border-radius: 5px;

                border-color: light-dark(var(--lightmode-color-drinputdate-button-border, rgb(234, 234, 234)), var(--darkmode-color-drinputdate-button-border, rgb(71, 71, 71)));
                background-color: light-dark(var(--lightmode-color-drinputdate-button-background, rgb(212, 212, 212)), var(--darkmode-color-drinputdate-button-background, rgb(107, 107, 107)));
                border-width: 0px;
                cursor: pointer;
            }



            .contentdivider
            {
                display: flex;
            }

            input
            {
                border-width: 0px;
                width: 100%;
            }
            
            .clockicon
            {
                display: inline;
                cursor: pointer;
                
                
                margin: 0px;
                padding-top: 5px;
                
                box-sizing: content-box;
            }

            .clockbody
            {
                display: inline-flex;
                user-select: none;
            }

            .clockbody .row
            {
                height: 40px;
            }

            button.clockplusmin
            {
                border-radius: 10px;
                width: 40px;
                height: 30px;
                
                padding: 5px;
            }

            .clockdigits
            {
                text-align: center;
                font-size: 20px;    
                line-height: 40px;            
            }            
                    

            .clockfooter
            {
                text-align: center;
                margin: 0px;
                margin-top: 10px;
            }
        </style>

        <div class="contentdivider">
            <input type="text">
        
            <div class="clockicon">
                <!-- SVG icon inserted here -->
            </div>
        </div>
    

        <dr-popover>
            <div class="clockbody">
                <!-- text below is replaced and dynamically generated based on phpformat -->
                <div>
                    <button class="row clockplusmin">
                        <svg fill="currentColor" style="transform: rotate(270deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                    </button>
                    <div class="row clockdigits">
                    13
                    </div>
                    <button class="row clockplusmin">
                        <svg fill="currentColor" style="transform: rotate(90deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                    </button>
                </div>
                <div>
                    <div class="row">
                        &nbsp;
                    </div>
                    <div class="row clockdigits">
                        :
                    </div>
                    <div class="row">
                        &nbsp;
                    </div>                    
                </div>
                <div>
                    <button class="row clockplusmin">
                        <svg fill="currentColor" style="transform: rotate(270deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                    </button>
                    <div class="row clockdigits">
                    24
                    </div>
                    <button class="row clockplusmin">
                        <svg fill="currentColor" style="transform: rotate(90deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                    </button>
                </div>
                <div>
                    <div class="row">
                        &nbsp;
                    </div>
                    <div class="row clockdigits">
                        <select>
                            <option>am</option>
                            <option>pm</option>
                        </select>
                    </div>
                    <div class="row">
                        &nbsp;
                    </div>                    
                </div>
            </div>
            <div class="clockfooter">
                <button class="now">now<!-- auto generated by language text --></button>
            </div>
        </dr-popover>        
    `;

    #objFormInternals = null;
    #objTime = null; //Date() object that is only used for time
    #objDivClockIcon = null;//
    #objDivClockBody = null;
    #objBtnNow = null; //now button
    #objBtnHoursMin = null;
    #objBtnHoursPlus = null;
    #objDivHoursgText = null; //refers to php format "g"
    #objDivHoursGText = null; //refers to php format "G"
    #objDivHourshText = null; //refers to php format "h"
    #objDivHoursHText = null; //refers to php format "H"
    #objBtnMinsMin = null;
    #objBtnMinsPlus = null;
    #objDivMinsText = null;
    #objDivColHours = null;
    #objDivColsMins = null;
    #objDivColsSecs = null;
    #objDivColsAMPM = null;
    #objBtnSecsMin = null;
    #objBtnSecsPlus = null;
    #objDivSecsText= null;
    #objSelAMPM = null; //Value is either 0 or 12 (AM = 0, PM = 12) ==> 12 referring to the 12 extra hours
    #objEditBox = null; //the internal <input type="text">
    #objDRBubble = null; //bubble with calendar
    #sPHPFormat = "H:i:s";
    #objAbortController = null;    
    #objAbortControllerClock = null;    
    #bRequired = false;    
    #bShowEditBox = true;
    #bShowClock = true;
    #bAllowEmptyTime = false;
    #bDisabled = false;
    #bConnectedCallbackHappened = false;
    #bDispatchChange = false; //keeps track if value is updated (with dispatchUpdate()), so we can fire a 'changed' event when focus is lost. THIS IS NOT A 'DIRTY'-FLAG!!!

    sTransAM = "am"; //translation of ante meridian 
    sTransPM = "pm"; //translation of post meridian
    sTransAMPM = "am/pm"; //translation of Ante or post meridian (used in placeholder)
    sTransHours = "hh"; //translation of hours (used in placeholder)
    sTransMinutes = "mm"; //translation of minutes (used in placeholder)
    sTransSeconds = "ss"; //translation of seconds (used in placeholder)
    sTransNow = "Now"; //translation of now

    sSVGCalendar = '<svg viewBox="-4 0 38 32" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:currentColor;}</style></defs><title/><g data-name="Layer 15" id="Layer_15"><path class="cls-1" d="M16,31A15,15,0,1,1,31,16,15,15,0,0,1,16,31ZM16,3A13,13,0,1,0,29,16,13,13,0,0,0,16,3Z"/><path class="cls-1" d="M20.24,21.66l-4.95-4.95A1,1,0,0,1,15,16V8h2v7.59l4.66,4.65Z"/></g></svg>';
    sSVGPlus = '<svg fill="currentColor" style="transform: rotate(270deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>';
    sSVGMin = '<svg fill="currentColor" style="transform: rotate(90deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>';

    #sCachePlaceholder = "";  
    #sCacheMaxLength = 5;  //12:12 (=5 characters)
    #arrCacheValueSeparators = []; //value separators are all the characters that are not special characters. In 12-04-2006 that would be ["-", "-"]

    static formAssociated = true;        

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objFormInternals = this.attachInternals();           
        this.#objAbortController = new AbortController();
        this.#objAbortControllerClock = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputTime.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
    }    

    #readAttributes()
    {
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", false);
        this.required = DRComponentsLib.attributeToBoolean(this, "required", this.#bRequired);
        this.phpformat = DRComponentsLib.attributeToString(this, "phpformat", this.#sPHPFormat);     
        this.allowemptytime = DRComponentsLib.attributeToBoolean(this, "allowemptytime", this.#bAllowEmptyTime);
        if (!this.#bAllowEmptyTime)
            this.#objTime = new Date();
        this.showeditbox = DRComponentsLib.attributeToBoolean(this, "showeditbox",this.#bShowEditBox);
        this.showclock = DRComponentsLib.attributeToBoolean(this, "showclock",this.#bShowClock);    

        if (this.getAttribute("value") !== null)
        {
            if (this.getAttribute("value") == "")
                this.#objTime = null;
            else
                this.internalTimeAsISO8601 = this.getAttribute("value");   
        }
        
        //translations
        this.sTransAM       = DRComponentsLib.attributeToString(this, "transam", this.sTransAM);  
        this.sTransPM       = DRComponentsLib.attributeToString(this, "transpm", this.sTransAM); 
        this.sTransAMPM     = DRComponentsLib.attributeToString(this, "transampm", this.sTransAMPM); 
        this.sTransHours    = DRComponentsLib.attributeToString(this, "transhours", this.sTransHours); 
        this.sTransMinutes  = DRComponentsLib.attributeToString(this, "transminutes", this.sTransMinutes); 
        this.sTransSeconds  = DRComponentsLib.attributeToString(this, "transseconds", this.sTransSeconds); 
        this.sTransNow      = DRComponentsLib.attributeToString(this, "transnow", this.sTransNow); 
    }


   /**
     * create cache for items, so we only have to calculate stuff once
     */
   buildCache()
   {

       //determine placeholder based on PHP Format
       this.#sCachePlaceholder = "";  
       this.#sCacheMaxLength = 0;          
       for (let iIndex = 0; iIndex < this.#sPHPFormat.length; iIndex++)
       {
           switch(this.#sPHPFormat[iIndex]) 
           {
               case "a": //AM/PM: Lowercase Ante meridiem and Post meridiem: am or pm
               case "A": //AM/PM: Uppercase Ante meridiem and Post meridiem: AM or PM
                   this.#sCachePlaceholder += this.sTransAMPM;
                   this.#sCacheMaxLength += 2;
                   break;
               case "g": //hours: 12-hour format of an hour without leading zeros: 1 through 12
               case "G": //hours: 24-hour format of an hour without leading zeros: 0 through 23
               case "h": //hours: 12-hour format of an hour with leading zeros: 01 through 12
               case "H": //hours: 24-hour format of an hour with leading zeros: 00 through 23
                   this.#sCachePlaceholder += this.sTransHours;
                   this.#sCacheMaxLength += 2;
                   break;
               case "i": //minutes: Minutes with leading zeros: 00 to 59
                   this.#sCachePlaceholder += this.sTransMinutes;
                   this.#sCacheMaxLength += 2;
                   break;
               case "s": //seconds: Seconds with leading zeros: 00 through 59
                   this.#sCachePlaceholder += this.sTransSeconds;
                   this.#sCacheMaxLength += 2;
                   break;
               default:
                   this.#sCachePlaceholder += this.#sPHPFormat[iIndex];            
                   this.#sCacheMaxLength += 1;
           }    
       }

       //value separators
       this.#arrCacheValueSeparators = this.#getValueSeparatorsPHPFormat();

   }       


    /**
     * create UI elements
     */
    populate()
    {
        //adding clock icon
        if ((this.#bShowEditBox) && (this.#bShowClock))
        {
            this.#objDivClockIcon = this.shadowRoot.querySelector(".clockicon");
            this.#objDivClockIcon.innerHTML += this.sSVGCalendar;
        }
    
        //editbox
        if (this.#bShowEditBox)
        {
            this.#objEditBox = this.shadowRoot.querySelector("input");
        }

        //bubble with clock
        if (this.#bShowClock)
        {
            this.#objDRBubble = this.shadowRoot.querySelector("dr-popover");
            this.#objDRBubble.anchorobject = this;
            this.#objDRBubble.anchorpos = this.#objDRBubble.iPosTop;
            this.#objDRBubble.hideonclick = false;

            this.#objDivClockBody = this.#objDRBubble.querySelector(".clockbody");

            this.#objBtnNow = this.#objDRBubble.querySelector(".now");

            this.renderClock();
        }

        //set form value
        this.#objFormInternals.setFormValue(this.internalTimeAsISO8601);
    }

    /**
     * render bubble clock
     */
    renderClock()
    {
        this.#objDivClockBody.innerHTML = "";

        //render elements in order of PHP Format    
        for (let iIndex = 0; iIndex < this.#sPHPFormat.length; iIndex++)
        {
            switch(this.#sPHPFormat[iIndex]) 
            {
                case "a": //AM/PM: Lowercase Ante meridiem and Post meridiem: am or pm
                    this.#objDivClockBody.appendChild(this.#renderClockColAMPM(false));
                    break;
                case "A": //AM/PM: Uppercase Ante meridiem and Post meridiem: AM or PM
                    this.#objDivClockBody.appendChild(this.#renderClockColAMPM(true));
                    break;
                case "g": //hours: 12-hour format of an hour without leading zeros: 1 through 12
                    this.#objDivHoursgText = document.createElement("div");
                    this.#objDivClockBody.appendChild(this.#renderClockColHours(this.#objDivHoursgText));
                    break;
                case "G": //hours: 24-hour format of an hour without leading zeros: 0 through 23
                    this.#objDivHoursGText = document.createElement("div");
                    this.#objDivClockBody.appendChild(this.#renderClockColHours(this.#objDivHoursGText));
                    break;
                case "h": //hours: 12-hour format of an hour with leading zeros: 01 through 12
                    this.#objDivHourshText = document.createElement("div");
                    this.#objDivClockBody.appendChild(this.#renderClockColHours(this.#objDivHourshText));                
                    break;
                case "H": //hours: 24-hour format of an hour with leading zeros: 00 through 23
                    this.#objDivHoursHText = document.createElement("div");
                    this.#objDivClockBody.appendChild(this.#renderClockColHours(this.#objDivHoursHText));
                    break;
                case "i": //minutes: Minutes with leading zeros: 00 to 59
                    this.#objDivClockBody.appendChild(this.#renderClockColMins());
                    break;
                case "s": //seconds: Seconds with leading zeros: 00 through 59
                    this.#objDivClockBody.appendChild(this.#renderClockColSecs());
                    break;
                default:
                    this.#objDivClockBody.appendChild(this.#renderClockColSeparator(this.#sPHPFormat[iIndex]));
            }    
        }

        //button "Now"
        this.#objBtnNow.innerHTML = this.sTransNow;
    }

    /**
     * render column for value separator
     * 
     * @returns {string} sSeparator
     * @returns {HTMLElement} <div> with column
     */
    #renderClockColSeparator(sSeparator)
    {
        const objDivParent = document.createElement("div");
        let objDiv = null;

        //first row - nothing
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.innerHTML = "&nbsp;";
        objDivParent.appendChild(objDiv);

        //second row - separator
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.classList.add("clockdigits");

        objDiv.innerHTML = sSeparator;

        objDivParent.appendChild(objDiv);

        //third row - nothing
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.innerHTML = "&nbsp;";
        objDivParent.appendChild(objDiv);
        
        return objDivParent;
    }    

    /**
     * render column with <select><option>am</option><option>pm</option></select>
     * @param {boolean} bUpperCase 
     * @returns {HTMLElement}
     */
    #renderClockColAMPM(bUpperCase)
    {
        this.#objDivColsAMPM = document.createElement("div");
        let objDiv = null;

        //first row - nothing
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.innerHTML = "&nbsp;";
        this.#objDivColsAMPM.appendChild(objDiv);

        //second row - combobox
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.classList.add("clockdigits");

        this.#objSelAMPM = document.createElement("select");

            //AM
            const objOptAM = document.createElement("option");
            objOptAM.value = 0;
            if (bUpperCase)
                objOptAM.textContent = this.sTransAM.toUpperCase();
            else
                objOptAM.textContent = this.sTransAM.toLowerCase();
            this.#objSelAMPM.appendChild(objOptAM);

            //PM
            const objOptPM = document.createElement("option");
            objOptPM.value = 12;
            if (bUpperCase)
                objOptPM.textContent = this.sTransPM.toUpperCase();
            else
                objOptPM.textContent = this.sTransPM.toLowerCase();
            this.#objSelAMPM.appendChild(objOptPM);

        objDiv.appendChild(this.#objSelAMPM);
        this.#objDivColsAMPM.appendChild(objDiv);

        //third row - nothing
        objDiv = document.createElement("div");
        objDiv.classList.add("row");
        objDiv.innerHTML = "&nbsp;";
        this.#objDivColsAMPM.appendChild(objDiv);
        
        return this.#objDivColsAMPM;
    }

    /**
     * render column for the hours
     * 
     * @param {HTMLElement} objDivText <div> with text
     * @returns {HTMLElement} <div> with column
     */
    #renderClockColHours(objDivText)
    {
        this.#objDivColHours = document.createElement("div");

        //first row - button plus
        this.#objBtnHoursPlus = document.createElement("button");
        this.#objBtnHoursPlus.classList.add("row");
        this.#objBtnHoursPlus.classList.add("clockplusmin");
        this.#objBtnHoursPlus.innerHTML = this.sSVGPlus;
        this.#objDivColHours.appendChild(this.#objBtnHoursPlus);
        
        //second row - digit
        objDivText.classList.add("row");
        objDivText.classList.add("clockdigits");
        objDivText.innerHTML = "0"; //temp placeholder
        this.#objDivColHours.appendChild(objDivText);

        //third row - button min
        this.#objBtnHoursMin = document.createElement("button");
        this.#objBtnHoursMin.classList.add("row");
        this.#objBtnHoursMin.classList.add("clockplusmin");
        this.#objBtnHoursMin.innerHTML = this.sSVGMin;
        this.#objDivColHours.appendChild(this.#objBtnHoursMin);

        return this.#objDivColHours;
    }

    /**
     * render column for the minutes
     * 
     * @returns {HTMLElement} <div> with column
     */
    #renderClockColMins()
    {
        this.#objDivColsMins = document.createElement("div");

        //first row - button plus
        this.#objBtnMinsPlus = document.createElement("button");
        this.#objBtnMinsPlus.classList.add("row");
        this.#objBtnMinsPlus.classList.add("clockplusmin");
        this.#objBtnMinsPlus.innerHTML = this.sSVGPlus;
        this.#objDivColsMins.appendChild(this.#objBtnMinsPlus);

        
        //second row - digit
        this.#objDivMinsText = document.createElement("div");
        this.#objDivMinsText.classList.add("row");
        this.#objDivMinsText.classList.add("clockdigits");
        this.#objDivMinsText.innerHTML = "0"; //temp placeholder
        this.#objDivColsMins.appendChild(this.#objDivMinsText);

        //third row - button min
        this.#objBtnMinsMin = document.createElement("button");
        this.#objBtnMinsMin.classList.add("row");
        this.#objBtnMinsMin.classList.add("clockplusmin");
        this.#objBtnMinsMin.innerHTML = this.sSVGMin;
        this.#objDivColsMins.appendChild(this.#objBtnMinsMin);

        return this.#objDivColsMins;
    }

    /**
     * render column for the seconds
     * 
     * @returns {HTMLElement} <div> with column
     */
    #renderClockColSecs()
    {
        this.#objDivColsSecs = document.createElement("div");

        //first row - button plus
        this.#objBtnSecsPlus = document.createElement("button");
        this.#objBtnSecsPlus.classList.add("row");
        this.#objBtnSecsPlus.classList.add("clockplusmin");
        this.#objBtnSecsPlus.innerHTML = this.sSVGPlus;
        this.#objDivColsSecs.appendChild(this.#objBtnSecsPlus);
        
        //second row - digit
        this.#objDivSecsText = document.createElement("div");
        this.#objDivSecsText.classList.add("row");
        this.#objDivSecsText.classList.add("clockdigits");
        this.#objDivSecsText.innerHTML = "0"; //temp placeholder
        this.#objDivColsSecs.appendChild(this.#objDivSecsText);

        //third row - button min
        this.#objBtnSecsMin = document.createElement("button");
        this.#objBtnSecsMin.classList.add("row");
        this.#objBtnSecsMin.classList.add("clockplusmin");
        this.#objBtnSecsMin.innerHTML = this.sSVGMin;
        this.#objDivColsSecs.appendChild(this.#objBtnSecsMin);

        return this.#objDivColsSecs;
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
                return;
            }

            //when things are wrong
            objEvent.preventDefault();

        }, { signal: this.#objAbortController.signal });


        //KEYUP
        this.#objEditBox.addEventListener("keyup", (objEvent)=>
        {

            if ((this.#bAllowEmptyTime) && (this.#objEditBox.value == ""))
            {
                this.#objTime = null;
                this.#objFormInternals.setFormValue("");
            }
            else
            {
                this.#objEditBox.value = 
                this.internalTimeAsPHPTime = this.#objEditBox.value;
                this.#objFormInternals.setFormValue(this.internalTimeAsISO8601);
                
                this.#updateUIClock();
            }

            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });     
        

        //MOUSEDOWN
        this.#objDivClockIcon.addEventListener("mousedown", (objEvent)=>
        {
            // console.log("mousedowniiiieeeee");
            if (this.#bShowClock)
            {
                if (this.#objDRBubble)
                {
                    if (this.#objDRBubble.isHidden())
                    {
                        if (this.#objTime === null)
                            this.#objTime = new Date();

                        //make the timestamp in the editbox the timestamp of the calendar
                        this.#objTime.setTime(this.#objTime.getTime());
                        
                        this.#objDRBubble.show();

                        this.updateUI();
                    }
                }
            }         

        }, { signal: this.#objAbortController.signal });     


        //DOUBLE CLICK MOUSE
        this.#objEditBox.addEventListener("dblclick", (objEvent)=>
        {
            if (this.#bShowClock)
            {
                if (this.#objDRBubble)
                {
                    if (this.#objDRBubble.isHidden())
                    {
                        if (this.#objTime === null)
                            this.#objTime = new Date();

                        //make the timestamp in the editbox the timestamp of the calendar
                        this.#objTime.setTime(this.#objTime.getTime());
                        
                        this.#objDRBubble.show();

                        this.updateUI();
                    }
                }
            }         

        }, { signal: this.#objAbortController.signal });  


        //FOCUSOUT: correct input so user knows what time is recognized
        this.#objEditBox.addEventListener("focusout", (objEvent)=>
        {
            //correct always, except when it's an empty date
            if (!((this.#bAllowEmptyTime) && (this.#objEditBox.value == "")))
            {
                this.#objEditBox.value = this.internalTimeAsPHPTime;
            }
        }, { signal: this.#objAbortController.signal });     
        

        //WHEEL: mousewheel adds or subtracts minutes
        this.#objEditBox.addEventListener("wheel", (objEvent)=>
        {
            if (this.#objTime)
            {
                if (objEvent.deltaY > 0)   
                    this.#objTime.setMinutes(this.#objTime.getMinutes() - 1);
                if (objEvent.deltaY < 0)   
                    this.#objTime.setMinutes(this.#objTime.getMinutes() + 1);

                this.updateUI();

                //prevent scrolling
                objEvent.preventDefault();
            }
        }, { signal: this.#objAbortController.signal });    
        
        //FOCUSOUT: this component
        this.addEventListener("focusout", (objEvent)=>
        {
            if (this.#bDispatchChange === true)
                this.#dispatchEventChange(this, "focusout this");
        }, { signal: this.#objAbortController.signal });     
                
    }

    /**
     * attach event listenres
     */
    addEventListenersClock()
    {

        //WHEEL: HOURS: mousewheel adds or subtracts
        if (this.#objDivColHours)
        {
            this.#objDivColHours.addEventListener("wheel", (objEvent)=>
            {
                if (this.#objTime)
                {
                    if (objEvent.deltaY > 0)   
                        this.#objTime.setHours(this.#objTime.getHours() - 1);
                    if (objEvent.deltaY < 0)   
                        this.#objTime.setHours(this.#objTime.getHours() + 1);

                    this.updateUI();

                    //prevent scrolling
                    objEvent.preventDefault();
                }
            }, { signal: this.#objAbortControllerClock.signal });  
        }

        //BUTTON: HOURS: MIN: MOUSEDOWN
        if (this.#objBtnHoursMin)
        {
            this.#objBtnHoursMin.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setHours(this.#objTime.getHours() - 1);

                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }

        //BUTTON: HOURS: PLUS: MOUSEDOWN
        if (this.#objBtnHoursPlus)
        {
            this.#objBtnHoursPlus.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setHours(this.#objTime.getHours() + 1);
                
                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }


        //WHEEL: MINUTES: mousewheel adds or subtracts
        if (this.#objDivColsMins)
        {
            this.#objDivColsMins.addEventListener("wheel", (objEvent)=>
            {
                if (this.#objTime)
                {
                    if (objEvent.deltaY > 0)   
                        this.#objTime.setMinutes(this.#objTime.getMinutes() - 1);
                    if (objEvent.deltaY < 0)   
                        this.#objTime.setMinutes(this.#objTime.getMinutes() + 1);

                    this.updateUI();

                    //prevent scrolling
                    objEvent.preventDefault();
                }
            }, { signal: this.#objAbortControllerClock.signal });  
        }
            
            
        //BUTTON: MINUTES: MIN: MOUSEDOWN
        if (this.#objBtnMinsMin)
        {
            this.#objBtnMinsMin.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setMinutes(this.#objTime.getMinutes() - 1);
                
                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }

        //BUTTON: MINUTES: PLUS: MOUSEDOWN
        if (this.#objBtnMinsPlus)
        {
            this.#objBtnMinsPlus.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setMinutes(this.#objTime.getMinutes() + 1);
                
                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }


        //WHEEL: SECONDS: mousewheel adds or subtracts
        if (this.#objDivColsSecs)
        {
            this.#objDivColsSecs.addEventListener("wheel", (objEvent)=>
            {
                if (this.#objTime)
                {
                    if (objEvent.deltaY > 0)   
                        this.#objTime.setSeconds(this.#objTime.getSeconds() - 1);
                    if (objEvent.deltaY < 0)   
                        this.#objTime.setSeconds(this.#objTime.getSeconds() + 1);

                    this.updateUI();

                    //prevent scrolling
                    objEvent.preventDefault();
                }
            }, { signal: this.#objAbortControllerClock.signal });  
        }
            
            
        //BUTTON: SECONDS: MIN: MOUSEDOWN
        if (this.#objBtnSecsMin)
        {
            this.#objBtnSecsMin.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setSeconds(this.#objTime.getSeconds() - 1);

                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }

        //BUTTON: SECONDS: PLUS: MOUSEDOWN
        if (this.#objBtnSecsPlus)
        {
            this.#objBtnSecsPlus.addEventListener("mousedown", (objEvent)=>
            {
                this.#objTime.setSeconds(this.#objTime.getSeconds() + 1);

                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }

        //SELECT: AM/PM: CHANGE
        if (this.#objSelAMPM)
        {
            this.#objSelAMPM.addEventListener("change", (objEvent)=>
            {
                if (parseInt(this.#objSelAMPM.value) == 0)     
                    this.#objTime.setHours(this.#objTime.getHours() - 12);
                if (parseInt(this.#objSelAMPM.value) == 12)     
                    this.#objTime.setHours(this.#objTime.getHours() + 12);
                                
                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });    
        }

        //BUTTON: NOW
        if (this.#objBtnNow)
        {
            this.#objBtnNow.addEventListener("mousedown", (objEvent)=>
            {                

                this.#objTime = new Date();

                this.#objDRBubble.hide();

                this.updateUI();
                this.#dispatchEventInputUpdated(this.#objDRBubble, "clock changed");
            }, { signal: this.#objAbortControllerClock.signal });                
        }

        //CHANGE event: editbox
        this.#objEditBox.addEventListener("change", (objEvent)=>
        {  
            this.#dispatchEventChange(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });  

    }

    
    /**
     * tranfer internal values to UI
     */
    updateUI()
    {
        //editbox
        if (this.#bShowEditBox)
        {
            this.#objEditBox.placeholder = this.#sCachePlaceholder; //done here so we can change php format or placeholder and just call updateUI()
            this.#objEditBox.maxLength = this.#sCacheMaxLength; //done here so we can change php format or placeholder and just call updateUI()

            if (this.#objTime)
                this.#objEditBox.value = this.internalTimeAsPHPTime;
            else 
                this.#objEditBox.value = "";
        }

        //clock
        if (this.#bShowClock)
        {
            if (this.#objTime)
            {
                this.#updateUIClock();
            }
        }

        //handle disabled
        if (this.#bDisabled)
        {
            this.#objEditBox.setAttribute("disabled", DRComponentsLib.boolToStr(this.#bDisabled));
        }
        else
        {
            this.#objEditBox.removeAttribute("disabled");
        }     
    }

    /**
     * update UI of clock
     */
    #updateUIClock()
    {
        //a/A: AM/PM
        if (this.#objSelAMPM)
        {
            for (let iIndex = 0; iIndex < this.#objSelAMPM.childElementCount; iIndex++)
            {
                //select nothing by default                
                this.#objSelAMPM.children[iIndex].selected = false;
                
                //before 12
                if ((this.#objTime.getHours() < 12) && (this.#objSelAMPM.children[iIndex].value == "0"))
                {
                    this.#objSelAMPM.children[iIndex].selected = true;
                    // console.log("before 12");
                }

                //after 12
                if ((this.#objTime.getHours() >= 12) && (this.#objSelAMPM.children[iIndex].value == "12"))
                {
                    this.#objSelAMPM.children[iIndex].selected = true;
                    // console.log("after 12");
                }
            }
        }

        //g: hours: 12-hour format of an hour without leading zeros: 1 through 12
        if (this.#objDivHoursgText)
        {
            if (this.#objTime.getHours() > 12)
                this.#objDivHoursgText.innerHTML = this.#objTime.getHours() - 12;
            else
                this.#objDivHoursgText.innerHTML = this.#objTime.getHours();

            //exception for 12-hour notation.
            if (this.#objTime.getHours() == 0)
                this.#objDivHoursgText.innerHTML = 12;
        }

        //G: hours: 24-hour format of an hour without leading zeros: 0 through 23
        if (this.#objDivHoursGText)
        {
            this.#objDivHoursGText.innerHTML = this.#objTime.getHours();
        }

        //h: hours: 12-hour format of an hour with leading zeros: 01 through 12
        if (this.#objDivHourshText)
        {
            if (this.#objTime.getHours() > 12)
                this.#objDivHourshText.innerHTML = (this.#objTime.getHours() - 12).toString().padStart(2, "0");
            else            
                this.#objDivHourshText.innerHTML = (this.#objTime.getHours()).toString().padStart(2, "0");

            //exception for 12-hour notation.
            if (this.#objTime.getHours() == 0)
                this.#objDivHourshText.innerHTML = 12;                
        }

        //H: hours: 24-hour format of an hour with leading zeros: 00 through 23
        if (this.#objDivHoursHText)
        {
            this.#objDivHoursHText.innerHTML = (this.#objTime.getHours()).toString().padStart(2, "0");
        }

        //i: minutes: Minutes with leading zeros: 00 to 59
        if (this.#objDivMinsText)
        {   
            this.#objDivMinsText.innerHTML = (this.#objTime.getMinutes()).toString().padStart(2, "0");
        }

        //i: seconds: Seconds with leading zeros: 00 through 59
        if (this.#objDivSecsText)
        {   
            this.#objDivSecsText.innerHTML = (this.#objTime.getSeconds()).toString().padStart(2, "0");
        }
    
    }

    /**
     * returns an array with value separators in this.#sPHPFormat (d-m-Y)
     * for d-m-Y the returning array would be: ["-", "-"]
     * for d!m-+()Y the returning array would be: ["!", "-+()"]
     * 
     * @returns array
     */
    #getValueSeparatorsPHPFormat()
    {
        const arrSpecialChars = ["d", "D", "j", "l", "N", "S", "w", "z", "W", "F", "m", "M", "n", "t", "L", "o", "X", "x", "Y", "y", "a", "A", "B", "g", "G", "h", "H", "i", "s", "u", "v", "e", "I", "O", "P", "p", "T", "Z", "c", "r", "U"]; //taken from https://www.php.net/manual/en/   atetime.format.php
        // const arrSpecialChars = ["h", "i"]; //quick for debugging purposes
        const arrValueSeparators = []; //these are the characters that are not recognized in the PHP-format string. For "d-m-Y" that would be: ["-", "-"]. This helps us navigate the actual date string, because these are value separators
        let sSeparator = ""; 
        let bRecognized = false;


        //first: analyze the PHP format string to create arrValueSeparators array
        for (let iPFIndex = 0; iPFIndex < this.#sPHPFormat.length; iPFIndex++) //PF=Php Format
        {
            bRecognized = false;

            //looking for characters we don't recognize
            for (let iSCIndex = 0; iSCIndex < arrSpecialChars.length; iSCIndex++) //SC= Special Characters
            {
                if (this.#sPHPFormat[iPFIndex] == arrSpecialChars[iSCIndex])
                {
                    bRecognized = true;
                    iSCIndex = arrSpecialChars.length; //stop loop
                }
            }

            //recognized
            if (bRecognized)
            {
                if (sSeparator != "") //only add when there is actually a separator
                {
                    arrValueSeparators.push(sSeparator);
                    sSeparator = "";      //reset separator          
                }
            }
            else //not recognized means: remember as separator
            {
                sSeparator += this.#sPHPFormat[iPFIndex];

                if (iPFIndex + 1 >= this.#sPHPFormat.length) //if this is the last time, we need to push the detected separator onto the array
                    arrValueSeparators.push(sSeparator);
            }
                
        }

        return arrValueSeparators;
    }

    /**
     * chops a string into parts based on value separators (created by getValueSeparatorsPHPFormat())
     * 
     * i.e. sInput = "d-m-Y" and arrValueSeparators["-", "-"]
     */
    #splitByValueSeparators(sInput, arrValueSeparators)
    {
        const arrResult = [];
        let iCharPtrStart = 0; //starting character pointer in sInput string
        let iCharPtrEnd = 0; //ending character pointer in sInput string
        let sPart = ""; //part in between separator
        let iIndexSep = 0;//index in separator array


        //loop chars input string
        while ((iCharPtrStart < sInput.length) && (iCharPtrEnd >= 0)) //quit loop when the auto-increment start-pointer is out, or there are no new separator occurences found
        {
            iCharPtrEnd = sInput.indexOf(arrValueSeparators[iIndexSep], iCharPtrStart);

            //FOUND
            if (iCharPtrEnd >= iCharPtrStart)
            {
                sPart = sInput.substring(iCharPtrStart, iCharPtrEnd);
                arrResult.push(sPart);
                iCharPtrStart = iCharPtrEnd + arrValueSeparators[iIndexSep].length;//continue next time searching at position of end-position
                iIndexSep++;
            }

            //NOT FOUND: we end the loop.
            //but we copy also last bit
            if (iCharPtrEnd < 0)
            {
                iCharPtrStart = iCharPtrStart;
                sPart = sInput.substring(iCharPtrStart, sInput.length);
                arrResult.push(sPart);
            }
        }


        return arrResult;
    }


    /**
    * set allow empty time
    */
    set allowemptytime(bValue)
    {
        this.#bAllowEmptyTime = bValue;
    }

    /**
    * set allow empty time
    */
    get allowemptytime()
    {
        return this.#bAllowEmptyTime;
    }

    /**
    * set show edit box
    */
    set showeditbox(bValue)
    {
        this.#bShowEditBox = bValue;
    }

    /**
    * set show edit box
    */
    get showeditbox()
    {
        return this.#bShowEditBox;
    }

    /**
    * set show clock
    */
    set showclock(bValue)
    {
        this.#bShowClock = bValue;
    }

    /**
    * set show clock
    */
    get showclock()
    {
        return this.#bShowClock;
    }

    /**
    * set value in ISO 8601 (YYYY-MM-DD)
    */
    set value(sValue)
    {
        if (sValue == "") //when empty it invalidates the time
            this.#objTime = null;
        else
            this.internalTimeAsISO8601 = sValue;
        
        this.updateUI();
    }

    /**
    * retrieve value in ISO 8601 (YYYY-MM-DD)
    */
    get value()
    {
        if (this.#objTime == null)
            return ""
        else
            return this.internalTimeAsISO8601;
    }

    /**
     * returns internal Date() object.
     * 
     * WARNING:
     * - when you change this date-object, use updateUI() to update the UI
     */
    get time()
    {
        return this.#objTime;
    }


    /**
    * set required
    */
    set required(bValue)
    {
        this.#bRequired = bValue;
    }

    /**
    * retrieve required
    */
    get required()
    {
        return this.#bRequired;
    }

    /**
    * set format
    */
    set phpformat(sFormat)
    {
        this.#sPHPFormat = sFormat;
    }

    /**
    * retrieve format
    */
    get phpformat()
    {
        return this.#sPHPFormat;
    }

    /**
     * make internal time empty (objTime = null)
     */
    emptyInternalTime()
    {
        this.#objTime = null;
        this.#objEditBox.value = "";
    }

    /**
     * create internal time object
     */
    createInternalTime()
    {
        this.#objTime = new Date();
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

        //allow AM/PM letters
        if (
            (objEvent.key == "a")
            ||
            (objEvent.key == "p")
            ||
            (objEvent.key == "m")
             )
        {
            return true;
        }

        if (objEvent.key == "ArrowUp")
        {
            if (this.#objTime !== null)
            {
                this.#objTime.setMinutes(this.#objTime.getMinutes() + 1);
                this.updateUI();
            }

            return true;
        }

        if (objEvent.key == "ArrowDown")
        {
            if (this.#objTime !== null)
            {
                this.#objTime.setMinutes(this.#objTime.getMinutes() - 1);
                this.updateUI();
            }
    
            return true;
        }

        if ((objEvent.key == "ArrowLeft") || (objEvent.key == "ArrowRight"))
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

        //allow value separators
        for (let iSepIndex = 0; iSepIndex < this.#arrCacheValueSeparators.length; iSepIndex++) 
        {
            for (let iCharIndex = 0; iCharIndex < this.#arrCacheValueSeparators[iSepIndex].length; iCharIndex++) //there can be multiple characters in one value separator (unlikely, but possible)
            {
                let iTemp = this.#arrCacheValueSeparators[iSepIndex][iCharIndex];
                if (objEvent.key == iTemp)
                {
                    return true;
                }
            }
        }

        return false;
    }



    /**
     * set time as H:i i.e. 15:21
     * 
     * @param {string} sISO8601FormattedDate 
     */
    set internalTimeAsISO8601(sISO8601FormattedDate)
    {
        // console.log("binktekring", sISO8601FormattedDate);
        if (!this.#objTime)
            this.#objTime = new Date();

        this.#objTime.setTime(Date.parse(sISO8601FormattedDate));
    }

    /**
     * get date as YYYY-MM-DD i.e. 2025-03-21
     * @param {string} sISO8601FormattedDate 
     */    
    get internalTimeAsISO8601()
    {
        if (this.#objTime)
        {
            return this.#objTime.toISOString();
        }
        else
            return "";
    }

    /**
     * 21-03-2025 (d-m-Y) 
     * @param {string} sPHPFormattedDate 
     */
    set internalTimeAsPHPTime(sPHPFormattedDate)
    {
        if (!this.#objTime)
            this.#objTime = new Date();   
        
        const arrValueSeparators = this.#getValueSeparatorsPHPFormat();

        //2 arrays have the same parts at the same index: example with d-m-Y: arrSpecialChars = [d,m,Y] and arrDateParts = [15,12,2006]
        const arrSpecialChars = this.#splitByValueSeparators(this.#sPHPFormat, arrValueSeparators); //search this.#sPHPFormat for value separators
        const arrDateParts = this.#splitByValueSeparators(sPHPFormattedDate, arrValueSeparators); //search sPHPFormattedDate for value separators
        
        //only apply AM/PM logic after everything is read, ONLY THEN we know how to interpret the hours, and apply exception for 12 AM
        let sAMPMValue = "";

        for (let iIndex = 0; iIndex < arrDateParts.length; iIndex++)
        {
            switch(arrSpecialChars[iIndex]) 
            {
                case "a": //AM/PM: Lowercase Ante meridiem and Post meridiem: am or pm
                case "A": //AM/PM: Uppercase Ante meridiem and Post meridiem: AM or PM 
                    sAMPMValue = arrDateParts[iIndex]; //apply logic later
                    break;
                case "g": //hours: 12-hour format of an hour without leading zeros: 1 through 12
                case "G": //hours: 24-hour format of an hour without leading zeros: 0 through 23
                case "h": //hours: 12-hour format of an hour with leading zeros: 01 through 12
                case "H": //hours: 24-hour format of an hour with leading zeros: 00 through 23
                    this.#objTime.setHours(this.#parseIntBetter(arrDateParts[iIndex], 0, 23));
                    break;
                case "i": //minutes: Minutes with leading zeros: 00 to 59
                    this.#objTime.setMinutes(this.#parseIntBetter(arrDateParts[iIndex], 0, 59));
                    break;
                case "s": //seconds: Seconds with leading zeros: 00 through 59
                    this.#objTime.setSeconds(this.#parseIntBetter(arrDateParts[iIndex], 0, 59));
                    break;
            }
        }

        //finally: apply AM/PM logic
        //we can't do it earlier because the AM/PM might proceed the hours
        //also we need to make an exception for 12 AM/PM
        if (sAMPMValue != "") //only when AM/PM is found
        {            

            //exception 12AM
            if (this.#objTime.getHours() == 12)
            {
                //12AM = 0:00h = midnight/'s-nachts, else 12PM = 12:00h = noon/'s-middags
                if (sAMPMValue.toLowerCase() == this.sTransAM.toLowerCase())
                    this.#objTime.setHours(0);
            }
            else
            {
                //PM means: add 12 hours
                if (sAMPMValue.toLowerCase() == this.sTransPM.toLowerCase())
                    this.#objTime.setHours(this.#objTime.getHours() + 12);
            }
        }
    }
 

    /**
     * dispatch when something has changed (but still in focus)
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventInputUpdated(objSource, sDescription)
{
        //probably something changed, thus update the form value
        this.#objFormInternals.setFormValue(this.internalTimeAsISO8601);
        this.#bDispatchChange = true;

        // console.log("dispatch event new methode ====================================")
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
     * dispatch when something has changed (out of focus)
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventChange(objSource, sDescription)
    {
        this.#bDispatchChange = false;
        // console.log("time: dispatch change");
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

    /**
     * a better integer parser than parseInt
     * 
     * @param {string} sShouldBeInt 
     * @param {int} iMinValue 
     * @param {int} iMaxValue 
     * @returns {int} 
     */
    #parseIntBetter(sShouldBeInt, iMinValue = 0, iMaxValue = 0)
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
     * set date as php time, like H:i:s meaning 23:08:12
     */    
    get internalTimeAsPHPTime()
    {
        let sResult = this.#sPHPFormat;
        
        if (this.#objTime === null)
            return "";

        if (this.#objTime.getHours() > 12) //PM
        {
            sResult = sResult.replaceAll("a", this.sTransPM.toLowerCase());
            sResult = sResult.replaceAll("A", this.sTransPM.toUpperCase());
            sResult = sResult.replaceAll("g", (this.#objTime.getHours() - 12).toString());
            sResult = sResult.replaceAll("h", (this.#objTime.getHours() - 12).toString().padStart(2, "0"));
        }
        else //AM
        {
            if (this.#objTime.getHours() == 12) //exception for 12
            {
                sResult = sResult.replaceAll("a", this.sTransPM.toLowerCase()); //convert to PM
                sResult = sResult.replaceAll("A", this.sTransPM.toUpperCase()); //convert to PM
            }
            else
            {
                sResult = sResult.replaceAll("a", this.sTransAM.toLowerCase());
                sResult = sResult.replaceAll("A", this.sTransAM.toUpperCase());
            }
            
            if (this.#objTime.getHours() == 0) //Exception for 12 hour notation on hour 0
            {
                sResult = sResult.replaceAll("g", 12);
                sResult = sResult.replaceAll("h", 12);
            }
            else
            {
                sResult = sResult.replaceAll("g", (this.#objTime.getHours()).toString());
                sResult = sResult.replaceAll("h", (this.#objTime.getHours()).toString().padStart(2, "0"));
            }
        }

        sResult = sResult.replaceAll("G", (this.#objTime.getHours()).toString());
        sResult = sResult.replaceAll("H", (this.#objTime.getHours()).toString().padStart(2, "0"));
        sResult = sResult.replaceAll("i", (this.#objTime.getMinutes()).toString().padStart(2, "0"));
        sResult = sResult.replaceAll("s", (this.#objTime.getSeconds()).toString().padStart(2, "0"));

        return sResult;
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

    static get observedAttributes() 
    {
        return ["value", "required", "phpformat", "allowemptydate", "showeditbox", "showcalendar", "disabled"];
    }
  

    attributeChangedCallback(sAttrName, sPreviousVal, sNextVal) 
    {
        switch(sAttrName)
        {
            case "disabled":
                this.disabled = sNextVal;
                break;
            case "value":
                this.value = sNextVal;
                break;
        }

        if (this.#bConnectedCallbackHappened)
            this.updateUI();
    }

    /**
     * removes ALL eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
        this.#objAbortControllerClock.abort();
    }

    /**
     * removes ONLY eventlisteners of clock
     */
    resetEventListenersClock()
    {
        this.#objAbortControllerClock.abort();
        this.#objAbortControllerClock = new AbortController();
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

            //build cache
            this.buildCache();            

            //render
            this.populate();
        }


        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();


        //event
        this.addEventListeners();
        this.addEventListenersClock();    


        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //show
            this.updateUI();

            //update form value
            this.#objFormInternals.setFormValue(this.internalTimeAsISO8601); //default checkbox is unchecked, so we need to explicitly set formvalue to be unchecked value
        }


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
customElements.define("dr-input-time", DRInputTime);