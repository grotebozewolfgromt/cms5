
<?php 
/**
 * dr-paginator.js
 *
 * class to create a paginator that can update live with javascript
 * a paginator divides results from a database in pages
 * 
 * EVENT:
 * dispatches event: "change" when user click on one of the pages or previous/next-buttons
 * 
 * 
 * @author Dennis Renirie
 * 
 * 26 apr 2025 dr-paginator.js created
 */
?>


class DRPaginator extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {     
                display: block;
                text-align: center;

                background-color: light-dark(var(--lightmode-color-drpaginator-background, rgb(255, 255, 255)), var(--darkmode-color-drpaginator-background, rgb(38, 38, 38)));                  
                border-color: light-dark(var(--lightmode-color-drpaginator-border, rgb(234, 234, 234)), var(--darkmode-color-drpaginator-border, rgb(71, 71, 71)));                  
                border-width: 1px;
                border-style: solid;
                border-radius: 5px;
                font-size: 14px;
                user-select: none;
            }      

            div.firstpage,
            div.previouspage,
            div.nextpage,
            div.lastpage,
            div.page,
            div.dots
            {
                display: inline-block;
                padding: 10px;
                padding-left: 15px;                
                padding-right: 15px;                
            }

            div.firstpage,
            div.previouspage,
            div.nextpage,
            div.lastpage,
            div.page
            {              
                cursor: pointer;
            }

            div.firstpage:hover,
            div.previouspage:hover,
            div.nextpage:hover,
            div.lastpage:hover,
            div.page:hover
            {
                background-color: light-dark(var(--lightmode-color-drpaginator-button-background-hover, rgb(234, 234, 234)), var(--darkmode-color-drpaginator-button-background-hover, rgb(71, 71, 71)));                  
            }            
            
            div.firstpage.disabled,
            div.previouspage.disabled,
            div.nextpage.disabled,
            div.lastpage.disabled
            {
                opacity: 0.2;
            }

            div.selected
            {
                background-color: light-dark(var(--lightmode-color-drpaginator-button-background-selected, rgb(234, 234, 234)), var(--darkmode-color-drpaginator-button-background-selected, rgb(71, 71, 71)));
                border-color: light-dark(var(--lightmode-color-drpaginator-button-border-selected, rgb(92, 92, 92)), var(--darkmode-color-drpaginator-button-border-selected, rgb(140, 140, 140)));                  
                border-width: 1px;
                border-style: solid;
            }
            
            div.info
            {
                padding: 10px;
                padding-bottom: 15px;
            }

            /* Media query for mobile devices */
            @media (max-width: 500px) 
            {
                div.removeonmobile
                {
                    display: none;
                }                
            }
        </style>
        <slot></slot>
    `;

    // sSVGNextPage = '<svg fill="currentColor" style="transform: rotate(90deg)" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg>';
    // sSVGPreviousPage = '<svg fill="currentColor" style="transform: rotate(270deg)" viewBox="0 0 96 96" xmlns="http://www.w3.org/2000/svg"><path d="M82.6074,62.1072,52.6057,26.1052a6.2028,6.2028,0,0,0-9.2114,0L13.3926,62.1072a5.999,5.999,0,1,0,9.2114,7.6879L48,39.3246,73.396,69.7951a5.999,5.999,0,1,0,9.2114-7.6879Z"/></svg>';
    // sSVGFirstPage = '<svg fill="currentColor" style="transform: rotate(180deg)" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g data-name="Layer 2" id="Layer_2"><path class="cls-1" d="M16.88,15.53,7,5.66A1,1,0,0,0,5.59,7.07l9.06,9.06-8.8,8.8a1,1,0,0,0,0,1.41h0a1,1,0,0,0,1.42,0l9.61-9.61A.85.85,0,0,0,16.88,15.53Z"/><path class="cls-1" d="M26.46,15.53,16.58,5.66a1,1,0,0,0-1.41,1.41l9.06,9.06-8.8,8.8a1,1,0,0,0,0,1.41h0a1,1,0,0,0,1.41,0l9.62-9.61A.85.85,0,0,0,26.46,15.53Z"/></g></svg>';
    // sSVGLastPage = '<svg fill="currentColor" style="transform: rotate(0deg)" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg"><g data-name="Layer 2" id="Layer_2"><path class="cls-1" d="M16.88,15.53,7,5.66A1,1,0,0,0,5.59,7.07l9.06,9.06-8.8,8.8a1,1,0,0,0,0,1.41h0a1,1,0,0,0,1.42,0l9.61-9.61A.85.85,0,0,0,16.88,15.53Z"/><path class="cls-1" d="M26.46,15.53,16.58,5.66a1,1,0,0,0-1.41,1.41l9.06,9.06-8.8,8.8a1,1,0,0,0,0,1.41h0a1,1,0,0,0,1.41,0l9.62-9.61A.85.85,0,0,0,26.46,15.53Z"/></g></svg>';
    sSVGNextPage        = '<svg fill="currentColor" style="transform: rotate(0deg)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19.7036 12L11.2125 3.27302C10.9236 2.97614 10.9301 2.50131 11.227 2.21246C11.5239 1.9236 11.9987 1.93011 12.2875 2.22698L21.2875 11.477C21.5708 11.7681 21.5708 12.2319 21.2875 12.523L12.2875 21.773C11.9987 22.0699 11.5239 22.0764 11.227 21.7875C10.9301 21.4987 10.9236 21.0239 11.2125 20.727L19.7036 12Z"/></svg>';
    sSVGPreviousPage    = '<svg fill="currentColor" style="transform: rotate(180deg)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19.7036 12L11.2125 3.27302C10.9236 2.97614 10.9301 2.50131 11.227 2.21246C11.5239 1.9236 11.9987 1.93011 12.2875 2.22698L21.2875 11.477C21.5708 11.7681 21.5708 12.2319 21.2875 12.523L12.2875 21.773C11.9987 22.0699 11.5239 22.0764 11.227 21.7875C10.9301 21.4987 10.9236 21.0239 11.2125 20.727L19.7036 12Z"/></svg>';
    sSVGFirstPage       = '<svg fill="currentColor" style="transform: rotate(180deg)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.25 3C18.6297 3 18.9435 3.28215 18.9932 3.64823L19 3.75V20.25C19 20.6642 18.6642 21 18.25 21C17.8703 21 17.5565 20.7178 17.5068 20.3518L17.5 20.25V3.75C17.5 3.33579 17.8358 3 18.25 3ZM5.21967 3.21967C5.48594 2.9534 5.9026 2.9292 6.19621 3.14705L6.28033 3.21967L14.5303 11.4697C14.7966 11.7359 14.8208 12.1526 14.6029 12.4462L14.5303 12.5303L6.28033 20.7803C5.98744 21.0732 5.51256 21.0732 5.21967 20.7803C4.9534 20.5141 4.9292 20.0974 5.14705 19.8038L5.21967 19.7197L12.9393 12L5.21967 4.28033C4.92678 3.98744 4.92678 3.51256 5.21967 3.21967Z"/></svg>';
    sSVGLastPage        = '<svg fill="currentColor" style="transform: rotate(0deg)" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M18.25 3C18.6297 3 18.9435 3.28215 18.9932 3.64823L19 3.75V20.25C19 20.6642 18.6642 21 18.25 21C17.8703 21 17.5565 20.7178 17.5068 20.3518L17.5 20.25V3.75C17.5 3.33579 17.8358 3 18.25 3ZM5.21967 3.21967C5.48594 2.9534 5.9026 2.9292 6.19621 3.14705L6.28033 3.21967L14.5303 11.4697C14.7966 11.7359 14.8208 12.1526 14.6029 12.4462L14.5303 12.5303L6.28033 20.7803C5.98744 21.0732 5.51256 21.0732 5.21967 20.7803C4.9534 20.5141 4.9292 20.0974 5.14705 19.8038L5.21967 19.7197L12.9393 12L5.21967 4.28033C4.92678 3.98744 4.92678 3.51256 5.21967 3.21967Z"/></svg>';
    #objAbrtCtrlKeyboard = null;
    #objAbrtCtrlBtnPreviousNext = null; //abort controllers for first,last, previous and next page
    #objAbrtCtrlBtnPages = null;
    #iPreviousPageNumber = 0;
    #iNextPageNumber = 0;
    #iCurrentPage = 0;
    #iTotalResults = 0;
    #iRecordsPerPage = 0;
    #iPageCount = 0; //page count is also last page number ;)
    #iDisplayMaxPages = 8; //display a maximum of 8 pages at a time. The remaining pages are displayed with "...". This prevents showing 100 pages when you have 100 pages, instead it shows only 8
    #arrDivPages = []; //array of <div> objects that represent pages in paginator
    #objDivInfo = null; //<div> object that contains information: "Page X of Y"
    #objDivPreviousPage = null; //<div> object that contains previous page
    #objDivNextPage = null; //<div> object that contains next page
    #objDivFirstPage = null; //<div> object that contains firstpage-button (left of the previous button)
    #objDivLastPage = null; //<div> object that contains lastpage-button (right of the next button)
    #bConnectedCallbackHappened = false;
        

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objAbrtCtrlBtnPreviousNext = new AbortController();
        this.#objAbrtCtrlBtnPages = new AbortController();
        this.#objAbrtCtrlKeyboard = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRPaginator.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

    }    

    #readAttributes()
    {
        this.#iPreviousPageNumber = DRComponentsLib.attributeToInt(this, "previouspagenumber", this.#iPreviousPageNumber);
        this.#iNextPageNumber = DRComponentsLib.attributeToInt(this, "nextpagenumber", this.#iNextPageNumber);
        this.#iCurrentPage = DRComponentsLib.attributeToInt(this, "currentpage", this.#iCurrentPage);
        this.#iTotalResults = DRComponentsLib.attributeToInt(this, "totalresults", this.#iTotalResults);
        this.#iRecordsPerPage = DRComponentsLib.attributeToInt(this, "recordsperpage", this.#iRecordsPerPage);
        this.#iPageCount = DRComponentsLib.attributeToInt(this, "pagecount", this.#iPageCount);
        this.#iDisplayMaxPages = DRComponentsLib.attributeToInt(this, "displaymaxpages", this.#iDisplayMaxPages);
    }


    /**
     * is current page the first page?
     * 
     * @return {boolean}
     */
    isFirstPage()
    {
        if (this.#iTotalResults == 0)
            return true;

        return (this.#iCurrentPage == 1);
    }

    /**
     * is current page the last page?
     * 
     * @return {boolean}
     */
    isLastPage()
    {
        if (this.#iTotalResults == 0)
            return true;

        return (this.#iCurrentPage == Math.ceil(this.#iTotalResults / this.#iRecordsPerPage));
    }

    /**
     * is current page in the beginning of pages in paginator?
     * 
     * difference with isFirstPage():
     * page 3 for example is in the beginning of pages, but is not the first page.
     * 
     * 
     * when you have 100 pages, you don't want to display all 100 pages, but shorten it to for example 8 (number is stored in iDisplayMaxPages)
     * this is a helper function determines where the dots (...) need to be placed
     * 
     * @param {integer} iOffset when you have 100 pages and your maxpages is 8 you want to add 1x dots (...). use iOffset = 1
     * @return {boolean} returns true when: 1) current page is in the first iDisplayMaxPages pages 2) when there are less pages than iDisplayMaxPages
     */
    isBeginning(iOffset)
    {

        if (this.#iPageCount > (this.#iDisplayMaxPages - iOffset))
        {
            // return (this.#iCurrentPage <= this.#iDisplayMaxPages - iOffset); //==> works!
            return (this.#iCurrentPage <= Math.ceil((this.#iDisplayMaxPages - iOffset) / 2) + iOffset); //==> we start to scroll sooner
        }
        else
            return true;
    }

    /**
     * is current page in the end of pages in paginator?
     * 
     * difference with isLastPage() when there are 100 pages:
     * page 98 for example is in the end of pages, but is not the last page.
     * 
     * 
     * when you have 100 pages, you don't want to display all 100 pages, but shorten it to for example 8 (number is stored in iDisplayMaxPages)
     * this is a helper function determines where the dots (...) need to be placed
     * 
     * @param {integer} iOffset when you have 100 pages and your maxpages is 8 you want to add 1x dots (...). use iOffset = 1
     * @return {boolean} returns true when: 1) current page is in the last iDisplayMaxPages pages 2) when there are less pages than iDisplayMaxPages
     */
    isEnd(iOffset)
    {
        if (this.#iPageCount > (this.#iDisplayMaxPages - iOffset))
        {
            //does current page fall within the last iDisplayMaxPages pages?
            return (this.#iCurrentPage > (this.#iPageCount - this.#iDisplayMaxPages + iOffset));
        }
        else
            return true;
    }

    /**
     * is current page NOT in the first few page and not in the last few pages in paginator?
     * 
     * 
     * when you have 100 pages, you don't want to display all 100 pages, but shorten it to for example 8 (number is stored in iDisplayMaxPages)
     * this is a helper function determines where the dots (...) need to be placed
     * 
     * @param {integer} iOffset when you have 100 pages and your maxpages is 8 you want to add 1x dots (...). use iOffset = 1
     * @return {boolean} returns true when: 1) current page is in the middle of paginator pages 2) when there are less pages than iDisplayMaxPages
     */    
    isMiddle(iOffset)
    {
        iOffset = Math.round(iOffset / 2); //when offset is 2: 1 for beginning + 1 for end

        //when current page is not in the first few pages and not in the last few pages, it is in the middle
        if ((!this.isBeginning(iOffset)) && (!this.isEnd(iOffset)))
            return true;

        //when current page is in the beginning and in the end, it's also in the middle
        if ((this.isBeginning(iOffset)) && (this.isEnd(iOffset)))
            return true;

        return false;
    }

    populate()
    {
        //remove event listeners pages
        if (this.#objAbrtCtrlBtnPreviousNext)
            this.#objAbrtCtrlBtnPreviousNext.abort();
        this.#objAbrtCtrlBtnPreviousNext = new AbortController();

        //first page
        this.#objDivFirstPage = document.createElement("div");
        this.#objDivFirstPage.className = "firstpage";
        this.shadowRoot.appendChild(this.#objDivFirstPage);

        //previous page
        this.#objDivPreviousPage = document.createElement("div");
        this.#objDivPreviousPage.className = "previouspage";
        this.shadowRoot.appendChild(this.#objDivPreviousPage);

        //individual pages
        //this.populatePages(); --> done in updateUI()

        //next page
        this.#objDivNextPage = document.createElement("div");
        this.#objDivNextPage.className = "nextpage";
        this.shadowRoot.appendChild(this.#objDivNextPage);

        //last page
        this.#objDivLastPage = document.createElement("div");
        this.#objDivLastPage.className = "lastpage";
        this.shadowRoot.appendChild(this.#objDivLastPage);        

        //extra information
        this.#objDivInfo = document.createElement("div");
        this.#objDivInfo.className = "info";
        this.shadowRoot.appendChild(this.#objDivInfo);


        this.updateUI();
    }

    populatePages()
    {
        let objDiv = null;
        let iStartPage = 1;
        let iEndPage = this.#iPageCount;

        //remove event listeners pages
        if (this.#objAbrtCtrlBtnPages)
            this.#objAbrtCtrlBtnPages.abort();
        this.#objAbrtCtrlBtnPages = new AbortController();

        //remove existing pages from UI
        if (this.#arrDivPages.length > 0)
        {
            for (let iIndex = 0; iIndex < this.#arrDivPages.length; iIndex++) 
                this.shadowRoot.removeChild(this.#arrDivPages[iIndex]);
        }
        this.#arrDivPages.length = 0; //clear array

        //shrink the amount of pages and add nodes with dots (...)
        if (this.#iPageCount > this.#iDisplayMaxPages) //max pages exceeded
        {
            if (this.isBeginning(1))
            {
                iEndPage = this.#iDisplayMaxPages -1;//-1 because we add dots (...)
            }

            if (this.isMiddle(2))
            {
                iStartPage = parseInt(this.#iCurrentPage) - Math.floor(this.#iDisplayMaxPages / 2) + 1; //we take floor, in endpage we take ceil; -1 because we add dots
                iEndPage = parseInt(this.#iCurrentPage) + Math.ceil(this.#iDisplayMaxPages / 2) -2; //we take ceil, in startpage we take floor; -2 because we add dots
            }

            if (this.isEnd(1))
            {
                iStartPage = this.#iPageCount - this.#iDisplayMaxPages +2; //+2 because we add dots (...) and start at page 1 instead of 0
            }
        }

        //add pages to UI
        for (let iPage = iStartPage; iPage <= iEndPage; iPage++) //page numbering starts at 1 instead of 0
        {
            objDiv = document.createElement("div");
            objDiv.className = "page";
            objDiv.textContent = iPage;
            objDiv.setAttribute("value", iPage);
            objDiv.classList.add("removeonmobile");

            if (iPage == this.#iCurrentPage)
                objDiv.classList.add("selected");

            //make clickable
            this.addEventListenersPage(objDiv, iPage);

            this.#arrDivPages.push(objDiv);
            this.#objDivNextPage.before(objDiv);
        }


        //add dots (...) to UI
        //(we can't do this earlier because elements are added, so when I do a before() then other elements do also a before() and then it isn't before anymore)
        if (this.#iPageCount > this.#iDisplayMaxPages) //max pages exceeded
        {
            if (this.isBeginning(1))
            {
                this.#objDivNextPage.before(this.#renderDivDots());
            }

            if (this.isMiddle(2))
            {
                this.#objDivNextPage.before(this.#renderDivDots());
                this.#objDivPreviousPage.after(this.#renderDivDots());
            }

            if (this.isEnd(1))
            {
                this.#objDivPreviousPage.after(this.#renderDivDots());                
            }
        }

    }

    /**
     * renders a div with dots
     * 
     * @returns {HTMLElement}
     */
    #renderDivDots()
    {
        const objDiv = document.createElement("div");
        objDiv.className = "dots";
        objDiv.textContent = "...";
        objDiv.classList.add("removeonmobile");
        // objDiv.setAttribute("value", iPage);


        this.#arrDivPages.push(objDiv);

        return objDiv;
    }

    /**
     * attach event listenres
     */
    addEventListeners()
    {
        //KEYUP ==> you can't use left and right in an editbox like quicksearch
        // document.addEventListener("keyup", (objEvent)=>
        // {
        //     switch (objEvent.key) 
        //     {
        //         case "ArrowLeft":
        //             if (this.isFirstPage())
        //             {
        //                 this.#iCurrentPage--;
        //                 this.updateUI();
        //             }
        //             break;
        //         case "ArrowRight":
        //             if (this.isLastPage())
        //             {
        //                 this.#iCurrentPage++;
        //                 this.updateUI();
        //             }
        //             break;
        //     }
        // }, { signal: this.#objAbrtCtrlKeyboard.signal });           

        //CLICK on buttons
        this.#objDivFirstPage.addEventListener("mousedown", (objEvent)=>
        {
            this.#dispatchEventPageChanged(this.#objDivFirstPage, 1, "clicked in first button");
        }, { signal: this.#objAbrtCtrlBtnPreviousNext.signal }); 

        this.#objDivPreviousPage.addEventListener("mousedown", (objEvent)=>
        {
            this.#dispatchEventPageChanged(this.#objDivPreviousPage, this.#iPreviousPageNumber, "clicked in previous button");
        }, { signal: this.#objAbrtCtrlBtnPreviousNext.signal });             

        this.#objDivNextPage.addEventListener("mousedown", (objEvent)=>
        {
            this.#dispatchEventPageChanged(this.#objDivNextPage, this.#iNextPageNumber, "clicked in next button");
        }, { signal: this.#objAbrtCtrlBtnPreviousNext.signal });     
        
        this.#objDivLastPage.addEventListener("mousedown", (objEvent)=>
        {
            this.#dispatchEventPageChanged(this.#objDivLastPage, this.#iPageCount, "clicked in last button");
        }, { signal: this.#objAbrtCtrlBtnPreviousNext.signal });             
    
    }

    /**
     * add event listeners page buttons
     * 
     * @param {HTMLElement} objSource html element to add event listener to
     * @param {integer} iPageNo number of page
     */
    addEventListenersPage(objSource, iPageNo)
    {
        objSource.addEventListener("mousedown", (objEvent)=>
        {
            this.#dispatchEventPageChanged(objSource, iPageNo, "clicked on page " + iPageNo);
        }, { signal: this.#objAbrtCtrlBtnPages.signal }); 
    }

    updateUI()
    {
        //first button
        this.#objDivFirstPage.innerHTML = "<dr-icon-spinner>" + this.sSVGFirstPage + "</dr-icon-spinner>";
        this.#objDivFirstPage.setAttribute("value", 1);
        this.#objDivFirstPage.classList.remove("disabled");
        if (this.isFirstPage())
            this.#objDivFirstPage.classList.add("disabled");


        //previous button
        this.#objDivPreviousPage.innerHTML = "<dr-icon-spinner>" + this.sSVGPreviousPage + "</dr-icon-spinner>";
        this.#objDivPreviousPage.setAttribute("value", this.#iPreviousPageNumber);
        this.#objDivPreviousPage.classList.remove("disabled");
        if (this.isFirstPage())
            this.#objDivPreviousPage.classList.add("disabled");

        //create pages
        this.populatePages();

        //next button
        this.#objDivNextPage.innerHTML =  "<dr-icon-spinner>" + this.sSVGNextPage + "</dr-icon-spinner>";
        this.#objDivNextPage.setAttribute("value", this.#iNextPageNumber);
        this.#objDivNextPage.classList.remove("disabled");
        if (this.isLastPage())
            this.#objDivNextPage.classList.add("disabled");


        //last button
        this.#objDivLastPage.innerHTML = "<dr-icon-spinner>" + this.sSVGLastPage + "</dr-icon-spinner>";
        this.#objDivLastPage.setAttribute("value", this.#iPageCount);
        this.#objDivLastPage.classList.remove("disabled");
        if (this.isLastPage())
            this.#objDivLastPage.classList.add("disabled");

        //update info
        let iStart = ((this.#iCurrentPage * this.#iRecordsPerPage) - this.#iRecordsPerPage +1);
        let iEnd = (this.#iCurrentPage * this.#iRecordsPerPage);
        this.#objDivInfo.textContent = iStart + " - " + iEnd + " (" + this.#iTotalResults + ")";//X-Y (Z)
    }

    /** 
     * get internal value 
    */
    get previouspagenumber()
    {
        return this.#iPreviousPageNumber;
    }

    /** 
     * set internal value 
    */
    set previouspagenumber(iValue)
    {
        this.#iPreviousPageNumber = parseInt(iValue);
    }

    /** 
     * get internal value 
    */
    get nextpagenumber()
    {
        return this.#iNextPageNumber;
    }

    /** 
     * set internal value 
    */
    set nextpagenumber(iValue)
    {
        this.#iNextPageNumber = parseInt(iValue);
    }

    /** 
     * get internal value 
    */
    get currentpage()
    {
        return this.#iCurrentPage;
    }
    
    /** 
     * set internal value 
    */
    set currentpage(iValue)
    {
        this.#iCurrentPage = parseInt(iValue);
    }


    /**
     */
    get totalresults()
    {
        return this.#iTotalResults;
    }

    /**
     */
    set totalresults(iValue)
    {
        this.#iTotalResults =  parseInt(iValue);
    }        

    /** 
     * get internal value
    */
    get recordsperpage()
    {
        return this.#iRecordsPerPage;
    }    

    /** 
     * set internal value
    */
    set recordsperpage(iValue)
    {
        this.#iRecordsPerPage =  parseInt(iValue);
    }   

    /** 
     * get internal value
    */
    get pagecount()
    {
        return this.#iPageCount;
    }    

    /** 
     * set internal value
    */
    set pagecount(iValue)
    {
        this.#iPageCount = parseInt(iValue);
    }    


    /**
     * 
     * @param {HTMLElement} objSource 
     * @param {integer} iPageNo 
     * @param {string} sDescription 
     */
    #dispatchEventPageChanged(objSource, iPageNo, sDescription)
    {
        this.dispatchEvent(new CustomEvent("change",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                page: iPageNo,
                description: sDescription
            }
        }));
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbrtCtrlKeyboard.abort();
        this.#objAbrtCtrlBtnPages.abort();
        this.#objAbrtCtrlBtnPreviousNext.abort();
    }


    static get observedAttributes() 
    {
        return ["previouspagenumber", "nextpagenumber", "currentpage", "totalresults", "recordsperpage", "pagecount"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        this.#readAttributes();

        // if (this.#bConnectedCallbackHappened)
        //     this.updateUI();        
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
        if (this.#objAbrtCtrlBtnPreviousNext.signal.aborted)
            this.#objAbrtCtrlBtnPreviousNext = new AbortController();

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
customElements.define("dr-paginator", DRPaginator);