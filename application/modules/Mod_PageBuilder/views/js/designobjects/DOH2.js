

/**
 * class for <h2> text
 */
class DOH2 extends DOText
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_h2_title', 'Head 2') ?>"; //language aware title shown to user
    sIconSVG = '<svg class="iconchangefill" viewBox="0 0 1000 1000" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g transform="matrix(0.939171,0,0,0.939171,-269.922,-14.9494)"><g transform="matrix(745.657,0,0,745.657,1286.19,816.273)"></g><text x="333px" y="816.273px" style="font-family:\'ArialMT\', \'Arial\';font-size:745.657px;">H2</text></g></svg>'; //icon
    sSearchLabelsCSV = "h2,<h2>,<?php echo transm($sModule, 'pagebuilder_designobject_elements_h2_searchlabelscsv', 'header,heading,chapter,text'); ?>";
    sType = objDOTypes.element; //type
    arrCategories = [objDOCategories.all, objDOCategories.allelements, objDOCategories.textelements, objDOCategories.favorites]; //category

 
    /**
     * renders element in designer
     */
    renderDesigner()
    {
        let objH = document.createElement("h2");
        objH.appendChild(document.createTextNode(this.sPlaceHolder));
        this.appendChild(objH);
    }      
}