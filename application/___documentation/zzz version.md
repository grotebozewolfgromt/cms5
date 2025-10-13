# Version document starting  28 apr 2024


## next up: pagebuilder
* module moet designobjects kunnen aanleveren (TPagebuilderDesignobjectAbstract ???)
* gestippelde lijn in highlight color wanneer drag allowed
* anti cross site token bij saven
* checkin/checkout
* drag and drop on mobile
* bezig met undo states
    //@todo window van maximaal 100 undo levels
* delete knop delete teveel elementen
* selectie-window met muis om elementen te selecteren. Een doorzichtige canvas over de designer: https://medium.com/variance-digital/interactive-rectangular-selection-on-a-responsive-image-761ebe24280c 

## next up: short-term todos
* hashfield voor username en bij inloggen user opzoeken op basis van hashed username
* config file define('GLOBAL_CMS_LOGINONLYWHITELISTEDIPS') ipv $bLoginOnly
* schrijven config variablen functie maken
* explanation dialog maken.
* explanation van tabs naar dialog verplaatsen 
* alle modules staan default op front-weergeven --> default: NIET op frontpage weergeven
* bij users ook hashed username vastleggen, en deze ophalen met loadFromDBByUserLoginAllowed()
* @TODO: dubbele processen voor draggen en droppen voor elk op elkaar liggend event
* @TODO: bug drag drop vanuit newpanel in grid dropped het echte element ipv render

* @todo remove db-settings emailaddress (config vervangt dit)

* @todo als pagebuilder niet op slaat, moet je een form toevoegen waarin de textboxen vallen

## TODO searching + gdpr
* bugfixes: dbfilters
    - selecteren checkboxes "contains" verdwijnt oude waarde niet
    - status_applied moet ook in menu verschijnen
    - bij toevoegen opent scherm filter. 
    - checkbox 'enable' vervangt knop 'set filter'
    - bug: status_applied geeft php error
    - minimaal 3 characters zoeken beperking verwijderen
    - number filter geeft met integer ook .0000 terug'
    - na <enter> in quicksearch field gefocussed blijven op quicksearch field
* corrigeren contacten invoer
    * bic toevoegen
    * aanhef toevoegen
    * onchange en onkey automatisch aan ieder veld toevoegen
* labels component maken


## 13 okt 2025:
* ajaxform.js: automatisch attachen event listeners verbetert
* dr-input-date + dr-input-time: ADD: focusout triggert een 'change'-event wanneer waarde gewijzigd


* gestopt: midden in overschakeling naar auto attachen event listeners die het document dirty maken.
* todo: afmaken onPageLoad() in ajaxform.js waarin 
    1. checkbox events attachen
    2. DRInputX events attachen
* todo: setOnkeyup("setDirtyRecord()"); verwijderen in alle detailsave_ schermen


## 10 okt 2025:
* generateNiceid() geeft nu nummers ipv characters en nummers
* contacts: eerste letter hoofdletter converteren
* contacts: alles naar hoofdletter converteren
* contacts: alles naar kleine letter converteren
* bugfix: ajaxform.js: removeAllInputErrors() verwijderde de css class niet
* rename validator classname => T and camelCase
* correct postal code weird characters
* lastnameprefixes als aparte tabel
* salutations als aparte tabel
* TSysContacts heeft geen parent TsysContactAbstract meer
* added: TSysContacts: createContactDefaultsDB() voor snelle contact creatie
* TSysContacts: nederlandse postcodes worden gecorrigeerd bij het saven
* TSysContacts: nederlandse telefoonnummers worden gecorrigeerd bij het saven
* detailsave_contacts: velden worden nu allemaal gefilterd of verkeerde karakters



## 9 okt 2025:
* skip maintenance mode check is now a constant instead of variable
* rename TConfigFileFramework => TConfigFileApplication
* installer uses and sets maintenance mode
* installer uses application_defaults.php config file
* rename constant APP_PATH_CMS_CONFIGFILE_FRAMEWORK => APP_PATH_CMS_CONFIGFILE_APPLICATION
* installer prompts for GDPR days retention and unencrypted fields
* anonymize data after X days (cronjob)

## 8 okt 2025: 
* maintenance mode implemented in bootstrap and config file classes + config files

## 4 okt 2025: heavy renames
* added gitignores
* admindr directory => application directory
* constants rename 'GLOBAL_*' => 'APP_*'
* rename config files 'framework_' => 'application_'
* PHP_EXT_INTL value changed: 'php_intl' => 'intl' 

## 2 okt 2025: 
* TDBPreparedStatementMySQL begonnen met quicksearch query werkt in principe, echter sql fout op rijen die indexes niet matchen 
* column sCountryIDCodePhone1 i ervoor
* column sCountryIDCodePhone2 i ervoor
* fulltext-indexes nu op alle colommen en individuele colommen
* limit form submits seconds in config in te stellen
* het steeds verder specificeren van een zoekopdracht met nieuwe quicksearches werkt nu

## 1 okt 2025: 
* TDBPreparedStatementMySQL ondersteunt nu Fulltext velden in createTableFromModel()
* aparte formsections toegevoegd aan contacts-detail
* recordid en niceid toegevoegd aan contacts-detail
* modellist.js events fired in document.onload
* events dr-paginator en dr-db-filters lange naam => 'changed'
* TSysModel added extra fields for improved quicksearch
* TDBPreparedStatementMySQL begonnen met quicksearch query bouwen 

## 30 sept 2025: 
* added function collateString()
* collateString() changes
* contacts: 2x postcodes zijn nu encrypted
* bugfixes: database filters
* 30 sept 2025: lib_string: generateNiceID() added
* niceid toegevoegd aan TSysModel
* generateNiceID blocksize als parameter
* generateNiceID bugfixes
* niceid toegevoegd aan contacts list
* setCustomIdentifierAuto() verwijderd
* land comboboxen vervangen door dr-input-combobox
* tel country codes toegevoegd in TSysContacts
* tel country codes toegevoegd in contacts scherm toegevoegd
* generateSearchKeywordsField() toegevoegd aan TSysContacts
* lib_security: decrypt + encrypt beveiligd tegen null en ''
* detailsave_contact geupdate met i-icons

## 29 sept 2025: 
* dr-input-upload.js translation "delete before upload"
* de oude uploadmanager verwijderd
* countrycodes phone toegevoegd bij landen
* CrudlistcontrollerAjax => css wordt niet meer ingeladen + nieuwe includejswebcomponent functie gebruikt
* bugfix: CrudlistcontrollerAjax: dr-input-checkbox werd niet ingeladen
* locks en checkouts op records triggeren een fatale error nu
* bugfix: dr-input-datetime operation on null waardoor het niet werkte
* setFieldFulltext() added to TSysModel() voor betere zoekprestaties
* alle TSysModel children hebben nu in defineDBTable() een setFieldFulltext
* door systeem heen geklikt en werkt allemaal



## 26 sept: dr-input-upload
* checkt nu of file al bestaat in directory wanneer upload en rename (naar nieuwe naam)
* bugfix: wanneer een file upload die al bestaat in directory, kun je die niet meer renamen =>fout
* temp disabled commented code terug gezet
 * 26 sept 2025 dr-context-menu.js niet nodig om id op te geven, dit component zoekt niet meer naar id om zichzelf te verwijderen
 * 26 sept 2025 dr-context-menu.js text aligned to left
 * cms global: wat wijzigingen aan titel weergave ter voorbereiding van de header die gaat veranderen
 * 26 sept 2025: HTMLTag: >getInnerHTML() added
  * 26 sept 2025 dr-checkbox.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de checkboxen niet gesaved
  * 26 sept 2025 dr-info-icon.js removeEventListeners() was commented out wich resulted in an error
 * 26 sept 2025 dr-input-combobox.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
* 26 sept 2025 dr-input-date.js: BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 26 sept 2025 dr-input-datetime.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 26 sept 2025 dr-input-number.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
 * 26 sept 2025 dr-input-text.js BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
* 26 sept 2025: dr-input-time.js: BUGFIX: formvalue not set on creation. dus een record openen, dan save, werden de values van de box niet gesaved
* detailsave_cmsmenu: extra <dr-icon-info> met meer uitleg
* header aangepakt: het is nu mogelijk om menuitems in toolbar te plaatsen
* dr-input-upload.js: bugfix: file werd niet weergegeven bij database load
* dr-input-upload.php+js: bugfix: bij deleten nu alle bestandsnamen van geresizde images doorgegeven
* dr-input-upload.php: extra filtering op filenamen
* dr-input-upload.js: multiple uploads voegde geen extra box toe bij uploaden nieuw bestand
* dr-input-upload.js: status: getIsUploading()
* dr-input-upload.js: status: getIsUploading() wordt gecheckt bij needsNewInputUpload()
* dr-input-upload.php: problemen in jsonDecodeForm() verholpen die optraden bij multipleuploads
* dr-input-upload.js: bugfix: wanneer file niet bestaat en je delete deze, geen server request, maar een reset();


## 21 sept: dr-input-upload
* version number now in config files
* rename class config file class: 'TPHPConstantsConfigFile' => 'TConfigFilePHPConstants'
* grondige rename constanten: 'global_path' => 'path' en 'global_www' => 'url'
* module list: gaf error vanwege verwijderde positie veld
* rename dir '___installer' => 'installer'
* wp-admin honeypot added
* css aangepast voor menu iconen
* DRInputUpload: wanneer resize failed nu goede foutmelding
 * 25 sept 2025: HTMLTag: setAttributeAsBool() extra parameter voor implicit booleans zoals disabled en 
 checked ==> dit gaf problemen met multiple, waardoor de formvalue van de verkeerde input gepakt werd, waardoor niks in de database opgeslagen werd
 * bugfix: wanneer: upload, save, delete, save dan lege gegevens niet naar database geschreven
* transparant wanneer nog aan het uploaden 
* wordwrap uit in label
* libstring: filterWhitelist(): struikelt niet meer over boolean
* libstring: filterWhitelist(): struikelt niet meer over lege string
* libfile: getFileExtension(): struikelt niet meer over een file zonder extensie
* ajaxform.js: in debug mode: geeft json weer in console
* bugfix: filenames met spaties werden niet weergegeven bij openen uit database
* bugfix:  wanneer alt text gewijzigd, wordt deze niet bijgewerkt in label
* renames nu zonder punt (.) in filename in JS. Wordt ook gefiltert in PHP
* 25 sept 2025: lib_file: changed: generateUniqueFileName() with a far more intelligent algo to generate a unique filename
* bij het uploaden wordt bestaande file niet meer overschreven, maar een unique filename gegenereerd
* verder gezocht naar rename fout, zonder resultaat


## 20 sept: dr-input-upload
* setting alt text werkt nu
* bugfix: icoon  voor ovige filetypes is zwart ook in dark mode 
* added: preview op non-image bestand geeft file weer
* alle dialog vensters vervangen door nieuwe <dr-dialog>

## 19 sept: dr-input-upload
* bugfix: bij het record laden en multiple uploads, dan werd geen extra upload weergegeven. Nu wel
* bugfix: ajaxform.js: elementen worden nu uit formulier in een keer opgevraagd ipv te loopen. Hierdoor komen nu elementen met namen met haakjes (=array in php) mee.
* added: ondersteuning voor meerdere DRInputUploads die terugkomen
* separate .droppable and .thumbnail image classes (while still the same div in this version)
* added lib_string.js
* changed lib_ui_ajax.js => lib_ui_dom.js
* extra php opening tag check
* 19 sept 2025: lib_file: added: getMimeTypeExtension(), getMimeTypeFile()
* isValidFileType() gebruikt nu getMimeTypeFile();
* dr-input-upload: viewToModelImage() modelToViewImage() skippen over het setten van de velden heen als de file geen image is
* bugfix: list_products irandomid not found

## 18 sept: dr-input-upload
* FormInputAbstract: WHITELIST_* consts verplaatst naar lib_sys_typedef voor zover deze nog niet bestonden
* 18 sept 2025: lib_file: filterFileName() updated for directory traversal
* handleUpload() filtert op gevaarlijke filenames
* handleRename() filtert op gevaarlijke filenames
* handleDelete() filtert op gevaarlijke filenames
* cronjob checkt of installer enabled is
* cronjob checkt op missende php modules
* rename getPrequisitesNotLoaded() => getPHPExtensionsNotLoaded()
* DRIconInfo.js resets the i-icon color, font, and font weight
* images zijn nu achtergrondafbeeldingen waardoor DRInputUpload niet meer verspringt bij meerdere DRInputUploads
* ophalen database record en weergeven werkt nu
 * on delete: multiple: DRInputUpload uit DOM verwijderd ipv resetten
 * alt is empty by default
 * reset() resets all values (instead only filename)
 * bugfix: .js handleUpdate(), handleDelete(), handleRename() ivm file history was het niet mogelijk om renames te verwijderen
 * bugfix: modelToView() voegt nu files aan filehistory toe

## 17 sept: dr-input-upload
 * 17 sept 2025: TSysModel: added trashcan field
 * 17 sept 2025: TSysModel: added image alt text field
 * voorbereidingen voor alt en width+height voor resize images
 * renamen werkt met geresizede images
 * deleten werkt met geresizede images
 * check maximum upload size php + restrict maximum upload size
 * 17 sept 2025 dr-dialog.js hoeft geen document.querySelector("body").appendChild(objDialog) meer aan te roepen 
 * opslaan in database werkt


## 12 sept: dr-input-upload
* DRInputUpload: can create new sibling
* DRComponentsLib.js + lib_ui_ajax.js: added function: getNewHTMLId();
* createSiblingInputUpload() aangepast
* setFileDataTransfer() aangemaakt
* setFileDataTransfer() wordt nu gebruikt door drag and drop
* drag and drop multiple files works 
* always add new clean DRInputUpload on dropping (also when 1)
* error: when more than 1 file is dragged but its not a multiple upload
* "name" property automatically converted into array brackets[] when multiple upload is possible
* selecting multiple files in file upload dialog werkt nu!!!
* er wordt niet zomaar blindelings een nieuwe DRInputUpload toegevoegd bij multiple files, maar alleen als dit nodig is.
* er wordt nu gekeken naar de allerlaatste DRInputUpload met dezelfde naam om een nieuwe DRInputUpload achter te voegen
 * function om image te resetten en alles wissen (thumbnail+title). Bijv. bij foutmelding
* betere foutafhandeling bij bijv offline. object wordt gereset
* mogelijkheid om upload te cancellen via menu (objXHR is nu globaal in klasse)
* some translations


## 11 sept: dr-input-upload
* extra checks op file type
* 11 sept 2025: lib_file: isMimeImage() added
* bugfix: dr-progress-bar.js werd niet geinclude in DRInputUpload
* 11 sept 2025 dr-progress-bar.js v2 created. now with infinite progressbar
* nu ook status: "processing" wordt weergegeven met infinite progressbar
* dr-progress-bar.js: css transition voor 2 properties

## 10 sept: dr-input-upload
* public -> private handle[x]() functions
* executeURLParams() vervangt ook paths nu
* rewrite handleDelete()
* handleDelete() checkt op goede form-field-name
* handleUpload() form-field-name check toegevoegd
* handleRename() form-field-name check toegevoegd
* handleUpload() geeft nu json response terug, bij error geeft dialog met foutmelding
* resizen werkt in basis, nog wat optimalisaties doen
* resizen proportioneel werkt nu.

* next: goede foutmelding als resizen mislukt

## 9 sept: dr-input-upload
* menu niet meer getoond wanneer geen afbeelding is geupload
* upload directory heeft geen htaccess meer die alles blockt
* 9 sept 2025: lib_sys_framework: createIndexFile404InDir() toegevoegd
* extra vars  in config file: GLOBAL_CRONJOB_ISEXECUTING en GLOBAL_CRONJOB_LASTEXECUTED
* settings: maintenance scherm verbeterd
* settings: maintenance: tijd cronjob toegevoegd
* dialog venster niet groter dan beeld + overflow blijft nu binnen dialog
* zoom in en uit preview werkt nu
* preview venster werkt nu helemaal
* 9 sept 2025 dr-dialog.js defaultred button
* dr-input-upload dispatch 'change' event
* copy to clipboard works


## 6 sept: dr-input-upload
* philosophy comments dr-dialog.js
* todos toegevoegd DRInputUpload

## 5 sept: dr-input-upload
* fileupload: menu wordt weergegeven
* webcomponents weggehaald uit url router
* theme_webcomponents.css aangemaakt voor css van webcomponenten
* bezig verhuizen naar form controller
* function renames getUploadDir()
* uploaden werkt nu in dedicated upload directory voor module
* deleten werkt nu 
* file history checkt of je file wel mag verwijderen
* rename werkt nu
* extra functions for filehistory

*@todo nog een paar items op todo list in DRInputUpload.php

## 3 sept: file upload + dialogs
* acceptedfiletypes renamed => accept
* accept toegepast op internal upload file ( accept filetypes in file selector)
* set dirty werkt nu
* rename upload component in js en php
* preview is geresized
* knoppen naar footer verplaatst
* css dialog nu aan het bijwerken met default waardes
* dialog is klaar
* exposed abortcontroller zodat je die kunt gebruiken voor knoppen



## 29 aug: dr-input-upload
* begonnen met daarwerkelijke file upload
* backup werkende versie file upload
* file upload werkt!!!!

## 26 aug: demo mode
* favicon installer
* rename tab: settings: cronjob -> maintenance
* tab: settings: maintenance: added start installer
* tab: settings: maintenance: enable/disable installer
* CMS categories modules verwijderd
* position field bij modules verwijderd
* permissions "_own" (i.e. change_own) globaal maken
* favorites only in menu when there is at least 1
* iconinfo smaller'
* meer uitleg maintenance tab
* begin fileupload gemaakt. Javascript gedeelte werkt.



## 23 aug: demo mode
* added GLOBAL_DEMOMODE to config files
* addded function getPermissionsDemoModeAllowed() to modules
* verder gegaan met implementeren demo mode
* demo mode zover af




## 22 aug: installer
* haLVERWEGE STAP7
* in principe stap7 af en installatie gedeelte
* installer_enabled in config file
* bugfix in SysIPAdressBlackListWhitelist - tijdens installeren werden iedere keer 1 record ingevoegd. bij meerdere installs dus meerdere records, waardoor 404 fake error
*bugfix: stap2: prequisites not loaded
* bufgix: permissioncountries table  checkt of record al bestaat (wordt met installeren/updaten telkens toegevoegd)
* gedaan: installpassword oplossen zoals installerenabled
* file renames
* bugfix: installpassword: sessie werd niet gestart
* uninstall werkt nu helemaal
* function disableInstaller() nu beschikbaar in TInstallerScreen
* update aan update procedure: disableInstaller() disabled the installer
* oude install functies in framework onklaar maken
* oude installatie files verwijderd



## 21 aug: installer
* 21 aug 2025: TSysSettings: install: looks if record exist
* 21 aug 2025: TSysModel: added recordExistsTableDB()
* bezig stap 6: database schema wordt ook gecreeerd nu. stap 5 kan verwijderd worden.
* bezig stap 6: referentie module naar module categories verwijderd
* bezig stap 6: step6: updateModules() done
* bezig stap 6: step5: verwijderd
* bezig stap 5: alle stappen klaar - oeps niet waaar
* bezig stap 5: propagate data system tables klaar
* bezig stap 5: propagate data modules klaar
* TInstallerSCreen: modes geimplementeerd
* stap 6 user createn klaar!

## 20 aug: installer
* rename constante frameworkinstallpassword in config file framework
* installer: installpassword tries toegevoegd aan config en wordt nu gecheckt
* rename constante frameworkinstallpassword in config file framework again => GLOBAL_INSTALLER_PASSWORD and GLOBAL_INSTALLER_PASSWORD_TRIES
* stap 4 klaar
* stap 5 klaar
* bezig stap 6 + implementeren server side events - progressbar enzo werkt, maar zaken voor framework nog integreren
* bezig stap 6: nu framework install implementeren
* bezig stap 6: framework install werkt, afgezien van items in upgradeframework

## 19 aug: installer
* isntaller: betere ondersteuning voor mobiel
* installer: welcome screen
* installer: step1: license
* javascript function voor afhandelen form submit (ipv save button)
* javascript : prevent user from clicking twice
* installer: step 2 complete
* installer: step3 complete
* installer: install password opgeslagen in session
* installer: bezig step4 (foutje: config file bestaat altijd!)



## 17 aug: installer
* logfiles directory wordt automatisch gecreeerd wanneer deze niet bestaat
* cache en backup directory wordt in installatie process gecreeerd
* FormInputAbstract::WHITELIST_URLSLUG added
* ratelimiter maakt dir aan als nog niet bestaat
* sse_progress in ___design dir
* TInstallerScreen klasse gemaakt
* step1 klasse aangemaakt




## 15 aug: CMS menu db
* fix dubbele use dr\modules\Mod_POSWebshop\Mod_POSWebshop; in productcategorien
* favorites worden nu bovenaan menu getoond
* 15 aug 2025: lib_sys_typedef: added SESSIONARRAYKEY_CACHE
 * 15 aug 2025: lib_sys_framework: speed improve: auth() en authRes() doen nu 1 check ipv 2
 * 15 aug 2025: menu: caching werkt nu in session
 * 15 aug 2025: bootstrap_cms: $objsysmodules->loadFromDB() verwijderd waardoor framework meer responsive is
 * 15 aug 2025: aanpassingen pagebuilder die de default module opvroeg uit $objsysmodules
 * debug cache cleanup
 * 15 aug 2025: TTreeModel: added deleteFromDB() overloaded from parent
 * 15 aug 2025: DRInputCombobox: addItemsFromModel() and addItem()
 * 15 aug 2025: TDBPreparedStatementMySQL: support for sql DISTINCT
 * 15 aug 2025: TSysModel: added unique() and distinct() 
 * 15 aug 2025: TSysModel: added unique() and distinct() 
 * 15 aug 2025: detailsave_cmsmenu: permissions worden weergegeven in combobox
 * 15 aug 2025: detailsave_cmsmenu: modules worden weergegevn in combobox
 * 15 aug 2023: FormGenerator: support for info icon
 * 15 aug 2023: FormGenerator: added: addQuick()
 * list_productcategories en detailsave_cmsmenu

* todo: checksum menu gaat nog niet goed
* url slug copy vanuit andere editbox en converteer


## 14 aug: CMS menu db
 * 14 aug 2025: tpl_block_menu created voor het nieuwe menu
 * 14 aug 2025: TSysModel: get() checks if table is set explicitly instead of implicitly in debug mode
 * 14 aug 2025: TSysModel: constructor: checks only if getTable() exists in debug mode
 * 14 aug 2025: TTreeModel: added generateHTMLUlTree(), generateHTMLUlTreeParent(), generateHTMLUlTreeChild()
 * 14 aug 2025: HTMLTag: bugfix: setInnerHTML(), setTextContent() didnt reset cache count children
  * 14 aug 2025: HTMLTag: optimalisaties doorgevoerd aan renderHTMLNode();
  * 14 aug 2025: menu aan de linker kant bestaat nu
 * 14 aug 2025: TSysCMSMenu removeMenuItemsForModuleDB() rewrite for module field  
 * 14 aug 2025: TSysCMSMenu translation van menu
 * 14 aug 2025: lib_sys_framework: authRes() added
 * 14 aug 2025: authRes() verwerkt in TSysCMSMenu menu genereren


## 13 aug: CMS menu db
* nu mogelijkheid om ook toolbar items op te slaan
* 13 aug 2024: FormInputAbstract: added blacklist and whitelist filtering
* 13 aug 2025: HTMLTag: sanitizeHTMLTagAttributeValue() replaces " with &quot;
 * 13 aug 2025: lib_sys_inet: purifyHTMLRecursive() function uit purifyHTML() gehaald (anders php klaagt over dubbele declaratie)
 * 13 aug 2025: lib_sys_inet: filterXSSRecursive() function uit filterXSS() gehaald   (anders php klaagt over dubbele declaratie)
 * 13 aug 2025: lib_sys_inet: function purifyHTMLSVG($sSVG) added
 * 13 aug 2025: ajaxform.js: unescapes html coming back from server after exit field (a value check is done)
 * menu: uninstall module: menu items verwijderen werkt nu
  * 13 aug 2025: HTMLTag: parserProcessTag() rewritten. The old one didn't allow spaces in attribute value
  * 13 aug 2025: bugfix: module: POSWebshop was niet te deinstalleren door samenvoegen van modules was de model volgorde niet goed
  * 13 aug 2025: bugfix: modules: installeren. was niet mogelijk door dubbele pagina laadt, waardoor 2x werd teogevoegd wat fout gaf




## 12 aug: CMS menu db
* de modules worden nu toegevoegd aan db menu
 * 12 aug 2025: TSysCMSMenu bugfix moveToParentNodeDB(): record werd niet gecleared, dus niet goed geladen uit db
* iets andere fucties, zodat ik deze per module kan installeren
* items uit menu opvragen en toevoegen werkt nu
* menu maken toegevoegd aan installatie procedure
* comments geupdate voor alle public function getMenuItems() functies

* todo: menu: maken aan de linker kant
* todo: menu: auto complete in detailscherm: url modules + permission resource
* todo: menu: uninstall menu verwijderen





## 8 aug:
* alle POS gerelateerde zaken nu in de POSWebshop module
* cms menu model
* cms menu list
* bugfix: TSysModel(): isFavorite column werd niet gecreerd (gekeken naar isDefault ipv isFavorite)
* cms menu detailscherm basis
* cms menu resource veld toegevoegd
* aan elke module getMenuItems() toegevoegd
* changes to getMenuItems() 
 * 8 aug 2025: TSysModel: addCopy() addded
 * 8 aug 2025: TSysModel: removeRecord() added
 * 8 aug 2025: TSysModel: removeRecordAtRecordpointer() added
 * bezig met menu maken createMenuFromModulesDB();


## 6 aug 2025: verder met drag drop in recordlist ajax 
* error wanneer het position move records failed
* function herschreven om records te moven in modellistajax
* draggen op eerste child nu niet meer mogelijk
* functie om laatste child te vinden
* added: halt on error true/false-> recordlist. 
* draggen naar eerste child resulteert nu in het inserten NA de laatste child

## 31 juli 2025: verder met drag drop in recordlist ajax 
* verplaatsen nodes met kinderen werkt nu (nog wat zaakjes verfijnen, zoals id-1 en parentid=0)
* id 0 ipv -1 voor root in positionChangeDB()
* depth aanpassen wanneer dropafter in tree
* parentid=0 uitzondering in  updatePositionAndDepthByParent()
* modellist.js: bBusyMoving boolean ivm race condition
* spinner toegevoegd
* bugfix: wanneer je een parent op zn eigen child dropt
* todo: goede error bij fout in change position
* todo: wanneer je tussen parent en child dropt, komt item ertussen te staan, ipv achter alle children

## 30 juli 2025: verder met drag drop in recordlist ajax 
* bugfix: geen beeld door verwijderen updown constants in lib_sys_typedef
* dragging dropping voor gewone records werkt nu met database
* dropping ON werkt nu!



## 25 juli 2025: verder met drag drop in recordlist ajax 
* 25 july 2025 dr-dragdrop.js bugfix: met drop-drop eerste element wanneer draggable ook droppable is
* 25 july 2025 dr-dragdrop.js kleine dingetjes ter voorbereiding
* 25 july 2025 dr-dragdrop.js bugfix2: met drop-drop eerste element wanneer draggable ook droppable is
* 25 july 2025 dr-dragdrop.js attribute "behavior" now called "dropbehavior"
* 25 july 2025 record ids worden toegevoegd aan tabelrij
* bug ergens waardoor geen beeld

## 24 juli 2025: verder met drag drop in recordlist ajax 
* bugs: draggen droppen niet mogelijk in productcategories
* bugs: dropcursor css table nu klein
* werkt in principe, 
* nog een bug voor eerste item

## 22 juli 2025: verder met drag drop in recordlist ajax 
* dr-dragdrop.js dropcursor nu tabel ook mogelijk
* dr-dragdrop.js op dropcursor droppen nu mogelijk
* dr-dragdrop.js vergeten signals toegevoegd aan eventlisters
*  eerste stap drop on draggables. werkt met transaction types, nog niet met productgroepen (wrs auth issue)

## 19 juli 2025: verder met drag drop in recordlist ajax 
 * 19 july 2025 dr-dragdrop.js added comments
 * 19 july 2025 dr-dragdrop.js changed updateUI() -> reloadDraggablesDroppables
 * 19 july 2025 dr-dragdrop.js ONLY uses draggables and droppables inside the html tag <dr-drag-drop>
 * 19 july 2025 dr-dragdrop.js isDropAllowed() function and functionality added
 * next: droppen op een item
* next: dragcursor tabelbreed

## 18 juli 2025: verder met drag drop in recordlist ajax 
* draggen/droppen werkt redelijk, maar nog niet in database
* update aan pagebuilder
* omgezet naar versie met dragcursor zodat je kunt droppen op een item zelf

## 17 juli 2025:
* draggable rows

## 16 juli 2025:
* position change methodes geupdate, met name positionChangeDB()
* positionChangeDB() is getest en af.
* nu doorgaan met drag & drop volgorde

## 18 juni 2025:
* isFavorite field toegevoegd aan TSysModel;
* 18 jun 2025: TSysModel: isFavorite field added + loadFromDBByIsFavorite() function
* verschillende aanpassingen in verschillende modules
* sommige schermen hebben een favorite field erbij gekregen
* favorite icon in list screen
* transaction has parked state
 * 18 jun 2025 dr-popover.js show() + css has extra capabilities for overflowing and showing a scrollbar
 * 18 jun 2025 dr-input-combobox.js scrolls only itemlist when too big, not the searchbar
 * 18 jun 2025 dr-input-combobox.js better selected and hover colors
 * transactions isCancelled() field toegevoegd
 * extends TCRUDDetailSaveControllerAJAX classes: onchange en onkeyup events toevoegd
* 18 jun 2025 dr-input-combobox.js stores old value, which is dispatched as parameter with "change" event 
* 18 jun 2025 dr-input-*.js renamed "change"-event to "update"-event
* 18 jun 2025 dr-input-*.js "change"-event is fired when users leave the textbox
* 18 jun 2025 dr-input-combobox.js css: items don't wrap
* verbetering aan form. de form-descript en formsection-line-error list omgedraaid

 
## 17 juni 2025:
* name-veld verplaatst van ttreemodel naar product model
* css shizzle for no-records message
* only web components needed are loaded
* getGUIItemName renamed to => getDisplayRecordShort
* added getDisplayRecordColumn() and getDisplayRecordTable() to TTreeModel
* added css class treeinvisibleident
* 17 jun 2025 dr-input-combobox.js checks if there are any items with value attribute
* product categories detail scherm
* 17 jun 2025: lib_string: generateChars() added
* product categories detail scherm: parent node werkt nu
* product categories detail scherm: diepte setten werkt
* @todo: order field in categories detail scherm

## 15 juni 2025:
* use js include in modeldetailssaveajax.php instead of template
* use js include in modellistajax.php instead of template

## 14 juni 2025:
* TDBPreparedStatement classes : updateFieldIncrementBetween() updateFieldDecrementBetween added
* functions added includeJS(), includeJSDOMEnd(), includeCSS() to flexibily load only the CSS and JS files you need
* included arrays for js and css files in templates for CMS
* standaard footer dingen toegevoegd aan $arrIncludeJSDOMEnd.
* TSysModel nieuwe function orderChangeDB() nog niet geimplementeerd
* order-field is renamed: "position"
* written out logic public function positionChangeDB
* updated logic public function positionChangeDB

## 13 juni 2025:
* TTreeModel created
* TProductCategories created
* product categories: list werkt
* TODO: product categories detailscherm



## 12 juni 2025:
 * 12 jun 2025 dr-checkbox.js BUGFIXES
 * tsyswebsites heeft nu een default veld
 GLOBAL_DB_SITEID_DEFAULT is verwijderd ivm tsyswebsites.isdefault
 * websites worden niet meer voor elke pagina geladen, alleen wanneer menu wordt weergegeven


## 6 juni 2025:
* 6 jun 2025 dr-tabsheets.js created
* 6 jun 2025 dr-tabsheets.js shadowdom not used, but DOM
* 6 jun 2025 dr-tabsheets.js dispatch event
* 6 jun 2025 dr-tabsheets.js geen buttons meer maar divs voor iets meer CSS controle
* 6 jun 2025 dr-tabsheets.js mouse hover hints
* 6 jun 2025 dr-tabsheets.js url load tabsheet werkt
* 6 jun 2025 dr-tabsheets.js tabsheets werken met hoogte van 200px (tab content scrollt)
* 6 jun 2025 dr-tabsheets.js tabsheets kunnen nu scrollen (knoppen nog implementeren)
* 6 jun 2025 dr-tabsheets.js tabsheets kunnen nu scrollen - werkt nu ook op scrollen (goede div panels)
* 6 jun 2025 dr-tabsheets.js tabsheets kunnen nu scrollen - gradient variabelen in css
* 6 jun 2025 dr-tabsheets.js als geen default tabsheet is opgegeven wordt standaard de eerste geselecteerd
* 6 jun 2025 tabsheets toegevoegd aan product scherm


## 5 juni 2025: 
* 5 jun 2025 dr-input-text: auto complete werkt nu
* 5 jun 2025 dr-components-lib.js: library voor alle web components
* dr-input-text updated met library
* dr-input-combobox updated met library
* dr-input-checkbox updated met library
* dr-input-checkbox-group updated met library
* dr-input-paginator updated met library
* dr-input-filters updated met library
* bugfixes in vorige componenten
* dr-info-bubble updated met library
* dr-input-number updated met library
* dr-icon-spinner updated met library
* dr-input-date updated met library
* dr-input-time updated met library
* backup
* dr-input-datetime updated met library
  * 5 jun 2025 dr-info-bubble.js renamed dr-info-bubble ==> dr-popover
  * 5 jun 2025 dr-info-icon.js renamed dr-icon-info ("icon" and "info" are switched)
  * 5 jun 2025 dr-input-text: nasty bug eruit waardoor character counter altijd werd weergegeven
  * 5 jun 2025 dr-input-text: php counterpart
  * 5 jun 2025 dr-input-text: php counterpart auto injects whitelist
  * verder met product-detail scherm

## 4 juni 2025:
* 4 jun 2025 dr-input-combobox.js setValue() en readAttributes() checken op bConnectedCallbackHappened
* 4 jun 2025 dr-input-text.js created
 * 4 jun 2025 dr-input-number.js defaults changed in readAttributes
 * 4 jun 2025 dr-input-* #attributeToString() function bugfix:  if (objHTMLObject.hasAttribute(sAttrName)) ipv getAttribute() ==> gaat fout met lege attribute waarde
 * 4 jun 2025 dr-input-* dispatched event is called "change" to be compatible with other <input>- elements
 * 4 jun 2025 dr-input-text verder 2
 * 4 jun 2025 dr-input-text verder 3 - character counter werkt nu
 * 4 jun 2025 dr-input-text verder 4 - zoeken voor autocomplete werkt (maar je kunt niets selecteren)


## 3 juni 2025:
* CT_CURRENCY and TP_CURRENCY is back
* alle TSysModel klassen functie getTableUseIsDefault() toegevoegd
* 3 jun 2025: TSysModel: bugfix setFieldDefaultsIntegerForeignKey()
* bugfixes: pagebuilder list
* bugfixes: transaction types
* bugfixes: organisations detail
* bugfixes: contact details
* * 3 jun 2025: TDBPreparedStatementMySQL: updateField() params extended with comparison operator + functionality for comparison operator
* 3 jun 2025: TSysModel: when record is default, it auto removes defaults from other records
* beginnetje gemaakt aan product detail scherm
==> moet nu een editbox webcomponent aanmaken (met checkmark en character counter)


## 30 mei 2025:
* error messages uitgebreid met stack trace in customExceptionHandler and customErrorHAndler
* TP_INTEGER --> TP_INTEGER32
* database wijzigingen producten module
* installeerbaar met wijzigingen
* vatclasses verplaatst naar mod_transactions
* 30 may 2025: TVatClasses: createMissingCountries() bugfix
* currency helemaal verwijderd
* meer verwijzingen van CT_INTEGER => CT_INTEGER32
 * 30 may 2025: TDecimal replaces bug in getValue() with 0.05, 0.005 and 0.0005 
 

## 23 mei 2025: 
 * 23 may 2025 dr-infobubble.js hideonclickoutside is new property + internal value that is taken into account when checking for mouseclickaction
  * 23 may 2025 dr-db-filters.js filter bubbles toggle when clicked on chiptext twice
 * 23 may 2025 dr-db-filters.js filter bubbles are not hidden when clicked outside
 * 23 may 2025 dr-input-combobox.js werkt nu via connected callback met eventlisteners items over te plaatsen
* HUGE BUGFIX: custom error handler didn't work!!! This exposed for example file paths to attackers. 
      Now gives vague error when not in debug mode.
* error handler gives also errors back in JSON form!!!!
* 23 may 2025 dr-input-combobox.js alwaysreturnvalue"  attribute toegevoegd
 * 23 may 2025 dr-input-combobox.js getValue() en labelvalue geeft nu defaults weer (eerste geselecteerde item)
 * 23 may 2025 modellistajax.js: bugfix: "show next page" in table when 0 records
 * 23 may 2025 modellistajax.js: when 0 records found: proper message.
  * 23 may 2025: TSysModel: added support for logical operators in findXXX() and quicksearch()
* 23 may 2025: TDBPreparedStatementMySQL: generateSQLWhereModel() support for logical operators AND and OR  
 * 23 may 2025 dr-db-filters.js elementen verplaatst in bubbles om ze meer gebruiksvriendelijk te maken
 * 23 may 2025 dr-db-filters.js bubble wordt automatisch geopend bij het toevoegen via menu


@todo: and/or behavior inbouwen bij filters
@todo: getDirty() bij meer dr-input-x componenten inbouwen
@todo: getValueNice() bij dr-input-x componenten inbouwen ==> kun je de UI waarde opvragen



## 22 mei 2025: ombouwen components naar goede constructor
* checkbox
* combobox
* contextmenu
* dbfilters
* spinner
* infobubble
* infoicon 
* infoicon werkte niet, nu wel weer
* checkboxgroup
* inputdate
* inputdatetime
* inputnumber
* inputtime
* paginator
* fix bug opslaan settings port printer
* tabs



## 21 mei 2025:
 * 21 may 2025 dr-db-filters.js better prepared for custom html element
 * 21 may 2025 dr-db-filters.js when stringfilter search it now gives searched string as chiptext
 * 21 may 2025 all dr-* webcomponents have a attachEventlisters() when abortcontroller was aborted
* 21 may 2025 combobox: zoeken niet hoofdletter gevoelig
* 21 may 2025 dr-input-combobox.js componenten vanuit constructor verplaatsen naar connectedCallback()
* 21 may 2025 filter: html elements werkt semi



## 20 mei 2025:
* 20 may 2025 dr-input-combobox.js zoeken mogelijk zonder selectie te verwijderen


## 19 mei 2025:
* ticketprinter



## 16 mei 2025: dr-input-combobox, artikelen, database wijzigingen
* 16  may 2025: TSysModel: added setFieldDefaultTCurrency() +  setFieldDefaultTDateTime functions
* 16  may 2025: database wijzigingen products and skus
* 16  may 2025: combobox: zoeken werkt nu
* 16  may 2025: combobox: zoeken werkt nu beter
* 16  may 2025: combobox: focussen werkt nu en cyclen waardes
* 16 mei 2025 dr-checkbox.js focussable en met space en enter de waarde te veranderen
* 16  may 2025: combobox: betere event listeners
* 16  may 2025: combobox: bugfix: na zoeken waren evenlisteners children pleite
* 16  may 2025: combobox: multiselect werkt beter
* nog kijken naar multi select: als zoeken: vervangt de volledige selectie als je checkbox checkt



## 15 mei 2025: dr-input-combobox, artikelen
 * 15 may 2025 dr-infobubble.js auto min width based on anchor
 * start gemaakt met dr-input-combobox
 * weergegeven items werkt nu na klik knop
 * verder met dr-input-combobox 2
 * verder met dr-input-combobox 3: multiselect mode werkt!
 * verder met dr-input-combobox 4: focussen werkt, zoekvak toegevoegd
 * 15 may 2025 dr-infobubble.js dispatches events when bubble is hiding and showing 
  * verder met dr-input-combobox 5: nu bezig met zoeken


## 14 mei 2025: artikelen
 * 14 may 2025 - TSysWebsites compatibility CMS2 eruit gehaald
 * 14 may 2025 - TCRUDListControllerAJAX defineDBQuery() in child klassen aangepast
 * 14 may 2025 - TCRUDListControllerAJAX defineDBQuery() in child klassen aangepast
* product overzicht werkt enigsinds
* begonnen met filter voor combobox, zodat language erin kan. 
      dit geeft nog problemen omdat JSON als html attribuut is meegegeven die "" filtert (en dus geen geldige json is)
      gebleven in dr-db-filters.js: renderFilterCombobox()


## 13 mei 2025: artikelen
* 13 may 2025: TSysModel: added setFieldDefault...() fields for varchar integer and boolean for fast defining of fields
* wijzigingen PRoductSkus and products
* rename function TsysModel updateDBTable($iFromVersion) ==> refactorDBTable
* rename function TsysModel PUBLIC refactorDBTable($iFromVersion) ==> PROTECTED
* url router updated, so it will accept 'install' even when database tables don't exist
* producten, skus en productvertalingen kunnen aangemaakt worden
* NEXT: verder nu met overzicht maken producten


## 9 mei 2025: artikelen
* TProductSKUs created
* VAT classes has extra field: is default
* 9 may 2025: HTMLTag: ADD: getTextContent();
* FIX: vatclasses-detailsave: percentages werden niet goed opgeslagen
* FIX: landen buiten de EU wordt niet gevraagd om INTRA EU percentage
* ADD: syscountries: added unknown country
* CHANGED: JS webcomponents. All dispatched events of input fields are called "dr-input-changed"
* start gemaakt met TProducts (setters en getters gemaakt, de rest moet nog)


## 8 mei 2025: rewrite DRInputNumber
* rewriting DRInputNumber ==> werkt nu
* DRInputNumber werkt: add thousand separator in #convertValueToString()
* DRInputNumber init value in html tag can not exceed min or max

* @todo php db filters: joined fields quicksearch werkt nog niet



## 7 mei 2025: rewrite DRInputNumber
* rewriting DRInputNumber ==> naar 1 integer met decimal precision ipv 2 integers
* @todo: boundaries(), updateUI(), min, max

* @todo php db filters: joined fields quicksearch werkt nog niet


## 2 mei 2025: kassa
* Fixed 2 bugs: pagebuilder: preventing from saving
* 'Exit' button icon: pijltje nu naar links ipv rechts
* bugfix: rechtendingetje: volgorde(=order) werkt niet bij vatclasses
* vatclasses detailscherm werkt nu


## 1 mei 2025: kassa
* modellistajax template: visual separate section for bulk actions
* vat classes list works
* translated errors verwijderd in verschillende schermen. (update van een paar maanden geleden niet goed uitgevoerd)
* vatclasses detailscherm werkt nu
* dr-icon-spinner toegepast op save button bij saven record (vervangt oude spinner actie)
* dr-icon-spinner toegepast op exit button bij exit pagina
* 1 may 2025: DRInputDateTime getValueAsTDateTime() returned nothing
* 1 may 2025: DRInputNumber created
* het is mogelijk om save notification uit te schakelen
* commandpanels oude savedetails aan bovenkant scherm


## 30 april 2025: kassaaaaaa!
* default wordt nu een gebruiker aangemaakt met random username, wachtwoord en loginenabled false. By default kun je dus niet meer inloggen.(je weet gebruikersnaam en wachtwoord niet en inloggen is uitgeschakeld)
* Bij installatie kan gebruikersnaam en wachtwoord gewijzigd worden en wordt loginenabled op true gezet.
* "quote" toegevoegd bij default transaction types
* 30 apr 2024: TSysCountries: rename FIELD_ISEUROPEANUNION ==> FIELD_ISEEA
* step3: installer: button ipv link
* VAT en VATCountries. models aangemaakt -> geeft nog bij bij installeren op defineTable() => vatclaSSID



## 29 april 2025: misc
* 4 image grootten in alle TModel klassen: thumbnail, medium, large, max
* 4 image grootten tot 1 getTableUseImageFile() function reduced in alle childklassen
* image sizes in config scherm in te stellen

## 26 april 2025: paginator
* 26 apr 2025: TPaginator: isLastPage() bugfix. gaf max 8 pagina's terug
* 26 apr 2025: user non select next-page/previous-page
* 26 apr 2025 dr-context-menu.js responds to ESC-KEY
* 26 apr 2025 dr-infobubble.js responds to ESC-key
* begin gemaakt aan dr-paginator
* verder gegaan met dr-paginator
* Customevent ipv Event in webcomponents gebruikt bij dispatch events
* paginator werkt nu!
* request url wordt geupdate waardoor filters in paginator wordt meegenomen!!!
* werkt nu: previous / next button disabled bij eerste/laatste pagina
* werkt nu: firstpage / lastpage button disabled bij eerste/laatste pagina
* werkt nu: max 8 paginas weergeven
* betere iconen in paginator
* aanpassingen voor weegave op mobiele telefoon
* paginanummering wordt verwijderd op mobiele telefoon ipv firstpage en lastpage




## 25 april 2025: paginator
* 25 apr 2025 dr-db-filters.js bugfix: quicksearch verwijderen lukte niet, omdat de textinhoud chip gewijzigd werd
* 25 apr 2025 dr-db-filters.js placeholder quicksearch
* 25 apr 2025 dr-db-filters.js bugfix: added translation items
* 25 apr 2025 paginator values worden doorgegeven via JSON
* 25 apr 2025 esthetische verbetering recordlist page ajax
* 25 apr 2025 next page, previouspage werkt in tabel MET BUG PAGE 8
* 25 apr 2025 bugfix: TR was missing in table for buttons next, previous


## 24 april 2025: db filters
* ERD ontwerpen toegevoegd
* 24 apr 2025: TSysModel: some esthetic cleanup
 * 24 apr 2025: TSysModel: added findBetween();
 * 24 apr 2025: TSysModel: added findBetween() werkt nu
 * 24 apr 2025: TDBPreparedStatementMySQL: rename: parseSQL() ==> renderSQL()
 * 24 apr 2025: TDBPreparedStatement: rename: parseSQL() ==> renderSQL()
 * 24 apr 2025: BETWEEN werkt nu voor filters
 * BUGFIX: conversie nummers naar ints was te strikt waardoor er een 0 aan het nummer geplakt werd
* 24 apr 2025 DRDBFilters.js bugfix: eerst werd event ge-dispatched en daarna verwijderd van DOM. hierdoor gebeurde er niks wanneer je een chip verwijderde

## 16 april 2025: 
* description field toegevoegd aand transaction-type detail scherm
* begonnen met createDBQuery() in DRDBFilters
* 16 apr 2025: TSysModel: findLike() deprecated, use find() with COMPARISON_OPERATOR_LIKE instead
* verder gegaan met filters
* bezig met bouwen aan quicksearch in TSysModel()/TDBPreparedStatementMySQL
* bugfix: quicksearch werd niet goed opgepakt
* 16 apr 2025 dr-db-filters.js bugfix: waarde boolean-filter werd niet uit chip gelezen bij laten zien van bubble


## 14 april 2025: json data in table
* json response header
* data kolom op mobiel werd niet weergegeven
* CSS classes zijn nu class constants
* column ordering refresh via json
* bulk actions refreshen via json.
* crudcontroller iets aangepast zodat het logischer is dat je een query definieert in child class
* crudcontroller doet nu een bulkaction en database query in 1 go, wat het sneller maakt
* BUGFIX: selecteren kolommmen werd gereset. crudcontroller volgorde uitvoeren handelingen omgedraaid
* CSS font size iets bijgeschaafd
* description field toegevoegd aan transaction type screen
* 15 apr 2024: lib_types: strToInt() extra parameter to correct invalid integers
* verder met filters gegaan. 


## 13 april 2025: opruimen - JQUERY WEG!!!!!
* DOM class bestanden verwijderd die met date en time te maken haddden
* JQuery en alle verwijzingen naar JQuery verwijderd!!!!!!


## 12 april 2025: json data in table
* classname toegevoegd aan JSON object
* checkbox, order, new button, edit icon toegevoegd aan tabel via json
* 12 apr 2025 lib_inet.js bugfix: addVariableToURL(). first part of url was repeated
* order json table met datbase koppelen
* oplossing adds/subtract avail stock + adds/subtract reserved stock + adds/subtract financial


## 11 april 2025: json data in table
* data wordt nu opgeroepen uit database via json.
* next step: classname toevoegen

* @todo checkbox, order, new button, edit icon
* @todo filters met database koppelen
* @todo column ordering refresh via json


## 6 april 2025: systeem onderhoud
* translations php DRInputDateTime()
* tags voor webcomponents in apart directory
* <dr-input-datetime> translations worden nu goed gepusht naar datum en tijd boxen
* <dr-input-datetime> getest en werkt. nu gaan toepassen
* <dr-input-datetime> problemen van lege boxen opgelost
* <dr-input-datetime> first day of the week wordt nu doorgegeven
* <dr-input-datetime> alloweptydate werkt nu (nog php bug met empty datum)
* forminput abstract: getValueSubmittedAsTDateTimeISO() extra beveiliging dat altijd een datum object wordt terug gegeven ipv null
* users detail: nieuwe datum boxen
* organizations detail: nieuwe datum boxen
* invite codes detail: nieuwe datum boxen
* blacklist detail: nieuwe datum boxen
* pagebuilder heeft nu alle webcomponenten
* pagebuilder heeft nu datum componenten

* @TODO: Jquery verwijderen!!!!!!@!

## 5 april 2025: systeem onderhoud
* verplaatsen klassen stap 1. ongebruikte klassen naar __unused
* cms2 zaken verplaatst naar __cms2
* TSysContacts -> module
* TPageBuilderWebpages -> module
* activelanguagesper side => module
* filterRecursiveDirectoryTraversal() outside filterDirectoryTraversal() want gaf fout bij 2x aanroepen
* syscountries => module
* syssettings -> module
* TSysLanguages -> module
* 5 apr 2025: lib_string: IMPROVED: startswith() and endswith() uses str_ends_with and str_starts_with
* TSysCurrencies -> module
* TSysWebsites -> module
* bugfixes: TSysWebsites -> module: 
* pagebuilder statusses => module
* pagebuilder documentsabstract  => module
* rename mod_bobpagebuilder => mod_pagebuilder
* TSysModulesCategories => module
* use dr\classes\models\TSysModules; ==> use dr\modules\Mod_Sys_Modules\models\TSysModules;
* use dr\classes\models\TSysCMSInvitationCodes; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSInvitationCodes;
* use dr\classes\models\TSysCMSLoginIPBlackWhitelist; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
* use dr\classes\models\TSysCMSPermissionsCountries; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
* use dr\classes\models\TSysCMSOrganizations; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
* BUGFIXES: use dr\classes\models\TSysCMSOrganizations; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
* use dr\classes\models\TSysCMSUsersRolesAssignUsers; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRolesAssignUsers;
* use dr\classes\models\TSysCMSUsersRoles; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
* use use dr\classes\models\TSysCMSPermissions; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions;
* use dr\classes\models\TSysCMSUsersHistory; ==>use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersHistory;
* use dr\classes\models\TSysCMSUsersSessions; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
* use dr\classes\models\TSysCMSUsersFloodDetect; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect;
* use dr\classes\models\TSysCMSUsers; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;
* php klasse DRInputDateTime aangemaakt
 * <dr-input-datetime> clock wordt gererenderd waardoor de eventlisteners pleitte zijn
  * 5 apr 2025: dr-input-time.js: aparte eventlisteners voor editbox en clock
 * 5 apr 2025: dr-input-time.js: mousewheel update nu uren, minuten en seconden
 * 15 mrt 2025 dr-input-date.js scrollwheel scrollt maanden in kalender

@todo
datum/tijd boxen + jquery eruit
CSV rewrite


problems with finding one of these classes?
* use dr\classes\models\TSysContacts; ==> use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
* dr\classes\models\TPageBuilderWebpages ==> dr\modules\Mod_BobPageBuilder\models\TPageBuilderWebpages
* use dr\classes\models\TSysActiveLanguagesPerSite; ==> use dr\modules\Mod_Sys_Localisation\models\TSysActiveLanguagesPerSite;
* use dr\classes\models\TSysCountries; ==> use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
* use dr\classes\models\TSysSettings; ==> use dr\modules\Mod_Sys_Settings\models\TSysSettings;
* use dr\classes\models\TSysLanguages; ==> use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
* use dr\classes\models\TSysCurrencies; ==> use dr\modules\Mod_Sys_Localisation\models\TSysCurrencies;
* use dr\classes\models\TSysWebsites; ==> use dr\modules\Mod_Sys_Localisation\models\TSysWebsites;
* use dr\classes\models\TPageBuilderDocumentsStatusses; ==> use dr\modules\Mod_BobPageBuilder\models\TPageBuilderDocumentsStatusses;
* use dr\classes\models\TPageBuilderDocumentsAbstract; ==> use dr\modules\Mod_BobPageBuilder\models\TPageBuilderDocumentsAbstract;
* RENAMED Mod_BobPageBuilder ==> Mod_PageBuilder
* use dr\classes\models\TSysModulesCategories; ==> use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;
* use dr\classes\models\TSysModules; ==> use dr\modules\Mod_Sys_Modules\models\TSysModules;
* use dr\classes\models\TSysCMSInvitationCodes; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSInvitationCodes;
* use dr\classes\models\TSysCMSLoginIPBlackWhitelist; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSLoginIPBlackWhitelist;
* use dr\classes\models\TSysCMSPermissionsCountries; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissionsCountries;
* use dr\classes\models\TSysCMSOrganizations; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSOrganizations;
* use dr\classes\models\TSysCMSUsersRolesAssignUsers; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRolesAssignUsers;
* use dr\classes\models\TSysCMSUsersRoles; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
* use use dr\classes\models\TSysCMSPermissions; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSPermissions;
* use dr\classes\models\TSysCMSUsersHistory; ==>use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersHistory;
* use dr\classes\models\TSysCMSUsersSessions; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersSessions;
* use dr\classes\models\TSysCMSUsersFloodDetect; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersFloodDetect;
* use dr\classes\models\TSysCMSUsers; ==> use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsers;


## 4 april 2025
* <dr-input-*> strToBool() when null it defaults to false instead of true. fixes bug in <dr-db-filters> when removing "disabled" attribute from <dr-input-number> and <dr-input-date> 
* transaction types renamed: current stock => available stock
* <dr-input-checkbox> visualstyle property renamed => type
* <dr-input-number> visualstyle property renamed => type
* <dr-db-filters> translations done
* <dr-icon-spinner> created for spinning icons.
* veel gedaan aan de php kant voor de filters.
* @todo: next step: post json voor filter.



## 3 april 2025
* 3 april 2025 dr-input-number.js when min and max == 0 dan was de plus en min icon disabled op ongewenste momenten
* 3 april 2025 dr-input-number.js filters thousand separator
* <dr-db-filters> string filter werkt (client-side)
* <dr-db-filters> json export voor string filter werkt
* <dr-db-filters> number filter werkt (client-side)
* <dr-db-filters> json export updates
* <dr-db-filters> quicksearch bubble en filter werkt nu
* <dr-db-filters> date bubble en filter werkt nu
* <dr-db-filters> "enabled" is nu definitief weg en vervangen door "disabled"
* andere componenten hebben nu ook ondersteuning voor "disabled" attribuut
* kleine bugfixes

* <dr-db-filters> bug: als bubble verlaat (nummer + datum) en between is selected dan is veld disabled


## 1 apr 2025:
* <dr-input-number> trashcan icoon vervangen

## 30 mrt 2025: 
* <dr-input-number> created en afgemaakt. HOPPAAAAAA!

## 29 mrt 2025: <dr-db-filters>
* <dr-input-checkbox-group> BUGFIX: de correctUserInput() werkt nu
* <dr-info-bubble> BUGFIX: als updateUI() aangeroepen werd de title overschreven incl close-icoon. Nu verholpen

## 27 mrt 2025: <dr-db-filters>
* bubble X icon toegevoegd + attributes voor x icoon
* bugfix: <dr-input-time> + <dr-input-date> anchorobject ipv anchor waardoor anchor niet gevonden
* <dr-input-checkbox> label value is nu intern als innertext
* <dr-info-bubble> heeft nu ruimte voor sluit icoon
* <dr-db-filters> de bubble closen werkte niet
* <dr-info-bubble> nu title in bubble
* <dr-db-filters> CSS styling filter bubbles
* <dr-db-filters> string en boolean ziet er nu veel beter uit
* <dr-db-filters> string filter comparison operators toegevoegd
* <dr-input-checkbox> heeft nu een visualstyle: checkbox en radio

## 26 mrt 2025: <dr-db-filters>
* <dr-db-filters> verwijderen en disablen werkt op JS niveua
* <dr-db-filters> css verbeterd
* <dr-db-filters> bubble wordt nu getoond bij klikken op text
* todo: enabled switch in bubble
* todo: in bubble de filter in kunnen stellen

## 26 mrt 2025: <dr-db-filters>
* web components: bezig css en functienamen bijwerken nieuwe standaard
* <dr-db-filters> css naar intern. 
* <dr-db-filters> nieuwe functienamen
* <dr-db-filters> nu gestart met menu toevoegen aan "new filter"
nieuwe poging om menu in shadowdom te doen
* <dr-context-menu> alle CSS en logica etc werkt nu intern in de klasse!
* <dr-context-menu> bugfix: set anchorpos(
* <dr-db-filters> bezig met callback function
* <dr-db-filters> filters worden nu toegevoegd na geklikt te zijn in addfilter menu

## 25 mrt 2025:
* <dr-input-time> minder vatbaar voor foutieve invoer
* <dr-input-datetime> created
* <dr-input-time> + <dr-input-date> separate event dispatch functions
* <dr-input-datetime> + <dr-input-time> + <dr-input-date> allowemptytime en emptydate werkt. geeft geen null errors meer
* <dr-input-datetime> waarde opvragen werkt
* <dr-input-datetime> allow empty date == false automatisch vult een datum in voor de gebruiker onfocusout
* <dr-input-time> + <dr-input-date> betere ondervanging slecht datums en tijd
* parseIntBetter() is now a lib_types.js function
* <dr-input-time> + <dr-input-date> corrections on date and time also when allowemptydate/time == false
*  <dr-input-time> BUGFIX: 12:40AM is translated to 12:40h (24h notation), 12:40AM = 0:40h in 24h notation). The picker works correctly, the editbox NOT! 
* <dr-input-time> + <dr-input-date> Scrollwheel mouse telt minuten erbij/eraf en dagen erbij/eraf
* <dr-input-time> BUGFIX: kon niet meer terug naar AM als PM gekozen in klok
* <dr-input-datetime> content divider zodat tijd en datum gelijk verdeeld worden
* <dr-input-datetime> het setten van een value werkt nu in attribute
* <dr-input-time> + <dr-input-date> in  <dr-input-datetime> BUGFIX: had nog problemen met maxlenth en placeholder
* <dr-input-time> + <dr-input-date> and <dr-input-datetime> translations "days", "hours" nu in attributen
* <dr-input-time> + <dr-input-date> BUGFIX: placeholder + maxlength werd niet geupdate in updateUI()
 


## 23 mrt 2025: 
* <dr-input-time> bezig met dynamische klok maken gebaseerd op php formaat
* <dr-input-time> dynamische kolommen genereren werkt nu
* <dr-input-time> event listeners toegevoegd
* <dr-input-time> dispatch event werkt nu
* <dr-input-time> am/pm bug eruit
* <dr-input-time> 12AM bug
* <dr-input-time> attribute value werkt nu
* <dr-input-time> typen in textbox update de timepicker
* <dr-input-time> werkt nu!
* <dr-input-date> typen in textbox update de datepicker


## 22 mrt 2025:
* <dr-input-time> created. 
* <dr-input-date> heeft nu een kalender icoon ipv focus of 1x klik
* <dr-input-date> css aangepast zodat je de breedte van buitenaf kunt instellen
* <dr-input-date> dubbelklik oproepen bubble
* <dr-input-date> kalendericoon verbeteringen css
* <dr-input-time> clock icoon
* <dr-input-time> nu bezig met bubble maken : dynamisch genereren kolommen gebaseerd op phpformat

## 21 mrt 2025: 
* <dr-info-icon> created
* <dr-input-date> in principe klaar. 
tijd box nog doen.
combi datum en tijdbox

## 20 mrt 2025: <dr-input-checkbox><dr-input-checkbox-group>
* created <dr-input-checkbox><dr-input-checkbox-group>
* bugfix: form werd niet gesubmit, ook als geen fout

## 18 mrt 2025: <dr-input-date> 
* <dr-input-date> 1 box ipv 3
* <dr-input-date> on focus out corrigeert invoer
* <dr-input-date> bug in attributeToBool weg
* <dr-input-date> leeghalen editbox en submit maakt formvalue ook leeg
* <dr-input-date> up and down arrows om dag eerder en later te selecteren
* <dr-input-date> calendar showt on focus
* <dr-info-bubble> bugfix: attachEventListeners
* <dr-input-date> kalender knoppen werken, behalve de dagen zelf



## 17 mrt 2025: <dr-input-date> 
* <dr-input-date> bug eruit dat datum in form niet goed was (bij setten php datum geset ipv iso datum)
* <dr-input-date> tab mag weer
* <dr-input-date> ctrlv invalid input gefilterd
* <dr-input-date> beter ondersteuning als geen datum ingevuld in attribute (niet meer 0px)
* 

## 16 mrt 2025: <dr-input-date> 
* <dr-input-date> getValueSeparatorsPHPFormat():  bugfix laatste separator werd niet toegevoegd
* <dr-input-date> setInternalDateAsPHPDate() afgemaaklt
* <dr-input-date> invoer van datum werkt nu helemaal goed.
* <dr-input-date> verder gegaan
* <dr-input-date>  ////=====> gebleven bij checkValue()


## 15 mrt 2025:
* <dr-input-date> begin gemaakt. gebleven bij setInternalDateAsPHPDate()

## 14 maart 2025:
* dr-db-filters web component start gemaakt
* TODO: bubble toevoegen
* TODO: remove actie uitvoeren
* TODO: apply actie uitvoeren + icoon doorzichtig maken

## 13 maart 2025:
* andere dir indeling web components. nu CSS apart weer
* uitbreidingen dr-context-menu: mogelijk om auto uit DOM te verwijderen
* uitbreidingen dr-info-bubble: mogelijk om auto uit DOM te verwijderen

## 12 maart 2025:
* verschillende default useraccounts verwijderd
* rename user accounts => organization. Dat is veel duidelijker. User accounts kan verwarrend zijn met logins
* alle templates zijn nu verplaatst naar de webcomponents intern. cleaner geheel en meer performance want er hoeven geen extra bestanden geladen te worden
* nu beginnen: dr-context-menu als web component (oude nog even weggequote)
* dr-context-menu omgezet naar web component


## 11 maart 2025: 
* DRContectMenu: scrollbar breedte/hoogte wordt meegenomen in overwegingen waar weer te geven
* gestylde context menu
* dr-info-bubble component toegevoegd en gestyled
* dr-info-bubble component bugs eruit met klikken waardoor deze verschijnt en gelijk weer verdwijnt
* dr-info-bubble component klaar


## 9 maart 2025: context menu
* DRContextMenu gemaakt

## 8 mart 2025: recordlist ajax
* <dr-tabs> component gemaakt

## 7 mart 2025: recordlist ajax
* renames CSS vars naar consistente benaming, waaronder: --lightmode-icon-color
* filters visueel klaar chips ontwerp
* betere visuele representatie chips
* lettergroottes chips ahv REM ipv PX
* chips worden nu dynamisch gecreeerd
* quicksearch wordt nu toegevoegd
* eventlisteners toegevoegd en verwijderd chips

## 6 mrt 2025:
* language detail screen beter omschrijving bij checkboxen
* postcodes niet meer encrypted bij contacten
* in installatieproces wordt nu een gebruiker aangemaakt, ipv standaard username+pass: root root
* loginform had nog oude validators waardoor niet goede zaken gecheckt werden
* FIX filterbadcharsliteral() had bug
* postalcode is not encrypted anymore
* postalcode is now searchable
* installer: prevents empty username + passwords
* installer: messages are colored red or green
* ajaxlist: sortorder errors gone
* ajaxlist: spinner icon showing when click on edit
* dialogs hebben grotere knoppen
* dialogs hebben nu titles
* fix: bugje list: contacts


## 5 mrt 2025: 
* css iconchangefill etc nu hard coded kleur, omdat niet alle tags css variabelen onderstuenen
* icoon order vervangen
* icoon order header vervangen
* icoon edit vervangen
* templates uit elkaar getrokken voor listcontroller (aparte template voor tablebody en pagina)


## 28 feb 2025: 
* CMS5 heet nu Archimedes
* Archimedes nu te installeren via /admindr/install
* transaction types: reserve voorraad kan nu bijgehouden worden
* module categorie POS toegevoegd tijdens installatie
* detail transactiontypes: now ajax
* color type and hex type toegevoegd
* color type nu in list overzicht weergegeven
* list: booleans worden nu weergegeven als checks
* bugfix: checkboxes and radio boxes checked werden altijd als checked opgeslagen. Had te maken met TCRUDListControllerAJAX->ajaxform.js save() die waardes ophaalde ipv "checked"
* FIX: form: Cross site Request forgery protection zat niet in ajaxform.js en ajaxformcontroller
* FIX: form: flood protection zat niet in ajaxformcontroller
* begin gemaakt met ajaxlist controller. nog afmaken

## 7 feb 2025: fileuploadmanager
* dev doc
* json file list terug geven werkt nu
* ook check op access module
* FIX: bug in checkModule()
* FIX bug: uploadmanager wordt 2x uitgevoerd met param filelist=1
* IMPORTANT bug fix: internal server error bij SQL error ($this werd doorgegeven ipv gebruikersnaam)
 * 07 jan 2025: TDBPreparedStatementMySQL: updateModel(): IMPORTANT BUGFIX  field value wasn't FILTERED!
 * 07 jan 2025: TDBPreparedStatementMySQL: updateModel(): support for autoincrement field
 * 07 jan 2025: pagebuilder: data getrimd
 * 07 jan 2025: AJAX controller Fetch terug gezet
 * files worden nu weergegeven in de fileuploadmanager. Nog veel werk te doen

## 5 feb 2025:
* FIX: nieuwe docs werden iedere keer opnieuw opgeslagen met een nieuw id wanneer op save-knop geklikt
* consistency config file GLOBAL_GOOGLE_RECAPTCHAV3_USE -> GLOBAL_GOOGLE_RECAPTCHAV3_ENABLE, zelfde voor V2
* API key added to CMS users
* probeersel om flikkeren in pagebuilder tegen te gaan. mislukt


## 1 feb 2025:
* zebraprint extension in ___installer dir
* zpl tests
* global json header in lib_sys_typedef
* 2 feb 2025: lib_file: isDirectoryTraversal() updated for empty param
* basics van het teruggeven van JSON in uploadfilemanager werken
* log dir traversal in security floodlog
* fix: verhuizing json header nog niet aangepast in pagebuilder
* note field in security floodlog
* installer heeft 4e stap voor data propagation
* verder gegaan met fileuploadmanager json filelist

## 31 jan 2025: voor edwin
* zebraprint.js added
* escpos 3rd party component added
* 31 jan 2025: lib_sys_framework: autoLoaderVendor() added --> laadt 3rd party components in vendor dir automatisch
* bootstrap: autoload registers new autoLoaderVendor() function
* function getValueSubmitted() in InputTime() aangepast naar nieuwe methode

## 30 jan 2025: FLINKE BEVEILIGINGSUPDATES!!!
* directory traversal methods added to lib_sys_file:
    * 30 janv 2025: lib_file: FIX: filterDirectoryTraversal() support for recursion
    * 30 janv 2025: lib_file: isDirectoryTraversal() added
    * 30 janv 2025: lib_file: concatDirFileSafe() added
* 30 jan 2025: lib_sys_inet: purifyHTML() function added
* 30 jan 2025: HTMLTag: several functions added
* 30 jan 2024: FormInputAbstract: getAllowHTML() verwijderd, parameter van getValueSubmitted() vervangt dit
* 30 jan 2024: FormInputAbstract: getValueSubmitted() parameter voor filtering. defaults to escaping via htmlspecialchars()
 * 30 jan 2025: HTMLTag: FIX: eerste karakter input was foetsie in render
 * 30 jan 2025: HTMLTag: FIX: wanneer alleen text (geen nodes), dan is return van de render leeg. dit wordt nu gedetecteerd en als textnode toegevoegd
 * ___prefix voor directories te verwijderen na installatie
 * ratelimiter is te enablen in config file
 * update dev doc


## 29 jan 2025: upload file manager
* verder gegaan layout uploadfilemanager 3 - stylen preview + icons darkmode

## 28 jan 2025: upload file manager
* verder gegaan layout uploadfilemanager 1
* verder gegaan layout uploadfilemanager 2 - het begint al op iets te lijken

## 25 jan 2025: pagebuilder
* FIX: CMS: ondervangen dat niet CMS DB niet geinstalleerd is
* DOgrid heeft nu een grid inside het element ipv een apart element
* aparte uploadfilemanager.css and uploadfilemanager.js en dialog verhuisd naar block_uploadfilemanager
* begin gemaakt met layout maken van uploadloadfilemanager met grid. NOG AFMAKEN!

## 24 jan 2025: pagebuilder
* FIX: TCRUDSaveControllerAJAX -> create gaf error met ID checkout
* FIX: dubbele js removeAllInputErrors() function waardoor errors niet gewist werden bij opslaan
* FIX: problemen met opslaan in pagebuilder, miste paar velden
* FIX: lib_inet.js: addVariableToURL() accepteert geen dubbele variabelen meer
* FIX: lib_types.js: strToInt() isInt() added
* Pagebuilder preview werkt nu
* FIX: pagebuilder preview werkt nu bij nieuw document
* pagebuilder: checkin werkt nu bij nieuw document
* pagebuilder: de custom designobject worden nu door de controller terug gegeven
* pagebuilder: de form-line classen bij divs aangemaakt zodat fouten op standaard manier worden weergegeven (had al eerder in versiedoc vandaag gemoeten)
* bugfix: transm() zat in js library, die geladen werd buiten modules
* alle TCRUDSaveControllerAJAX child controllers hebben nu een save functie in dialog

## 22 jan 2025: pagebuilder
* converteren tussen text objecten werkt nu
* toevoegen door dubbelklik werkt nu
* drag drop vanaf create tab werkt nu
* onFocusOut in selection-mode zet editable weer op false na klik
* design elements standaard: display: block in css
* grid en columns gaven fout bij createn, werkt nu ook
* removeCSSClassAcceptDraggingFromDroppables gaf fout bij draggen in structure tab
* @TODO bezig preview: html cleanup werkt nog niet goed

## 21 jan 2025: pagebuilder
* eerste stap naar omzetting web components

## 19 jan 2025: CRUD detailsave ajax controller in pagebuilder
* pagebuilder volledig omgezet naar CRUD detailsave ajax controller
* checking out werkt nu
* todo: foutmeldingen van velden weergeven
* todo": kijken naar bDirtyDocument

## 17 jan 2025: CRUD detailsave ajax controller
 * controller uit elkaar getrokken en onderverdeeld in TCRUDdetailSaveAjax en TAJAXFormController
 *  TAJAXFormController v2
 * heel veel validators herschreven en gecontroleerd , sommige verwijderd
 * class InputContents removed
 * validators hebben nu constructors
 * sommige validator constructors hebben nu een parameter (en interne variable) voor ignoreEmpty
 * validators foutmeldingen niet meer als parameter meegeven, validators genereren nu zelf foutmeldingen
 * PARAMETERS VOOR FOUTMELDINGEN ZIJN VERWIJDERD, DIT KAN FOUTEN GEVEN
 * FormInput->getContentsSubmitted() verwijderd, DIT KAN FOUTEN GEVEN
 * InputAbstract->getContentsInit removed
 * alle verwijzingen naar getContentsSubmitted() verwijderd en naar equivalenten gerenamed
 * bug gefixt: textbox werd endless loop
 * alle detailschermen foutmeldingen voor validators weggehaald

 * @todo nieuwe controller toepassen in pagebuilder
 

## 16 jan 2025: CRUD detailsave ajax controller
* saven werkt nu voor meerdere validators
* onchange werkt nu voor meerdere validators
* bugfix: create gaf error
* dirty exit dialog: kijkt naar dirty en ignored de browser dialog wanneer op exit geklikt
* nu eindelijk user krijgt melding dat deze weg klikt zonder op te slaan
* check-in na exit
* bugfix: check-in na exit (als NIET dirty, werd niet uitgecheckt)
* rename dialog from pagebuilder -> DSC
* netjes CSS en JS wegwerken zodat deze ook voor de voorkant van een site gebruikt kan worden
 


## 15 jan 2025: CRUD detailsave ajax controller
* saven werkt, maar zijn nog wat bugs
* @todo nog afmaken languages nieuwe controller


## 14 jan 2025: CRUD detailsave ajax controller
* save function gemaakt, nog testen



## 13 jan 2025: CRUD detailsave ajax controller
* CRUD-AJAX controller
* rename $sURLThisScript ==> GLOBAL_URLTHISSCRIPT


## 12 jan 2025: pagebuilder / upload file manager
* cms_dialogs.js: eventlisteners worden weggehaald na click
* door import dialogs.js deed exit dialog niet meer (dubbele id's)
* DOImage toevoegen: doorzichtige SVG op achtergrond, knop op voorgrond

## 10 jan 2025: pagebuilder + upload file manager
* rename variable objCurrentModule => constant CMS_CURRENTMODULE
* de input velden worden nu met validators gecheckt
* er kunnen nu meerdere foutmeldingen terug gegeven worden
* de interne objecten van de pagebuilder worden nu gebruikt voor velden ipv $_POST[] voor viewToModel()
* lege naam, lege html titel, lege url slug worden automatisch gevuld wanneer deze leeg zijn
* extra auth()'s voor verschillende losse velden worden nu niet opgeslagen wanneer je daar geen rechten voor hebt
* rechtensysteem met pagebuilder getest en laatste bugs eruit gehaald
* bulk delete werkt nu ook
* image element DO toegevoegd
* tab: element: sizing properties toegevoegd
* tab: element: border properties toegevoegd
* tab: element: top margin chapter

## 6 jan 2025: upload file manager
* bootstrap: added: path voor models en controllers
* bootstrap_cms_auth: added: filemanager
* module aangemaakt voor filemanager dialog
* start gemaakt met filemanager dialog
* @TODO: auth() aanmaken in render controller
* @TODO: afmaken module + installeren

## 5 jan 2025: pagebuilder
* functie getEventTargetIgnorePlaceholders() weg
* functie getHoverElement() weg
* renames: onDragStart handleDragStart
    onDragEnd handleDragEnd
    onDragOver handleDragging
    onDrop handleDrop
* rename objDesignerContainer => objDivDesignerContainer
* handleDrop() from UIDragDrop geupdate
* drop on designer-add-container kopieert het bestaande object
* placeholders nu gecentreerd en lichtjes doorzichtig
* droppen werkt nu
* BUGFIX: draggen subitems in grid in structure tab nu mogelijk
* de dragging resultaten worden intern opgeslagen, zodat de drop exact hetzelfde resultaat geeft als aangegeven met draggen
* beslissingsmechanisme drop/insert werkt nu beter
* droppables werden niet verwijderd in updateStructureTab
* html object added
* icoontje html object had hoogte en breedte, waardoor deze in structure raar werd weergegeven
* @TODO: dubbele processen voor draggen en droppen voor elk op elkaar liggend event
* @TODO: structure tab drag/drop kijken


## 4 jan 2025: pagebuilder
* de html render is nu clean, zonder CSS classes en ID's van de designer
* iedere DesignObject class kan nu zijn eigen cleanup regelen voor de HTML render
* voorlopig werkt draggen op droptargets, maar dropcursor wordt niet weergegeven
* draggen werkt nu op droppables en dragcursor
* getEventTargetIgnoringPlaceholders() aangemaakt
* getFirstParentDroppable() aangemaakt
* gebleven bij: + pakt ie niet bij dragover

## 3 jan 2025: general
* globals voor de website hebben prefix: WEBSITE_ ipv GLOBAL_
* "theme" variabele toegevoegd aan website config
* "theme" variabele toegevoegd aan framework config
* themes wisselen in instellingen scherm werkt met theme uit framework config file
* alle verwijzingen (incl bootstrap constants) verwijderd naar oude themes methode
* als dir niet bestaat, dan error gelogd
* extra themes even verwijderd om fouten te voorkomen
* extra opties vanuit framekwork config file in settings scherm
  * 3 jan 2025: lib_sys_framework: function getPathConfigWebsite() added
  * configuratie files voor een website wordt geschreven
  * centrale directory voor themes voor website nu in framework config files
  * default directories aangemaakt voor website (uploads,models, views (incl themes),controllers)
  * abstract Config file CRUD controller aangemaakt
  * test settings in pagebuilder module + dev module
  

## 2 jan 2025: pagebuilder
* draggen van new-tab naar designer werkt nu
* draggen onderling in designer werkt nu
* ondrag in parent nog doen
* accept color drag werkt nu
* extra categorien toegevoegd voor design objects
* structure-tab draggen werkt nu
* javascript libraries onderverdeeld in verschillende bestanden
* bugfix: drag+drop van designer naar structure geeft "null" in designer



## 1 jan 2025 : pagebuilder
* verder gegaan met algemene drag en drop klasse
* negeren delete-knop-op-toetsenbord hangt af van cursor mode
* save-bug ctrl-s dialoog venster openen verholpen
* draggen in designer semi-werkt
* BUGFIX desinger items staan twee keer in arrDraggables
* @TODO draggen van new-tab naar designer werkt nog niet
* @TODO structure-tab draggen werkt nog niet


## 30 dec 2024: pagebuilder
* klasse geschreven om draggables en droppables the vereenvoudigen in lib_global_ui_dragdrop.js. 
===> klasse nog testen en toepassen

## 29 dec 2024: pagebuilder
* rename naar constanten uit lib_ui_dragdrop.js
* dev doc zaken toegevoegd
* globale dropcursor div toegevoegd
* rename globale constanten
* rename class designobject-createtab-text
* dragging and dropping from createtab in designer werkt!!!
* onderling draggen en droppen in designer werkt (maar heeft bug)

## 28 dec 2024: pagebuilder
* var iHighestIDDOInDesigner toegevoegd, dit wordt nu gebruikt door getNewIDDesignObjectDesigner();
* bugfix: na klikken tussen 2 items werd element in Designer niet meer geselecteerd. updateDesignerSelected() vervangen door snellere code die wel werkt
* Van niet-text elementen is de tekst niet selecteerbaar met de muis
* JS library created for dragging and dropping
* getDropNearestElement() drop element function works now
* getDropBefore() functie werkt nu

## 22 dec 2024: pagebuilder
* verbouwing: DesignObject JS class heeft createElement() function gekregen die renderOnDropInDesigner() vervangt
* nu de DesignObjects in andere DesignObjects hebben de juiste attributen om als dusdanig herkend te worden
* nu de DesignObjects in andere DesignObjects hebben eventListeners
* addEvenentListeners() verplaatst naar DesignObject class
* kolommen verminderen werkt nu eindelijk
* gap voor grids toegevoegd
* @todo klikken in structure werkt nog niet helemaal goed met grids

## 21 dec 2024: pagebuilder
* TPageBuilderWebpages en pagebuilder classes, zo veel mogelijk naar parent classes geheveld
* settings met abstract functions bepalen nu de extra velden die bij webpages opgeslagen worden
* tab element nu ook op mobiel beschikbaar
* mogelijk om rijen toe te voegen/verwijderen.
* @todo kolommen moet nog
* @todo gewone div ipv DesignObject Div in Grid

## 20 dec 2024: pagebuilder
* grid en container toegevoegd aan library (nu ook zichtbaar in inspector)
* nieuwe iconen voor 2column, 3column, grid en container
* properties: padding heeft nu right en left erbij gekregen
* alle DesignObjects zitten nu in aparte klassen
* margin property toegevoegd op element tab
* property: visibility heeft eigen kopje
* property: delete heeft eigen kopje
* property: move kopje toegevoegd
* property: moving elements

## 19 dec 2024: pagebuilder
* dragging dropping verder gegaan
* steken gebleven bij getClosestElement(): dat gaat nog niet goed.


## 18 dec 2024: pagebuilder
* 1x status published verwijderd (2x published: 1x public, 1x private)
* event listeners van toolbar en tab-elemts worden verwijderd
* event listeners van designobjects-in-designer worden
* event listeners van structure tab worden verwijderd
* fixed: toolbar wordt niet geupdate als item geselecteerd onder structure
* updateToolbarAndDetailsElementTab => updateToolbarAndElementTab
* problemen met eventlisteners verwijderen opgelost
* array structure tab wordt gecheckt op lengte, anders geeft het een fout bij het verwijderen van eventlisteners

* start gemaakt met drag and drop
* https://www.youtube.com/watch?v=jfYWwQrtzzY&t=628s 10:40
* geeft fout bij drop meerdere items wanneer meerdere items geselecteerd

## 17 dec 2024: pagebuilder
*  .dataset.designobjectnameinternal ==> .dataset.designobjectjsclassname  en 
* direct JS designobject.getClassName() gebruiken ipv internalname
* converteren naar andere DOText werkt nu.

## 16 dec 2024: pagebuilder
* 2 column and 3column DesignObjects uitgebreid met een parent grid
* selection mode: editing text werkt. en ook als exit wordt contentEditable op false gezet


## 15 dec 2024: pagebuilder
* display: collapse for iconsheet isn't supported in chrome. replaced for display: none
* verder gegaan met selecteren op basis can cursormode

## 14 dec 2024: pagebuilder
* buttons voor cursor mode
* global vars cursor mode
* nieuw .js lib voor header. bestaande zaken uit viewports.js van header overgezet
* viewports.js => designer.js
* viewports.css => designer.css
* hernoemen kolommen header
* herdefieren header lengtes+kolommen op verschillende resoluties
* knoppen voor wisselen cursor modes werkt nu (functionaliteit nog niet)
* viewports op dezelfde manier oplossen als cursormode met globale variabele ==> BESLOTEN NIET TE DOEN
* cleanup

## 12 dec 2024: pagebuilder
* objDesignerContainer als globaal object gedefinieerd
* sCSSClassDesignObject als globale string gedefinieerd
* shift select werkt nu met parent
* bugfixes objDesignerContainer werd aangeroepen met local var dataContainer
* er wordt geen div-wrapper meer gebruikt voor Design Objects
* meer bugfixes objDesignerContainer werd aangeroepen met local var dataContainer
* nieuwe designobjects: h1 - h6
* nieuwe iconen voor designobjects: h1 - h6 + paragraph
* preview functie heeft nu aparte functie in pagebuilder controller
* preview functie neemt stylesheet mee


## 8 dec 2024: pagebuilder
* selecteren in designer werkt nu voor geneste items
* problemen met selecteren in structure-tab verholpen dat ie de parent element in de designer selecteert
* todo: move-tool and edit-tool

## 5 dec 2024: config files
* aparte config file voor framework en website
* installer aangepast nieuwe config files
* dev doc
* grote verbouwing in database klassen voor support van ip addressen via hexadecimale manier
* getest op dev.dexxclark.com ip addressen en het werkt!

## 4 dec 2024: pagebuilder
* structure tab werkt nu (op root niveau)
* structure tab werkt nu met tree min of meer (nog naar kijken)

## 29 nov 2024
* user agent toegevoegd in floodlogs
* bij usersessie useragent vastleggen
* bij users loginhistorie vastleggen met fingerprint
* loginhistorie wordt weergegeven in gebruiker detailscherm
* loginhistorie wordt na 3 jaar automatisch verwijderd met cronjob
* loginhistorie => user history. dus algemener dan alleen login history
@todo username change nog loggen, session verwijderd loggen, role change loggen, account change loggen

## 24 nov 2024 - config
* v3 van de config files: ipv 2 aparte config files, is het nu weer 1 die per host anders is en deze bevat nu alle config vars

## 22 nov 2024 - pagebuilder
* statussus hebben nu een unlisted en public boolean
* visibleonsite check verwijderd ivm status die dit vervangt
* statussus unlisted en public boolean weer verwijderd
* visibility  private, unlisted, public toegevoegd
* password toegevoegd
* date published toegevoegd
* colommen toegevoegd pages list

## 21 nov 2024 - installation + DB object connect
* grootschalige verbouwing voor de installer!!
* maar 
* DATABASE CONNECTION WORDT ALLEEN GEINITIEERD WANNEER DEZE NODIG IS
* voor gecachete pagina's is er dus geen database connectie meer nodig!!! WHOHOOO!!!
* installer nu in 3 stappen
* installer getest en laatste fouten eruit gehaald
* waardes uit config files lezen en weergeven is eruit gehaald ivm iedereen kan wachtwoorden zien van bestaande config files

## 20 nov 2024 - installation
* nieuwe gebruiksvriendelijke installatie procedure
* test database connection werkt nu 
* creates database on install


## 19 nov 2024 - config file
* 19 nov 2024: lib_file: getParentDirectory() implemented not hardcoded directory separator
* 19 nov 2024: config file is opgesplitst in 2 config files met php constanten: 1 per host, 1 voor CMS&framework
* 19 nov 2024: installatie procedure is nu in 2 stappen: 1e voor createn van config files TODO ingeven DB waarden, 2e voor het createn van de database
* in principe werkt nu met nieuwe config, behalve compatibilitiet CMS2


## 18 nov 2024 - config file 
* TConfigFile(); nieuwe klasse voor het lezen, schrijven en checken van config files. dit verhelpt hopelijk de eindeloze checks in de bootstrap files
* nieuwe soort config file geintegreerd
* @TODO: alle bestaande waardes overzetten nieuwe soort config file
* @TODO: install geeft OOK fout als config niet bestaat


## 15 nov 2024 - blacklist & whitelist
* 15 nov 2024: TLoginFormControllerAbstract: getNewLoginIPBlackWhitelistObject toegevoegd
* direction fouten verholpen bij not allowed
* show404() toegevoegd in lib_CMS
* showAccessDenied() gewijzigd in lib_cms
* stealth mode geimplementeerd. deze hides existing pages behind a 404 header
* op iedere pagina check blacklist
* de accessdenied op loginpage zelf weggehaald (overige pagina's is blijven staan) om attacker geen aanwijzingen te geven dat zijn IP-address is geblokkeerd
* rename libsysinet: getIPAddress() => getIPAddressClient();
* 15 nov 2024: TAuthenticationSystemAbstract(): authenticate() renamed naar authenticateUser() om verschil te maken tussen users en ip-adres authenticatie
 * 15 nov 2024: TSysModel: getIPAddr() renamed to getAsIPAddressBin() to make EXTRA clear that you get a type and not a field-name
 * 15 nov 2024: TSysModel: setIPAddr() renamed to setAsIPAddressBin() to make EXTRA clear that you set a type and not a field-name
 * i_id, i_volgorde renamed naar iID en iOrder wat compatibilitiet met CMS2 breekt
* TLoginFormControllerAbstract: handleLogOut(): Flooddetect based on ip address --> removeFromDB() verwijderd om veiligheid te verhogen
* TLoginFormControllerAbstract: handleLogOut(): Flooddetect based on username --> removeFromDB() verwijderd om veiligheid te verhogen
 * 15 nov 2024: TSysModel: getInt() deprecated in favor of getAsInt() like it was in the old days
 * 15 nov 2024: TSysUsersFloodDetectAbstract: FIELD_USERNAMEHASHEDENCRYPTED renamed to FIELD_USERNAMEHASHED om duidelijker verschil te maken tussen de hashed en nog-te-implementeren encrypted username 
 * 15 nov 2024: TSysUsersFloodDetectAbstract: velden en functies gerenamed die met encrypted genaamd waren, maar in werkelijkheid met hashen te maken hadden
  * 15 nov 2024: TSysUsersFloodDetectAbstract: FIELD_USERNAMEENCRYPTED toegevoegd
  * support voor usernameencrypted ingebouwd: in floodlogs ook de geencrypte username vastleggen, en deze weergeven bij floodlogs in mod_security


## 14 nov 2024 - blacklist & whitelist
* 14 nov 2024: isWhitelistAllowed() checkt nu op checksum
* 14 nov 2024: isWhitelistAllowed() bugfix: 0-datums gingen nog niet goed
* 14 nov 2024: TLoginFormControllerAbstract:handleLogInUsernamePasswordSubmitted() heeft support voor blacklists
 * 14-11-2024 settings_user: ip address bij sessies toegevoegd
  * 14-11-2024: detailsave_users: ip address bij sessies toegevoegd

## 10 nov 2024 - blacklist & whitelist 
* omschrijvingen bovenaan tabsheets toegevoegd
* ip address wordt op installatie aan whitelist toegevoegd
* config file aangepast met nieuwe variabelen voor enablen whitelist
* logica blackwhitelist regels geimplementeerd in TSysIPBlackwhitelistAbstract

## 1 nov 2024 - blacklist & whitelist 
* rename TSysLoginIPBlackWhitelist to TSysCMSLoginIPBlackWhitelist. TSysCMSLoginIPBlackWhitelist 'CMS' ontbreekt
* end date toegevoegd aan blackwhitelist
* translate() test nu op lege parameters
* tijd opslaan werkte niet in bij alle schermen, nu wel :)


## 30 okt 2024 - blacklist & whitelist
* database klasse voor blacklist & whitelist
* schermen voor blacklist en whitelist
* TODO: tijd opslaan werkt niet (alle schermen)
* TODO: rename TSysLoginIPBlackWhitelist ==> TSysCMSLoginIPBlackWhitelist 'CMS' ontbreekt

## 29 okt 2024 - ip address type
* TUsersflooddetect aangepast op nieuwe ip adres type
* TUsersSessions aangepast op nieuwe ip adres type
 * 29 okt 2024 - TCRUDOverviewController executeDB() heeft meer support voor andere id fields dan FIELD_ID: FIELD_RANDOMID en FIELD_UNIQUEID
 * mod_security toegevoegd
 * flood logs zijn nu te bekijken (en te verwijderen) in mod_security
 

## 28 okt 2024 - IP address type
* added type CT_BINARY and CT_IPADDRESSS. This allows us to store IP addresses in database (incl IPv6)
* added support for IP-address type in database classes
* added support ip address in tpl_modellist.php

## 31 aug 2024 - pagebuilder
* wanneer id niet bestond: unexpected errors. nu netjes opgevangen door nieuwe aan te maken.
* checksum gecheckt en geeft fout wanneer niet klopt
* checksum bug met haakjes () die niet alle velden meepakte
* checksum velden uitgebreid
* icoontjes worden nu weergegeven in toolbar bij klikken op text element
* gebleven bij: link inserten (selection wordt gedeselect als in dialog)


## 30 aug 2024 pagebuilder
* toolbar wordt nu geupdate bij klikken
* mogelijk elementen niet visible te maken (uitsluiten van renderen)
* non visible elementen: css bijwerkt zodat de checkbox er goed uit ziet
* 30 aug 2024: HTMLTag: onkeyup added
* naam bovenaan pagebuilder
* naam wordt geupdate bij wijzigen interne titel
* naam bovenaan pagebuilder gecentreerd
 * 31 aug 2024 lib_global_ui.js added: checkedToNumeric
 * 31 aug 2024 lib_global_header.js boolToInt() added
 * 31 aug 2024 lib_global_header.js intToBool() added
* extra velden werken: visibleonsite, needswork, notesinternal


## 10 juni 2024 pagebuilder
* 10 jun 2024 lib_global_header.js removeEnd() added
* detailtab werkt nu! waarden lezen uit designer en in tab zetten + terugschrijven naar designer!!!!!
* delete werkt nu met delete key
* root node designer heeft nu id "designer-root" ipv "designviewport-contentcontainer"
* delete knop in details tab werkt nu!
* grid terug in details tab, ziet er beter uit. maar wel standaard 1 column
* bezig met undo states, undo/redo werkt nog niet lekker

## 9 juni 2024 pagebuilder
* temp fix: traagheidsproblemen kwamen door rateLimiter. 
* DesignObjects can handle clicks and doubleclicks.
* text designobjects are now editable!!!
* detail panel is weer terug onderverdeeld in 2 colommen

## 8 juni 2024 pagebuilder
* FIX: designobjects JS manier nog niet weergegeven in new tab. nicename/internalname probleem
* new array arrDesignObjectsAvailable instead of arrDesignObjectsAll. Now the designobjects are stored by their internal name as key in the array. This allows fast lookups
* searching JS designobjects now works
* 8 jun 2024: TControllerAbstract: late binding variables are indicated with DOUBLE opening/closing brackets instead of SINGLE
* sHTMLTitle en sHTMLMetaDescription nu weer terug latebinding vars
* can go back now in add-panel
* saven werkt nu!!



## 7 juni 2024 pagebuilder
* cronjob: rate limiter files verwijderen na 1 maand
* rate limiter per dag verplaatst naar cms
* nieuwe js library lib_global_ajax.js
* nieuwe functie loadPageInHTMLElement()
* loadPageInHTMLElement() heeft waitinghtml param
* basisklassen opgezet voor volledig JS approach designerobjects (ipv php approach)
* categories and types aangemaakt voor JS approach designerobjects (ipv php approach)
* translation van categorien JS
* categorien nu weergegeven in <select>
* bug: designobjects JS manier nog niet weergegeven in new tab. nicename/internalname probleem


## 6 juni 2024 pagebuilder
* wegschrijven waarden uit tab-detail-element naar objected in designer werkt nu
* 6 jun 2024: HTMLTag: fix: tagname werd niet gesanitized met sanitizeHTMLTagIdName()
* 6 jun 2024: lib_sys_inet: sanitizeFileName() alias
* 6 jun 2024: lib_sys_inet: getIPAddress() nu parameter met replace colon(:)
* betere bescherming tegen ipv6 addressen in bootstrap bij rate limiter
* nu: het lezen van waarden uit designer en in tab-detail-element zetten

## 5 juni 2024 pagebuilder
* detailpanel is nu niet meer in tabel structuur, maar mobile structuur
* ctrl-s werkt nu voor saven
* bezig details weergeven elementen in designer
* selecteren element geeft detailtab element weer. Nog wat bugs gladstrijken
* eenvoudige rate limiter die een site helpt to beschermen tegen DOS-attacks
* 2 rate limiters: 1 per dag + 1 per seconde
* NU: waarden detail tab toepassen op objecten in designer


## 31 mei 2024 dialog / pagebuilder
* replaced div approach for dialog with <dialog>. werkt nu voor system een pagebuilder
* exit confirmation cms is nu een dialog
* 3 dialogs in main cms vervangen: exit, bulk actions, change website
* change website bar on top weg gehaald
* change website pulldown bovenaan geplaatst + gestyled
* canonical veld toevoegen
* rename pagebuilderdocumentabstract: title -> nameInternal
* lettertype detailpanel iets kleiner
* statussen kunnen bewerken (alleen model bestaat nu, nog geen controllers om te bewerken)
* fix: slepen werkte niet ivm array in js die overeenkwam met url


## 30 mei 2024 pagebuilder
 * 30 apr 2024: TSysModel: added orderBy() alias for sort()
 * bezig met opslaan detailspanel. Lukt niet ivm dubbele ids mobiel en desktop-detail panels
 * modal dialog toegevoegd on exit
 * dirty document and savedstatedesigner. show of modal dialog hangt daar vanaf
 * detailpanel opslaan in specifieke klassen (parent, child van TPageBuilderControllerAbstract)
 

## 29 mei 2024 pagebuilder
* selecteren werkt nu
* selecteren met ctrl en Shift werkt!
* document details panel grid werkt nu
* document details panel heeft content nu
* pagebuilder-frontpage-theme.css toegevoegd
* styling all input elements
* nog meer styling input elements
* tpl_pagebuilder is uitgefaseerd, zat niks in
* laden van document details werkt, op 2 na (saven moet nog)
* laden van document details werkt, op 1 na (status) (saven moet nog)
* laden van document details werkt, status werkt (saven moet nog)
* status column toegevoegd in list_pages controller

## 28 mei 2024 pagebuilder
* fix: ergens iets niet goed gegaan met renamen lib_global_header.js, dit was lib_global_footer.js
* fix: creating new pagebuilder doc gaf onnodig error
* css class namen gewijzigd voor designobjects-ininpector, voor beter onderscheid designobjects-indesigner 
* functie voor uitdelen id's designobjects in designer



## 24 mei 2024 pagebuilder
* algemene rename modelToForm() en formToModel() ==> modelToView() en viewToModel()
* module icons loaded from database
* saven met gerenderde content werkt nu
* er is nu een categorie met -- ALL OBJECTS -- ipv een rare default die alles onder elkaar plakt met dubbele designobjects die in meerdere categorien vallen (zoals favorites)
* preview url werkt nu 
* js libraries rename lib_std_* --> lib_global_
* notification.js rename lib_global_notification.js
* rename notification.css ==> global_notification.css
* std_reset.css ==> global_reset.css
* rename global_notification.css --> theme_notification.css
* rename std_general.css ==> theme_global.css
* rename withmenu.css ==> theme_withmenu.css
* rename withoutmenu.css ==> theme_withoutmenu.css
* rename cmsfooter.js ==> cms_footer.js
* rename cmsheader.js ==> cms_header.js
 * 24 mei 2024: TControllerAbstract & childs: rename to bindVarsEarly()
 * 24 mei 2024: TControllerAbstract & childes: rename to bindVarsLate()


## 22 mei php8
* mysql dates can now be null, which TDBPreparedStatement can now process for TSysModel as default timestamp 0 dates
* 22 apr 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() datums zijn nu null by default, and nullable! dit was voorheen 0 wat problemen gaf in mysql
* 22 apr 2024: alle children van TSysModel aangepast op nieuwe datum structuur
* 22 apr 2024: HTMLTag: fix: addAttributeToHTML() strlen() doesn't expect null (php8)
* 22 apr 2024: utf8_decode() functies vervangen
* werkt nu op php8


## 21 mei pagebuilder, php8
* 21 mei 2024: TDBPreparedStatementMySQL: generateSQLColumnsTableManipulation() strlen() werkte niet meer met null in php8
* iSiteID in config
* install: probleem datums niet nul mogen zijn



## 19 mei pagebuilder
* spinner icon wanneer save click
* uitsplitsing handleSaveChild() -> html title, meta description
* earlybinding variables (title etc) verplaatst -> late-binding variables
* variable: now sData instead of sHTML = data from pagebuilder
* daadwerkelijk saven naar database werkt nu!
* bezig met renderen html, foutje ergens met objecten uit arrDesignObjects laden

## 18 mei pagebuilder
* pagebuilder: notification: saved
* pagebuilder: button feedback is saving
* entire framework: progressbar on top of screen
* pagebuilder: showing progressbar on save
 * 18 apr 2024: HTMLTag: constructor heeft nu $objParentNode
  * 18 apr 2024: HTMLTag: all child classes implement now the $objParentNode in constructor
  * 18 apr 2024: HTMLTag: all child classes use appendChild() instead of addNode() -- komt meer overeen met javascript
 * 18 apr 2024: HTMLTag and childs: verschillende renames 1. om het duidelijker te maken dat 't childnodes zijn 2. betere overeenkomsten met javascript DOM-manipulation  
 * rename renderHTMLNodeSpecific() --> renderChild()
 * renderChild() is now a protected function
* 18 mei 2024: TPageBuilderDocumentsAbstract: rename nameinternal -> title
 * 18 mei 2024: TPageBuilderDocumentsAbstract: data field added
 * 18 mei 2024: saven lukt in principe all, alleen nog processen


## 17 mei html parser
 * 17 apr 2024: HTMLTag: fix: text node teveel geparst
 * 17 apr 2024: HTMLTag: tag contents is being processed now
 * 17 apr 2024: HTMLTag: removed separate dataset array for data-* attributes
 * 17 apr 2024: HTMLTag: hasclosingtags methods en internal variable removed
 * 17 apr 2024: HTMLTag: cleanup: unnessary comments removed
 * 17 apr 2024: HTMLTag: cleanup: unnessary comments removed
 * 17 mei 2024: TCRUDDetailSaveController: fix: getDateTimeFormatDefault() gaf alleen tijd terug
* 17 mei 2024: FormInputAbstract: isArray verwijderd (want deze zit al in de parent)
* 17 apr 2024: HTMLTag: fix: renderHTMLNode(): rendered a useless </> after the last tag
 * 17 apr 2024: HTMLTag: fix: parserDoNewTagsExist(): didnt support <!-- comments and <!DOCTYPE


## 16 mei html parser
* bezig met parser deel 1: recursief
* hij lijkt te werken, paar dingen nog
* nog een nieuwe herschreven versie, nog een paar todo's


## 15 mei pagebuilder
* rename TagAbstract => HTMLTag en niet abstract meer!
 * 15 mei 2024: lib_string: sanitizeHTMLTagAttribute() less conversion, only whitelist filtering with preg_replace, hopefully faster too
 * 15 mei 2024: lib_string: sanitizeHTMLTagIdName() less conversion, only whitelist filtering with preg_replace, hopefully faster too
 * 15 mei 2024: lib_string: renamed: getSafeJavascriptFunctionName ==> sanitizeJavascriptFunctionName
 * 15 mei 2024: HTMLTag omgebouwd to dynamische attributen met array. dit maakt html parsen mogelijk
 * 15 apr 2024: lib_sys_framework: logError() geeft een div (rode achtergrond) met error info  in debug mode
 * 15 apr 2024: HTMLTag: addAttributeToHTML() uses new sanitize functions
* 15 apr 2024: HTMLTag: specific methods for setting and getting values as booleans ===> THE DEFAULT BEHAVIOR IS STRINGS INSTEAD OF BOOLS
* 15 apr 2024: lib_testcases added 
* in principe werkt de html parser, maar nog wat uitbreidingen, zoals data-*

## 14 mei oops bij 13 mei geschreven


## 13 mei pagebuilder
* 13 mei 2024: lib_string: renamed getSafeHTML* => sanitizeHTML*
* tabs worden nu gegenereerd n.a.v. category array
* fix: detailtabs deden het niet meer
* js: panel stuff -> panels.js
* zoeken naar DesignObjects werkt nu volgens nieuwe methode
* add-panel favorieten toegevoegd
* html end tag </button> miste voor strikethrougg
* svg voor dezelfde <button> miste een spatie scheiding html attribuut
* bezig met dropping
* rename class: designviewport-content => id: designviewport-contentcontainer
* add-panel accepteren drops op basis van types
* add-panel onder designviewport-contentcontainer
* drag and drop van designobjects op add-panel werkt nu.
* doubleclick on designobjects werkt nu ook!
* saving with fetch (fetch post werkt, daadwerkelijk saven nog niet)
* nog iets verder met saven
 

## 12 mei pagebuilder
* rename $arrDesignObjects -> $arrDesignObjectsCategories
* favorites array is gewoon een category
* json object: designobjects (inclusief naam, type, html, svg, isfavorite, details-tewijzigenin-detailpanel) als client-side global js var beschikbaar
* json object: designobjecttypes (name, allowDropFrom (array)) als client-side global js var beschikbaar
* new category layout array
* bezig met genereren DesignObjects op tabs via js objDesignObjects methode (verkregen via json) ==> zie console.log('hiergebleven'+iTabIndex);

## 11 mei pagebuilder
* types toegevoegd voor designobjects
* for searchlabelscsv  is now constant used
* iets verder met dragging & dropping on add-panel

## 10 mei pagebuilder
* tabsheets genereren werkt nu via TBuilderBuilderControllerAbstract voor desktop en mobiel
* kleine wijzingen aan verschillende dom html tags
* rename css names: designobject-inspector-grid -> designobject-grid
* add-panel: kan handmatig schakelen tussen panels
* de new tabsheets worden nu automatisch gegenereerd op basis van categorien in de controller
* ook de detail tabsheets worden nu automatisch benoemd volgens de naming structure in de controller
* rename: types => categories 
* designobject css grid wrapt nu cells naar volgende regel, waardoor deze nu beter passen in alle grids (inclusief the add-panel)
* basis opgezet voor dragging and dropping
* animation onclick add panel


## 8 mei pagebuilder
* opruiming filler comments
* transm() voor designobjects
* restructure panels, zodat kopieren makkelijker gaat
* kopieer functie gescrheven
* alles kopieert nu naar goede tabblad op mobiel
* wat ui functies verhuisd van algemene lib_std.js naar lib_ui.js
* koppen in tabsheets
* first heading in tabsheet minder marge
* <select><option>--all new objects--</option></select> is now supported, and shown by default
* created abstract pagebuilder controller in php
* created TPageBuilderDesignObject
* bezig genereren <select> op basis van categorien
* rename handmatige tabsheets, zodat we deze kunnen gebruiken om automatisch tabsheets te genereren
 * 8 mei 2024: lib_string: added: getSafeHTMLTagIdName()
 * 8 mei 2024: lib_string: added: getSafeHTMLTagAttribute()
 * 8 mei 2024: lib_string: added: getSafeHTMLTagAttributeValue()
 * 8 apr 2024: TagAbstract: added support for data attributes <div data-*="value">
* 8 apr 2024: TagAbstract: added support for onmousedown event
* 8 apr 2024: added class DOM: Svg
* bezig TPageBuilderControllerAbstract->executeEarlyBinding() ---> nog afmaken, ergens foutje met layout array


## 7 mei 2024 pagebuilder
* divjes voor headings
* fix: class grid-> id grid
* copy objects from desktop inspector to mobile inspector
* fix: id fout met selecteren designobject
* selecteren van designobject in beide inspectors (desktop+mobiel) lukt nu
* fix: de border van de mobiele menus was zichtbaar, ook in tablet-mode
* zoekfunctie designobjects werkt voor desktop en mobiel

## 6 mei 2024 pagebuilder
* rename font variable for fonts in panels, so it applies to all panels including mobile
* algemene js class voor tabsheets geschreven (lib_std_ui.js) voor <div>-tabheads en <select>-tab selector. nog bezig met implementeren in pagebuilder
* new panel voor mobiel werkt
* details panel voor mobiel werkt
* FIX: extra </div> zorgde voor tabsheets weergegeven in header
* rename en bij elkaar voegen variabelen achtergrondkleuren panels
* mobiel panel streep aan onderkant
* mobiel panel lichtere achtegrond kleur
* size aanpassingen zodat de pagebuilder beter weergegeven wordt op mobiel MET toetsenbord
* marge top viewport weggehaald op mobiel


## 5 mei pagebuilder
* combobox + serach box toegevoegd new panel
* rename border color variable
* border left and right panel
* combobox werkt nu met tabs new panel
* rename tabs ids new-panel desktop
* styling tabs new-panel desktop (color)
* styling tabs detail-panel desktop (color)
* details tabs desktop are now working
* first tab details desktop is set on pageload
* tabheads netjes gestyled nu
* marge viewport content top ge-set
* kleur tab font wordt nu aangepast als geselecteerd
* viewport kleuren zijn nu gedaan
* scrollbar nu in viewport-container ipv viewport-content
* mobiele menus 2 pixels omhoog
* hoogte viewport was screwed up. verholpen met height: max-content
* mobiele menus schakelen elkaar uit wanneer deze in de weg zitten
* mobiele menus groter
* mobiel menu: new: search en types selection box


## 4 mei pagebuilder
* styling icoontjes kleuren
* font poppins nu door hele CMS
* ideas and fonts toegevoegd
* icons afmetingen beter met padding
* buttons definiti is pagebuilder-global.css
* text op buttons werkt nu fatsoenlijk
* resizen werkt nu beter in header
* rare borderwidth fout verholpen waardoor scrollbar getoont werd
* toolbar layout aanwezig


## 3 mei pagebuilder
* tablet viewport toegevoegd
* viewport op basis van css class
* viewport resized met animatie
* header buttonpanel goede afstanden
* header kleuren verbeterd
* panels mobiel animeren nu
* overgebleven tablet icoontje verdwijnt nu met mediaquery op mobiel
* button states mobile panels wordt nu bijgewerkt
* rename css class var name in js
* alles mbt panels in aparte css
* alles mbt panels in aparte js
* alles mbt viewsports in aparte css
* alles mbt viewsports in aparte js
* alles mbt tabs in aparte css
* alles mbt tabs in aparte js
* alles mbt header in aparte css
* 4x rename js functions ipv toggle-left and right panel, it is now called toggle-new and detail panel
* background color issue verholpen met knoppen die niet geselecteerd leken
* hele cms borders input boxen wanneer gefocussed is nu professioneler
* standard css included
* standard css weggehaald
* alle children in header title removed on mobile


## 2 mei
* pagebuilder: design4: tabsheets new panel werkt nu
* pagebuilder: design4: tabsheets details panel werkt nu
* cms: alle CSS en javascripts worden geinclude voor alle skins
* nu bezig met overzetten pagebuilder design4 naar skin en template. nog testen
* overzetten pagebuilder naar skin en template voldaan
* fix: pagebuilder foutje tabs details werkten niet goed
* ff reset dit was goed
* fix desktop viewport done!
* fix knoppen viewports
* animatie desktop panels rechts en links werkt nu


## 1 mei
* verder gegaan aan design3.html voor pagebuilder: mobile view, desktop views buttons werken nu
* pagebuilder: design3: resizen werkt nu met knoppen voor juiste device
* pagebuilder: design3: content scrollt nu zonder toolbar
* pagebuilder: design4: verbeterde scroll bars op content area
* pagebuilder: knoppen update viewport werkt nu

## 30 april
* fix: loglogError() ipv logError() in verschillende files
* rename: .hamburgermenu-parentcanvas to .hamburgermenu-container
* verder gegaan aan design3.html voor pagebuilder


## 29 april
* scrollende tabsheets
* switchen websites werkt met <select>-box
* HEADER: splitsing cms javascript lib en general function lib
* HEADER: cms javascript lib naar theme verplaatst, lib_std_header 1 dir lower
* FOOTER: splitsing cms javascript lib en general function lib
* FOOTER: cms javascript lib naar theme verplaatst, lib_std_header 1 dir lower



## 28 april
* rename module ->update() naar module ->refactorDB()
* rename module->install() module->installDB()
* rename module->uninstall() module-uninstallDB()
* rename pagebuilder FIELD_DESCRIPTIONINTERNAL -> FIELD_NAMEINTERNAL
* list_pages joint tblModules niet meer (ivm deinstalleren pagebuilder)
* const voor pagebuilder location
* pagebuilder wordt weergegeven, maar nog problemen met access denied
* showAccessDenied() wordt met logAccess() gelogd
* 28 apr 2024: TCRUDDetailSaveController: if modulename == '', then modulename is AUTH_MODULE_CMS
* pagebuilder router start nu eindelijk op
* auth operations toegevoegd pagebuilder
* betere naam voor system settings -> global settings
* fix: global settings worden nu wel opgeslagen
* TController abstract kan nu string output geven ipv renderen naar scherm
* TSysModel update() heet nu refactorDB()
* terug gemerged vanaf rename branch
* 28 apr 2024: TSysModel: generateHTMLSelect() extra parameter so you can define a field (instead of always using the id field)
* wijzigingen aan pagebuilderrouter
 * 28 apr 2024: TCRUDDetailSaveController: handleSubmitted() wordt nu alleen aangeroepen wanneer daadwerkelijk gesubmit is (ipv altijd)
 * updates pagebuilderrouter
 * tpl_modellist gebruikt nu addVariableToURL() ipv zo plat een ?id= te doen
 * links onderstrepen in menu weggehaald + table header onderstrepen links weggehaald
  * 28 apr 2024: TSysModel: generateHTMLSelect() extra parameter so you can define a text field (instead of always using getGUIItem())
  * pagebuilderrouter werkt in principe, 
  * pagebuilderrouter detecteerd verkeerde versie, maar nog een fout in translation laden waardoor js foutmelding niet weer geeft
  * pagebuilderrouter translation fout gevonden, 
  * javascript error messages worden weergegeven


## 27 april
* verschillende renames

## 26 april
* extra klasse 'elements' pagebuilder
* rename error() => logError()
* web paginas worden nu weergegeven
* rename pagebuildercontainers -> pagebuilderstructures
* rename pagebuilderpagesabstract -> pagebuilderdocumentsabstract
* velden modulenaaminternal, nicename, version toegevoegd. Het is nu mogelijk om te achterhalen welke pagebuilder de pagina gemaakt heeft en zo de juiste pagebuilder op te roepen. versienummer geeft mogelijkheid om conversie door te voeren
* velden verhuisd van pagebuilderdocumentsabstract -> pagebuilderwebpages
* pagebuilder layout kan nu links en rechts togglen (behalve wanneer start met mobiele layout)


## 25 april 2024
* 25 apr 2024: TControllerAbstract: fix: populate() werd uitgevoerd zelf wanneer deze gecached was. Nu wordt populate alleen uitgevoerd wanneer GEEN cache hit
* modules geven module type terug
* sychronizeDirStructure() removed from TSysModules
* module type toegevoegd aan database, waardoor nu zichtbaar is in module lijst van Mod_sys_modules
* class TSysWebpageBuilderPages and TSysPageBuilderPagesAbstract created and tested
* css tabsheets verbeterd
 * 25 apr 2024: TSysModel: fix: in defineTableDebug() infinite loop
 * 25 apr 2024: TSysModel: defineTableDebug rename to debugDefineTable()
* created data klassen voor pagebuilder
* websites nu netjes weergegeven als dit enabled is

## 24 april 2024
 * 24 apr 2024: CSS quarterwidthtag and halfwidthtag worden met mediaquery 100% op mobiel
 * 24 apr 2024: Select(): addOption() parameters switched
 * 24 apr 2024: Select(): fix: generateDaysOfTheWeek() extra parameter for selected day
 * 24 apr 2024: Select(): fix: generateMonths() extra parameter for selected month
 * 24 apr 2024: settings user. goede dag wordth nu geselecteerd
 * 24 apr 2024: localization ook bij edit user
 * 24 apr 2024: verplaatsingen bootstrap
 * 24 apr 2024: translations system renamed to translations global
 * 24 apr 2024: alle transw() functies zijn nu transg() functies
 * 24 apr 2024: alle language files hebben nu de extensie .csv
 * 24 apr 2024: GLOBAL_PATH_CMS_LANGUAGES rename -> GLOBAL_PATH_LANGUAGES
 * 24 apr 2024: nieuwe transw() functie kan meerdere websites aan met een website-identifier
 * 24 apr 2024: TCountrySettings en objCountrySettings renamed naar TLocalisation en objLocalisation
 * 24 apr 2024: users hebben nu een datelong dateshort en timelong en timeshort
 * 24 apr 2024: bloat uit localisation file
 * 24 apr 2024: bugjes ivm verwijderen bloat fixed
* 24 apr 2024: TLocalisation: setSetting() added
* 24 apr 2024: user preferences date and time format is now being used
 * 24 apr 2024: TLocalisation: loadFromFile() parameter added as protection to be loaded twice (and wasting system resources)
* 24 apr 20204: timezone is set by user settings --> maar nog niet bij laden andere onderdelen
* 24 apr 2024: TLocalisation: timezone is set in this class: date_default_timezone_set 
* 24 apr 2024: TLocalisation: rename getCountrySetting() -> getSetting()

## 23 april 2024
* new directory 'removeafterinstall' for everything that needs removed after installation, like installer scripts, but also this version document
* installation procedure aangepast. nu heel gebruiksvriendelijk. het wordt opgepikt in de index.php dat de "removeafterinstall" nog bestaat
* development environment renamed naar debug mode
* dev doc in "removeafterinstall"
* localisation stored for users
* renames TSysUsersAbstract, TSysCMSUsersRoles, TUsersSessionsAbstract
* 23 apr 2024: Select(): added: generateFromArray()
 * 23 apr 2024: Select(): added: generateDaysOfWeek()
 * 23 apr 2024: Select(): added: generateMonths()
* 23 apr 2024: Select(): added: extra parameter for addOption() to select the option
* halfwidthtag en quarterwidthtag aan css toegevoegd, en uit standaard componenten verwijderd
* loginschermen aangepast op halfwidthtag en quarterwidthtag
* usersettings localization wordt geladen en gesaved
* TODO: localisation van settings kopieren naar user

## 22 april 2024
* start extractie password recover controller
* passwordrecover_enteremail controller werkt. nu nog email sturen
* in principe werkt de password recover. nu nog testen
* password recover werkt nu helemaal. = getest
* alleen 2x create account controllers nog overzetten
* createaccount bezig
* createaccount controller entercredentials klaar
* alle create account controllers nu af
* alle log_error() weg uit create account controllers
* alle transw('logincontroller_xxx') vervangen door transw('authenticationsystem_xxx')
* access wordt nu gelogd in 5 autentication controllers in construct__
* opruiming in TCMSAuthenticationSystem en TAuthenticationSystem abstract. alle uitgecommente functies weggehaald
* de oude manier van controllers (in de root van het cms die weer TCMSAuthenticationSystem instantieren) is nu verwijderd. Alle CMS controllers staan vanaf nu in de controller directory van het cms


Notice: Undefined index: tblSysCMSUsersRolesAssignUsers in /volume1/web/cms5/www/admindr/classes/models/TSysModel.php on line 1939

Notice: Trying to access array offset on value of type null in /volume1/web/cms5/www/admindr/classes/models/TSysModel.php on line 1939

Notice: Trying to access array offset on value of type null in /volume1/web/cms5/www/admindr/classes/models/TSysModel.php on line 1939


## 20 april
* verder met verplaatsen
* login werkt in principe, nu nog testen en de rest van de controllers omzetten


## 19 april 2024 rename loginController -> authenticationSystem
* rename: TLoginControllerAbstract -> TAuthenticationSystemAbstract
* rename: TCMSLoginController -> TCMSAuthenticationSystem
* rename: objLoginController -> objAuthenticationSystem
* fix: language files werden dubbel ingeladen. in bootstrap en bootstrap_cms_auth. nu alleen wanneer gewijzigd door user
* splitsing TLoginformControllerAbstract en loginform voor CMS
* bezig om alle login+logout functies te verplaatsen naar loginform controller


## 18 april 2024 - major changes to get the CMS5 framework up to MVC standard structure
* css changes: hamburger menu locatie nu normaal
* css changes: login form nu proper border en ronde hoeken
* css changes: login form nu text leesbaar
* renamed 'themes' dir to 'views' dir to adhere better to MVC conventions
* moved global 'css' and 'js' dir to global 'views' dir
* 18 apr 2024: lib_cms_url: getPathModuleTemplates() added
* module dir structure changed: 'views' dir added
* module dir structure changed: 'templates' moved to 'views' dir
* module dir structure changed: 'images' moved to 'views' dir
* 'font' dir verplaatst naar 'views' dir
* 18 apr 2024: urlrouter.php: supports instantiating controllers from cms global controller directory if including .php file doesn't exist in root
* dashboard omgezet naar controller in controller directory
* problem fixed where $objLoginController didnt exist (cronjob for example) and therefore didn't render templates
* 18 apr 2024: TTranslation: bugfix: translate() trims the result


## 9 april 2024
* onderverdeling js general and skin specifiek
* verbeterd login formulier

## 6 april 2024
* tabsheets duidelijker
* volgorde permissions bij currencies toegevoegd
* path js gewijzigd -> verplaatst naar root ipv in themes
* tekst bulk actions geupdate
* inline javascript verhuisd naar aparte js files
* menu: CMS2 modules zelfde look and feel als de rest
* js header footer
* extra css folder in root
* jquery UI and jquery UI path corrections in css folder root
* reset.css verhuisd naar root css folder
* jquery moved to vendor dir
* GLOBAL_URL_CMS_JSSCRIPT renamed GLOBAL_URL_CMS_JAVASCRIPTS
* notificaties vervangen door een eigen notificaties
* minder afhankelijk van JQuery door vervangen notificaties

## 5 april 2024
* vendor dir verwijdert uit automatisch htaccess toevoegen in cron job
* mobile menu werkt
* title in top header weergegeven.
* de tabsheets en de rest wordt niet meer naar beneden gedrukt op kleine scherm
* tabsheets werken nu helemaal

## 4 april 2024
* header weg gehaald zodat er meer ruimte is op mobiel -> items verhuisd naar menu onderaan
* dark mode finished!
* menu items iets dichter bij elkaar
* facturatiemodule kleuren darkmode

## 15 maart 2024
* 15 mrt 2023: mod_contactform, mod_transactions recht order toegevoegd
* 15 mrt 2023: chmod uit ttranslation verwijderd, gaf fouten
* 15 mrt 2023: mod_transactions. list_transactiontypes had verkeerde permissies PERM_CAT_TRANSACTIONSTYPES
* 15 mrt 2023: FormInputAbstract: getValueSubmittedAsBool() added
* 15 mrt 2023: mod_contactform in principe klaar
* 15 mrt 2023: 64 bits integer wordt bij installatie gecheckt
 * 15 mrt 2024: lib_types: is32BitInteger faster check
 * 15 mrt 2024: lib_types: is32BitInteger ==> isPHP32BitInteger
 * 15 mrt 2024: mod_transactions: wordt niet meer met onzichtbar row-templates gewerkt, maar de eerste regel wordt gekopieerd met JS. opslaan werkt nu
 * 15 mrt 2024: TSysModel: debugResultset() updated
 * 15 mrt 2024: mod_transactions lines can be removed

## 13 maart 2024
* 13 mrt 2023: mod_contactform werkt
* 13 mrt 2023: TContactFormController verhuisd naar mod_contactform
* 13 mrt 2023: TContactFormController gekoppeld aan categorien
* 13 mrt 2023: FormInputAbstract: getValueSubmittedInt() added for fast reading of int values
* 13 mrt 2023: FormInputAbstract: getValueSubmitted() returns null if none of the conditions are met
* 13 mrt 2023: FormInputAbstract: getContentsSubmitted() deprecated, use getValueSubmitted() instead



## 12 maart 2024
* start gemaakt met module contact form
* verder gegaan met module
* todo: submission showen en editen

