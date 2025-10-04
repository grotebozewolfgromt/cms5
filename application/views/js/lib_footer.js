<?php 
/**
 * lib_footer.js
 *
 * This Javascript file contains ONLY the standard javascript for the entire framework 
 * that needs to be handled AFTER the page is loaded.
 * 
 * THIS IS NOT YOUR DEFAULT DUMP FOR JAVASCRIPT!
 * BY DEFAULT JAVASCRIPT CODE GOES HERE: lib_footer.js
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
 * 6 apr 2024 lib_footer.js created
 */
?>

<?php 
/**
 * Honeypot handling for forms.
 * The honeypot injects an extra field that is hidden with css
 * 
 * This auto executed 'function' disables all fields with 'letitbee' css class 
 * (the term "bee" refers to honeypot. without giving away to scammers it is a honeypot)
 */   
?>
//===== BEE FIELD =====/

    const objNodes = document.getElementsByClassName("letitbee");
    let iNodeCount = objNodes.length;
    for (i = 0; i < iNodeCount; i++) 
    {
        // objNodes[i].parentElement.style.visibility = 'hidden';
        objNodes[i].parentElement.style.height = 0; //otherwise it still takes up space
        objNodes[i].parentElement.style.overflow = 'hidden';
    } 

//END ===== BEE FIELD =====/    
