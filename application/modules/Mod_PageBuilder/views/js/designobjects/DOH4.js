
/**
 * class for <h3> text
 */
class DOH4 extends DOText
{
    sTitle = "<?php echo transm($sModule, 'pagebuilder_designobject_elements_h4_title', 'Head 4') ?>"; //language aware title shown to user
    // sIconSVG = '<svg class="iconchangestroke" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 3V21M9 21H15M19 6V3H5V6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'; //icon
    sIconSVG = '<svg class="iconchangefill" viewBox="0 0 1000 1000" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g transform="matrix(0.939171,0,0,0.939171,-271.46,-15.9752)"><g transform="matrix(745.657,0,0,745.657,1286.19,816.273)"></g><text x="333px" y="816.273px" style="font-family:\'ArialMT\', \'Arial\';font-size:745.657px;">H4</text></g></svg>'; //icon
    sSearchLabelsCSV = "h4,<h4>,<?php echo transm($sModule, 'pagebuilder_designobject_elements_h4_searchlabelscsv', 'header,heading,chapter,text'); ?>";
    sType = objDOTypes.element; //type
    arrCategories = [objDOCategories.all, objDOCategories.allelements, objDOCategories.textelements]; //category

    /**
     * renders element in designer
     */
    renderDesigner()
    {
        let objH = document.createElement("h4");
        objH.appendChild(document.createTextNode(this.sPlaceHolder));
        this.appendChild(objH);
    }      
}
