
/**
 * The most barebone element you can get
 * It is used to put 1 or more designobjects in it
 * it is basically a <div> in it's purest form
 * 
 * It is used to:
 * - space components (margin, padding etc) 
 * - colored background for elements
 * - used in grids as an initial drag-and-droptarget
 */
class DOContainer extends DesignObject
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_layouts_container_title', 'Container') ?>"; //language aware title shown to user
    sIconSVG = '<svg class="iconchangecolor" version="1.1" viewBox="-2 -2 22 22" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><title/><desc/><defs/><g fill="none" fill-rule="evenodd" id="Page-1" stroke="none" stroke-width="1"><g fill="currentColor" id="Core" transform="translate(-3.000000, -87.000000)"><g id="check-box-outline-blank" transform="translate(3.000000, 87.000000)"><path d="M16,2 L16,16 L2,16 L2,2 L16,2 L16,2 Z M16,0 L2,0 C0.9,0 0,0.9 0,2 L0,16 C0,17.1 0.9,18 2,18 L16,18 C17.1,18 18,17.1 18,16 L18,2 C18,0.9 17.1,0 16,0 L16,0 L16,0 Z" id="Shape"/></g></g></g></svg>';
    sSearchLabelsCSV = "p,<p>,<?php echo transm($sModule, 'pagebuilder_designobject_layouts_container_searchlabelscsv', 'container,placeholder,space,margin,padding'); ?>";
    sType = objDOTypes.layout; //type
    arrCategories = [objDOCategories.all, objDOCategories.layouts]; //category
    bDropTarget = true; 

    // sPlaceHolder = "<?php echo transm($sModule, 'pagebuilder_designobject_layouts_container_placeholder', 'Drop element inside here...') ?>";


    /**
     * renders element in designer
     */
    renderDesigner()
    {
        this.innerHTML = '<svg class="iconchangecolor" style="width:25px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor" className="w-6 h-6"><path strokeLinecap="round" strokeLinejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>';
        this.children[0].classList.add(objDragDrop.sCSSClassPlaceholder); //mark as placeholder
    }

    /**
     * PROTECTED FUNCTION
     * 
     * cleans a node for rendering HTML
     * 
     * it calls all children to clean up too
     * we want each Design Object to be able to clean up its own node,
     * so,
     * OVERLOAD (AND INHERIT with super.cleanDOForRenderHTML()) THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {HTMLElement} objHTMLElement Div element of design object in designer
     */
    cleanDOForRenderHTML(objHTMLElement)
    {
        let iLenChilds = objHTMLElement.childElementCount;

        //look for SVG children and remove them 
        //needs to happen BEFORE calling parent, because parent is going to look at the children!!!
        if (objHTMLElement.hasChildNodes)
        {
            for (let iIndex = 0; iIndex < iLenChilds; iIndex++)   
            {           
                // if (objHTMLElement.children[iIndex].tagName.toUpperCase() == "SVG")
                if (objHTMLElement.children[iIndex].classList.contains(objDragDrop.sCSSClassPlaceholder))
                {
                    objHTMLElement.removeChild(objHTMLElement.children[iIndex]);
                    iLenChilds--; //adjust length because of removal
                }
            }
        }

        //inherit parent
        super.cleanDOForRenderHTML(objHTMLElement);
    }  
    
    /**
     * returns true or false whether drop is allowed
     * (only makes sense if this.bDropTarget == true)
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {Array} of HTMLElement objects that are currently dragging
     * @param {HTMLElement} objDroppable droppable object from arrDroppables
     * @param {HTMLElement} objTarget actual target that mouse is dragging onto (objDroppable and objTarget can be the same, but often it's not because you are technically dragging onto a sibling element to determine whether to drop before or after)
     * @returns {bool}
     */
    isDropAllowed(arrDraggableElements, objDroppable, objTarget)
    {
        return true;
    }          
}
