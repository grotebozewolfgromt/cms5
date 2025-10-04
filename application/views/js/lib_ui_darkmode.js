<?php 
/**
 * lib_ui_darkmode.js
 *
 * stuff for the darkmode
 * 
 * This Javascript file contains the standard javascript for the entire framework, loaded in header
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
 * 02 jan 2025 lib_darkmode.js created
 */
?>
   





/* =====================  DARK MODE ================== */

/**
 * toggle dark mode
 */
function toggleDarkLightMode()
{   
    var bSwitchToDarkMode = true; //false = switch to light mode

    //first time switching?
    if (document.body.style.colorScheme == "") //default is empty on first load of page
    {
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) //request browser preference
            bSwitchToDarkMode = false;                    
    }
    else //subsequent switching
    {
        if (document.body.style.colorScheme == "dark")
            bSwitchToDarkMode = false;
    }


    //doing the actual toggle
    if (bSwitchToDarkMode)
        document.body.style.colorScheme = "dark";
    else
        document.body.style.colorScheme = "light";


    //saving preference
    if (bSwitchToDarkMode)
        localStorage.setItem('darkMode', '1');
    else
        localStorage.setItem('darkMode', '0');
}

/**
 * loading dark mode on page load
 */
document.addEventListener('DOMContentLoaded', () => 
{
    var bSwitchToDarkMode = true; //false = switch to light mode

    //determine what to switch to
    if (localStorage.getItem('darkMode') == null) //not saved? Then take browser default
        bSwitchToDarkMode = (window.matchMedia('(prefers-color-scheme: dark)').matches) //request browser preference
    else
        bSwitchToDarkMode = (localStorage.getItem('darkMode') == '1');


    //actual switch to dark mode
    if (bSwitchToDarkMode) 
        document.body.style.colorScheme = "dark";
    else 
        document.body.style.colorScheme = "light"; 
});            
