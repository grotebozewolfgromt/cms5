<?php 
/**
 * dr-input-date.js
 *
 * class for a date picker
 * 
 * This class inputs and outputs dates in ISO-8601 format (YYYY-MM-DD), see https://en.wikipedia.org/wiki/ISO_8601
 * This way programmatically setting and getting values from this class is always the same!
 * This prevents programming errors.
 * However, the UI gives the user custom formatted inputs with the "phpdate" attribute.
 * In other words: even if the user inputs "25-03-2025" (with phpformat="d-m-Y"), the retrieved value will be: "2025-03-25" (ISO 8601)
 * 
 * 
 * HTML ATTRIBUTES:
 * - <dr-input-date value="123">            ===> value in ISO-8601 format
 * - <dr-input-date required="true">
 * - <dr-input-date phpformat="d-m-Y">      ===> user inputs their dates in this format in UI
 * - <dr-input-date allowemptydate="true">
 * - <dr-input-date showeditbox="true">
 * - <dr-input-date showcalendar="true">
 * - <dr-input-date transday="DD">       ==> placeholder for day field
 * - <dr-input-date transmonth="MM">     ==> placeholder for month field
 * - <dr-input-date transyear="YYYY">    ==> placeholder for year field
 * 
 * FIRES EVENT: 
 * - "update" when anything changes in editbox (on each keypress for example)
 * - "change" when user leaves editbox and is changed
 * 
    objDatumpje.addEventListener('changed', (e) => 
    { 
        console.log("keyupevent ondervangen. Value datum: ", objDatumpje.value, e);
    });   
 * 
 *  
 * 
 * @author Dennis Renirie
 * 
 * @todo scrollwheel up-down for months
 * 
 * 
 * 15 mrt 2025 dr-input-date.js created
 * 15 mrt 2025 dr-input-date.js scrollwheel scrollt maanden in kalender
 * 26 sept 2025 dr-input-date.js: BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 13 okt 2025 dr-input-date.js: ADD: focusout triggert een 'change'-event wanneer waarde gewijzigd
 */
?>


class DRInputDate extends HTMLElement
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
            
            .calendaricon
            {
                display: inline;
                cursor: pointer;
                
                
                margin: 0px;
                padding: 2px;
                padding-top: 5px;
                
                box-sizing: content-box;
            }


            .drinputdatecalendarhead
            {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr 1fr;
                gap: 5px;
            }
            
            .drinputdatecalendartable
            {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr 1fr 1fr 1fr 1fr;
                gap: 3px;
                margin: 0px;
                margin-top: 8px;
                margin-bottom: 8px;
                user-select: none; 
            }

            .drinputdatecalendartable .header
            {
                align-self: center;
                text-align: center;
                justify-self: center;
            }
            
            .drinputdatecalendartable .day
            {
                height: 20px;
                width: 20px;
                align-self: center;
                text-align: center;
                justify-self: center;      
                padding: 5px;       
                padding-top: 8px;       
                /*line-height: 10px;*/
                font-size: 11px;  
                border-radius: 20px;                 
            }

            .drinputdatecalendartable .currentmonth
            {
                background-color: light-dark(var(--lightmode-color-drinputdate-calendar-daycurrentmonth-background, rgb(236, 236, 236)), var(--darkmode-color-drinputdate-calendar-daycurrentmonth-background, rgb(107, 107, 107)));
                cursor: pointer;
            }

            .drinputdatecalendartable .currentday
            {
                background-color: light-dark(var(--lightmode-color-drinputdate-calendar-daycurrentday-background, rgb(1, 63, 180)), var(--darkmode-color-drinputdate-calendar-daycurrentday-background, rgb(255, 255, 255)));
                color: light-dark(var(--lightmode-color-drinputdate-calendar-daycurrentmonth-background, rgb(239, 239, 239)), var(--darkmode-color-drinputdate-calendar-daycurrentmonth-background, rgb(107, 107, 107)));
            }

            .drinputdatecalendartable .saturday,
            .drinputdatecalendartable .sunday
            {
                background-color: light-dark(var(--lightmode-color-drinputdate-calendar-daysaturdaysunday-background, rgba(236, 236, 236, 0.48)), var(--darkmode-color-drinputdate-calendar-daysaturdaysunday-background, rgba(97, 97, 97, 0.41)));
            }

            .drinputdatecalendartable .othermonth
            {
                color: light-dark(var(--lightmode-color-drinputdate-calendar-dayothermonth, rgb(212, 212, 212)), var(--darkmode-color-drinputdate-calendar-dayothermonth, rgb(107, 107, 107)));
            }            
 
            .drinputdatecalendarfooter
            {
                 /* background-color: purple;*/
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 5px;
            }

        </style>


        <div class="contentdivider">
            <input type="text">
        
            <div class="calendaricon">
                <!-- SVG icon inserted here -->
            </div>
        </div>
    

        <dr-popover>
            <div class="drinputdatecalendarhead">
                <button class="drinputdatecalendarpreviousmonth">
                    <svg fill="currentColor" style="transform: rotate(180deg)" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                </button>
                <select class="drinputdatecalendarmonths">
                    <!-- auto generated by language text -->
                    <!--
                    <option value="1">Jan</option>
                    <option value="2">Feb</option>
                    <option value="3">Mar</option>
                    <option value="4">Apr</option>
                    <option value="5">May</option>
                    <option value="6">Jun</option>
                    <option value="7">Jul</option>
                    <option value="8">Aug</option>
                    <option value="9">Sep</option>
                    <option value="10">Oct</option>
                    <option value="11">Nov</option>
                    <option value="12">Dec</option>
                    -->
                </select>
                <select class="drinputdatecalendaryears">
                    <option value="2025">2025</option> <!-- auto generated -->
                </select>                
                <button class="drinputdatecalendarnextmonth">
                    <svg fill="currentColor" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>
                </button>
            </div>
            <div class="drinputdatecalendartable">
                <!-- auto generated -->
            </div>
            <div class="drinputdatecalendarfooter">
                <button class="drinputdatecalendaryesterday"><!-- auto generated by language text --></button>
                <button class="drinputdatecalendartoday"><!-- auto generated by language text --></button>
                <button class="drinputdatecalendartomorrow"><!-- auto generated by language text --></button>
            </div>
        </dr-popover>
    `;

    #objEditBox = null; //the internal <input type="text">
    #objDivCalendarIcon = null;//<div> with <svg> calendaricon 
    #objDRBubble = null; //bubble with calendar
    #objDate = null; //the date that is in the edit box. date = null means: invalid date/empty date. This is allowed when bAllowEmptyDate == true
    #objDateCalendar = null; //this date is the date that the calendar is currently representing. (users can browse through months without selecting them)
    #objDivCalendarTable = null;
    #bRequired = false;
    #sPHPFormat = "Y-m-d"; //default assuming the ISO 8601 notation for PHP format (year-month-day). meaning of letters: in https://www.php.net/manual/en/datetime.format.php
    #bAllowEmptyDate = false;
    #bShowEditBox = true;
    #bShowCalendar = true;
    #objFormInternals = null;
    #objAbortController = null;
    #objAbortControllerCalendarDays = null;
    #bDisabled = false;
    #bConnectedCallbackHappened = false;    
    #bDispatchChange = false;

    sTransDay = "dd";
    sTransMonth = "mm";
    sTransYear = "yyyy";
    sTransJanuary = "Jan";
    sTransFebruary = "Feb";
    sTransMarch = "Mar";
    sTransApril = "Apr";
    sTransMay = "May";
    sTransJune = "Jun";
    sTransJuly = "Jul";
    sTransAugust = "Aug";
    sTransSeptember = "Sep";
    sTransOctober = "Oct";
    sTransNovember = "Nov";
    sTransDecember = "Dec";
    sTransMonday = "Mon";
    sTransTuesday = "Tue";
    sTransWednesday = "Wed";
    sTransThursday = "Thu";
    sTransFriday = "Fri";
    sTransSaturday = "Sat";
    sTransSunday = "Sun";
    sTransYesterday = "Yesterday";
    sTransToday = "Today";
    sTransTomorrow = "Tomorrow";

    // sSVGCalendar = '<svg class="changeiconcolor" viewBox="0 -100 448 612" xmlns="http://www.w3.org/2000/svg"><path d="M148 288h-40c-6.6 0-12-5.4-12-12v-40c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12zm108-12v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 96v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm-96 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm192 0v-40c0-6.6-5.4-12-12-12h-40c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12zm96-260v352c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V112c0-26.5 21.5-48 48-48h48V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h128V12c0-6.6 5.4-12 12-12h40c6.6 0 12 5.4 12 12v52h48c26.5 0 48 21.5 48 48zm-48 346V160H48v298c0 3.3 2.7 6 6 6h340c3.3 0 6-2.7 6-6z"/></svg>';
    sSVGCalendar = '<svg viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path fill="currentColor" d="M20,8 L20,5 L18,5 L18,6 L16,6 L16,5 L8,5 L8,6 L6,6 L6,5 L4,5 L4,8 L20,8 Z M20,10 L4,10 L4,20 L20,20 L20,10 Z M18,3 L20,3 C21.1045695,3 22,3.8954305 22,5 L22,20 C22,21.1045695 21.1045695,22 20,22 L4,22 C2.8954305,22 2,21.1045695 2,20 L2,5 C2,3.8954305 2.8954305,3 4,3 L6,3 L6,2 L8,2 L8,3 L16,3 L16,2 L18,2 L18,3 Z M9,14 L7,14 L7,12 L9,12 L9,14 Z M13,14 L11,14 L11,12 L13,12 L13,14 Z M17,14 L15,14 L15,12 L17,12 L17,14 Z M9,18 L7,18 L7,16 L9,16 L9,18 Z M13,18 L11,18 L11,16 L13,16 L13,18 Z" fill-rule="evenodd"/></svg>';

    #iFirstDayOfWeek = 0;//0=sunday, 1=monday
    #arrDaysOfWeek = []; //all the translations of days above in array. the index is the day of the week, depending on the first day of the week set by #iFirstDayOfWeek. So: #arrDaysOfWeek[0] = "sunday" when #iFirstDayOfWeek = 0. #arrDaysOfWeek[0] = "monday" when #iFirstDayOfWeek = 1

    #sCachePlaceholder = "";  
    #sCacheMaxLength = 10;  //12-04-2006 (=10 characters)
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
        this.#objAbortControllerCalendarDays = new AbortController();
        this.#objDateCalendar = new Date();

        this.attachShadow({mode: "open", delegatesFocus: true});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputDate.sTemplate;

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);        

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
                case "d": //day
                    this.#sCachePlaceholder += this.sTransDay;
                    this.#sCacheMaxLength += 2;
                    break;
                case "j": //day
                    this.#sCachePlaceholder += this.sTransDay;
                    this.#sCacheMaxLength += 2;
                    break;
                case "m": //month
                    this.#sCachePlaceholder += this.sTransMonth;
                    this.#sCacheMaxLength += 2;
                    break;
                case "n": //month
                    this.#sCachePlaceholder += this.sTransMonth;
                    this.#sCacheMaxLength += 2;
                    break;
                case "Y": //year
                    this.#sCachePlaceholder += this.sTransYear;
                    this.#sCacheMaxLength += 4;
                    break;
                case "y": //year
                    this.#sCachePlaceholder += this.sTransYear;
                    this.#sCacheMaxLength += 4;
                    break;
                default:
                    this.#sCachePlaceholder += this.#sPHPFormat[iIndex];            
                    this.#sCacheMaxLength += 1;
            }    
        }

        //value separators
        this.#arrCacheValueSeparators = this.#getValueSeparatorsPHPFormat();

        //create days of week array (indexes are possibly shifted later)
        this.#arrDaysOfWeek[0] = this.sTransSunday;
        this.#arrDaysOfWeek[1] = this.sTransMonday;
        this.#arrDaysOfWeek[2] = this.sTransTuesday;
        this.#arrDaysOfWeek[3] = this.sTransWednesday;
        this.#arrDaysOfWeek[4] = this.sTransThursday;
        this.#arrDaysOfWeek[5] = this.sTransFriday;
        this.#arrDaysOfWeek[6] = this.sTransSaturday;

        //shift indexes array according to this.#iFirstDayOfWeek. if iFirstDayOfWeek == 1 the array indexes are shifted once, which makes monday the first day of the week
        let sTempTrans = "";
        for (let iShifts = 0; iShifts < this.#iFirstDayOfWeek; iShifts++)
        {
            sTempTrans = this.#arrDaysOfWeek.shift();
            this.#arrDaysOfWeek.push(sTempTrans);
        }
    }        

    populate()
    {
        //adding calendar aicon
        if ((this.#bShowEditBox) && (this.#bShowCalendar))
        {
            this.#objDivCalendarIcon = this.shadowRoot.querySelector(".calendaricon");
            this.#objDivCalendarIcon.innerHTML += this.sSVGCalendar;
        }

        //editbox
        if (this.#bShowEditBox)
        {
            this.#objEditBox = this.shadowRoot.querySelector("input");
        }

        //bubble with calendar
        if (this.#bShowCalendar)
        {
            this.#objDRBubble = this.shadowRoot.querySelector("dr-popover");
            this.#objDRBubble.anchorobject = this;
            this.#objDRBubble.anchorpos = this.#objDRBubble.iPosTop;
            this.#objDRBubble.hideonclick = false;

            this.renderCalendar();
        }

        //set form value
        this.#objFormInternals.setFormValue(this.internalDateAsISO8601);
    }

    /**
     * when changes happen from outside, we need to update the UI
     * 
     */
    updateUI()
    {
        //editbox
        if (this.#bShowEditBox)
        {
            this.#objEditBox.placeholder = this.#sCachePlaceholder; //is done here, so we can call updateUI() to update placeholder
            this.#objEditBox.maxLength = this.#sCacheMaxLength;  //is done here, so we can call updateUI() to update maxlength

            if (this.#objDate)             
                this.#objEditBox.value = this.internalDateAsPHPDate;
            else
                this.#objEditBox.value = "";
        }

        //calendar
        if ((this.#bShowCalendar) && (this.#objDRBubble))
        {

            if (this.#objDate !== null)
            {
                this.#updateUICalendar();
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
     * updates only the calendar UI
     * 
     */
    #updateUICalendar()
    {
        if (this.#objDRBubble)
        {
            this.#renderCalendarYears();
            this.#renderCalendarMonths();
            this.#renderCalendarTable();
                // this.#renderCalendarFooter();
        }
    }

    

    /**
     * create calendar table of a certain month
     * 
     */
    renderCalendar()
    {
        if (this.#objDRBubble)
        {
            this.#renderCalendarYears();
            this.#renderCalendarMonths(true);
            this.#renderCalendarTable(true);
            this.#renderCalendarFooter(true);
        }
        
    }

    #renderCalendarYears()
    {        
        let objOption = null;  

        //generate years in combobox
        const objSelectYears = this.#objDRBubble.querySelector(".drinputdatecalendaryears");
        if (objSelectYears.childElementCount < 2) //generate only new years when <select> has no <option>'s
        {
            const iStartYear = this.#objDateCalendar.getFullYear() - 100;
            const iEndYear = this.#objDateCalendar.getFullYear() + 50;
            // const iCurrentYear = this.#objDate.getFullYear();
            objSelectYears.innerHTML = "";

            for (let iYears = iStartYear; iYears < iEndYear; iYears++)
            {                    
                objOption = document.createElement("option");
                objOption.value = iYears;
                objOption.textContent = iYears;
                objSelectYears.appendChild(objOption);
            }
        }

        //select current year in combobox
        let objOptionsYears = objSelectYears.children;
        for (let iIndex = 0; iIndex < objOptionsYears.length; iIndex++)
        {                    
            objOption = objOptionsYears[iIndex];
            if (objOption.value == this.#objDateCalendar.getFullYear()) //==> select current year
                objOption.selected = true;
        }
    }

    /**
     * 
     * @param {boolean} bForceRerender forced to rerender everything?
     */
    #renderCalendarMonths(bForceRerender = false)
    {
        let objOption = null;  
                
        //==== MONTHS ====
        //select current month in combobox
        const objSelectMonths = this.#objDRBubble.querySelector(".drinputdatecalendarmonths");
        if ((objSelectMonths.childElementCount < 2) || (bForceRerender))
        {
            objSelectMonths.innerHTML = "";

            //jan
            objOption = document.createElement("option");
            objOption.value = 1;
            objOption.textContent = this.sTransJanuary;
            objSelectMonths.appendChild(objOption);

            //feb
            objOption = document.createElement("option");
            objOption.value = 2;
            objOption.textContent = this.sTransFebruary;
            objSelectMonths.appendChild(objOption);

            //mar
            objOption = document.createElement("option");
            objOption.value = 3;
            objOption.textContent = this.sTransMarch;
            objSelectMonths.appendChild(objOption);

            //apr
            objOption = document.createElement("option");
            objOption.value = 4;
            objOption.textContent = this.sTransApril;
            objSelectMonths.appendChild(objOption);

            //may
            objOption = document.createElement("option");
            objOption.value = 5;
            objOption.textContent = this.sTransMay;
            objSelectMonths.appendChild(objOption);

            //jun
            objOption = document.createElement("option");
            objOption.value = 6;
            objOption.textContent = this.sTransJune;
            objSelectMonths.appendChild(objOption);

            //jul
            objOption = document.createElement("option");
            objOption.value = 7;
            objOption.textContent = this.sTransJuly;
            objSelectMonths.appendChild(objOption);

            //aug
            objOption = document.createElement("option");
            objOption.value = 8;
            objOption.textContent = this.sTransAugust;
            objSelectMonths.appendChild(objOption);

            //sept
            objOption = document.createElement("option");
            objOption.value = 9;
            objOption.textContent = this.sTransSeptember;
            objSelectMonths.appendChild(objOption);

            //oct
            objOption = document.createElement("option");
            objOption.value = 10;
            objOption.textContent = this.sTransOctober;
            objSelectMonths.appendChild(objOption);

            //nov
            objOption = document.createElement("option");
            objOption.value = 11;
            objOption.textContent = this.sTransNovember;
            objSelectMonths.appendChild(objOption);

            //dec
            objOption = document.createElement("option");
            objOption.value = 12;
            objOption.textContent = this.sTransDecember;
            objSelectMonths.appendChild(objOption);
        }

        //select curren month in combobox
        let objOptionsMonths = objSelectMonths.children;
        const iCurrentMonth = this.#objDateCalendar.getMonth() + 1; //getCurrentmonth is from 0 - 11
        // console.log("currentmonth selected: ", iCurrentMonth);
        for (let iMonths = 0; iMonths < objOptionsMonths.length; iMonths++)
        {        
            objOption = objOptionsMonths[iMonths];
            if (objOption.value == iCurrentMonth)
                objOption.selected = true;
        }        
    }

    #renderCalendarTable()
    {
        let objCell = null;
        let iYear = this.#objDateCalendar.getFullYear();
        let iMonth = this.#objDateCalendar.getMonth() + 1;
        let iDay = this.#objDateCalendar.getDate();
        let objCalcDate = new Date(iYear, iMonth, iDay);


        //get last day of previous month (because we need to show a part of the previous month)
        objCalcDate = new Date(iYear, iMonth-1, 0);
        const iDaysInPreviousMonth = objCalcDate.getDate(); 

        //get last day of the month
        objCalcDate = new Date(iYear, iMonth, 0); //setDay(0) has a bug that skips august and january of each year, so instantiate a new date every time to reset it
        const iDaysInCurrentMonth = objCalcDate.getDate(); 

        //get fist day of the month
        objCalcDate = new Date(iYear, iMonth - 1, 1);
        const iStartMonthDayOfWeek = objCalcDate.getDay(); //the month started on a: 0 = sunday, 1 = monday, 6 = saturday
        let iStartMonthOnDay = iStartMonthDayOfWeek % 7; //how many empty cells do we need to create for previous month?
        iStartMonthOnDay = iStartMonthOnDay - this.#iFirstDayOfWeek; //adjust for fist day of the week (sunday or monday)

        //first days of the next month (start day + all days in curr month, modulo 7 for day)
        let iEndMonthOnDay = 7 - ((iStartMonthOnDay + iDaysInCurrentMonth) % 7);
        iEndMonthOnDay = iEndMonthOnDay % 7; //sometimes it exceeds 7 for some weird reason. Example: august 2025

        // console.log("iDaysInCurrentMonth", iDaysInCurrentMonth);
        // console.log("iStartMonthOnDay", iStartMonthOnDay);
        // console.log("iEndMonthOnDay", iEndMonthOnDay);


        //==== CALENDAR ====
        this.#objDivCalendarTable = this.#objDRBubble.querySelector(".drinputdatecalendartable");
        this.#objDivCalendarTable.innerHTML = "";


        //HEAD
        for (let iDayOfWeek = 0; iDayOfWeek < 7; iDayOfWeek++)
        {
            objCell = document.createElement("div");
            objCell.className = "header";
            objCell.textContent = this.#arrDaysOfWeek[iDayOfWeek];

            this.#objDivCalendarTable.appendChild(objCell);
        }


        //BODY
        //manage event listeners
        this.#objAbortControllerCalendarDays.abort();
        this.#objAbortControllerCalendarDays = new AbortController();

        //add previous month
        for (let iIndex = 0; iIndex < iStartMonthOnDay; iIndex++)
        {
            objCell = document.createElement("div");
            objCell.classList.add("day");
            objCell.classList.add("othermonth");
            objCell.textContent = " ";
            this.#objDivCalendarTable.appendChild(objCell);     
        }        

        //add current month
        let iCurrDayOfWeek = 0;
        for (let iIndex = 0; iIndex < iDaysInCurrentMonth; iIndex++)
        {
            objCell = document.createElement("div");
            objCell.classList.add("day");
            objCell.classList.add("currentmonth");

            //current day??
            if (this.#objDate)
                if (this.#objDate.getFullYear() == iYear)
                    if (this.#objDate.getMonth() == iMonth - 1)
                        if (this.#objDate.getDate() == iIndex + 1)
                            objCell.classList.add("currentday");       

            //give class based on day-of-week
            iCurrDayOfWeek = (iStartMonthOnDay + (iIndex) + this.#iFirstDayOfWeek) % 7;
            switch (iCurrDayOfWeek)
            {
                case 0:
                    objCell.classList.add("sunday");
                    break;
                case 1:
                    objCell.classList.add("monday");
                    break;
                case 2:
                    objCell.classList.add("tuesday");
                    break;
                case 3:
                    objCell.classList.add("wednesday");
                    break;
                case 4:
                    objCell.classList.add("thursday");
                    break;
                case 5:
                    objCell.classList.add("friday");
                    break;
                case 6:
                    objCell.classList.add("saturday");
                    break;
            }


            objCell.textContent = iIndex + 1;
            this.addEventListenersCalendarDay(objCell, iYear, iMonth, iIndex + 1);            
            this.#objDivCalendarTable.appendChild(objCell);   
        }

        //add next month
        for (let iIndex = 0; iIndex < iEndMonthOnDay; iIndex++)
        {
            objCell = document.createElement("div");
            objCell.classList.add("day");
            objCell.classList.add("othermonth");
            objCell.textContent = " ";
            this.#objDivCalendarTable.appendChild(objCell);   
        }    
    
    }

    #renderCalendarFooter()
    {
        const objBtnYesterday = this.shadowRoot.querySelector(".drinputdatecalendaryesterday");
        objBtnYesterday.textContent = this.sTransYesterday;
        const objBtnToday = this.shadowRoot.querySelector(".drinputdatecalendartoday");
        objBtnToday.textContent = this.sTransToday;
        const objBtnTomorrow = this.shadowRoot.querySelector(".drinputdatecalendartomorrow");
        objBtnTomorrow.textContent = this.sTransTomorrow;
    }
    

    #readAttributes()
    {
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", false);
        this.required = DRComponentsLib.attributeToBoolean(this, "required", this.#bRequired);
        this.phpformat = DRComponentsLib.attributeToString(this, "phpformat", this.#sPHPFormat);
        this.allowemptydate = DRComponentsLib.attributeToBoolean(this, "allowemptydate", this.#bAllowEmptyDate);
        if (!this.#bAllowEmptyDate)
            this.#objDate = new Date();
        this.showeditbox = DRComponentsLib.attributeToBoolean(this, "showeditbox",this.#bShowEditBox);
        this.showcalendar = DRComponentsLib.attributeToBoolean(this, "showcalendar",this.#bShowCalendar);
        this.firstdayofweek = DRComponentsLib.attributeToInt(this, "firstdayofweek",this.#iFirstDayOfWeek);

        if (this.getAttribute("value") !== null)
        {
            if (this.getAttribute("value") == "")
                this.#objDate = null;
            else
                this.internalDateAsISO8601 = this.getAttribute("value");
        }

        //translations         
        this.sTransDay      = DRComponentsLib.attributeToString(this, "transday", this.sTransDay);
        this.sTransMonth    = DRComponentsLib.attributeToString(this, "transmonth", this.sTransMonth);
        this.sTransYear     = DRComponentsLib.attributeToString(this, "transyear", this.sTransYear);
        this.sTransJanuary  = DRComponentsLib.attributeToString(this, "transjanuary", this.sTransJanuary);
        this.sTransFebruary = DRComponentsLib.attributeToString(this, "transfebruary", this.sTransFebruary);
        this.sTransMarch    = DRComponentsLib.attributeToString(this, "transmarch", this.sTransMarch);
        this.sTransApril    = DRComponentsLib.attributeToString(this, "transapril", this.sTransApril);
        this.sTransMay      = DRComponentsLib.attributeToString(this, "transmay", this.sTransMay);
        this.sTransJune     = DRComponentsLib.attributeToString(this, "transjune", this.sTransJune);
        this.sTransJuly     = DRComponentsLib.attributeToString(this, "transjuly", this.sTransJuly);
        this.sTransAugust   = DRComponentsLib.attributeToString(this, "transaugust", this.sTransAugust);
        this.sTransSeptember = DRComponentsLib.attributeToString(this, "transseptember", this.sTransSeptember);
        this.sTransOctober  = DRComponentsLib.attributeToString(this, "transoctober", this.sTransOctober);
        this.sTransNovember = DRComponentsLib.attributeToString(this, "transnovember", this.sTransNovember);
        this.sTransDecember = DRComponentsLib.attributeToString(this, "transdecember", this.sTransDecember);
        this.sTransMonday   = DRComponentsLib.attributeToString(this, "transmonday", this.sTransMonday);
        this.sTransTuesday  = DRComponentsLib.attributeToString(this, "transtruesday", this.sTransTuesday);
        this.sTransWednesday = DRComponentsLib.attributeToString(this, "transwednesday", this.sTransWednesday);
        this.sTransThursday = DRComponentsLib.attributeToString(this, "transthursday", this.sTransThursday);
        this.sTransFriday   = DRComponentsLib.attributeToString(this, "transfrday", this.sTransFriday);
        this.sTransSaturday = DRComponentsLib.attributeToString(this, "transsaturday", this.sTransSaturday);
        this.sTransSunday   = DRComponentsLib.attributeToString(this, "transsunday", this.sTransSunday);
        this.sTransYesterday = DRComponentsLib.attributeToString(this, "transyesterday", this.sTransYesterday);
        this.sTransToday    = DRComponentsLib.attributeToString(this, "transtoday", this.sTransToday);
        this.sTransTomorrow = DRComponentsLib.attributeToString(this, "transtomorrow", this.sTransTomorrow);          
    }

    #attributeToString(sAttrName, sDefault = "")
    {
        if (this.getAttribute(sAttrName))
            return this.getAttribute(sAttrName);

        return sDefault;
    }

    #attributeToBoolean(sAttrName, bDefault = false)
    {
        // console.log("attributename: this.getAttribute(sAttrName)", sAttrName, this.getAttribute(sAttrName))
        if (this.getAttribute(sAttrName) !== null)
        {
            if (this.getAttribute(sAttrName) == "")
                return true;

            if (this.getAttribute(sAttrName) == "false")
                return false
            else
                return true;
        }

        return bDefault;
    }

    #attributeToInt(sAttrName, iDefault = 0)
    {
        if (this.getAttribute(sAttrName) !== null)
        {
            return parseInt(this.getAttribute(sAttrName));
        }

        return iDefault;
    }    


    strToBool(sBoolean)
    {
        if (sBoolean === null) //seems counter intuitive but is consistent with "disabled" attribute on html elements
            return false;

        if (sBoolean == true)
            return true;
        if (sBoolean == false)
            return false;

        if (sBoolean == "true")
            return true;
        if (sBoolean == "false")
            return false;

        if (sBoolean == "")
            return true;
    }    

    boolToStr(bBoolVal)
    {
        if (bBoolVal == true)
            return "true";
        else
            return "false";
    }        

    /**
     * attach event listeners for THIS object
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
            // debugger ; 
            if ((this.#bAllowEmptyDate) && (this.#objEditBox.value == ""))
            {
                this.#objDate = null;
                this.#objFormInternals.setFormValue("");
            }
            else
            {
                this.internalDateAsPHPDate = this.#objEditBox.value;
                this.#objFormInternals.setFormValue(this.internalDateAsISO8601);
                this.#objDateCalendar.setTime(this.#objDate.getTime());
                this.#updateUICalendar();
            }
            
            this.#dispatchEventInputUpdated(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });     
        

        //MOUSEDOWN
        this.#objDivCalendarIcon.addEventListener("mousedown", (objEvent)=>
        {
            // console.log("mousedowniiiieeeee", this.#bDisabled);

            if ((this.#bShowCalendar) && (this.#bDisabled == false))
            {
                if (this.#objDRBubble)
                {
                    if (this.#objDRBubble.isHidden())
                    {
                        if (this.#objDate === null)
                            this.#objDate = new Date();

                        //make the timestamp in the editbox the timestamp of the calendar
                        this.#objDateCalendar.setTime(this.#objDate.getTime());
                        
                        this.#objDRBubble.show();
                        this.updateUI();
                    }
                }
            }         

        }, { signal: this.#objAbortController.signal });     


        //DOUBLE CLICK MOUSE
        this.#objEditBox.addEventListener("dblclick", (objEvent)=>
        {
            // console.log("bubllllblblbldubbekl klikt");
            if (this.#bShowCalendar)
            {
                if (this.#objDRBubble)
                {
                    if (this.#objDRBubble.isHidden())
                    {
                        if (this.#objDate === null)
                            this.#objDate = new Date();

                        //make the timestamp in the editbox the timestamp of the calendar
                        this.#objDateCalendar.setTime(this.#objDate.getTime());
                        
                        this.#objDRBubble.show();

                        this.updateUI();
                    }
                }
            }         

        }, { signal: this.#objAbortController.signal });     


        //FOCUSOUT: correct input so user knows what time is recognized
        this.#objEditBox.addEventListener("focusout", (objEvent)=>
        {
            // console.log("focusoutieeeeeee", this.#bAllowEmptyDate, this.#objEditBox.value);

            //correct always, except when it's an empty date
            if (!((this.#bAllowEmptyDate) && (this.#objEditBox.value == "")))
            {
                this.#objEditBox.value = this.internalDateAsPHPDate;
            }
        }, { signal: this.#objAbortController.signal });    
        

        //WHEEL: mousewheel adds or subtracts minutes
        this.#objDivCalendarTable.addEventListener("wheel", (objEvent)=>
        {
            if (this.#objDate)
            {
                if (objEvent.deltaY > 0)   
                    this.#objDate.setDate(this.#objDate.getDate() - 1);
                if (objEvent.deltaY < 0)   
                    this.#objDate.setDate(this.#objDate.getDate() + 1);

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
     * attach event listeners for the calendar
     */
    addEventListenersCalendar()
    {
        const objBtnPreviousMonth = this.#objDRBubble.querySelector(".drinputdatecalendarpreviousmonth");
        const objSelMonths = this.#objDRBubble.querySelector(".drinputdatecalendarmonths");
        const objSelYears = this.#objDRBubble.querySelector(".drinputdatecalendaryears");
        const objBtnNextMonth = this.#objDRBubble.querySelector(".drinputdatecalendarnextmonth");
        const objBtnYesterday = this.#objDRBubble.querySelector(".drinputdatecalendaryesterday");
        const objBtnToday = this.#objDRBubble.querySelector(".drinputdatecalendartoday");
        const objBtnTomorrow = this.#objDRBubble.querySelector(".drinputdatecalendartomorrow");
        const objToday = new Date();

        //MOUSEDOWN: PREVIOUS MONTH
        objBtnPreviousMonth.addEventListener("mousedown", (objEvent)=>
        {
            this.#objDateCalendar.setMonth(this.#objDateCalendar.getMonth() - 1);
            this.updateUI();
        }, { signal: this.#objAbortController.signal });    

        //CHANGE: MONTHS <SELECT>
        objSelMonths.addEventListener("change", (objEvent)=>
        {
            this.#objDateCalendar.setMonth(parseInt(objSelMonths.value) -1);
            this.updateUI();
        }, { signal: this.#objAbortController.signal });   

        //CHANGE: YEARS <SELECT>
        objSelYears.addEventListener("change", (objEvent)=>
        {
            this.#objDateCalendar.setFullYear(objSelYears.value);
            this.updateUI();
        }, { signal: this.#objAbortController.signal });     

        //MOUSEDOWN: NEXT MONTH
        objBtnNextMonth.addEventListener("mousedown", (objEvent)=>
        {
            this.#objDateCalendar.setMonth(this.#objDateCalendar.getMonth() + 1);            
            this.updateUI();
        }, { signal: this.#objAbortController.signal });     

        //MOUSEDOWN: YESTERDAY
        objBtnYesterday.addEventListener("mousedown", (objEvent)=>
        {
            this.#objDate.setTime(objToday.getTime());
            this.#objDate.setDate(this.#objDate.getDate() - 1);
            this.#objFormInternals.setFormValue(this.internalDateAsISO8601);
            this.#objDRBubble.hide();
            this.updateUI();
            this.#dispatchEventInputUpdated(this.#objDRBubble, "yesterday clicked");
        }, { signal: this.#objAbortController.signal });    

        //MOUSEDOWN: TODAY
        objBtnToday.addEventListener("mousedown", (objEvent)=>
        {
            // console.log("TODAY", objToday.value);
            this.#objDate.setTime(objToday.getTime());    
            this.#objFormInternals.setFormValue(this.internalDateAsISO8601);
            this.#objDRBubble.hide();  
            this.updateUI();
            this.#dispatchEventInputUpdated(this.#objDRBubble, "today clicked");
        }, { signal: this.#objAbortController.signal });    
    
        //MOUSEDOWN: TOMORROW
        objBtnTomorrow.addEventListener("mousedown", (objEvent)=>
        {
            // console.log("TOMORROW");
            this.#objDate.setTime(objToday.getTime());           
            this.#objDate.setDate(this.#objDate.getDate() + 1);
            this.#objFormInternals.setFormValue(this.internalDateAsISO8601);

            this.#objDRBubble.hide();

            this.updateUI();
            this.#dispatchEventInputUpdated(this.#objDRBubble, "tomorrow clicked");
        }, { signal: this.#objAbortController.signal });      
        
        //WHEEL: mousewheel adds or subtracts months
        this.#objDivCalendarTable.addEventListener("wheel", (objEvent)=>
        {
            if (this.#objDateCalendar)
            {
                if (objEvent.deltaY > 0)   
                    this.#objDateCalendar.setMonth(this.#objDateCalendar.getMonth() - 1);
                if (objEvent.deltaY < 0)   
                    this.#objDateCalendar.setMonth(this.#objDateCalendar.getMonth() + 1);
                this.updateUI();

                //prevent scrolling
                objEvent.preventDefault();
            }
        }, { signal: this.#objAbortController.signal });    


        //CHANGE event: editbox
        this.#objEditBox.addEventListener("change", (objEvent)=>
        {  
            this.#dispatchEventChange(this.#objEditBox, "editbox changed");
        }, { signal: this.#objAbortController.signal });          
    }

    /**
     * attach event listener to a div in the calendar 
     * @param {HTMLElement} objDivSource 
     */
    addEventListenersCalendarDay(objDivSource, iYear, iMonth, iDay)
    {

        //MOUSEDOWN
        objDivSource.addEventListener("mousedown", (objEvent)=>
        {
            // console.log("Yes, dag geklikt", iYear, iMonth, iDay);
            this.#objDate.setFullYear(iYear);
            this.#objDate.setMonth(iMonth -1);
            this.#objDate.setDate(iDay);
            this.#objFormInternals.setFormValue(this.internalDateAsISO8601);

            this.#objDRBubble.hide();

            this.updateUI();
            this.#dispatchEventInputUpdated(this.#objDRBubble, "calendar changed");
        }, { signal: this.#objAbortControllerCalendarDays.signal });            
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
        // console.log("date: dispatch change");
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
            if (this.#objDate !== null)
            {
                this.#objDate.setDate(this.#objDate.getDate() + 1);
                this.updateUI();
            }

            return true;
        }

        if (objEvent.key == "ArrowDown")
        {
            if (this.#objDate !== null)
            {
                this.#objDate.setDate(this.#objDate.getDate() - 1);
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


    static get observedAttributes() 
    {
        return ["value", "required", "phpformat", "allowemptydate", "showeditbox", "showcalendar", "disabled"];
    }
  

    /**
     * 21-03-2025 (d-m-Y) 
     * @param {string} sPHPFormattedDate 
     */
    set internalDateAsPHPDate(sPHPFormattedDate)
    {
        if (!this.#objDate)
            this.#objDate = new Date();   
 

        const arrValueSeparators = this.#getValueSeparatorsPHPFormat();

        //2 arrays have the same parts at the same index: example with d-m-Y: arrSpecialChars = [d,m,Y] and arrDateParts = [15,12,2006]
        const arrSpecialChars = this.#splitByValueSeparators(this.#sPHPFormat, arrValueSeparators); //search this.#sPHPFormat for value separators
        const arrDateParts = this.#splitByValueSeparators(sPHPFormattedDate, arrValueSeparators); //search sPHPFormattedDate for value separators
        
        for (let iIndex = 0; iIndex < arrDateParts.length; iIndex++)
        {
            switch(arrSpecialChars[iIndex]) 
            {
                case "d": //day
                    this.#objDate.setDate(this.#parseIntBetter(arrDateParts[iIndex], 1, 31));
                    break;
                case "j": //day
                    this.#objDate.setDate(this.#parseIntBetter(arrDateParts[iIndex], 1, 31));
                    break;
                case "m": //month
                    this.#objDate.setMonth(this.#parseIntBetter(arrDateParts[iIndex]-1, 0, 11)); //months are -1: 3rd month is april and not march!!!
                    break;
                case "n": //month
                    this.#objDate.setMonth(this.#parseIntBetter(arrDateParts[iIndex]-1, 0, 11)); //months are -1: 3rd month is april and not march!!!
                    break;
                case "Y": //year
                    this.#objDate.setFullYear(this.#parseIntBetter(arrDateParts[iIndex], 1900, 3000));
                    break;
                case "y": //year: technically it is a 2 digit number, but to prevent weird millenium bug-issues we assume 4
                    this.#objDate.setFullYear(this.#parseIntBetter(arrDateParts[iIndex], 1900, 3000));
                    break;
            }
        }

        // console.log("setinternalshizzelbrea: ", sPHPFormattedDate, this.#objDate, arrDateParts.length)   ;     
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
     * set date as php date, like d-m-Y meaning 21-03-2025
     * @param {string} sISO8601FormattedDate 
     */    
    get internalDateAsPHPDate()
    {
        let sResult = this.#sPHPFormat;
        
        sResult = sResult.replaceAll("d", (this.#objDate.getDate()).toString().padStart(2, "0"));
        sResult = sResult.replaceAll("j", (this.#objDate.getDate()).toString());
        sResult = sResult.replaceAll("m", (this.#objDate.getMonth() + 1).toString().padStart(2, "0"));
        sResult = sResult.replaceAll("n", (this.#objDate.getMonth() + 1).toString());
        sResult = sResult.replaceAll("Y", (this.#objDate.getFullYear()).toString().padStart(4, "0"));
        sResult = sResult.replaceAll("y", (this.#objDate.getFullYear()).toString()); //technically it is a 2 digit number, but to prevent weird millenium bug-issues we assume 4

        return sResult;
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
        // const arrSpecialChars = ["d", "m", "Y", "j", "y", "n"]; //quick for debugging purposes
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
     * set date as YYYY-MM-DD i.e. 2025-03-21
     * @param {string} sISO8601FormattedDate 
     */
    set internalDateAsISO8601(sISO8601FormattedDate)
    {
    
        if (!this.#objDate)
            this.#objDate = new Date();
        /*
        //year
        this.#objDate.setFullYear(sISO8601FormattedDate.substring(0,4));
        
        //month - starts at 0 instead of 1
        let sMonth = sISO8601FormattedDate.substring(5,7);
        let iMonth = parseInt(sMonth);
        iMonth--;
        this.#objDate.setMonth(iMonth);

        //day
        this.#objDate.setDate(sISO8601FormattedDate.substring(8,10));
        */

        if ((sISO8601FormattedDate != "") && (sISO8601FormattedDate != "0")) //dont trip over an empty date
            this.#objDate.setTime(Date.parse(sISO8601FormattedDate));
    }

    /**
     * get date as YYYY-MM-DD i.e. 2025-03-21
     * @param {string} sISO8601FormattedDate 
     */    
    get internalDateAsISO8601()
    {
        if (this.#objDate)
        {
            return this.#objDate.toISOString();
        }
        else
            return "";
    }




    /**
    * set value in ISO 8601 (YYYY-MM-DD)
    */
    set value(sValue)
    {
        if (sValue == "")
            this.#objDate = null;
        else
            this.internalDateAsISO8601 = sValue;

        this.updateUI();
    }

    /**
    * retrieve value in ISO 8601 (YYYY-MM-DD)
    */
    get value()
    {
        if (this.#objDate == null)
            return ""
        else
            return this.internalDateAsISO8601;
    }

    /**
     * returns internal Date() object.
     * 
     * WARNING:
     * - Can be null, This happens for an invalid date/00-00-0000 date
     * - when you change this date-object, use updateUI() to update the UI
     */
    get date()
    {
        return this.#objDate;
    }


    /**
    * set required
    */
    set required(bValue)
    {
        // this.#bRequired = bValue;
        this.#bDisabled = DRComponentsLib.strToBool(bValue);
    }

    /**
    * retrieve required
    */
    get required()
    {
        // return this.#bRequired;
        return DRComponentsLib.boolToStr(this.#bDisabled);
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
        this.#bDisabled = this.strToBool(bValue);
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
    * set allow empty
    */
    set allowemptydate(bValue)
    {
        this.#bAllowEmptyDate = bValue;
    }

    /**
    * retrieve allow empty
    */
    get allowemptydate()
    {
        return this.#bAllowEmptyDate;
    }


    /**
     * make internal date empty (objDate = null)
     */
    emptyInternalDate()
    {
        this.#objDate = null;
       
        this.#objEditBox.value = "";
    }

    /**
     * create internal date object
     */
    createInternalDate()
    {
        this.#objDate = new Date();
    }
        

    /**
    * set first day of week. 0=sunday, 1=monday
    */
    set firstdayofweek(iDay)
    {
        this.#iFirstDayOfWeek = iDay;
    }

    /**
    * get first day of week. 0=sunday, 1=monday
    */
    get firstdayofweek()
    {
        return this.#iFirstDayOfWeek;
    }

    /**
    * set show edit box
    */
    set showeditbox(bValue)
    {
        this.#bShowEditBox = bValue;
    }

    /**
    * get show edit box
    */
    get showeditbox()
    {
        return this.#bShowEditBox;
    }

    /**
    * set show calendar
    */
    set showcalendar(bValue)
    {
        this.#bShowCalendar = bValue;
    }

    /**
    * set show calendar
    */
    get showcalendar()
    {
        return this.#bShowCalendar;
    }

    /**
    * set language text for day
    */
    set transday(sValue)
    {
        this.sTransDay = sValue;
    }

    /**
    * get language text for day
    */
    get transday()
    {
        return this.sTransDay;
    }

    /**
    * set language text for month
    */
    set transmonth(sValue)
    {
        this.sTransMonth = sValue;
    }

    /**
    * get language text for month
    */
    get transmonth()
    {
        return this.sTransMonth;
    }

    /**
    * set language text for year
    */
    set transyear(sValue)
    {
        this.sTransYear = sValue;
    }

    /**
    * get language text for year
    */
    get transyear()
    {
        return this.sTransYear;
    }



    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
        this.#objAbortControllerCalendarDays.abort();
    }


    /** 
     * When added to DOM
     */
    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {        
            //first read
            this.#readAttributes();

            //build cache
            this.buildCache();        

            //then render
            this.populate();
        }

        this.#objFormInternals.setFormValue(this.internalDateAsISO8601); //update value in the form

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();

        //at last: events    
        this.addEventListeners();    
        this.addEventListenersCalendar();      
        

        this.#bConnectedCallbackHappened = true;             
    }

    /** 
     * remove from DOM 
     **/
    disconnectedCallback()
    {
        this.removeEventListeners();
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
      


  

}


/**
 * make component available in HTML
 */
customElements.define("dr-input-date", DRInputDate);