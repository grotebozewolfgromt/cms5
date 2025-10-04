<?php
/**
 * test
 */

use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\webcomponents\DRInputDateTime;
use dr\classes\dom\tag\form\InputTel;
use dr\classes\models\TCMSInvitationCodes;

use dr\classes\patterns\TWeightedScores;
use dr\classes\models\TIPGeoLocation;
use dr\modules\Mod_PageBuilder\models\TPageBuilderWebpages;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\classes\TSpamDetector;
use dr\classes\TWeightedAverageScore;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\classes\models\TSysWebpageBuilderPages;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;
use dr\modules\Mod_PageBuilder\models\TPageBuilderBlocks;
use dr\modules\Mod_PageBuilder\models\TPageBuilderStructures;
use dr\modules\Mod_POSWebshop\models\TTransactions;
use dr\modules\Mod_POSWebshop\models\TTransactionsTypes;
use dr\classes\dom\tag\HTMLTag;
use dr\classes\mail\TMailSend;
use dr\classes\patterns\TConfigFile;
use dr\classes\types\TDecimal;
use dr\modules\Mod_Dev\models\TTestTable;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductSKUs;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TProductsSKUs;
use dr\modules\Mod_POSWebshop\models\TSKU;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;

// vardump(isValidEmail('dennis@dennisrenirie.nl', true, true));


include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_testcases.php');
// include_once(APP_PATH_VENDOR.DIRECTORY_SEPARATOR.'Mike42'.DIRECTORY_SEPARATOR.'autoload.php');

// $objDecimal = new TDecimal('123.928', 10);
// $objDecimal2 = new TDecimal('669.812', 4);
// $objDecimal = new TDecimal('12.512', 10);
// $objDecimal2 = new TDecimal('691.512', 4);

// $objDecimal->add($objDecimal2);
// vardump($objDecimal);
// $iTest = 10 ** 3;
// vardump($iTest);

// $objTemp = new TVATClasses();
// vardump($objTemp->install());
// $objTemp = new TVATClassesCountries();
// vardump($objTemp->install());

// $objTemp = new TProductsLanguages();
// vardump($objTemp->install());

/*
$objProd = new TProducts();
vardump($objProd->install());
$objProd->newRecord();
$objProd->setManufacturerCountryID(1);
vardump($objProd->saveToDB());
*/
/*
$objProdLang = new TProductsLanguages();
// vardump($objProdLang->install());
$objProdLang->newRecord();
$objProdLang->setTranslationLanguageID(1);
$objProdLang->setProductID(1);
vardump($objProdLang->saveToDB());
*/
/*
$objSKU = new TProductsSKUs();
vardump($objSKU->install());
$objSKU->newRecord();
$objSKU->setProductID(1);
$objSKU->setVATClassID(1);
$objSKU->saveToDB();
*/
// $obj


// require __DIR__ . '/../vendor/autoload.php';
use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\NetworkPrintConnector;

// $connector = new FilePrintConnector("php://stdout");
$connector = new NetworkPrintConnector("192.168.1.73", 9100);
$printer = new Printer($connector);

/* Initialize */
$printer -> initialize();

/* Text */
$printer -> text("Hello world");
$printer -> text("Hello world\n");
// $printer -> barcode("123456", Printer::BARCODE_CODE39);

// $img = EscposImage::load(APP_PATH_MODULES."\Mod_Dev\controllers\bestand.jpg");
// $printer -> graphics($img);
$printer ->pdf417Code("hallo meneer", 2, 3);

$printer -> cut();

$printer -> text("ABC");
$printer -> feed(7);
$printer -> text("DEF");
$printer -> feedReverse(3);
$printer -> text("GHI");
$printer -> feed();
$printer -> cut();



?>
<!-- <dr-input-combobox>
    <li value="1">item 1</li>
    <li value="2">item 2</li>
    <li value="3">item 3</li>
</dr-input-combobox> -->
<br>
<?php vardump($_POST); ?>
<!-- <form method="post"> -->
<!-- <dr-input-combobox placeholder="select an item" name="customselect" type="selectone" value="2,3,5">
    <div value="1">item 1</div>
    <div value="2">item 2</div>
    <div value="3">item 3</div>
    <div value="5">lekkere <b>dikke</b> titten</div>
    <div>item no value</div>
    <div value="">value empty</div>    
    <div>nested value
        <div value="4">item 4</div>
        <div value="5">item 5</div>
        <div value="6">item 6 nested          
            <div value="dog">hond</div>
            <div value="pussy">poes</div>
            <div value="giraffe">giraffe</div>
            <div>poezen
                <div value="huiskat">huiskat</div>
                <div value="leeuw">leeuw</div>
                <div value="tijger">tijger</div>
            </div>    
        </div>
    </div>
</dr-input-combobox> -->
<dr-input-combobox placeholder="select an item" name="customselect" type="selectmultiple">
    <div value="1">item 1</div>
    <div value="2">item 2</div>
    <div value="3">item 3</div>
    <div value="4">lekkere <b>dikke</b> button</div>
    <div>item no value</div>
    <!-- <div value="">value empty</div>     -->
    <div>nested value
        <div value="5">item 5</div>
        <div value="6">item 7</div>
        <div>nested item       
            <div value="dog">hond</div>
            <div value="pussy">poes</div>
            <div value="giraffe">giraffe</div>
            <div>poezen
                <div value="huiskat">huiskat</div>
                <div value="leeuw">leeuw</div>
                <div value="tijger">tijger</div>
            </div>    
        </div>
    </div>
</dr-input-combobox>
<dr-input-checkbox name="mychecker"></dr-input-checkbox>
<input type="checkbox">


<select name="standardselect">
    <option value="1">hallo1</option>
    <option value="2">hallo2</option>
    <option value="3">hallo3</option>
    <option value="4">hallo4s</option>
    <option value="">value empty</option>
    <optgroup>
        <option value="5">hallo4</option>
        <option value="6">hallo6</option>   
        <option value="7">hallo7</option>
    </optgroup>
</select>
<input type="text" value="blaat">
<!-- <input type="submit"> -->
<!-- </form> -->

<!-- <dr-input-number id="lutjebroek" precision="4" padzero="2" min="100" max="2000000"></dr-input-number>

<script>
    // console.log(Number.MAX_SAFE_INTEGER);
    window.addEventListener("load", (event)  => 
    {

        let objNum = document.getElementById("lutjebroek");
        // objNum.setD
        

        // objNum.setInternalValue(200,2);
        objNum.setValueAsString('1234567890.123456');
        objNum.updateUI();
        console.log(objNum.getValueAsUserOutput());
        console.log(objNum.getValueAsString());
        console.log(objNum.getMinValueAsString());
        console.log(objNum.getMaxValueAsString());
    });
</script> -->


