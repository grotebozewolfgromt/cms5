

/**
 * class for paragraph text
 */
class DOHTML extends DOText
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_html_title', 'Custom HTML') ?>"; //language aware title shown to user
    sIconSVG = '<svg class="iconchangefill" enable-background="new 0 0 64 64" id="Layer_1" version="1.1" viewBox="0 0 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M22.527,46.916L0,35.193v-5.129l22.527-11.771v6.636L6.773,32.707l15.754,7.568V46.916z M39.613,12l-9.559,42h-5.578  l9.559-42H39.613z M57.229,32.707L41.473,24.93v-6.636L64,30.064v5.129L41.473,46.916v-6.641L57.229,32.707z"/></svg>';
    sSearchLabelsCSV = "htm,html,<html>,code,js,javascript,<?php echo transm($sModule, 'pagebuilder_designobject_elements_html_searchlabelscsv', 'computercode,weblanguage'); ?>";
    sType = objDOTypes.element; //type
    arrCategories = [objDOCategories.all, objDOCategories.allelements, objDOCategories.textelements]; //category
    sPlaceHolder = "<?php echo transm($sModule, 'pagebuilder_designobject_html_placeholder', '<!-- Enter custom HTML code here ... -->') ?>";

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
     * @param {HTMLElement} objHTMLElement Div element of design object in designer
     */
    renderHTML() 
    {
        if (this.classList.contains(CSSCLASS_INVISIBLE_ELEMENT))
            return "";
        else
            return this.innerText;
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
         //==== visibility
         objTabGridContainer.appendChild(this.renderPropertyVisibility(arrDOInDesigner)); 
           
         //==== move
         objTabGridContainer.appendChild(this.renderPropertyMove(arrDOInDesigner)); 
       
         //==== delete
         objTabGridContainer.appendChild(this.renderPropertyDelete(arrDOInDesigner)); 
 
     }


    /**
     * renders element in designer
     */
    renderDesigner()
   {
        let objPre = document.createElement("pre");
        objPre.appendChild(document.createTextNode(this.sPlaceHolder));
        this.appendChild(objPre);
   }     
}