<?php 
/**
 * lib_ui_contextmenu.js
 *
 * class to create a right-click contextmenu
 * 
 * WARNING:
 * - don't forget to remove the event listeners with removeEventListeners() to prevent memory leaks:
 *      the best way is to have a global var with menu object:
 *      var objContextMenu = null;
 *      every time you create a new menu, check if it exists, if yes then call removeEventListeners():
 *      if (objContextMenu) //check if exists
 *          objContextMenu.removeEventListeners(); //remove listeners
 *      objContextMenu = new DRContextMenu(); //create new menu
 *      objMenu.addMenuItem("menuitem");
 *      etc...
 * - don't forget to style every menu item to take up just one line (not wrapping):  #mytable td { white-space: nowrap; }
 * 
 * FEATURES:
 * - works on mobile and desktop.
 * - uses the browser popover API (optional)
 * - adds icons (optional)
 * - adds checkmarks (optional)
 * 
 * EXAMPLE JS:
 * objMenu = new DRContextMenu();
    objMenu.addMenuItem("test1", callbackMenu1, ["Control", "Shift", "l"], "", false, false);
    objMenu.addMenuItem("test2", "", [], '<svg class="feather feather-phone" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>', false, true);
    objMenu.addHR();
    objMenu.addMenuItem("test3 dit is een test &gt; blaat hond<button>knoppie</button>", "", [], "icon3", true, false);
    objMenu.addMenuItem("test4", "", [], "icon4", false, true);  
 * var objTable = objMenu.renderAsTable(); //it's rendered, but not shown yet
 * 
 * //method 1: show menu: CSS native                    -->anchoring CSS is still experimental and not supported by every browser
 * <button id="virginbutton">virginbutton</button>
 * objMenu.anchorTo("virginbutton"); 
 * //method 2: show menu: pure JS solution              --> anchored to button
 * <button id="showhere" onclick="objMenu.showAnchorToElement('showhere', 1);">show on element</button>
 * //method 3: show menu: pure JS solution              --> shows at mouse cursor
 * <button id="showhere" onclick="objMenu.show();">show at cursor pos</button>
 * 
 * //don't forget to remove event listeners
 * objMenu.removeEventListeners();
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

.contextmenu
{
    border-color: light-dark(var(--lightmode-color-contextmenu-border), var(--darkmode-color-contextmenu-border)); 
    border-width: 1px;
    border-style: solid;
    background-color: light-dark(var(--lightmode-color-contextmenu-background), var(--darkmode-color-contextmenu-background)); 
    user-select: none;
    border-radius: 20px;
    border-collapse: inherit; 
    cursor: pointer;
}

.contextmenu td
{
    padding: 5px; 
}

.contextmenu tr:hover
{
    background-color: light-dark(var(--lightmode-color-contextmenu-hover), var(--darkmode-color-contextmenu-hover)); 
    border-top-left-radius: 20px;
    border-top-right-radius: 20px;    
}

.contextmenu tr.notallowed > :hover
{
    background-color: light-dark(var(--lightmode-color-contextmenu-background), var(--darkmode-color-contextmenu-background)); 
    cursor: default;
}

.contextmenu svg
{
    width: 16px;
    height: 16px;
}    

.contextmenu td
{
    white-space: nowrap;
}

.contextmenu hr
{
    margin: 20px;
    margin-top: 10px;
    margin-bottom: 10px;
    height: 1px;
    background: light-dark(var( --lightmode-color-contextmenu-hr), var( --lightmode-color-contextmenu-hr)); 
    font-size: 0;
    border: 0;    
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
 * 9 mrt 2025 lib_ui_contextmenu.js created
 */
?>


class DRContextMenu
{
    arrRows = [];
    iRowPointer = 0;
    sSVGChecked = '<svg viewBox="6 6 12 12" xmlns="http://www.w3.org/2000/svg"><path d="M10.5858 13.4142L7.75735 10.5858L6.34314 12L10.5858 16.2427L17.6568 9.1716L16.2426 7.75739L10.5858 13.4142Z" fill="currentColor"/></svg>';
    sSVGMore = '<svg fill="currentColor" style="enable-background:new 0 0 512 512;" viewBox="0 0 512 512" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><polygon points="160,128.4 192.3,96 352,256 352,256 352,256 192.3,416 160,383.6 287.3,256 "/></svg>';
    objAbortController = null;
    sHTMLId = "";
    sHTMLClass = "";

    //class constants;
    iPosTop = 0
    iPosRight = 1;
    iPosBottom = 2;
    iPosLeft = 3;

    constructor()
    {
        this.objAbortController = new AbortController();
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
     * @param {function} fnCallback 
     * @param {array} arrShorcutKeys event.key values like array["Control", "v"] ==> it doesn't attach event listeners
     * @param {string} sSVGIcon 
     * @param {boolean} bChecked 
     * @param {boolean} bMore 
     */
    addMenuItem(sText, fnCallback = "", arrShorcutKeys = [], sSVGIcon = "", bChecked = false, bMore = false, )
    {
        this.arrRows.push({"text": sText, "checked": bChecked, "callback": fnCallback, "shortcut": arrShorcutKeys, "icon": sSVGIcon, "checked": bChecked, "more": bMore, "hr": false});
    }

    /**
     * adds <hr> to menu
     */
    addHR()
    {
        this.arrRows.push({"text": "", "checked": false, "callback": null, "shortcut": [], "icon": "", "checked": false, "more": false, "hr": true});
    }

    /**
     * render menu as <table> element and registers eventlisteners
     * 
     * @param {string} sHTMLId
     * @param {boolean} bPopover use as popover ==> only works when you use the sHTMLId parameter
     * @returns {HTMLElement}
     */
    renderAsTable(sHTMLClass = "contextmenu", sHTMLId = "", bPopover = false)
    {
        const objTable = document.createElement("table");
        const objTBody = document.createElement("tbody");
        let objTR = null;
        let objTD = null;
        this.sHTMLClass = sHTMLClass;
        this.sHTMLId = sHTMLId;

        //loop menu items
        for (let iIndex = 0; iIndex < this.arrRows.length; iIndex++)
        {
            objTR = document.createElement("tr");


            if (this.arrRows[iIndex].hr)
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
                if (this.arrRows[iIndex].checked) //only if exists
                    objTD.innerHTML = this.sSVGChecked;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====icon
                objTD = document.createElement("td");
                if (this.arrRows[iIndex].icon) //only if exists
                    objTD.innerHTML = this.arrRows[iIndex].icon;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====text
                objTD = document.createElement("td");
                if (this.arrRows[iIndex].text) //only if exists
                    objTD.innerHTML = this.arrRows[iIndex].text;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====shortcuts
                objTD = document.createElement("td");
                if (this.arrRows[iIndex].shortcut.length > 0) //only if exists
                {
                    let sShortCuts = "";
                    for (let iIndexShort = 0; iIndexShort < this.arrRows[iIndex].shortcut.length; iIndexShort++)
                    {
                        if (iIndexShort > 0)
                            sShortCuts += " + ";
                        sShortCuts += this.arrRows[iIndex].shortcut[iIndexShort];
                    }
                    objTD.innerHTML = sShortCuts;
                }
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====more
                objTD = document.createElement("td");
                if (this.arrRows[iIndex].more) //only if exists
                    objTD.innerHTML = this.sSVGMore;
                else
                    objTD.innerHTML = "&nbsp;";
                objTR.appendChild(objTD);

                //=====respond to mouse and mobile
                objTR.addEventListener("click", ()=>
                {
                    objTable.hidePopover();
                    if (this.arrRows[iIndex].callback)
                        this.arrRows[iIndex].callback();
                }, { signal: this.objAbortController.signal });

                objTR.addEventListener("touch", ()=> 
                {
                    objTable.hidePopover();
                    if (this.arrRows[iIndex].callback)
                        this.arrRows[iIndex].callback();
                }, { signal: this.objAbortController.signal });       
                
                //====respond to shortcuts     
                /*           
                if (this.arrRows[iIndex].shortcut.length > 0) //only if exists
                {
                    let arrShorts = this.arrRows[iIndex].shortcut; //doesn't accept as parameter, so declare it temp
                    document.addEventListener("keydown", (objEvent) => 
                    {
                        if (!objEvent.repeat) //prevents user holding down key
                        {
                            console.log("keydownnnie", arrShorts, objEvent.key);

                            let bShortcutDetected = true;
                            for (let iIndexShort = 0; iIndexShort < this.arrRows[iIndex].shortcut.length; iIndexShort++)
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

        objTable.appendChild(objTBody);

        if (sHTMLId !== "")
            objTable.id = sHTMLId;
        if (sHTMLClass !== "")
            objTable.className = sHTMLClass;
        if (bPopover)
            objTable.popover = "";

        return objTable;
    }

    /**
     * attach menu to HTMLElement, for example: a button 
     * this way the menu shows up under the button
     * 
     * showAttachedToElement() is a pure JS alternative for this function
     * 
     * this uses anchoring which is in experimental state, see:
     * https://caniuse.com/?search=anchor
     * 
     * ALSO ADD THIS TO YOUR CSS: 
     *     [popover]
    {
        inset: unset;
        top: anchor(bottom);
        left: anchor(center);
        translate: -50%;
    }
     * 
     * 
     * @param {string} sHTMLId 
     */
    anchorTo(sHTMLId)
    {
        const objHTMLElement = document.getElementById(sHTMLId);

        if (!objHTMLElement)
        {
            console.error("DRContextMenu: anchorTo(): HTML element '" + sHTMLId + "' not found.");
            return;
        }

        objHTMLElement.setAttribute("popovertarget", this.sHTMLId);
        objHTMLElement.setAttribute("popovertargetaction", "show");
    }

    /**
     * shows menu at current mouse position
     */
    show()
    {
        const objEvent = window.event;
        const objMenu = document.getElementById(this.sHTMLId);
        let fXPosMenu = 0.0;
        let fYPosMenu = 0.0;
        let fWidthMenu = 0.0;
        let fHeightMenu = 0.0;
        let bNeedsRerender = false;

        if (!objMenu)
        {
            console.error("DRContextMenu: show(): HTML element '" + this.sHTMLId + "' not found. This is set in render function")
            return;
        }

        //we need to show the menu first, otherwise we don't have a width and height for the menu (determined when rendered in html)
        objMenu.showPopover();       

        //defaults
        fXPosMenu = objEvent.clientX;
        fYPosMenu = objEvent.clientY;
        fWidthMenu = objMenu.getBoundingClientRect().width;
        fHeightMenu = objMenu.getBoundingClientRect().height;
        

        //RENDER: 1st try:
        objMenu.style.left = objEvent.clientX + "px";
        objMenu.style.top = objEvent.clientY + "px";
    

        //check: menu out of bounds screen and stick it to a side of the screen
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag        
        if(this.isOutOfBoundsScreenRight(objMenu)) 
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds right, stick to right");    
            fXPosMenu = window.innerWidth - fWidthMenu - 20; //-20 for potential scrollbar
        }
        if(this.isOutOfBoundsScreenBottom(objMenu))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds bottom, stick to bottom");      
            fYPosMenu = window.innerHeight - fHeightMenu - 20; //-20 for potential scrollbar
        }

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 2nd try
        objMenu.style.left = fXPosMenu + "px";
        objMenu.style.top = fYPosMenu + "px";
        

    }

    /**
     * show element that is attached to html element (like a button)
     * 
     * @todo: iAlignedPositionTopRightBottomLeft parameter doesn't work yet
     * @param {string} sHTMLElementId 
     * @param {integer} iAnchorPositionTopRightBottomLeft use class constants:  iPosTop,  iPosTop,  iPosTop,  iPosTop ==> where to show the menu? on top, bottom, left or right? ==> default is bottom
     */
    showAnchorToElement(sAnchorHTMLElementId, iAnchorPositionTopRightBottomLeft = this.iPosBottom)
    {
        const objMenu = document.getElementById(this.sHTMLId);
        const objAnchor = document.getElementById(sAnchorHTMLElementId);
        let fXPosAnchor = 0.0; //top left x position
        let fYPosAnchor = 0.0; //top left y position
        let fWidthAnchor = 0.0;
        let fHeightAnchor = 0.0;
        let fXPosMenu = 0.0;
        let fYPosMenu = 0.0;
        let fWidthMenu = 0.0;
        let fHeightMenu = 0.0;
        let bNeedsRerender = false;

        //checks
        if (!objMenu)
        {
            console.error("DRContextMenu: showAnchorToElement(): HTML element id '" + this.sHTMLId + "' not found. This is set in render function");
            return;
        }
        if (!objAnchor)
        {
            console.error("DRContextMenu: showAnchorToElement(): anchor HTML element id '" + this.sHTMLId + "' not found. This is set as function parameter");
            return;
        }

        //invalid anchor position, fallback to default
        if ((iAnchorPositionTopRightBottomLeft > this.iPosLeft) || (iAnchorPositionTopRightBottomLeft < 0))
            iAnchorPositionTopRightBottomLeft = this.iPosBottom;


        //we need to show the menu first, otherwise we don't have a width and height for the menu (determined when rendered in html)
        objMenu.showPopover();       


        //get coordinates
        const rctAnchor = objAnchor.getBoundingClientRect();
        fXPosAnchor = rctAnchor.left;
        fYPosAnchor = rctAnchor.top;
        fWidthAnchor = rctAnchor.width;
        fHeightAnchor = rctAnchor.height;

        const rctMenu = objMenu.getBoundingClientRect();
        fXPosMenu = fXPosAnchor; //default: assume topleft corner of anchor
        fYPosMenu = fYPosAnchor; //default: assume topleft corner of anchor     
        fWidthMenu = rctMenu.width;
        fHeightMenu = rctMenu.height;

    
        //TOP POS: the bottom of menu is top of element
        if (iAnchorPositionTopRightBottomLeft == this.iPosTop)
            fYPosMenu = fYPosAnchor - fHeightMenu;

        //RIGHT POS: the X is the X of the element + width of element
        if (iAnchorPositionTopRightBottomLeft == this.iPosRight)
            fXPosMenu = fXPosAnchor + fWidthAnchor;

        //BOTTOM POS: the top of menu is height of element
        if (iAnchorPositionTopRightBottomLeft == this.iPosBottom)
            fYPosMenu = fYPosAnchor + fHeightAnchor;
        
        //LEFT POS: the X of the menu is the X of anchor - width of the menu
        if (iAnchorPositionTopRightBottomLeft == this.iPosLeft)
            fXPosMenu = fXPosAnchor - fWidthMenu;

        //RENDER: 1st try: render to screen
        objMenu.style.left = fXPosMenu + "px";
        objMenu.style.top = fYPosMenu + "px";



        //check: menu out of bounds screen and try opposite site
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag        
        if(this.isOutOfBoundsScreenTop(objMenu))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds top, try bottom instead");    
            fYPosMenu = fYPosAnchor + fHeightAnchor; //try bottom instead
        }
        if(this.isOutOfBoundsScreenRight(objMenu))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds right, try left instead");    
            fXPosMenu = fXPosAnchor - fWidthMenu; //try left instead
        }
        if(this.isOutOfBoundsScreenBottom(objMenu))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds bottom, try top instead");      
            fYPosMenu = fYPosAnchor - fHeightMenu; //try top instead  
        }
        if(this.isOutOfBoundsScreenLeft(objMenu))
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: out of bounds left, try right instead");   
            fXPosMenu = fXPosAnchor + fWidthAnchor; //try right instead     
        }

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 2nd try
        objMenu.style.left = fXPosMenu + "px";
        objMenu.style.top = fYPosMenu + "px";



        //check: menu out of bounds screen on new position and stick it to a side of the screen
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag
        if(this.isOutOfBoundsScreenTop(objMenu)) //= old bottom
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds top, stick to bottom");    
            fYPosMenu = window.innerHeight - fHeightMenu - 20; //stick to bottom -> -20 for scrollbar
        }
        if(this.isOutOfBoundsScreenRight(objMenu)) //= old left
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds right, stick to left");    
            fXPosMenu = 0.0; //stick to the left
        }
        if(this.isOutOfBoundsScreenBottom(objMenu)) //= old top
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds bottom, stick to top");      
            fYPosMenu = 0.0; //stick to top
        }
        if(this.isOutOfBoundsScreenLeft(objMenu)) //= old right
        {
            bNeedsRerender = true;
            console.log("DRContextMenu: also out of bounds left, stick to right");   
            fXPosMenu = window.innerWidth - fWidthMenu - 20; //stick to right -> -20 for scrollbar
        }        

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 3rd try
        objMenu.style.left = fXPosMenu + "px";
        objMenu.style.top = fYPosMenu + "px";        
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
     * hides menu
     */
    hide()
    {
        const objMenu = document.getElementById(this.sHTMLId);

        if (!objMenu)
        {
            console.error("DRContextMenu: hide(): HTML element '" + this.sHTMLId + "' not found. This is set in render function")
            return;
        }

        objMenu.hidePopover();
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
    }

}
