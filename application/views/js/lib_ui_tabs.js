<?php 
/**
 * lib_ui_tabs.js
 *
 * UI tabsheet Javascript library
 * This Javascript file contains the standard javascript for the entire framework, loaded in header
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
 * 2 jan 2025 lib_ui_tabs.js separate file
 */
?>



/**
 * class TDivTabsheets
 * 
 * helper class for managing tabsheets that are created with <div>s
 * 
 * 2 SITUATIONS: 
 * ============
 * SITUATION 1: tabheads are <div>s:
 * <div id="tabheadid1">tab description 1<div>
 * <div id="tabheadid2">tab description 2<div>
 * <div id="tabheadid3">tab description 3<div>
 * <div id="tabcontentid1">content 1<div>
 * <div id="tabcontentid2">content 2<div>
 * <div id="tabcontentid3">content 3<div>
 * When the user clicks on the tab-head, the corresponding tab-content <div> is shown
 * This class adds events listeners automatically to show the corresponding <div>
 *
 * SITUATION 2: tabheads is a <select>
 * <select id="selectid">
 *      <option value="tabcontentid1">tab description 1</option>
 *      <option value="tabcontentid2">tab description 2</option>
 *      <option value="tabcontentid3">tab description 3</option>
 * </select>
 * <div id="tabcontentid1">content 1<div>
 * <div id="tabcontentid2">content 2<div>
 * <div id="tabcontentid3">content 3<div>
 * When the user selects another option in <select>, the corresponding tab-content <div> is shown
 * This class adds events listeners automatically to show the corresponding <div>
 * 
 * 
 * IMPORTANT TO KNOW
 * =================
 * - the fist tabsheet is automatically shown, the rest is hidden by default
 * - visible tabs get .style.display="", non-visible tabs get .style.display="none"
 * - the <div>s must already exist in the document, before you can use this class, so instantiate it on a document.load
 * 
 * HOW TO USE
 * ==========
 * On document load: 
 * objTabsNewPanelMobile = new TDivTabsheets("", "selNewMobileCategory");
 * objTabsNewPanelMobile.addTabsheetSelect("tab-content-newstructures-mobile");
 * objTabsNewPanelMobile.addTabsheetSelect("tab-content-newblocks-mobile");
 * objTabsNewPanelMobile.addTabsheetSelect("tab-content-newelements-mobile");
 * objTabsNewPanelMobile.addTabsheetSelect("tab-content-newvariables-mobile");
 * //to show "all tabs": add an option that has an empty value: <option value="">show all tabs</option>
 *
 * objTabsDetailsPanelMobile = new TDivTabsheets("selected");
 * objTabsDetailsPanelMobile.addTabsheetDiv("tab-head-detailsdocument", "tab-content-detailsdocument");
 * objTabsDetailsPanelMobile.addTabsheetDiv("tab-head-detailsstructure", "tab-content-detailsstructure");
 * objTabsDetailsPanelMobile.addTabsheetDiv("tab-head-detailselement", "tab-content-detailselement");    
 */
class TDivTabsheets
{
    #arrTabsheetContents = [];
    #arrTabsheetHeads = [];
    #sSelectedCSSClass = "selected";
    #objSelect = "";

    /**
     * constructor
     * 
     * @param {string} sSelectedCSSClass class that is attached when tabsheet is selected (only used when tab-selector is <div>)
     * @param {string} sSelectId id of the <select>. (Only used when the tab-selector is a <select>)
     */
    constructor(sSelectedCSSClass = "selected", sSelectId = "") 
    {
        //=== when tabsheet selector is a <div>
        this.#sSelectedCSSClass = sSelectedCSSClass;

        //=== when tabsheet selector is <select> (we need to add event listener here in the constructor regretfully, calling a class-function looses availability of internal variables)
        if (sSelectId)
        {
            this.#objSelect = document.getElementById(sSelectId);
            if (this.#objSelect)
            {
                this.#objSelect.addEventListener("change", () =>
                {
                    // console.log("<select> has changed to: " + this.#objSelect.value);

                    if (this.#objSelect.value == "") //if there is no value, "ALL" is assumed
                    {
                        //loop all content <div>s
                        for (let iIndex = 0; iIndex < this.#arrTabsheetContents.length; iIndex++) 
                            this.#arrTabsheetContents[iIndex].style.display = ""; //show all

                        // console.log("value of select is ''");
                    }
                    else
                    {

                        //loop all content <div>s
                        for (let iIndex = 0; iIndex < this.#arrTabsheetContents.length; iIndex++) 
                        {
                            //show selected
                            if (this.#arrTabsheetContents[iIndex].id == this.#objSelect.value) 
                                this.#arrTabsheetContents[iIndex].style.display = "";
                            else
                                this.#arrTabsheetContents[iIndex].style.display = "none";
                        }
                    }
                });
            } //end: if (this.#objSelect)
        } //end: if <selected>

    }


    /**
     * add tabsheet when the tabsheet selector is a <div>-tag
     * 
     * @param {string} sDivIdTabHead <div id="sDivIdTabHead"> of the tab-head
     * @param {string} sDivIdTabContent <div id="sDivIdTabContent"> of the tab-content
     */
    addTabsheetDiv(sDivIdTabHead, sDivIdTabContent)
    {
        var objTempHead = document.getElementById(sDivIdTabHead);
        var objTempContent = document.getElementById(sDivIdTabContent);

        //push head-divs on head array
        if (objTempHead)
        {
            this.#arrTabsheetHeads.push(objTempHead);

            //when tabhead clicked
            objTempHead.addEventListener("click", () => 
            {                
                //=== DIV VISIBILITY

                    //remove visibility all tabs
                    for (let iIndex = 0; iIndex < this.#arrTabsheetContents.length; iIndex++) 
                        this.#arrTabsheetContents[iIndex].style.display = "none";

                    //make clicked tab visible
                    objTempContent.style.display = "";


                //==== CSS CLASSES

                    //remove css classes of all tabs
                    for (let iIndex = 0; iIndex < this.#arrTabsheetHeads.length; iIndex++) 
                        this.#arrTabsheetHeads[iIndex].classList.remove(this.#sSelectedCSSClass);

                    //add css class to div-head
                    objTempHead.classList.add(this.#sSelectedCSSClass);

                //==== UPDATE TAB
                    updateStructureTab();
            });
        }
        else
            console.log("tab-head with id '" + sDivIdTabHead + "' does not exist");

        //push content-divs on content array
        if (objTempContent)
        {
            //keep first tabsheet showing, the rest is set to non-visible
            if (this.#arrTabsheetContents.length > 0)
                objTempContent.style.display = "none";

            this.#arrTabsheetContents.push(objTempContent);
        }
        else
            console.log("tab-content with id '" + sDivIdTabContent + "' does not exist");
    }

    /**
     * add tabsheet when the tabsheet selector is a <select>-tag
     * 
     * CAUTION: you must set the id of the <select> in the constructor, otherwise this WON'T work
     * 
     * <select>
     *  <option value=""> -- show all tabs --</option>
     *  <option value="sDivIdTabContent1">tab 1</option>
     *  <option value="sDivIdTabContent2">tab 2</option>
     *  <option value="sDivIdTabContent3">tab 3</option>
     * </select>
     * 
     * 
     * @param {string} sDivIdTabContent id of the <div> of the tab-content.
     */
    addTabsheetSelect(sDivIdTabContent)
    {
        var objTempContent = document.getElementById(sDivIdTabContent);


        if (!this.#objSelect)
        {
            console.log("can not find <select>-tag in DOM"); 
            return;
        }

        //content not found
        if (!objTempContent)
        {
            console.log("can not find <div>-tag '"+sDivIdTabContent+"' in DOM"); 
            return;
        }
        else
        {
            if (this.#objSelect.value == "") //no value? assume: "show-all-tabs"
            {
                objTempContent.style.display = "";  
            }
            else
            {
                //keep first tabsheet showing, the rest is set to non-visible
                if (this.#arrTabsheetContents.length > 0)
                    objTempContent.style.display = "none";  
            }

            this.#arrTabsheetContents.push(objTempContent);
        }

    }



}