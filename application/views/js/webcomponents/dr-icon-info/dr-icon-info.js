
<?php 
/**
 * dr-icon-info.js
 *
 * class that represents an info icon (i-icon). When hovering over this icon: more information will show.
 * 
 * DEPENDENCIES:
 * <dr-popover>
 * 
 * EXAMPLE:
 *  <dr-icon-info>Dit zijn extra aanwijzingen</dr-icon-info>
 * 
 * @author Dennis Renirie
 * 
 * 21 mrt 2025 dr-info-icon.js created
 * 5 jun 2025 dr-info-icon.js renamed dr-icon-info ("icon" and "info" are switched)
 * 26 sept 2025 dr-info-icon.js removeEventListeners() was commented out wich resulted in an error
 */
?>


class DRIconInfo extends HTMLElement
{
    static sTemplate = 
    `<style>
            :host 
            {     
                cursor: pointer;                
                padding: 5px;

                color: light-dark(var(--lightmode-driconinfo-color, rgba(47, 47, 47, 1)), var(--darkmode-driconinfo-color, rgba(245, 245, 245, 1)));                
                font-family: var(--driconinfo-font-family, Poppins);
                font-weight: normal;
                text-align: left;
            }      

            svg
            {
                width: 12px;
                height: 12px;
            }
        </style>`;

    sSVGIcon = '<svg class="iconchangefill" viewBox="0 0 20 20" w xmlns="http://www.w3.org/2000/svg"><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="currentColor" id="Core" transform="translate(-86.000000, -296.000000)"><g id="info-outline" transform="translate(86.000000, 296.000000)"><path d="M9,15 L11,15 L11,9 L9,9 L9,15 L9,15 Z M10,0 C4.5,0 0,4.5 0,10 C0,15.5 4.5,20 10,20 C15.5,20 20,15.5 20,10 C20,4.5 15.5,0 10,0 L10,0 Z M10,18 C5.6,18 2,14.4 2,10 C2,5.6 5.6,2 10,2 C14.4,2 18,5.6 18,10 C18,14.4 14.4,18 10,18 L10,18 Z M9,7 L11,7 L11,5 L9,5 L9,7 L9,7 Z" id="Shape"/></g></g></g></svg>';
    #objBubble = null; //DRPopover
    #sHTMLInformation = ""; //html/text that was inserted, which needs to be in the info bubble <dr-icon-info>THIS TEXT</dr-icon-info>
    #bConnectedCallbackHappened = false;

    /**
     * 
     */
    constructor()
    {
        super();
        this.objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRIconInfo.sTemplate;
        
        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

    }    

    #readAttributes()
    {
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

    populate()
    {
        //icon
        this.shadowRoot.innerHTML += this.sSVGIcon;

        //bubble
        this.#objBubble = new DRPopover();
        this.#objBubble.anchorobject = this;  
        this.#objBubble.innerHTML = this.#sHTMLInformation;
        this.#objBubble.showonhoveranchor = true;
        this.#objBubble.showcloseicon = false;
        this.#objBubble.addEventListenersAnchor();
        // this.#objBubble.show();

        this.shadowRoot.appendChild(this.#objBubble);
    }


    /**
     * attach event listenres
     */
    addEventListeners()
    {
        //HOVER
        // this.addEventListener("onmouseover", (objEvent)=>
        // {


        // }, { signal: this.objAbortController.signal });           


    }

    updateUI()
    {
    }


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
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
            //render
            this.#sHTMLInformation = this.innerHTML;        

            //read attributes
            this.#readAttributes();

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
        this.#objBubble.removeEventListeners();
    }


}


/**
 * make component available in HTML
 */
customElements.define("dr-icon-info", DRIconInfo);