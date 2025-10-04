<?php 
/**
 * dr-context-menu.js
 *
 * class to create a right-click contextmenu
 * 
 * WARNING:
 * - in pure JS: don't forget to remove the menu from the DOM
 * - don't forget to style every menu item to take up just one line (so it is not wrapping):  #mytable td { white-space: nowrap; }
 * 
 * FEATURES:
 * - works on mobile and desktop.
 * - adds icons (optional)
 * - adds checkmarks (optional)
 * 
 * EXAMPLE PURE JS:
    function knopvoid()
    {
        element = document.getElementById("contextmenuid2");

        menu = new DRContextMenu(true); //true if you want to remove from DOM after hide()
        menu.anchorobject = this.#objMenuDots; //or: document.getElementById("mybutton");
        menu.id ="newmenu"; //optional        
        menu.addMenuItem("test");
        menu.addHR();
        menu.addMenuItem("test2");
        menu.addMenuItem("delete file", ()=>this.#handleDeleteFile(), [], this.#sSVGIconDelete);

        element.parentElement.appendChild(menu);

        menu.show();
    }
 *
 *
 * EXAMPLE WEBCOMPONENT:
<dr-context-menu id="contextmenuid" for="knoppie">
    <div onmousedown="callbackMenu1()" shortcut="Enter,Control">Menu item 1<b>people met legs</b></div>
    <div hr></div>
    <div checked>Menu item 3</div>
    <div><hr></div>
    <div hassubmenu>Menu item 4</div>
    <div hassubmenu>blekr<svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M18 12.75H6C5.59 12.75 5.25 12.41 5.25 12C5.25 11.59 5.59 11.25 6 11.25H18C18.41 11.25 18.75 11.59 18.75 12C18.75 12.41 18.41 12.75 18 12.75Z" fill="black"/></svg>Menu item 6</div>
</dr-context-menu>
 * 
 * 
 * 
 * 
 * 
 * EXAMPLE CSS:
 * 
:root
{
    --lightmode-color-contextmenu-background: rgb(255, 255, 255);
    --lightmode-color-contextmenu-border: rgb(219, 219, 219);
    --lightmode-color-contextmenu-hover: rgb(240, 240, 240);
    --lightmode-color-contextmenu-hr: rgb(232, 232, 232);


    --darkmode-color-contextmenu-background: rgb(39, 39, 39);
    --darkmode-color-contextmenu-border: rgba(107, 107, 107, 0.543);
    --darkmode-color-contextmenu-hover: rgb(69, 69, 69);
    --darkmode-color-contextmenu-hr: rgb(232, 232, 232);

}


 * @TODO
 * show/hide toggle menu
 * 
 * 
 * 
 *************************************************************************
 * WARNING:
 *************************************************************************
 * this file uses PHP so it can ONLY be used by including it with PHP!
 * 
 *************************************************************************
 * 
 * @author Dennis Renirie
 * 
 * 13 mrt 2025 dr-context-menu.js created
 * 26 apr 2025 dr-context-menu.js responds to ESC-KEY
 * 26 sept 2025 dr-context-menu.js niet nodig om id op te geven, dit component zoekt niet meer naar id om zichzelf te verwijderen
 * 26 sept 2025 dr-context-menu.js text aligned to left
 */
?>


class DRContextMenu extends HTMLElement
{
    arrMenuItems = []; //the menu items itself (needed if we dont have child elements when this class is purely used in a JS fashion)
    arrChildElements = []; //keeps track of the original child elements (we delete them in the constructor)
    iRowPointer = 0;
    sSVGChecked = '<svg viewBox="6 6 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"/></svg>';
    sSVGMore = '<svg fill="currentColor" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>';
    objAbortController = null;
    iAnchorPos = this.iPosBottom; //position to anchor menu to. iPosTop, iPosRight, iPosBottom, iPosLeft
    objAnchor = null; //HTMLElement where this menu is anchored to  
    bRemoveFromDOMOnHide = false;
    bRenderAsTable = true; //it renders either as a div or table      
    #bConnectedCallbackHappened = false;       
    #sHTMLElementId = ""; //<div id="iHTMLElementID">

    //class constants;
    iPosTop = 0
    iPosRight = 1;
    iPosBottom = 2;
    iPosLeft = 3;

    static sTemplate = `
        <style>
            :host 
            {    
                position: fixed;
                z-index: 10000;                  
            }          

            
            table
            {
                border-color: light-dark(var(--lightmode-color-contextmenu-border, rgb(214, 214, 214)), var(--darkmode-color-contextmenu-border, rgb(151, 151, 151))); 
                border-width: 1px;
                border-style: solid;
                background-color: light-dark(var(--lightmode-color-contextmenu-background, rgb(234, 234, 234)), var(--darkmode-color-contextmenu-background, rgb(48, 48, 48))); 
                color: light-dark(var(--lightmode-color-contextmenu-text,  rgb(18, 18, 18)), var(--darkmode-color-contextmenu-text,  rgb(255, 255, 255)));     
                user-select: none;
                border-radius: var(--borderradius-contextmenu, 5px);
                border-collapse: inherit; 
                cursor: pointer;
                font-size: 12px;
            } 


            table td
            {
                padding: 5px; 
                margin: 0px;
                text-overflow: ellipsis;
                overflow: hidden;
                max-width: 200px;
                white-space: nowrap;
                text-align: left;
            }

            table tr:hover
            {
                background-color: light-dark(var(--lightmode-color-contextmenu-hover, rgb(54, 127, 255)), var(--darkmode-color-contextmenu-hover, rgb(54, 117, 255))); 
                color: light-dark(var(--lightmode-color-contextmenu-hover-text, rgb(252, 252, 252)), var(--darkmode-color-contextmenu-hover-text, rgb(255, 255, 255))); 
            }

            table tr.notallowed:hover
            {
                background-color: light-dark(var(--lightmode-color-contextmenu-background, rgb(234, 234, 234)), var(--darkmode-color-contextmenu-background, rgb(48, 48, 48)));             
                cursor: default;
            }

            table svg
            {
                width: 16px;
                height: 16px;
            }    




            table hr
            {
                margin: 20px;
                margin-top: 5px;
                margin-bottom: 5px;   
                height: 1px;
                background: light-dark(var(--lightmode-color-contextmenu-hr, rgb(179, 179, 179)), var(--darkmode-color-contextmenu-hr, rgb(130, 130, 130))); 
                font-size: 0;
                border: 0;            
            }     
                




            div.contextmenu
            {
                border-color: light-dark(var(--lightmode-color-contextmenu-border, rgb(214, 214, 214)), var(--darkmode-color-contextmenu-border, rgb(151, 151, 151))); 
                border-width: 1px;
                border-style: solid;
                background-color: light-dark(var(--lightmode-color-contextmenu-background, rgb(234, 234, 234)), var(--darkmode-color-contextmenu-background, rgb(48, 48, 48))); 
                color: light-dark(var(--lightmode-color-contextmenu-text,  rgb(18, 18, 18)), var(--darkmode-color-contextmenu-text,  rgb(255, 255, 255)));     
                user-select: none;
                border-radius: var(--borderradius-contextmenu, 5px);
                border-collapse: inherit; 
                cursor: pointer;
                font-size: 12px;
            }

            div.row
            {
                display: grid;
                grid-template-columns: auto auto auto auto auto; 
                gap: 5px;   
                cursor: pointer;
            }
            
            div.column
            {
                padding-top: 5px; 
                padding-bottom: 5px; 
                text-overflow: ellipsis;
                overflow: hidden;
                max-width: 200px;     
                white-space: nowrap;       
            }

            div.row:hover
            {
                background-color: light-dark(var(--lightmode-color-contextmenu-hover, rgb(54, 127, 255)), var(--darkmode-color-contextmenu-hover, rgb(54, 117, 255))); 
                color: light-dark(var(--lightmode-color-contextmenu-hover-text, rgb(252, 252, 252)), var(--darkmode-color-contextmenu-hover-text, rgb(255, 255, 255)));             
            }

        
        </style>
        <slot></slot>
    `;


    /**
     * 
     * @param {boolean} bRemoveFromDOMOnHide when hide() is called, should this object be removed from the DOM? It needs an ID to be able to find itself!
     */
    constructor(bRemoveFromDOMOnHide = false)
    {
        super();
        this.objAbortController = new AbortController();
        this.bRemoveFromDOMOnHide = bRemoveFromDOMOnHide;

        this.attachShadow({mode: "open"});


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRContextMenu.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
    

        //hidden by default
        this.hide();
    }    

    #populate()
    {
        if (this.bRenderAsTable)
        {
            const objTable = this.renderAsTable();
            objTable.className = "contextmenu";
            this.shadowRoot.appendChild(objTable);
            this.innerHTML = "";
        }
        else
        {
            const objDiv = this.renderAsDivs();
            this.shadowRoot.appendChild(objDiv);
            this.innerHTML = "";
        }
    }

    #readAttributes()
    {
        //anchorpos
        if (this.getAttribute("anchorpos") !==  null)
        {
            if (this.getAttribute("anchorpos") === "")               
                this.iAnchorPos = this.iPosTop;
            else if (this.getAttribute("anchorpos") == "right")
                this.iAnchorPos = this.iPosRight;
            else if (this.getAttribute("anchorpos") == "bottom")
                this.iAnchorPos = this.iPosBottom;
            else if (this.getAttribute("anchorpos") == "left")
                this.iAnchorPos = this.iPosLeft;
            else //everything else is "top", including top itself
                this.iAnchorPos = this.iPosTop;
        }
        
        //anchor
        if (this.getAttribute("for") !== null)
        {
            this.objAnchor = document.getElementById(this.getAttribute("for"));    
        }
        if (this.getAttribute("anchor") !== null)
        {
            this.objAnchor = document.getElementById(this.getAttribute("anchor"));    
        }        
    
    }

    /** 
     * converts the child elements into menu items we can use
     **/
    #childrenToMenuItems()
    {
        // this.arrMenuItems = []; //don't empty it, because it will overwrite the values set manually (not from DOM, but with javascript)
        let sItemText = "";
        let sSVGIcon = "";

        for (let iIndex = 0; iIndex < this.arrChildElements.length; iIndex++)
        {
            //read "hr" attribute
            let bHR = false;
            if (this.arrChildElements[iIndex].getAttribute("hr") !==  null)
            {
                if (this.arrChildElements[iIndex].getAttribute("hr") === "")               
                    bHR = true;
                else if (this.getAttribute("hr") == "false")
                    bHR = false;
                else
                    bHR = true;
            }

            //alternative <hr> as child: 
            //if 1 of the children is an <hr>, treat is as an hr-item 
            if (this.arrChildElements[iIndex].hasChildNodes())
            {       
                if (this.arrChildElements[iIndex].firstChild.nodeType !== Node.TEXT_NODE)
                {
                    if (this.arrChildElements[iIndex].firstChild.tagName.toUpperCase() == "HR")
                        bHR = true;
                }
            }


            //read onmousedown attribute
            let sCallback = "";
            if (this.arrChildElements[iIndex].getAttribute("onmousedown") !==  null)
            {
                sCallback = this.arrChildElements[iIndex].getAttribute("onmousedown");
            }      
            //read onclick attribute
            if (this.arrChildElements[iIndex].getAttribute("onclick") !==  null)
            {
                sCallback = this.arrChildElements[iIndex].getAttribute("onclick");
            }               
            
            //read "shortcut" attribute
            let arrShortCutKeys = "";
            if (this.arrChildElements[iIndex].getAttribute("shortcut") !==  null)
            {
                arrShortCutKeys = this.arrChildElements[iIndex].getAttribute("shortcut").split(",");
            }            


            //read "checked" attribute
            let bChecked = false;
            if (this.arrChildElements[iIndex].getAttribute("checked") !==  null)
            {
                if (this.arrChildElements[iIndex].getAttribute("checked") === "")               
                    bChecked = true;
                else if (this.arrChildElements[iIndex].getAttribute("checked") == "false")
                    bChecked = false;
                else
                    bChecked = true;
            }

            //read "hassubmenu" attribute
            let bSubMenu = false;
            if (this.arrChildElements[iIndex].getAttribute("hassubmenu") !==  null)
            {
                if (this.arrChildElements[iIndex].getAttribute("hassubmenu") === "")               
                    bSubMenu = true;
                else if (this.arrChildElements[iIndex].getAttribute("hassubmenu") == "false")
                    bSubMenu = false;
                else
                    bSubMenu = true;
            }            

            //separate icons from text
            sItemText = "";
            for (let iChildIndex = 0; iChildIndex < this.arrChildElements[iIndex].childNodes.length; iChildIndex++)
            {
                //filter text
                if (this.arrChildElements[iIndex].childNodes[iChildIndex].nodeType == 3) //text node
                    sItemText = sItemText + this.arrChildElements[iIndex].childNodes[iChildIndex].textContent;

                //filter icons
                if (this.arrChildElements[iIndex].childNodes[iChildIndex].nodeType == 1) //object
                {
                    if (this.arrChildElements[iIndex].childNodes[iChildIndex].tagName.toUpperCase() == "SVG")
                        sSVGIcon = this.arrChildElements[iIndex].childNodes[iChildIndex].outerHTML;
                }
            }


            //Add item to menu
            if (bHR) //when horizontal line, ignore the rest
                this.addHR();
            else
                this.addMenuItem(sItemText, sCallback, arrShortCutKeys, sSVGIcon, bChecked, bSubMenu);
       
        }
    }


    setSVGChecked(sSVG)
    {
        this.sSVGChecked = sSVG;
    }

    setSVGMore(sSVG)
    {
        this.sSVGMore = sSVG;
    }


    /**
     * add row item to menu
     * 
     * @param {string} sText 
     * @param {function} fnCallback function callback when clicked
     * @param {array} arrShorcutKeys event.key values like array["Control", "v"] ==> it doesn't attach event listeners
     * @param {string} sSVGIcon 
     * @param {boolean} bChecked 
     * @param {boolean} bMore 
     */
    addMenuItem(sText, fnCallback = "", arrShorcutKeys = [], sSVGIcon = "", bChecked = false, bMore = false)
    {
        this.arrMenuItems.push({"text": sText, "checked": bChecked, "callback": fnCallback, "shortcut": arrShorcutKeys, "icon": sSVGIcon, "checked": bChecked, "more": bMore, "hr": false});
    }

    /**
     * adds <hr> to menu
     */
    addHR()
    {
        this.arrMenuItems.push({"text": "", "checked": false, "callback": null, "shortcut": [], "icon": "", "checked": false, "more": false, "hr": true});
    }

    /**
     * render menu as <table> element and registers eventlisteners
     * 
     * @param {string} sHTMLId
     * @param {boolean} bPopover use as popover ==> only works when you use the sHTMLId parameter
     * @returns {HTMLElement}
     */
    renderAsTable()
    {
        const objTable = document.createElement("table");
        const objTBody = document.createElement("tbody");
        let objTR = null;
        let objTD = null;

        //loop menu items
        for (let iIndex = 0; iIndex < this.arrMenuItems.length; iIndex++)
        {
            objTR = document.createElement("tr");


            if (this.arrMenuItems[iIndex].hr)
            {
                objTD = document.createElement("td");
                objTD.setAttribute("colspan", 5);
                objTD.innerHTML = "<hr>";
                objTR.className = "notallowed";
                objTR.appendChild(objTD);                
            }
            else
            {
                //====checked
                objTD = document.createElement("td");
                if (this.arrMenuItems[iIndex].checked) //only if exists
                    objTD.innerHTML = this.sSVGChecked;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====icon
                objTD = document.createElement("td");
                if (this.arrMenuItems[iIndex].icon) //only if exists
                    objTD.innerHTML = this.arrMenuItems[iIndex].icon;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====text
                objTD = document.createElement("td");
                if (this.arrMenuItems[iIndex].text) //only if exists
                    objTD.innerHTML = this.arrMenuItems[iIndex].text;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====shortcuts
                objTD = document.createElement("td");
                if (this.arrMenuItems[iIndex].shortcut.length > 0) //only if exists
                {
                    let sShortCuts = "";
                    for (let iIndexShort = 0; iIndexShort < this.arrMenuItems[iIndex].shortcut.length; iIndexShort++)
                    {
                        if (iIndexShort > 0)
                            sShortCuts += " + ";
                        sShortCuts += this.arrMenuItems[iIndex].shortcut[iIndexShort];
                    }
                    objTD.innerHTML = sShortCuts;
                }
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====more
                objTD = document.createElement("td");
                if (this.arrMenuItems[iIndex].more) //only if exists
                    objTD.innerHTML = this.sSVGMore;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====respond to mouse and mobile
                // objTR.addEventListener("click", ()=>
                // {
                //     console.log("respond to clickie");
                //     objTable.hidePopover();
                //     if (this.arrMenuItems[iIndex].callback)
                //         this.arrMenuItems[iIndex].callback();
                // }, { signal: this.objAbortController.signal });

                if (this.arrMenuItems[iIndex].callback)
                {
                    this.#addEventListenerMenuItem(objTR, this.arrMenuItems[iIndex].callback);
                    // objTR.setAttribute("onmousedown", this.arrMenuItems[iIndex].callback);
                }
                else
                {
                    objTR.className = "notallowed"; //no "hand" when no event listener assigned
                }

                // objTR.addEventListener("touch", ()=> 
                // {
                //     objTable.hidePopover();
                //     if (this.arrMenuItems[iIndex].callback)
                //         this.arrMenuItems[iIndex].callback();
                // }, { signal: this.objAbortController.signal });       
                
                //====respond to shortcuts     
                /*           
                if (this.arrMenuItems[iIndex].shortcut.length > 0) //only if exists
                {
                    let arrShorts = this.arrMenuItems[iIndex].shortcut; //doesn't accept as parameter, so declare it temp
                    document.addEventListener("keydown", (objEvent) => 
                    {
                        if (!objEvent.repeat) //prevents user holding down key
                        {
                            console.log("keydownnnie", arrShorts, objEvent.key);

                            let bShortcutDetected = true;
                            for (let iIndexShort = 0; iIndexShort < this.arrMenuItems[iIndex].shortcut.length; iIndexShort++)
                            {
                                if (objEvent.key !== arrShorts[iIndexShort])
                                {
                                    bShortcutDetected = false;
                                }
                            }

                            if (bShortcutDetected) 
                            {   
                                console.log("do the thing");
                                return objEvent.preventDefault(); // overrides default action
                            }
                        }                        

                    }, { signal: this.objAbortController.signal });   
                }             
                */
            } //end if(hr)

            objTBody.appendChild(objTR);
        }

        objTable.id = this.#sHTMLElementId;
        objTable.appendChild(objTBody);

        return objTable;
    }


    renderAsDivs()
    {
        const objParentDiv = document.createElement("div");
        objParentDiv.classList.add("contextmenu");
        let objRowDiv = null;
        let objColDiv = null;

        //loop menu items
        for (let iIndex = 0; iIndex < this.arrMenuItems.length; iIndex++)
        {
            //row
            objRowDiv = document.createElement("div");
            objRowDiv.classList.add("row");

            if (this.arrMenuItems[iIndex].hr)
            {
                objTD = document.createElement("td");
                objTD.setAttribute("colspan", 5);
                objTD.innerHTML = "<hr>";
                objTR.className = "notallowed";
                objTR.appendChild(objTD);                
            }
            else
            {
                if (this.arrMenuItems[iIndex].callback)
                    this.#addEventListenerMenuItem(objRowDiv, this.arrMenuItems[iIndex].callback);
                else
                    objRowDiv.className = "notallowed"; //no "hand" when no event listener assigned

                //column: checked
                objColDiv = document.createElement("div");
                objColDiv.className = "column";
                if (this.arrMenuItems[iIndex].checked) //only if exists
                    objColDiv.innerHTML = this.sSVGChecked;
                else
                    objColDiv.innerHTML = "&nbsp;";
                objRowDiv.appendChild(objColDiv);

                //column: icon
                objColDiv = document.createElement("div");
                objColDiv.className = "column";
                if (this.arrMenuItems[iIndex].icon) //only if exists
                    objColDiv.innerHTML = this.arrMenuItems[iIndex].icon;
                else
                    objColDiv.innerHTML = "&nbsp;";
                objRowDiv.appendChild(objColDiv);

                //column: text
                objColDiv = document.createElement("div");
                objColDiv.className = "column";
                objColDiv.innerHTML = this.arrMenuItems[iIndex].text;
                objRowDiv.appendChild(objColDiv);  
                
                //column: shortcut
                objColDiv = document.createElement("div");
                objColDiv.className = "column";
                if (this.arrMenuItems[iIndex].shortcut.length > 0) //only if exists
                {
                    let sShortCuts = "";
                    for (let iIndexShort = 0; iIndexShort < this.arrMenuItems[iIndex].shortcut.length; iIndexShort++)
                    {
                        if (iIndexShort > 0)
                            sShortCuts += " + ";
                        sShortCuts += this.arrMenuItems[iIndex].shortcut[iIndexShort];
                    }
                    objColDiv.innerHTML = sShortCuts;
                }
                else
                    objColDiv.innerHTML = "&nbsp;";
                objRowDiv.appendChild(objColDiv);

                //column: more
                objColDiv = document.createElement("div");
                objColDiv.className = "column";
                if (this.arrMenuItems[iIndex].more) //only if exists
                    objColDiv.innerHTML = this.sSVGMore;
                else
                    objColDiv.innerHTML = "&nbsp;";

                objRowDiv.appendChild(objColDiv);
            }

            
            objParentDiv.appendChild(objRowDiv);
        }

        objParentDiv.id = this.#sHTMLElementId;        
        return objParentDiv;
    }


    /**
     * shows menu at current mouse position
     */
    positionAtMouseCursor()
    {
        const objEvent = window.event;

        let fXPosMenu = 0.0;
        let fYPosMenu = 0.0;
        let fWidthMenu = 0.0;
        let fHeightMenu = 0.0;
        let bNeedsRerender = false;    

        //defaults
        fXPosMenu = objEvent.clientX;
        fYPosMenu = objEvent.clientY;
        fWidthMenu = this.getBoundingClientRect().width;
        fHeightMenu = this.getBoundingClientRect().height;
        
        //RENDER: 1st try:
        this.style.left = objEvent.clientX + "px";
        this.style.top = objEvent.clientY + "px";
    
        //check: menu out of bounds screen and stick it to a side of the screen
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag        
        if(this.isOutOfBoundsScreenRight(this)) 
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds right, stick to right");    
            fXPosMenu = window.innerWidth - fWidthMenu - 20; //-20 for potential scrollbar
        }
        if(this.isOutOfBoundsScreenBottom(this))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds bottom, stick to bottom");      
            fYPosMenu = window.innerHeight - fHeightMenu - 20; //-20 for potential scrollbar
        }

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 2nd try
        this.style.left = fXPosMenu + "px";
        this.style.top = fYPosMenu + "px";
    
    }

    /**
     * show element that is attached to html element (like a button)
     * 
     */
    positionAtAnchorElement()
    {
        let fXPosAnchor = 0.0; //top left x position
        let fYPosAnchor = 0.0; //top left y position
        let fWidthAnchor = 0.0;
        let fHeightAnchor = 0.0;
        let fXPosMenu = 0.0;
        let fYPosMenu = 0.0;
        let fWidthMenu = 0.0;
        let fHeightMenu = 0.0;
        let bNeedsRerender = false;

        //invalid anchor position, fallback to default
        if ((this.iAnchorPos > this.iPosLeft) || (this.iAnchorPos < 0))
            this.iAnchorPos = this.iPosBottom;

        //get coordinates
        const rctAnchor = this.objAnchor.getBoundingClientRect();
        fXPosAnchor = rctAnchor.left;
        fYPosAnchor = rctAnchor.top;
        fWidthAnchor = rctAnchor.width;
        fHeightAnchor = rctAnchor.height;

        const rctMenu = this.getBoundingClientRect();
        fXPosMenu = fXPosAnchor; //default: assume topleft corner of anchor
        fYPosMenu = fYPosAnchor; //default: assume topleft corner of anchor     
        fWidthMenu = rctMenu.width;
        fHeightMenu = rctMenu.height;

    
        //TOP POS: the bottom of menu is top of element
        if (this.iAnchorPos == this.iPosTop)
            fYPosMenu = fYPosAnchor - fHeightMenu;

        //RIGHT POS: the X is the X of the element + width of element
        if (this.iAnchorPos == this.iPosRight)
            fXPosMenu = fXPosAnchor + fWidthAnchor;

        //BOTTOM POS: the top of menu is height of element
        if (this.iAnchorPos == this.iPosBottom)
            fYPosMenu = fYPosAnchor + fHeightAnchor;
        
        //LEFT POS: the X of the menu is the X of anchor - width of the menu
        if (this.iAnchorPos == this.iPosLeft)
            fXPosMenu = fXPosAnchor - fWidthMenu;

        //RENDER: 1st try: render to screen
        this.style.left = fXPosMenu + "px";
        this.style.top = fYPosMenu + "px";



        //check: menu out of bounds screen and try opposite site
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag        
        if(this.isOutOfBoundsScreenTop(this))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds top, try bottom instead");    
            fYPosMenu = fYPosAnchor + fHeightAnchor; //try bottom instead
        }
        if(this.isOutOfBoundsScreenRight(this))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds right, try left instead");    
            fXPosMenu = fXPosAnchor - fWidthMenu; //try left instead
        }
        if(this.isOutOfBoundsScreenBottom(this))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds bottom, try top instead");      
            fYPosMenu = fYPosAnchor - fHeightMenu; //try top instead  
        }
        if(this.isOutOfBoundsScreenLeft(this))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds left, try right instead");   
            fXPosMenu = fXPosAnchor + fWidthAnchor; //try right instead     
        }

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 2nd try
        this.style.left = fXPosMenu + "px";
        this.style.top = fYPosMenu + "px";



        //check: menu out of bounds screen on new position and stick it to a side of the screen
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag
        if(this.isOutOfBoundsScreenTop(this)) //= old bottom
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds top, stick to bottom");    
            fYPosMenu = window.innerHeight - fHeightMenu - 20; //stick to bottom -> -20 for scrollbar
        }
        if(this.isOutOfBoundsScreenRight(this)) //= old left
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds right, stick to left");    
            fXPosMenu = 0.0; //stick to the left
        }
        if(this.isOutOfBoundsScreenBottom(this)) //= old top
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds bottom, stick to top");      
            fYPosMenu = 0.0; //stick to top
        }
        if(this.isOutOfBoundsScreenLeft(this)) //= old right
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds left, stick to right");   
            fXPosMenu = window.innerWidth - fWidthMenu - 20; //stick to right -> -20 for scrollbar
        }        

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 3rd try
        this.style.left = fXPosMenu + "px";
        this.style.top = fYPosMenu + "px";        
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objMenu HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenTop(objMenu)
    {
        if (objMenu.getBoundingClientRect().top < 0.0)
            return true;
        return false;
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objMenu HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenRight(objMenu)
    {
        const fTotalX = objMenu.getBoundingClientRect().left + objMenu.getBoundingClientRect().width;
        if (fTotalX > (window.innerWidth - 20)) //-20 for potential scrollbar
            return true;
        return false;
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objMenu HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenBottom(objMenu)
    {
        const fTotalY = objMenu.getBoundingClientRect().top + objMenu.getBoundingClientRect().height;
        if (fTotalY > (window.innerHeight - 20)) //-20 for potential scrollbar
            return true;
        return false;
    }    

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objMenu HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenLeft(objMenu)
    {
        if (objMenu.getBoundingClientRect().left < 0.0)
            return true;
        return false;
    }    


    /**
     * attach event listeners for THIS object, NOT THE MENU ITEMS
     */
    addEventListeners()
    {

        //when clicked on anchor
        // if (this.objAnchor)
        // {             
        //     this.objAnchor.addEventListener("click", ()=>
        //     {
        //         this.toggle();
        //     }, { signal: this.objAbortController.signal });

        // }

        //when ESC-key pressed
        document.addEventListener("keyup", (objEvent)=>
        {
            switch (objEvent.key) 
            {
                case "Escape":
                    if (this.isShowing())
                    {
                        this.hide(); 
                        if (this.bRemoveFromDOMOnHide)
                            this.removeFromDOM();
                    }       
                    break;
            }
        }, { signal: this.objAbortController.signal }); 

        //when clicked with mouse on document
        document.addEventListener("mousedown", (objEvent)=>
        {               
            if (this.objAnchor)
            {
                if (this.isMouseCursorInRect(objEvent, this.objAnchor.getBoundingClientRect()))
                {
                    this.toggle();
                    if (this.isHidden())
                        if (this.bRemoveFromDOMOnHide)
                            this.removeFromDOM();
                }
                
                //hide when clicked outside the anchor and bubble
                if (this.isShowing())
                {
                    if  (
                            (!this.isMouseCursorInRect(objEvent, this.objAnchor.getBoundingClientRect())) //outside anchor
                            &&
                            (!this.isMouseCursorInRect(objEvent, this.getBoundingClientRect())) //outside bubble
                        )
                        {
                            this.hide();
                            if (this.bRemoveFromDOMOnHide)
                                this.removeFromDOM();
                        }
                }
            }
            else //when no anchor, it is displayed at mouse cursor
            {
                //dissappear when clicked outside menu
                if (this.isShowing())
                {
                    this.hide(); 
                    if (this.bRemoveFromDOMOnHide)
                        this.removeFromDOM();
                }                
            }
            
        }, { signal: this.objAbortController.signal });  

        
    }

    #addEventListenerMenuItem(objHTMLElement, fnCallback)
    {
        objHTMLElement.addEventListener("mousedown", (objEvent)=>
        {               
            objEvent.stopPropagation();
            console.log("menuitem clicked:", objHTMLElement)
            fnCallback();
        }, { signal: this.objAbortController.signal });  
    }

    /**
     * shows bubble
     */
    show()
    {

        this.hidden = false; //we need to show first before we can position (because it needs to render first before we know a width and height)
        if (this.objAnchor)
            this.positionAtAnchorElement();
        else
            this.positionAtMouseCursor();
    }

    /**
     * closes menu
     */
    hide()
    {
        // console.log("menu: hide111111");
        this.hidden = true;
    }    

    /**
     * toggles automatically between show and hide
     */
    toggle()
    {
        if (this.isHidden())
            this.show();
        else
            this.hide();
    }

    isHidden()
    {
        return this.hidden;
    }

    isShowing()
    {
        return !this.hidden;
    }        

    /**
     * remove this element from the DOM
     * an id is needed
     */
    removeFromDOM()
    {
        this.parentNode.removeChild(this);
    }

    /**
     * set anchored element
     * I want to keep it consistent with the attribute, which supplies an id
     * @param {string} sHTMLElementId id of the anchor
     */
    set anchor(sHTMLElementId)
    {
        this.objAnchor = document.getElementById(sHTMLElementId);
    }

    /**
     * get anchored element id
     * I want to keep it consistent with the attribute, which supplies an id
     */
    get anchor()
    {
        return this.objAnchor.id;
    }

    /**
     * set anchored element
     * 
     * @param {HTMLElement} objHTMLElement anchor object
     */
    set anchorobject(objHTMLElement)
    {
        this.objAnchor = objHTMLElement;
    }

    /**
     * get anchored element
     */
    get anchorobject()
    {
        return this.objAnchor;
    }    

    /**
     * set position of menu on anchor
     * 
     * @param {integer} iAnchorPos 
     */
    set anchorpos(iAnchorPos = this.iPosTop)
    {
        this.iAnchorPos = iAnchorPos;
    }

    /**
     * get position of menu on anchor
     */
    get anchorpos()
    {
        return this.iAnchorPos;
    }    

    /**
     * set id of the html element: <div id="THISONE">
     * 
     * @param {string sHTMLElementId 
     */
    set id(sHTMLElementId)
    {
        this.#sHTMLElementId = sHTMLElementId;
    }

    /**
     * get id of the html element: <div id="THISONE">
     */
    get id()
    {
        return this.#sHTMLElementId;
    }    

    /**
     * looks if mouse cursor is within a rectangle
     * 
     * @param {event} objEvent 
     * @param {DOMRect} objRect 
     */
    isMouseCursorInRect(objEvent, objRect)
    {
        if  (
                (objEvent.clientX > objRect.left)
                && 
                (objEvent.clientX < (objRect.left + objRect.width))
                &&
                (objEvent.clientY > objRect.top)
                &&
                (objEvent.clientY < (objRect.top + objRect.height))
            )
        {
            return true;
        }
        else
        {
            return false;
        }
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
            //get items from divs
            this.arrChildElements = [...this.children];
            this.#childrenToMenuItems();

             //read attributes
            this.#readAttributes();

            //render
            this.#populate();
        }

        //reattach abortcontroller when disconnected
        if (this.objAbortController.signal.aborted)
            this.objAbortController = new AbortController();

        //events    
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
customElements.define("dr-context-menu", DRContextMenu);