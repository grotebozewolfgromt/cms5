<?php 
/**
 * dr-input-datetime.js
 *
 * combines <dr-input-date> and <dr-input-time> into one component.
 * With one date you set and get a datetime timestamp
 * 
 * FIRES EVENT: 
 * - "update" when anything changes in an editbox (on each keypress for example)
 * - "change" when user leaves a editbox and is changed
 * 
 * 
 * 
 * DEPENDENCIES:
 * -dr-input-date
 * -dr-input-time
 * -dr-popover
 * 
 * 
 * EXAMPLE:
 * <dr-input-datetime id="moppiedate" allowemptydatetime></dr-input-datetime>
 * <dr-input-datetime id="moppiedate" name="formdatetime" value="2025-03-21 14:22:45"></dr-input-datetime> <!-- only accepts ISO 8601 date format -->
 * <dr-input-datetime id="moppiedate" name="formdatetime" phpdateformat="d-m-Y" phptimeformat="h:i a"></dr-input-datetime> <!-- phpformat first date then space, then time -->
 * 
 * 
 * @author Dennis Renirie
 * 
 * 25 mrt 2025 dr-input-datetime.js created
 * 5 apr 2025 dr-input-datetime.js aparte bij rerenderen van klok nu ook event listeners gereset
 * 26 sept 2025 dr-input-datetime.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 */
?>


class DRInputDateTime extends HTMLElement
{

    static sTemplate = `
        <style>
            :host
            {
                display: inline-block;
                min-width: 150px;
            }
            
            .contentdivider
            {
                display: grid;
                grid-template-columns: 1fr 1fr;
            }
        </style>
        <div class="contentdivider">
            <dr-input-date></dr-input-date><dr-input-time></dr-input-time>
        </div>
    `;

    #objFormInternals = null;
    #arrAbortController = []; //array with abort controllers for filter chips. Each item in array is a AbortController object for a chip
    #objDateTime = null;//internal Date() object with date and timestamp of both input boxes
    #objInputDate = null;//<dr-input-date> object
    #objInputTime = null;//<dr-input-time> object
    #bAllowEmptyDateTime = false;
    #sPHPDateFormat = "Y-m-d";
    #sPHPTimeFormat = "H:i";
    #bRequired = false;
    #iFirstDayOfWeek = 0;
    // #sTimeZone = "Europe/Amsterdam"; --> in internal ISO
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

        this.attachShadow({mode: "open"});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputDateTime.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
    }    

    /**
     * populate UI
     */
    #populate()
    {
        this.#objInputDate = this.shadowRoot.querySelector("dr-input-date");
        this.#objInputTime = this.shadowRoot.querySelector("dr-input-time");

        //set form value
        if (this.#objDateTime)
            this.#objFormInternals.setFormValue(this.#objDateTime.toISOString());
    }

    /**
     * updates UI of sub components
     * 
     */    
    updateUI()
    {
        // this.#objInputDate.getDate().setTime(this.#objDateTime);
        this.#objInputDate.updateUI();

        // this.#objInputTime.getDate().setTime(this.#objDateTime);
        this.#objInputTime.updateUI();
    }

    #readAttributes()
    {
        this.required = DRComponentsLib.attributeToBoolean(this, "required", this.#bRequired);
        this.phpdateformat = DRComponentsLib.attributeToString(this, "phpdateformat", this.#sPHPDateFormat);     
        this.phptimeformat = DRComponentsLib.attributeToString(this, "phptimeformat", this.#sPHPTimeFormat);     
        this.phptimeformat = DRComponentsLib.attributeToString(this, "phptimeformat", this.#sPHPTimeFormat);     
        this.firstdayofweek = DRComponentsLib.attributeToInt(this, "firstdayofweek", this.#iFirstDayOfWeek);
        this.allowemptydatetime = DRComponentsLib.attributeToBoolean(this, "allowemptydatetime", this.#bAllowEmptyDateTime);
        // this.timezone = DRComponentsLib.attributeToInt(this, "timezone", this.#sTimeZone);

        if (!this.#bAllowEmptyDateTime)
            this.#objDateTime = new Date();  

        if (this.getAttribute("value") !== null)
        {
            if (this.getAttribute("value") == "")
            {
                this.#objDateTime = null;
                this.#objFormInternals.setFormValue("");
            }
            else
            {
                this.internalDateTimeAsISO8601 = this.getAttribute("value");        
            }
        }
    }    


    /**
    * set allow empty date time
    */
    set allowemptydatetime(bValue)
    {
        this.#bAllowEmptyDateTime = bValue;

    }

    /**
    * get allow empty date time
    */
    get allowemptydatetime()
    {
        return this.bAllowEmptyDateTime;
    }

    /**
    * set value in ISO 8601 (YYYY-MM-DD)
    */
    set value(sValue)
    {
        this.internalDateTimeAsISO8601 = sValue;
        this.updateUI();
    }

    /**
    * retrieve value in ISO 8601 (YYYY-MM-DD)
    */
    get value()
    {
        return this.internalDateTimeAsISO8601;
    }

    /**
     * returns internal Date() object.
     * 
     * WARNING:
     * - when you change this date-object, use updateUI() to update the UI
     */
    get datetime()
    {
        return this.#objDateTime;
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
    set phpdateformat(sFormat)
    {
        this.#sPHPDateFormat = sFormat;
    }

    /**
    * retrieve format
    */
    get phpdateformat()
    {
        return this.#sPHPDateFormat;
    }    

    /**
    * set format
    */
    set phptimeformat(sFormat)
    {
        this.#sPHPTimeFormat = sFormat;
    }

    /**
    * retrieve format
    */
    get phptimeformat()
    {
        return this.#sPHPTimeFormat;
    }    

    /**
    * set first day of the week.
    * 0=sunday, 1=monday
    */
    set firstdayofweek(iFirstDay)
    {
        this.#iFirstDayOfWeek = iFirstDay;
    }

    /**
    * get first day of the week.
    * 0=sunday, 1=monday
    */
    get firstdayofweek()
    {
        return this.#iFirstDayOfWeek;
    }    

    // /**
    // * set timezone
    // */
    // set timezone(sTimeZone)
    // {
    //     this.#sTimeZone = sTimeZone;
    // }

    // /**
    //  * get timezone
    // */
    // get timezone()
    // {
    //     return this.#sTimeZone;
    // }    



    /**
     * set time as H:i i.e. 15:21
     * 
     * @param {string} sISO8601FormattedDateTime 
     */
    set internalDateTimeAsISO8601(sISO8601FormattedDateTime)
    {
        if (!this.#objDateTime)
            this.#objDateTime = new Date();
    
        this.#objDateTime.setTime(Date.parse(sISO8601FormattedDateTime));
        this.#objFormInternals.setFormValue(this.#objDateTime.toISOString());
    }

    /**
     * get date as YYYY-MM-DDTHH:MM:SS i.e. 2025-03-21T12:23:01
     * @param {string} sISO8601FormattedDateTime 
     */    
    get internalDateTimeAsISO8601()
    {
        if (this.#objDateTime)
        {
            this.#setInternalDateTimeFromComponents();

            return this.#objDateTime.toISOString();
        }
        else
            return "";
    }
    
    /**
     * pushes internal data to <dr-input-date> and <dr-input-time> components
     */
    #pushInternalDataToComponents()
    {
        if (this.#bAllowEmptyDateTime)
        {
            this.#objInputDate.emptyInternalDate();
            this.#objInputTime.emptyInternalTime();
        }

        if (this.#objDateTime)
        {
            //push date and time value
            if (this.#objInputDate.date == null)
                this.#objInputDate.createInternalDate();    
            this.#objInputDate.date.setTime(this.#objDateTime.getTime());

            if (this.#objInputTime.time == null)
                this.#objInputTime.createInternalTime();    
            this.#objInputTime.time.setTime(this.#objDateTime.getTime());
        }
        
        //push php format
        // const arrDateTimeFormat = this.#sPHPFormat.split(" "); //date and time split by a space
        // for (let iIndex = 0; iIndex < arrDateTimeFormat.length; iIndex++)
        // {
        //     if (iIndex == 0) //first part is assumed date
        //         this.#objInputDate.phpformat = arrDateTimeFormat[0];
        //     else if (iIndex == 1)
        //         this.#objInputTime.phpformat = arrDateTimeFormat[1];
        //     else if (iIndex > 1)
        //         this.#objInputTime.phpformat += " " + arrDateTimeFormat[iIndex]; //add space back in (can happen with AM/PM)
        // }

        this.#objInputDate.firstdayofweek = this.#iFirstDayOfWeek;
        this.#objInputDate.allowemptydate = this.#bAllowEmptyDateTime;
        this.#objInputDate.phpformat = this.#sPHPDateFormat;
        this.#objInputTime.allowemptytime = this.#bAllowEmptyDateTime
        this.#objInputTime.phpformat = this.#sPHPTimeFormat;

        //push translations from attributes to DATE
        this.#objInputDate.sTransDay      = DRComponentsLib.attributeToString(this, "transday", this.#objInputDate.sTransDay);
        this.#objInputDate.sTransMonth    = DRComponentsLib.attributeToString(this, "transmonth", this.#objInputDate.sTransMonth);
        this.#objInputDate.sTransYear     = DRComponentsLib.attributeToString(this, "transyear", this.#objInputDate.sTransYear);
        this.#objInputDate.sTransJanuary  = DRComponentsLib.attributeToString(this, "transjanuary", this.#objInputDate.sTransJanuary);
        this.#objInputDate.sTransFebruary = DRComponentsLib.attributeToString(this, "transfebruary", this.#objInputDate.sTransFebruary);
        this.#objInputDate.sTransMarch    = DRComponentsLib.attributeToString(this, "transmarch", this.#objInputDate.sTransMarch);
        this.#objInputDate.sTransApril    = DRComponentsLib.attributeToString(this, "transapril", this.#objInputDate.sTransApril);
        this.#objInputDate.sTransMay      = DRComponentsLib.attributeToString(this, "transmay", this.#objInputDate.sTransMay);
        this.#objInputDate.sTransJune     = DRComponentsLib.attributeToString(this, "transjune", this.#objInputDate.sTransJune);
        this.#objInputDate.sTransJuly     = DRComponentsLib.attributeToString(this, "transjuly", this.#objInputDate.sTransJuly);
        this.#objInputDate.sTransAugust   = DRComponentsLib.attributeToString(this, "transaugust", this.#objInputDate.sTransAugust);
        this.#objInputDate.sTransSeptember = DRComponentsLib.attributeToString(this, "transseptember", this.#objInputDate.sTransSeptember);
        this.#objInputDate.sTransOctober  = DRComponentsLib.attributeToString(this, "transoctober", this.#objInputDate.sTransOctober);
        this.#objInputDate.sTransNovember = DRComponentsLib.attributeToString(this, "transnovember", this.#objInputDate.sTransNovember);
        this.#objInputDate.sTransDecember = DRComponentsLib.attributeToString(this, "transdecember", this.#objInputDate.sTransDecember);
        this.#objInputDate.sTransMonday   = DRComponentsLib.attributeToString(this, "transmonday", this.#objInputDate.sTransMonday);
        this.#objInputDate.sTransTuesday  = DRComponentsLib.attributeToString(this, "transtuesday", this.#objInputDate.sTransTuesday);
        this.#objInputDate.sTransWednesday = DRComponentsLib.attributeToString(this, "transwednesday", this.#objInputDate.sTransWednesday);
        this.#objInputDate.sTransThursday = DRComponentsLib.attributeToString(this, "transthursday", this.#objInputDate.sTransThursday);
        this.#objInputDate.sTransFriday   = DRComponentsLib.attributeToString(this, "transfriday", this.#objInputDate.sTransFriday);
        this.#objInputDate.sTransSaturday = DRComponentsLib.attributeToString(this, "transsaturday", this.#objInputDate.sTransSaturday);
        this.#objInputDate.sTransSunday   = DRComponentsLib.attributeToString(this, "transsunday", this.#objInputDate.sTransSunday);
        this.#objInputDate.sTransYesterday = DRComponentsLib.attributeToString(this, "transyesterday", this.#objInputDate.sTransYesterday);
        this.#objInputDate.sTransToday    = DRComponentsLib.attributeToString(this, "transtoday", this.#objInputDate.sTransToday);
        this.#objInputDate.sTransTomorrow = DRComponentsLib.attributeToString(this, "transtomorrow", this.#objInputDate.sTransTomorrow); 
        
        //push translations from attributes to TIME
        this.#objInputTime.sTransAM       = DRComponentsLib.attributeToString(this, "transam", this.#objInputTime.sTransAM);  
        this.#objInputTime.sTransPM       = DRComponentsLib.attributeToString(this, "transpm", this.#objInputTime.sTransAM); 
        this.#objInputTime.sTransAMPM     = DRComponentsLib.attributeToString(this, "transampm", this.#objInputTime.sTransAMPM); 
        this.#objInputTime.sTransHours    = DRComponentsLib.attributeToString(this, "transhours", this.#objInputTime.sTransHours); 
        this.#objInputTime.sTransMinutes  = DRComponentsLib.attributeToString(this, "transminutes", this.#objInputTime.sTransMinutes); 
        this.#objInputTime.sTransSeconds  = DRComponentsLib.attributeToString(this, "transseconds", this.#objInputTime.sTransSeconds); 
        this.#objInputTime.sTransNow      = DRComponentsLib.attributeToString(this, "transnow", this.#objInputTime.sTransNow); 

        //rerender UI elements based on data pushed
        this.#objInputDate.buildCache();
        this.#objInputDate.renderCalendar();

        this.#objInputTime.buildCache();
        this.#objInputTime.resetEventListenersClock();
        this.#objInputTime.renderClock(); //the clock needs to be rerendered, because the phpformat can be changed by this class
        this.#objInputTime.addEventListenersClock(); //the clock needs to be rerendered, because the phpformat can be changed by this class
    }

    /**
     * sets internal objDateTime by retrieving values from <dr-input-date> and <dr-input-time> components
     */
    #setInternalDateTimeFromComponents()
    {
        // console.log("setInternalDateTimeFromComponents", this.#bAllowEmptyDateTime, this.#objInputDate.value, this.#objInputTime.value, this)
        if (!this.#bAllowEmptyDateTime)
        {
            if (this.#objDateTime === null)
                this.#objDateTime = new Date();
        }
        else
        {

            //when date is empty, it COMPLETELY invalidates the internal date (and thus time)
            if ((this.#objInputDate.value == "") || (this.#objInputTime.value == ""))
            {
                this.#objDateTime = null;

                //can't type partial dates anymore when enabling below:
                // if (this.#objInputDate.value == "")
                //     this.#objInputTime.value = "";
                // else if (this.#objInputTime.value == "")
                //     this.#objInputDate.value = "";
            }
            else //also create date when both boxes have dates
            {
                if (this.#objDateTime === null)
                    this.#objDateTime = new Date();
            }

        }

        //only when date exists, update it
        if (this.#objDateTime)
        { 
            //date
            if (this.#objInputDate.date === null)
            {
                this.#objInputDate.createInternalDate();
                this.#objInputDate.updateUI();
            }

            this.#objDateTime.setFullYear(this.#objInputDate.date.getFullYear());
            this.#objDateTime.setMonth(this.#objInputDate.date.getMonth());
            this.#objDateTime.setDate(this.#objInputDate.date.getDate());

            //time
            if (this.#objInputTime.time === null)
            {
                this.#objInputTime.createInternalTime();
                this.#objInputTime.updateUI();
            }


            this.#objDateTime.setHours(this.#objInputTime.time.getHours());
            this.#objDateTime.setMinutes(this.#objInputTime.time.getMinutes());
            this.#objDateTime.setSeconds(this.#objInputTime.time.getSeconds());
            this.#objDateTime.setMilliseconds(this.#objInputTime.time.getMilliseconds());
        }

    }

    
    /**
     * add event listeners to component
     */
    addEventListeners()
    {

        // debugger
        this.#objInputDate.addEventListener("update", (objEvent) => 
        {
            this.#setInternalDateTimeFromComponents();
            this.#dispatchEventInputUpdated(this.#objInputDate, "date changed");

        }, { signal: this.#arrAbortController.signal });  

        this.#objInputTime.addEventListener("update", (objEvent) => 
        {
            this.#setInternalDateTimeFromComponents();
            this.#dispatchEventInputUpdated(this.#objInputDate, "time changed");          
        }, { signal: this.#arrAbortController.signal });  

        //CHANGE event: editboxes
        this.#objInputDate.addEventListener("change", (objEvent)=>
        {  
            this.dispatchEvent(new CustomEvent("change",
            {
                bubbles: true,
                detail:
                {
                    source: this
                }
            }));
        }, { signal: this.#arrAbortController.signal });          
        this.#objInputTime.addEventListener("change", (objEvent)=>
        {  
            this.dispatchEvent(new CustomEvent("change",
            {
                bubbles: true,
                detail:
                {
                    source: this
                }
            }));
        }, { signal: this.#arrAbortController.signal });          
    }


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        //remove all on parent abort controller
        this.objAbortController.abort();
    }

    /**
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventInputUpdated(objSource, sDescription)
    {
        //probably something changed, thus update the form value
        this.#objFormInternals.setFormValue(this.internalDateTimeAsISO8601);

        //console.log("dispatch event", this.#objDateTime);
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

    // static get observedAttributes() 
    // {
    //     return ["disabled", "value"];
    // }

    // attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    // {
    //     console.log("attribute changed in combobox", sAttrName, sNewVal);
    //     switch(sAttrName)
    //     {
    //         case "disabled":
    //             this.disabled = sNewVal;
    //             if (this.#bConnectedCallbackHappened)
    //                 this.updateUI();
    //             break;
    //         case "value":
    //             this.value = sNewVal;
    //             if (this.#bConnectedCallbackHappened)
    //                 this.updateUI();
    //             break;
    //     }
    // }   

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
            this.#populate();

            //update
            this.#pushInternalDataToComponents();
            this.updateUI();
            
            //update form value
            this.#objFormInternals.setFormValue(this.internalDateTimeAsISO8601); //default checkbox is unchecked, so we need to explicitly set formvalue to be unchecked value
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
customElements.define("dr-input-datetime", DRInputDateTime);