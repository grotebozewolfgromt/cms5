
<?php 
/**
 * dr-popover.js (formerly dr-info-bubble)
 *
 * webcomponent for popovers (formerly called: bubbles)
 * Applications can be: 
 * -extra option-bubbles, like an extra checkbox or editbox 
 * -a error message on component (i.e. characters in password not sufficient)
 * -a hint bubble when hovering over "i"-icon
 * -a date picker
 * this component achors to another component, like a button or a textbox or "i"-icon
 * 
 * BEHAVIOR
 * -it will show a bubble next to the anchored element
 * -it will disappear when:
 *      -scrolling
 *      -click outside bubble
 *      -click on the bubble (optional with "hideonclick"-attribute)
 * 
 * EVENTS:
 * "dr-popover-showing" when the bubble started showing
 * "dr-popover-hiding" when the bubble started hiding
 * 
 * WARNING:
 * -this component is hidden by default! So you won't see anything when you add it to the DOM! you can undo this by explicitly adding <dr-popover hideoncreate=false>
 * -you need to use the "for" attribute (same as <label>) to anchor it to another component
 * -when title doesn't show make sure the attribute "showtitle" is true
 * 
 * HTML ATTRIBUTES:
 * - <dr-popover for="anchorid">        ==> MANDATORY
 * - <dr-popover hideonclick>           ==> OPTIONAL hides bubble when clicked on bubble
 * - <dr-popover showonhoveranchor>     ==> OPTIONAL when user hovers over anchor the bubble is shown 
 * - <dr-popover showonclickanchor>     ==> OPTIONAL when user clicks with mouse on anchor the bubble is shown
 * - <dr-popover anchorpos="top">       ==> OPTIONAL either: top, right, bottom or left. Top is assumed by default when attribute is omitted
 * - <dr-popover showcloseicon="false"> ==> OPTIONAL show X icon in the top right corner of the bubble
 * - <dr-popover hideoncreate="true">   ==> OPTIONAL does this bubble needs to be hidden when it is created?
 * - <dr-popover showtitle="true">      ==> OPTIONAL shows title
 *  
 * EXAMPLE:
 *  <dr-popover id="bubbleid" for="buttonid" hideonclick showonclickanchor anchorpos="top">
        Dit is informatie in een popover<br>
        test
    </dr-popover>
    <button id="buttonid">show popover</button>

    EXAMPLE PURE IN JS:
    <button onclick="autocreatebubble()" id="knoppebubbkle">auto create bubble</button>
    <script>
        function autocreatebubble()
        {
            objParent = document.getElementById("contextmenuid2");
            objPopover = new DRPopover();
            objPopover.showtitle = false;
            objPopover.showcloseicon = false;
            objPopover.removeFromDOMOnHide = true;
            objPopover.id = "bubbletje";
            objPopover.anchorobject = document.getElementById("knoppebubbkle");
            objPopover.innerHTML = "halladiejee";
            objParent.appendChild(objPopover);
            objPopover.show();
        }
    </script>

 * 
    CSS VARIABLES:
        --lightmode-color-drpopover-border: rgb(255, 0, 251);
        --lightmode-color-drpopover-background:  rgb(252, 240, 13);
        --darkmode-color-drpopover-border:  rgb(116, 88, 255);
        --darkmode-color-drpopover-background: rgb(112, 81, 112); 
 * 
 * 
 * @todo speech bubble puntje naar anchor
 * 
 * @author Dennis Renirie
 * 
 * 11 mrt 2025 dr-info-bubble.js created
 * 26 apr 2025 dr-info-bubble.js responds to ESC-key
 * 15 may 2025 dr-info-bubble.js auto min width based on anchor
 * 15 may 2025 dr-info-bubble.js dispatches events when bubble is hiding and showing
 * 23 may 2025 dr-info-bubble.js hideonclickoutside is new property + internal value that is taken into account when checking for mouseclickaction
 * 6 jun 2025 dr-info-bubble.js renamed dr-info-bubble ==> dr-popover
 * 18 jun 2025 dr-popover.js show() + css has extra capabilities for overflowing and showing a scrollbar
 */
?> 




class DRPopover extends HTMLElement
{
    #objAnchor = null;
    #bHideOnClickOutside = true;
    #bShowOnHoverAnchor = false;
    #bShowOnClickAnchor = false;
    #iAnchorPos = this.iPosTop;
    #bRemoveFromDOMOnHide = false;
    #bShowCloseIcon = true; //shows X-icon to close bubble
    #bShowTitle = true; //shows title on top of bubble
    sSVGCloseIcon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" /></svg>';
    #objCloseIcon = null; //<div> HTMLElement with close icon
    #objDivTitle = null; //<div> with title
    #sBubbleTitle = ""; //text that describes the contents of the bubble (optional)
    #bConnectedCallbackHappened = false;    

    //class constants;
    iPosTop = 0
    iPosRight = 1;
    iPosBottom = 2;
    iPosLeft = 3;

    static sTemplate = `
        <style>
            :host 
            {
                border-color: light-dark(var(--lightmode-color-drpopover-border, rgb(234, 234, 234)), var(--darkmode-color-drpopover-border, rgb(71, 71, 71)));
                background-color: light-dark(var(--lightmode-color-drpopover-background, rgb(234, 234, 234)), var(--darkmode-color-drpopover-background, rgb(71, 71, 71)));
                border-width: 1px;
                border-style: solid;
                border-radius: 10px;
                padding: 10px;
                width: fit-content;
                /* position: absolute;  geeft mss problemen */
                position: fixed;
                z-index: 10000;
                cursor: default;
                box-sizing: border-box; /* important for setting the width manually based on anchor */

                font-size: 0.7rem;
                line-height: 1.2rem;
                overflow-x: hidden;
                overflow-y: scroll;
            } 


            .title
            {   
                top: 0px;
                width: 100%;
                height: 27px;
                display: block;
                font-size: 12px;
                font-weight: bold;
            }

            .close
            {   
                right: 0px;
                top: 0px;
                position: absolute;

                width: 16px;
                height: 16px;
                cursor: pointer;
                border-radius: 16px;
                padding: 5px;
                margin: 5px;
                background-color: light-dark(var(--lightmode-color-drpopover-closeicon-hover-background, rgb(240, 240, 240)), var(--darkmode-color-drpopover-closeicon-hover-background, rgb(77, 77, 77)));
            }

            .close:hover
            {   
                background-color: light-dark(var(--lightmode-color-drpopover-closeicon-hover-background, rgb(215, 215, 215)), var(--darkmode-color-drpopover-closeicon-hover-background, rgb(109, 109, 109)));
                color: light-dark(var(--lightmode-color-drpopover-closeicon-hover, rgb(171, 17rgb(0, 0, 0)rkmode-color-drpopover-closeicon-hover, rgb(148, 148, 148)));
            }            
        </style>
        <slot></slot>
    `;


    /**
     */
    constructor()
    {
        super();
        this.attachShadow({mode: "open"});

        this.objAbortController = new AbortController();          

        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRPopover.sTemplate;
        
        //get template and clone it
        // const objTemplate = document.getElementById("template-dr-popover");
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);    
          
    }


    readAttributes()
    {
        //showing by default?
        this.hidden = DRComponentsLib.attributeToBoolean(this, "hideoncreate", this.hidden);
        // console.log("valuekabouter", this.hidden, DRComponentsLib.attributeToBoolean(this, "hideoncreate", true));

        //read "for"/"anchor" attribute
        const sFor = DRComponentsLib.attributeToString(this, "for", "");
        if (sFor != "")
            this.#objAnchor = document.getElementById(sFor);    

        const sAnchor = DRComponentsLib.attributeToString(this, "anchor", "");
        if (sAnchor != "")
            this.#objAnchor = document.getElementById(sAnchor);    
    

        //read "hideonclick" attribute
        this.#bHideOnClickOutside = DRComponentsLib.attributeToBoolean(this, "hideonclick", this.#bHideOnClickOutside);


        //read "showonhoveranchor" attribute
        this.#bShowOnHoverAnchor = DRComponentsLib.attributeToBoolean(this, "showonhoveranchor", this.#bShowOnHoverAnchor);

        //read "showclickanchor" attribute
        this.#bShowOnClickAnchor = DRComponentsLib.attributeToBoolean(this, "showonclickanchor", this.#bShowOnClickAnchor);

        //read "anchorpos"
        const sAnchorPos = DRComponentsLib.attributeToString(this, "anchorpos", "top");
        switch (sAnchorPos)
        {
            case "right":
                this.#iAnchorPos = this.iPosRight;
                break;
            case "bottom":
                this.#iAnchorPos = this.iPosBottom;
                break;
            case "left":
                this.#iAnchorPos = this.iPosLeft;
                break;
            default:
                this.#iAnchorPos = this.iPosTop;
        }

        //read "showcloseicon" attribute
        this.#bShowCloseIcon = DRComponentsLib.attributeToBoolean(this, "showcloseicon", this.#bShowCloseIcon); 

        //read "title" attribute
        this.#sBubbleTitle = DRComponentsLib.attributeToString(this, "title", this.#sBubbleTitle);        

        //read "showtitle" attribute
        this.#bShowTitle = DRComponentsLib.attributeToBoolean(this, "showtitle", this.#bShowTitle);          
    }

    /**
     * render shizzle
     */
    populate()
    {
        if (this.#bShowCloseIcon)
        {
            this.#objDivTitle = document.createElement("div");
            this.#objDivTitle.innerHTML = this.#sBubbleTitle;
            this.#objDivTitle.classList.add("title");

            this.#objCloseIcon = document.createElement("div");
            this.#objCloseIcon.classList.add("close");
            this.#objCloseIcon.innerHTML = this.sSVGCloseIcon;
            this.#objDivTitle.appendChild(this.#objCloseIcon);
            // debugger

            this.shadowRoot.children[0].before(this.#objDivTitle);
            // this.shadowRoot.appendChild(objDivSpace);
        }
    }

    updateUI()
    {
        if (this.#objDivTitle)
            this.#objDivTitle.innerHTML = this.#sBubbleTitle;

        if (this.#bShowCloseIcon)
            this.#objDivTitle.appendChild(this.#objCloseIcon);
    }

    /**
     * opens bubble
     * 
     */
    show()
    {
        let fXPosAnchor = 0.0; //top left x position
        let fYPosAnchor = 0.0; //top left y position
        let fWidthAnchor = 0.0;
        let fHeightAnchor = 0.0;
        let fXPosElement = 0.0;
        let fYPosElement = 0.0;
        let fWidthElement = 0.0;
        let fHeightElement = 0.0;
        let bNeedsRerender = false;

        console.log("dr-popover: show() called");

        //checks
        if (!this.#objAnchor)
        {
            console.error("DRPopover: show(): can't show because anchored element not found");
            return;
        }



        //we need to show the menu first, otherwise we don't have a width and height for the menu (determined when rendered in html)
        // this.showPopover();  
        this.hidden = false;    
        // this.addEventListeners();

        //get coordinates
        const rctAnchor = this.#objAnchor.getBoundingClientRect();
        fXPosAnchor = rctAnchor.left;
        fYPosAnchor = rctAnchor.top;
        fWidthAnchor = rctAnchor.width;
        fHeightAnchor = rctAnchor.height;

        const rctMenu = this.getBoundingClientRect();
        fXPosElement = fXPosAnchor; //default: assume topleft corner of anchor
        fYPosElement = fYPosAnchor; //default: assume topleft corner of anchor     
        fWidthElement = rctMenu.width;
        fHeightElement = rctMenu.height;

        //ADJUST MIN WIDTH MENU: based on anchor width
        if ((this.#iAnchorPos == this.iPosTop) || (this.#iAnchorPos == this.iPosBottom))
        {
            if (rctAnchor.width > rctMenu.width) //use anchor width when it is bigger
                this.style.width = rctAnchor.width + "px";
        }

    
        //TOP POS: the bottom of menu is top of element
        if (this.#iAnchorPos == this.iPosTop)
            fYPosElement = fYPosAnchor - fHeightElement;

        //RIGHT POS: the X is the X of the element + width of element
        if (this.#iAnchorPos == this.iPosRight)
            fXPosElement = fXPosAnchor + fWidthAnchor;

        //BOTTOM POS: the top of menu is height of element
        if (this.#iAnchorPos == this.iPosBottom)
            fYPosElement = fYPosAnchor + fHeightAnchor;
        
        //LEFT POS: the X of the menu is the X of anchor - width of the menu
        if (this.#iAnchorPos == this.iPosLeft)
            fXPosElement = fXPosAnchor - fWidthElement;

        //RENDER: 1st try: render to screen
        this.style.left = fXPosElement + "px";
        this.style.top = fYPosElement + "px";
        

        //check: menu out of bounds screen and try opposite side
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag        
        if(this.isOutOfBoundsScreenTop(this))
        {
            bNeedsRerender = true;
            console.log("DRPopover: out of bounds top, try bottom instead");    
            fYPosElement = fYPosAnchor + fHeightAnchor; //try bottom instead
        }
        if(this.isOutOfBoundsScreenRight(this))
        {
            bNeedsRerender = true;
            console.log("DRPopover: out of bounds right, try left instead");    
            fXPosElement = fXPosAnchor - fWidthElement; //try left instead
        }
        if(this.isOutOfBoundsScreenBottom(this))
        {
            bNeedsRerender = true;
            console.log("DRPopover: out of bounds bottom, try top instead");      
            fYPosElement = fYPosAnchor - fHeightElement; //try top instead  
        }
        if(this.isOutOfBoundsScreenLeft(this))
        {
            bNeedsRerender = true;
            console.log("DRPopover: out of bounds left, try right instead");   
            fXPosElement = fXPosAnchor + fWidthAnchor; //try right instead     
        }

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 2nd try
        this.style.left = fXPosElement + "px";
        this.style.top = fYPosElement + "px";



        //check: menu out of bounds screen on new position and stick it to a side of the screen
        //can only check for out-of-bounds when coordinates are set (and thus rendered on the screen)
        bNeedsRerender = false;//reset render flag
        if(this.isOutOfBoundsScreenTop(this)) //= old bottom
        {
            bNeedsRerender = true;
            console.log("DRPopover: also out of bounds top, stick to bottom");    
            fYPosElement = window.innerHeight - fHeightElement - 20; //stick to bottom -> -20 for scrollbar
        }
        if(this.isOutOfBoundsScreenRight(this)) //= old left
        {
            bNeedsRerender = true;
            console.log("DRPopover: also out of bounds right, stick to left");    
            fXPosElement = 0.0; //stick to the left
        }
        if(this.isOutOfBoundsScreenBottom(this)) //= old top
        {
            bNeedsRerender = true;
            console.log("DRPopover: also out of bounds bottom, stick to top");      
            fYPosElement = 0.0; //stick to top
        }
        if(this.isOutOfBoundsScreenLeft(this)) //= old right
        {
            bNeedsRerender = true;
            console.log("DRPopover: also out of bounds left, stick to right");   
            fXPosElement = window.innerWidth - fWidthElement - 20; //stick to right -> -20 for scrollbar
        }        

        //quit when don't need render
        if (!bNeedsRerender) 
            return;

        //RENDER: 3rd try
        this.style.left = fXPosElement + "px";
        this.style.top = fYPosElement + "px";      
        
        
        //Check: if Element is be still outside the screen, 
        //it might be too big for the screen to display, then set the top + height and left + width so it can overflow for the scrollbars
        if(this.isOutOfBoundsScreenTop(this)) 
        {
            this.style.top = 20 + "px"; 
            this.style.height = (window.innerHeight - 40) + "px"; //2x -20 for scrollbars
            console.log("still out of bounds TOP");
        }
        if(this.isOutOfBoundsScreenLeft(this)) 
        {
            this.style.left = 20 + "px"; 
            this.style.width = (window.innerWidth - 40) + "px"; //2x -20 for scrollbars
            console.log("still out of bounds LEFT");
        }           


        //fire event
        this.#dispatchEventShowing(this, "Bubble showing");
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objElement HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenTop(objElement)
    {
        if (objElement.getBoundingClientRect().top < 0.0)
            return true;
        return false;
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objElement HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenRight(objElement)
    {
        const fTotalX = objElement.getBoundingClientRect().left + objElement.getBoundingClientRect().width;
        if (fTotalX > (window.innerWidth - 20)) //-20 for potential scrollbar
            return true;
        return false;
    }

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objElement HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenBottom(objElement)
    {
        const fTotalY = objElement.getBoundingClientRect().top + objElement.getBoundingClientRect().height;
        if (fTotalY > (window.innerHeight - 20)) //-20 for potential scrollbar
            return true;
        return false;
    }    

    /**
     * internal function to check if menu is cutoff by the side of the screen
     * 
     * @param {HTMLElement} objElement HTMLElement object of the menu
     * @return {boolean} true = out of bounds
     */
    isOutOfBoundsScreenLeft(objElement)
    {
        if (objElement.getBoundingClientRect().left < 0.0)
            return true;
        return false;
    }        

    /**
     * closes bubble
     */
    hide()
    {
        this.hidden = true;
        this.#dispatchEventHiding(this, "Bubble hiding");
    }

    /**
     * toggles automatically between show and hide
     */
    toggle()
    {
        if (this.hidden)
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
        const objThisObject = document.getElementById(this.id);//regretfully "this" is not the same :(
        if (objThisObject == null)
            console.error("DRPopover: hide(): can't remove from DOM because id is not found in DOM. Have you assigned an id at all??");
        else
            objThisObject.parentNode.removeChild(objThisObject);
    }

    /**
     * has to remove from DOM on hide?
     */
    set removeFromDOMOnHide(bRemove)
    {
        this.#bRemoveFromDOMOnHide = bRemove;
    }

    /**
     * set has to remove from DOM on hide?
     */
    get removeFromDOMOnHide()
    {
        return this.#bRemoveFromDOMOnHide;
    }

    /**
     * has to hide when clicked-on?_
     */
    set hideonclickoutside(bHide)
    {
        // debugger
        this.#bHideOnClickOutside = bHide;
    }

    /**
     * set has to hide when clicked-on?
     */
    get hideonclickoutside()
    {
        return this.#bHideOnClickOutside;
    }

    /**
     * has to show when hovered over anchor?
     */
    set showonhoveranchor(bShow)
    {
        this.#bShowOnHoverAnchor = bShow;
    }

    /**
     * shas to show when hovered over anchor?
     */
    get showonhoveranchor()
    {
        return this.#bShowOnHoverAnchor;
    }

    /**
     * has to show when clicked on anchor?
     */
    set showonclickanchor(bShow)
    {
        this.#bShowOnClickAnchor = bShow;
    }

    /**
     * shas to show when clicked on anchor?
     */
    get showonclickanchor()
    {
        return this.#bShowOnClickAnchor;
    }

    /**
     * set text for the title
     */
    set title(sTitle)
    {
        this.#sBubbleTitle = sTitle;
    }

    /**
     * get text for title
     */
    get title()
    {
        return this.#sBubbleTitle;
    }

    /**
     * should show title
     */
    set showtitle(bShow)
    {
        this.#bShowTitle = bShow;
    }

    /**
     * should show title
     */
    get showtitle()
    {
        return this.#bShowTitle;
    }


    /**
     * creates event listeners
     */
    addEventListeners()
    {
 
        //regular click on bubble
        // if (this.#bHideOnClickOutside)
        // {
        //     this.addEventListener("mousedown", ()=>
        //     {
        //         this.hide();

        //         if (this.#bRemoveFromDOMOnHide)
        //             this.removeFromDOM();                
        //     }, { signal: this.objAbortController.signal });              
        // }
        /**
         * when window resizes, so may change what needs to be scrolled
         */
        window.addEventListener("resize", (objEvent) => 
        {
            if (this.isShowing())
                this.show();
            // console.log("DRPopover: screen size changed");
        }, { signal: this.objAbortController.signal });  

   
        //dissappear on scroll website
        document.addEventListener("scroll", ()=> 
        {             
            if (!this.isHidden())
            {
                this.hide();

                if (this.#bRemoveFromDOMOnHide)
                    this.removeFromDOM();                
            }
        }, { signal: this.objAbortController.signal });      

        //when ESC-key pressed
        document.addEventListener("keyup", (objEvent)=>
        {
            switch (objEvent.key) 
            {
                case "Escape":
                    // alert("ESCAPPPPP");
                    if (!this.isHidden())
                    {
                        this.hide();

                        if (this.#bRemoveFromDOMOnHide)
                            this.removeFromDOM();                
                    }    
                    break;
            }
        }, { signal: this.objAbortController.signal });         

        //when clicked with mouse on document
        document.addEventListener("mousedown", (objEvent)=>
        {               

            if (this.#bShowOnClickAnchor)
            {
                //clicked on anchored element
                if (this.isMouseCursorInRect(objEvent, this.#objAnchor.getBoundingClientRect()))
                {
                    console.log("mousedown in anchored element");
                    this.toggle();

                    if (this.isHidden())
                        if (this.#bRemoveFromDOMOnHide)
                            this.removeFromDOM();
                }
            }


            //hide when clicked outside the anchor and bubble
            if (this.#bHideOnClickOutside)
            {            
                if (this.isShowing())
                {
                    if  (
                            (!this.isMouseCursorInRect(objEvent, this.#objAnchor.getBoundingClientRect())) //outside anchor
                            &&
                            (!this.isMouseCursorInRect(objEvent, this.getBoundingClientRect())) //outside bubble
                        )
                        {
                            // console.log("mousedown outside rects", this,  this.getBoundingClientRect());
                            console.log("mousedown outside rects anchor or bubble", this);


                            this.hide();

                            if (this.#bRemoveFromDOMOnHide)
                                this.removeFromDOM();                            
                        }
                }       
            }
            
            
        }, { signal: this.objAbortController.signal });  


       
        //close icon
        this.addEventListenersCloseIcon();

        //anchor
        this.addEventListenersAnchor();
    }

    /**
     * creates event listeners for anchor
     */    
    addEventListenersAnchor()
    {
        //shows when user hovers over anchor
        if ((this.#bShowOnHoverAnchor) && (this.#objAnchor !== null))
        {
            this.#objAnchor.addEventListener("mouseover", ()=> 
            {
                this.show();
            }, { signal: this.objAbortController.signal });

            this.#objAnchor.addEventListener("mouseout", ()=> 
            {
                this.hide();

                if (this.#bRemoveFromDOMOnHide)
                    this.removeFromDOM();                
            }, { signal: this.objAbortController.signal });       
        }     
    }

    /**
     * creates event listeners for anchor
     */    
    addEventListenersCloseIcon()
    {
  

        //shows when user hovers over anchor
        if ((this.#bShowCloseIcon) && (this.#objCloseIcon !== null))
        {
            this.#objCloseIcon.addEventListener("mousedown", ()=> 
            {
                console.log("hit close");
                this.hide();
            }, { signal: this.objAbortController.signal });    
        }     
    }


    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.objAbortController.abort();
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
     * set anchored element
     * I want to keep it consistent with the attribute, which supplies an id
     * @param {string} sHTMLElementId id of the anchor
     */
    set anchor(sHTMLElementId)
    {
        this.#objAnchor = document.getElementById(sHTMLElementId);
    }

    /**
     * get anchored element id
     * I want to keep it consistent with the attribute, which supplies an id
     */
    get anchor()
    {
        return this.#objAnchor.id;
    }

    /**
     * set anchored element
     * 
     * @param {HTMLElement} objHTMLElement anchor object
     */
    set anchorobject(objHTMLElement)
    {
        this.#objAnchor = objHTMLElement;
    }

    /**
     * get anchored element
     */
    get anchorobject()
    {
        return this.#objAnchor;
    }    

    set anchorpos(iAnchorPos)
    {
        console.log("dr-popover: set anchorpos() called with value", iAnchorPos);
        this.#iAnchorPos = iAnchorPos;
    }

    get anchorpos()
    {
        return this.#iAnchorPos;
    }        

    set showcloseicon(bShow)
    {
        this.#bShowCloseIcon = bShow;
    }

    get showcloseicon()
    {
        return this.#bShowCloseIcon;
    }  


    static get observedAttributes() 
    {
        return [/* "hideoncreate" , */ "for", "anchor", "hideonclick", "showonhoveranchor", "showonclickanchor", "anchorpos", "showcloseicon", "title", "showtitle"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        switch(sAttrName)
        {
            // case "hideoncreate":
            //     this.hideoncreate = sNewVal;
            //     break;
            case "for":
                this.anchor = sNewVal;
                break;
            case "anchor":
                this.anchor = sNewVal;
                break;
            case "hideonclick":
                this.hideonclick = sNewVal;
                break;
            case "showonhoveranchor":
                this.showonhoveranchor = sNewVal;
                break;
            case "showonclickanchor":
                this.showonclickanchor = sNewVal;
                break;
            case "anchorpos":
                this.anchorpos = sNewVal;
                break;
            case "showcloseicon":
                this.showcloseicon = sNewVal;
                break;
            case "title":
                this.title = sNewVal;
                break;
            case "showtitle":
                this.showtitle = sNewVal;
                break;
        }

        if (this.#bConnectedCallbackHappened)
            this.updateUI();
    }    

    /**
     * dispatch event that bubble is showing
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventShowing(objSource, sDescription)
    {          
        this.dispatchEvent(new CustomEvent("dr-popover-showing",
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
     * dispatch event that bubble is hiding
     * 
     * @param {*} objSource 
     * @param {*} sDescription 
     */
    #dispatchEventHiding(objSource, sDescription)
    {            
        this.dispatchEvent(new CustomEvent("dr-popover-hiding",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription
            }
        }));
    }        

    connectedCallback()
    {
        if (this.#bConnectedCallbackHappened == false) //first time running
        {        
            //hide by default
            this.hidden = true;

            //read attributes
            this.readAttributes();

            //render
            this.populate();
        }
        

        //reattach abortcontroller when disconnected
        if (this.objAbortController.signal.aborted)
            this.objAbortController = new AbortController();

        //event
        this.addEventListeners();    //event listeners ==> reads conditions for showing and hiding, so needs to be last in constructor


        this.#bConnectedCallbackHappened = true;   
    }

    disconnectedCallback()
    {
        console.log("disconnected callback: remove eventlisteners");
        this.removeEventListeners();
    }

}

/**
 * make component available in HTML
 */
customElements.define("dr-popover", DRPopover);