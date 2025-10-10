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
use dr\classes\models\TSysContactsSalutations;
use dr\classes\models\TSysContactsLastNamePrefixes;
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
use dr\classes\dom\tag\webcomponents\DRInputUpload;
use dr\classes\mail\TMailSend;
use dr\classes\patterns\TConfigFile;
use dr\classes\types\TDecimal;
use dr\modules\Mod_Dev\models\TTestTable;
use dr\modules\Mod_POSWebshop\models\TProductCategories;
use dr\modules\Mod_POSWebshop\models\TProducts;
use dr\modules\Mod_POSWebshop\models\TProductSKUs;
use dr\modules\Mod_POSWebshop\models\TProductsLanguages;
use dr\modules\Mod_POSWebshop\models\TProductsSKUs;
use dr\modules\Mod_POSWebshop\models\TSKU;
use dr\modules\Mod_POSWebshop\models\TVATClasses;
use dr\modules\Mod_POSWebshop\models\TVATClassesCountries;
use dr\modules\Mod_Sys_Settings\models\TSysCMSMenu;

// includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'dr-input-combobox.js');
// includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'dr-input-number.js');

includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'dr-popover.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-info'.DIRECTORY_SEPARATOR.'dr-icon-info.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-progress-bar'.DIRECTORY_SEPARATOR.'dr-progress-bar.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-icon-spinner'.DIRECTORY_SEPARATOR.'dr-icon-spinner.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-context-menu'.DIRECTORY_SEPARATOR.'dr-context-menu.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-date'.DIRECTORY_SEPARATOR.'dr-input-date.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-time'.DIRECTORY_SEPARATOR.'dr-input-time.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-datetime'.DIRECTORY_SEPARATOR.'dr-input-datetime.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox'.DIRECTORY_SEPARATOR.'dr-input-checkbox.js');
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox-group'.DIRECTORY_SEPARATOR.'dr-input-checkbox-group.js');
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-number'.DIRECTORY_SEPARATOR.'dr-input-number.js');
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'dr-input-combobox.js');
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-text'.DIRECTORY_SEPARATOR.'dr-input-text.js');
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-db-filters'.DIRECTORY_SEPARATOR.'dr-db-filters.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-paginator'.DIRECTORY_SEPARATOR.'dr-paginator.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-tabsheets'.DIRECTORY_SEPARATOR.'dr-tabsheets.js'); 
includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-dialog'.DIRECTORY_SEPARATOR.'dr-dialog.js'); 
// includeJSDOMEnd( APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-upload'.DIRECTORY_SEPARATOR.'dr-input-upload.js'); 

// die(collateString('Italië met Têlegram graftak '.'Ù'.'ä'.'ß'));
// die(generateNiceID(4,2));

?>
<?php
// $objTemp = new DRInputUpload();
// $objTemp->setAttribute('name', 'snotgorgel');
// $objTemp->setUploadDir(APP_PATH_UPLOADS.DIRECTORY_SEPARATOR.'subbie');
// $objTemp->setAccept(array(MIME_TYPE_PNG, MIME_TYPE_JPEG, MIME_TYPE_WEBP));
// $sTest = $objTemp->renderHTMLNode();
// echo $sTest;


$objTemp = new TSysContactsLastNamePrefixes();
vardump($objTemp->install());
die();

?>
<dr-dialog title="titeltje">
    dit is een bericht
    <button class="default" onclick="alert('left')">left</button>
    <button onclick="alert('right')">right</button>
</dr-dialog>
<button onclick="document.querySelector('dr-dialog').showModal();">show dialog html</button>
<button onclick="showdloag();">show dialog js</button>

<script>

    function showdloag()
    {
        let objDialog = new DRDialog();
        objDialog.setTitle("Rnter title here");
        objDialog.setBody("Text in dialog"); 
        document.querySelector("body").appendChild(objDialog);

        objButton = document.createElement("button");
        objButton.textContent = "knoppie";
        objButton.addEventListener("click", (objEvent)=>
        {
            objDialog.close("returrrrrnvalue");
            console.log("objDialog.returnValue====1", objDialog.returnValue);
            objDialog.returnValue = "another returrrrrnvalue";
            console.log("objDialog.returnValue====2", objDialog.returnValue);
        // }, { once: true });
        }, { signal: objDialog.getAbortController().signal });  
        objDialog.addButton(objButton);

        objDialog.showModal();  
    };
</script>

<style>
    <?php include_once APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-tabsheets'.DIRECTORY_SEPARATOR.'style.css'; ?>
</style>

<?php

// vardump(isValidEmail('dennis@dennisrenirie.nl', true, true));

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_testcases.php');

vardump(generateUniqueFileName(APP_PATH_UPLOADS.'\poswebshop\productcategories', 'image(1).jpg'));

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


// $objProd = new TProducts();
// // vardump($objProd->install());
// $objProd->newRecord();
// $objProd->setManufacturerCountryID(1);
// $objProd->setVATClassesID(1);
// vardump($objProd->saveToDB());


// $objProdLang = new TProductsLanguages();
// vardump($objProdLang->install());
// $objProdLang->newRecord();
// $objProdLang->setTranslationLanguageID(1);
// $objProdLang->setProductID(2);
// vardump($objProdLang->saveToDB());


// $objSKU = new TProductsSKUs();
// vardump($objSKU->install());
// $objSKU->newRecord();
// $objSKU->setProductID(2);
// $objSKU->saveToDB();


// $obj

// $objMenu = new TSysCMSMenu();
// vardump($objMenu->install(), 'blakkkiikl');
// vardump($objMenu->createMenuItemsForModulesDB(), 'blakkkiik1231231234l');


// $objMenu1 = new TSysCMSMenu();
// $objMenu1->setID(3);
// $objMenu1->setNameDefault('test');
// $objMenu1->newRecord();
// $objMenu1->setID(6);
// $objMenu1->setNameDefault('bl;aat');

// $objMenu2 = new TSysCMSMenu();
// $objMenu2->addCopy($objMenu1, false);

// $objMenu2->removeRecordAtRecordpointer(1);

// var_dump($objMenu2);

// $objCat = new TProductCategories();
// echo "reset tree hierarchy product categories";
// vardump($objCat->resetTreeHierarchy(true), "reset tree hierarchy product categories");
// vardump($objCat->install());
// $objCat->newRecord();
// $objCat->setName('hallo meneer');
// $objCat->setParentID(0);
// $objCat->setMetaDepthLevel(0);
// vardump($objCat->saveToDB());
// $objCat->newRecord();
// $objCat->setName('bla vla');
// $objCat->setParentID(1);
// $objCat->setMetaDepthLevel(1);
// vardump($objCat->saveToDB());
// $objCat->newRecord();
// $objCat->setName('haink');
// $objCat->setParentID(1);
// $objCat->setMetaDepthLevel(1);
// vardump($objCat->saveToDB());
// $objCat->orderBy($objCat::FIELD_POSITION);
// $objCat->loadFromDB();
// $objCat->dumpTree();
// $objCat->uninstallDB();


// $objCat->positionChangeDB(5,2);
// $objCat->positionChangeDB(2,7);
// $objCat->positionChangeDB(3,-1);


?>
<!-- <dr-input-combobox>
    <li value="1">item 1</li>
    <li value="2">item 2</li>
    <li value="3">item 3</li>
</dr-input-combobox> -->



<br>
<?php vardump($_POST); ?>
<form method="post">
<dr-input-combobox placeholder="select an item" name="customselect" type="selectone" value="2,3,5">
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
</dr-input-combobox>
<script>
    // debugger
    let sValu = "henmke2";
    let sValu2 = "";
    console.log(sValu.split("e"));
</script>
<!-- <dr-input-combobox placeholder="select an item" name="customselect" type="selectone" value="4">
    <div value="1">item 1</div>
    <div value="2">item 2</div>
    <div value="3">item 3</div>
    <div value="4">lekkere <b>dikke</b> button</div>
    <div>item no value</div>
    <div>nested value
        <div value="5">item 5</div>
        <div value="6">item 6</div>
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
</dr-input-combobox> -->
<dr-input-checkbox-group name="mycheckergroup" maxselected="2">
    <dr-input-checkbox name="mychecker1"></dr-input-checkbox>
    <dr-input-checkbox name="mychecker2"></dr-input-checkbox>
    <dr-input-checkbox name="mychecker3"></dr-input-checkbox>
</dr-input-checkbox-group>
<input type="checkbox">

<dr-input-datetime></dr-input-datetime>
<dr-input-date></dr-input-date>

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
<input type="submit">


<dr-input-number id="lutjebroek" precision="4" padzero="2" min="100" max="2000000"></dr-input-number>

<dr-input-time></dr-input-time>

<dr-input-text name="textboxie1" id="textboxie" maxlength="25" minlength="2">

</dr-input-text>

<dr-input-text name="textboxie" id="textboxie" maxlength="25" minlength="2" charcounter>
    <div>beEr1</div>
    <div>beer2</div>
    <div>coalA</div>
    <div>VLEERmuis</div>
    <div>muis</div>
    <div>huismuis <b>vette ding</b></div>
    <div>grote muis</div>
    <div>kleine muis</div>
    <div>mickey muis</div>
    <div>mus</div>
    <div>olifant</div>
    <div>giraffe met een dikke <b>B</b></div>
</dr-input-text>
</form>

<dr-tabsheets type="buttons" >
    <div label="tab1" description="tab1 does very much blala" class="active">
       Lorem, ipsum dolor sit amet consectetur adipisicing elit. Laborum quas exercitationem esse animi accusantium nesciunt harum ratione eos qui, sunt corporis blanditiis necessitatibus nulla in dolores doloremque. Dolore, quos ab.
       Totam officia ducimus amet maiores laboriosam aliquam consequatur et, iste perspiciatis nobis doloribus quam incidunt in tempore possimus vel reiciendis at aspernatur porro. Laboriosam odit fuga eius! Incidunt, accusantium maxime.
       Earum iste eveniet vero odio maxime illo suscipit nisi repellat id dolorem? Unde incidunt natus voluptates? Animi nam commodi fugit corrupti aliquid rem, ea alias maiores aspernatur laborum sit dolorum.
       Non, illum? Commodi deleniti dignissimos quidem porro quibusdam molestiae, totam asperiores tempore fuga deserunt quas, dolore sapiente repellat amet modi odit quaerat architecto? Delectus similique cupiditate optio? Voluptatem, natus fugit?
       Tempora consequuntur debitis sunt aliquid officia, est, voluptatibus quis deserunt quos odit, esse architecto iusto iure nam quod saepe fugit commodi earum. Blanditiis debitis dolores labore nesciunt fugit quas aliquam.
       Animi doloremque eum iure reiciendis, eius in odio blanditiis amet iste debitis quibusdam accusamus officia doloribus saepe, suscipit pariatur quidem vero expedita aspernatur perferendis harum natus! Minima unde velit voluptas!
       Similique recusandae nam minima molestiae, ut, doloribus mollitia hic blanditiis voluptas assumenda ipsum ullam eos saepe fuga, distinctio provident veniam culpa expedita. Tenetur animi earum ex modi libero odio rerum!
       Fugiat deserunt dolore esse nulla aliquam ad hic amet aliquid vero minima, ullam nihil adipisci totam facilis excepturi reprehenderit quam quod! Dolorum atque natus temporibus quia laborum culpa ratione porro?
       Sunt asperiores tenetur saepe quis voluptate et. Dolores eum, consectetur numquam ducimus deserunt non quia voluptatum enim dicta, similique ullam velit possimus atque earum, doloremque suscipit eveniet obcaecati repellat harum?
       Asperiores atque voluptatum nulla rerum. Recusandae, ratione est eius soluta eum facere mollitia? Debitis consequuntur a, praesentium dignissimos tenetur amet minima. Reprehenderit enim eos commodi repellat qui possimus ipsam ut!
    </div>
    <div label="tab2" description="tab2 does very much blala">
        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Repellendus facere debitis optio possimus necessitatibus expedita vel neque unde incidunt animi, assumenda tempore accusamus quis totam, non quam inventore vitae voluptas.
    </div>
    <div label="tab3" description="tab3 does very much blala">
        Lorem, ipsum dolor sit amet consectetur adipisicing elit. Rerum soluta quasi, explicabo veniam sit incidunt doloremque praesentium, quas a repellendus perspiciatis magnam earum esse! Repellat voluptates modi facere sequi reiciendis.
        Molestiae sunt explicabo eveniet voluptate enim dicta nobis quasi, neque, labore alias accusamus culpa illum asperiores, magni natus recusandae a excepturi distinctio. Sapiente adipisci reiciendis laborum sit nisi quis optio.
        Recusandae autem, sed cumque ipsam non atque placeat a nihil hic ad repellendus, perspiciatis saepe? Quaerat laborum perferendis odit, corrupti delectus deserunt consectetur aut voluptas, officia sapiente ea nemo neque.
    </div>
    <div label="tab5" description="tab3 does very much blala">
        tab5
    </div>
    <div label="tab6 - hollander" description="tab much blala">
        tab6
    </div>    
    <div label="tab7 b- ssdfsfsfdfsdfsdfsdfs" description="tab does very much blala">
        tab7
    </div>
     <div label="load url" url="http://localhost/cms5/application/vendor/cookieconsent/cookieconsent.css">
        tab4
    </div>

    <div label="tab8 asdfasdfasdf" description="blala">
        tab7
        Lorem ipsum dolor sit, amet consectetur adipisicing elit. Soluta voluptatem, libero obcaecati iusto nam ratione dignissimos assumenda quidem aliquam, non consectetur nesciunt eaque eius incidunt vel facilis porro quos voluptates!
        Suscipit obcaecati soluta quas eaque qui error ad dolor quibusdam, veniam sequi. Explicabo ratione vel a culpa officiis totam quidem voluptatem sunt quasi tempore dolores, sequi aut magnam! Distinctio, eligendi.
        Veniam eos nobis placeat recusandae quibusdam. Et eos deleniti natus explicabo, a placeat laboriosam suscipit consequatur rem eius quam consectetur aperiam sit recusandae ipsam rerum, culpa fugiat unde, numquam labore?
        Maiores debitis ea, minima ex facere voluptas ipsum praesentium, dignissimos suscipit fuga exercitationem tenetur repellat? Quis nesciunt, eveniet quia inventore eius explicabo, consectetur adipisci veritatis facilis minus ratione ducimus necessitatibus.
        Consequatur eum odio qui quibusdam velit ipsam sunt necessitatibus suscipit? Tenetur est veniam adipisci voluptatem provident officiis nemo alias, laudantium minima ipsa neque doloremque ut doloribus mollitia, nisi id praesentium.
        Laudantium, blanditiis? Consequuntur, qui, molestiae sit esse vero perspiciatis amet vitae fugit error sapiente quidem quas nam, hic beatae. Autem nemo similique iusto amet ipsam repellendus dignissimos reiciendis eos voluptatem.
        Voluptas voluptatum laudantium ut veniam similique natus aspernatur quisquam repellat sunt voluptates inventore repudiandae eos, error molestias animi consequatur dicta, nisi facilis fuga tempore odit quos reiciendis nesciunt! Culpa, consectetur!
        Similique sit earum quis? Fugiat amet illum eius deleniti iste consequatur corporis adipisci, rem ab error culpa, molestias molestiae dicta doloremque quidem perferendis repellat nam ducimus? Exercitationem quo velit blanditiis!
        Quia nulla dolorum vel fugiat in? Expedita dolorum nisi incidunt repellendus excepturi, quaerat maiores minima mollitia suscipit odit magnam? Ad mollitia nobis hic voluptates voluptatibus esse ratione modi rem saepe?
        Vel quam minima, fuga, soluta atque alias laboriosam quis natus repellendus voluptatum sequi neque cumque nam, voluptas nihil eaque facere provident. Aperiam, impedit laboriosam. Dignissimos doloremque quam omnis adipisci quia.
    </div>
</dr-tabsheets>

<div id="tabcontainer">
    <div id="tablistcontainer">
        <div>tab tur eum odio qui q</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
        <div>tab niam similiq</div>
    </div>
    <div>
        <div>tabcontent asdf asdf asdf asdf asdf asd fasd </div>
    </div>
</div>

<style>
    #tabcontainer
    {
        max-width: 100%;
        overflow: hidden;

        display: grid;
        grid-template-rows: 25px minmax(25px, 200px); /* height tabsheets + height tab content */

    }

    #tablistcontainer
    {
        /* overflow: hidden; */
        /* position: relative; */
        /* column-gap: 1px; */

        display: flex;
        overflow: scroll;
        -ms-overflow-style: none; /* remove scrollbar ms */
        scrollbar-width: none; /* remove scrollbar firefox */
        scroll-behavior: smooth;

    }

    #tablistcontainer div
    {
        display: inline-block;
        /* border-radius: 0.5rem; */
        user-select: none;
        white-space: nowrap;
    }    

</style>


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

        // objMenu = new DRContextMenu();
        // console.log(objMenu.isShowing());
        // objMenu.anchorobject = objNum;
        // debugger
        // objCombobox = document.createElement('dr-input-combobox');
        // console.log("testtttttt123123123");

        // let objMain = document.querySelector(".maincolumn-contentwrapper");
        // objMain.append(objCombobox);


        let objText = document.getElementById("textboxie");
        // objText.setValue("kut");

    });
</script>

<!-- <dr-input-upload></dr-input-upload> -->