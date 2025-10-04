

/**
 * class for paragraph text
 */
class DOParagraph extends DOText
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_p_title', 'Text') ?>"; //language aware title shown to user
    sIconSVG = '<svg class="iconchangefill" viewBox="0 0 1000 1000" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g transform="matrix(0.939171,0,0,0.939171,-27.8259,-15.9752)"><g transform="matrix(745.657,0,0,745.657,788.477,816.273)"></g><text x="333px" y="816.273px" style="font-family:\'ArialMT\', \'Arial\';font-size:745.657px;">T</text></g></svg>';
    sSearchLabelsCSV = "p,<p>,<?php echo transm($sModule, 'pagebuilder_designobject_elements_p_searchlabelscsv', 'paragraph,text'); ?>";
    sType = objDOTypes.element; //type
    arrCategories = [objDOCategories.all, objDOCategories.allelements, objDOCategories.textelements, objDOCategories.favorites]; //category

    /**
     * renders element in designer
     */
    renderDesigner()
    {
        let objP = document.createElement("p");
        objP.appendChild(document.createTextNode(this.sPlaceHolder));
        this.appendChild(objP);
    }  
}