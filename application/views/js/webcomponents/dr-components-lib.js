/**
 * dr-components-lib.js => DRComponentsLib class
 * 
 * DESCRIPTION:
 * common library for dr web components.
 * 
 * There are some comonalities between DRComponentsLib class and the javascript library of the framework.
 * The goal is keep the web components independent of the framework library
 * 
 * WHY?
 * - Prevents us from writing the same function over and over again for each web component 
 * - easier to maintain: a bug in one function needs to be updated for each component
 * - easier to maintain: each web components gets smaller and therefore easier to maintain
 * - want to keep a separate JS library from our normal JS library in case we decide to open source the component
 * 
 * NAMING CONVENTION:
 * - each component is named <dr-*-*> in html tags (hence the name of the file)
 * - each JS class is named DR*
 * 
 * 16 okt 2025 DRComponentsLib.js add: sanitizeWhitelist()
 */
class DRComponentsLib
{

    /**
     * makes a string from html attribute
     * 
     * @param {HTMLElement} objHTMLObject 
     * @param {string} sAttrName name of the attribute
     * @param {string} sDefault default value
     * @returns string
     */
    static attributeToString(objHTMLObject, sAttrName, sDefault = "")
    {
        if (objHTMLObject.hasAttribute(sAttrName))
            return objHTMLObject.getAttribute(sAttrName);
        
        return sDefault;
    }


    static attributeToBoolean(objHTMLObject, sAttrName, bDefault = false)
    {
        // console.log("attributename: this.getAttribute(sAttrName)", sAttrName, this.getAttribute(sAttrName))
        if (objHTMLObject.getAttribute(sAttrName) !== null)
        {
            if (objHTMLObject.getAttribute(sAttrName) == "")
                return true;

            if (objHTMLObject.getAttribute(sAttrName) == "false")
                return false
            else
                return true;
        }

        return bDefault;
    }    

    static strToBool(sBoolean)
    {
        if (sBoolean === null) //seems counter intuitive but is consistent with "disabled" attribute on html elements
            return false;

        if (sBoolean == true)
            return true;
        if (sBoolean == false)
            return false;

        if (sBoolean == "true")
            return true;
        if (sBoolean == "false")
            return false;

        if (sBoolean == "")
            return true;
    }    

    static boolToStr(bBoolVal)
    {
        if (bBoolVal == true)
            return "true";
        else
            return "false";
    }        

    static attributeToInt(objHTMLObject, sAttrName, iDefault = 0)
    {
        if (objHTMLObject.getAttribute(sAttrName) !== null)
        {
            return this.parseIntBetter(objHTMLObject.getAttribute(sAttrName));
        }

        return iDefault;
    }    

    static attributeToBigInt(objHTMLObject, sAttrName, iDefault = 0n)
    {
        if (objHTMLObject.getAttribute(sAttrName) !== null)
        {
            return BigInt(objHTMLObject.getAttribute(sAttrName));
        }

        return iDefault;
    }    


    static attributeToArray(objHTMLObject, sAttrName, sValueSeparator = ",")
    {
        if (objHTMLObject.hasAttribute(sAttrName))
            return objHTMLObject.getAttribute(sAttrName).split(sValueSeparator);

        return [];
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
    static parseIntBetter(sShouldBeInt, iMinValue = 0, iMaxValue = 0)
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
     * alert with <dr-dialog>
     * 
     * WARNING: this needs dr-dialog.js to be loaded!
     */
    static alert(sTitle, sMessage, sTextButton = "Ok")
    {
        const objDialog = new DRDialog();
        objDialog.setTitle(sTitle);
        objDialog.setBody(sMessage); 
        objDialog.setTransOk(sTextButton); 
        document.querySelector("body").appendChild(objDialog);

        objDialog.showModal();        
    }

    /**
     * add variable to a url and url-encodes variable and value if it needs to
     * 
     * @param {string} sURL 
     * @param {string} sVariable 
     * @param {string} sValue 
     */
    static addVariableToURL(sURL, sVariable, sValue)
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
     * returns a new unique id for a HTMLElement
     * 
     * <div id="myid"> returns: <div id="myid-1">
     * <div id="myid-2"> returns: <div id="myid-3">
     * 
     * @param string sCurrentIDInHTMLDocument this is "myid" in <div id="myid">
     * @param int iMaxTries trying to find a unique id this amount of times
     * @returns string new id, will return "" when max amount of tries exceeded
     */
    static getNewHTMLId(sCurrentIDInHTMLDocument, iMaxTries = 1000)
    {
        const objCurrentElement = document.getElementById(sCurrentIDInHTMLDocument);
        let sNewId = "";
        let iEnumerator = 0;
        let arrIdParts = []; //parts of the id separated by dash (-), including the enumerator
        let arrIdBase = []; //arrIdParts minus the enumerator

        //is not found?
        if (objCurrentElement === null)
        {
            console.warn("getNewHTMLId(): HTML element width id '"+ sCurrentIDInHTMLDocument + "' not found. Returning: '" + getNewHTMLId + "'");
            return sCurrentIDInHTMLDocument;
        }

        //looping to find a unique id
        for (let iIndex = 0; iIndex < iMaxTries; iIndex++)
        {
            arrIdParts = sCurrentIDInHTMLDocument.split("-"); //index 0 is base, index 1 is enumerator  

            //determine the base
            if (arrIdParts.length == 1) //when no enumerator is present
                arrIdBase = arrIdParts;
            else
                arrIdBase = arrIdParts.slice(0, arrIdParts.length - 1); //everything except the last element (=enumerator)

            //determine enumerator
            iEnumerator = parseInt(arrIdParts[arrIdParts.length -1]);
            if (Number.isInteger(iEnumerator))
                iEnumerator++;
            else
                iEnumerator = 1;

            //construct new id
            sNewId = arrIdBase.join("-") + "-" + iEnumerator.toString();

            //check if exists
            if (document.getElementById(sNewId) === null) //not exists, it is unique, thus end function
                return sNewId;
        }

        return ""; //max tries exhausted
    }    


    /**
     * returns mime type for a file extension
     * @param {string} sFileExtension like 'txt'
     * @returns string
     */
    static getMimeTypeFromExtension(sFileExtension) 
    {
        return {
            "aac": "audio/aac",
            "abw": "application/x-abiword",
            "arc": "application/x-freearc",
            "avi": "video/x-msvideo",
            "azw": "application/vnd.amazon.ebook",
            "bin": "application/octet-stream",
            "bmp": "image/bmp",
            "bz": "application/x-bzip",
            "bz2": "application/x-bzip2",
            "cda": "application/x-cdf",
            "csh": "application/x-csh",
            "css": "text/css",
            "csv": "text/csv",
            "doc": "application/msword",
            "docx": "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "eot": "application/vnd.ms-fontobject",
            "epub": "application/epub+zip",
            "gz": "application/gzip",
            "gif": "image/gif",
            "htm": "text/html",
            "html": "text/html",
            "ico": "image/vnd.microsoft.icon",
            "ics": "text/calendar",
            "jar": "application/java-archive",
            "jpeg": "image/jpeg",
            "jpg": "image/jpeg",
            "js": "text/javascript",
            "json": "application/json",
            "jsonld": "application/ld+json",
            "mid": "audio/midi audio/x-midi",
            "midi": "audio/midi audio/x-midi",
            "mjs": "text/javascript",
            "mp3": "audio/mpeg",
            "mp4": "video/mp4",
            "mpeg": "video/mpeg",
            "mpkg": "application/vnd.apple.installer+xml",
            "odp": "application/vnd.oasis.opendocument.presentation",
            "ods": "application/vnd.oasis.opendocument.spreadsheet",
            "odt": "application/vnd.oasis.opendocument.text",
            "oga": "audio/ogg",
            "ogv": "video/ogg",
            "ogx": "application/ogg",
            "opus": "audio/opus",
            "otf": "font/otf",
            "png": "image/png",
            "pdf": "application/pdf",
            "php": "application/x-httpd-php",
            "ppt": "application/vnd.ms-powerpoint",
            "pptx": "application/vnd.openxmlformats-officedocument.presentationml.presentation",
            "rar": "application/vnd.rar",
            "rtf": "application/rtf",
            "sh": "application/x-sh",
            "svg": "image/svg+xml",
            "swf": "application/x-shockwave-flash",
            "tar": "application/x-tar",
            "tif": "image/tiff",
            "tiff": "image/tiff",
            "ts": "video/mp2t",
            "ttf": "font/ttf",
            "txt": "text/plain",
            "vsd": "application/vnd.visio",
            "wav": "audio/wav",
            "weba": "audio/webm",
            "webm": "video/webm",
            "webp": "image/webp",
            "woff": "font/woff",
            "woff2": "font/woff2",
            "xhtml": "application/xhtml+xml",
            "xls": "application/vnd.ms-excel",
            "xlsx": "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "xml": "application/xml",
            "xul": "application/vnd.mozilla.xul+xml",
            "zip": "application/zip",
            "3gp": "video/3gpp",
            "3g2": "video/3gpp2",
            "7z": "application/x-7z-compressed"
        }[sFileExtension] || "application/octet-stream";
    }

    /**
     * filter a string
     * function sanitizes a value against a whitelist of characters
     * when whitelist is empty the whitelist is assumed to be disabled
     * 
     * @param {string} sValue 
     * @param {string} sWhitelistChars when empty, returns original string
     * @return {string}
     */
    static sanitizeWhitelist(sDirtyValue, sWhitelistChars)
    {
        //declare + init
        let sCleanValue = "";
        const iLenValue = sDirtyValue.length;
        const iLenWhite = sWhitelistChars.length;

        //conditions
        if (sDirtyValue.length == 0)
            return "";

        if (sWhitelistChars.length == 0)
            return sDirtyValue;

        //filter white list
        for (let iIndexValue = 0; iIndexValue < iLenValue; ++iIndexValue) //loop letters value
        {
            for (let iIndexWhite = 0; iIndexWhite < iLenWhite; ++iIndexWhite) //loop letters whitelist
            {
                if (sDirtyValue[iIndexValue] === sWhitelistChars[iIndexWhite])
                    sCleanValue+= sDirtyValue[iIndexValue];
            }                                
        }
                       
        return sCleanValue;
    }

    /**
     * Helper function to emit a beep sound in the browser using the Web Audio API.
     * 
     * @param {number} iDuration - The curation of the beep sound in milliseconds.
     * @param {number} iFrequency - The frequency of the beep sound.
     * @param {number} iVolumePercent - The volume of the beep sound in percent
     * 
     * @returns {Promise} - A promise that resolves when the beep sound is finished.
     */
    static beep(iDuration = 100, iFrequency = 400, iVolumePercent = 100)
    {
        return new Promise((resolve, reject) =>
        {
            // Set default iDuration if not provided
            iDuration = iDuration || 200;
            iFrequency = iFrequency || 440;
            iVolumePercent = iVolumePercent || 100;

            try
            {                
                // The browser will limit the number of concurrent audio contexts
                // So be sure to re-use them whenever you can
                const myAudioContext = new AudioContext();

                let oscillatorNode = myAudioContext.createOscillator();
                let gainNode = myAudioContext.createGain();
                oscillatorNode.connect(gainNode);

                // Set the oscillator frequency in hertz
                oscillatorNode.frequency.value = iFrequency;

                // Set the type of oscillator
                oscillatorNode.type= "square";
                gainNode.connect(myAudioContext.destination);

                // Set the gain to the iVolumePercent
                gainNode.gain.value = iVolumePercent * 0.01;

                // Start audio with the desired iDuration
                oscillatorNode.start(myAudioContext.currentTime);
                oscillatorNode.stop(myAudioContext.currentTime + iDuration * 0.001);

                // Resolve the promise when the sound is finished
                oscillatorNode.onended = () => 
                {
                    resolve();
                };
            }
            catch(error)
            {
                reject(error);
            }
        });
    }    
}