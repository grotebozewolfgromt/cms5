
<?php 
/**
 * dr-progress-bar.js
 *
 * web component for a progress bar
 * 
 * the goal was to emulate as much behavior from <progress id="file" value="32" max="100"> as possible
 * 
 * EVENT:
 * 
 * EXAMPLE:
 * 
 * @author Dennis Renirie
 * 
 * 11 sept 2025 dr-progress-bar.js v2 created. now with infinite progressbar
 */
?>


class DRProgressBar extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {
                display: block;
                width: var(--drprogressbar-width, 250px);
                height: var(--drprogressbar-height, 40px);
                background: light-dark(var(--lightmode-color-drprogressbar-background, rgb(234, 234, 234)), var(--darkmode-color-drprogressbar-background, rgba(52, 52, 52, 1)));
                border-radius: 4px;
                position: relative;
                overflow: hidden;
            }

            .fill 
            {
                width: 0%;
                height: 100%;
                background: light-dark(var(--lightmode-color-drprogressbar-fill, rgba(0, 110, 194, 1)), var(--darkmode-color-drprogressbar-fill, rgba(0, 143, 253, 1)));
                transition: width 0.25s ease, left 0.25s ease;     
                position: relative;  
                border-radius: 4px;
            }

            .percent
            {
                position: absolute;
                top: 0px;
                color: light-dark(var(--lightmode-color-drprogressbar-percent, rgba(255, 255, 255, 1)), var(--darkmode-color-drprogressbar-percent, rgba(255, 255, 255, 1)));                
                background: light-dark(var(--lightmode-color-drprogressbar-percent-background, rgba(234, 234, 234, 0.65)), var(--darkmode-color-drprogressbar-percent-background, rgba(52, 52, 52, 0.61)));            
                margin: 2px;
                padding-left: 4px;
                padding-right: 4px;
                border-radius: 4px;
                font-size: 11px;
                box-sizing: border-box;
            }

            .percent.invisible
            {
                display: none;
            }            
        </style>
        <div class="fill"></div>
        <label class="percent"></label>        
    `;

    #objDivFill = null; //the <div> that fills and shows progress
    #objLblPercent = null; //<label> with text: percentage
    #iMaxProgress = 100; //the total/maximum value
    #iCurrentProgress = 0; //the current progress value
    #objInfiniteInterval = null;//for infinite progress to trigger the progress. if != null then infinite progress started
    #iInfiniteAnimationStage = 1; //the stage of animation of the infinite scrollbar

    #bConnectedCallbackHappened = false;

    /**
     * 
     */
    constructor()
    {
        super();
        this.objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: false });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRProgressBar.sTemplate;
        
        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    

    }    

    #readAttributes()
    {
        //read attributes
        this.#iMaxProgress = DRComponentsLib.attributeToInt(this, "max", this.#iMaxProgress);
        this.#iCurrentProgress = DRComponentsLib.attributeToInt(this, "value", this.#iCurrentProgress);
    }


    populate()
    {
        this.#objDivFill = this.shadowRoot.querySelector(".fill");
        this.#objLblPercent = this.shadowRoot.querySelector(".percent");

        //UI
        this.updateUI();
    }


    /**
     * attach event listenres
     */
    addEventListeners()
    {          
    }

    updateUI()
    {
        this.#objDivFill.style.width = this.getPercent() + "%";
        this.#objLblPercent.textContent = this.getPercent() + "%";
    }

    /** 
     * set internal value 
    */
    set value(iValue)
    {
        this.#iCurrentProgress = iValue;
        this.updateUI();
    }

    /** 
     * get internal value 
    */
    get value()
    {
        return this.#iCurrentProgress;
    }

    /** 
     * set internal max value 
    */
    set max(iMax)
    {
        this.#iMaxProgress = iMax;
        this.updateUI();
    }

    /** 
     * get internal max value 
    */
    get max()
    {
        return this.#iMaxProgress;
    }

    /** 
     * set internal value 
    */
    setValue(iValue)
    {
        this.#iCurrentProgress = iValue;
        this.updateUI();        
    }

    /** 
     * get internal value 
    */
    getValue()
    {
        return this.#iCurrentProgress;
    }

    /** 
     * set internal max value 
    */
    setMax(iValue)
    {
        this.#iMaxProgress = iValue;
        this.updateUI();        
    }

    /** 
     * get internal max value 
    */
    getMax()
    {
        return this.#iMaxProgress;
    }

    /**
     * calculates the percentage based on total and current progress value
     */
    getPercent()
    {
        return Math.ceil(this.#iCurrentProgress / this.#iMaxProgress * 100);
    }

    /**
     * start infinite progressbar
     * 
     * WARNING:
     * Don't forget to stop it with stopInfinite(), otherwise you'll eat system resources for no reason
     */
    startInfinite()
    {
        if (this.#objInfiniteInterval === null) //you can only start 1
        {
            this.#objInfiniteInterval = setInterval(()=>this.intervalInfinite(), 25);
            this.#objLblPercent.classList.add("invisible");
        }
    }

    /**
     * the interval of an infinite progress bar
     */
    intervalInfinite()
    {
        //declare + init
        const objRectParent = this.getBoundingClientRect();
        const objRectFill = this.#objDivFill.getBoundingClientRect();
        const iMaxWidthFillPx = 50; //maxwidth is 30 pixels
        const iStepsPx = 20; //the steps to take in pixels
        let iLeftOffsetFillPx = 0; //offset between left position of parent and fill
        
        //calculations
        iLeftOffsetFillPx = objRectFill.left - objRectParent.left;
        this.#objDivFill.style.width = iMaxWidthFillPx + "px";
        
        //STEP 1: moving fill to the right
        if (this.#iInfiniteAnimationStage == 1)
        {
            this.#objDivFill.style.left = (iLeftOffsetFillPx + iStepsPx) + "px";

            //go to step 2
            if ((iLeftOffsetFillPx + iMaxWidthFillPx + 10) > objRectParent.width) //we take a 10px leeway (if we choose 0 the frame update might not happen and it disappears on the left)
                this.#iInfiniteAnimationStage = 2;   
        }

        //STEP 2: move fill to the left
        if (this.#iInfiniteAnimationStage == 2)
        {
            this.#objDivFill.style.left = (iLeftOffsetFillPx - iStepsPx) + "px";

            //go to step 1
            if (iLeftOffsetFillPx <= 10) //we take a 10px leeway (if we choose 0 the frame update might not happen and it disappears on the left)
                this.#iInfiniteAnimationStage = 1;   
        }         


        // console.log("interval", objRectParent, objRectFill);
    }

    /**
     * stops infinite progressbar
     */
    stopInfinite()
    {
        if (this.#objInfiniteInterval != null)
        {
            window.clearInterval(this.#objInfiniteInterval);
            this.#objDivFill.style.width = "0px";
            this.#objDivFill.style.left = "0px"
            this.#objLblPercent.classList.remove("invisible");
        }
        this.#objInfiniteInterval = null;
    }

    // /**
    //  * stops infinite progressbar
    //  * 
    //  * alternative for stopInfinite();
    //  */
    // get stopInfinite()
    // {
    //     return this.stopInfinite();
    // } 

    // /**
    //  * stops infinite progressbar
    //  */
    // set stopInfinite(bValue)
    // {
    //     this.stopInfinite(bValue);
    // }     


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
    }


    static get observedAttributes() 
    {
        return ["value", "max"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number");
        switch(sAttrName)
        {
            case "value":
                this.value = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "max":
                this.max = sNewVal;
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
        this.stopInfinite();//in case a infinite progressbar was started
    }


}


/**
 * make component available in HTML
 */
customElements.define("dr-progress-bar", DRProgressBar);