
<?php 
/**
 * dr-tabsheets.js 
 *
 * class that represents tabs
 * This component needs a divs with tab contents in the DOM as input
 * 
 * FEATURES
 * -load URL in tabsheet
 * -dispatches event "changed" when tabsheet has changed
 * -when hovering with mouse shows hint
 * -tabsheets scroll when there is not enough room on screen
 * 
 * WARNING
 * - SHADOWDOM ISN'T USED, but actual DOM instead!!!!! The reason is, that we want to have 100% control over the looks with CSS from the outside so you can integrate is EVERYWHERE
 * - this class adds buttons for tabsheets automatically when attribute "type" = buttons
 * 
 * 
 * DISPATCHES EVENT:
 * "change" when tabsheet is changed
 * 
 * 
 * ATTRIBUTES
 * "type" (parent)       : string    : navigation type buttons, combobox or custom, see this.arrTypes for the exact values
 * "label" (child)       : string    : text displayed on button that activates tabsheet
 * "description" (child) : string    : text displayed when hovered over the tabsheet button with the mouse
 * 
 * DEPENDENCIES:
 * - DRComponentsLib
 * - <dr-popover>
 *  
 * EXAMPLES:
 * <dr-tabsheets type="buttons">
    <div label="tab1" description="tab1 is for doing things" class="active">
        tab1
    </div>
    <div label="tab2" description="tab2 does other stuff">
        tab2
    </div>
    <div label="tab3" description="tab3 does something completely different">
        tab3
    </div>
    <div label="tab4" description="loads html page" url="mysite.com/webpage.html">
        text is replaced by contents of page
    </div>
</dr-tabsheets>


    EXAMPLE CSS:

dr-tabsheets
{              

    display: grid;
    grid-template-rows: 25px minmax(25px, 200px); 
}             

dr-tabsheets .tablistcontainer 
{

    overflow: hidden;
    position: relative; 

dr-tabsheets .tablistcontainer svg
{   
    width: 16px;
    height: 16px;
    margin-left: 10px;
    margin-right: 10px;
}



dr-tabsheets .tablistcontainer .tablist
{
    max-width: 100%;
    display: flex;
    overflow-x: scroll;
    overflow-y: hidden;
    -ms-overflow-style: none; 
    scrollbar-width: none; 
    scroll-behavior: smooth;    
}

dr-tabsheets .tablistcontainer .tablist div
{
    display: inline-block;
    
    border-width: 1px;
    border-bottom: 0px;
    border-style: solid;
    border-color: light-dark(var(--lightmode-color-drtabsheets-border, rgb(183, 183, 183)), var(--darkmode-color-drtabsheets-border, rgb(103, 103, 103)));
    background-color: light-dark(var(--lightmode-color-drtabsheets-button-background, rgb(236, 236, 236)), var(--darkmode-color-drtabsheets-button-background, rgba(71, 71, 71, 0)));
    margin: 0px;
    padding: 5px;
    padding-left: 10px;
    padding-right: 10px;
    cursor: pointer;
    border-top-left-radius: 5px;                     
    border-top-right-radius: 5px;                  
    opacity: 0.6;

    user-select: none;
    white-space: nowrap;

    font-size: 11px; 
}


dr-tabsheets .tablistcontainer div.scrollleft,
dr-tabsheets .tablistcontainer div.scrollright
{
    background-color: blue;
    position: absolute;
    height: 100%;
    width: 50px;
    top: 0;
    display: none;
    align-items: center;
    padding: 0 0px;    
    cursor: pointer;
}


dr-tabsheets .tablistcontainer div.scrollleft.active,
dr-tabsheets .tablistcontainer div.scrollright.active
{
    display: flex; 

}

dr-tabsheets .tablistcontainer div.scrollright
{
    right: 0;
    background:linear-gradient(to left, light-dark(var(--lightmode-color-drtabsheets-tabsheet-background, rgb(255, 255, 255)), var(--darkmode-color-drtabsheets-tabsheet-background, rgb(43, 43, 43))) 20%, transparent);
    justify-content: flex-end;


}

dr-tabsheets .tablistcontainer div.scrollleft
{
    left: 0px;
    background:linear-gradient(to right, light-dark(var(--lightmode-color-drtabsheets-tabsheet-background, rgb(255, 255, 255)), var(--darkmode-color-drtabsheets-tabsheet-background, rgb(43, 43, 43))) 20%, transparent);
}




dr-tabsheets .tablistcontainer .tablist div.active
{
    background-color: light-dark(var(--lightmode-color-drtabsheets-tabsheet-background, rgb(255, 255, 255)), var(--darkmode-color-drtabsheets-tabsheet-background, rgb(43, 43, 43)));
    transform: translate3d(0, 2px, 0);
    opacity: 1;
}



dr-tabsheets .tabcontent div
{
    box-sizing: border-box;
    border-width: 1px;
    border-style: solid;
    border-color: light-dark(var(--lightmode-color-drtabsheets-border, rgb(183, 183, 183)), var(--darkmode-color-drtabsheets-border, rgb(103, 103, 103)));
    background-color: light-dark(var(--lightmode-color-drtabsheets-tabsheet-background, rgb(255, 255, 255)), var(--darkmode-color-drtabsheets-tabsheet-background, rgb(43, 43, 43)));
    padding: 10px;
    border-radius: 5px;
    border-top-left-radius: 0px;     
    height: 100%;
    overflow: scroll;
}

dr-tabsheets .tabcontent div
{
    display: none;
}

dr-tabsheets .tabcontent div.active
{
    display: block;
}         





   @todo drag tabsheets
   @todo andere elementen zoals combobox toepassen
 * 
 * @author Dennis Renirie
 * 6 jun 2025 dr-tabsheets.js created
 */
?>


class DRTabsheets extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
                /* box-sizing: border-box;
                width: 100%;*/
            }             
  
        </style>
        <slot></slot>
    `;

    sSVGRight = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path></svg>';
    sSVGLeft = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" transform="rotate(180)" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"></path></svg>';

    #bDisabled = false;
    #arrTabs = []; //array of tabs (=div objects). This is what we work with internally. This allows us to dynamically add tabsheets with js code, instead of doing it only via the DOM
    #arrButtons = []; //array of buttons that control visibility of tabs
    arrTypes = {buttons: "buttons", combobox: "combobox", custom: "custom"} //types of navigation between tabsheets. Buttons is default, combobox (each item in combobox represents tabsheet), custom (assign your own custom buttons)
    #sType = this.arrTypes.buttons; //stores the type of navigation

    #objAbortController = null; //abort controller for the GUI of this component itself (like keypresses)
    #objAbortControllerButtons = null; //abort controller for each individual tab button
    #objDIVTabListContainer = null; //the container contains the tablist + the scroll-left button + scroll-right-button
    #objDIVTabList = null;
    #objDIVScrollLeft = null;
    #objDIVScrollRight = null;
    #objDIVTabContent = null;
    #objPopoverHint = null;


    #bConnectedCallbackHappened = false;    
     

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objAbortController = new AbortController();
        this.#objAbortControllerButtons = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRTabsheets.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
    }    

    #readAttributes()
    {
        this.#bDisabled = DRComponentsLib.attributeToBoolean(this, "disabled", this.#bDisabled);
        this.#sType = DRComponentsLib.attributeToString(this, "type", this.#sType);

        //make focussable
        this.setAttribute("tabindex", 0);   
    }


    populate()
    {
        //copy items from DOM to internal tab-array
        if (this.children.length > 0)
        {
            for (let iIndex = 0; iIndex < this.children.length; iIndex++)
            {
                this.#arrTabs.push(this.children[iIndex].cloneNode(true));
            }
        }

        //clean 
        this.innerHTML = "";

        //==== create divs
        this.#objDIVTabListContainer = document.createElement("div");
        this.#objDIVTabListContainer.classList.add("tablistcontainer");
        this.appendChild(this.#objDIVTabListContainer);

        //buttons
        this.#objDIVTabList = document.createElement("div");
        this.#objDIVTabList.classList.add("tablist");
        this.#objDIVTabListContainer.appendChild(this.#objDIVTabList);
        
        //div tab content
        this.#objDIVTabContent = document.createElement("div");        
        this.#objDIVTabContent.classList.add("tabcontent");
        this.appendChild(this.#objDIVTabContent);

        //=== create tabs
        if (this.children.length > 0)
        {
            for (let iIndex = 0; iIndex < this.#arrTabs.length; iIndex++)
            {
                //add button (based on tabs)
                if (this.#sType == this.arrTypes.buttons)
                {
                    let objButton = document.createElement("div");
                    this.#arrButtons.push(objButton);
                    objButton.innerHTML = DRComponentsLib.attributeToString(this.#arrTabs[iIndex], "label", "[untitled]") + "<dr-icon-spinner></dr-icon-spinner>";
                    if (this.#arrTabs[iIndex].classList.contains("active"))
                    {
                        objButton.classList.add("active");
                        objButton.setAttribute("tabindex", 1);
                    }
                    else
                    {
                        objButton.setAttribute("tabindex", -1);
                    }
                    this.addEventListenersButton(objButton, this.#arrTabs[iIndex]);
                    this.#objDIVTabList.appendChild(objButton);
                }
                
                //add tab itself
                this.#objDIVTabContent.appendChild(this.#arrTabs[iIndex]);
            }
        }

        //add button scrollers
        if (this.#sType == this.arrTypes.buttons)
        {
            //scrollers
            this.#objDIVScrollLeft = document.createElement("div");
            this.#objDIVScrollLeft.classList.add("scrollleft");
            this.#objDIVScrollLeft.innerHTML = this.sSVGLeft;
            this.#objDIVTabListContainer.appendChild(this.#objDIVScrollLeft, this.#objDIVTabListContainer.firstChild);

            this.#objDIVScrollRight = document.createElement("div");
            this.#objDIVScrollRight.classList.add("scrollright");
            this.#objDIVScrollRight.innerHTML = this.sSVGRight;
            this.#objDIVTabListContainer.appendChild(this.#objDIVScrollRight);
        }

        //if no tab is selected, then select the first one
        let objSelected = this.#objDIVTabList.querySelector(".active");
        if ((objSelected === null) && (this.#arrButtons.length > 0))
        {
            this.#arrButtons[0].classList.add("active");
            this.#arrTabs[0].classList.add("active");
        }


        //update UI
        this.updateUI();
    }

    /**
     * shows and hides scrollbar icons of tablist with buttons (<) (>)
     */
    #manageScrollbarIcons()
    {

        if (this.#objDIVTabList.scrollLeft >= 20)
        {
            this.#objDIVScrollLeft.classList.add("active");
        }
        else
        {
            this.#objDIVScrollLeft.classList.remove("active");
        }

        let iMaxScrollValue = this.#objDIVTabList.scrollWidth - this.#objDIVTabList.clientWidth - 20;

        if (this.#objDIVTabList.scrollLeft >= iMaxScrollValue)
        {
            this.#objDIVScrollRight.classList.remove("active");
        }
        else
        {
            this.#objDIVScrollRight.classList.add("active");
        }
    }

    /**
     * attach event listeners on entire tabsheet element
     */
    addEventListeners()
    {
        if ((this.#objDIVScrollRight) && (this.#objDIVScrollLeft))
        {

            this.#objDIVScrollRight.addEventListener("click", () => 
            {
                this.#objDIVTabList.scrollLeft += 150;
                this.#manageScrollbarIcons();
            }, { signal: this.#objAbortController.signal });


            this.#objDIVScrollLeft.addEventListener("click", () => 
            {
                this.#objDIVTabList.scrollLeft -= 150;
                this.#manageScrollbarIcons();
            }, { signal: this.#objAbortController.signal });


            this.#objDIVTabList.addEventListener("scroll", ()=>
            {
                this.#manageScrollbarIcons();
            }, { signal: this.#objAbortController.signal });    
            
            /**
             * when window resizes, so may change what needs to be scrolled
             */
            window.addEventListener("resize", (objEvent) => 
            {
                this.#manageScrollbarIcons();
            }, { signal: this.#objAbortController.signal });          
        }
    }

    /**
     * attach event listeners for tab-buttons
     */
    addEventListenersButton(objButtonTab, objDivTabsheetContent)
    {
        //MOUSE CLICK
        objButtonTab.addEventListener("mousedown", (objEvent)=>
        {       
            this.activateTab(objButtonTab, objDivTabsheetContent);
        }, { signal: this.#objAbortControllerButtons.signal });


        //KEYDOWN
        objButtonTab.addEventListener("keydown", (objEvent)=>
        {   
            let objTab = null;

            switch(objEvent.key)
            {
                case "ArrowLeft":     
                    this.activateTabNext(objDivTabsheetContent, false);
                    objEvent.preventDefault();
                    break;
                case "ArrowRight":
                    this.activateTabNext(objDivTabsheetContent, true);
                    objEvent.preventDefault();
                    break;
            }

        }, { signal: this.#objAbortControllerButtons.signal });

        //HOVER mouse
        objButtonTab.addEventListener("mouseover", (objEvent)=>
        {   
            if (this.#objPopoverHint !== null)
                this.#objPopoverHint.hide();

            let sHint = DRComponentsLib.attributeToString(objDivTabsheetContent, "description");
            
            if (sHint != "")
            {
                this.#objPopoverHint = new DRPopover();
                this.#objPopoverHint.showtitle = false;
                this.#objPopoverHint.showcloseicon = false;
                this.#objPopoverHint.removeFromDOMOnHide = true;
                this.#objPopoverHint.anchorobject = objButtonTab;
                objButtonTab.parentElement.appendChild(this.#objPopoverHint);
                this.#objPopoverHint.innerHTML = sHint;
                this.#objPopoverHint.show();
            }

        }, { signal: this.#objAbortControllerButtons.signal });

        //HOVER mouse
        objButtonTab.addEventListener("mouseleave", (objEvent)=>
        {   
            if (this.#objPopoverHint !== null)
                this.#objPopoverHint.hide();
        }, { signal: this.#objAbortControllerButtons.signal });        
        
    }

    /**
     * returns the next or previous tab object of the current one
     * 
     * @param {HTMLElement} objCurrentTab
     * @param {boolean} bNext true=next, false, previous
     * @returns {HTMLElement}
     */
    activateTabNext(objCurrentTab, bNext)
    {
        let iIndexTab = 0;
        iIndexTab = this.#arrTabs.indexOf(objCurrentTab);

        //not found then return
        if (iIndexTab == -1)
            return objCurrentTab;

        //seek next or previous
        if (bNext)
            iIndexTab++;
        else
            iIndexTab--;

        //correct boundaries
        if (iIndexTab >= this.#arrTabs.length)
            iIndexTab = 0; //wrap around to the beginning
        if (iIndexTab < 0)
            iIndexTab = this.#arrTabs.length-1; //wrap around to las item

        if (this.#sType == this.arrTypes.buttons)
            this.activateTab(this.#arrButtons[iIndexTab],  this.#arrTabs[iIndexTab]);
        else
            this.activateTab(null, this.#arrTabs[iIndexTab]);
    }

    /**
     * activates proper tab and disables others
     */
    activateTab(objButtonTab, objDivTabsheetContent)
    {
        //==== TAB BUTTONS
        if (this.#sType == this.arrTypes.buttons)
        {
            //remove all "active" classes
            for (let iIndex = 0; iIndex < this.#arrButtons.length; iIndex++)
            {
                this.#arrButtons[iIndex].classList.remove("active");
                this.#arrButtons[iIndex].setAttribute("tabindex", -1);
            }

            //add "active" class
            objButtonTab.classList.add("active");   
            objButtonTab.setAttribute("tabindex", 0);

            //focus
            // console.log("active alelemt", document.activeElement);
//            if (document.activeElement == objButtonTab )
            // objButtonTab.focus();
        }

        //==== TAB CONTENT
        //remove all "active" classes
        for (let iIndex = 0; iIndex < this.#arrTabs.length; iIndex++)
            this.#arrTabs[iIndex].classList.remove("active");

        //add "active" class
        objDivTabsheetContent.classList.add("active");  

        //add content to tab 
        if (objDivTabsheetContent.hasAttribute("url"))
        {
            let objSpinner = objButtonTab.querySelector("dr-icon-spinner");
            this.loadPageInTab(objDivTabsheetContent, objDivTabsheetContent.getAttribute("url"), objSpinner);
        }   

        //dispatch event
        this.#dispatchEventTabChanged(objDivTabsheetContent, "activated new tab")
    }

    updateUI()
    {

    }


    /**
     * return navigation type
     * 
     * @returns string
     */
    getType()
    {
        return this.#sType;
    }

    /**
     * set type of navigation
     * 
     * @param {string} sType see this.arrTypes for values
     */
    setType(sType = this.arrTypes.buttons)
    {
        this.#sType = sType;
        this.populate();
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
     * get navigation
    */
    get type()
    {
        return this.getType();
    }    

    /** 
     * set navigation
    */
    set type(sValue)
    {
        this.setType(sValue);
    }

    /**
     * dispatch event that tab has changed
     * 
     * @param {HTMLElement} objActiveTab 
     * @param {string} sDescription 
     */
    #dispatchEventTabChanged(objActiveTab, sDescription)
    {            
        this.dispatchEvent(new CustomEvent("change",
        {
            bubbles: true,
            detail:
            {
                activeTab: objActiveTab,
                description: sDescription
            }
        }));
    }        


    /**
     * load the contents of an url into the innerHTML of a tabsheet
     * 
     * WARNING:
     * URL must be located on the same domain for security reasons!
     * 
     * @param {HTMLElement} objDivTab 
     * @param {string} sURL 
     * @param {HTMLElement} <dr-icon-spinner> object 
     */
    loadPageInTab(objDivTab, sURL, objSpinner)
    {   
        //start spinner
        objSpinner.start();

        //do request
        const objRequest = new Request(sURL,
        {
            method: "GET",
            credentials: "same-origin"
        });

        //process response
        fetch(objRequest)
        .then((response) => response.text())
        .then((objHTMLRes) => 
        {
            objDivTab.innerHTML = objHTMLRes;
            objSpinner.stop();
            console.log("Page (" + sURL+ ") loaded in element: ", objDivTab);
        })
        .catch((error) => 
        {
            objSpinner.stop();
            console.warn(error);
        });
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
        return ["disabled"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "disabled":
                this.disabled = sNewVal;
                break;
        }

        if (this.#bConnectedCallbackHappened)
            this.updateUI();        
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
            // console.log("setformvalue connectedCallback",this.getValueAsString("."));

            //render
            this.populate();
        }

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();
        if (this.#objAbortControllerButtons.signal.aborted)
            this.#objAbortControllerButtons = new AbortController();

        //event
        this.addEventListeners();

        this.#manageScrollbarIcons();

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
customElements.define("dr-tabsheets", DRTabsheets);