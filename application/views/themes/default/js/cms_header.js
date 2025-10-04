<?php 
/**
 * cms_header.js
 *
 * This Javascript file contains the javascript for the CMS that is available across all themes
 * except things that need to be handled AFTER the page is loaded ==> see cms_footer.js for that
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
 * 29 apr 2024 header.js created
 */
?>

/**
 * toggle checkboxes on overview form
 * 
 * @param object objSource
 * @param string sCheckboxNames
 * @returns null             
 * */
function toggleAllCheckboxes(objSource, sCheckboxNames) 
{
    checkboxes = document.getElementsByName(sCheckboxNames);
    for(var i=0, n=checkboxes.length;i<n;i++)
    {
        checkboxes[i].checked = objSource.checked;
    }
}

/**
 * toggle <TR> rowcolor when checkbox is checked
 * 
 * @param object objSource
 * @returns null             
 * */
function toggleRowColorCheckboxClick(objSource) 
{
    if (objSource.checked == true)
        objSource.parentElement.parentElement.style.backgroundColor = 'light-dark(var(--lightmode-color-tablerow-selected), var(--darkmode-color-tablerow-selected));'; //selected    
    else
        objSource.parentElement.parentElement.style.backgroundColor = 'light-dark(var(--lightmode-color-tablerow-unselected), var(--darkmode-color-tablerow-unselected));'; //unselected
}            

/**
 * submit quicksearch form when pressing the X
 * 
 * @param  object objInput
 * @returns null             
 * */
function onQuickSearch(objInput) 
{                
    if(objInput.value == "") 
    {
        document.getElementById('frmQuickSearch').submit();
    }
}       


/**
 * Toggle the mobile menu on/off by clicking the button in the left top corner
 */
function toggleHamburgerMenu()
{
    var objDivHamburgerMenuCanvas = document.getElementsByClassName('hamburgermenu-container')[0];
    var objDivHamburgerMenu = document.getElementsByClassName('hamburgermenu')[0];
    var objDivLeftcolumn = document.getElementsByClassName('leftcolumn')[0];
    
    //the actual toggle
    if ((objDivHamburgerMenuCanvas.style.display === '') || (objDivHamburgerMenuCanvas.style.display === 'none'))
    {
        objDivHamburgerMenuCanvas.style.display = 'block';

        //clear contents from hamburger menu
        while (objDivHamburgerMenu.firstChild) 
        {
            objDivHamburgerMenu.removeChild(objDivHamburgerMenu.lastChild);
        }                    

        //copy contents from menu-on-the-left to the hamburger menu
        for (iIndex = 0; iIndex < objDivLeftcolumn.children.length; iIndex++)
        {
            objNewNode = objDivLeftcolumn.children[iIndex].cloneNode(true);
            objDivHamburgerMenu.appendChild(objNewNode);
        }
    }
    else
    {
        objDivHamburgerMenuCanvas.style.display = 'none';
    }
}
