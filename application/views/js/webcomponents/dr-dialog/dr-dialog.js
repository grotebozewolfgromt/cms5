<?php 
/**
 * dr-dialog.js
 * 
 * A modal popup dialog representing the user with a few choices.
 * The dialog has a title, body with the message, and footer with buttons
 * This modal dialog has an "ok"-button by default, but this is overwritten by
 * addButton() or when you add 1 (or multiple) <button>-elements in the body with
 * setBody().
 * 
 * PHILOSOPHY
 * ===================
 * 1. BUTTON PLACEMENT
 *      LEFT: Least destructive action, like "cancel"
 *      RIGHT: Most destructive action, like "delete".
 * 
 *      Reasoning:
 *      Regretfully there is no standard for this in OS-land, 
 *      MacOS does it this way, Windows exactly the other way around.
 *      Let's keep it consistent with our software and use this standard.
 * 
 * 2. DEFAULT BUTTON
 *      The most destructive action is marked as 'default' by css class 
 *      (which colors the button blue by default).
 * 
 *      Reasoning: 
 *      Dialogs are often confirmations, marking the button 'default'
 *      that continues the natural flow of what the user is most likely 
 *      wanted to do in the first place.
 * 
 * 2. TEXT ON BUTTONS
 *      Use text of the action on the button, like "rename" instead of a generic "ok"
 *      Example: "are you sure you want to delete this file?" 
 *              ==> use buttons: "cancel" + "delete" (instead of "no" and "yes").
 *      Example: "are you sure you want to close this screen and not save the changes?"
 *              ==> use buttons "stay" + "exit & save" (instead of "no" and "yes")
 * 
 *      Reasoning: 
 *      Some messages are hard to wrap your head around in 0.5 seconds as a user.
 *      Like the second example.
 *      By naming the button for the user with the action it performs makes it instantly
 *      clear what the button does.
 * 
 * 
 * FEATURES
 * -automatically an "ok"-button
 * -colors the default button (marked with css class: "default")
 * 
 * ATTRIBUTES:
 * "title"      => sets tite on top of dialog
 * "transok"      => sets the translation of the "ok"-button
 * 
 * WARNING:
 * 
 * FIRES EVENT: 
 * 
 * EXAMPLE with html:
 * <dr-dialog title="Enter title here">
 *      Text in dialog
 *      <button class="default" onclick="alert('left')">left button</button>
 *      <button onclick="alert('right')">right button</button>
 * </dr-dialog>
 * <button onclick="document.querySelector('dr-dialog').showModal();">show dialog html</button>
 * 
 * EXAMPLE with javascript:
        let objDialog = new DRDialog();
        objDialog.setTitle("Rnter title here");
        objDialog.setBody("Text in dialog"); 

        objButton = document.createElement("button");
        objButton.textContent = "knoppie";
        objButton.addEventListener("click", (objEvent)=>
        {
            objDialog.close("returrrrrnvalue");
            console.log("objDialog.returnValue====1", objDialog.returnValue);
            objDialog.returnValue = "another returrrrrnvalue";
            console.log("objDialog.returnValue====2", objDialog.returnValue);
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objButton);

        objDialog.showModal();  
 * 
 * DEPENDENCIES:
 * -
 * 
 * @author Dennis Renirie
 * 
 * 3 sept 2025 dr-dialog.js created
 * 9 sept 2025 dr-dialog.js defaultred button
 * 17 sept 2025 dr-dialog.js hoeft geen document.querySelector("body").appendChild(objDialog) meer aan te roepen
 */
?>


class DRDialog extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
            }             


            /* Modal container */
            dialog
            {
                padding: 0;
                border: 1px solid light-dark(var(--lightmode-drdialog-border-color, rgba(87, 87, 87, 0.86)), var(--darkmode-drdialog-border-color, rgba(140, 140, 140, 0.66)));
                box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19);
                font-family: var(--drdialog-font-family, Poppins);
                border-radius: 10px;
                box-sizing: border-box;
            }

            dialog::backdrop 
            {
                background-color: light-dark(var(--lightmode-drdialog-background-color, rgba(0, 0, 0, 0.479)), var(--darkmode-drdialog-background-color, rgba(157, 157, 157, 0.16)));
                backdrop-filter: blur(5px);             
                /* opacity: 0.75; */
            }

            .dialog-header 
            {
                font-family: var(--drdialog-font-family, Poppins);
                font-weight: bold;
                font-size: 0.9rem;

                padding-left: 15px;
                padding-top: 5px;
                padding-bottom: 5px;

                height: var(--drdialog-header-height, 30px);

                background-color: light-dark(var(--lightmode-drdialog-header-background-color,  rgba(243, 243, 243, 0.48)), var(--darkmode-drdialog-header-background-color,  rgba(162, 162, 162, 0.04)));
                color: light-dark(var(--lightmode-drdialog-header-font-color, rgb(56, 56, 56)), var(--darkmode-drdialog-header-font-color, rgba(220, 220, 220, 1)));

                vertical-align: middle;
            }
            
            .dialog-title
            {
                text-align: center;
                padding-top: 9px;
                user-select: none;
            }

            /* The Close X Button */
            .dialog-button-x
            {
                position: absolute;
                color: light-dark(var(--lightmode-drdialog-header-button-x-color, rgba(46, 46, 46, 1)), var(--darkmode-drdialog-header-button-x-color, rgba(255, 255, 255, 1)));
                /* float: right; */
                /* font-size: 28px; */
                /* font-weight: bold; */
                height: 20px;
                width: 20px;
                right: 5px;
                top: 5px;

                padding: 5px;

                border-radius: 50%;
                background-color: light-dark(var(--lightmode-drdialog-header-button-x-background-color, rgba(221, 221, 221, 1)), var(--darkmode-drdialog-header-button-x-background-color, rgba(56, 56, 56, 1)));
            }
            
            .dialog-button-x:hover,
            .dialog-button-x:focus 
            {
                color: light-dark(var(--lightmode-drdialog-header-button-x-hover-color, rgba(60, 60, 60, 1)), var(--darkmode-drdialog-header-button-x-hover-color, rgba(255, 255, 255, 1)));    
                text-decoration: none;
                cursor: pointer;
                background-color: light-dark(var(--lightmode-drdialog-header-button-x-background-hover-color, rgba(255, 109, 109, 1)), var(--darkmode-drdialog-header-button-x-background-hover-color, rgba(139, 3, 3, 1)));
            }


            .dialog-body 
            {
                margin: 20px;
                font-family: var(--drdialog-font-family, Poppins);
                font-size: 0.75rem;
                line-height: 1.0rem;
                text-align: center;    
                overflow: scroll;
                box-sizing: border-box;
            }
            
            .dialog-footer
            {
                padding: 0px;
                background-color: light-dark(var(--lightmode-drdialog-footer-background-color, rgba(245, 245, 245, 1)), var(--darkmode-drdialog-footer-background-color, rgba(31, 31, 31, 1)));    
                /* color: white; */

                border-width: 0px;

                text-align: center;

                height: var(--drdialog-footer-height, 50px);

                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(50px, 1fr));
            }

            .dialog-footer button
            {
                font-family: var(--drdialog-font-family, Poppins);
                font-weight: bolder;
                font-size: 0.9rem;

                height: var(--drdialog-footer-height, 50px);
                padding: 10px;
                padding-left: 20px;
                padding-right: 20px;

                /* background-color: light-dark(var(--lightmode-drdialog-footer-button-background-color), var(--darkmode-drdialog-footer-button-background-color));                  
                color: light-dark(var(--lightmode-drdialog-footer-button-font-color), var(--darkmode-drdialog-footer-button-font-color));    */

                border-width: 0px;
                border-style: solid;
                /* border-color: light-dark(var(--lightmode-drdialog-footer-button-border-color), var(--darkmode-drdialog-footer-button-border-color)); */
                
                /* border-radius: 10px; */

            
            /*    cursor: pointer; */
            }

            .dialog-footer button:hover
            {                                    
                background-color: light-dark(var(--lightmode-drdialog-footer-button-background-hover-color, rgba(76, 154, 255, 1)), var(--darkmode-drdialog-footer-button-background-hover-color, rgba(76, 154, 255, 1))) !important;
                background-color: white;
                cursor: pointer;
            }

            .dialog-footer button.default
            {
                background-color: light-dark(var(--lightmode-drdialog-footer-button-background-default-color, rgba(24, 124, 255, 1)), var(--darkmode-drdialog-footer-button-background-default-color, rgba(24, 124, 255, 1))) !important;
                color: light-dark(var(--lightmode-drdialog-footer-button-font-default-color, rgba(255, 255, 255, 1)), var(--darkmode-drdialog-footer-button-font-default-color, rgba(255, 255, 255, 1)));
            }

            .dialog-footer button.default:hover
            {
                background-color: light-dark(var(--lightmode-drdialog-footer-button-background-default-hover-color, rgba(76, 154, 255, 1)), var(--darkmode-drdialog-footer-button-background-default-hover-color, rgba(76, 154, 255, 1))) !important;
            }

            .dialog-footer button.defaultred
            {
                background-color: light-dark(var(--lightmode-drdialog-footer-button-background-default-color, rgba(193, 0, 0, 1)), var(--darkmode-drdialog-footer-button-background-default-color, rgba(193, 0, 0, 1))) !important;
                color: light-dark(var(--lightmode-drdialog-footer-button-font-default-color, rgba(255, 255, 255, 1)), var(--darkmode-drdialog-footer-button-font-default-color, rgba(255, 255, 255, 1)));
            }

            .dialog-footer button.defaultred:hover
            {
                background-color: light-dark(var(--lightmode-drdialog-footer-button-background-default-color, rgba(194, 43, 43, 1)), var(--darkmode-drdialog-footer-button-background-default-color, rgba(194, 43, 43, 1))) !important;
            }

            


            /* ======================================= SMALL SIZE SCREENS =========================== */
            @media all and (max-width: 768px)
            {

            }

            /* ======================================= MEDIUM SIZE SCREENS ========================= */
            @media all and (min-width: 768px) AND (max-width: 1024px)
            {
                .dialog-body 
                {
                    min-height: 75px;
                    min-width: 500px;
                }         

            }

            /* ======================================= LARGE SIZE SCREENS ========================== */
            @media all and (min-width: 1024px)
            {
                .dialog-body 
                {
                    min-height: 75px;
                    min-width: 500px;
                }         
            }

        </style>
        <dialog>
            <div class="dialog-header">
                <div class="dialog-button-x" id="btnDialogExitX">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </div>
                <div class="dialog-title"></div>
            </div>
            <div class="dialog-body">
                <slot></slot>            
            </div>
            <div class="dialog-footer">
                <button class="dialog-button-ok default">Ok</button>
            </div>
        </dialog>        
    `;


    #objAbortController = null;
    #bConnectedCallbackHappened = false;    
    #objDialog = null; //the <dialog> component
    #arrButtons = [];//array of button objects displayed on the bottom of the dialog (the X in the top-right to close dialog is not included)
    #objOKButton = null; //by default there is an ok button
    #objExitXButton = null; //the close button on the top-right of the dialog
    #sTitle = ""; //the title on top of the dialog
    #objTitle = null; //the <div> with the title
    #sBody = ""; //body of dialog
    #objBody = null; //the <div> with the body
    #objFooter = null; //the <div> with the footer
    sTransOk = "Ok";
    #objDocumentBody = null;//document-<body>-element when null then it is assumed this dialog is not added to the body, thus needs to be added


    /**
     * 
     */
    constructor()
    {
        super();
        this.#objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRDialog.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);   

    }    

    #readAttributes()
    {
        this.#sTitle = DRComponentsLib.attributeToString(this, "title", this.#sTitle);
        this.sTransOk = DRComponentsLib.attributeToString(this, "transok", this.sTransOk);
    }

 
    populate()
    {
        this.#objDialog = this.shadowRoot.querySelector("dialog");

        //title, body, footer
        this.#objTitle = this.shadowRoot.querySelector(".dialog-title");
        this.#objBody = this.shadowRoot.querySelector(".dialog-body");
        this.#objFooter = this.shadowRoot.querySelector(".dialog-footer");

        //buttons
        this.#objExitXButton = this.shadowRoot.querySelector(".dialog-button-x");
        this.#objOKButton = this.shadowRoot.querySelector(".dialog-button-ok");
        
        //move buttons from the body to the footer (replaces "ok" button)
        const arrTempButtons = [...this.querySelectorAll("button")];
        if (arrTempButtons.length > 0)
        {
            this.#arrButtons = arrTempButtons; 
        }

        //update UI
        this.updateUI();
    }


    /**
     * returns internal AbortController(), so you can attach it to event listeners of buttons 
     * @returns {AbortController}
     */
    getAbortController()
    {
        return this.#objAbortController;
    }

    /**
     * show dialog 
     * 
     * @returns {any} return value supplied with the close() function as parameter
     */
    showModal()
    {
        this.populate();//make sure that the populate is executed (in js-only situation it is not executed)

        if (this.#objDocumentBody === null)
        {
            this.#objDocumentBody = document.querySelector("body");
            this.#objDocumentBody.appendChild(this);            
        }

        return this.#objDialog.showModal();
    }

    /**
     * close this dialog
     * 
     * @param {any} mReturnValue 
     */
    close(mReturnValue)
    {
        this.#objDialog.close(mReturnValue);
    }

    setTitle(sTitle)
    {
        this.#sTitle = sTitle;
    }

    getTitle()
    {
        return this.#sTitle;
    }

    setBody(sBody)
    {
        this.#sBody = sBody;
    }

    getBody()
    {
        return this.#sBody;
    }

    getBodyObject()
    {
        return this.#objBody;
    }

    setTransOk(sTranslationOKButton)
    {
        this.sTransOk = sTranslationOKButton;
    }

    getTransOk()
    {
        return this.sTransOk;
    }

    /**
     * adds button to dialog
     * (don't forget to add the eventlistener before calling this function)
     * 
     * @param {HTMLButtonElement} objButton 
     */
    addButton(objButton)
    {
        this.#arrButtons.push(objButton);
    }

    /**
     * attach event listenres
     */
    addEventListeners()
    {      
        //click exit button
        this.#objExitXButton.addEventListener("click", (objEvent)=>
        {
            this.#objDialog.close(false);
        }, { signal: this.#objAbortController.signal });  

        //click ok button
        if (this.#objOKButton)
        {
            this.#objOKButton.addEventListener("click", (objEvent)=>
            {
                this.#objDialog.close(false);
            }, { signal: this.#objAbortController.signal });  
        }        
    }


    /**
     * updates ui
     */
    updateUI()
    {
        this.#objTitle.innerHTML = this.#sTitle;

        if (this.#sBody !== "")
            this.#objBody.innerHTML = this.#sBody;

        const sMaxWidth = (window.innerWidth - 150) + "px";
        const sMaxHeight = (window.innerHeight - 200) + "px";

        this.#objBody.style.maxWidth = sMaxWidth;
        this.#objBody.style.maxHeight = sMaxHeight;

        if (this.#objOKButton)
            this.#objOKButton.innerHTML = this.sTransOk;

        if (this.#arrButtons.length > 0)
        {
            this.#objFooter.innerHTML = ""; //replace existing "ok"-button
            this.#arrButtons.forEach(objButton => 
            {
                this.#objFooter.appendChild(objButton);
            });
        }
    }


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
    }

    get returnValue()
    {
        return this.#objDialog.returnValue;
    }

    set returnValue(aReturnValue)
    {
        this.#objDialog.returnValue = aReturnValue;
    }

    static get observedAttributes() 
    {
        return ["title", "body"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "title":
                this.#sTitle = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "body":
                this.#sBody = sNewVal;
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
customElements.define("dr-dialog", DRDialog);