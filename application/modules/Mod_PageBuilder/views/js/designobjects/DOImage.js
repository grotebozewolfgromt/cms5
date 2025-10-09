

/**
 * class for paragraph text
 */
class DOImage extends DesignObject
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_image_title', 'Image') ?>"; //language aware title shown to user
    // sIconSVG = '<svg class="iconchangefill" viewBox="0 0 1000 1000" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g transform="matrix(0.939171,0,0,0.939171,-252.9,-164.526)"><g transform="matrix(306.654,0,0,306.654,1270.48,816.273)"></g><text x="333px" y="816.273px" style="font-family:\'ArialMT\', \'Arial\';font-size:306.654px;">&lt;html&gt;</text></g></svg>';
    sIconSVG = '<svg class="iconchangefill" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M152 120c-26.51 0-48 21.49-48 48s21.49 48 48 48s48-21.49 48-48S178.5 120 152 120zM447.1 32h-384C28.65 32-.0091 60.65-.0091 96v320c0 35.35 28.65 64 63.1 64h384c35.35 0 64-28.65 64-64V96C511.1 60.65 483.3 32 447.1 32zM463.1 409.3l-136.8-185.9C323.8 218.8 318.1 216 312 216c-6.113 0-11.82 2.768-15.21 7.379l-106.6 144.1l-37.09-46.1c-3.441-4.279-8.934-6.809-14.77-6.809c-5.842 0-11.33 2.529-14.78 6.809l-75.52 93.81c0-.0293 0 .0293 0 0L47.99 96c0-8.822 7.178-16 16-16h384c8.822 0 16 7.178 16 16V409.3z"/></svg>';
    sSearchLabelsCSV = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_image_searchlabelscsv', 'image,picture,graphic,photo,icon'); ?>";
    sType = objDOTypes.element; //type
    arrCategories = [objDOCategories.all, objDOCategories.allelements, objDOCategories.multimediaelements]; //category


    /**
     * renders element in designer
     */
    renderDesigner()
   {        
        this.style.marginTop = "10px";
        this.style.marginBottom = "10px";

        this.classList.add("placeholderdoimage");

        const sMod = "<?php echo CMS_CURRENTMODULE; ?>";
        const sModSubDir = "<?php echo $sUploadSubDirectoryName; ?>";

debugger;
        //I use blunt HTML (.innerHTML),instead of create element, because then I can use an event without attaching an eventlistener.
        //This way when saving the document and opening, it is saved in document (I don't have to reattach it)
        const objForeground = document.createElement("div");
        objForeground.innerHTML = '<button onmousedown="openUploadFileManager(\'' + sMod + '\')"><svg class="iconchangefill" style="enable-background:new 0 0 50 50;" version="1.1" viewBox="0 0 50 50" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Layer_1_1_"><path d="M41.841,10H19.255l-4-4H0.841v36h0.033l-0.033,2h41v-1.863L49.159,16h-7.318V10z M14.427,8l4,4h21.414v4H8.083L2.841,34.72 V8H14.427z M39.878,41.73L39.841,42H2.88L9.6,18h36.923L39.878,41.73z"/></g></svg><?php echo transm($sModule, 'pagebuilder_designobject_elements_image_button_fileuploaddialog', 'Open upload browser'); ?></button>';
        objForeground.classList.add("foreground");
        this.appendChild(objForeground);

        const objBackground = document.createElement("div");
        objBackground.classList.add("background");
        objBackground.innerHTML = '<svg class="iconchangefill backgroundimage" viewBox="0 0 510 343" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="Artboard1" style="opacity:0.1" transform="matrix(0.968629,0,0,0.924591,13.3397,-54.9062)"><rect x="-13.772" y="59.384" width="525.772" height="370.364" style="fill:none;"/><clipPath id="_clip1"><rect x="-13.772" y="59.384" width="525.772" height="370.364"/></clipPath><g clip-path="url(#_clip1)"><g transform="matrix(1.27906,0,0,1.2807,-80.1161,-93.2424)"><clipPath id="_clip2"><rect x="49.043" y="95.913" width="413.257" height="312.774"/></clipPath><g clip-path="url(#_clip2)"><path d="M152,120C125.49,120 104,141.49 104,168C104,194.51 125.49,216 152,216C178.51,216 200,194.51 200,168C200,141.49 178.5,120 152,120ZM447.1,32L63.1,32C28.65,32 -0.009,60.65 -0.009,96L-0.009,416C-0.009,451.35 28.641,480 63.091,480L447.091,480C482.441,480 511.091,451.35 511.091,416L511.091,96C511.1,60.65 483.3,32 447.1,32ZM463.1,409.3L326.3,223.4C323.8,218.8 318.1,216 312,216C305.887,216 300.18,218.768 296.79,223.379L190.19,367.479L153.1,321.379C149.659,317.1 144.166,314.57 138.33,314.57C132.488,314.57 127,317.099 123.55,321.379L48.03,415.189C48.03,415.16 48.03,415.218 48.03,415.189L47.99,96C47.99,87.178 55.168,80 63.99,80L447.99,80C456.812,80 463.99,87.178 463.99,96L463.99,409.3L463.1,409.3Z" style="fill-rule:nonzero;"/></g></g></g></g></svg>';
        this.appendChild(objBackground);
   }  

    /**
     * render the toolbar on top of the screen
     * 
     * 
     * @param {HTMLElement} objToolbarContainer --> container <div> of toolbar
     * @param {Array} arrDOInDesigner --> array of selected <div>s in designer
     */
    renderToolbar(objToolbarContainer, arrDOInDesigner)
    {
        objToolbarContainer.innerHTML = ""; //clear toolbar
    }  


    /**
     * PUBLIC FUNCTION
     * rendering the final html for the front-end for this DesignObject
     * 
     * we clean up on a copy of the node
     * because when we do it on the node itself, 
     * it will destoy the functionality of the designer.
     * 
     */
    renderHTML() 
    {
        if (this.classList.contains(CSSCLASS_INVISIBLE_ELEMENT))
            return "";
        else
            return "IMAGE TODO!!!";
    }

     /**
     * render the detail tab
     * 
     * OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
     * 
     * @param {HTMLElement} objTabGridContainer --> container <div> of detailtab
     * @param {HTMLCollection} arrDOInDesigner --> array of selected <div>s in designer
     */
     renderElementTab(objTabGridContainer, arrDOInDesigner)
     {
        //==== sizing
        objTabGridContainer.appendChild(this.renderPropertyWidthHeightContainer(arrDOInDesigner)); 

        //==== padding
        objTabGridContainer.appendChild(this.renderPropertyPaddingContainer(arrDOInDesigner)); 

        //==== margin
        objTabGridContainer.appendChild(this.renderPropertyMarginContainer(arrDOInDesigner)); 

        //==== border
        objTabGridContainer.appendChild(this.renderPropertyBorderContainer(arrDOInDesigner));         

         //==== visibility
         objTabGridContainer.appendChild(this.renderPropertyVisibility(arrDOInDesigner)); 
           
         //==== move
         objTabGridContainer.appendChild(this.renderPropertyMove(arrDOInDesigner)); 
       
         //==== delete
         objTabGridContainer.appendChild(this.renderPropertyDelete(arrDOInDesigner)); 
 
     }
}