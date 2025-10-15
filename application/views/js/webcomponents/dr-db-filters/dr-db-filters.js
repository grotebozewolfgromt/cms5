<?php 
/**
 * dr-db-filters.js
 *
 * class to create the UI for database filters as "chips"
 * The idea is to have a counter PHP element that takes care of the database side of things.
 * This class fires an event "change", use this event to trigger a database (re)load.
 * 
 * FILTER-CHANGE EVENT:
 * object fires event "change" when filter changed:
 * window.addEventListener('change', (e) => { console.log(e)})
 * 
 * WARNING:
 * - don't use actual database column names to prevent malicious actors from doing shady shizzle. Use for example column indexes instead (and check returning values to be numbers)
 * - the field "value" represents either a value or start value (depending on whether it's a range or not)
 * 
 * DEPENDENCIES:
 * -dr-popover
 * -dr-context-menu
 * -dr-input-number
 * -dr-input-date
 * -DRContextMenu
 * 
 * 
 * @todo datum: mogelijkheid om periodes te definieren: 7 dagen, 30 dagen, 90 dagen, 365 dagen, week-to-date, quarter-to-date, month-to-date, year-to-date
  * @todo bug calender datum: klik op volgende maand of selecteer jaar en de kalender is foetsie (waarschijnlijk bubble hide probleem)
  * @todo loading and saving all filters to browser storage (can use getFiltersAsJSON() function for that)
  * @todo filters: als quicksearch toegevoegd wordt via menu wordt event getriggerd, terwijl disabled is
 * 
 * @author Dennis Renirie
 * 
 * 14 mrt 2025 dr-db-filters.js created
 * 16 apr 2025 dr-db-filters.js bugfix: waarde boolean-filter werd niet uit chip gelezen bij laten zien van bubble
 * 25 apr 2025 dr-db-filters.js bugfix: quicksearch verwijderen lukte niet, omdat de textinhoud chip gewijzigd werd
 * 25 apr 2025 dr-db-filters.js placeholder quicksearch
 * 25 apr 2025 dr-db-filters.js bugfix: added translation items
 * 21 may 2025 dr-db-filters.js better prepared for custom html element
 * 21 may 2025 dr-db-filters.js when stringfilter search it now gives searched string as chiptext
 * 23 may 2025 dr-db-filters.js filter bubbles toggle when clicked on chiptext twice
 * 23 may 2025 dr-db-filters.js filter bubbles are not hidden when clicked outside
 * 23 may 2025 dr-db-filters.js elementen verplaatst in bubbles om ze meer gebruiksvriendelijk te maken
 * 23 may 2025 dr-db-filters.js bubble wordt automatisch geopend bij het toevoegen via menu
 */
?>


class DRDBFilters extends HTMLElement
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

            .chip.disabled
            {
                color: light-dark(var(--lightmode-color-drdbfilters-chips-disabled, rgb(169, 169, 169)), var(--darkmode-color-drdbfilters-chips-disabled, rgb(166, 166, 166)));
                background-color: light-dark(var(--lightmode-color-drdbfilters-chips-disabled-background, rgb(247, 247, 247)), var(--darkmode-color-drdbfilters-chips-disabled-background, rgb(59, 59, 59)));
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
                padding-top: 6px;

                text-overflow: ellipsis;
                max-width: 150px;
                overflow: hidden; 
                white-space: nowrap;
            }

            .newfilter .chipinnertext
            {
                padding-left: 16px;
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


            .chipquicksearch
            {
                flex-grow: 1;    
            }

            .chipquicksearch [type=text]
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

            dr-popover
            {
                
            }

        
            dr-popover input,
            dr-popover label,
            dr-popover dr-input-checkbox            
            {
                display: block;
                width: 100%;
                box-sizing: border-box;        
            }        

            dr-popover label.comparisonoperator,
            dr-popover label.lblvalue,
            dr-popover label.lblvalueend,
            dr-popover label.lblvisibility
            {
                margin-top: 10px;
                font-weight: bold;           
            }

            dr-popover .edtvalueend[disabled=true],
            dr-popover .lblvalueend[disabled=true]
            {
                opacity: 0.2;
            }            

            dr-popover button.apply
            {
                margin-top: 10px;
                padding: 5px;
                display: block;
                width: 100%;
                border-radius: 5px;
                border-width: 0px;
                border-style: solid;
                cursor: pointer;
           
                background-color: light-dark(var(--lightmode-color-drdbfilters-bubble-button-background-hover, rgb(235, 235, 235)), var(--darkmode-color-drdbfilters-bubble-button-background-hover, rgb(76, 76, 76)));
            }          
                
            dr-popover button.apply:hover
            {
                background-color: light-dark(var(--lightmode-color-drdbfilters-bubble-button-background-hover, rgb(221, 221, 221)), var(--darkmode-color-drdbfilters-bubble-button-background-hover, rgb(95, 95, 95)));
            }          
                
            dr-popover button.apply svg
            {
                width: 16px;
                height: 16px;
            }
        </style>



        <!-- templates for the filter bubbles -->

        <!-- STRING -->
        <dr-popover class="stringfilter" title="" showtitle showcloseicon>
            <label class="comparisonoperator"><!-- translation: comparison operator --></label>            
            <dr-input-checkbox-group minselected="1" maxselected="1">
                <dr-input-checkbox class="like" type="radio"><!-- translation --></dr-input-checkbox>
                <dr-input-checkbox class="notlike" type="radio"><!-- translation --></dr-input-checkbox>
                <dr-input-checkbox class="equalto" type="radio"><!-- translation:  --></dr-input-checkbox>
                <dr-input-checkbox class="notequalto" type="radio"><!-- translation: --></dr-input-checkbox>
            </dr-input-checkbox-group>                                  
            <label class="lblvalue"><!-- translation: value --></label>
            <input type="text" class="edtvalue">
            <label class="lblvisibility"><!-- translation: visibility --></label>
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>  
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>

        <!-- BOOLEAN -->
        <dr-popover class="booleanfilter" title="" showtitle showcloseicon>          
            <label class="lblvalue"><!-- translation: value --></label>
            <dr-input-checkbox-group minselected="1" maxselected="1">
                <dr-input-checkbox class="valuetrue" type="radio"><!-- translation: true --></dr-input-checkbox>
                <dr-input-checkbox class="valuefalse" type="radio"><!-- translation: false --></dr-input-checkbox>
            </dr-input-checkbox-group>
            <label class="lblvisibility"><!-- translation: visibility --></label>
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>

        <!-- NUMBER -->
        <dr-popover class="numberfilter" title="" showtitle showcloseicon>          
            <label class="comparisonoperator"><!-- translation: comparison operator --></label>            
            <dr-input-checkbox-group minselected="1" maxselected="1">
                <dr-input-checkbox class="lessequal" type="radio"><!-- translation -->&lt=</dr-input-checkbox>
                <dr-input-checkbox class="greaterequal" type="radio"><!-- translation: -->&gt;=</dr-input-checkbox>
                <dr-input-checkbox class="equal" type="radio"><!-- translation -->=</dr-input-checkbox>
                <dr-input-checkbox class="between" type="radio"><!-- translation -->=</dr-input-checkbox>
            </dr-input-checkbox-group>        
            <label class="lblvalue"><!-- translation: value --></label>
            <dr-input-number class="edtvalue"></dr-input-number>
            <label class="lblvalueend"><!-- translation: value --></label>
            <dr-input-number class="edtvalueend"></dr-input-number>
            <label class="lblvisibility"><!-- translation: visibility --></label>            
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>

        <!-- DATE -->
        <dr-popover class="datefilter" title="" showtitle showcloseicon>          
            <label class="comparisonoperator"><!-- translation: comparison operator --></label>            
            <dr-input-checkbox-group minselected="1" maxselected="1">
                <dr-input-checkbox class="lessequal" type="radio"><!-- translation -->&lt=</dr-input-checkbox>
                <dr-input-checkbox class="greaterequal" type="radio"><!-- translation: -->&gt;=</dr-input-checkbox>
                <dr-input-checkbox class="equal" type="radio"><!-- translation -->=</dr-input-checkbox>
                <dr-input-checkbox class="between" type="radio"><!-- translation -->=</dr-input-checkbox>
            </dr-input-checkbox-group>        
            <label class="lblvalue"><!-- translation: value --></label>
            <dr-input-date class="edtvalue"></dr-input-date>
            <label class="lblvalueend"><!-- translation: value --></label>
            <dr-input-date class="edtvalueend"></dr-input-date>
            <label class="lblvisibility"><!-- translation: visibility --></label>            
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>    
        
        <!-- HTML Element -->
        <dr-popover class="htmlelementfilter" title="" showtitle showcloseicon>          
            <div class="htmlelementsbody">
            </div>
            <label class="lblvisibility"><!-- translation: visibility --></label>            
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>      
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>    


        <!-- QUICKSEARCH -->
        <dr-popover class="quicksearchfilter" title="" showtitle showcloseicon>
            <label class="lblvalue"><!-- translation: value --></label>
            <input type="text" class="edtvalue">
            <label class="lblvisibility"><!-- translation: visibility --></label>            
            <dr-input-checkbox class="filterenabled"><!-- translation: enabled --></dr-input-checkbox>  
            <button class="apply"><!-- translation: apply --></button>
        </dr-popover>        

    `;

    sSVGIconRemove = '<svg stroke="currentColor" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="-2 -2 28 28" stroke-width="1.5" ><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
    sSVGIconPlus = '<svg fill="currentColor" viewBox="1 1 30 30" xmlns="http://www.w3.org/2000/svg"><g><g><path d="M16,24c-0.5527344,0-1-0.4472656-1-1V9c0-0.5527344,0.4472656-1,1-1s1,0.4472656,1,1v14    C17,23.5527344,16.5527344,24,16,24z"/></g><g><path d="M23,17H9c-0.5527344,0-1-0.4472656-1-1s0.4472656-1,1-1h14c0.5527344,0,1,0.4472656,1,1S23.5527344,17,23,17z"/></g></g></svg>';
    sSVGIconApply = '<svg fill="currentColor" viewBox="5 5 16 16" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"></path></svg>';
    sSVGIconFilter = '<svg fill="currentColor" viewBox="-5 -5 40 40" xmlns="http://www.w3.org/2000/svg"><defs><style></style></defs><title/><g data-name="Layer 2" id="Layer_2"><path d="M13,28a1,1,0,0,1-.53-.15A1,1,0,0,1,12,27V17.38L4.28,8.81A5,5,0,0,1,3,5.46V5A1,1,0,0,1,4,4H28a1,1,0,0,1,1,1v.46a5,5,0,0,1-1.28,3.35L20,17.38v5.38a3,3,0,0,1-1.66,2.69l-4.89,2.44A1,1,0,0,1,13,28ZM5.05,6a3,3,0,0,0,.72,1.47l8,8.86A1,1,0,0,1,14,17v8.38l3.45-1.72a1,1,0,0,0,.55-.9V17a1,1,0,0,1,.26-.67l8-8.86A3,3,0,0,0,27,6Z"/></g><g id="frame"></g></svg>';
    sSVGIconSearch = '<svg fill="currentColor" viewBox="-50 -50 600 600"xmlns="http://www.w3.org/2000/svg"><path d="M456.69,421.39,362.6,327.3a173.81,173.81,0,0,0,34.84-104.58C397.44,126.38,319.06,48,222.72,48S48,126.38,48,222.72s78.38,174.72,174.72,174.72A173.81,173.81,0,0,0,327.3,362.6l94.09,94.09a25,25,0,0,0,35.3-35.3ZM97.92,222.72a124.8,124.8,0,1,1,124.8,124.8A124.95,124.95,0,0,1,97.92,222.72Z"/></svg>';
    
    #objNewFilterMenu = null;//DRContextMenu object
    #objChipBubble = null; //DRPopover object => bubble with extra settings for the filter (we only work with one bubble that we render for each chip)
    #objChipBubbleAbortController = null; //abort controller for everything that is in the bubble (we only work with one bubble that we render for each chip)
    #arrChipsAbortControllers = []; //array with abort controllers for filter chips themselves. Each item in array is a AbortController object for a chip
    #objAbortController = null;
    #bConnectedCallbackHappened = false;

    sTransNewFilter = "New filter";
    sTransButtonApply = "Set filter";
    sTransCheckEnabled = "Filter enabled";
    sTransLabelVisibility = "Visibility";
    sTransLabelValue = "Value";
    sTransValueBoolTrue = "Checked";
    sTransValueBoolFalse = "Unchecked";
    sTransComparisonOperator = "Result";
    sTransEqualTo = "is exactly";
    sTransNotEqualTo = "is exactly NOT";
    sTransLike = "contains";
    sTransNotLike = "contains NOT";
    sTransLessEqual = "&le;";
    sTransGreaterEqual = "&ge;";
    sTransEqual = "&equals;";
    sTransBetween = "between";    
    sTransBetweenAnd = "and";  
    sTransPeriod = "Period";       
    sTransPeriodFrom = "From";       
    sTransPeriodTo = "To";       
    sTransQuicksearch = "Quicksearch";    
    sTransQuicksearchPlaceholder = "Type search query here";

    arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", valuescombobox: "valuescombobox", filterindex: "filterindex", comparisonoperator: "comparisonoperator", namenice:"namenice"}; //all attributes of a slotted chip.  the keys and values are always the same for ease of programming
    arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement: "htmlelement"}; //types of filters. the keys and values are always the same for ease of programming
    arrFilterStatus = {available: "available", applied: "applied"}; //status of a chip. "available" is only visible in pulldown-menu
    arrAvailableFilters = []; //filters that are available --> chips read from DOM
    arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
    
    //locale aware shizzle
    #sDecimalSeparator = ",";
    #sThousandSeparator = ".";
    #sPHPDateFormat = "Y-m-d"; //default assuming the ISO 8601 notation for PHP format (year-month-day). meaning of letters: in https://www.php.net/manual/en/datetime.format.php
    #iFirstDayOfWeek = 0;//0=sunday, 1=monday

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

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objAbortController = new AbortController();
        this.#objChipBubbleAbortController = null;

        this.attachShadow({mode: "open"});


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRDBFilters.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

    }    

    #readAttributes()
    {
        this.#sDecimalSeparator = DRComponentsLib.attributeToString(this, "decimalseparator", this.#sDecimalSeparator);
        this.#sThousandSeparator = DRComponentsLib.attributeToString(this, "thousandseparator", this.#sThousandSeparator);
        this.#sPHPDateFormat = DRComponentsLib.attributeToString(this, "phpdateformat", this.#sPHPDateFormat);
        this.#iFirstDayOfWeek = DRComponentsLib.attributeToInt(this, "firstdayofweek", this.#iFirstDayOfWeek);

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
        this.sTransTuesday  = DRComponentsLib.attributeToString(this, "transtuesday", this.sTransTuesday);
        this.sTransWednesday = DRComponentsLib.attributeToString(this, "transwednesday", this.sTransWednesday);
        this.sTransThursday = DRComponentsLib.attributeToString(this, "transthursday", this.sTransThursday);
        this.sTransFriday   = DRComponentsLib.attributeToString(this, "transfriday", this.sTransFriday);
        this.sTransSaturday = DRComponentsLib.attributeToString(this, "transsaturday", this.sTransSaturday);
        this.sTransSunday   = DRComponentsLib.attributeToString(this, "transsunday", this.sTransSunday);
        this.sTransYesterday = DRComponentsLib.attributeToString(this, "transyesterday", this.sTransYesterday);
        this.sTransToday    = DRComponentsLib.attributeToString(this, "transtoday", this.sTransToday);
        this.sTransTomorrow = DRComponentsLib.attributeToString(this, "transtomorrow", this.sTransTomorrow);

        this.sTransNewFilter = DRComponentsLib.attributeToString(this, "transnewfilter", this.sTransNewFilter);
        this.sTransButtonApply = DRComponentsLib.attributeToString(this, "transbuttonapply", this.sTransButtonApply);
        this.sTransLabelVisibility = DRComponentsLib.attributeToString(this, "translabelvisibility", this.sTransLabelVisibility);
        this.sTransCheckEnabled = DRComponentsLib.attributeToString(this, "transcheckenabled", this.sTransCheckEnabled);
        this.sTransLabelValue = DRComponentsLib.attributeToString(this, "transchecklabelvalue", this.sTransLabelValue);
        this.sTransValueBoolTrue = DRComponentsLib.attributeToString(this, "transvaluebooltrue", this.sTransValueBoolTrue);
        this.sTransValueBoolFalse = DRComponentsLib.attributeToString(this, "transvalueboolfalse", this.sTransValueBoolFalse);
        this.sTransComparisonOperator = DRComponentsLib.attributeToString(this, "transcomparisonoperator", this.sTransComparisonOperator);
        this.sTransEqualTo = DRComponentsLib.attributeToString(this, "transequalto", this.sTransEqualTo);
        this.sTransNotEqualTo = DRComponentsLib.attributeToString(this, "transnotequalto", this.sTransNotEqualTo);
        this.sTransLike = DRComponentsLib.attributeToString(this, "translike", this.sTransLike);
        this.sTransNotLike = DRComponentsLib.attributeToString(this, "transnotlike", this.sTransNotLike);
        this.sTransLessEqual = DRComponentsLib.attributeToString(this, "translessequal", this.sTransLessEqual);
        this.sTransGreaterEqual = DRComponentsLib.attributeToString(this, "transgreaterequal", this.sTransGreaterEqual);
        this.sTransEqual = DRComponentsLib.attributeToString(this, "transequal", this.sTransEqual);
        this.sTransBetween = DRComponentsLib.attributeToString(this, "transbetween", this.sTransBetween);
        this.sTransBetweenAnd = DRComponentsLib.attributeToString(this, "transbetweenand", this.sTransBetweenAnd);
        this.sTransPeriod = DRComponentsLib.attributeToString(this, "transperiod", this.sTransPeriod);
        this.sTransPeriodFrom = DRComponentsLib.attributeToString(this, "transperiodfrom", this.sTransPeriodFrom);
        this.sTransPeriodTo = DRComponentsLib.attributeToString(this, "transperiodto", this.sTransPeriodTo);
        this.sTransQuicksearchPlaceholder = DRComponentsLib.attributeToString(this, "transquicksearchplaceholder", this.sTransQuicksearchPlaceholder);

    }

    #populate()
    {
        let sChipStatus = this.arrFilterStatus.available;
        let sChipFilterType = this.arrFilterTypes.string;
        let sFilterIndex = "";
        let sNameNice = "";

        //==== add revamped chips
        for (let iIndex = 0; iIndex < this.children.length; iIndex++)
        {
            sChipStatus = DRComponentsLib.attributeToString(this.children[iIndex], this.arrChipAttributes.status, this.arrFilterStatus.available);
            sChipFilterType = DRComponentsLib.attributeToString(this.children[iIndex], this.arrChipAttributes.filtertype, this.arrFilterTypes.string);
            sFilterIndex = DRComponentsLib.attributeToString(this.children[iIndex], this.arrChipAttributes.filterindex);
            sNameNice = DRComponentsLib.attributeToString(this.children[iIndex], this.arrChipAttributes.namenice);

            this.arrAvailableFilters.push(this.children[iIndex]);//always add to menu

            if (sChipStatus == this.arrFilterStatus.applied)
                this.addFilterUI(this.sSVGIconFilter, sNameNice, sFilterIndex, sChipFilterType);
        }


        //==== add "new filter" element
        let objNewChip = document.createElement("div");
        objNewChip.classList.add("chip");
        objNewChip.classList.add("highlight");
        objNewChip.classList.add("newfilter");

        //add text div
        const objNewFilter = document.createElement("div");
        objNewFilter.classList.add("chipinnertext")
        objNewFilter.innerHTML = this.sTransNewFilter;
        objNewChip.appendChild(objNewFilter);

        //add + icon div
        let objPlusButton = document.createElement("div");  
        objPlusButton.classList.add("chipicon");
        objPlusButton.innerHTML = this.sSVGIconPlus;  
        objNewChip.appendChild(objPlusButton);                    

        this.shadowRoot.appendChild(objNewChip);

        //create menu
        this.#renderNewFilterMenu(objNewChip);
        this.shadowRoot.appendChild(this.#objNewFilterMenu);


        //=== add edit-box element for quicksearch
        const objChipEdit = document.createElement("div");
        objChipEdit.classList.add("chipquicksearch");
            const objEditBox = document.createElement("input");
            objEditBox.type = "text";
            objEditBox.placeholder = this.sTransQuicksearchPlaceholder;
            objChipEdit.appendChild(objEditBox);
        this.shadowRoot.appendChild(objChipEdit);        
    }


    /**
     * renders new filter menu
     * 
     * @param {HTMLElement} objParentChip 
     */
    #renderNewFilterMenu(objParentChip)
    {
        this.#objNewFilterMenu = new DRContextMenu();
        this.#objNewFilterMenu.anchorobject = objParentChip;
        this.#objNewFilterMenu.anchorpos = this.#objNewFilterMenu.iPosBottom;

        //loop available filters
        for (let iIndex = 0; iIndex < this.arrAvailableFilters.length; iIndex++)
        {
            //add menu item to filters
            this.#objNewFilterMenu.addMenuItem(DRComponentsLib.attributeToString(this.arrAvailableFilters[iIndex], this.arrChipAttributes.namenice, ""), () =>
            {

                this.addFilterUI(
                    this.sSVGIconFilter, 
                    DRComponentsLib.attributeToString(this.arrAvailableFilters[iIndex], this.arrChipAttributes.namenice, ""), 
                    DRComponentsLib.attributeToString(this.arrAvailableFilters[iIndex], this.arrChipAttributes.filterindex, ""), 
                    DRComponentsLib.attributeToString(this.arrAvailableFilters[iIndex], this.arrChipAttributes.filtertype, this.arrFilterTypes.string),
                    !DRComponentsLib.attributeToBoolean(this.arrAvailableFilters[iIndex], this.arrChipAttributes.disabled, false),
                    this.arrAvailableFilters[iIndex],
                    true
                );
                this.#objNewFilterMenu.hide();

                //dispatch event then status is enabled
                const bEnabled = !DRComponentsLib.attributeToBoolean(this.arrAvailableFilters[iIndex], this.arrChipAttributes.disabled, false);                
                if (bEnabled)
                    this.#dispatchEventFiltersChanged(this.#objNewFilterMenu, "add new filter menu");
            }); 
        }

        //line
        this.#objNewFilterMenu.addHR();

        //quicksearch
        this.#objNewFilterMenu.addMenuItem(this.sTransQuicksearch, () =>
        {
            this.addFilterUI(this.sSVGIconSearch, this.sTransQuicksearch, "", this.arrFilterTypes.quicksearch, false);
            this.#objNewFilterMenu.hide();

            this.#dispatchEventFiltersChanged(this.#objNewFilterMenu, "add new quicksearch filter via menu");
        });         

    }



    /**
     * add event listeners to component 
     */
    addEventListeners()
    {

        //attach event listeners for each child
        for (let iIndex = 0; iIndex < this.shadowRoot.childElementCount; iIndex++)
        {
            //regular filter ==> happens already in this.addFilterUI()
            // if (this.children[iIndex].classList.contains("filter"))
            // {
            //     this.#addEventListenersFilter(this.children[iIndex]);
            // }
            

            //quicksearch
            if (this.shadowRoot.children[iIndex].classList.contains("chipquicksearch"))
            {
                this.#addEventListenersQuicksearch(this.shadowRoot.children[iIndex]);
            }            
        }
    }


    /**
     * attach event listeners for chip with quicksearch editbox
     * 
     * @param {HTMLElement} objDivChip 
     */        
    #addEventListenersQuicksearch(objDivChip)
    {
        const objInputText = objDivChip.querySelector("input[type=text]"); 
        objInputText.addEventListener("keydown", (objEvent) => 
        {
            if (!objEvent.repeat)
            {
                if (objEvent.key == "Enter")
                {
                    if (objInputText.value.length >= 3)
                    {
                        const objChip = this.addFilterUI(this.sSVGIconSearch, '"' + objInputText.value + '"', "", this.arrFilterTypes.quicksearch, true);
                        objChip.setAttribute(this.arrChipAttributes.value, objInputText.value);
                        objInputText.value = "";                        
                        this.#dispatchEventFiltersChanged(objDivChip, "search");
                    }
                }
            }
        }, { signal: this.#objAbortController.signal });  
    }

    /**
     * attach event listeners for regular filter chip
     * 
     * @param {HTMLElement} objDivChip revamped chip
     * @param {HTMLElement} objDivOriginalChip original chip
     */
    #addEventListenersFilter(objDivChip, objDivOriginalChip)
    {
        const objCtrl = new AbortController();

        //==== enabled & type-icon
        const objDivType = objDivChip.querySelector(".type");
        objDivType.addEventListener("mousedown", (objEvent) => 
        {
            console.log("click on apply");

            //toggle enabled status
            if (DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false))
            {
                objDivChip.setAttribute(this.arrChipAttributes.disabled, false); //attribute
                objDivChip.classList.remove("disabled"); //css
            }
            else
            {
                objDivChip.setAttribute(this.arrChipAttributes.disabled, true); //attribute
                objDivChip.classList.add("disabled"); //css
            }

            this.#dispatchEventFiltersChanged(objDivChip, "apply");
        }, { signal: objCtrl.signal });  


        //==== text
        const objDivText = objDivChip.querySelector(".chipinnertext");
        objDivText.addEventListener("mousedown", (objEvent) => 
        {            

            //render proper bubble
            this.#renderFilterBubble(objDivChip)

        }, { signal: objCtrl.signal });   


        //===== remove
        const objDivRemove = objDivChip.querySelector(".remove");        
        objDivRemove.addEventListener("click", (objEvent) => //this is a "click" instead of "mousedown", because otherwise another filter will slight under the mouse cursor when removing, triggering their "mousedown"-event. Most likely this will happen with the "new filter+"-chip which will show the new-filters-menu 
        {
            // console.log("clicked on remove");

            //remove event listener
            const objDivText = objDivChip.querySelector(".chipinnertext");
            let sChipText = objDivChip.querySelector(".chipinnertext").textContent;
            if (objDivText)
            {
                sChipText = objDivText.textContent;
                this.#arrChipsAbortControllers[sChipText].abort();
            }

            //remove from DOM
            objDivChip.parentNode.removeChild(objDivChip);

            //dispatch filter-changed-event based on "disabled" attribute --> AFTER being removed from DOM
            if (!DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false))
                this.#dispatchEventFiltersChanged(objDivChip, "remove filter");

           
        }, { signal: objCtrl.signal });    
        

        //add controllers to internal array
        this.#arrChipsAbortControllers[objDivText.textContent] = objCtrl;         
    }

    /**
     * render proper filter bubble
     * 
     * @param {HTMLElement} objDivChip chip object
     */
    #renderFilterBubble(objDivChip)
    {
        const sFilterType = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.filtertype, this.arrFilterTypes.string);

        //toggle visibility OLD bubble  (we render a new one in the code below)
        if (this.#objChipBubble)
        {
            if (this.#objChipBubble.isShowing())
            {
                this.#objChipBubble.hide();
                return;
            }
        }

        //create new abortcontroller if not exists
        if (this.#objChipBubbleAbortController ==  null)
        {
            this.#objChipBubbleAbortController = new AbortController();
        }
        else
        {
            this.#objChipBubbleAbortController.abort();
            this.#objChipBubbleAbortController = new AbortController();
        }    

        switch (sFilterType)
        {               
            case this.arrFilterTypes.boolean:
                this.#renderFilterBubbleBoolean(objDivChip);
                break;
            case this.arrFilterTypes.number:
                this.#renderFilterBubbleNumber(objDivChip);
                break;
            case this.arrFilterTypes.date:
                this.#renderFilterBubbleDate(objDivChip);
                break;
            case this.arrFilterTypes.quicksearch:
                this.#renderFilterBubbleQuicksearch(objDivChip);
                break;
            case this.arrFilterTypes.htmlelement:
                this.#renderFilterHTMLElement(objDivChip, objDivOriginalChip);
                break;
            default:
                this.#renderFilterBubbleString(objDivChip);
        }

        //determine title (the title can be overwritten in the renderbubble() of filter)
        const objTextDiv = objDivChip.querySelector(".chipinnertext");
        if (objTextDiv !== null)
        {
            if (DRComponentsLib.attributeToString(this.#objChipBubble, "title", "") == "")
                this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    
        }    

        //set common elements
        const objBtnApply = this.#objChipBubble.querySelector(".apply");
        objBtnApply.innerHTML = this.sSVGIconApply + this.sTransButtonApply;

        const objLblVisibility = this.#objChipBubble.querySelector(".lblvisibility");
        objLblVisibility.innerHTML = this.sTransLabelVisibility;

        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.label = this.sTransCheckEnabled;

        const objLblValue = this.#objChipBubble.querySelector(".lblvalue");
        if (objLblValue)
            objLblValue.innerHTML = this.sTransLabelValue;                
    

        //show
        this.#objChipBubble.anchorobject = objDivChip;
        this.#objChipBubble.anchorpos = this.#objChipBubble.iPosBottom;            
        this.#objChipBubble.hideonclickoutside = false;//needs to be done after #renderFilterBubbleXXX(), because there is the NEW bubble created
        this.#objChipBubble.show();

        //focus on value field if it exists
        const objEdtValue = this.#objChipBubble.querySelector(".edtvalue");
        if (objEdtValue)
            objEdtValue.focus({ focusVisible: true }); //doesn't work, don't know why
    }

    /**
     * render the bubble: string and add event listeners
     * 
     * @param {HTMLElement} objDivChip 
     */
    #renderFilterBubbleString(objDivChip)
    {
        this.#objChipBubble = this.shadowRoot.querySelector(".stringfilter");
        const objChipText = objDivChip.querySelector(".chipinnertext");     
        
        //update title
        this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    

        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        const objLblComparisonOperator = this.#objChipBubble.querySelector(".comparisonoperator");
        objLblComparisonOperator.innerHTML = this.sTransComparisonOperator;

        const objEqualTo = this.#objChipBubble.querySelector(".equalto");
        objEqualTo.label = this.sTransEqualTo;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.equalto)
            objEqualTo.checked = true;

        const objNotEqualTo = this.#objChipBubble.querySelector(".notequalto");
        objNotEqualTo.label = this.sTransNotEqualTo;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.notequalto)
            objNotEqualTo.checked = true;

        const objLike = this.#objChipBubble.querySelector(".like");
        objLike.label = this.sTransLike;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.like)
            objLike.checked = true;     

        const objNotLike = this.#objChipBubble.querySelector(".notlike");
        objNotLike.label = this.sTransNotLike;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.notlike)
            objNotLike.checked = true;         

        const objEdtValue = this.#objChipBubble.querySelector(".edtvalue");
        objEdtValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.value, "");

        const objBtnApply = this.#objChipBubble.querySelector(".apply");

        //==== add event listeners

        //press enter in value edit box
        objEdtValue.addEventListener("keydown", (objEvent) => 
        {
            if (objEvent.key == "Enter")
                objBtnApply.focus();
        });

        //button: apply
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        

                //comparison operator
                if (objEqualTo.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.equalto);
                if (objNotEqualTo.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.notequalto);
                if (objLike.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.like);
                if (objNotLike.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.notlike);

                //value
                objDivChip.setAttribute(this.arrChipAttributes.value, objEdtValue.value);

                //change values, but this means also: swapping indexes in abortcontroller array (because they are recognized on textContent)
                let objAbrtCtrl = this.#arrChipsAbortControllers[objChipText.textContent]; //temp store abort controller
                delete this.#arrChipsAbortControllers[objChipText.textContent]; //remove from array
                objChipText.innerHTML = '"' + objEdtValue.value + '"'; //update chip text
                this.#arrChipsAbortControllers[objChipText.textContent] = objAbrtCtrl; //add back to array

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }       
        
    }


    /**
     * render the bubble: boolean
     * 
     * @param {HTMLElement} objDivChip 
     */
    #renderFilterBubbleBoolean(objDivChip)
    {
        //select proper filter
        this.#objChipBubble = this.shadowRoot.querySelector(".booleanfilter");

        //update title
        this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    

        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        const objValueTrue = this.#objChipBubble.querySelector(".valuetrue");
        objValueTrue.label = this.sTransValueBoolTrue;
        if (DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.value, true) == true)
            objValueTrue.checked = true;

        const objValueFalse = this.#objChipBubble.querySelector(".valuefalse");
        objValueFalse.label = this.sTransValueBoolFalse;
        if (DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.value, true) == false)
            objValueFalse.checked = true;



        //==== add event listeners
        //button: apply
        const objBtnApply = this.#objChipBubble.querySelector(".apply");
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        

                //comparison operator
                objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.equalto);

                //value
                objDivChip.setAttribute(this.arrChipAttributes.value, "false");//default
                if (objValueTrue.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.value, "true");

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }
        
    }


    /**
     * render the bubble: number
     * 
     * @param {HTMLElement} objDivChip 
     */
    #renderFilterBubbleNumber(objDivChip)
    {
        this.#objChipBubble = this.shadowRoot.querySelector(".numberfilter");
  
        //update title
        this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    

        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        const objLblComparisonOperator = this.#objChipBubble.querySelector(".comparisonoperator");
        objLblComparisonOperator.innerHTML = this.sTransComparisonOperator;

        const objLessEqual = this.#objChipBubble.querySelector(".lessequal");
        objLessEqual.label = this.sTransLessEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.lessequal)
            objLessEqual.checked = true;

        const objGreaterEqual = this.#objChipBubble.querySelector(".greaterequal");
        objGreaterEqual.label = this.sTransGreaterEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.greaterthan)
            objGreaterEqual.checked = true;

        const objEqual = this.#objChipBubble.querySelector(".equal");
        objEqual.label = this.sTransEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.equalto)
            objEqual.checked = true;     

        const objBetween = this.#objChipBubble.querySelector(".between");
        objBetween.label = this.sTransBetween;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.between)
            objBetween.checked = true;         
                
        const objEdtValue = this.#objChipBubble.querySelector(".edtvalue");
        objEdtValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.value, "");
        objEdtValue.thousandseparator = this.#sThousandSeparator;
        objEdtValue.decimalseparator = this.#sDecimalSeparator;

        const objLblEndValue = this.#objChipBubble.querySelector(".lblvalueend");
        objLblEndValue.innerHTML = this.sTransBetweenAnd;
        if (objBetween.checked)
            objLblEndValue.removeAttribute("disabled");
        else
            objLblEndValue.setAttribute("disabled", "true");        

        const objEdtEndValue = this.#objChipBubble.querySelector(".edtvalueend");
        objEdtEndValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.valueend, "");
        objEdtEndValue.thousandseparator = this.#sThousandSeparator;
        objEdtEndValue.decimalseparator = this.#sDecimalSeparator;        
        if (objBetween.checked)
            objEdtEndValue.removeAttribute("disabled");
        else
            objEdtEndValue.setAttribute("disabled", "true");


        const objBtnApply = this.#objChipBubble.querySelector(".apply");

        //==== add event listeners

        //press enter in value edit box
        objEdtValue.addEventListener("keydown", (objEvent) => 
        {
            if (objEvent.key == "Enter")
                objBtnApply.focus();
        });

        //between enables/disables endvalue editbox
        objGreaterEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });

        objLessEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });        

        objEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });    

        objBetween.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", !objBetween.checked);
            objEdtEndValue.setAttribute("disabled", !objBetween.checked);
        });        

        //button: apply
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        

                //comparison operator
                if (objLessEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.lessequal);
                if (objGreaterEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.greaterequal);
                if (objEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.equalto);
                if (objBetween.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.between);

                //values
                objDivChip.setAttribute(this.arrChipAttributes.value, objEdtValue.value);
                objDivChip.setAttribute(this.arrChipAttributes.valueend, objEdtEndValue.value);
                

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }       
    }

    /**
     * render the bubble: date
     * 
     * @param {HTMLElement} objDivChip 
     */
    #renderFilterBubbleDate(objDivChip)
    {
        this.#objChipBubble = this.shadowRoot.querySelector(".datefilter");
  
        //update title
        this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    

        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        const objLblComparisonOperator = this.#objChipBubble.querySelector(".comparisonoperator");
        objLblComparisonOperator.innerHTML = this.sTransComparisonOperator;

        const objLessEqual = this.#objChipBubble.querySelector(".lessequal");
        objLessEqual.label = this.sTransLessEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.lessequal)
            objLessEqual.checked = true;

        const objGreaterEqual = this.#objChipBubble.querySelector(".greaterequal");
        objGreaterEqual.label = this.sTransGreaterEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.greaterthan)
            objGreaterEqual.checked = true;

        const objEqual = this.#objChipBubble.querySelector(".equal");
        objEqual.label = this.sTransEqual;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.equalto)
            objEqual.checked = true;     

        const objBetween = this.#objChipBubble.querySelector(".between");
        objBetween.label = this.sTransPeriod;
        if (DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.comparisonoperator, "") == this.arrComparisonOperators.between)
            objBetween.checked = true;         
                

        const objLblValue = this.#objChipBubble.querySelector(".lblvalue");
        if (objBetween.checked)
            objLblValue.innerHTML = this.sTransPeriodFrom;
        else
            objLblValue.innerHTML = this.sTransLabelValue;


        const objEdtValue = this.#objChipBubble.querySelector(".edtvalue");
        objEdtValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.value, "");
        objEdtValue.phpformat = this.#sPHPDateFormat;
        objEdtValue.firstdayofweek = this.#iFirstDayOfWeek;
        objEdtValue.transday = this.sTransDay;
        objEdtValue.transmonth = this.sTransMonth;
        objEdtValue.transyear = this.sTransYear;
        objEdtValue.transjanuary = this.sTransJanuary;
        objEdtValue.transfebruary = this.sTransFebruary;
        objEdtValue.transmarch = this.sTransMarch;
        objEdtValue.transapril = this.sTransApril;
        objEdtValue.transmay = this.sTransMay;
        objEdtValue.transjune = this.sTransJune;
        objEdtValue.transjuly = this.sTransJuly;
        objEdtValue.transaugust = this.sTransAugust;
        objEdtValue.transseptember = this.sTransSeptember;
        objEdtValue.transoctober = this.sTransOctober;
        objEdtValue.transnovember = this.sTransNovember;
        objEdtValue.transdecember = this.sTransDecember;
        objEdtValue.transmonday = this.sTransMonday;
        objEdtValue.transtuesday = this.sTransTuesday;
        objEdtValue.transwednesday = this.sTransWednesday;
        objEdtValue.transthursday = this.sTransThursday;
        objEdtValue.transfriday = this.sTransFriday;
        objEdtValue.transsaturday = this.sTransSaturday;
        objEdtValue.transsunday = this.sTransSunday;
        objEdtValue.transyesterday = this.iFirstDayOfWeek;
        objEdtValue.transtoday = this.sTransToday;
        objEdtValue.transtomorrow = this.sTransTomorrow;

        const objLblEndValue = this.#objChipBubble.querySelector(".lblvalueend");
        objLblEndValue.innerHTML = this.sTransPeriodTo;
        if (objBetween.checked)
            objLblEndValue.removeAttribute("disabled");
        else
            objLblEndValue.setAttribute("disabled", "true");        

        const objEdtEndValue = this.#objChipBubble.querySelector(".edtvalueend");
        objEdtEndValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.valueend, "");
        objEdtEndValue.phpformat = this.#sPHPDateFormat;
        objEdtEndValue.firstdayofweek = this.#iFirstDayOfWeek;
        objEdtEndValue.transday = this.sTransDay;
        objEdtEndValue.transmonth = this.sTransMonth;
        objEdtEndValue.transyear = this.sTransYear;
        objEdtEndValue.transjanuary = this.sTransJanuary;
        objEdtEndValue.transfebruary = this.sTransFebruary;
        objEdtEndValue.transmarch = this.sTransMarch;
        objEdtEndValue.transapril = this.sTransApril;
        objEdtEndValue.transmay = this.sTransMay;
        objEdtEndValue.transjune = this.sTransJune;
        objEdtEndValue.transjuly = this.sTransJuly;
        objEdtEndValue.transaugust = this.sTransAugust;
        objEdtEndValue.transseptember = this.sTransSeptember;
        objEdtEndValue.transoctober = this.sTransOctober;
        objEdtEndValue.transnovember = this.sTransNovember;
        objEdtEndValue.transdecember = this.sTransDecember;
        objEdtEndValue.transmonday = this.sTransMonday;
        objEdtEndValue.transtuesday = this.sTransTuesday;
        objEdtEndValue.transwednesday = this.sTransWednesday;
        objEdtEndValue.transthursday = this.sTransThursday;
        objEdtEndValue.transfriday = this.sTransFriday;
        objEdtEndValue.transsaturday = this.sTransSaturday;
        objEdtEndValue.transsunday = this.sTransSunday;
        objEdtEndValue.transyesterday = this.iFirstDayOfWeek;
        objEdtEndValue.transtoday = this.sTransToday;
        objEdtEndValue.transtomorrow = this.sTransTomorrow;        
        if (objBetween.checked)
            objEdtEndValue.removeAttribute("disabled");
        else
            objEdtEndValue.setAttribute("disabled", "true");
        


        const objBtnApply = this.#objChipBubble.querySelector(".apply");

        //==== add event listeners

        //press enter in value edit box
        objEdtValue.addEventListener("keydown", (objEvent) => 
        {
            if (objEvent.key == "Enter")
                objBtnApply.focus();
        });

        //between enables/disables endvalue editbox
        objGreaterEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });

        objLessEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });        

        objEqual.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", true);
            objEdtEndValue.setAttribute("disabled", true);
        });    

        objBetween.addEventListener("mousedown", (objEvent) => 
        {
            objLblEndValue.setAttribute("disabled", !objBetween.checked);
            objEdtEndValue.setAttribute("disabled", !objBetween.checked);
        });        

        //button: apply
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        

                //comparison operator
                if (objLessEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.lessequal);
                if (objGreaterEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.greaterequal);
                if (objEqual.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.equalto);
                if (objBetween.checked)
                    objDivChip.setAttribute(this.arrChipAttributes.comparisonoperator, this.arrComparisonOperators.between);

                //values
                console.log(objEdtValue.value, objEdtEndValue.value,"hoinkietoink");
                objDivChip.setAttribute(this.arrChipAttributes.value, objEdtValue.value);
                objDivChip.setAttribute(this.arrChipAttributes.valueend, objEdtEndValue.value);
                

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }       
    }


    /**
     * render the bubble: quicksearch
     * 
     * @param {HTMLElement} objDivChip 
     */
    #renderFilterBubbleQuicksearch(objDivChip)
    {
        this.#objChipBubble = this.shadowRoot.querySelector(".quicksearchfilter");
        const objChipText = objDivChip.querySelector(".chipinnertext");
        
        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        this.#objChipBubble.setAttribute("title", this.sTransQuicksearch);
                
        const objEdtValue = this.#objChipBubble.querySelector(".edtvalue");
        objEdtValue.value = DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.value, "");
        
        const objBtnApply = this.#objChipBubble.querySelector(".apply");

        //==== add event listeners

        //press enter in value edit box
        objEdtValue.addEventListener("keydown", (objEvent) => 
        {
            if (objEvent.key == "Enter")
                objBtnApply.focus();
        });

        //button: apply
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        


                //values
                objDivChip.setAttribute(this.arrChipAttributes.value, objEdtValue.value);  

                //change values, but this means also: swapping indexes in abortcontroller array (because they are recognized on textConten)
                let objAbrtCtrl = this.#arrChipsAbortControllers[objChipText.textContent]; //temp store abort controller
                delete this.#arrChipsAbortControllers[objChipText.textContent]; //remove from array
                objChipText.innerHTML = '"' + objEdtValue.value + '"'; //update chip text
                this.#arrChipsAbortControllers[objChipText.textContent] = objAbrtCtrl; //add back to array

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }       
    }


    /**
     * render the bubble with html element
     * 
     * @param {HTMLElement} objDivChip revamped chip
     * @param {HTMLElement} objDivOriginalChip the original chip
     */
    #renderFilterHTMLElement(objDivChip, objDivOriginalChip)
    {
        this.#objChipBubble = this.shadowRoot.querySelector(".htmlelementfilter");
        const objBtnApply = this.#objChipBubble.querySelector(".apply");
        const objDivHTMLElements = this.#objChipBubble.querySelector(".htmlelementsbody");//look for the div with class "htmlelements"

        //update title
        this.#objChipBubble.setAttribute("title", DRComponentsLib.attributeToString(objDivChip, this.arrChipAttributes.namenice, ""));    

        //values
        const objChkEnabled = this.#objChipBubble.querySelector(".filterenabled");
        objChkEnabled.checked = !DRComponentsLib.attributeToBoolean(objDivChip, this.arrChipAttributes.disabled, false);

        //copy all children of originalchip
        for (let iIndex = 0; iIndex < objDivOriginalChip.children.length; iIndex++)
            objDivHTMLElements.appendChild(objDivOriginalChip.children[iIndex]); //we assign the node to another parent also

        
        //==== add event listeners


        //button: apply
        if (objBtnApply)
        {
            objBtnApply.addEventListener("mousedown", (objEvent) => 
            {
                
                //enabled
                objDivChip.setAttribute(this.arrChipAttributes.disabled, !objChkEnabled.checked);
                if (objChkEnabled.checked)
                {
                    objDivChip.classList.remove("disabled");                            
                }
                else
                {
                    objDivChip.classList.add("disabled");
                }        

                //gather values
                console.log("asdflkjhasdflkjhasdf2222---",  objDivHTMLElements);
                if (objDivHTMLElements)
                {
                    console.log("kom hier", objDivHTMLElements.children[0], objDivHTMLElements.children[0].value);
                    objDivChip.setAttribute(this.arrChipAttributes.value, objDivHTMLElements.children[0].value);
                }
                

                //dispatch event
                this.#dispatchEventFiltersChanged(objBtnApply, "filter conditions changed");

                //hide bubble
                this.#objChipBubble.hide();
            }, { signal: this.#objChipBubbleAbortController.signal });  
        }    
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
     * add filter to filters
     * 
     * @param {string} sSVGPre SVG icon as string
     * @param {string} sText text on chip
     * @param {integer} iFilterIndex database field
     * @param {string} sFilterType filter type
     * @param {HTMLElement} objOriginalChip the original chip in the DOM
     * @return HTMLElement created <div> element representing the chip
     */
    addFilterUI(sSVGPre, sText, iFilterIndex, sFilterType = this.arrFilterTypes.string, bEnabled = false, objOriginalChip = null, bOpenImmediately = false)
    {
        const objParentChip = document.createElement("div"); //create new revamped chip
        
        objParentChip.classList.add("chip");
        objParentChip.classList.add("highlight");
        objParentChip.classList.add("filter");
        if (!bEnabled)
            objParentChip.classList.add("disabled");

        
        objParentChip.setAttribute(this.arrChipAttributes.filtertype, this.#recognizeFilterType(sFilterType));
        objParentChip.setAttribute(this.arrChipAttributes.filterindex, iFilterIndex);
        objParentChip.setAttribute(this.arrChipAttributes.disabled, !bEnabled);         
        objParentChip.setAttribute(this.arrChipAttributes.namenice, sText); //nicename to attribute      

        //add first icon (either filter or search)
        const objPreButton = document.createElement("div");  
        objPreButton.classList.add("chipicon");
        objPreButton.classList.add("highlightinside");
        objPreButton.classList.add("type");
        objPreButton.innerHTML = sSVGPre;  
        objParentChip.appendChild(objPreButton);           

        //add text to div
        const objText = document.createElement("div");
        objText.classList.add("chipinnertext");
        objText.innerHTML = sText;
        objParentChip.appendChild(objText);         

        //add X icon div
        const objXButton = document.createElement("div");  
        objXButton.classList.add("chipicon");
        objXButton.classList.add("highlightinside");
        objXButton.classList.add("remove");
        objXButton.innerHTML = this.sSVGIconRemove;  
        objParentChip.appendChild(objXButton);                    

        //determine the right position to add to DOM
        const objNewFilter = this.shadowRoot.querySelector(".newfilter");
        if (objNewFilter) //the New-filter chip is added via this method too. if it doesn't exist, add element to DOM anyway
            objNewFilter.before(objParentChip); //add
        else
            this.shadowRoot.appendChild(objParentChip); 

        //immediately open bubble
        if (bOpenImmediately)
            this.#renderFilterBubble(objParentChip);
        
        //respond to user
        this.#addEventListenersFilter(objParentChip, objOriginalChip);

        return objParentChip;
    }

    /**
     * checks if filter type is in type list.
     * if not, then default to default-type
     * 
     * @param {string}  
     * @returns {string} key in array
     * 
     */
    #recognizeFilterType(sFilterType)
    {
        //find 
        for (let sKey in this.arrFilterTypes) 
        {
            if (sFilterType == this.arrFilterTypes[sKey])
                return sKey;
        }

        //not found? then pick first
        for (let sKey in this.arrFilterTypes) 
        {
            return sKey;
        }
    }

    /**
     * return filters in JSON string
     */
    getFiltersAsJSON()
    {
        const objJSON = {};
        let objRow = {};
        let iRowIndexJSON = 0;

        for (let iIndex = 0; iIndex < this.shadowRoot.childElementCount; iIndex++)
        {
            if (this.shadowRoot.children[iIndex].classList.contains("filter"))
            {
                objRow = {};
                objRow[this.arrChipAttributes.status] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.status, this.arrFilterStatus.applied);
                objRow[this.arrChipAttributes.filtertype] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.filtertype, this.arrFilterTypes.string);
                objRow[this.arrChipAttributes.filterindex] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.filterindex, "");
                objRow[this.arrChipAttributes.disabled] = DRComponentsLib.attributeToBoolean(this.shadowRoot.children[iIndex], this.arrChipAttributes.disabled, false);
                objRow[this.arrChipAttributes.comparisonoperator] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.comparisonoperator);
                objRow[this.arrChipAttributes.value] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.value);
                objRow[this.arrChipAttributes.valueend] = DRComponentsLib.attributeToString(this.shadowRoot.children[iIndex], this.arrChipAttributes.valueend);
                objRow[this.arrChipAttributes.namenice] = this.shadowRoot.children[iIndex].textContent.trim();

                objJSON[iRowIndexJSON] = objRow;
                iRowIndexJSON++;
            }
        }

        // console.log("getFiltersAsJSON() asdfasdfasdf", JSON.stringify(objJSON));

        return objJSON;
    }

    /**
     * update User Interface
     */
    updateUI()
    {
    }

    /**
     * set filters as JSON string
     */
    // set filtersAsJSON(sJSON)
    // {

    // }


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
    * set format
    */
    set phpdateformat(sFormat)
    {d
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
     * broadcasts "change" event
     * 
     * @param {HTMLElement} objSource 
     * @param {string} sDescription 
     */
    #dispatchEventFiltersChanged(objSource, sDescription)
    {
        // console.log("dispatch event", sDescription);
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
customElements.define("dr-db-filters", DRDBFilters);

