<?php 
/**
 * lib_string.js
 *
 * string library
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
 * 02 jan 2025 lib_string.js lib created
 */
?>


/**
 * return a safe id or name for a html tag
 * 
 * <div id="supersafe" name="alsosupersafe">
 */
function sanitizeHTMLNodeIdName(sDirtyId)
{
    return sDirtyId.replace(/[^0-9a-zA-Z\-]+/,'');
}

/**
 * return a safe attribute name for a html tag
 * 
 * <div thisnameissupersafe="value">
 */
function sanitizeHTMLAttribute(sDirtyName)
{
    return sDirtyId.replace(/[^0-9a-zA-Z\-]+/,'');
}

/**
 * return a safe attribute or name for a html tag
 * 
 * <div id="very safe value">
 */
function sanitizeHTMLAttributeValue(sDirtyValue)
{
    return sDirtyId.replace(/[^0-9a-zA-Z\-., ]+/,'');
}

/**
 * sanize string to be a valid number
 * 
 * this function allows for a bit more flexibility than parseInt();
 * 
 * @param {sInput} source string
 * @param {bAllowDecimal} allow decimal point
 */
function sanitizeStringNumber(sInput, bAllowDecimal = true, bAllowEmpty = false)
{
    let sResult = "";

    if ((sInput == "") || (sInput == null))
    {
        if (bAllowEmpty)
            return ""
        else
            return 0;
    }

    if (bAllowDecimal)
        sResult = sInput.replace(/[^\d.]+/g, "");
    else
        sResult = sInput.replace(/[^\d]+/g, "");

    if (!bAllowEmpty)
    {
        if (sResult == "")
            return 0;
    }

    return sResult;
}


/**
 * strips html tags from string
 * equivalent to strip_tags() in php
 * 
 * @param {string} sInput 
 * @output string
 */
function strip_tags(sInput)
{
    return sInput.replace(/<[^>]+>/ig,"");
}

/**
 * remove last part of a string
 * 
 * removeEnd("woofmiauw", "miauw") result: "woof"
 * removeEnd("100px", "px") result: "100"
 */
function removeEndString(sInput, sRemove)
{
    const iLenRem = sRemove.length;
    return sInput.slice(0, (0-iLenRem)); 
}


   
  
