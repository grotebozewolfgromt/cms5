<?php 
/**
 * lib_types.js
 *
 * regarding to types
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
 * 02 jan 2025 lib_types.js lib created
 * 25 mrt 2025 lib_types.js parseIntBetter() added
 */
?>
   

/**
 * converts integer to bool
 * 
 * @param {integer} iInteger 
 */
function intToBool(iInteger)
{
    if (iInteger == 0)
        return false;
    else
        return true;
}

/**
 * converts boolean into integer
 * 
 * @param {boolean} bBool 
 * @returns {integer}
 */
function boolToInt(bBool)
{
    if (bBool === false)
        return 0
    else
        return 1;
}

/**
 * converts string into integer
 * wrapper for  parseIntBetter()
 * 
 * @param {string} sString
 * @returns {integer}
 */
function strToInt(sString, iMinValue = 0, iMaxValue = 0)
{
    parseIntBetter(sString, iMinValue, iMaxValue);
}

/**
 * a better integer parser than parseInt():
 * -converts NaN into 0
 * -converts "" into 0
 * -converts max exceeded values into max values
 * -converts min exceeded values into min values
 * 
 * @param {string} sShouldBeInt 
 * @param {int} iMinValue 
 * @param {int} iMaxValue 
 * @returns {int} 
 */
function parseIntBetter(sShouldBeInt, iMinValue = 0, iMaxValue = 0)
{
    if (sShouldBeInt == "")
        return 0;
    
    let mResult = parseInt(sShouldBeInt);

    if (isNaN(mResult))
        return 0;
    
    if (iMaxValue > 0)
        if (mResult > iMaxValue)
            return iMaxValue;

    if (iMinValue < 0)
        if (mResult < iMinValue)
            return iMinValue;

    return mResult;
}   

/**
 * checks if is valid integer
 * 
 * @param {mixed} value 
 * @returns {boolean}
 */
function isInt(mValue) 
{
    var x = parseInt(mValue);
    return !isNaN(mValue) && (x | 0) === x;
}
  
