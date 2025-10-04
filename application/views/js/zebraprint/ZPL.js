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

class ZPL
{
    sZPLString = ""; //the generated ZPL

    render()
    {
        return this.sZPLString;
    }
}