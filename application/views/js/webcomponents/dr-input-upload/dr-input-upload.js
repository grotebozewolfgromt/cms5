<?php 
/**
 * dr-upload-file.js
 *
 * class to upload file (needs php counterpart to work)
 * Enables drag-and-drop file uploads
 * This element is designed to resemble <input type="files"> as much as possible
 * 
 * 
 * FEATURES:
 * - renaming files for SEO purposes
 * - checks on uploaded files, to prevent users uploading a malicious payload
 * - progress bar showing upload and processing file
 * - automatic resizing for images and conversion of images to .webp
 * - drag and drop files
 *
 * 
 * ATTRIBUTES:
 * - "transdrop"            --> translation of drop text: "Click or drop file here";
 * - "transtypenotallowed"  --> translation of error text: "File type not accepted";
 * - "transdelete"          --> translation of delete text: "Delete" show in menu
 * - "transpreview"         --> translation of preview text: "Preview" show in menu
 * - "transrename"          --> translation of rename text: "Rename" show in menu
 * - "uploaddirurl"         --> URL where uploads are stored
 * - "uploadnewurl"         --> path to send the upload to
 * - "deleteurl"            --> path to send the delete request to
 * - "renameurl"            --> path to send the rename request to
 * - "renameurl"            --> path to send the rename request to
 * - "uploadfield"          --> name of the internal upload component inside this component
 * - "accept"               --> the accepted mime file types, separated by comma (,)
 * - "whitelist"            --> characters that are allowed in filename
 * - "multiple"             --> multiple files can be uploaded at once
 * 
 * WARNING:
 * 
 * FIRES EVENT: 
 * "change" when file(s) are changed
 * 
 * EXAMPLE:
 * 
 * 
 * DEPENDENCIES:
 * DRComponentsLib
 * <dr-context-menu>
 * <dr-dialog>
 * <dr-input-text>
 * <dr-icon-info>
 * <dr-progress-bar>
 * <dr-icon-info>
 * 
 * XHR AND FETCH:
 * By default we use Fetch to get and send information to/from server.
 * However Fetch doesn't support partial progress (5%, 10%, 20% in progress bar) support yet.
 * This is why we use XHR for the upload function
 * 
 * HOW THIS COMPONENT WORKS:
 *  1. To upload a file we send a file with XHtmlRequest to the server-side counter component.
 *  The server returns a JSON response with the resized filenames, alt text and and image sizes.
 *  When it is a regular file, only the filename value is filled
 *  2. The form value (<form><input value="{[jsondata]}">) of this component is a JSON value as well with resized filenames, alt text and and image sizes.
 * 
 * @todo tabindex=0 doesn't work for some reason
 * 
 * @author Dennis Renirie
 * 
 * 26 aug 2025 dr-upload-file.js created
 */
?>


class DRInputUpload extends HTMLElement
{
    static sTemplate = `
        <style>
            :host 
            {              
                height: var(--height-drinputupload, 300px);
                width: var(--width-drinputupload, 300px);
                display: inline-block; /* otherwise it can be fullscreen */
            }             

            .droppable 
            {              
                box-sizing: border-box;
                border-width: 3px;
                border-style: dashed;
                border-color: light-dark(var(--lightmode-color-drinputupload-border, rgba(205, 205, 205, 1)), var(--darkmode-color-drinputupload-border, rgba(145, 145, 145, 1)));
                background-color: light-dark(var(--lightmode-color-drinputupload-background, rgb(255, 255, 255)), var(--darkmode-color-drinputupload-background, rgb(71, 71, 71)));
                background-size: contain;
                background-repeat: no-repeat;       
                background-position: center;         
                height: 100%;
                width: 100%;
                border-radius: 10px;
                padding: var(--padding-drinputupload, 35%);
                display: flex;
                align-items: center;
                text-align: center;
                justify-content: center;                
                cursor: pointer;
                position: relative;
            }             
            
            .droppable.droptarget
            {
                border-style: solid;
                background-color: light-dark(var(--lightmode-color-drinputupload-background, rgba(0, 140, 254, 0.15)), var(--darkmode-color-drinputupload-background, rgba(170, 217, 255, 0.54)));                
            }

            .thumbnailimage.uploading
            {
                opacity: 0.6
            }            

            .menudots
            {
                border-radius: 30px;
                overflow: hidden;                
                position: absolute;
                top: 15px;
                right: 15px;
                width: 30px;
                height: 30px;
                padding: 5px;                
            }

            .menudots.disabled
            {
                opacity: 0.2;
            }

            .menudots:hover
            {
                background-color: light-dark(var(--lightmode-color-drinputupload-overlay-background, rgba(235, 235, 235, 0.73)), var(--darkmode-color-drinputupload-overlay-background, rgba(51, 51, 51, 0.6)));                            
            }            

            
            dr-progress-bar
            {
                border-radius: 5px;
                overflow: hidden;
                background-color: light-dark(var(--lightmode-color-drinputupload-overlay-background, rgba(235, 235, 235, 0.73)), var(--darkmode-color-drinputupload-overlay-background, rgba(51, 51, 51, 0.6)));                
                position: absolute;
                bottom: 50px;
                left: auto;
                width: calc(100% - 50px); /* dont overflow parent border */
                height: 10px;
                padding: 5px;
            }
            

            .label
            {
                border-bottom-left-radius: 10px;
                border-bottom-right-radius: 10px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                background-color: light-dark(var(--lightmode-color-drinputupload-overlay-background, rgba(235, 235, 235, 0.73)), var(--darkmode-color-drinputupload-background, rgba(51, 51, 51, 0.6)));                
                position: absolute;
                bottom: 0px;
                left: auto;
                width: calc(100% - 10px); /* dont overflow parent border */
                padding: 5px;
            }

        </style>
        <div class="droppable thumbnailimage">
            <slot></slot>
            <div class="menudots">
                <svg fill="currentColor" enable-background="new 0 0 24 24" id="Layer_1" version="1.0" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><circle cx="12" cy="12" r="2"/><circle cx="12" cy="5" r="2"/><circle cx="12" cy="19" r="2"/></svg>
            </div>
            <dr-progress-bar></dr-progress-bar>
            <div class="label"></div>
        </div>
    `;


    #objFormInternals = null;
    #objAbortController = null;
    #bDisabled = false;
    #bDirty = false;
    #arrAcceptedFileTypes = [];
    // #sValue = "";//===> formvalue is rendered in setFormValueJSON();
    #sFileName = "";//original file name, or resized max-size image (when it is an image). when this value is empty, an upload is assumed to be existent
    #sFileNameLarge = "";//large-size file name
    #sFileNameMedium = "";//medium-size file name
    #sFileNameThumbnail = "";//thumbnail-size file name
    #sImageAlt = ""; //alt text for image <img alt="mountain">
    #iImageMaxWidth = 0; //max-size image width
    #iImageMaxHeight = 0; //max-size image height
    #iImageLargeWidth = 0; //large-size image width
    #iImageLargeHeight = 0; //large-size image height
    #iImageMediumWidth = 0; //medium-size image width
    #iImageMediumHeight = 0; //medium-size image height
    #iImageThumbnailWidth = 0; //thumbnail-size image width
    #iImageThumbnailHeight = 0; //thumbnail-size image height
    #sWhitelistChars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890-_.";//whitelisted characters that can exist in a filename
    #bMultipleFilesUpload = false;
    #objDroppable = null; //element which a file can be dropped on
    #objImageThumbnail = null; //image element which represents the thumbnail
    #objLabel = null; //the name on the bottom of the thumbnail
    #objMenuDots = null; //the name on the bottom of the thumbnail
    #objUploadFile = null;//the old-school html fileupload element, used for displaying file-select-dialog
    #objProgressBar = null;//the progressbar showing how much of the file is uploaded
    #objFormInternal = null; //internal form used to upload the file
    #objFormParent = null; //internal form used to upload the file
    #objXHR = null; //internal XMLHttpRequest() object
    #sUploadNewURL = "";//url to upload new files
    #sUploadFieldName = ""; //the form field name of: <input type="file" name="[THISONE]">
    #sDeleteURL = "";  //url to delete the file
    #sRenameURL = "";  //url to rename the file
    #sUploadDirURL = "";//the directory in the url where you can find uploaded files. You can  You can access an uploaded image by concatenating a slash and the filename
    #sCSSClassDropTarget = "droptarget";
    #iMaxSizeFile = 0;
    #bFileExists = false; //registers if a file is found or not in order to display the appropriate icon
    #bIsUploading = false; //is this object currently uploading a file?
    //original SVG 100% zoomed in:  #sSVGIconUploadDefault = '<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" fill="currentColor" style="opacity: 0.2"><path d="M13 10v6H7v-6H2l8-8 8 8h-5zM0 18h20v2H0v-2z"/></svg>'; //default svg icon when no elements are inside
    #sSVGIconUploadDefault = '<svg fill="currentColor" style="opacity: 0.2" viewBox="-8 -6 35 35" xmlns="http://www.w3.org/2000/svg"><path d="M13 10v6H7v-6H2l8-8 8 8h-5zM0 18h20v2H0v-2z"/></svg>'; //default svg icon when no elements are inside
    #sSVGIconUploadOtherFile = '<svg fill="currentColor" style="opacity: 0.2" height="24" viewBox="-6 -5 36 36" width="24" xmlns="http://www.w3.org/2000/svg"><path d="M15,3.41421356 L15,7 L18.5857864,7 L15,3.41421356 Z M19,9 L15,9 C13.8954305,9 13,8.1045695 13,7 L13,3 L5,3 L5,21 L19,21 L19,9 Z M5,1 L15.4142136,1 L21,6.58578644 L21,21 C21,22.1045695 20.1045695,23 19,23 L5,23 C3.8954305,23 3,22.1045695 3,21 L3,3 C3,1.8954305 3.8954305,1 5,1 Z" fill-rule="evenodd"/></svg>'; 
    #sSVGIconDelete = '<svg fill="currentColor" style="enable-background:new -265 388.9 64 64;" version="1.1" viewBox="-265 388.9 64 64" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><path d="M-214.4,400h-9.8l-0.4-1.1c-0.2-0.6-0.8-1-1.5-1h-13.8c-0.7,0-1.3,0.4-1.5,1.1l-0.4,1.1h-9.8c0,0-3.6,0-3.7,7   c0,0.2,0.2,0.5,0.4,0.5h43.6c0.2,0,0.4-0.2,0.4-0.5C-210.9,400-214.4,400-214.4,400z"/><path d="M-214.2,410.6h-37.5c-0.2,0-0.3,0.2-0.3,0.3l3,31.5c0.1,0.8,0.8,1.4,1.6,1.4h28.9c0.8,0,1.5-0.6,1.6-1.4l3-31.5   C-213.9,410.8-214.1,410.6-214.2,410.6z M-226.6,420.2h-12.7V417h12.7V420.2z"/></g></svg>';   
    #sSVGIconPreview = '<svg fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><g data-name="32. View" id="_32._View"><path d="M23.909,11.582C21.943,7.311,17.5,3,12,3S2.057,7.311.091,11.582a1.008,1.008,0,0,0,0,.836C2.057,16.689,6.5,21,12,21s9.943-4.311,11.909-8.582A1.008,1.008,0,0,0,23.909,11.582ZM12,19c-4.411,0-8.146-3.552-9.89-7C3.854,8.552,7.589,5,12,5s8.146,3.552,9.89,7C20.146,15.448,16.411,19,12,19Z"/><path d="M12,7a5,5,0,1,0,5,5A5.006,5.006,0,0,0,12,7Zm0,8a3,3,0,1,1,3-3A3,3,0,0,1,12,15Z"/></g></svg>';
    #sSVGIconRename = '<svg fill="none" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" d="M10 4H8V6H5C3.34315 6 2 7.34315 2 9V15C2 16.6569 3.34315 18 5 18H8V20H10V4ZM8 8V16H5C4.44772 16 4 15.5523 4 15V9C4 8.44772 4.44772 8 5 8H8Z" fill="currentColor" fill-rule="evenodd"/><path d="M19 16H12V18H19C20.6569 18 22 16.6569 22 15V9C22 7.34315 20.6569 6 19 6H12V8H19C19.5523 8 20 8.44771 20 9V15C20 15.5523 19.5523 16 19 16Z" fill="currentColor"/></svg>';
    #sSVGIconAlt = '<svg fill="currentColor" height="24" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><path d="m16 2.012 3 3L16.713 7.3l-3-3zM4 14v3h3l8.299-8.287-3-3zm0 6h16v2H4z"/></svg>';
    #sSVGIconCopyClipboard = '<svg fill="currentColor" style="enable-background:new 0 0 24 24;" version="1.1" viewBox="0 0 24 24" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><style type="text/css">.st0{display:none;}.st1{display:inline;}.st2{opacity:0.2;fill:none;stroke-width:5.000000e-02;stroke-miterlimit:10;}</style><g class="st0" id="grid_system"/><g id="_icons"><path d="M17,3h-6C8.8,3,7,4.8,7,7c-2.2,0-4,1.8-4,4v6c0,2.2,1.8,4,4,4h6c2.2,0,4-1.8,4-4c2.2,0,4-1.8,4-4V7C21,4.8,19.2,3,17,3z    M15,17c0,1.1-0.9,2-2,2H7c-1.1,0-2-0.9-2-2v-6c0-1.1,0.9-2,2-2h1h5c1.1,0,2,0.9,2,2v5V17z M19,13c0,1.1-0.9,2-2,2v-4   c0-2.2-1.8-4-4-4H9c0-1.1,0.9-2,2-2h6c1.1,0,2,0.9,2,2V13z"/></g></svg>';
    #sSVGIconAbort = '<svg fill="currentColor" height="200" id="Layer_1" viewBox="0 0 200 200" width="200" xmlns="http://www.w3.org/2000/svg"><path d="M114,100l49-49a9.9,9.9,0,0,0-14-14L100,86,51,37A9.9,9.9,0,0,0,37,51l49,49L37,149a9.9,9.9,0,0,0,14,14l49-49,49,49a9.9,9.9,0,0,0,14-14Z"/></svg>';
    #sSVGIconFileNotFound = '<svg fill="currentColor" style="opacity: 0.2" baseProfile="tiny" height="24px" id="Layer_1" version="1.2" viewBox="0 0 24 24" width="24px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M12,4c-4.411,0-8,3.589-8,8s3.589,8,8,8s8-3.589,8-8S16.411,4,12,4z M7,12c0-0.832,0.224-1.604,0.584-2.295l6.711,6.711  C13.604,16.776,12.832,17,12,17C9.243,17,7,14.757,7,12z M16.416,14.295L9.705,7.584C10.396,7.224,11.168,7,12,7  c2.757,0,5,2.243,5,5C17,12.832,16.776,13.604,16.416,14.295z"/></svg>';
    sTransDropFileHere = "Click or drop file here";
    sTransFileTypeNotSupported = "File type not allowed";
    sTransDelete = "Delete";
    sTransPreview = "Preview";
    sTransRename = "Rename";
    sTransAlt = "Set img alt text";
    sTransAltDescription = "Change or set ALT text of image: <dr-icon-info>Images can have an alternative (alt) text to describe the contents of the image.<br>This is useful for SEO purposes, text-based browsers, screen readers and if image can not be displayed because of an error.<br>Example: &lt;img src=&quot;mountain.jpg&quot; alt=&quot;boat on a lake with mountain in background&quot;&gt;</dr-icon-info>";
    sTransAltSetButton = "Set text";
    sTransCopyURLClipboard = "Copy URL to clipboard";
    sTransCopyURLClipboardDone = "Copied URL to clipboard:";
    sTransCancel = "Cancel";
    sTransError = "Error";
    sTransErrorUnknownOccurred = "Error occurred when uploading.<br>Do you have a working network/internet connection?";
    sTransErrorTimeOut = "Connection timed out";
    sTransErrorMaxSizeExceeded = "Maximum file size exceeded.<br>Max file size: ";
    sTransOk = "Ok";
    sTransClose = "Close";
    sTransUploading = "Uploading file ...";
    sTransProcessing = "Processing file ...";
    sTransQueue = "In queue ...";
    sTransAbort = "Abort upload";
    sTransFileNotFound = "File not found";
    sTransDeleteBeforeUpload = "Please delete the old file first, before uploading a new one";

    #bConnectedCallbackHappened = false;    

    static formAssociated = true;        

    /**
     * 
     */
    constructor()
    {
        super();
        this.#objFormInternals = this.attachInternals();           
        this.#objAbortController = new AbortController();

        this.attachShadow({mode: "open", delegatesFocus: true });


        const objTemplate = document.createElement("template");
        objTemplate.innerHTML = DRInputUpload.sTemplate;
        

        //get template and clone it
        const objCloneTemplate = objTemplate.content.cloneNode(true); 
        this.shadowRoot.appendChild(objCloneTemplate);   

    }    

    #readAttributes()
    {
        this.sTransDropFileHere = DRComponentsLib.attributeToString(this, "transdrop", this.sTransDropFileHere);
        this.sTransFileTypeNotSupported = DRComponentsLib.attributeToString(this, "transtypenotsupported", this.sTransFileTypeNotSupported);
        this.sTransDelete = DRComponentsLib.attributeToString(this, "transdelete", this.sTransDelete);
        this.sTransPreview = DRComponentsLib.attributeToString(this, "transpreview", this.sTransPreview);
        this.sTransRename = DRComponentsLib.attributeToString(this, "transrename", this.sTransRename);
        this.sTransAlt = DRComponentsLib.attributeToString(this, "transalt", this.sTransAlt);
        this.sTransAltDescription = DRComponentsLib.attributeToString(this, "transaltdescription", this.sTransAltDescription);
        this.sTransAltSetButton = DRComponentsLib.attributeToString(this, "transaltsetbutton", this.sTransAltSetButton);
        this.sTransCopyURLClipboard = DRComponentsLib.attributeToString(this, "transcopyrulclipboard", this.sTransCopyURLClipboard);
        this.sTransCopyURLClipboardDone = DRComponentsLib.attributeToString(this, "transcopyurlclipboarddone", this.sTransCopyURLClipboardDone);
        this.sTransClose = DRComponentsLib.attributeToString(this, "transclose", this.sTransClose);
        this.sTransCancel = DRComponentsLib.attributeToString(this, "transcancel", this.sTransCancel);
        this.sTransError = DRComponentsLib.attributeToString(this, "transerror", this.sTransError);
        this.sTransErrorUnknownOccurred = DRComponentsLib.attributeToString(this, "transerrorunknown", this.sTransErrorUnknownOccurred);
        this.sTransErrorTimeOut = DRComponentsLib.attributeToString(this, "transerrortimeout", this.sTransErrorTimeOut);
        this.sTransErrorMaxSizeExceeded = DRComponentsLib.attributeToString(this, "transerrormaxsizeexceed", this.sTransErrorMaxSizeExceeded);
        this.sTransOk = DRComponentsLib.attributeToString(this, "transok", this.sTransOk);
        this.sTransUploading = DRComponentsLib.attributeToString(this, "transuploading", this.sTransUploading);
        this.sTransProcessing = DRComponentsLib.attributeToString(this, "transprocessing", this.sTransProcessing);
        this.sTransQueue = DRComponentsLib.attributeToString(this, "transqueue", this.sTransQueue);
        this.sTransAbort = DRComponentsLib.attributeToString(this, "transabort", this.sTransAbort);
        this.sTransFileNotFound = DRComponentsLib.attributeToString(this, "transfilenotfound", this.sTransFileNotFound);
        this.sTransDeleteBeforeUpload = DRComponentsLib.attributeToString(this, "transdeletebeforeupload", this.sTransDeleteBeforeUpload);
        this.#sUploadNewURL = DRComponentsLib.attributeToString(this, "uploadnewurl", this.#sUploadNewURL); //url to upload new files
        this.#sUploadFieldName = DRComponentsLib.attributeToString(this, "uploadfield", this.#sUploadFieldName);
        if (this.#sUploadFieldName == "")
            console.error("DRInputUpload: 'uploadfield' attribute is empty. Uploading will not work");
        this.#sDeleteURL = DRComponentsLib.attributeToString(this, "deleteurl", this.#sDeleteURL);        
        this.#sRenameURL = DRComponentsLib.attributeToString(this, "renameurl", this.#sRenameURL);        
        this.#sUploadDirURL = DRComponentsLib.attributeToString(this, "uploaddirurl", this.#sUploadDirURL); //the directory in the url where you can find uploaded files. You can  You can access an uploaded image by concatenating a slash and the filename        
        this.#arrAcceptedFileTypes = DRComponentsLib.attributeToArray(this, "accept", ",");
        this.#sWhitelistChars = DRComponentsLib.attributeToString(this, "whitelist", this.#sWhitelistChars);
        this.#bMultipleFilesUpload = DRComponentsLib.attributeToBoolean(this, "multiple", this.#bMultipleFilesUpload);
        this.#iMaxSizeFile = DRComponentsLib.attributeToInt(this, "maxsize", this.#iMaxSizeFile);

        //read "value" attribute
        const sValue = DRComponentsLib.attributeToString(this, "value", "");
        if (sValue !== "")
            this.processValueJSON(JSON.parse(sValue));

        //add attributes when nessesary
        if (!this.hasAttribute('tabindex')) 
        {
            this.setAttribute('tabindex', 0);
        }           
    }

 
    populate()
    {    
        this.#objDroppable = this.shadowRoot.querySelector(".droppable");
        this.#objImageThumbnail = this.shadowRoot.querySelector(".thumbnailimage");
        this.#objLabel = this.shadowRoot.querySelector(".label");
        this.#objMenuDots = this.shadowRoot.querySelector(".menudots");
        this.#objUploadFile = document.createElement("input");
        this.#objUploadFile.type = "file";
        this.#objUploadFile.name = this.#sUploadFieldName;
        this.#objUploadFile.accept = this.getAccept();
        this.#objUploadFile.multiple = this.getAccept();

        this.#objProgressBar = this.shadowRoot.querySelector("dr-progress-bar");
        this.#objProgressBar.style.display = "none";

        this.#objFormInternal = document.createElement("form");
        this.#objFormInternal.name = "frmUpload";
        this.#objFormInternal.id = "frmUpload";
        this.#objFormInternal.appendChild(this.#objUploadFile)

        this.#objFormParent = this.closest("form");


        //update UI
        this.updateUI();
    }


    /**
     * attach event listenres
     */
    addEventListeners()
    {      
        //==== dragover
        this.#objDroppable.addEventListener("dragover", (objEvent)=>
        {
            objEvent.preventDefault();
            this.#objDroppable.classList.add(this.#sCSSClassDropTarget);
        }, { signal: this.#objAbortController.signal });  


        //==== dragleave + dragend
        ["dragleave", "dragend"].forEach (sType => 
        {
            this.#objDroppable.addEventListener(sType, (objEvent)=>
            {
                this.#objDroppable.classList.remove(this.#sCSSClassDropTarget);
            }, { signal: this.#objAbortController.signal });  
        }, { signal: this.#objAbortController.signal });  


        //==== dragend
        this.#objDroppable.addEventListener("dragend", (objEvent)=>
        {
            this.#objDroppable.classList.remove(this.#sCSSClassDropTarget);
        }, { signal: this.#objAbortController.signal });  


        //==== drop
        this.#objDroppable.addEventListener("drop", (objEvent)=>
        {
            objEvent.preventDefault();   
            this.#objDroppable.classList.remove(this.#sCSSClassDropTarget);

            this.#processMultipleFileUpload(objEvent.dataTransfer.files);
        }, { signal: this.#objAbortController.signal });  


        //==== click
        this.#objDroppable.addEventListener("mousedown", (objEvent)=>
        {
            this.#objUploadFile.click();
        }, { signal: this.#objAbortController.signal });  


        //==== objUploadFile change
        this.#objUploadFile.addEventListener("change", (objEvent)=>
        {       
           this.#processMultipleFileUpload(this.#objUploadFile.files);
        }, { signal: this.#objAbortController.signal });   
        

        //==== menu dots
        this.#objMenuDots.addEventListener("mousedown", (objEvent)=>
        {            
            objEvent.stopPropagation(); //stop click on background

            if (this.#sFileName) //only show when there is a file uploaded
            {
                const objMenu = new DRContextMenu(true); //true if you want to remove from DOM after hide()
                objMenu.anchorobject = this.#objMenuDots;
                objMenu.anchorpos = objMenu.iPosBottom;
                objMenu.setAttribute("id", "3dotsmenu");
                objMenu.addMenuItem(this.sTransDelete, ()=>this.#handleDeleteFile(objMenu), [], this.#sSVGIconDelete);
                objMenu.addMenuItem(this.sTransPreview, ()=>this.#handlePreviewFile(objMenu), [], this.#sSVGIconPreview);
                objMenu.addMenuItem(this.sTransRename, ()=>this.#handleRenameFile(objMenu), [], this.#sSVGIconRename);
                if (this.isImage())
                    objMenu.addMenuItem(this.sTransAlt, ()=>this.#handleChangeAlt(objMenu), [], this.#sSVGIconAlt);
                objMenu.addHR();
                objMenu.addMenuItem(this.sTransCopyURLClipboard, ()=>this.#handleCopyURLClipboard(objMenu), [], this.#sSVGIconCopyClipboard);
                if (this.#objXHR)
                    if ((this.#objXHR.readyState == 1) || (this.#objXHR.readyState == 2) || (this.#objXHR.readyState == 3)) //if OPENED, HEADERS_RECEIVED or LOADING (https://developer.mozilla.org/en-US/docs/Web/API/XMLHttpRequest/readyState) 
                        objMenu.addMenuItem(this.sTransAbort, ()=>this.#handleAbortUpload(objMenu), [], this.#sSVGIconAbort);

                // objMenu.addMenuItem("paste from clipboard", ()=>this.#handlePasteFromClipboard(objMenu, objEvent), [], this.#sSVGIconCopyClipboard);
                // if (this.getMultiple())
                //     objMenu.addMenuItem("New uploader", ()=>this.createSiblingInputUpload());

                this.#objMenuDots.appendChild(objMenu);
            
                objMenu.show();
            }
        }, { signal: this.#objAbortController.signal });           
    }

    /**
     * handles deletion when clicked on "delete" in context menu
     * 
     * @param {DRContextMenu} objMenu 
     */
    async #handleDeleteFile(objMenu)
    {
        objMenu.hide();
        // console.log(objMenu, 'meeeennnuuuu');

        //if file doesn't exist: don't try to delete it from server
        if (!this.#bFileExists)
        {
            this.reset();
            return;
        }


        this.#sDeleteURL = DRComponentsLib.addVariableToURL(this.#sDeleteURL, "inputupload-filename", this.#sFileName);
        this.#sDeleteURL = DRComponentsLib.addVariableToURL(this.#sDeleteURL, "inputupload-filenamelarge", this.#sFileNameLarge);
        this.#sDeleteURL = DRComponentsLib.addVariableToURL(this.#sDeleteURL, "inputupload-filenamemedium", this.#sFileNameMedium);
        this.#sDeleteURL = DRComponentsLib.addVariableToURL(this.#sDeleteURL, "inputupload-filenamethumbnail", this.#sFileNameThumbnail);

        try 
        {
            const objResponse = await fetch(this.#sDeleteURL);
            if (!objResponse.ok) 
            {
                throw new Error(`Response status: ${objResponse.status}`);
            }

            const objResult = await objResponse.json();

            //==== for debugging:
            //const objResult = await objResponse.text();
            //console.log(objResult);

            if (objResult.errorcode === 0)
            {
                console.log(objResult.message);
                
                //remove from DOM
                if (this.#bMultipleFilesUpload)
                {
                    if (this.#needsNewInputUpload())
                        this.reset();
                    else
                        this.parentElement.removeChild(this);
                        
                }
                else                    
                    this.reset();
                
                this.#dispatchEventChange(this.#objUploadFile, "file deleted");
            }
            else //error
            {
                DRComponentsLib.alert(`${this.sTransError} (${objResult.errorcode})`, objResult.message, this.sTransOk);
                console.error(objResult.message, `Error code: ${objResult.errorcode}`);                
            }
            
        } 
        catch (error) 
        {
            console.error(error.message);
        }
    }

    /**
     * handles rename when clicked on "delete" in context menu
     * 
     * @param {DRContextMenu} objMenu 
     */
    async #handleRenameFile(objMenu)
    {
        let sFileNameWithoutExtension = "";
        let iPosDot = 0;

        objMenu.hide();


        //strip file extension
        iPosDot = this.#sFileName.indexOf(".");
        if (iPosDot == -1) //no dot in filename
            sFileNameWithoutExtension = this.#sFileName;
        else
            sFileNameWithoutExtension = this.#sFileName.substring(0, iPosDot);


        //==== SHOW DIALOG (rename prompt)
        const objDialog = new DRDialog();
        const objEdtRename = new DRInputText();
        objDialog.populate();
        objDialog.setTitle(this.sTransRename);

        objEdtRename.style.width = "100%";
        objEdtRename.value = sFileNameWithoutExtension;
        objEdtRename.setWhitelist(this.#sWhitelistChars);
        objDialog.getBodyObject().appendChild(objEdtRename);


        //cancel button
        const objBtnCancel = document.createElement("button");
        objBtnCancel.innerHTML = this.sTransCancel;
        objBtnCancel.addEventListener("click", (objEvent)=>
        {
            objDialog.close();
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objBtnCancel);  
        
        
        //rename button
        const objBtnRename = document.createElement("button");
        objBtnRename.innerHTML = this.sTransRename;
        objBtnRename.classList.add("default");
        objBtnRename.addEventListener("click", (objEvent)=>
        {
            this.#handleRenameButton(objEdtRename.value);
            objDialog.close();
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objBtnRename);


        objDialog.showModal();  

    }


    /**
     * handles rename in button in file rename dialog
     */
    async #handleRenameButton(sFileNameNew)
    {
        this.#sRenameURL = DRComponentsLib.addVariableToURL(this.#sRenameURL, "inputupload-filename", this.#sFileName);
        this.#sRenameURL = DRComponentsLib.addVariableToURL(this.#sRenameURL, "inputupload-filenamenew", sFileNameNew);
        this.#sRenameURL = DRComponentsLib.addVariableToURL(this.#sRenameURL, "inputupload-filenamelarge", this.#sFileNameLarge);
        this.#sRenameURL = DRComponentsLib.addVariableToURL(this.#sRenameURL, "inputupload-filenamemedium", this.#sFileNameMedium);
        this.#sRenameURL = DRComponentsLib.addVariableToURL(this.#sRenameURL, "inputupload-filenamethumbnail", this.#sFileNameThumbnail);

        try 
        {
            const objResponse = await fetch(this.#sRenameURL);
            if (!objResponse.ok) 
            {
                throw new Error(`Response status: ${objResponse.status}`);
            }

            const objResult = await objResponse.json();

            //==== for debugging:
            // const objResult = await objResponse.text();
            // console.log(objResult);

            if (objResult.errorcode === 0)
            {
                this.processValueJSON(objResult);

                this.#objFormInternals.setFormValue(this.renderValueJSON())

                this.updateUI();

                console.log(objResult.message);
                this.#dispatchEventChange(this.#objUploadFile, "renamed file");
            }
            else //error
            {
                DRComponentsLib.alert(`${this.sTransError} (${objResult.errorcode})`, objResult.message, this.sTransOk);
                console.error(objResult.message, `Error code: ${objResult.errorcode}`);                
            }
            
        } 
        catch (error) 
        {
            console.error(error.message);
        }
            
    }


    /**
     * handles preview
     * 
     * @param {DRContextMenu} objMenu 
     */
    #handlePreviewFile(objMenu)
    {
        objMenu.hide();


        //not image: open new tab
        if (!this.isImage())
        {
            window.open(this.#sUploadDirURL + "/" + this.#sFileName, "_blank");
            return; //exit
        }



        //==== SHOW DIALOG (rename prompt)
        const objDialog = new DRDialog();
        const objDivPreview = document.createElement("div");
        const objImgPreview = document.createElement("img");
        
        objDialog.populate();
        objDialog.setTitle(this.#sFileName);
        
        objDivPreview.appendChild(objImgPreview);
        objDialog.getBodyObject().appendChild(objDivPreview);

        //==== image
        objImgPreview.src = this.#sUploadDirURL + "/" + this.#sFileName;
        objImgPreview.style.cursor = "zoom-in";//default cursor
        objImgPreview.style.width = "100%"; //default zoomed out
        objImgPreview.addEventListener("mousedown", (objEvent)=>
        {
            if (objImgPreview.style.width == "100%")
            {
                objImgPreview.style.width = "";
                objImgPreview.style.cursor = "zoom-out";
            }
            else
            {
                objImgPreview.style.width = "100%";
                objImgPreview.style.cursor = "zoom-in";
            }
        }, { signal: objDialog.getAbortController().signal });    


        //==== "close" button at bottom
        const objBtnClose = document.createElement("button");
        objBtnClose.classList.add("default");
        objBtnClose.innerHTML = this.sTransClose;
        objBtnClose.addEventListener("mousedown", (objEvent)=>
        {
            objDialog.close();
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objBtnClose);



        objDialog.showModal();  
    }    

    /**
     * handles changing the image alt text <img alt="mountain">
     * 
     * @param {DRContextMenu} objMenu 
     */
    #handleChangeAlt(objMenu)
    {
        objMenu.hide();
        

        //==== SHOW DIALOG (alt text prompt)
        const objDialog = new DRDialog();
        const objEdtAlt = new DRInputText();
        objDialog.populate();
        objDialog.setTitle(this.sTransAlt);

        
        objDialog.getBodyObject().innerHTML = this.sTransAltDescription + "<br><br>";

        objEdtAlt.style.width = "100%";
        objEdtAlt.value = this.#sImageAlt;
        objEdtAlt.setWhitelist(this.#sWhitelistChars);
        // objEdtAlt.setBlacklist('"');
        objDialog.getBodyObject().appendChild(objEdtAlt);


        //cancel button
        const objBtnCancel = document.createElement("button");
        objBtnCancel.innerHTML = this.sTransCancel;
        objBtnCancel.addEventListener("click", (objEvent)=>
        {
            objDialog.close();
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objBtnCancel);  
        
        
        //set text button
        const objBtnRename = document.createElement("button");
        objBtnRename.innerHTML = this.sTransAltSetButton;
        objBtnRename.classList.add("default");
        objBtnRename.addEventListener("click", (objEvent)=>
        {
            this.#sImageAlt = objEdtAlt.value;   
            this.#objFormInternals.setFormValue(this.renderValueJSON());
            this.#objLabel.innerHTML = objEdtAlt.value;
            objDialog.close();
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objBtnRename);


        objDialog.showModal(); 
    } 

    /**
     * handles preview
     * 
     * @param {DRContextMenu} objMenu 
     */
    #handleCopyURLClipboard(objMenu)
    {
        objMenu.hide(); 
        navigator.clipboard.writeText(this.#sUploadDirURL + "/" + this.#sFileName);

        //==== SHOW DIALOG
        const objDialog = new DRDialog();
        objDialog.populate();
        objDialog.setTitle(this.sTransCopyURLClipboard);
        objDialog.setBody(this.sTransCopyURLClipboardDone + "<br>" + this.#sUploadDirURL + "/" + this.#sFileName);

        objDialog.showModal();       
        
    }

    /**
     * handles aborting upload
     * 
     * @param {DRContextMenu} objMenu 
     */
    #handleAbortUpload(objMenu)
    {
        objMenu.hide();

        this.#objXHR.abort();
        console.log("Upload aborted by user");

        this.reset();
    }    

    /**
     * creates a new <dr-input-upload> and adds it to the DOM next to this component.
     * 
     * @returns {DRInputUpload} the newly created component
     */
    createSiblingInputUpload()
    {
        // const objBrother = this.cloneNode();
        const objBrother = new DRInputUpload();

        //copy all attributes
        for (let iIndex = 0; iIndex < this.attributes.length; iIndex++)
        {
            if (this.attributes[iIndex].name !== "value") //dont copy value
                objBrother.setAttribute(this.attributes[iIndex].name, this.attributes[iIndex].value);
        }
        objBrother.id = DRComponentsLib.getNewHTMLId(objBrother.id) //changes the id, but not the name. ==> the id needs to be unique

        //add to DOM
        this.after(objBrother);

        return objBrother;
    }

    /**
     * sets a file (from DataTransfer object) into internal objUploadFile.
     * 
     * WARNING: 
     * This is a public function because we must be able to set files of other DRInputUpload-objects
     * to deligate downloads when multiple files are uploaded
     * 
     * @param {File} objFile 
     */
    setFileDataTransfer(objFile)
    {
        if (this.getOccupied() && this.#bFileExists)
        {
            const objDialog = new DRDialog();
            objDialog.populate();
            objDialog.setTitle(this.sTransError);
            objDialog.setBody(this.sTransDeleteBeforeUpload);

            objDialog.showModal(); 

            return;
        }

        const objDataTransfer = new DataTransfer();
        objDataTransfer.items.add(objFile);
        this.#objUploadFile.files = objDataTransfer.files; //throw files from drop to internal inputtypefile

        //file too big??
        if (this.#objUploadFile.files[0].size > this.#iMaxSizeFile)
        {
            this.reset();

            //==== SHOW DIALOG (with error)
            const objDialog = new DRDialog();
            objDialog.populate();
            objDialog.setTitle(this.sTransError + " " + objDataTransfer.files[0].name);
            objDialog.setBody(this.sTransErrorMaxSizeExceeded + Math.ceil(this.#iMaxSizeFile / 1024) + " kb");

            objDialog.showModal();  

            return false;            
        }

        //update stuff
        this.#objImageThumbnail.classList.add("uploading");
        this.#updateThumbnail(objFile);
        this.#objLabel.textContent = this.sTransQueue;
        this.#objProgressBar.startInfinite();
        if (this.#objProgressBar.style.display != "block")
            this.#objProgressBar.style.display = "block";        
        this.#startUpload();
        this.setDirty(true);
        this.#dispatchEventChange(this.#objUploadFile, "setFileDataTransfer(): uploaded file changed");
    }

    /**
     * returns a JSON object based on internal values
     * 
     */
    renderValueJSON()
    {        

        const objJSON = 
            { 
                filename: this.#sFileName,
                filenamelarge: this.#sFileNameLarge,
                filenamemedium: this.#sFileNameMedium,
                filenamethumbnail: this.#sFileNameThumbnail,
                imagealt: this.#sImageAlt,
                imagemaxwidth: this.#iImageMaxWidth,
                imagemaxheight: this.#iImageMaxHeight,
                imagelargewidth: this.#iImageLargeWidth,
                imagelargeheight: this.#iImageLargeHeight,
                imagemediumwidth: this.#iImageMediumWidth,
                imagemediumheight: this.#iImageMediumHeight,
                imagethumbnailwidth: this.#iImageThumbnailWidth,
                imagethumbnailheight: this.#iImageThumbnailHeight,
                fileexists: this.#bFileExists,
            };

        return JSON.stringify(objJSON);       
    }

    /**
     * sets internal values based on JSON object
     * 
     * @param {Object} objJSON 
     */
    processValueJSON(objJSON)
    {
        if (objJSON.filename)        
            this.#sFileName =               objJSON.filename;                    
        if (objJSON.filenamelarge)
            this.#sFileNameLarge =          objJSON.filenamelarge;
        if (objJSON.filenamemedium)        
            this.#sFileNameMedium =         objJSON.filenamemedium;
        if (objJSON.filenamethumbnail)                
            this.#sFileNameThumbnail =      objJSON.filenamethumbnail;
        if (objJSON.imagealt)                
            this.#sImageAlt =               objJSON.imagealt;
        if (objJSON.imagemaxwidth)                        
            this.#iImageMaxWidth =          objJSON.imagemaxwidth;
        if (objJSON.imagemaxheight)                        
            this.#iImageMaxHeight =         objJSON.imagemaxheight;
        if (objJSON.imagelargewidth)                        
            this.#iImageLargeWidth =        objJSON.imagelargewidth;
        if (objJSON.imagelargeheight)                        
            this.#iImageLargeHeight =       objJSON.imagelargeheight;
        if (objJSON.imagemediumwidth)                        
            this.#iImageMediumWidth =       objJSON.imagemediumwidth;
        if (objJSON.imagemediumheight)                        
            this.#iImageMediumHeight =      objJSON.imagemediumheight;
        if (objJSON.imagethumbnailwidth)                        
            this.#iImageThumbnailWidth =    objJSON.imagethumbnailwidth;
        if (objJSON.imagethumbnailheight)                        
            this.#iImageThumbnailHeight =   objJSON.imagethumbnailheight;
        if (objJSON.fileexists !== undefined)                        
            this.#bFileExists =             objJSON.fileexists;
    }

    /**
     * Processes multiple files uploading, 
     * either from a drop or selecting it with internal <input type="upload"> object
     * 
     * @param {FileList} objFileList 
     */
    #processMultipleFileUpload(objFileList)
    {
        let objNewInputUpload = null;        
        let objLastInputUpload = null;

        //check if there is anything to process
        if (objFileList.length == 0)
            return;

        //check types
        for (let iIndex = 0; iIndex < objFileList.length; iIndex++)
        {
            if (!this.#checkFileType(objFileList[iIndex]))
                return;//exit
        }

        //when more than 1 file is dragged but its not a multiple upload
        if ((objFileList.length > 1) && (!this.#bMultipleFilesUpload))
            console.error("DRInputUpload: " + objFileList.length + " files were selected, but this upload component allows only 1 file. I will process only the first one and ignore the rest");

        //deligate upload tasks to proper dr-input-upload object
        if (this.#bMultipleFilesUpload)
        {
            for (let iIndex = 0; iIndex < objFileList.length; iIndex++)
            {
                if (iIndex > 0)//we skip the first file (we deal with it later)
                {
                    //I want to add the new one AFTER the PREVIOUS one, NOT after the CURRENT one, because
                    //then I can create the empty new DRInputUpload at the end instead next to the first one
                    if (objNewInputUpload == null)
                        objNewInputUpload = this.createSiblingInputUpload(); 
                    else
                        objNewInputUpload = objNewInputUpload.createSiblingInputUpload(); 

                    //set file
                    objNewInputUpload.setFileDataTransfer(objFileList[iIndex]);
                }
            }

            //last: add new clean DRInputUpload
            objLastInputUpload = this.#getLastInputUploadInForm();
            if (objLastInputUpload !== null)
                objLastInputUpload.createSiblingInputUpload(); //after current
        }

        //only now we handle the first file ourselves (after the rest has been deligated away)
        //the reason for this is: setFileDataTransfer() overwrites the current list, so it is overwritten while we're still working on it (only applies to the file list of the internal <input type="file">-object)
        this.setFileDataTransfer(objFileList[0]);
    }

     /**
     * checks file type when uploading
     * 
     * shows error message 
     * 
     * @param {File} objFile 
     * @param {boolean} bShowError 
     * @returns {boolean} true=file accepted, false=not accepted
     */
    #checkFileType(objFile, bShowError = true)
    {
        const bAccept = this.#arrAcceptedFileTypes.includes(objFile.type);
        
        if ((!bAccept) && (bShowError))
        {
            console.error("DRInputUpload: file type "+ objFile.type + " not supported", this.#arrAcceptedFileTypes);
   
            DRComponentsLib.alert(this.sTransError,this.sTransFileTypeNotSupported + this.#arrAcceptedFileTypes.join("<br>"), this.sTransOk);
        }

        if (this.#arrAcceptedFileTypes.length === 0)
            console.error("DRInputUpload: no accepted file types are specified with 'accept'-attribute");

        return bAccept;
    }

    /**
     * resets this component back to its initial state (also visually), for example on create or after an error
     * 
     * WARNING: IT DOES NOT DELETE THE FILE!!!
     */
    reset()
    {
        this.#sFileName =               "";
        this.#sFileNameLarge =          "";
        this.#sFileNameMedium =         "";
        this.#sFileNameThumbnail =      "";
        this.#sImageAlt =               "";
        this.#iImageMaxWidth =          "";
        this.#iImageMaxHeight =         "";
        this.#iImageLargeWidth =        "";
        this.#iImageLargeHeight =       "";
        this.#iImageMediumWidth =       "";
        this.#iImageMediumHeight =      "";
        this.#iImageThumbnailWidth =    "";
        this.#iImageThumbnailHeight =   "";
        this.#bFileExists =             false;

        this.#objImageThumbnail.classList.remove("uploading");
        this.#objFormInternals.setFormValue(this.renderValueJSON());

        this.updateUI();        
    }

    /**
     * updates the thumbnail after dropping
     * 
     * @param {File} objFile 
     */
    #updateThumbnail(objFile)
    {
        this.#objLabel.textContent = objFile.name;

        //show thumbnail for image files
        if (objFile.type.startsWith("image/"))
        {
            const objReader = new FileReader();
            objReader.readAsDataURL(objFile);
            objReader.onload = () =>
            {
                this.#objImageThumbnail.style.backgroundImage = "url(" + objReader.result + ")";
                // this.innerHTML = "";
                this.#objMenuDots.classList.remove("disabled");
                this.#bFileExists = true;
            }
        }
        else //remove thumbnail image
        {
            this.#objImageThumbnail.style.backgroundImage = "url('data:image/svg+xml, " + this.#sSVGIconUploadOtherFile + "')";
            //this.#objImageThumbnail.style.backgroundImage = null;
        }

    }


    /**
     * starts upload process (can be called by drop or click)
     * 
     */
    #startUpload()
    {
        let iPercent = 0;
        
        if (!this.hasAttribute("name"))
        {
            console.error("DRInputUpload: can't upload because element has no 'name'-attribute");
            return;
        }

        this.setIsUploading(true);

        this.#objXHR = new XMLHttpRequest();
        this.#objXHR.open('POST', this.#sUploadNewURL);
        
        //==== PROGRESS
        this.#objXHR.upload.addEventListener("progress", (objEvent) => 
        {            
            if (objEvent.lengthComputable) //sometimes doesn't work
            {
                this.#objProgressBar.stopInfinite();//just to make sure the "queue" infinite progress bar is stopping
                this.#objProgressBar.max = objEvent.total;
                this.#objProgressBar.value = objEvent.loaded;

                iPercent = this.#objProgressBar.getPercent();
                console.log(this.id, "uploading", iPercent, "%");

                if (iPercent == 100)
                {
                    this.#objLabel.textContent = this.sTransProcessing;
                    this.#objProgressBar.startInfinite();
                }
                else
                {
                    this.#objLabel.textContent = this.sTransUploading;
                    if (this.#objProgressBar.style.display != "block")
                        this.#objProgressBar.style.display = "block";
                }

            }
            else
            {
                this.#objProgressBar.startInfinite();
            }
        }, { signal: this.#objAbortController.signal });  
        
        //==== ERROR
        this.#objXHR.upload.addEventListener("error", (objEvent) => 
        {
            this.setIsUploading(false);
            this.reset();

            //==== SHOW DIALOG (with error)
            const objDialog = new DRDialog();
            objDialog.populate();
            objDialog.setTitle(this.sTransError);
            objDialog.setBody(this.sTransErrorUnknownOccurred);

            objDialog.showModal();  

        }, { signal: this.#objAbortController.signal });

        //==== TIMEOUT
        this.#objXHR.upload.addEventListener("timeout", (objEvent) => 
        {
            this.setIsUploading(false);
            this.reset();

            //==== SHOW DIALOG (with error)
            const objDialog = new DRDialog();
            objDialog.populate();
            objDialog.setTitle(this.sTransError);
            objDialog.setBody(this.sTransErrorTimeOut);

            objDialog.showModal();  
        }, { signal: this.#objAbortController.signal });  

      

        //==== LOAD START (when uploading starts)
        // this.#objXHR.upload.addEventListener("loadstart", (objEvent) => 
        // {
        //     console.log("loadstart this.#objXHR.upload", objEvent);
        // }, { signal: this.#objAbortController.signal });          
        
        //==== LOAD (after upload is finished, but page is loading a.k.a. image still processing)
        // this.#objXHR.upload.addEventListener("load", (objEvent) => 
        // {
        //     console.log("load this.#objXHR.upload", objEvent);
        // }, { signal: this.#objAbortController.signal });    

        //==== LOAD END (when processing is finished)
        // this.#objXHR.upload.addEventListener("loadend", (objEvent) => 
        // {
        //     console.log("loadend this.#objXHR.upload", objEvent);
        // }, { signal: this.#objAbortController.signal });         

        //==== FINISHED
        this.#objXHR.onreadystatechange = (objEvent) =>
        {
            // console.log("onreadystatechange this.#objXHR.upload", objEvent);
            if(this.#objXHR.readyState == 4 && this.#objXHR.status == 200) 
            {
                // console.log('reponsssssseeeeE:', this.#objXHR.responseText);

                const objResponse = JSON.parse(this.#objXHR.responseText);
                this.#objProgressBar.stopInfinite();
                this.#objProgressBar.style.display = "none";
                this.#objImageThumbnail.classList.remove("uploading");
                this.setIsUploading(false);

                //error occurred?
                if (objResponse.errorcode > 0)
                {
                    //==== SHOW DIALOG (with error)
                    const objDialog = new DRDialog();
                    objDialog.populate();
                    objDialog.setTitle(this.sTransError);
                    objDialog.setBody(objResponse.message);

                    objDialog.showModal();  
                    
                    this.reset();
                }
                else //no error
                {
                    this.processValueJSON(objResponse);
                    this.#objFormInternals.setFormValue(JSON.stringify(objResponse));

                    this.updateUI();
                }
            }
        }      
                

        // this.#objXHR.setRequestHeader("Content-Type", "multipart/form-data"); ==> gives error
        this.#objXHR.send(new FormData(this.#objFormInternal));
    }


    setDirty(bValue)
    {
        this.#bDirty = bValue;
    }

    getDirty()
    {
        return this.#bDirty;
    }

    setValue(sValue)
    {
        this.processValueJSON(JSON.parse(sValue));
    }

    getValue()
    {
        return this.renderValueJSON(JSON.stringify(this.renderValueJSON()));
    }

    setAccept(sValue)
    {
        if (sValue == "")
            this.#arrAcceptedFileTypes = [];
        else
            this.#arrAcceptedFileTypes = sValue.split(",");
    }

    getAccept()
    {
        return this.#arrAcceptedFileTypes.join(",");
    }

    setMultiple()
    {
        this.#bMultipleFilesUpload;
    }

    getMultiple()
    {
        return this.#bMultipleFilesUpload;
    }

    setWhitelist(sValue)
    {
        this.#sWhitelistChars = sValue;
    }

    getWhitelist()
    {
        return this.#sWhitelistChars;
    }

    /**
     * returns if there is a file inside this component
     */
    getOccupied()
    {
        return (this.#sFileName !== "");
    }

    /**
     * sets a file inside this component to empty
     */
    setOccupiedFalse()
    {
        this.#sFileName = "";
    }

    /**
     * sets name of the internal file.
     * When resized images, this is the max-size image
     * 
     * @param {string} sValue 
     */
    setFileName(sValue)
    {
        this.#sFileName = sValue;
    }

    /**
     * gets name of the internal file.
     * When resized images, this is the max-size image
     * 
     * @returns {string} sValue 
     */    
    getFileName()
    {
        return this.#sFileName;
    }    

    /**
     * sets if this object is currently uploading a file
     * 
     * @param {boolean} bValue 
     */
    setIsUploading(bValue)
    {
        console.log("#setIsUploading#setIsUploading()", bValue)
        this.#bIsUploading = bValue;
    }

    /**
     * gets if this object is currently uploading a file
     * 
     * @returns {boolean}  
     */    
    getIsUploading()
    {
        return this.#bIsUploading;
    }    

    /**
     * does this form need a new DRInputUpload?
     * 
     * 1. it looks at how many DRInputUpload's there are with the same name (in the same form)
     * 2. if it finds a DRInputUpload that is not occupied, there is space, so DOESN'T need a new DRInputUpload, so returns false
     * 
     * @returns {boolean} true when all DRInputUpload objects are occupied, false otherwise
     */
    #needsNewInputUpload()
    {
        const arrSiblings = [...document.getElementsByName(this.getAttribute("name"))]; //we can't do a querySelectorAll() because of the brackets[] in the name

        //when multiple file uploads are not allowed
        if (!this.#bMultipleFilesUpload)
            return false;

        for (let iIndex = 0; iIndex < arrSiblings.length; iIndex++)
        {
            if (this.#objFormParent.contains(arrSiblings[iIndex])) //check if it's part of the same form (just to be sure)
            {
                console.log("arrSiblings[iIndex].getOccupied()arrSiblings[iIndex].getOccupied()", arrSiblings[iIndex].getOccupied());
                console.log("arrSiblings[iIndex].getIsUploading()arrSiblings[iIndex].getIsUploading()", arrSiblings[iIndex].getIsUploading());
                if (arrSiblings[iIndex] instanceof DRInputUpload)
                    if ((!arrSiblings[iIndex].getOccupied()) && (!arrSiblings[iIndex].getIsUploading()))
                        return false;
            }
        }

        return true;
    }

    /**
     * returns the last DRInputUpload with the same name in the same form
     * 
     * @returns {DRInputUpload} when there are 
     */
    #getLastInputUploadInForm()
    {
        const arrSiblings = [...document.getElementsByName(this.getAttribute("name"))]; //we can't do a querySelectorAll() because of the brackets[] in the name
        let objLastInputUpload = null;

        for (let iIndex = 0; iIndex < arrSiblings.length; iIndex++)
        {
            if (this.#objFormParent.contains(arrSiblings[iIndex])) //check if it's part of the same form (just to be sure)
                if (arrSiblings[iIndex] instanceof DRInputUpload)
                    objLastInputUpload = arrSiblings[iIndex];
        }

        return objLastInputUpload;
    }

    updateUI()
    {

        if (this.#sFileName !== "") //FILE EXISTS: needs to show file
        {
            this.#objMenuDots.classList.remove("disabled");     

            //=== when file NOT found
            if (!this.#bFileExists)
            {
                // console.log("ffffffilenotexissts");
                this.#objLabel.innerHTML = this.sTransFileNotFound + " [" + this.#sFileName + "]";
                this.#objImageThumbnail.style.backgroundImage = "url('data:image/svg+xml, " + this.#sSVGIconFileNotFound + "')";
                return;
            }

            //=== when file found

            this.#objLabel.innerHTML = this.#sFileName;
            if (this.#sImageAlt)
                this.#objLabel.innerHTML = this.#sImageAlt;    
            
            let sFile = this.#sUploadDirURL + "/" + encodeURI(this.#sFileName);

            //image
            if (this.isImage())
                this.#objImageThumbnail.style.backgroundImage = "url('" + sFile + "')";     
            else
                this.#objImageThumbnail.style.backgroundImage = "url('data:image/svg+xml, " + this.#sSVGIconUploadOtherFile + "')";
            // this.#objImageThumbnail.style.backgroundImage = null;
        }
        else //EMPTY
        {
        
            this.#objMenuDots.classList.add("disabled");    

            this.#objLabel.textContent = this.sTransDropFileHere;

            if (typeof this.#objProgressBar.stopInfinite === "function") //because of the weird limbo state that objects are in when being created, i check if the function exists, otherwise we get an error
                this.#objProgressBar.stopInfinite();//just to make sure the "queue" infinite progress bar is stopping
            this.#objProgressBar.style.display = "none";            

            //image
            this.#objImageThumbnail.style.backgroundImage = "url('data:image/svg+xml, " + this.#sSVGIconUploadDefault + "')";
            // this.#objImageThumbnail.style.backgroundImage = null;
        }
        
    }



    /** 
     * get disabled
    */
    get disabled()
    {
        return DRComponentsLib.boolToStr(this.#bDisabled);
    }    

    /** 
     * set disabled
    */
    set disabled(bValue)
    {
        this.#bDisabled = DRComponentsLib.strToBool(bValue);
    }


    /** 
     * set internal value as string with decimal separator dot(.)
    */
    set value(sValue)
    {
        this.setValue(sValue);
    }

    /** 
     * get internal value as string with decimal separator dot(.)
    */
    get value()
    {
        return this.getValue()
    }

    /**
     * returns if user changed the contents of this box
     */
    get dirty()
    {
        return this.getDirty();
    }


    get whitelist()
    {
        return this.getWhitelist();
    }

    set whitelist(sCharacters)
    {
        this.setWhitelist(sCharacters);
    }    

    get multiple()
    {
        return this.getMultiple();
    }

    set multiple(bMultipleFilesUploads)
    {
        this.setMultiple(bMultipleFilesUploads);
    }    

    /**
     * returns if internal file is an image 
     * WARNING: it looks purely at the file extension, not the contents of the file
     */
    isImage()
    {
        const sMimeType = this.getMimeType();
        return (sMimeType.startsWith('image/'));
    }

    /**
     * returns mime type of internal file
     * WARNING: it looks purely at the file extension, not the contents of the file
     */    
    getMimeType()
    {
        const arrSplit = this.#sFileName.split(".");
        if (arrSplit.length > 1)
            return DRComponentsLib.getMimeTypeFromExtension(arrSplit[arrSplit.length-1]);

        return "";
    }

    /**
     * removes all eventlisteners used in this object
     */
    removeEventListeners()
    {
        this.#objAbortController.abort();
    }



    static get observedAttributes() 
    {
        return ["disabled", "value","accept"];
    }

    attributeChangedCallback(sAttrName, sOldVal, sNewVal) 
    {
        // console.log("attribute changed in input number", sAttrName, sNewVal);
        switch(sAttrName)
        {
            case "disabled":
                this.disabled = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "value":
                this.value = sNewVal;
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
            case "accept":
                this.setAccept(sNewVal);
                if (this.#bConnectedCallbackHappened)
                    this.updateUI();
                break;
        }
    }   

    /** 
     * When added to DOM
     */
    connectedCallback()
    {


        if (this.#bConnectedCallbackHappened == false) //first time running
        {
            //read attributes
            this.#readAttributes();
            this.#objFormInternals.setFormValue(this.renderValueJSON());
            // console.log("setformvalue connectedCallback",this.getValueAsString("."));

            //render
            this.populate();

            //last: add new clean DRInputUpload
            if (this.#needsNewInputUpload())
            {
                const objLastInputUpload = this.#getLastInputUploadInForm();
                if (objLastInputUpload !== null)
                    objLastInputUpload.createSiblingInputUpload(); //after current
            }                   
        }

        //reattach abortcontroller when disconnected
        if (this.#objAbortController.signal.aborted)
            this.#objAbortController = new AbortController();

        //event
        this.addEventListeners();


        this.#bConnectedCallbackHappened = true;           
   }

    /** 
     * remove from DOM 
     **/
    disconnectedCallback()
    {
        this.removeEventListeners();
    }

    /**
     * broadcasts "change" event
     * 
     * @param {HTMLElement} objSource 
     * @param {string} sDescription 
     */
    #dispatchEventChange(objSource, sDescription)
    {
        // console.log("dispatch event", sDescription);
        this.dispatchEvent(new CustomEvent("change",
        {
            bubbles: true,
            detail:
            {
                source: objSource,
                description: sDescription
            }
        }));
    }


}


/**
 * make component available in HTML
 */
customElements.define("dr-input-upload", DRInputUpload);