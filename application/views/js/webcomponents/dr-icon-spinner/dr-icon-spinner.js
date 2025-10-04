<?php 
/**
 * dr-icon-spinner.js
 *
 * class for a spinning spinner icon
 * this class shows the inside of this container (innerHTML) by default.
 * 
 * HOW TO USE:
 * - When called start() it removes the original innerHTML and shows a spinner in the shadowDOM
 * - When called stop() it removes the spinning icon and puts the original innerHTML back
 * - toggle() toggles between start() and stop()
 * 
 * 
 * EXAMPLE:
 <dr-icon-spinner>
    <svg id="Bold" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><title/><path d="M23.85,11.65a.5.5,0,0,0-.45-.14l-5.3,1.06a.5.5,0,0,0-.26.84l.71.72L14,16.65V9.57a5,5,0,1,0-4,0v7.1L5.39,14.18l.77-.77a.5.5,0,0,0-.26-.84L.6,11.51a.51.51,0,0,0-.45.14A.5.5,0,0,0,0,12.1l1.06,5.3a.5.5,0,0,0,.84.26L3,16.54l8.64,7.34a.5.5,0,0,0,.65,0L21,16.54l1.12,1.12a.5.5,0,0,0,.84-.26L24,12.1A.5.5,0,0,0,23.85,11.65ZM12,7a2,2,0,1,1,2-2A2,2,0,0,1,12,7Z"/></svg>
 </dr-icon-spinner>
 <script>
        addEventListener("load", (event) => 
        {
            spinner = document.querySelector("dr-icon-spinner");
            // spinner.start();
            // spinner.stop();

            spinner.addEventListener("click", (event) => 
            {
                spinner.toggle();
            });

        }) 
</script>
 * 
 * 
 *  
 * 
 * @todo custom icon inside that is replaced by spinner icon. when spinner stops, icon is placed back
 * 
 * @todo spinner that avoids mouse cursor. This way the user has something to play with while waiting
 * @todo spinner that slows down when clicking on it. This way the user has something to play with while waiting
 * @todo spinner that is "magnetically" attracted to the mouse cursor. you must trying to avoid it
 * 
 * @author Dennis Renirie
 * 
 * 
 * 
 * 4 april 2025 dr-progress-spinner.js created
 */
?>


class DRIconSpinner extends HTMLElement
{
    static sTemplate = `
        <style>
        
            :host 
            { 
                display: inline-block;
                /* no width or height , this allows for anything put inside here that can be wider than the icon */
            } 

            svg
            {
                height: 16px;
                width: 16px;

                animation-name: spinneranimation;
                animation-duration: 1s;    
                animation-iteration-count: infinite;   
                animation-timing-function: linear;                 
            }

            @keyframes spinneranimation 
            {
                0%   {transform: rotate(0deg);}
                100% {transform: rotate(360deg);}
            }

        </style>
        <slot></slot>
    `;

    sSVGSpinner = '<svg fill="currentColor" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg"><path d="M41.9 23.9c-.3-6.1-4-11.8-9.5-14.4-6-2.7-13.3-1.6-18.3 2.6-4.8 4-7 10.5-5.6 16.6 1.3 6 6 10.9 11.9 12.5 7.1 2 13.6-1.4 17.6-7.2-3.6 4.8-9.1 8-15.2 6.9-6.1-1.1-11.1-5.7-12.5-11.7-1.5-6.4 1.5-13.1 7.2-16.4 5.9-3.4 14.2-2.1 18.1 3.7 1 1.4 1.7 3.1 2 4.8.3 1.4.2 2.9.4 4.3.2 1.3 1.3 3 2.8 2.1 1.3-.8 1.2-2.5 1.1-3.8 0-.4.1.7 0 0z"/></svg>';
    #objSpinner = null;
    #bDisabled = false;
    #objAbortController = null;
    #sInnerHTMLOrg = ""; //original innerHTML that is replaced by the spinner icon
    #bIsSpinning = false; //has started spinning?
    #bConnectedCallbackHappened = false;


    /**
     * 
     */
    constructor()
    {
        super();
        this.#objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true});

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRIconSpinner.sTemplate;

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);        


    }    

    #readAttributes()
    {


    }

    populate()
    {
        // this.shadowRoot.innerHTML += this.sSVGSpinner;
        // this.#objSpinner = this.shadowRoot.querySelector("svg");
        // console.log( this.#objSpinner);
        // this.#objSpinner.hidden = true;

    }

    /**
     * when changes happen from outside, we need to update the UI
     */
    updateUI()
    {

    }


    /**
     * attach event listeners for THIS object
     */
    addEventListeners()
    {
        
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

    start()
    {
        if (this.#bIsSpinning == false)
        {
            this.innerHTML = "";
            this.shadowRoot.innerHTML += this.sSVGSpinner;
            this.#objSpinner = this.shadowRoot.querySelector("svg");

            this.#bIsSpinning = true;
        }
    }

    stop()
    {
        if (this.#bIsSpinning == true)
        {
            this.innerHTML = this.#sInnerHTMLOrg;
            this.shadowRoot.removeChild(this.#objSpinner);
            this.#objSpinner = null;

            this.#bIsSpinning = false;
        }
    }

    /**
     * 
     */
    isSpinning()
    {
        return this.#bIsSpinning;
    }

    /**
     * toggles between starting and stopping the spinner
     */
    toggle()
    {
        if (this.#bIsSpinning)
            this.stop();
        else
            this.start();
    }


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
    }


    /** 
     * When added to DOM
     */
    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //first read
            this.#sInnerHTMLOrg = this.innerHTML; //store original content
            this.#readAttributes();      

            //then render
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

    // attributeChangedCallback(sAttrName, sPreviousVal, sNextVal) 
    // {
    //     switch(sAttrName)
    //     {
    //         case "disabled":
    //             this.disabled = sNextVal;
    //             if (this.#bConnectedCallbackHappened)
    //                 this.updateUI();                
    //             break;
    //     }

    // }
      
    // static get observedAttributes() 
    // {
    //     return ["start", "stop"];
    // }

  

}


/**
 * make component available in HTML
 */
customElements.define("dr-icon-spinner", DRIconSpinner);