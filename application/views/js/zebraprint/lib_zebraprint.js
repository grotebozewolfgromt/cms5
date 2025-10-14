/*<?php 
/**
 * lib_zebraprint.js
 *
 * library for printing labels with Zebra network printers
 * based on the Chrome plugin: Zebra Printing: https://chromewebstore.google.com/detail/zebra-printing/ndikjdigobmbieacjcgomahigeiobhbo?hl=nl&pli=1
 * 
 * all methods start with "zebra", "setZebra" or "getZebra"
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
 * 31 jan 2025 lib_zebraprint.js created
 */
?>*/


/**
 * global JS vars
 */
var arrZebraPinters = [];
arrZebraPinters["<?php echo APP_ADMIN_PRINTING_ZPL_PRINTER1_NAME; ?>"] = "<?php echo APP_ADMIN_PRINTING_ZPL_PRINTER1_IPADDRESS; ?>";
var bZebraPrintBrowserExtensionInstalled = false;

/**
 * event fired on each message
 * meant to be run at page load to determine the state if Zebra printing browser extension is installed
 */
window.addEventListener('message', function (event) 
{
    if (!event.data.ZebraPrintingVersion) 
        return;
    
    bZebraPrintBrowserExtensionInstalled = true;
});


/**
 * returns whether the browser extension is installed
 * @returns boolean
 */
function getZebraPrintBrowserExtensionInstalled()
{
    return bZebraPrintBrowserExtensionInstalled;
}

/**
 * print a test label to see if printer works
 * 
 * @param {string} sIP 
 */
function zebraTestPrint(sIP)
{
    if (bZebraPrintBrowserExtensionInstalled)
        console.log("Zebra Printing Chrome browser extension is installed");
    else
        console.log("Zebra Printing Chrome browser extension is NOT installed, download via: https://chromewebstore.google.com/detail/zebra-printing/ndikjdigobmbieacjcgomahigeiobhbo?hl=nl&pli=1");

    window.postMessage({
        type: "zebra_print_label",
        // zpl: "^XA^PW400^LL200^FO20,20^A0N,30,30^FDThis is a TEST^FS^XZ",
        zpl: "^XA^CF0,30^FO300,30^FDHU  Label^FS^CF0,25^FO20,100^FDHU ID:         112345678000001107^FS^BY2.2,3,70^FO20,130^BCN,,N^FD112345678000001107^FS^FO20,230^FD60-Volt Cordless Electric Hedge Trimmer^FS^FO20,260^FD13^FS^FO650,200^BQN,2,5^FDQA,^FS^XZ",
        url: "http://" + sIP + "/pstprnt"
    }, "*");
}


/**
 * print a test label to see if printer works
 * 
 * @param {string} sIP 
 */
function zebraPrint(sIP, sZPL)
{
    if (bZebraPrintBrowserExtensionInstalled)
        console.log("Zebra Printing Chrome browser extension is installed");
    else
        console.log("Zebra Printing Chrome browser extension is NOT installed, download via: https://chromewebstore.google.com/detail/zebra-printing/ndikjdigobmbieacjcgomahigeiobhbo?hl=nl&pli=1");

    window.postMessage({
        type: "zebra_print_label",
        // zpl: "^XA^PW400^LL200^FO20,20^A0N,30,30^FDThis is a TEST^FS^XZ",
        zpl: sZPL,
        url: "http://" + sIP + "/pstprnt"
    }, "*");
}

function zebraTestPrint2(sIP)
{
    debugger
    objZPL = new ZPLTest();
    objZPL.addTest();
    zebraPrint(sIP, objZPL.render());
}


