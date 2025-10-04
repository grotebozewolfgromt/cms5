<?php 
/**
 * lib_inet.js
 *
 * internet related functions
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
 * 02 jan 2025 lib_inet.js lib created
 * 12 apr 2025 lib_inet.js bugfix: addVariableToURL(). first part of url was repeated
 */
?>


/**
 * add variable to a url and url-encodes variable and value if it needs to
 * 
 * @param {string} sURL 
 * @param {string} sVariable 
 * @param {string} sValue 
 */
function addVariableToURL(sURL, sVariable, sValue)
{
    //return sURL + '?' + sVariable + '=' + sValue;
    let iPosQuestionmark = 0;
    // let iPosAmpersand = 0;
    // let sURLPreQuestionmark = "";
    // let sURLPostQuestionmark = "";
    let sReturnURL = "";

    //catch if URL is empty
    if ((sURL !== "") && (sURL !== null) && (sURL !== undefined)) 
    {
        iPosQuestionmark = sURL.indexOf("?");
    }
    else
    {
        iPosQuestionmark = -1;
        sURL = ""; //reset url to empty;
    }


    //take first part of the url before question mark
    sReturnURL = sURL.substring(0, iPosQuestionmark);
    sReturnURL += "?";
    
    //does variable exist?
    let sAfterQuestionMark = sURL.substring(iPosQuestionmark+1, sURL.length);
    let arrVars = sAfterQuestionMark.split("&");//separate variables
    let bVarExist = false;
    for (let iIndex = 0; iIndex < arrVars.length; iIndex++)
    { 
        let arrPair = arrVars[iIndex].split("="); //seperate variable from value

        if (iIndex > 0)
            sReturnURL += "&";
        
        //exists?
        if (arrPair[0] == sVariable)
        {
            bVarExist = true; 
            sReturnURL += encodeURIComponent(sVariable) + "=" + encodeURIComponent(sValue);
        }
        else
        {
            sReturnURL += arrVars[iIndex];
        }
    }    

    //if variable existed
    if (bVarExist)
    {
        return sReturnURL; //we built the new url already, so nothing left to do
    }
    else (!bVarExist) //variable not exist: add it
    {
        if (iPosQuestionmark > 0)
        {
            // sURLPostQuestionmark = sURL.slice(iPosQuestionmark, sURL.length -1);
            // sURLPreQuestionmark = sURL.slice(0, iPosQuestionmark -1);
            // iPosAmpersand = sURLPostQuestionmark.indexOf("&"); //we only need to know if one ampersand exists

            return sURL + "&" + encodeURIComponent(sVariable) + "=" + encodeURIComponent(sValue);
        }
        else
        {
            return sURL + "?" + encodeURIComponent(sVariable) + "=" + encodeURIComponent(sValue);
        }
    }

}
   
/**
 * get value of a variable in the url
 * 
 * PHP equivalent of looking in $_GET['variable'] = 'value'
 * 
 * @param {string} sVariable 
 * @returns {string} value of URL variable
 */
function getValueFromURL(sVariable)
{
    var query = window.location.search.substring(1); 
    var vars = query.split("&"); 
    for (var i=0;i<vars.length;i++)
    { 
      var pair = vars[i].split("="); 
      if (pair[0] == sVariable)
      { 
        return pair[1]; 
      } 
    }
    return -1; //not found 
}


/**
 * generate prettyURL
 * 
 * @param {string} sValue 
 * @returns {string}
 */
function generatePrettyURL(sValue)
{
  return sValue.replace(/ /g, "-").replace(/@/g, "").replace(/\$/g, "").replace(/!/g, "").replace(/#/g, "").toLowerCase();
}


/**
 * set a cookie
 */
function setCookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

/**
 * get a cookie
 */
function getCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
    var c = ca[i];
    while (c.charAt(0) == ' ') {
        c = c.substring(1);
    }
    if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
    }
    }
    return "";
}      
  
/**
 * escapes HTML code with html equivalent characters
 * 
 * input: escapeHTML('<a href="#">Me & you</a>');
 * returns: '&lt;a href=&quot;#&quot;&gt;Me &amp; you&lt;/a&gt;'
 * 
 * https://www.30secondsofcode.org/js/s/escape-unescape-html/
 * 
 * @param {string} str 
 * @returns string
 */
const escapeHTML = str =>
    str.replace(
      /[&<>'"]/g,
      tag =>
        ({
          '&': '&amp;',
          '<': '&lt;',
          '>': '&gt;',
          "'": '&#39;',
          '"': '&quot;'
        }[tag] || tag)
    );

/**
 * converts string to HTML code
 * 
 * input: unescapeHTML('&lt;a href=&quot;#&quot;&gt;Me &amp; you&lt;/a&gt;');
 * returns: '<a href="#">Me & you</a>'
 * 
 * https://www.30secondsofcode.org/js/s/escape-unescape-html/
 * 
 * @param {string} str 
 * @returns string
 */    
const unescapeHTML = str =>
    str.replace(
        /&amp;|&lt;|&gt;|&#39;|&quot;/g,
        tag =>
        ({
            '&amp;': '&',
            '&lt;': '<',
            '&gt;': '>',
            '&#39;': "'",
            '&quot;': '"'
        }[tag] || tag)
    );    
