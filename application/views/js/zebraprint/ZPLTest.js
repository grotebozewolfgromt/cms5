/*<?php 
/**
 * ZPL.js
 *
 * Class to talk ZPL (Zebra Printing Language) to print labels with Zebra network printers
 * based on the Chrome plugin: Zebra Printing: https://chromewebstore.google.com/detail/zebra-printing/ndikjdigobmbieacjcgomahigeiobhbo?hl=nl&pli=1
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
 * 31 jan 2025 ZPL.js created
 */
?>*/

class ZPLTest extends ZPL
{
    addTest()
    {
        this.sZPLString += "^XA^CF0,30^FO300,30^FDHU  Label^FS^CF0,25^FO20,100^FDHU ID:         112345678000001107^FS^BY2.2,3,70^FO20,130^BCN,,N^FD112345678000001107^FS^FO20,230^FD60-Volt Cordless Electric Hedge Trimmer^FS^FO20,260^FD13^FS^FO650,200^BQN,2,5^FDQA,^FS^XZ";
    }
}