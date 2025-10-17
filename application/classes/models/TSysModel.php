<?php
namespace dr\classes\models;


use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TCurrency;
use dr\classes\models\TSysTableVersions;
use dr\classes\dom\tag\TPaginator;
use dr\classes\db\TDBConnection;
use dr\classes\dom\tag\form\Select;
use dr\classes\dom\tag\form\Option;
use dr\classes\dom\tag\I;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');
include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_security.php');



/**
 * This TSysModel represents a database table with all information about columns (columntypes etc)
 * This class represents 0, 1 or more records in the table, pointer to by the recordpointer
 * 
 * The codeword 'DB' in function names means that the dabase is consulted/manipuled and probably the internal resultset is changed
 * 
 * 
 ****************************************************
 * unique features
 ****************************************************
 * -> elegant querybuilder with easy to use interface and no nested array interfaces (they are there but hidden for the user)
 * -> quick functions to define database fields. Define once and copy it: $this->setFieldCopyProps(destination, source);
 * 
 ****************************************************
 * How to add a new record?
 ****************************************************
 * $objModel->newRecord() --> is nessasary
 * $objModel->setFirstName('harry');
 * $objModel->setLastName('leMonde');
 * $objModel->saveToDB(); //add also to database
 *
 * 
 ****************************************************
 * Need-to-knows about this class
 ****************************************************
 * -> 'Dirty'-flag means that the record is changed after the last read from the database
 * -> 'New'-flag means that the record is never saved to the database
 * -> the 'DB' keyword means that the function does some interaction with the database in stead of only locally
 * -> with referenced tables: the instantiated objects are stored per row in place where the referencekeyvalue is normally stored, i.e. the customer object is stored in the field iCustomerID
 * -> it is possible to use another (external) database connection (other than de default from the bootstrap). You have to explicitly set it with setConnectionObject()
 * -> use unique names for fields. This saves a lot of trouble with aliasses in queries when using joins. So, the fieldname 'name' is too generic, try using 'petname' or 'bookname' etc. 
 * -> checksum calculation is automatic on saving. but if you want to use it, you have to call explicitly matchChecksum() --> because you otherwise allways have to load the required fields for calculating the checksum (not ideal for easy listing records)
 * -> record locking and checkout look the same basically the same, but a checkout expires automatically, a lock does not. the checkout is meant for the GUI and doesn't have any automatic functions under the hood (for ultimate control of the programmer). The checkin/checkout of records are supposed to be managed automatically for the user by the controllers. 
 * -> Locking and unlocking is meant to give a possiblity to protect a record against editing and deletion, locking/unlocking are manual actions for the user. TSysModel has support for parameters in the deleteFromDB() and saveToDB() functions to check for locks. These parameters are default: NO CHECK to prevent annoying complications for the programmer
 * 
 ****************************************************
 * Identifiers
 ****************************************************
 * This class has multiple identifiers.
 * 1. FIELD_ID = default primary key	Fast, used internally, but iteratable for malicious actors. If you know that invoice 19423 exists, you know that 19422 also exists 
 * 2. Random id							Fast, generates a (long) random number that is not iterable (unless you do a brute force attack)
 * 3. Unique id							Slow, generates a (long) random string that is unique to the record.
 * 
 * this randomid has the same function as ID, but for security reasons we don't 
 * want an id that enumerates ascending (i.e. after userid 3 always comes userid 4), 
 * so an attacker can guess the next value 
 * This way we can securely store it in a cookie or session, but using a 
 * real random unique-id string value (so NOT unqueid() ) is probably safer for this purpose,
 * because it's harder to guess.
 * This randomid has no relation to the record id (on purpose)
 * for speed-database-lookup purposes this id is a numeric value and not 
 * character based generated like with uniqid() for example
 * DON'T USE randomid as primary key or reference tables if a regular ID is available.
 * A randomid is randomly generated on save of a new record.
 * DON'T USE randomid on large tables, because id collisions will slow down the 
 * performance of this class tremendously on saving, or won't be able to save a 
 * record at all (because it can't find a unique randomid in the db table).
 * Randomid is also very useful for small tables with a lot of refeshing LIFO or 
 * FIFO stack-like data, like user-login-sessions.
 * Which otherwise might run out MAX_INTEGER bounds of the normal id very quickly.
 * 
 * 
 * 
 ****************************************************
 *  	       	DEFINING A DATABASE TABLE
 ****************************************************
 * Each model corresponds with a database table.
 * However you still need to define the table in the database,
 * like: which type is the column (integer, character, blob etc),
 * how long is a field (100 characters), does it refer to another DB table etc.
 * 
 * You define a table with defineTable() of the child class.
 * (there is a function defineTableInternal() that does this for pre-defined fields in TSysModel)
 * Each property has to be defined individually, for example
 *		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_COMPANYNAME, '');
 *		$this->setFieldType(TSysContactsAbstract::FIELD_COMPANYNAME, CT_VARCHAR);
 *		$this->setFieldLength(TSysContactsAbstract::FIELD_COMPANYNAME, 100);
 *		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_COMPANYNAME, 0);
 *		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_COMPANYNAME, false);
 *		 .... etc ... 
 * 
 * COPYING PROPERTIES:
 * Defining 100 fields with each 20 properties is a bit tedious, so there is a copy function: setFieldCopyProps()
 * $this->setFieldCopyProps(TSysContactsAbstract::FIELD_LASTNAME, TSysContactsAbstract::FIELD_COMPANYNAME);
 * It is no problem to overwrite the fields later after calling $this->setFieldCopyProps()
 * for example copying form companyname to lastname:
 * $this->setFieldCopyProps(TContacts::FIELD_LASTNAME, TContacts::FIELD_COMPANYNAME);
 * $this->setFieldLength(TContacts::FIELD_LASTNAME, 255);
 * 
 * DEBUGGING:
 * When copy/pasting values it is easy to overlook an attribute or have a typo.
 * You will get an error, which can be hard to track down, 
 * because it might error on the source field you copied instead of the 
 * destination field that actually causes the error.
 * The function defineTableDebug() helps you to find the problem
 * 
 * 
 * 
 * 
 * 
 * 
 ****************************************************
 *               	ENCRYPTION
 ****************************************************
 * 2 way encryption is built into the class.
 * with ->set() ->get() you can use an extra boolean parameter to use encrypt or decrypt the data.
 * For this to work, the encryption cypher, digest and passphrase have to be defined in defineTableInternal()
 * if the passphrase is empty then encryption is disabled for this field.
 * 
 * Internally the encrypted version of the data is stored.
 * I choose for storying encrypted instead descrypted data for speed reasons.
 * I estimated that the likelyhood that you needed to query a database returning 100 records 
 * (assuming 2 encrypted fields, which would result in 100x2 resource intensive encryptions) 
 * was much larger than having to request the same field 2 or 3 times (with 3 resource intensive encryptions)
 * So I only encrypt/decrypt the data when you actually need it, instead encrypt/decript with every database action. 
 * 
 * Before you encrypt every single column in a table, remember:
 * 1) encryption is resource intensive, it will slow the system down tremendously!
 * 2) you can't search for data in encrypted fields, therefore you could generate a fingerprint 
 * (a hashed version that is always the same like md5) and search for the fingerprint.
 * But that means that searching is literal and needs a 100% match of all characters 
 * (including matching upper and lowercase).
 * 
 * 
 ****************************************************
 *               	CHECKSUMS
 ****************************************************
 * This class supports checksum to validate data integrity in a database table.
 * The checksum calculation automatically uses a salt and pepper.
 * Although TSysModel hashes and validates the checksum, you have to explicitly return the data for 
 * the checksum in the child class with the function getChecksumUncrypted().
 * In this function you have to output all the values you want to be validated.
 * For example: getChecksumUncrypted() for a user child-class could be:
 * 		return 'random123'.getUsername().getPassword().'extra-rando-things456'; //checksum on username and password
 * Tip 1: with numbers: throw in a couple of divisions and modulos to make it extra hard to crack
 * Tip 2: make sure that getChecksumUncrypted() returns at least 10 characters for extra security (harder to crack)
 * 
 * Be careful with placing checksums on id and uniqueid, because those are 
 * generated AFTER the checksum is already calculated.
 * In other words: the checksum is based on default values (0), while isChecksumValid() 
 * uses the actual numbers, this means that isChecksumValid() 
 * will ALWAYS return false.
 * 
 ****************************************************
 *                QUERY BUILDER ('QB' prefix)
 ****************************************************
 * You can use the 'MAGICAL' query builder of this class to query a database without any database-server specific implementation
 * The query builder has a default limition in the number of records it returns. This is stored in QB_AUTOLIMITRESULTCOUNT. if you dont provide a specific limit count, this number is assumed
 * 
 * These functions are available for you database convenience:
 * ->newQuery(); 									--> optional, only nessary when you reset the querybuilder for a second query with the same object
 * ->selectFrom(arrfields, $objmodel = defaultthisobject) 	--> can stack -->also fields other table
 * ->countResults(field, alias, model)				--> count number of results of query of field
 * ->countDistinctResults(field, alias, model)		--> count number of unique results of query of field
 * ->avg(field, alias, model)						--> average value of field in databasetable
 * ->min(field, alias, model)						--> minimum value of field in databasetable
 * ->max(field, alias, model)						--> maximum value of field in databasetable
 * ->find(field, value, compop) 					--> can stack -->search for records wich meet the requirements
 * ->findBetween() 									--> can stack -->search for records between 2 values
 * ->findID(value)
 * ->findSubquery()
 * ->findQuicksearch() 								--> for providing quicksearch/fulltext search/fuzzy search features (optional fields, otherwise searchable fields is used)
 * ->limit(count, offset = 0)						--> limit resulset to x records. If you only want records 90-100 you can define the offset = 90
 * ->limitOne() 									--> returns only one result
 * ->limitPaginator(paginator)						--> using the paginator object to determine wich results should be loaded
 * ->sort(field, ASC) 								--> can stack --> organise the results 
* ->join(external table, external field)			--> can stack --> inner join with another table
 * ->joinLeft(external table, external field)		--> can stack --> left join with another table (left table is THIS table, right table is the parameter)
 * ->joinRight(external table, external field)		--> can stack --> right join with another table (left table is THIS table, right table is the parameter)
 * ->loadFromDB($bAutoJoinDefinedTables = true, paginator) 	--> executes the actual query in the database (the defined tables are automatically joined, if you dont want that, you have to set the parameter to false)
 * ==========> every function returns this TSysModel object, so you can stack the query with just one line like: $objModel->select(...)->find(...)->loadFromDB();
 * 

 *  
 * created 29 juli 2015
 * 3 sept 2015 : TSysModel: defaults voor getField...() functions
 * 3,4,5,6,7 sept 2015: verderbouwen
 * 7 okt 2015: TSysModel: clear() heeft parameter gekregen om querybuilder te behouden (loadfromDB() gebruikt deze nu)
 * 8 okt 2015: TSysModel: update() bugfix: parameter werd niet doorgegeven naar referenced table object
 * 29 okt 2015: TSysModel: rename select() naar selectFrom()
 * 29 okt 2015: TSysModel: subbqueries van joins mogelijk en where (findSubquery())
 * 29 okt 2015: TSysModel: saveToDB() bugfix voor models zonder idfield
 * 29 okt 2015: TSysModel: loadFromDB() bugfix voor counten resultaten werd geen clear() gegegven
 * 30 okt 2015: TSysModel: initRecordInternal(): updated dat automatisch goede klassen geinitieerd worden TDateTime TCurrency etc
 * 30 okt 2015: TMode: setDateTimeCreated(): bugfix: sette de dateTimeChanged ipv dateTimecreated
 * 8 nov 2015: TSysModel: findID() toegeovegd
 * 22 apr 2016: TMode: generateHTMLSelect() bugfixes. functie werkte niet
 * 24 apr 2016: TSysModel: set() controleert nu of er een nieuw record nodig is. Voorgeen als je vergat newRecord() aan te roepen kreeg je foutmeldingen dat systeemvelden niet gevonden konden worden
 * 4 mei 2019: TSysModel: find(), sort(), findSubquery() heeft extra parameter $bForceFieldCheck
 * 8 mei 2019: TSysModel: find(), sort(), findSubquery() parameter $bForceFieldCheck is default false
 * 8 mei 2019: TSysModel: rename selectFrom -> select() and selectAliasFrom -> selectAlias
 * 24 aug 2019: TSysModel: orderChangeOneUpDownDB() database transactie toegevoegd (en weer verwijderd omdat deze al bestond in deze klasse)
 * 12 sept 2019: TSysModel: isFirstRecord() and isLastRecord() toegevoegd
 * 13 sept 2019: TSysModel: added: checkoutSource, locked, lockedSource related functions and database fields
 * 9 jan 2020: TSysModel: loadFromDB(): de objPagintorClone->loadFromDB(false) --> false toegevoegd omdat je anders 2 inner join statements in je sql query hebt voor je paginator
 * 10 jan 2020: TSysModel: setTDateTime(), setTInteger etc toegevoegd
 * 10 jan 2020: TSysModel: internal setRecordChanged() etc  aangepast naar setTDateTime() functie
 * 10 jan 2020: TSysModel: checkinNowDB() uses now 0 timestamp
 * 10 jan 2020: TSysModel: init() uses now 0 timestamp for checkoutexpires default
 * 14 jan 2020: TSysModel: getFieldsSelected() toegevoegd. het is mogelijk om velden uit te sluiten. in praktijk is het echter sneller om handmatig de velden te definieren
 * 16 jan 2020: TSysModel: uniqueid support toegevoegd
 * 16 jan 2020: TSysModel: findID() wordt niet toegevoegd wanneer parameter niet nuermic is
 * 16 jan 2020: TSysModel: findUniqueID() toegevoegd
 * 18 jan 2020: TSysModel: saveToDB() ondersteuning voor uniqueid en id met automatisch toevoegen find()
 * 18 jan 2020: TSysModel: saveToDB() checks for duplicate unique-ids on save
 * 21 jan 2020: TSysModel: deleteFromDB() heeft parameter $bCheckAffectedRows gekregen
 * 22 jan 2020: TSysModel: setRecordPointerToValue() bug fixed deu to swithcing internal data structure
 * 23 jan 2020: TSysModel: join functions have support for setting an internal table (not always current is assumed)
 * 23 jan 2020: TSysModel: loadFromDB() has support for unlimited recursion of foreign key tables in query (previously only one layer)
 * 1 nov 2020: TSysModel: findQuicksearch() toegevoegd (dit was voorheen findLike()), findLike is nu dedicated aan 1 veld met wildcards
 * 4 nov 2020: ;TSysModel: saveToDB() werkte niet goed met meerdere records (alleen de eerste)
 * 4 nov 2020: ;TSysModel: saveToDB() ondersteuning voor het opslaan van een record met uniqueid als primary key
 * 11 nov 2020: TSysModel: getFieldNullable(): BUGFIX gaf true terug als deze false was (een !=== bug)
 * 13 nov 2020: TSysModel: limitNone(): added
 * 2 dec 2020: TSysModel: added functions: alterFieldDBAdd, alterFieldDBModify, alterFieldDBRename, alterFieldDBDrop
 * 4 dec 2020: TSysModel: definetable() for languages the old method of instantiating the class was used, now the static version is used, which is faster
 * 23 jun 2021: TSysModel: where() alias added for find(). It is a bit more consistent with SQL and new programmers
 * 20/21 aug 2021: TSysModel: support for 2 way encryption
 * 11 sept 2021: TSysModel: isMatchUncryptedValue() added
 * 28 sept 2021: TSysModel: calculateChecksum() added support for pepper
 * 28 sept 2021: TSysModel: isChecksumValid() and calculateChecksum() implemented salt + salt constant in class
 * 28 sept 2021: TSysModel: calculateChecksum() bugfix: speed
 * 28 sept 2021: TSysModel: calculateChecksum(): even more speed
 * 3 nov 2021: TSysModel: uniqueID is now called RandomID to avoid confusion with uniqueid()
 * 3 nov 2021: TSysModel: getUseUniqueID + getUseUqiqueIDAsPrimary is now called RandomID
 * 3 nov 2021: TSysModel: rename findUniqueID() -> findRandomID()
 * 3 nov 2021: TSysModel: meer renames ivm unique voornamel0jk commentaren
 * 22 nov 2021: TSysModel: added string based uniqueid
 * 04 mrt 2022: TSysModel: added support for atomic database transaction
 * 14 nov 2022: TSysModel: defineTableDebug() added. When you ever forget a field or have a copy/paste error, this function lets you track it
 * 14 nov 2022: TSysModel: defineTableDebug() no only works in developmentenvironment
 * 14 nov 2022: TSysModel: debugResultset() added to show the complete table of the resultset
 * 15 nov 2022: TSysModel: buildJoinArrayAutoJoin() heeft parameter voor level of joins gekregen
 * 15 nov 2022: TSysModel: loadFromDB support voor levels of join, eerste parameter ($mAutoJoinDefinedTables = 0) is nu een mixed parameter. true voorheen betekende oneindig, nu betekent het 1 level
 * 28 nov 2022: TSysModel: added: isChecksumValidAllRecords()
 * 31 okt 2023: TSysModel: checksum is md5 instead of sha1 for speed reasons
 * 17 nov 2023: TSysModel: saveToDB() custom order numbers allowed > 0
 * 15 mrt 2024: TSysModel: debugResultset() updated
 * 25 apr 2024: TSysModel: fix: in defineTableDebug() infinite loop
 * 25 apr 2024: TSysModel: defineTableDebug rename to debugDefineTable()
 * 28 apr 2024: TSysModel: generateHTMLSelect() extra parameter so you can define a value field (instead of always using the id field)
 * 28 apr 2024: TSysModel: generateHTMLSelect() extra parameter so you can define a text field (instead of always using getGUIItem())
 * 30 apr 2024: TSysModel: added orderBy() alias for sort()
 * 15 nov 2024: TSysModel: getIPAddr() renamed to getAsIPAddressBin() to make EXTRA clear that you get a type and not a field-name
 * 15 nov 2024: TSysModel: setIPAddr() renamed to setAsIPAddressBin() to make EXTRA clear that you set a type and not a field-name
 * 15 nov 2024: TSysModel: getInt() deprecated in favor of getAsInt() like it was in the old days
 * 16 apr 2025: TSysModel: findLike() deprecated, use find() with COMPARISON_OPERATOR_LIKE instead
 * 24 apr 2025: TSysModel: some esthetic cleanup
 * 24 apr 2025: TSysModel: added findBetween();
 * 13 may 2025: TSysModel: added setFieldDefault...() fields for varchar integer and boolean for fast defining of fields
 * 16 may 2025: TSysModel: added setFieldDefaultTCurrency() +  setFieldDefaultTDateTime functions
 * 23 may 2025: TSysModel: added support for logical operators in findXXX() and quicksearch()
 * 3 jun 2025: TSysModel: bugfix setFieldDefaultsIntegerForeignKey()
 * 3 jun 2025: TSysModel: when record is default, it auto removes defaults from other records
 * 18 jun 2025: TSysModel: isFavorite field added + loadFromDBByIsFavorite() function
 * 8 aug 2025: TSysModel: addCopy() addded
 * 8 aug 2025: TSysModel: removeRecord() added
 * 8 aug 2025: TSysModel: removeRecordAtRecordpointer() added
 * 14 aug 2025: TSysModel: get() checks if table is set explicitly instead of implicitly in debug mode
 * 14 aug 2025: TSysModel: constructor: checks only if getTable() exists in debug mode
 * 15 aug 2025: TSysModel: added unique() and distinct()
 * 21 aug 2025: TSysModel: added recordExistsTableDB()
 * 17 sept 2025: TSysModel: added trashcan field
 * 17 sept 2025: TSysModel: added image alt text field
 * 17 okt 2025: TSysModel: loadFromDBByXXX() doesn't do database load when parameter is invalid. This could have been a security risk
 * 
 * @todo check for too large integer values
 */

abstract class TSysModel
{
//	private $arrData = array(); //the rows of the resulset (aka the table that this model represents)
	protected $arrDataNew = array(); //new internal array 20jan2020: structure $arrDataNew[tablename][fieldname][value] --> tablename = '' in current model, other tables only filled on table joins
	private $iCachedCountArrData = 0; //because of performance reasons we cache the count, because it wont change very often and ik makes a huge performance difference in loops
                
	private $arrFieldInfo = array(); //the fieldnames are the keys in this array; here is information stored about columns: ie. type, maxlength, nullable, foreignkey
	private $arrQBFieldTypesExt = array();//array with types of foreign tables and aliassed fields  
	
	private $iRecordPointer = 0;
	private $bFirstTimeNextCalled = true; //is the next() function called for the first time?
	
	private $objDBConn = null;//database connection object
	

	//field name constants, please define your own in the derived classes
	const FIELD_ID							= 'iID';//identifying number of record. This is often the primary key of the database table
    const FIELD_RANDOMID                    = 'iRandomID'; //a numeric value that basically has the same function as ID, but for security reasons we don't want an ascending enumerating id so an attacker doesn't know what the next id is. Use this for quick db resolving, use uniqueid for better security in sessions and cookies
    const FIELD_UNIQUEID                    = 'sUniqueID'; //a unique value that basically has the same function as ID, but it's a string instead of an integer, so it is slower. For security reasons we don't want an ascending enumerating id so an attacker doesn't know what the next id is, so we can store it securely in a session or cookie. if you have the opportunity to use randomid instead ==> USE RANDOMID, because it is quicker
    const FIELD_NICEID                    	= 'sNiceID'; //a unique value that basically has the same function as sUniqueID or iRandomID, but its human readable
	const FIELD_RECORDCHANGED   			= 'dtRecordChanged';
	const FIELD_RECORDCREATED				= 'dtRecordCreated';
	const FIELD_RECORDHIDDEN 				= 'bRecordHidden'; //niet fysiek deleten, maar op onzichtbaar zetten
	const FIELD_CHECKSUMENCRYPTED           = 'sChecksum';
	const FIELD_POSITION					= 'iPosition'; //display order in table. Use ORDERBY to sort on this number
	const FIELD_CHECKOUTEXPIRES				= 'dtCheckoutExpires';//date with end date to lock file for deleting. this is used by the gui, not under the hood	
	const FIELD_CHECKOUTSOURCE              = 'sCheckoutSource';//a reference to who or what locked the record. this can be a system component or a user. this string will be empty if record is checked in, filled when checked out. It's just meant as a reference for the user (or system adminsitrator) to have a clue why a record is checked out. 
	const FIELD_LOCKED                      = 'bLocked';//you can lock a record to prevent accidental editing. this is used by the gui, not under the hood. both checkout and lock will prevent a record from editing. A checkout wil expire automatically, a lock won't. 
	const FIELD_LOCKEDSOURCE				= 'sLockedSource';//a reference to who or what locked the record. this can be a system component or a user. this string will be empty if record is checked in, filled when checked out. It's just meant as a reference for the user (or system adminsitrator) to have a clue why a record is checked out. 
	const FIELD_IMAGEFILE_THUMBNAIL			= 'sImageFileThumbnail';//if you want to store an image file name, this is the small size version (i.e. 150x150px)
	const FIELD_IMAGEFILE_MEDIUM			= 'sImageFileMedium';//if you want to store an image file name, this is the medium size version (i.e. 500x400px)
	const FIELD_IMAGEFILE_LARGE				= 'sImageFileLarge';//if you want to store an image file name, this is the large size version (i.e. 1024x1024px)
	const FIELD_IMAGEFILE_MAX				= 'sImageFileMax';//if you want to store an image file name, this is the max size version (i.e. 2500x2500px)
	const FIELD_IMAGE_ALT					= 'sImageAlt';//if you want to store an image file, this is the text in the 'alt' attribute (i.e. <img alt="mountain">)
	const FIELD_IMAGE_MAX_WIDTH				= 'sImageMaxWidth';//the width of the image
	const FIELD_IMAGE_MAX_HEIGHT			= 'sImageMaxHeight';//the height of the image
	const FIELD_IMAGE_LARGE_WIDTH			= 'sImageLargeWidth';//the width of the image
	const FIELD_IMAGE_LARGE_HEIGHT			= 'sImageLargeHeight';//the height of the image
	const FIELD_IMAGE_MEDIUM_WIDTH			= 'sImageMediumWidth';//the width of the image
	const FIELD_IMAGE_MEDIUM_HEIGHT			= 'sImageMediumHeight';//the height of the image
	const FIELD_IMAGE_THUMBNAIL_WIDTH		= 'sImageThumbnailWidth';//the width of the image
	const FIELD_IMAGE_THUMBNAIL_HEIGHT		= 'sImageThumbnailHeight';//the height of the image
	const FIELD_TRANSLATIONLANGUAGEID		= 'iTranslationLanguageID';//languageid
	const FIELD_ISDEFAULT					= 'bIsDefault';//boolean is default record? Can only be 1 default! example for countries: when showing countries in a combobox, this is the default country that is selected
	const FIELD_ISFAVORITE					= 'bIsFavorite';//boolean is a favorited record. Example: for languages: you only want to show 10 favorite languages instead of all 230
	const FIELD_ISINTRASHCAN				= 'bIsInTrashcan'; //boolean if record is in trashcan (less evil version of delete)
	const FIELD_SEARCHKEYWORDS				= 'sSearchKeywords';//keywords separated by comma (,). This is used for searching records. This is also known as tags or labels

	const FIELD_SYS_DIRTY					= 'bDirty'; //system field: is record changed since loaded from DB?
	const FIELD_SYS_NEW						= 'bNew'; //system field: is record to be added to DB?
	
	//constants of indexes in $arrFieldInfo (FI stands for Fieldinformation Index)
	const FI_DEFAULTVALUE					= 'defaultvalue'; //mixed
	const FI_TYPE							= 'type'; //integer i.e. CT_VARCHAR
	const FI_LENGTH							= 'length'; //integer i.e. 255
	const FI_DECIMALPRECISION 				= 'decimalprecision'; //integer i.e. 4
	const FI_PRIMARYKEY						= 'primarykey'; //boolean (is a primary key?)
	const FI_NULLABLE						= 'nullable'; //boolean (can field be written with null?)
	const FI_ENUMVALUES						= 'enumvals'; //array with possible values for enumeration
	const FI_UNIQUE							= 'unique'; //boolean (is field unique?)
	const FI_INDEXED						= 'indexed'; //boolean: is column indexed?	
	const FI_FULLTEXT						= 'fulltext'; //boolean: is column a fulltext column used for fuzzy searching
	const FI_FOREIGNKEY_CLASS				= 'foreignkey_class'; //string with the classname wich is references to (php level, NOT database level)
	const FI_FOREIGNKEY_TABLE				= 'foreignkey_table'; //string with table name of foreign table
	const FI_FOREIGNKEY_FIELD				= 'foreignkey_field'; //string with fieldname of foreign key in foreign table
	const FI_FOREIGNKEY_ACTIONONUPDATE		= 'foreignkey_actiononupdate'; //integer contains value i.e. FOREIGNKEY_REFERENCE_NOACTION
	const FI_FOREIGNKEY_ACTIONONDELETE		= 'foreignkey_actionondelete'; //integer contains value i.e. FOREIGNKEY_REFERENCE_NOACTION
	const FI_FOREIGNKEY_JOIN				= 'foreignkey_join'; //string with onstant of lib_types like JOIN_INNER			
	const FI_AUTOINCREMENT					= 'autoincrement'; //boolean (is auto incremented field by database?)
	const FI_UNSIGNED						= 'unsigned'; //boolean (is value unsigned ?)
	const FI_ENCRYPTIONCYPHER				= 'encryptioncypher'; //string with cypher algorithm. Empty means: uncrypted
	const FI_ENCRYPTIONDIGEST				= 'encryptiondigest'; //string with digest algorithm. Empty means: uncrypted
	const FI_ENCRYPTIONPASSPHRASE			= 'encryptionpassphrase'; //string with encryption key. Empty means: uncrypted
	
	//constants wich specify what to do on a record delete or a record update
	//http://dev.mysql.com/doc/refman/5.1/en/create-table-foreign-keys.html
	const FOREIGNKEY_REFERENCE_NOACTION = 0;
	const FOREIGNKEY_REFERENCE_SETNULL	= 1;
	const FOREIGNKEY_REFERENCE_RESTRICT = 2;
	const FOREIGNKEY_REFERENCE_CASCADE 	= 3; //A cascading delete specifies that if an attempt is made to delete a row with a key referenced by foreign keys in existing rows in other tables, all rows that contain those foreign keys are also deleted.
	//A cascading update specifies that if an attempt is made to update a key value in a row, where the key value is referenced by foreign keys in existing rows in other tables, all the values that make up the foreign key are also updated to the new value specified for the key.
		
	const FIELD_ID_VALUE_DEFAULT = -1;//the default non-existing id. When a record has this id we know that it hasnt been assigned yet
	const FIELD_CHECKOUTEXPIRES_VALUE_DEFAULT = 12; //12 hours
	
	//objecten of our own internal Query Builder ('QB' prefix). This replaces the old TDBSQLBuilder
	private $arrQBSelectFrom = array();//2d array. on every item in the array there is an array with the indexes QB_SELECTFIELD, QB_SELECTTABLE etc
	private $arrQBFind = array();//2d array. on every item in the array there is an array with the indexes QB_FINDFIELD, QB_FINDVALUE etc
	private $arrQBFindQuickSearch = array();//2d array with. on every item in the array there is an array with the indexes QB_FINDQUICKSEARCH_FIELDS, QB_FINDQUICKSEARCH_VALUE etc
	private $arrQBLimit = array(); //1d array with index QB_LIMITCOUNT and QB_LIMITOFFSET
	private $arrQBSort = array(); //2d array on every item in the array is an array QB_ORDERBYINDEX_TABLE etc
	private $arrQBJoin = array();//2d array on every item in the array is an array QB_JOININDEX_TABLEOTHER etc
	
	const QB_SELECTINDEX_FIELD 				= 'selectfield'; //index in $arrQBSelectFields -> its an array in wich the arraykey is the fieldname and the arrayvalue is the alias
	const QB_SELECTINDEX_TABLE 				= 'selecttable'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_ALIAS				= 'selectalias'; //index in $arrQBSelectFields 
	const QB_SELECTINDEX_COUNTFIELD			= 'selectcountfields'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_COUNTDISTINCTFIELD	= 'selectcountdisctinctfields'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_DISTINCTFIELD 		= 'selectdistinctfields'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_AVGFIELD 			= 'selectavgfields'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_MINFIELD 			= 'selectminfields'; //index in $arrQBSelectFields
	const QB_SELECTINDEX_MAXFIELD 			= 'selectmaxfields'; //index in $arrQBSelectFields
	
	const QB_FINDINDEX_TABLE				= 'findtable';
	const QB_FINDINDEX_FIELD				= 'findfield';
	const QB_FINDINDEX_VALUE				= 'findvalue'; //start value when using BETWEEN
	const QB_FINDINDEX_VALUEEND				= 'findvalueend'; //end value when using BETWEEN
	const QB_FINDINDEX_SUBQUERYOBJECT		= 'findsubqueryobject';
	const QB_FINDINDEX_COMPARISONOPERATOR	= 'findcomparisonoperator'; //COMPARISON_OPERATOR_EQUAL_TO, greater than etc  ==> see lib_sys_typedef
	const QB_FINDINDEX_LOGICALOPERATOR		= 'findlogicaloperator'; //LOGICAL_OPERATOR_AND, LOGICAL_OPERATOR_OR ==> see lib_sys_typedef

	const QB_FINDQUICKSEARCH_VALUE			= 'findquicksearchvalue'; //string: value of search query
	const QB_FINDQUICKSEARCH_JOINDEPTH		= 'findquicksearchjoindepth'; //integer: max level of join depth. 10 = 10 join levels deep. Default would be 1

	const QB_LIMITINDEX_COUNT				= 'limitcount';//resultset count x records
	const QB_LIMITINDEX_OFFSET				= 'limitoffset';//resultset starts at record	
	const QB_LIMITCOUNTDEFAULT 				= 1000; //when no limitation is specified, this number will be used (to prevent massive load on site and db server)

	const QB_SORTINDEX_TABLE				= 'orderbytable';
	const QB_SORTINDEX_FIELD				= 'orderbyfield';
	const QB_SORTINDEX_ORDER				= 'orderbyorder';
	
	const QB_JOININDEX_TABLEINTERNAL		= 'jointableinternal';
	const QB_JOININDEX_FIELDINTERNAL		= 'joinfieldinternal';
    const QB_JOININDEX_TABLEEXTERNAL		= 'jointableexternal';
	const QB_JOININDEX_FIELDEXTERNAL		= 'joinfieldexternal';
	const QB_JOININDEX_TYPE					= 'jointype';
	const QB_JOININDEX_SUBQUERYOBJECT		= 'joinsubqueryobject';
	const QB_JOININDEX_SUBQUERYTABLEALIAS   = 'joinsubquerytablealias';
	
	//new internal array-keys 20jan2020
	const DATA_VALUE                    	= 'value';
	const DATA_FIELDNAMEORIGINAL        	= 'fieldnameoriginal';//name of the original column (without alias)
	const DATA_TABLENAME                	= 'tablename'; //name of the table (can be alias)
	const DATA_TABLENAMEORIGINAL        	= 'tablenameoriginal'; //original name of the table (without alias)

	//checksums
	const CHECKSUM_SALTSEPARATOR			=  '::';
        
        
	public function __construct()
	{	
		$this->resetRecordPointer();
		$this->defineTableInternal();
		$this->defineTable();

		//defaults query builder
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = 0;
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;	
                
		//because we can't inherit a static method (and we wat getTable static to avoid instantiation due to performance) we check it manually
		if (APP_DEBUGMODE) //for performance reasons: check if we are in debugmode first, so we don't have to do an expensive method_exists() for every single model created in deployment mode
		{
			if (!method_exists($this, 'getTable'))
			{
				logError(__FILE__.__CLASS__.__LINE__, 'static function getTable() IS NOT DEFINED IN CHILD CLASS of TSysModel (this error is thrown in TSysModel->__construct() to enforce static method exists in child class). TIP: make sure the method is STATIC!!');
				// throw new Exception('static function getTable() IS NOT DEFINED IN CHILD CLASS of TSysModel (this error is thrown in TSysModel->__construct() to enforce static method exists in child class). TIP: make sure the method is STATIC!!');
			}
		}
                
	}
	
	public function __clone()
	{
		// $this->arrData = array_clone($this->arrDataNew);
	}
	
	public function __destruct()
	{
		unset($this->arrDataNew);
		unset($this->arrFieldInfo);
		unset($this->arrQBSelectFrom);
		unset($this->arrQBFieldTypesExt);
		unset($this->arrQBFind);
		unset($this->arrQBFindQuickSearch);
		unset($this->arrQBLimit);
		unset($this->arrQBSort);
		unset($this->arrQBJoin);
	}
	
	
	/**
	 * sometimes it is useful for debugging purposes to do a quick dump of the table to the screen to see its contents
	 * 
	 * @return string table data
	 */
	public function dumpTable()
	{
		$this->debugResultset();
	}


	//========= QUERY BUILDER =======
	
	public function getQBSelectFrom()
	{
		return $this->arrQBSelectFrom;
	}

	public function getQBFind()
	{
		return $this->arrQBFind;
	}
	
	public function getQBFindQuickSearch()
	{
		return $this->arrQBFindQuickSearch;
	}
	
	public function getQBLimit()
	{
		return $this->arrQBLimit;
	}
	
	public function getQBSort()
	{
		return $this->arrQBSort;
	}
	
	public function getQBJoin()
	{
		return $this->arrQBJoin;
	}	
	
	/**
	 * get Select fields from SQL query as 1d array
	 * so: "SELECT id, name, pass FROM tblUsers" will return array(id, name, pass) 
	 * this function will return with aliases but no table names
	 * 
	 * this has an equivalent output as getFieldsPublic(), but generated 
	 * from the fields you selected with $objModel->selectFrom()
	 * 
	 * you can use this function for displaying records:
	 * -first you select fields with $objModel->selectFrom()
	 * -then you use getSelectFieldsAsArray() so you can make a html table with all the selected fields
	 * 
	 * ATTENTION: THIS FUNCTION NEEDS TO PROCESS A NEW ARRAY BY LOOKING AT 
	 * $this->arrQBSelectFrom, SO THIS TAKES A PERFORMANCE HIT
	 * 
	 * @todo this function has no support yet for count(), distinct(), avg(), min(), max() etc
	 * 
	 * @param array $arrFieldsExcluded you can exclude system fields such as: checkoutexpires, locked etc
	 */
	public function getFieldsSelected($arrFieldsExcluded = null)
	{
		$arrResult = array();
		
		$iCountFields = 0;
		$iCountFields = count($this->arrQBSelectFrom);
		$bExclude = false;
		
		for ($iIndex = 0; $iIndex < $iCountFields; $iIndex++)
		{
			$bExclude = false;
			if ($arrFieldsExcluded)
			{
				//exists in alias of field?
				if ( (in_array($this->arrQBSelectFrom[$iIndex][TSysModel::QB_SELECTINDEX_FIELD], $arrFieldsExcluded)) || (in_array($this->arrQBSelectFrom[$iIndex][TSysModel::QB_SELECTINDEX_ALIAS], $arrFieldsExcluded))  )
					$bExclude = true;
			}
			
			if (!$bExclude) //only add to array if we we DO NOT NEED TO EXCLUDE
			{
				if ($this->arrQBSelectFrom[$iIndex][TSysModel::QB_SELECTINDEX_ALIAS])
					$arrResult[] = $this->arrQBSelectFrom[$iIndex][TSysModel::QB_SELECTINDEX_ALIAS];
				else
					$arrResult[] = $this->arrQBSelectFrom[$iIndex][TSysModel::QB_SELECTINDEX_FIELD]; 
			}
		}
		
		return $arrResult;
	}
             
         
        
	/**
	 * resets the build of a query
	 */
	public function newQuery()
	{
		//select
		unset($this->arrQBSelectFrom);
		$this->arrQBSelectFrom = array();
		unset($this->arrQBFieldTypesExt);
		$this->arrQBFieldTypesExt = array();
		
		//find --> same as clearFind()
		unset($this->arrQBFind);
		$this->arrQBFind = array();
		
		//quicksearch
		unset($this->arrQBFindQuickSearch);
		$this->arrQBFindQuickSearch = array();
		
		//limit
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = 0;
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;	

		//orderby
		unset($this->arrQBSort);
		$this->arrQBSort = array();
		
		//join
		unset($this->arrQBJoin);
		$this->arrQBJoin = array();
	}

	public function clearFind()
	{
		unset($this->arrQBFind);
		$this->arrQBFind = array();
	}
	
	/**
	 * add array of field(s) you want to display in the resultset 
	 * you cant use aliases with this call, therefore you have to use selectAlias
	 * 
	 * you can specify more than one select() in one query
	 * 
	 * example call
	 * select(array('id', 'name'), 'tblAdresses');  //selects fields id and name from table tblAdresses
	 * 
	 * @param array $arrfields with fields and aliasses, can be null then $objModel->getFieldsDefined() will be assumed
	 * @param TSysModel $objModel if empty, $this will be assumed. (otherwise the object of the foreign table)
	 * @return TSysModel
	 */
	public function select($arrFields = array(), $objModel = null)
	{
		if ($objModel == null)
			$objModel = $this;
		
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'selectFrom(): parameter $objModel not of type TSysModel');		
                
		if (!is_array($arrFields))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'selectFrom(): parameter $arrFields not an array');	
                
		if (!$arrFields)
			$arrFields = $objModel->getFieldsDefined();
		
		//be sure there is always an id field
		if ($objModel == $this)
			if ($this->getTableUseIDField())
				if (!in_array(TSysModel::FIELD_ID, $arrFields))
					array_unshift($arrFields, TSysModel::FIELD_ID);//add element to beginning of array
					
		foreach ($arrFields as $sField)
		{					
			$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_FIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => '');
			if ($objModel != $this)
				$this->arrQBFieldTypesExt[$sField] = $objModel->getFieldType($sField);				
		}
				
		return $this;//make 'm stack baby
	}
	
	/**
	 * add field you want to display in the resultset
	 * 
	 * you can specify more than one selectAlias() in one query
	 * 
	 * example call
	 * selectAliasFrom('id', 'supplierid', $objAddresses);  //selects field 'id' as 'supplierid' from table tblAdresses
	 * 
	 * @param string $sField
	 * @param string $sAlias
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @return TSysModel
	 */
	public function selectAlias($sField, $sAlias, $objModel = null)
	{
		if ($objModel == null)
			$objModel = $this;
		
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'selectAlias: objModel not of type TSysModel', $this);		
				
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_FIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);		
		$this->arrQBFieldTypesExt[$sAlias] = $objModel->getFieldType($sField); //dit kan voor foreign tables zijn, maar ook voor velden met een alias waarvan we geen type weten
		
		return $this; //make 'm stack baby		
	}


	
	/**
	 * count rows of field
	 * 
	 * calls SQL: "select count(Users) as iUserCount"
	 * 
	 * example call of this function:
	 * countResults('id', 'countid', 'tblAdresses');
	 * 
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function countResults($sField, $sAlias, $objModel = null, $iReturnType = CT_INTEGER64)
	{
		if ($objModel == null)
			$objModel = $this;
				
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'countResults: objModel not of type TSysModel', $this);
		
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_COUNTFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
		
		return $this;//make 'm stack
	}
	
	
	/**
	 * count unique rows of field
	 * Unique means that the duplicates are filtered (before counted)
	 * 
	 * calls SQL: "select count(distinct sUserName) as iUniqueUserCount" (filters unique usernames in table)
	 *
	 * example call of this function:
	 * countDistinctResults('order', 'ordercount', 'tblAdresses');
	 *
	 * @param string $sField
	 * @param string $sAlias
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY 
	 * @return TSysModel
	 */
	public function countDistinctResults($sField, $sAlias, $objModel = null, $iReturnType = CT_INTEGER64)
	{
		if ($objModel == null)
			$objModel = $this;
			
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'countDistinctResults: objModel not of type TSysModel', $this);
		
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_COUNTDISTINCTFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
		
		return $this;//make 'm stack
	}
		
	/**
	 * average of field
	 * 
	 * you can specify more than one avg() in one query
	 * 
	 * example call:
	 * avg('number', 'avgnumber', 'tblAdresses');
	 * 
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function avg($sField, $sAlias, $objModel = null, $iReturnType = CT_INTEGER64)
	{
		if ($objModel == null)
			$objModel = $this;
			
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'avg: objModel not of type TSysModel', $this);
		
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_AVGFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
		
		return $this;//make 'm stack
	}	
	
	
	/**
	 * minimum value of field
	 *
	 * you can specify more than one min() in one query
	 * 
	 * example call:
	 * min('number', 'minnumber', 'tblAdresses');
	 *  
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function min($sField, $sAlias, $objModel = null, $iReturnType = CT_INTEGER64)
	{
		if ($objModel === null)
			$objModel = $this;
		
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'min: objModel not of type TSysModel', $this);		
			
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_MINFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
				
		return $this;//make 'm stack
	}	
	
	/**
	 * max value of field
	 *
	 * you can specify more than one max() in one query
	 * 
	 * example call:
	 * max('number', 'maxnumber', 'tblAdresses');
	 *  
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function max($sField, $sAlias, $objModel = null, $iReturnType = CT_INTEGER64)
	{
		if ($objModel == null)
			$objModel = $this;
			
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'max: objModel not of type TSysModel', $this);
		
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_MAXFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
		
		return $this;//make 'm stack
	}	
	
	/**
	 * unique values in a field (filters doubles)
	 * this is the equivalent of the SELECT DISTINCT column1, column2, ... FROM table_name;
	 *
	 * you can specify more than one distinct() in one query
	 * 
	 * example call:
	 * distinct('lastname', 'surname', 'tblAdresses');
	 * 
	 * WARNING: a distincted field does not allow other field to be selected as well, it negates the whole effect of extincting a field
	 *  
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function distinct($sField, $sAlias = '', $objModel = null, $iReturnType = TP_UNDEFINED)
	{
		if ($objModel == null)
			$objModel = $this;
			
		if (!($objModel instanceof TSysModel))
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'distinct: objModel not of type TSysModel', $this);
		
		$this->arrQBSelectFrom[] = array(TSysModel::QB_SELECTINDEX_TABLE => $objModel::getTable(), TSysModel::QB_SELECTINDEX_DISTINCTFIELD => $sField, TSysModel::QB_SELECTINDEX_ALIAS => $sAlias);
		$this->arrQBFieldTypesExt[$sAlias] = $iReturnType;
		
		return $this;//make 'm stack
	}	
	
	/**
	 * alias for distinct()
	 * 
	 * unique values in a field (filters doubles)
	 * this is the equivalent of the SELECT DISTINCT column1, column2, ... FROM table_name;
	 *
	 * you can specify more than one distinct() in one query
	 * 
	 * example call:
	 * distinct('lastname', 'surname', 'tblAdresses');
	 * 
	 * WARNING: a distincted field does not allow other field to be selected as well, it negates the whole effect of extincting a field
	 *  
	 * @param string $sField
	 * @param string $sAlias 
	 * @param TSysModel $objModel if null, the $this will be assumed
	 * @param integer columntype like CT_INTEGER32, but can also be CT_FLOAT or CT_CURRENCY
	 * @return TSysModel
	 */
	public function unique($sField, $sAlias = '', $objModel = null, $iReturnType = TP_UNDEFINED)
	{
		$this->distinct($sField, $sAlias, $objModel, $iReturnType);

 		return $this;
	}


	/**
	 * find record(s) that where field has value, equivalent to WHERE in SQL
	 * 
	 * TIP:
	 * 1. you can specify more than one find() in one sql query :)
	 * 2. if you want to use COMPARISON_OPERATOR_LIKE, make sure to add % before and after the value!
	 * 3. if you want to use COMPARISON_OPERATOR_BETWEEN, use function findBetween()
	 * 
     * if function doesn't seem to work, maybe the $bForceFieldCheck detects a non-searchable field
	 * 
	 * @param string $sFieldOrAlias
	 * @param string $sValue
	 * @param string $sComparisonOperator
	 * @param string $sTable
     * @param integer $iColDataType TP_STRING, CT_VARCHAR, TP_INTEGER -> use this to define a data type when it is not specified in select() -> to prevent that SQL for joined fields is "WHERE courseid = '2'" (with quotes) instead of "WHERE courseid = '2'" which results in an error in MS Acces for example
     * @param string $sLogicalOperator 
	 * @return TSysModel
	 */
	public function find($sFieldOrAlias, $sValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sTable = '', $iColDataType = TP_UNDEFINED, $sLogicalOperator = LOGICAL_OPERATOR_AND)
	{
		if ($sTable == '')
			$sTable = $this::getTable();		

  
		$this->arrQBFind[] = array(
			TSysModel::QB_FINDINDEX_TABLE => $sTable, 
			TSysModel::QB_FINDINDEX_FIELD => $sFieldOrAlias, 
			TSysModel::QB_FINDINDEX_COMPARISONOPERATOR => $sComparisonOperator, 
			TSysModel::QB_FINDINDEX_VALUE => $sValue,
			TSysModel::QB_FINDINDEX_VALUEEND => '',
			TSysModel::QB_FINDINDEX_LOGICALOPERATOR => $sLogicalOperator
		);		
		
		if ($iColDataType != TP_UNDEFINED)
			$this->arrQBFieldTypesExt[$sFieldOrAlias] = $iColDataType;

			
		return $this;//make 'm stack baby
	}	

	/**
	 * find record(s) that where field has value between X and Y, equivalent to BETWEEN in SQL
	 * 
	 * TIP:
	 * 1. you can specify more than one findBetween() in one sql query :)
	 * 2. this method automatically assumes COMPARISON_OPERATOR_BETWEEN
	 * 
     * if function doesn't seem to work, maybe the $bForceFieldCheck detects a non-searchable field
	 * 
	 * @param string $sFieldOrAlias
	 * @param string $sValue
	 * @param string $sEndValue
	 * @param string $sTable
     * @param integer $iColDataType TP_STRING, CT_VARCHAR, TP_INTEGER -> use this to define a data type when it is not specified in select() -> to prevent that SQL for joined fields is "WHERE courseid = '2'" (with quotes) instead of "WHERE courseid = '2'" which results in an error in MS Acces for example
     * @param string $sLogicalOperator
	 * @return TSysModel
	 */
	public function findBetween($sFieldOrAlias, $sStartValue, $sEndValue, $sTable = '', $iColDataType = TP_UNDEFINED, $sLogicalOperator = LOGICAL_OPERATOR_AND)
	{
		if ($sTable == '')
			$sTable = $this::getTable();		

  
		$this->arrQBFind[] = array(
			TSysModel::QB_FINDINDEX_TABLE => $sTable, 
			TSysModel::QB_FINDINDEX_FIELD => $sFieldOrAlias, 
			TSysModel::QB_FINDINDEX_COMPARISONOPERATOR => COMPARISON_OPERATOR_BETWEEN, 
			TSysModel::QB_FINDINDEX_VALUE => $sStartValue,
			TSysModel::QB_FINDINDEX_VALUEEND => $sEndValue,
			TSysModel::QB_FINDINDEX_LOGICALOPERATOR => $sLogicalOperator			
		);		
		
		if ($iColDataType != TP_UNDEFINED)
			$this->arrQBFieldTypesExt[$sFieldOrAlias] = $iColDataType;

			
		return $this;//make 'm stack baby
	}	

	/**
	 * Checks with one function if a record field-value $sValue already exists in database table
	 * This function does not alter the database query, records or recordpointer in any way (this class is cloned to do operations on)
	 * 
	 * WARNING: does a database action
	 * WARNING: works with FIELD_ID
	 * 
	 * @param string $sFieldOrAlias
	 * @param string $sValue
	 * @param string $sComparisonOperator
	 * @param string $sTable
     * @param integer $iColDataType TP_STRING, CT_VARCHAR, TP_INTEGER -> use this to define a data type when it is not specified in select() -> to prevent that SQL for joined fields is "WHERE courseid = '2'" (with quotes) instead of "WHERE courseid = '2'" which results in an error in MS Acces for example
     * @param string $sLogicalOperator
	 * @return bool true=exists   false=doesnt exist or error
	 */
	public function recordExistsTableDB($sField, $sValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sTable = '', $iColDataType = TP_UNDEFINED, $sLogicalOperator = LOGICAL_OPERATOR_AND)
	{
		$objClone = clone $this;
		$objClone->clear(true);

		$objClone->select(array($sField));
		$objClone->find($sField, $sValue, $sComparisonOperator, $sTable, $iColDataType, $sLogicalOperator);
		$objClone->limit(1);
		if (!$objClone->loadFromDB())
			return false;

		return ($objClone->count() > 0);
	}

	/**
	 * alias for find()
	 *  
	 * @param string $sFieldOrAlias
	 * @param string $sValue
	 * @param string $sComparisonOperator
	 * @param string $sTable
     * @param integer $iColDataType TP_STRING, CT_VARCHAR, TP_INTEGER -> use this to define a data type when it is not specified in select() -> to prevent that SQL for joined fields is "WHERE courseid = '2'" (with quotes) instead of "WHERE courseid = '2'" which results in an error in MS Acces for example
     * @param string $sLogicalOperator
	 * @return TSysModel
	 */	
	public function where($sFieldOrAlias, $sValue, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sTable = '', $iColDataType = TP_UNDEFINED, $sLogicalOperator = LOGICAL_OPERATOR_AND)
	{
		$this->find($sFieldOrAlias, $sValue, $sComparisonOperator, $sTable, $iColDataType, $sLogicalOperator);
			
		return $this;//make 'm stack baby
	}	

	/**
	 * the same as find(), but with the difference that it only searches for the id field
	 * 
	 * @param integer $iID
	 */
	public function findID($iID)
	{
		if (is_numeric($iID)) //only add when numeric
			$this->find(TSysModel::FIELD_ID, $iID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER, LOGICAL_OPERATOR_AND);
		
		return $this;//make 'm stack baby
	}
        
	/**
	 * the same as find(), but with the difference that it only searches for the random-id field
	 * 
	 * @param integer $iID
	 */        
	public function findRandomID($iID)
	{
		if (is_numeric($iID)) //only add when numeric
			$this->find(TSysModel::FIELD_RANDOMID, $iID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER, LOGICAL_OPERATOR_AND);
		
		return $this;//make 'm stack baby 
	}       
	
	/**
	 * the same as find(), but with the difference that it only searches for the unique-id field
	 * 
	 * @param string $sUniqueID
	 */        
	public function findUniqueID($sUniqueID)
	{
		if (isUniqueidRealValid($sUniqueID)) //only add when uniqueid is valid
			$this->find(TSysModel::FIELD_UNIQUEID, $sUniqueID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER, LOGICAL_OPERATOR_AND);
		
		return $this;//make 'm stack baby
	}  

	/**
	 * the same as find(), but with the difference that it only searches for the nice-id field
	 * 
	 * @param string $sNiceID
	 */        
	public function findNiceID($sNiceID)
	{
		if (isNiceIDValid($sNiceID)) //only add when uniqueid is valid
			$this->find(TSysModel::FIELD_NICEID, $sNiceID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER, LOGICAL_OPERATOR_AND);
		
		return $this;//make 'm stack baby
	}  	
	
	/**
	 * to prevent that we add two of the same fields to the $this->arrQBFind
	 * we can check if it already exists
	 * 
	 * this function is precise in what it needs: not only the field and table
	 * has to fit, but also the comparison operator.
	 * you may want to specify multiple conditions for one field
	 * 
	 * @param string $sField
	 * @return boolean true exists, false is not found in $this->arrQBFind
	 */
	private function findExists($sField, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $sTable = '')
	{
		if ($sTable == '')
			$sTable = $this::getTable();	            
	
		foreach ($this->arrQBFind as $arrFindItem)
		{
			if ($arrFindItem[TSysModel::QB_FINDINDEX_FIELD] == $sField)
			{
				if ($arrFindItem[TSysModel::QB_FINDINDEX_TABLE] == $sTable)
				{
					if ($arrFindItem[TSysModel::QB_FINDINDEX_COMPARISONOPERATOR] == $sComparisonOperator)    
						return true;
				}
			}
		}
		
		return false;
	}
        
	/**
	 * find record(s) in a subquery
	 *
	 * you can specify more than one find() in one query
	 *
	 * if function doesn't seem to work, maybe the $bForceFieldCheck detects a non-sortable field
	 * 
	 * @param string $sField
	 * @param TSysModel $objSubquery
	 * @param string $sComparisonOperator
     * @param string $sLogicalOperator
     * @param boolean $bForceFieldCheck if true checks against getFieldsSearchable(): find ONLY added if $sField exists in getFieldsSearchable()
	 * @return TSysModel
	 */	
	public function findSubquery($sField, $sValue, TSysModel $objSubquery, $sComparisonOperator = COMPARISON_OPERATOR_IN, $sLogicalOperator = LOGICAL_OPERATOR_AND, $bForceFieldCheck = false)
	{
            
		if ($bForceFieldCheck)
		{
			$arrSearchable = $objSubquery->getFieldsSearchable();
			if (in_array($sField, $arrSearchable))
			{
				$this->arrQBFind[] = array(
					TSysModel::QB_FINDINDEX_FIELD => $sField, 
					TSysModel::QB_FINDINDEX_SUBQUERYOBJECT => $objSubquery, 
					TSysModel::QB_FINDINDEX_COMPARISONOPERATOR => $sComparisonOperator, 
					TSysModel::QB_FINDINDEX_VALUE => $sValue,
					TSysModel::QB_FINDINDEX_VALUEEND => '',
					TSysModel::QB_FINDINDEX_LOGICALOPERATOR => $sLogicalOperator
				);
			}  
			else
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'TSysModel->findSubquery(): field '.$sField.' not searchable');                        
		}
		else
		{
			$this->arrQBFind[] = array(
				TSysModel::QB_FINDINDEX_FIELD => $sField, 
				TSysModel::QB_FINDINDEX_SUBQUERYOBJECT => $objSubquery, 
				TSysModel::QB_FINDINDEX_COMPARISONOPERATOR => $sComparisonOperator, 
				TSysModel::QB_FINDINDEX_VALUE => $sValue,
				TSysModel::QB_FINDINDEX_VALUEEND => '',
				TSysModel::QB_FINDINDEX_LOGICALOPERATOR =>  $sLogicalOperator						
			);
		}
                	
			
		return $this;//make 'm stack baby		
	}

	/**
	 * quicksearch 
	 * same as findLike() but every word os treated as a separate search-query and requested for every field $arrFields
	 * 
	 * no need to supply wildcards, they are added automatically
	 * 
	 * @param string $sSearchQuery without wildcards
	 * @param boolean $bUseFieldsJoinedTables automatically include the fields of the joined tables (the external classes have to be instantiated, be aware of the performance)
	 * @return TSysModel
	 */
	// public function findQuicksearch($sSearchQuery, $bUseFieldsJoinedTables = false,  $sLogicalOperator = LOGICAL_OPERATOR_AND)
	// {
	// 	$this->arrQBFindQuickSearch[] = array(
	// 			TSysModel::QB_FINDQUICKSEARCH_LOOKINJOINEDTABLES => $bUseFieldsJoinedTables, 
	// 			TSysModel::QB_FINDQUICKSEARCH_VALUE => $sSearchQuery,
	// 			TSysModel::QB_FINDQUICKSEARCH_LOGICALOPERATOR => $sLogicalOperator
	// 		);

	// 	return $this; //stack 'm
	// }


	/**
	 * quicksearch / fulltext search / fuzzy search over multiple database fields
	 * this meant to be findLike() 2.0 
	 * 
	 * 
	 * @param string $sSearchQuery without wildcards
	 * @param array $arrTableFields array with tables and fields. format: arrFields['table']=array(field1','field2'). Use '' for current table. When parameter omitted (or empty array) all fields form getFieldsSearchable are assumed.
	 * @return TSysModel
	 */	
	public function findQuicksearch($sSearchQuery, $iMaxLevelJoinDepth = 1)
	{

		//==== assemble array $this->arrQBFindQuickSearch 
		$this->arrQBFindQuickSearch[] = array(
				TSysModel::QB_FINDQUICKSEARCH_VALUE => $sSearchQuery,
				TSysModel::QB_FINDQUICKSEARCH_JOINDEPTH => $iMaxLevelJoinDepth,
			);

		return $this; //stack 'm
	}
	
	/**
	 * limit resultset to $iCount number of records.
	 * If you want only records 90-100, you have to define the offset 90 (count will be 10 in this example)
	 * 
	 * @param integer $iCount i.e. 1000 records. 0 means no limit
	 * @param integer $iOffset
	 * @return TSysModel
	 */
	public function limit($iCount, $iOffset = 0)
	{
		if (is_numeric($iCount))
			$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = $iCount;
		else
			$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = 0;
		
		if (is_numeric($iOffset))
			$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = $iOffset;
		else
			$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;
		
		return $this; //stack 'm
	}

	/**
	 * equivalent to limit(0);
	 * but it types faster
	 */
	public function limitNone()
	{
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = 0;
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;

		return $this; //stack m
	}
	
	/**
	 * return only one record in resultset
	 * 
	 * @return TSysModel
	 */
	public function limitOne()
	{
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = 1;
		$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = 0;

		return $this; //stack m
	}

	/**
	 * order result by field in ascending or descending order
	 * 
	 * you can specify more than one sort() :)
     * 
     * if function doesn't seem to work, maybe the $bForceFieldCheck detects a non-sortable field
	 * 
	 * @param string $sField
	 * @param string $sSortOrder SORT_ORDER_ASCENDING or SORT_ORDER_DESCENDING
	 * @param string $sTable if empty the default table getTable() will be assumed
     * @param boolean $bForceFieldCheck if true checks against getFieldsSortable(): sort ONLY added if $sField exists in getFieldsSortable() AND $objModel->getQBSelectFrom() is not empty 
	 * @return TSysModel
	 */
	public function sort($sField, $sSortOrder = SORT_ORDER_ASCENDING, $sTable = '', $bForceFieldCheck = false)
	{
		if ($sTable == '')
			$sTable = $this::getTable();

		if ($bForceFieldCheck)
		{
			$arrQBSelect = array(); // to determine on which column to sort
			$arrQBSelect = $objModel->getQBSelectFrom();   
			$iCountQBSelect = count($arrQBSelect);                    
			
			if (!$arrQBSelect)
			{
				error_log('skipped field check because $arrQBSelect is empty. that can because objModel->select() is not used. the database fields are automatically generated by executeSQL(). Error thrown in '.__METHOD__);
				$this->arrQBSort[] = array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);
			}
			else
			{
			
				//figure out the index of the sortcolumn
				for ($iSCICounter = 0; $iSCICounter < $iCountQBSelect; $iSCICounter++)
				{
					if ($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_FIELD] == $sField)
					{
						if (($arrQBSelect[$iSCICounter][TSysModel::QB_SELECTINDEX_TABLE] == $sTable) || $sTable == '') //tablenames of the current TSysModel are empty (and later replaced by the real tablename)
						{
							$this->arrQBSort[] = array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);
							$iSCICounter = $iCountQBSelect;//jump out of for loop, we found the sort column
						}
					}
				}   
			
			}
							
		}
		else
		{
			$this->arrQBSort[] = array(TSysModel::QB_SORTINDEX_TABLE => $sTable, TSysModel::QB_SORTINDEX_FIELD => $sField, TSysModel::QB_SORTINDEX_ORDER => $sSortOrder);
		}
		
		return $this; //stack m baby  
	}	
	
	/**
	 * alias for sort()
	 * 
     * if function doesn't seem to work, maybe the $bForceFieldCheck detects a non-sortable field
	 * 
	 * @param string $sField
	 * @param string $sSortOrder SORT_ORDER_ASCENDING or SORT_ORDER_DESCENDING
	 * @param string $sTable if empty the default table getTable() will be assumed
     * @param boolean $bForceFieldCheck if true checks against getFieldsSortable(): sort ONLY added if $sField exists in getFieldsSortable() AND $objModel->getQBSelectFrom() is not empty 
	 * @return TSysModel
	 */
	public function orderBy($sField, $sSortOrder = SORT_ORDER_ASCENDING, $sTable = '', $bForceFieldCheck = false)
	{
		$this->sort($sField, $sSortOrder, $sTable, $bForceFieldCheck);
	}

	/**
	 * inner join with an external/foreign table
	 * 
	 * @param string $sInternalTable
	 * @param string $sInternalField
	 * @param string $sExternalTable
	 * @param string $sExternalField
	 */
	public function join($sInternalTable, $sInternalField, $sExternalTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_TABLEEXTERNAL => $sExternalTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_INNER);
	}
	
	/**
	 * inner join with a subquery
	 *
	 * @param string $sInternalTable with '' $this->getTable() is used
	 * @param string $sInternalField
	 * @param TSysModel $objExternalSubqueryModel
	 * @param string $sExternalAliasSubqueryTable
	 * @param string $sExternalField
	 */
	public function joinSubquery($sInternalTable, $sInternalField, TSysModel $objExternalSubqueryModel, $sExternalAliasSubqueryTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_SUBQUERYOBJECT => $objExternalSubqueryModel, TSysModel::QB_JOININDEX_SUBQUERYTABLEALIAS => $sExternalAliasSubqueryTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_INNER);
	}	
	
	
	
	/**
	 * left join with an external/foreign table
	 *
	 * @param string $sInternalTable with '' $this->getTable() is used
	 * @param string $sExternalTable
	 * @param string $sExternalField
	 * @param string $sInternalField
	 */
	public function joinLeft($sInternalTable, $sInternalField, $sExternalTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_TABLEEXTERNAL => $sExternalTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_LEFT);
	}	
	
	/**
	 * left join with a subquery
	 * 
	 * @param string $sInternalTable with '' $this->getTable() is used
	 * @param string $sInternalField
	 * @param TSysModel $objExternalSubqueryModel
	 * @param string $sExternalAliasSubqueryTable
	 * @param string $sExternalField
	 */
	public function joinLeftSubquery($sInternalTable, $sInternalField, TSysModel $objExternalSubqueryModel, $sExternalAliasSubqueryTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_SUBQUERYOBJECT => $objExternalSubqueryModel, TSysModel::QB_JOININDEX_SUBQUERYTABLEALIAS => $sExternalAliasSubqueryTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_LEFT);
	}
	
	/**
	 * right join with an external table
	 *
	 * @param string $sInternalTable with '' $this->getTable() is used
	 * @param string $sExternalTable
	 * @param string $sExternalField
	 * @param string $sInternalField
	 */
	public function joinRight($sInternalTable, $sInternalField, $sExternalTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_TABLEEXTERNAL => $sExternalTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_RIGHT);
	}
		
	
	/**
	 * right join with a subquery
	 *
	 * @param string $sInternalTable with '' $this->getTable() is used
	 * @param string $sInternalField
	 * @param TSysModel $objExternalSubqueryModel
	 * @param string $sExternalAliasSubqueryTable
	 * @param string $sExternalField
	 */
	public function joinRightSubquery($sInternalTable, $sInternalField, TSysModel $objExternalSubqueryModel, $sExternalAliasSubqueryTable, $sExternalField)
	{
		$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $sInternalTable, TSysModel::QB_JOININDEX_FIELDINTERNAL => $sInternalField, TSysModel::QB_JOININDEX_SUBQUERYOBJECT => $objExternalSubqueryModel, TSysModel::QB_JOININDEX_SUBQUERYTABLEALIAS => $sExternalAliasSubqueryTable, TSysModel::QB_JOININDEX_FIELDEXTERNAL => $sExternalField, TSysModel::QB_JOININDEX_TYPE => JOIN_RIGHT);
	}	
	
	/**
	 * execute select query in database
	 * 
	 * you can specify a paginator here wich does a extra request to the database
	 * to count the results
	 * 
	 * you can specify field and table for the resultsetcount
	 * because with outerjoins or rightjoins the default id field  might not be available 
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 levels deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 * 				change: 15-11-2022: true formerly meant unlimited, now it means 1 level deep
	 * @param TPaginator $objPaginator if defined: 2 queries are actually preformed: one to count the results (that would have been the result of the query), 1 to actually perform the query with limited resultset
	 * @param string $sFieldResultsCount the field that is used to count the results, default the id field is used,  
	 * @param TSysModel $objModelResultsCount the model of $sFieldResultsCount that is used to count the results (default $this is used) 
	 */
	public function loadFromDB($mAutoJoinDefinedTables = 0, &$objPaginator = null, $sFieldResultsCount = TSysModel::FIELD_ID, $objModelResultsCount = null)
	{
		$this->clear(false); //--> only clear the resultset, not the query

		//if select fields empty, fill them
		$bEmptyFrom = false;
		if (!$this->arrQBSelectFrom)
		{
			$bEmptyFrom = true;
			$this->select($this->getFieldsDefined());
		}

		
		//auto join tables
		$iLevelsDeepJoin = 0; //default = no auto join
		if (is_int($mAutoJoinDefinedTables)) //is int, then copy
			$iLevelsDeepJoin = $mAutoJoinDefinedTables;
		if ($mAutoJoinDefinedTables === true) //is bool, false=0 by default
			$iLevelsDeepJoin = 1;

		if ($iLevelsDeepJoin != 0)
		{			
            $this->buildJoinArrayAutoJoin($this, $bEmptyFrom, $iLevelsDeepJoin);
		}
		
		
		
		
		//do extra database request for finding out how many records the query would return
		if ($objPaginator)
		{
			if ($objPaginator instanceof \dr\classes\dom\TPaginator)
			{
                            
				if ($objModelResultsCount == null)
					$objModelResultsCount = $this;
				
				$objClone = clone $this; //clone the model object including the query builder functionality we are gonna use
                $objClone->clear(false);
				$sCountAlias = '';
				$sCountAlias = 'countresults'.uniqid();
				$objClone->countResults($sFieldResultsCount, $sCountAlias, $objModelResultsCount);                               
				$objClone->loadFromDB(false); //false parameter: we do NOT apply the autojoin again, otherwise we have two inner join statements in the sql query
				$objPaginator->setTotalItemsCount($objClone->get($sCountAlias)); //set total resultcount								
				unset($objClone);//we dont need it anymore
				
				//setting other stuff for paginator
				$this->arrQBLimit[TSysModel::QB_LIMITINDEX_COUNT] = $objPaginator->getItemCountPerPage();
				$this->arrQBLimit[TSysModel::QB_LIMITINDEX_OFFSET] = $objPaginator->getItemRangeFrom();

				
			}	
		}
		
		return $this->getConnectionObject()->getPreparedStatement()->executeSelect($this);
	}
	
	/**
	 * load from database when you have the id
	 * The result is always one row
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_ID, id)
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	public function loadFromDBByID($iID, $mAutoJoinDefinedTables = 0)
	{
		if (is_numeric($iID)) //only add when numeric
		{
			$this->find(TSysModel::FIELD_ID, $iID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), CT_INTEGER64);
			$this->limitOne();
			return $this->loadFromDB($mAutoJoinDefinedTables);
		}
		else
		{
			logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Unique ID is not a valid Unique ID (determined by isUniqueidRealValid()). Might be the result of tampering with ids');
			return false;
		}		
	}	

	/**
	 * load from database when you have the unique id
	 * The result is always one row
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_UNIQUEID, id)
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 * 
	 * @todo test function. function written but never tested.
	 */	
	public function loadFromDBByUniqueID($sUniqueID, $mAutoJoinDefinedTables = 0)
	{
		if (isUniqueidRealValid($sUniqueID)) //only add when numeric
		{
			$this->find(TSysModel::FIELD_UNIQUEID, $sUniqueID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), CT_INTEGER64);
			$this->limitOne();
			return $this->loadFromDB($mAutoJoinDefinedTables);
		}
		else
		{
			logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Unique ID is not a valid Unique ID (determined by isUniqueidRealValid()). Might be the result of tampering with ids');
			return false;
		}
	}	

	/**
	 * load from database when you have a IPv4 or IPv6 IP-address
	 * The result can be multiple rows
	 * 
	 * it is a shortcut for:
	 * model->find($sField, $sIPAddress)
	 * model->loadfromdb()
	 * 
	 * this method is PROTECTED and has suffix 'internal', because you might want to expose 
	 * a more user friendly interface for the method name loadFromDBByIPAddress() 
	 * like one without the field name and depth parameters for example.
	 * 
	 * @param string $sField fieldname of the field where the ip address is stored
	 * @param string $sIPAddress IP address (either IPv4 or IPv6)
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	protected function loadFromDBByIPAddressInternal($sField, $sIPAddress, $mAutoJoinDefinedTables = 0)
	{
		if (filter_var($sIPAddress, FILTER_VALIDATE_IP))
		{
			$this->find($sField, $sIPAddress, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), CT_HEX);
			return $this->loadFromDB($mAutoJoinDefinedTables);		
		}
		else
		{
			logError( __CLASS__.': '.__FUNCTION__.': '.__LINE__, 'IP addres '.$sIPAddress.' is not a valid ip address');
			return false;
		}			

	}


	/**
	 * load from database the default record
	 * The result is always one row!
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_ISDEFAULT, true)
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	public function loadFromDBByIsDefault($mAutoJoinDefinedTables = 0)
	{
		$this->find(TSysModel::FIELD_ISDEFAULT, true, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_BOOL);
		$this->limitOne();
		return $this->loadFromDB($mAutoJoinDefinedTables);
	}		

	/**
	 * load from database the default record
	 * The result is always one row!
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_ISDEFAULT, true)
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	public function loadFromDBByIsFavorite($mAutoJoinDefinedTables = 0)
	{
		$this->find(TSysModel::FIELD_ISFAVORITE, true, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_BOOL);
		return $this->loadFromDB($mAutoJoinDefinedTables);
	}		

	/**
	 * load from database: the child node that has position number $iPosition
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_PARENTID. )
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	public function loadFromDBByPosition($iPosition, $mAutoJoinDefinedTables = 0)
	{
		$this->find(TTreeModel::FIELD_POSITION, $iPosition, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER);
		return $this->loadFromDB($mAutoJoinDefinedTables);
	}	


	/**
	 * is called from loadFromDB()
	 * 
	 * this function recursively includes all referenced tables and puts them 
	 * ont the $this->arrQBJoin array
	 * 
	 * @param TSysModel $objModel
	 * @param boolean $bIncludeAllFieldsInSelect include all
	 * @param int $iMaxLevelJoinDepth level of recursive joins allowed: -1=unlimited, 0=onlyCurrentLevel, 5=5LevelsDeep
	 */
	private function buildJoinArrayAutoJoin(&$objModel, $bIncludeAllFieldsInSelect, $iMaxLevelJoinDepth = -1)
	{
		$arrFields = array();
		$arrFields = $objModel->getFieldsDefined();
		foreach ($arrFields as $sField) //loop through all fields 
		{
			//find the fields with joins
			if (($objModel->getFieldForeignKeyField($sField)) && ($objModel->getFieldForeignKeyTable($sField)))
			{ 
				$this->arrQBJoin[] = array(TSysModel::QB_JOININDEX_TABLEINTERNAL => $objModel::getTable(), TSysModel::QB_JOININDEX_FIELDINTERNAL => $sField, TSysModel::QB_JOININDEX_TABLEEXTERNAL => $objModel->getFieldForeignKeyTable($sField), TSysModel::QB_JOININDEX_FIELDEXTERNAL => $objModel->getFieldForeignKeyField($sField), TSysModel::QB_JOININDEX_TYPE => $objModel->getFieldForeignKeyJoin($sField));

				$objExtModel = null;
				$sTempClass = '';
				$sTempClass = $objModel->getFieldForeignKeyClass($sField);
				$objExtModel = new $sTempClass;


				//if add all PUBLIC fields from foreign tables
				if ($bIncludeAllFieldsInSelect)
				{
					$this->select($objExtModel->getFieldsDefined(), $objExtModel); //@todo: getFieldsDefined is better because it includes all fields, but we can have ambigues column names
				}


				//recusive join
				//level: -1=unlimited, 0=onlyCurrentLevel/stopjoin, 5=5LevelsDeep
				if ($iMaxLevelJoinDepth != 0) //0=stop/skip, notZero=go
				{
					$iMaxLevelJoinDepth--;
					$this->buildJoinArrayAutoJoin($objExtModel, $bIncludeAllFieldsInSelect, $iMaxLevelJoinDepth);
				}
				
				unset($objExtModel);
				unset($sTempClass);
			}				
		}
		unset($arrFields);            
	}
        
	
	//END: ========= QUERY BUILDER =======
	
	/**
	 * initialise a record (row in internal array)
	 */
	private function initRecordInternal()
	{
		//system fields
		$this->setDirty(false);
		$this->setNew(true);
		
		
		//fill all the fields with the default defined values
		$arrFieldNames = $this->getFieldsDefined();		
		foreach ($arrFieldNames as $sFieldName)
		{	
			$iType = $this->getFieldType($sFieldName);
			switch ($iType)
			{
				case CT_DATETIME:
					$this->set($sFieldName, new TDateTime());
					break;
				case CT_CURRENCY:
					// $this->set($sFieldName, new TCurrency(0, $this->getFieldDecimalPrecision($sFieldName)));
					// break;
				case CT_DECIMAL:
					$this->set($sFieldName, new TDecimal(0, $this->getFieldDecimalPrecision($sFieldName)));
					break;
				default:
					$this->set($sFieldName, $this->getFieldDefaultValue($sFieldName));
			}
			
		}
		
		
		//id field		
		if ($this->getTableUseIDField())
			$this->setID(TSysModel::FIELD_ID_VALUE_DEFAULT);	
		
		//date time record changed created
		if ($this->getTableUseDateCreatedChangedField())
		{			
			//==date created
			$this->getDateCreated()->setTimestamp(time());
			
			//==date changed
			//$this->setDateChanged(new TDateTime(time())); -->updated with save
		}
				
		//record hidden / physical delete
		if (!$this->getTablePhysicalDeleteRecord())
			$this->setIsRecordHidden(false);
		
		//checksum
		if ($this->getTableUseChecksumField())
			$this->setChecksumEncrypted('');
		
		//order
		if ($this->getTableUseOrderField())
			$this->setPosition(0);
		
		//checkout
		if ($this->getTableUseCheckout())
			$this->setCheckoutExpires(new TDateTime());
		
	}
	
	/**
	 * defines database table for the default supplied fields
	 */
	protected function defineTableInternal()
	{

		//id field --> UNSIGNED BIGINT!!!!! --> in case of a table reference: these types must be the same
		if ($this->getTableUseIDField())
		{			
			$this->setFieldDefaultValue(TSysModel::FIELD_ID, null);
			$this->setFieldType(TSysModel::FIELD_ID, $this->getTableIDFieldType());
			$this->setFieldLength(TSysModel::FIELD_ID, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_ID, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_ID, true);
			$this->setFieldNullable(TSysModel::FIELD_ID, false);
			$this->setFieldEnumValues(TSysModel::FIELD_ID, null);
			$this->setFieldUnique(TSysModel::FIELD_ID, true);
			$this->setFieldIndexed(TSysModel::FIELD_ID, false);
			$this->setFieldFulltext(TSysModel::FIELD_ID, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_ID, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_ID, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_ID, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_ID, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_ID, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_ID, null);			
			$this->setFieldAutoIncrement(TSysModel::FIELD_ID, $this->getTableIDFieldType() == CT_AUTOINCREMENT);
			$this->setFieldUnsigned(TSysModel::FIELD_ID, true);		
			$this->setFieldEncryptionCypher(TSysModel::FIELD_ID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_ID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_ID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}
	
		//random id field --> UNSIGNED BIGINT!!!!! 
		if ($this->getTableUseRandomID())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldType(TSysModel::FIELD_RANDOMID, CT_INTEGER64);
			$this->setFieldLength(TSysModel::FIELD_RANDOMID, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_RANDOMID, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_RANDOMID, $this->getTableUseRandomIDAsPrimaryKey());
			$this->setFieldNullable(TSysModel::FIELD_RANDOMID, false);
			$this->setFieldEnumValues(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldUnique(TSysModel::FIELD_RANDOMID, true);
			$this->setFieldIndexed(TSysModel::FIELD_RANDOMID, false); //both unique and indexed is unnecessary
			$this->setFieldFulltext(TSysModel::FIELD_RANDOMID, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_RANDOMID, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_RANDOMID, false);
			$this->setFieldUnsigned(TSysModel::FIELD_RANDOMID, true);   
			$this->setFieldEncryptionCypher(TSysModel::FIELD_RANDOMID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_RANDOMID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_RANDOMID, '');  //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}

		//uniqueid field
		if ($this->getTableUseUniqueID())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldType(TSysModel::FIELD_UNIQUEID, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_UNIQUEID, 50);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_UNIQUEID, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_UNIQUEID, false);
			$this->setFieldNullable(TSysModel::FIELD_UNIQUEID, false);
			$this->setFieldEnumValues(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldUnique(TSysModel::FIELD_UNIQUEID, true);
			$this->setFieldIndexed(TSysModel::FIELD_UNIQUEID, false); //both unique and index is unnecessary
			$this->setFieldFulltext(TSysModel::FIELD_UNIQUEID, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_UNIQUEID, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_UNIQUEID, false);
			$this->setFieldUnsigned(TSysModel::FIELD_UNIQUEID, false);   
			$this->setFieldEncryptionCypher(TSysModel::FIELD_UNIQUEID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_UNIQUEID, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_UNIQUEID, '');  //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}

		//niceid field
		if ($this->getTableUseNiceID())
		{
			$this->setFieldDefaultsString(TSysModel::FIELD_NICEID, 100);
			$this->setFieldDefaultValue(TSysModel::FIELD_NICEID, null);
			$this->setFieldNullable(TSysModel::FIELD_UNIQUEID, false);
			$this->setFieldUnique(TSysModel::FIELD_UNIQUEID, true);
		}		

                
		//date time record changed created
		if ($this->getTableUseDateCreatedChangedField())
		{
			//==date created
			$this->setFieldDefaultValue(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldType(TSysModel::FIELD_RECORDCREATED, CT_DATETIME);
			$this->setFieldLength(TSysModel::FIELD_RECORDCREATED, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_RECORDCREATED, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_RECORDCREATED, false);
			$this->setFieldNullable(TSysModel::FIELD_RECORDCREATED, true);
			$this->setFieldEnumValues(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldUnique(TSysModel::FIELD_RECORDCREATED, false);
			$this->setFieldIndexed(TSysModel::FIELD_RECORDCREATED, false);
			$this->setFieldFulltext(TSysModel::FIELD_RECORDCREATED, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_RECORDCREATED, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_RECORDCREATED, null);
 			$this->setFieldAutoIncrement(TSysModel::FIELD_RECORDCREATED, false);
			$this->setFieldUnsigned(TSysModel::FIELD_RECORDCREATED, false);	
			$this->setFieldEncryptionCypher(TSysModel::FIELD_RECORDCREATED, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_RECORDCREATED, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_RECORDCREATED, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed

			
			//==date changed
			$this->setFieldDefaultValue(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldType(TSysModel::FIELD_RECORDCHANGED, CT_DATETIME);
			$this->setFieldLength(TSysModel::FIELD_RECORDCHANGED, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_RECORDCHANGED, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldNullable(TSysModel::FIELD_RECORDCHANGED, true);
			$this->setFieldEnumValues(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldUnique(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldIndexed(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldFulltext(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_RECORDCHANGED, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_RECORDCHANGED, false);
			$this->setFieldUnsigned(TSysModel::FIELD_RECORDCHANGED, false);	
			$this->setFieldEncryptionCypher(TSysModel::FIELD_RECORDCHANGED, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_RECORDCHANGED, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_RECORDCHANGED, '');	//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed							
		}
				
		//record hidden / physical delete
		if (!$this->getTablePhysicalDeleteRecord())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_RECORDHIDDEN, 0);
			$this->setFieldType(TSysModel::FIELD_RECORDHIDDEN, CT_BOOL);
			$this->setFieldLength(TSysModel::FIELD_RECORDHIDDEN, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_RECORDHIDDEN, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldNullable(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldEnumValues(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldUnique(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldIndexed(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldFulltext(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_RECORDHIDDEN, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_RECORDHIDDEN, false);
			$this->setFieldUnsigned(TSysModel::FIELD_RECORDHIDDEN, false);	
			$this->setFieldEncryptionCypher(TSysModel::FIELD_RECORDHIDDEN, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_RECORDHIDDEN, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_RECORDHIDDEN, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}
		
		//checksum
		if ($this->getTableUseChecksumField())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_CHECKSUMENCRYPTED, '');
			$this->setFieldType(TSysModel::FIELD_CHECKSUMENCRYPTED, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_CHECKSUMENCRYPTED, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_CHECKSUMENCRYPTED, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_CHECKSUMENCRYPTED, false);
			$this->setFieldNullable(TSysModel::FIELD_CHECKSUMENCRYPTED, true);
			$this->setFieldEnumValues(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldUnique(TSysModel::FIELD_CHECKSUMENCRYPTED, false);
			$this->setFieldIndexed(TSysModel::FIELD_CHECKSUMENCRYPTED, false);
			$this->setFieldFulltext(TSysModel::FIELD_CHECKSUMENCRYPTED, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_CHECKSUMENCRYPTED, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_CHECKSUMENCRYPTED, false);
			$this->setFieldUnsigned(TSysModel::FIELD_CHECKSUMENCRYPTED, false);	
			$this->setFieldEncryptionCypher(TSysModel::FIELD_CHECKSUMENCRYPTED, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_CHECKSUMENCRYPTED, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_CHECKSUMENCRYPTED, '');						//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}
		
		//order
		if ($this->getTableUseOrderField())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_POSITION, 0);
			$this->setFieldType(TSysModel::FIELD_POSITION, CT_INTEGER64);
			$this->setFieldLength(TSysModel::FIELD_POSITION, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_POSITION, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_POSITION, false);
			$this->setFieldNullable(TSysModel::FIELD_POSITION, false);
			$this->setFieldEnumValues(TSysModel::FIELD_POSITION, null);
			$this->setFieldUnique(TSysModel::FIELD_POSITION, false);//technically order is unique, but we want to be able to update the first than the second record (they can tempory have the same orderno). Also there can be an order for different categories.
			$this->setFieldIndexed(TSysModel::FIELD_POSITION, true);
			$this->setFieldFulltext(TSysModel::FIELD_POSITION, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_POSITION, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_POSITION, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_POSITION, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_POSITION, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_POSITION, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_POSITION, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_POSITION, false);
			$this->setFieldUnsigned(TSysModel::FIELD_POSITION, true);			
			$this->setFieldEncryptionCypher(TSysModel::FIELD_POSITION, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_POSITION, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_POSITION, '');	//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed			
		}
		
		//checkout
		if ($this->getTableUseCheckout())
		{
                        //date
			$this->setFieldDefaultValue(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldType(TSysModel::FIELD_CHECKOUTEXPIRES, CT_DATETIME);
			$this->setFieldLength(TSysModel::FIELD_CHECKOUTEXPIRES, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_CHECKOUTEXPIRES, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldNullable(TSysModel::FIELD_CHECKOUTEXPIRES, true);
			$this->setFieldEnumValues(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldUnique(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldIndexed(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldFulltext(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_CHECKOUTEXPIRES, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldUnsigned(TSysModel::FIELD_CHECKOUTEXPIRES, false);
			$this->setFieldEncryptionCypher(TSysModel::FIELD_CHECKOUTEXPIRES, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_CHECKOUTEXPIRES, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_CHECKOUTEXPIRES, '');				//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
                        
                        //source
			$this->setFieldDefaultValue(TSysModel::FIELD_CHECKOUTSOURCE, '');
			$this->setFieldType(TSysModel::FIELD_CHECKOUTSOURCE, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_CHECKOUTSOURCE, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_CHECKOUTSOURCE, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_CHECKOUTSOURCE, false);
			$this->setFieldNullable(TSysModel::FIELD_CHECKOUTSOURCE, true);
			$this->setFieldEnumValues(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldUnique(TSysModel::FIELD_CHECKOUTSOURCE, false);
			$this->setFieldIndexed(TSysModel::FIELD_CHECKOUTSOURCE, false);
			$this->setFieldFulltext(TSysModel::FIELD_CHECKOUTSOURCE, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_CHECKOUTSOURCE, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_CHECKOUTSOURCE, false);
			$this->setFieldUnsigned(TSysModel::FIELD_CHECKOUTSOURCE, false);    
			$this->setFieldEncryptionCypher(TSysModel::FIELD_CHECKOUTSOURCE, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_CHECKOUTSOURCE, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_CHECKOUTSOURCE, '');				               //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}		
                
		//lock
		if ($this->getTableUseCheckout())
		{
            //boolean: locked yes or no
			$this->setFieldDefaultValue(TSysModel::FIELD_LOCKED, 0);
			$this->setFieldType(TSysModel::FIELD_LOCKED, CT_BOOL);
			$this->setFieldLength(TSysModel::FIELD_LOCKED, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_LOCKED, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_LOCKED, false);
			$this->setFieldNullable(TSysModel::FIELD_LOCKED, false);
			$this->setFieldEnumValues(TSysModel::FIELD_LOCKED, null);
			$this->setFieldUnique(TSysModel::FIELD_LOCKED, false);
			$this->setFieldIndexed(TSysModel::FIELD_LOCKED, false);
			$this->setFieldFulltext(TSysModel::FIELD_LOCKED, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_LOCKED, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_LOCKED, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_LOCKED, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_LOCKED, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_LOCKED, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_LOCKED, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_LOCKED, false);
			$this->setFieldUnsigned(TSysModel::FIELD_LOCKED, false);
			$this->setFieldEncryptionCypher(TSysModel::FIELD_LOCKED, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_LOCKED, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_LOCKED, '');				//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
                        
                        //source
			$this->setFieldDefaultValue(TSysModel::FIELD_LOCKEDSOURCE, '');
			$this->setFieldType(TSysModel::FIELD_LOCKEDSOURCE, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_LOCKEDSOURCE, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_LOCKEDSOURCE, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_LOCKEDSOURCE, false);
			$this->setFieldNullable(TSysModel::FIELD_LOCKEDSOURCE, true);
			$this->setFieldEnumValues(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldUnique(TSysModel::FIELD_LOCKEDSOURCE, false);
			$this->setFieldIndexed(TSysModel::FIELD_LOCKEDSOURCE, false);
			$this->setFieldFulltext(TSysModel::FIELD_LOCKEDSOURCE, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_LOCKEDSOURCE, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_LOCKEDSOURCE, false);
			$this->setFieldUnsigned(TSysModel::FIELD_LOCKEDSOURCE, false);      
			$this->setFieldEncryptionCypher(TSysModel::FIELD_LOCKEDSOURCE, ''); //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_LOCKEDSOURCE, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_LOCKEDSOURCE, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}         
                
		
		if ($this->getTableUseImageFile())
		{
			//imagefile max
			$this->setFieldDefaultValue(TSysModel::FIELD_IMAGEFILE_MAX, '');
			$this->setFieldType(TSysModel::FIELD_IMAGEFILE_MAX, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_IMAGEFILE_MAX, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_IMAGEFILE_MAX, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_IMAGEFILE_MAX, false);
			$this->setFieldNullable(TSysModel::FIELD_IMAGEFILE_MAX, true);
			$this->setFieldEnumValues(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldUnique(TSysModel::FIELD_IMAGEFILE_MAX, false);
			$this->setFieldIndexed(TSysModel::FIELD_IMAGEFILE_MAX, false);
			$this->setFieldFulltext(TSysModel::FIELD_IMAGEFILE_MAX, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_IMAGEFILE_MAX, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_IMAGEFILE_MAX, false);
			$this->setFieldUnsigned(TSysModel::FIELD_IMAGEFILE_MAX, false);   
			$this->setFieldEncryptionCypher(TSysModel::FIELD_IMAGEFILE_MAX, '');
			$this->setFieldEncryptionDigest(TSysModel::FIELD_IMAGEFILE_MAX, '');
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_IMAGEFILE_MAX, '');
		        
			//imagefile large
			$this->setFieldDefaultValue(TSysModel::FIELD_IMAGEFILE_LARGE, '');
			$this->setFieldType(TSysModel::FIELD_IMAGEFILE_LARGE, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_IMAGEFILE_LARGE, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_IMAGEFILE_LARGE, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_IMAGEFILE_LARGE, false);
			$this->setFieldNullable(TSysModel::FIELD_IMAGEFILE_LARGE, true);
			$this->setFieldEnumValues(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldUnique(TSysModel::FIELD_IMAGEFILE_LARGE, false);
			$this->setFieldIndexed(TSysModel::FIELD_IMAGEFILE_LARGE, false);
			$this->setFieldFulltext(TSysModel::FIELD_IMAGEFILE_LARGE, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_IMAGEFILE_LARGE, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_IMAGEFILE_LARGE, false);
			$this->setFieldUnsigned(TSysModel::FIELD_IMAGEFILE_LARGE, false);   
			$this->setFieldEncryptionCypher(TSysModel::FIELD_IMAGEFILE_LARGE, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_IMAGEFILE_LARGE, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_IMAGEFILE_LARGE, '');				                      //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		        
			//medium imagefile 
			$this->setFieldDefaultValue(TSysModel::FIELD_IMAGEFILE_MEDIUM, '');
			$this->setFieldType(TSysModel::FIELD_IMAGEFILE_MEDIUM, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_IMAGEFILE_MEDIUM, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_IMAGEFILE_MEDIUM, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);
			$this->setFieldNullable(TSysModel::FIELD_IMAGEFILE_MEDIUM, true);
			$this->setFieldEnumValues(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldUnique(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);
			$this->setFieldIndexed(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);
			$this->setFieldFulltext(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_IMAGEFILE_MEDIUM, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);
			$this->setFieldUnsigned(TSysModel::FIELD_IMAGEFILE_MEDIUM, false);     
			$this->setFieldEncryptionCypher(TSysModel::FIELD_IMAGEFILE_MEDIUM, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_IMAGEFILE_MEDIUM, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_IMAGEFILE_MEDIUM, '');				                    //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		
			//small image file
			$this->setFieldDefaultValue(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, '');
			$this->setFieldType(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, CT_VARCHAR);
			$this->setFieldLength(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, 255);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);
			$this->setFieldNullable(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, true);
			$this->setFieldEnumValues(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldUnique(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);
			$this->setFieldIndexed(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);
			$this->setFieldFulltext(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);
			$this->setFieldUnsigned(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, false);     
			$this->setFieldEncryptionCypher(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, '');				                    //I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed

			//alt-field
			$this->setFieldDefaultsVarChar(TSysModel::FIELD_IMAGE_ALT, 100);

			//image dimensions
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_MAX_WIDTH);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_MAX_HEIGHT);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_LARGE_WIDTH);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_LARGE_HEIGHT);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_MEDIUM_WIDTH);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_MEDIUM_HEIGHT);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_THUMBNAIL_WIDTH);
			$this->setFieldDefaultsInteger(TSysModel::FIELD_IMAGE_THUMBNAIL_HEIGHT);
		}
                
		//use translation
		if ($this->getTableUseTranslationLanguageID())
		{
			//language
			$this->setFieldDefaultsIntegerForeignKey(TSysModel::FIELD_TRANSLATIONLANGUAGEID, TSysLanguages::class, TSysLanguages::getTable(), TSysModel::FIELD_ID);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_TRANSLATIONLANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_TRANSLATIONLANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		}

		//use isdefault-field
		if ($this->getTableUseIsDefault())
		{
			$this->setFieldDefaultValue(TSysModel::FIELD_ISDEFAULT, 0);
			$this->setFieldType(TSysModel::FIELD_ISDEFAULT, CT_BOOL);
			$this->setFieldLength(TSysModel::FIELD_ISDEFAULT, 0);
			$this->setFieldDecimalPrecision(TSysModel::FIELD_ISDEFAULT, 0);
			$this->setFieldPrimaryKey(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldNullable(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldEnumValues(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldUnique(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldIndexed(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldFulltext(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldForeignKeyClass(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldForeignKeyTable(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldForeignKeyField(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldForeignKeyJoin(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldForeignKeyActionOnUpdate(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldForeignKeyActionOnDelete(TSysModel::FIELD_ISDEFAULT, null);
			$this->setFieldAutoIncrement(TSysModel::FIELD_ISDEFAULT, false);
			$this->setFieldUnsigned(TSysModel::FIELD_ISDEFAULT, false);	
			$this->setFieldEncryptionCypher(TSysModel::FIELD_ISDEFAULT, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionDigest(TSysModel::FIELD_ISDEFAULT, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
			$this->setFieldEncryptionPassphrase(TSysModel::FIELD_ISDEFAULT, '');//I could use $this->setFieldEncryptionDisabled() , but this calls the 3 methods, calling them here in TSysModel is a bit quicker because they are ALWAUS executed
		}

		//use isfavorite-field
		if ($this->getTableUseIsFavorite())
		{
			$this->setFieldDefaultsBoolean(TSysModel::FIELD_ISFAVORITE);
		}

		//use isintrashcan-field
		if ($this->getTableUseTrashcan())
		{
			$this->setFieldDefaultsBoolean(TSysModel::FIELD_ISINTRASHCAN);
		}

		//use search keywords field
		if ($this->getTableUseSearchKeywords())
		{
			$this->setFieldDefaultsVarChar(TSysModel::FIELD_SEARCHKEYWORDS, 255);
			$this->setFieldFulltext(TSysModel::FIELD_SEARCHKEYWORDS, true);
		}
	} 
	
	/**
	 * array in format:
	 * mixed mysqli_result::fetch_array ([ int $resulttype = MYSQLI_ASSOC ] )
	 */
//	public function setInternalData($arrData)
//	{
//		$this->arrData = $arrData;
//		$this->iCachedCountArrData = count($arrData);
//	}
        
	/**
	 */
	public function setInternalDataNew(&$arrRows)
	{
		$this->arrDataNew = $arrRows;
		$this->iCachedCountArrData = count($arrRows);
	}        

	/**
	 * function to copy all the data from the original object of the joined table object
	 * THIS object is the joined table, you have to supply the original model as parameter
	 */
	public function setInternalDataJoinedTable(&$objOriginalModel)
	{
		$sTableNameCurrent = '';
		$sTableNameCurrent = $this::getTable();
		$arrInteralDataJoined = $objOriginalModel->getInternalData();
		$iCountRows = $objOriginalModel->count();
		$arrNewRow = array();
		
		//because the internal tablename in array is always empty we need to copy instead of setting the internal array
		$this->clear();                
		
		for ($iRowIndex = 0;$iRowIndex < $iCountRows; $iRowIndex++)
		{                    
			$arrNewRow[''] = $arrInteralDataJoined[$iRowIndex][$sTableNameCurrent];
			$this->arrDataNew[] = $arrNewRow;
		}
		
		//new
		$arrNewRow[''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_VALUE] = false;
		$arrNewRow[''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_FIELDNAMEORIGINAL] = TSysModel::FIELD_SYS_NEW;
		$arrNewRow[''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_TABLENAME] = $sTableNameCurrent;
		$arrNewRow[''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_TABLENAMEORIGINAL] = $sTableNameCurrent;
		$this->arrDataNew[] = $arrNewRow;
		
		//dirty
		$arrNewRow[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE] = false;
		$arrNewRow[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_FIELDNAMEORIGINAL] = TSysModel::FIELD_SYS_DIRTY;
		$arrNewRow[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_TABLENAME] = $sTableNameCurrent;
		$arrNewRow[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_TABLENAMEORIGINAL] = $sTableNameCurrent;
		$this->arrDataNew[] = $arrNewRow;                
                
                //update count
		$this->iCachedCountArrData = $iCountRows;
	}  
        
        protected function getInternalData()
        {
                return $this->arrDataNew;
        }
        
        /**
         * get used tables in resultset.
         * default is only one table when no table joins
         * 
         * current table is returned as an empty string
         */
        public function getTablesResulset()
        {
                if ($this->iCachedCountArrData == 0) //if no resultset returned
                {
                        return array('');
                }
                else
                {
                        return array_keys($this->arrDataNew[$this->iRecordPointer]);
                }
        }
        
	/**
	 * get data from field.
	 * 
	 * @param string $sFieldName name of the field
	 * @param string $sDBTable database table, default is current TSysModel table
	 * @param bool $bDecryptData decrypt data from field when it is an encrypted field. Can only decrypt data from current table (not referenced tables)
	 * @return mixed
	 */
	public function get($sFieldName, $sDBTable = '', $bDecryptData = false)
	{
		if (APP_DEBUGMODE)
		{
			// logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $sDBTable.''.$sFieldName);

			if ($this->arrDataNew)
			{				
				if (!isset($sFieldName, $this->arrDataNew[$this->iRecordPointer][$sDBTable]))
				{
					logError(__FILE__.__LINE__,'database field "'.$sFieldName.'" does not exist in table "'.$sDBTable.'". Could be referring to the wrong table');					

					if (($sDBTable != '') && ($sDBTable == $this::getTable()))
						logError(__FILE__.__LINE__,'regarding previous error: you set database table "'.$sDBTable.'" explicitly, while you should have set it implicitly with by leaving the parameter of get() empty.');					

					return;
				}
			}
			else
			{
				logError(__FILE__.__LINE__,'requesting field "'.$sFieldName.'" but $this->arrDataNew == null');
				return;
			}
		}
			
		if ($bDecryptData)
		{
			if ($sDBTable == '') //it only supports current table
			{
				if ($this->getFieldEncryptionEnabled($sFieldName))
				{				
					return decrypt($this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE], $this->getFieldEncryptionPassphrase($sFieldName), $this->getFieldEncryptionCypher($sFieldName), $this->getFieldEncryptionDigest($sFieldName));
				}
			}
				

			//@todo: support for external fields, by instantiating the external class and calling the encryption from there
		}

		//for performance reasons:
		//I don't want to do an extra check if $sDBTable == $this::getTable();
		
		return $this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE];
	}
        
	/**
	 * return value as int
	 * 
	 * @param string $sFieldName
	 * @return int
	 */
	public function getAsInt($sFieldName, $sDBTable = '')
	{
		return (int)$this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE];
	}
	
	/**
	 * return value as int 
	 * 
	 * @param string $sFieldName
	 * @return int
	 * @deprecated use getAsInt() instead 15-11-2024
	 */
	public function getInt($sFieldName, $sDBTable = '')
	{
		return (int)$this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE];
	}

	/**
	 * get date as string
	 * 
	 * @param $sFieldName string name of the field
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function getDateAsString($sFieldName, $sPHPDateFormat = '')
	{
		return $this->get($sFieldName)->getDateAsString($sPHPDateFormat);
	}

	/**
	 * get time as string
	 * 
	 * @param $sFieldName string name of the field
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function getTimeAsString($sFieldName, $sPHPDateFormat = '')
	{
		return $this->get($sFieldName)->getTimeAsString($sPHPDateFormat);
	}
	
	/**
	 * get datetime as string
	 * 
	 * @param $sFieldName string name of the field
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function getDateTimeAsString($sFieldName, $sPHPDateFormat = '')
	{
		return $this->get($sFieldName)->getDateTimeAsString($sPHPDateFormat);
	}        
        
	/**
	 * set data for field
	 * 
	 * @param string $sFieldName fieldname (you can use the constants defined in model class)
	 * @param mixed $mData 
	 * @param string $sDBTable database table, empty means current database table
	 * @param bool $bEncryptData encrypt data (only supported on current table)
	 * @return void
	 */
	public function set($sFieldName, $mData, $sDBTable = '', $bEncryptData = false)
	{
		if ($this->iCachedCountArrData == 0)//make a new record when this one is empty
			$this->newRecord();

		if ($bEncryptData)
		{
			if ($sDBTable == '') //it only supports current table
			{
				if ($this->getFieldEncryptionEnabled($sFieldName))
				{				
					$mData = encrypt($mData, $this->getFieldEncryptionPassphrase($sFieldName), $this->getFieldEncryptionCypher($sFieldName), $this->getFieldEncryptionDigest($sFieldName));
				}
			}
			//@todo: support for external fields, by instantiating the external class and calling the encryption from there
		}

		$this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE] = $mData;
		$this->setDirty();
	}
	
	/**
	 * compares decrypted value with internal encrypted value
	 *
	 * @param string $sField
	 * @return boolean true if match, false no match
	 */
	public function isMatchUncryptedValue($sFieldName, $sDecryptedValue, $sDBTable = '')
	{
		return (($this->get($sFieldName, $sDBTable, true)) == $sDecryptedValue);
	}


	/**
	 * exactly the same as set(), but for TDateTime
	 * 
	 * @param $sFieldName string fieldname (you can use the constants defined in model class)
	 * @param $objDateTime TDateTime if null, TDateTime() is assumed
	 */        
	public function setTDateTime($sFieldName, $objDateTime = null)
	{                    
		if (!$objDateTime)
				$objDateTime = new TDateTime();
		
		if ($objDateTime instanceof TDateTime)
		{
				$this->set($sFieldName, $objDateTime);
		}            
		else
				error_log(__CLASS__.':'.__FUNCTION__.'$objDateTime is not of type TDateTime');
	}        

	/**
	 * set date in the format of a string
	 * 
	 * @param type $sFieldName
	 * @param type $sDateAsString
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function setDateAsString($sFieldName, $sDateAsString, $sPHPDateFormat = '')
	{           
		$objDateTime = null;
		$objDateTime = $this->get($sFieldName);
		
		if (!$objDateTime)
		{
			$objDateTime = new TDateTime();
			$this->set($sFieldName, $objDateTime);
		}
		
		//TDateTime()
		if ($objDateTime instanceof TDateTime)
		{
			$objDateTime->setDateAsString($sDateAsString, $sPHPDateFormat);
		}            
		else
			error_log(__CLASS__.':'.__FUNCTION__.'$objDateTime is not of type TDateTime');
	}           
        
	/**
	 * set time in the format of a string
	 * 
	 * @param type $sFieldName
	 * @param type $sTimeAsString
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function setTimeAsString($sFieldName, $sTimeAsString, $sPHPDateFormat = '')
	{
		$objDateTime = null;
		$objDateTime = $this->get($sFieldName);
		
		if (!$objDateTime)
		{
			$objDateTime = new TDateTime();
			$this->set($sFieldName, $objDateTime);
		}
		
		//TDateTime()
		if ($objDateTime instanceof TDateTime)
		{
			$objDateTime->setTimeAsString($sTimeAsString, $sPHPDateFormat);
		}            
		else
			error_log(__CLASS__.':'.__FUNCTION__.'$objDateTime is not of type TDateTime');
	}           
        
        
	/**
	 * set time & time in the format of a string
	 * 
	 * @param type $sFieldName
	 * @param type $sDateTimeAsString
	 * @param string $sPHPDateFormat like 'd-m-Y' default is '' and assumes TLocalisation::DATEFORMAT_DEFAULT
	 */
	public function setDateTimeimeAsString($sFieldName, $sDateTimeAsString, $sPHPDateFormat = '')
	{
		$objDateTime = null;
		$objDateTime = $this->get($sFieldName);
		
		if (!$objDateTime)
		{
			$objDateTime = new TDateTime();
			$this->set($sFieldName, $objDateTime);
		}
		
		//TDateTime()
		if ($objDateTime instanceof TDateTime)
		{
			$objDateTime->setTimeAsString($sDateTimeAsString, $sPHPDateFormat);
		}            
		else
			error_log(__CLASS__.':'.__FUNCTION__.'$objDateTime is not of type TDateTime');
	}   
        
	/**
	 * exactly the same as set(), but for TCurrency
	 * 
	 * @param $sFieldName string fieldname (you can use the constants defined in model class)
	 * @param $objCurrency TCurrency if null TCurrency() is assumed
	 */        
	// public function setTCurrency($sFieldName, $objCurrency = null)
	// {                        
	// 	if (!$objCurrency)
	// 			$objCurrency = new TCurrency();
	// 	if ($objCurrency instanceof TCurrency)
	// 	{
	// 			$this->set($sFieldName, $objCurrency);
	// 	}            
	// 	else
	// 			error_log(__CLASS__.':'.__FUNCTION__.'$objCurrency is not of type TCurrency');
	// }     
        
	/**
	 * exactly the same as set(), but for TFloat
	 * 
	 * @param $sFieldName string fieldname (you can use the constants defined in model class)
	 * @param $objFloat TFloat if null TFloat() is assumed
	 */        
	public function setTFloat($sFieldName, $objFloat = null)
	{                        
		if (!$objFloat)
				$objFloat = new TFloat();
		if ($objFloat instanceof TFloat)
		{
				$this->set($sFieldName, $objFloat);
		}            
		else
				error_log(__CLASS__.':'.__FUNCTION__.'$objFloat is not of type TFloat');
	}     
        
	/**
	 * exactly the same as set(), but for TDecimal
	 * 
	 * @param $sFieldName string fieldname (you can use the constants defined in model class)
	 * @param $objDecimal TDecimal if null TDecimal() is  assumed
	 */        
	public function setTDecimal($sFieldName, $objDecimal = null)
	{                        
		if (!$objDecimal)
				$objDecimal = new TDecimal();
		if ($objDecimal instanceof TDecimal)
		{
				$this->set($sFieldName, $objDecimal);
		}            
		else
				error_log(__CLASS__.':'.__FUNCTION__.'$objDecimal is not of type TDecimal');
	}           
        
	/**
	 * exactly the same as set(), but for TInteger
	 * 
	 * @param $sFieldName string fieldname (you can use the constants defined in model class)
	 * @param $objInteger TInteger if null TInteger() is  assumed
	 */        
	public function setTInteger($sFieldName, $objInteger = null)
	{                        
		if (!$objInteger)
				$objInteger = new TInteger();
		if ($objInteger instanceof TInteger)
		{
				$this->set($sFieldName, $objInteger);
		}            
		else
				error_log(__CLASS__.':'.__FUNCTION__.'$objInteger is not of type TDecimal');
	}        
	

	/**
	 * defines that you want to start with a new record.
	 * 
	 * $objModel->newRecord()
	 * $objModel->setFirstName('harry');
	 * $objModel->setLastName('leMonde');
	 * $objModel->saveToDB();
         * @return TSysModel so you can stack
	 */
	public function newRecord($arrDefaults = null)
	{
		++$this->iCachedCountArrData;
		$this->iRecordPointer = $this->iCachedCountArrData -1;
		
		if ($arrDefaults === null)
		{
			$this->arrDataNew[] = array();
			$this->initRecordInternal();
			$this->initRecord();				
		}
		else
        {
			$this->arrDataNew[] = $arrDefaults;
        }
		
        return $this;
	}
	
	
	/**
	 * get dirty
	 * 
	 * (it takes the referenced foreign key objects into account)
	 */
	public function getDirty()
	{
//		foreach ($this->arrData as $arrRecord)
		foreach ($this->arrDataNew as $arrRecord)
		{
//			if ($arrRecord[TSysModel::FIELD_SYS_DIRTY])
			if ($arrRecord[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE])
				return true;
		}
			
//		return $this->arrData[$this->iRecordPointer][TSysModel::FIELD_SYS_DIRTY];
		return $this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE];
	}
	
	/**
	 * if one of the records in the internal array is dirty it will return true
	 */
	public function getDirtyAll()
	{
//		foreach ($this->arrData as $arrRecord)
//			if ($arrRecord[TSysModel::FIELD_SYS_DIRTY])
//				return true;
		foreach ($this->arrDataNew as $arrRecord)
			if ($arrRecord[''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE])
				return true;
			
		return false;
	}
	
	/**
	 * set dirty
	 *
	 * @param bool $iID
	 */
	public function setDirty($bRecordChanged = true)
	{
//		if (is_bool($bRecordChanged))
//			$this->arrData[$this->iRecordPointer][TSysModel::FIELD_SYS_DIRTY] = $bRecordChanged;
//		else
//			$this->arrData[$this->iRecordPointer][TSysModel::FIELD_SYS_DIRTY] = true;
		if (is_bool($bRecordChanged))
			$this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE] = $bRecordChanged;
		else
			$this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_DIRTY][TSysModel::DATA_VALUE] = true;
	}	
	
	/**
	 * get new
	 */
	public function getNew()
	{
		return $this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_VALUE];
	}
	
	/**
	 * if one of the records in the internal array is new it will return true
	 */
	public function getNewAll()
	{
		foreach ($this->arrDataNew as $arrRecord)
			if ($arrRecord[''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_VALUE])
				return true;
			
		return false;
	}	
	
	/**
	 * set new
	 *
	 * @param bool $bRecordNew
	 */
	public function setNew($bRecordNew = true)
	{		
		if (is_bool($bRecordNew))
			$this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_VALUE] = $bRecordNew;
		else
			$this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_SYS_NEW][TSysModel::DATA_VALUE] = true;
	}	
	
	
	/**
	 * get the id
	 */
	public function getID()
	{
//		return $this->arrData[$this->iRecordPointer][TSysModel::FIELD_ID];
		return $this->arrDataNew[$this->iRecordPointer][''][TSysModel::FIELD_ID][TSysModel::DATA_VALUE];
	}
	
	/**
	 * set id
	 *
	 * @param integer $iID
	 */
	public function setID($iID)
	{
		if (is_numeric($iID))
			$this->set(TSysModel::FIELD_ID, $iID);
		else
			$this->set(TSysModel::FIELD_ID, TSysModel::FIELD_ID_VALUE_DEFAULT);
	}

	/**
	 * set random id
	 * (as unique is as the regular id, but the id is not in succession
	 * so id=3 will not followup with id=4) 
	 * 
	 * @return int
	 */
	public function getRandomID()
	{
		return $this->get(TSysModel::FIELD_RANDOMID);
	}

	/**
	 * set random id
	 * (as unique as the regular id, but the id is not in succession
	 * so id=3 will not followup with id=4)
	 * 
	 * @param int $iID
	 */
	public function setRandomID($iID)
	{
		$this->set(TSysModel::FIELD_RANDOMID, $iID);
	} 


	/**
	 * get unique id
	 * (uniqueid is unique as the regular id, but it is a string)
	 * 
	 * @return string
	 */
	public function getUniqueID()
	{
		return $this->get(TSysModel::FIELD_UNIQUEID);
	}

	/**
	 * set unique id
	 * (uniqueid is unique as the regular id, but it is a string)
	 * 
	 * @param string $sUniqueID
	 */
	public function setUniqueID($sUniqueID)
	{
		$this->set(TSysModel::FIELD_UNIQUEID, $sUniqueID);
	} 	
    
	/**
	 * get nice id
	 * 
	 * @return string
	 */
	public function getNiceID()
	{
		return $this->get(TSysModel::FIELD_NICEID);
	}

	/**
	 * set nice id
	 * 
	 * @param string $sNiceID
	 */
	public function setNiceID($sNiceID)
	{
		$this->set(TSysModel::FIELD_NICEID, $sNiceID);
	} 	

	/**
	 * gets if the record is deleted-flag (when not fysically deleted)
	 *
	 * @return boolean
	 */
	public function getIsRecordHidden()
	{
		return $this->get(TSysModel::FIELD_RECORDHIDDEN);
	}
	
	/**
	 * sets if the record is deleted-flag (when not fysically deleted)
	 *
	 * @param boolean $bIsDeleted
	 */
	public function setIsRecordHidden($bIsDeleted)
	{
		$this->set(TSysModel::FIELD_RECORDHIDDEN, $bIsDeleted);
	}
	
	public function setPosition($iOrder)
	{
		$this->set(TSysModel::FIELD_POSITION, $iOrder);
	}
	
	public function getPosition()
	{
		return $this->get(TSysModel::FIELD_POSITION);
	}
	
	/**
	 * default the datetime object is null.
	 * when null the default
	 *
	 * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set, so it is always expired
	 */
	public function setCheckoutExpires($objDateTime = null)
	{
        $this->setTDateTime(TSysModel::FIELD_CHECKOUTEXPIRES, $objDateTime);
	}
	
	public function getCheckoutExpires()
	{
		return $this->get(TSysModel::FIELD_CHECKOUTEXPIRES);
	}
        
	/**
	 * Who or what checkout out?
	 * 
	 * @param string $sCheckoutSource
	 */
	public function setCheckoutSource($sCheckoutSource = '')
	{
		$this->set(TSysModel::FIELD_CHECKOUTSOURCE, $sCheckoutSource);
	}
	
	/**
	 * Who or what checkout out?
	 * 
	 * @return string 
	 */        
	public function getCheckoutSource()
	{
		return $this->get(TSysModel::FIELD_CHECKOUTSOURCE);
	}

	/**
	 * will return true if:
	 * the checkout dat is in the future
	 * 
	 */
	public function getCheckedOut()
	{
		return $this->getCheckoutExpires()->isInTheFuture();
	}
	
	/**
	 * set a file to locked (or unlocked)
	 * 
	 * @param boolean $bLocked
	 */
	public function setLocked($bLocked)
	{
		if ($bLocked) //this way always results in a proper boolean set internally
			$this->set(TSysModel::FIELD_LOCKED, true);
		else
			$this->set(TSysModel::FIELD_LOCKED, false);
	}
	
	/**
	 * is a file locked?
	 * 
	 * @return boolean 
	 */
	public function getLocked()
	{
		return $this->get(TSysModel::FIELD_LOCKED);
	}
	
	/**
	 * Who or what locked the record?
	 * 
	 * @param string $sSource
	 */
	public function setLockedSource($sSource)
	{
		$this->set(TSysModel::FIELD_LOCKEDSOURCE, $sSource);
	}
	
	/**
	 * Who or what locked the record?
	 * 
	 * @return string
	 */        
	public function getLockedSource()
	{
		return $this->get(TSysModel::FIELD_LOCKEDSOURCE);
	}        
	
	/**
	 * get the date when the record has changed in database-table
	 *
	 *
	 * @return TDateTime
	 */
	public function getDateChanged()
	{
		return $this->get(TSysModel::FIELD_RECORDCHANGED);
	}
	
	/**
	 * set the date when the record has changed
	 * (this happens automatically on saving and will be ovewritten on save)
	 *
	 * @param TDateTime $objDateTime
	 */
	public function setDateChanged(TDateTime $objDateTime)
	{
        $this->setTDateTime(TSysModel::FIELD_RECORDCHANGED, $objDateTime);		
	}
	
	/**
	 * get the date when the record is created
	 *
	 * @return TDateTime
	 */
	public function getDateCreated()
	{
		return $this->get(TSysModel::FIELD_RECORDCREATED);
	}
	
	/**
	 * set the date when the record is created
	 * (this happens automatically on saving and will be ovewritten on save)
	 *
	 * @param TDateTime $objDateTime
	 */
	public function setDateCreated(TDateTime $objDateTime)
	{
        $this->setTDateTime(TSysModel::FIELD_RECORDCREATED, $objDateTime);                
	}
	
	
	/**
	 * set checksum
	 *
	 * @param string $sChecksum
	 */
	public function setChecksumEncrypted($sChecksum)
	{
		$this->set(TSysModel::FIELD_CHECKSUMENCRYPTED, $sChecksum);
	}
	
	
	/**
	 * get checksum
	 *
	 * @return string
	 */
	public function getChecksumEncrypted()
	{
		return $this->get(TSysModel::FIELD_CHECKSUMENCRYPTED);
	}                

	public function getTranslationLanguageID()
	{
		return $this->get(TSysModel::FIELD_TRANSLATIONLANGUAGEID);
	}

	public function setTranslationLanguageID($iLangID)
	{
		$this->set(TSysModel::FIELD_TRANSLATIONLANGUAGEID, $iLangID);
	}     

	public function getImageFileMax()
	{
		return $this->get(TSysModel::FIELD_IMAGEFILE_MAX);
	}

	public function setImageFileMax($sPath)
	{
		$this->set(TSysModel::FIELD_IMAGEFILE_MAX, $sPath);
	}        

	public function getImageFileLarge()
	{
		return $this->get(TSysModel::FIELD_IMAGEFILE_LARGE);
	}

	public function setImageFileLarge($sPath)
	{
		$this->set(TSysModel::FIELD_IMAGEFILE_LARGE, $sPath);
	}        

	public function getImageFileMedium()
	{
		return $this->get(TSysModel::FIELD_IMAGEFILE_MEDIUM);
	}

	public function setImageFileMedium($sPath)
	{
		$this->set(TSysModel::FIELD_IMAGEFILE_MEDIUM, $sPath);
	}     

	public function getImageFileThumbnail()
	{
		return $this->get(TSysModel::FIELD_IMAGEFILE_THUMBNAIL);
	}

	public function setImageFileThumbnail($sPath)
	{
		$this->set(TSysModel::FIELD_IMAGEFILE_THUMBNAIL, $sPath);
	} 

	/**
	 * sets alt text for image
	 * <img alt="mountain">
	 */
	public function getImageAlt()
	{
		return $this->get(TSysModel::FIELD_IMAGE_ALT);
	}

	/**
	 * gets alt text for image
	 * <img alt="mountain">
	 */	
	public function setImageAlt($sAltText)
	{
		$this->set(TSysModel::FIELD_IMAGE_ALT, $sAltText);
	}        
	
	public function getImageMaxWidth()
	{
		return $this->get(TSysModel::FIELD_IMAGE_MAX_WIDTH);
	}

	public function setImageMaxWidth($iWidth)
	{
		$this->set(TSysModel::FIELD_IMAGE_MAX_WIDTH, $iWidth);
	} 

	public function getImageMaxHeight()
	{
		return $this->get(TSysModel::FIELD_IMAGE_MAX_HEIGHT);
	}

	public function setImageMaxHeight($iHeight)
	{
		$this->set(TSysModel::FIELD_IMAGE_MAX_HEIGHT, $iHeight);
	} 

	public function getImageLargeWidth()
	{
		return $this->get(TSysModel::FIELD_IMAGE_LARGE_WIDTH);
	}

	public function setImageLargeWidth($iWidth)
	{
		$this->set(TSysModel::FIELD_IMAGE_LARGE_WIDTH, $iWidth);
	} 

	public function getImageLargeHeight()
	{
		return $this->get(TSysModel::FIELD_IMAGE_LARGE_HEIGHT);
	}

	public function setImageLargeHeight($iHeight)
	{
		$this->set(TSysModel::FIELD_IMAGE_LARGE_HEIGHT, $iHeight);
	} 

	public function getImageMediumWidth()
	{
		return $this->get(TSysModel::FIELD_IMAGE_MEDIUM_WIDTH);
	}

	public function setImageMediumWidth($iWidth)
	{
		$this->set(TSysModel::FIELD_IMAGE_MEDIUM_WIDTH, $iWidth);
	} 

	public function getImageMediumHeight()
	{
		return $this->get(TSysModel::FIELD_IMAGE_MEDIUM_HEIGHT);
	}

	public function setImageMediumHeight($iHeight)
	{
		$this->set(TSysModel::FIELD_IMAGE_MEDIUM_HEIGHT, $iHeight);
	} 

	public function getImageThumbnailWidth()
	{
		return $this->get(TSysModel::FIELD_IMAGE_THUMBNAIL_WIDTH);
	}

	public function setImageThumbnailWidth($iWidth)
	{
		$this->set(TSysModel::FIELD_IMAGE_THUMBNAIL_WIDTH, $iWidth);
	} 

	public function getImageThumbnailHeight()
	{
		return $this->get(TSysModel::FIELD_IMAGE_THUMBNAIL_HEIGHT);
	}

	public function setImageThumbnailHeight($iHeight)
	{
		$this->set(TSysModel::FIELD_IMAGE_THUMBNAIL_HEIGHT, $iHeight);
	} 

	/**
	 * is default record
	 */
	public function getIsDefault()
	{
		return $this->get(TSysModel::FIELD_ISDEFAULT);
	}

	/**
	 * set if is default record
	 */
	public function setIsDefault($bIsDefaultRecord)
	{
		$this->set(TSysModel::FIELD_ISDEFAULT, $bIsDefaultRecord);
	}     

	/**
	 * get if is record is favorited by user
	 */
	public function getIsFavorite()
	{
		return $this->get(TSysModel::FIELD_ISFAVORITE);
	}

	/**
	 * set if is record is favorited by user
	 */
	public function setIsFavorite($bIsFavorite)
	{
		$this->set(TSysModel::FIELD_ISFAVORITE, $bIsFavorite);
	}       

	/**
	 * returns search keywords separated by comma (,)
	 */
	public function getSearchKeywords()
	{
		return $this->get(TSysModel::FIELD_SEARCHKEYWORDS);
	}

	/**
	 * set search keywords separated by comma (,)
	 */
	public function setSearchKeywords($sKeywords)
	{
		$this->set(TSysModel::FIELD_SEARCHKEYWORDS, $sKeywords);
	}             



	/**
	* check if the values are ok
	 */
	private function areValuesValidInternal()
	{

		$arrColumnNames = $this->getFieldsDefined();
		if (APP_DEBUGMODE)
		{
			//check if any record exists (and the recordpointer not pointing to an invalid record)
			if (count($this->arrDataNew) == 0)
				echo 'Error message from '.get_class($this).': There are NO records (recordpointer is pointing to record '.$this->iRecordPointer.'). Did you create a record with the newRecord() function?';

			//check if field exists (only in debug mode)
			foreach ($arrColumnNames as $sColumnName)
			{	
				if (!array_key_exists($sColumnName, $this->arrDataNew[$this->iRecordPointer]['']))
					echo 'Field "'.$sColumnName.'" does not exist in: '.get_class($this).' ';
			}				
		}		

		foreach ($arrColumnNames as $sColumnName)
		{
//			$mColValue = $this->arrData[$this->iRecordPointer][$sColumnName];

			$mColValue = $this->arrDataNew[$this->iRecordPointer][''][$sColumnName][TSysModel::DATA_VALUE];
			
			

			if ($mColValue != null)//als kolom voorkomt
			{
				//check strings
				if ($this->getFieldType($sColumnName) == CT_VARCHAR)
				{
					if (is_string($mColValue))
					{
						if (strlen($mColValue) > $this->getFieldLength($sColumnName)) //length check
						{
							logError(__CLASS__.':'.__FUNCTION__.':'.__LINE__,' length of value "'.($mColValue).'" (length:'.strlen($mColValue).') is larger than field '.$sColumnName.' (maxlength: '.$this->getFieldLength($sColumnName).') in databasetable '.$this::getTable());
							return false;
						}
					}
					else
					{
						if (!$this->getFieldNullable($sColumnName))
							$this->set($sColumnName, '');//make it string
					}
				}
	
				//check numeric types
				switch($this->getFieldType($sColumnName))
				{
					case CT_AUTOINCREMENT:
					case CT_FLOAT:
					case CT_DOUBLE:
					case CT_INTEGER32:
					case CT_INTEGER64:
						if ((!is_numeric($mColValue)) && (!is_object($mColValue))) //referenced table objects can be stored here
						{					
							//if it is null, and nullable than the value null is already auto written
							//but if it is not nullable, then replace value with 0	
							if (!$this->getFieldNullable($sColumnName))
							{
								logError(__CLASS__.':'.__FUNCTION__.':'.__LINE__, ' column: '.$sColumnName.' mColValue is not numeric ('. $mColValue.') but it is a numeric field. Made value 0');
								$this->set($sColumnName, 0);//make it numeric
								break;
							}
						}

				}
		
				//check boolean
				if ($this->getFieldType($sColumnName) == CT_BOOL)
				{
					if (!is_bool($mColValue))
					{
						if (!$this->getFieldNullable($sColumnName))
							$this->set($sColumnName, false);//make it boolean
					}
				}
			}//if not null
		
		} //eind foreach
		
		return true;		
	}	
	
	/**
	 * get the current connection object.
	 * if it doesnt exist one is created with the default connection object
	 * from the bootstrap
	 * 
	 * @return TDBConnection
	 */
	public function getConnectionObject()
	{
		//if connection object doesnt exist get the one from the bootstrap
		if (!$this->objDBConn)
		{
			global $objDBConnection;
			$this->objDBConn = $objDBConnection;			
		}
		
		return $this->objDBConn;
	}
	
	/**
	 * if you want to connect to an external database (not the default one)
	 * you have to set the connection object for the external database
	 */
	public function setConnectionObject(TDBConnection $objConn)
	{
		$this->objDBConn = $objConn;
	}

	
	/**
	 * checks out a record out of system for editing (locks record)
	 *
	 * this function updates the checkoutexpired field directly in the database
	 * it does NOT load, save or fill the internal fields of this record
	 * 
	 * don't want to execute immediately? use checkout()
	 *
	 * @param $iID integer record id
	 * @param $sLockSource string which user or system component checked out?
	 * @param $objDateTime TDateTime (can be null, then FIELD_CHECKOUTEXPIRES_VALUE_DEFAULT will be assumed)
	 * @return bool true if ok
	 */
	public function checkoutNowDB($iID, $sLockSource = '', $objDateTime = null)
	{
		if (!is_numeric($iID))
			return false;
		if ($iID == TSysModel::FIELD_ID_VALUE_DEFAULT)
			return false;
		
		if ($this->getTableUseCheckout())
		{
			if (!$objDateTime)
			{
				$objDateTime = new TDateTime(time());
				$objDateTime->addHour(TSysModel::FIELD_CHECKOUTEXPIRES_VALUE_DEFAULT);
			}
			 
			if ($objDateTime instanceof TDateTime)
			{
				$objClone = clone $this;
				$objClone->newQuery();
				$objClone->find(TSysModel::FIELD_ID, $iID)->limitOne()->loadFromDB();
				$objClone->set(TSysModel::FIELD_CHECKOUTEXPIRES, $objDateTime);
				$objClone->set(TSysModel::FIELD_CHECKOUTSOURCE, $sLockSource);
				return $objClone->saveToDB();
			}
			else
				return false;
		}
		else
			return true;
	}

	/**
	 * sets the checkout fields, but doesn't execute immediatately like checkoutNowDB() does
	 *
	 * 'this method does NOT load, save or fill the internal fields of this record, only the effected fields for checkout
	 *
	 * @param $iID integer record id
	 * @param $sLockSource string which user or system component checked out?
	 * @param $objDateTime TDateTime (can be null, then FIELD_CHECKOUTEXPIRES_VALUE_DEFAULT will be assumed)
	 * @return bool true if ok
	 */
	public function checkout($sLockSource = '', $objDateTime = null)
	{	
		if ($this->getTableUseCheckout())
		{
			if (!$objDateTime)
			{
				$objDateTime = new TDateTime(time());
				$objDateTime->addHour(TSysModel::FIELD_CHECKOUTEXPIRES_VALUE_DEFAULT);
			}
			 
			if ($objDateTime instanceof TDateTime)
			{
				$this->set(TSysModel::FIELD_CHECKOUTEXPIRES, $objDateTime);
				$this->set(TSysModel::FIELD_CHECKOUTSOURCE, $sLockSource);
			}
			else
				return false;
		}
		else
			return true;
	}

	/**
	 * alias for checkout
	 */
	public function setCheckout($sLockSource = '', $objDateTime = null)
	{
		return $this->checkout($sLockSource = '', $objDateTime = null);
	}
		
	
	/**
	 * checks in a record in system (unlock record)
	 *
	 * this function updates the checkoutexpired field directly in the database
	 * it does NOT load, save or fill the internal fields of this record
	 *
	 *
	 * @param $iID integer record id
	 * @param $objDateTime TDateTime (can be null, the current time is assumed)
	 * @return bool true if ok
	 */
	public function checkinNowDB($iID, $objDateTime = null)
	{
		if (!is_numeric($iID))
			return false;
		if ($iID == TSysModel::FIELD_ID_VALUE_DEFAULT)
			return false;
	
		if ($this->getTableUseCheckout())
		{
			if (!$objDateTime)
			{
				$objDateTime = new TDateTime();			
			}
	
			if ($objDateTime instanceof TDateTime)
			{
				$objClone = clone $this;
				$objClone->newQuery();
				$objClone->find(TSysModel::FIELD_ID, $iID)->limitOne()->loadFromDB();
				$objClone->set(TSysModel::FIELD_CHECKOUTEXPIRES, $objDateTime);
				$objClone->set(TSysModel::FIELD_CHECKOUTSOURCE, '');
				return $objClone->saveToDB();
			}
			else
				return false;
		}
		else
			return true;
	}	
	

	/**
	 * lock record
	 *
         * record locking is for the gui, it does NOTING under the hood
         * 
	 * this function updates the bLocked field
	 * it does NOT load, save or fill the internal fields of this record
	 *
	 * @param $iID integer record id
	 * @param $sLockSource string which user or system component checked out?
	 * @return bool true if ok
	 */
	public function lockNowDB($iID, $sLockSource = '')
	{
		if (!is_numeric($iID))
			return false;
		if ($iID == TSysModel::FIELD_ID_VALUE_DEFAULT)
			return false;
		
		if ($this->getTableUseLock())
		{
			$objClone = clone $this;
			$objClone->newQuery();
			$objClone->find(TSysModel::FIELD_ID, $iID)->limitOne()->loadFromDB();
			$objClone->set(TSysModel::FIELD_LOCKED, true);
			$objClone->set(TSysModel::FIELD_LOCKEDSOURCE, $sLockSource);
			return $objClone->saveToDB();

		}
		else
			return true;
	}
	
	/**
	 * checks in a record
	 *
	 * this function updates the checkoutexpired field directly in the database
	 * it does NOT load, save or fill the internal fields of this record
	 *
	 *
	 * @param $iID integer record id
	 * @return bool true if ok
	 */
	public function unlockNowDB($iID)
	{
		if (!is_numeric($iID))
			return false;
		if ($iID == TSysModel::FIELD_ID_VALUE_DEFAULT)
			return false;
	
		if ($this->getTableUseCheckout())
		{

                        $objClone = clone $this;
                        $objClone->newQuery();
                        $objClone->find(TSysModel::FIELD_ID, $iID)->limitOne()->loadFromDB();
                        $objClone->set(TSysModel::FIELD_LOCKED, false);
                        $objClone->set(TSysModel::FIELD_LOCKEDSOURCE, '');
                        return $objClone->saveToDB();

		}
		else
			return true;
	}        
        
	/**
	 * removes records from database by looking at the $mValue of $sFieldname
	 * 
         * @param mixed $mValue - 99% of the time this is $iID
         * @param string $sFieldname - to filter , default TSysModel::FIELD_ID
         * @param boolean $bCheckForLock, checks if the lock field should be considered. if locked, you can't delete record
         * @return boolean returns if error occured or record is locked
	 */
	public function deleteFromDB_OLD($mValue, $sFieldname = TSysModel::FIELD_ID, $sComparisonOperator = COMPARISON_OPERATOR_EQUAL_TO, $bCheckForLock = false)
	{		
		$objPrepStat = $this->getConnectionObject()->getPreparedStatement();
                
		if ($this->getTableUseLock() === false) //don't do lock check if it is not used
			$bCheckForLock = false;
						
		
		if (!$bCheckForLock) //just delete
			return $objPrepStat->deleteFromDB($this::getTable(), $sFieldname, $mValue, $sComparisonOperator);		
		else //lock check
			return $objPrepStat->deleteFromDB($this::getTable(), $sFieldname, $mValue, $sComparisonOperator, TMODEL::FIELD_LOCKED, false, COMPARISON_OPERATOR_EQUAL_TO);		
	}
        
	/**
	 * delete from database according to where conditions given in TSysModel
	 * 
	 * you need to specify the SQL WHERE part otherwise you will delete the 
	 * ENTIRE contents of the table.
	 * You can specify the SQL WHERE with $objModel->find();
	 * 
	 * @param boolean $bYesISpecifiedAWhereInMyModel to prevent you from deleting the contents of the whole table by accident
	 * @param boolean $bCheckAffectedRows if true this function checks if any records were deleted
	 * @return boolean
	 */
	public function deleteFromDB($bYesISpecifiedAWhereInMyModel, $bCheckAffectedRows = false)
	{
		$objPrepStat = $this->getConnectionObject()->getPreparedStatement();
		
		return $objPrepStat->deleteModel($this, $bYesISpecifiedAWhereInMyModel, $bCheckAffectedRows);
	}
	
    /**
     * saves ONLY the CURRENT record to the database
     * 
     * (only works for records with one primary key id field)
     *
     * @param boolean $bResetDirtyNewOnSuccess - reset dirty en new flags ?
     * @param boolean $bStartOwnDatabaseTransaction - start een (eigen) database transactie bij het uitvoern
     * @param boolean $bCheckForLock - if true: when record is locked, it won't save and return false (getTableUseLock() must return true)
     * @return boolean - is save succesful? true = yes (even if record isn't updated because its not dirty), false = no
     */
    public function saveToDB($bResetDirtyNewOnSuccess = true, $bStartOwnDatabaseTransaction = true, $bCheckForLock = false)
    {
        $bSuccess = false;
    				
    	$objConn = $this->getConnectionObject();
    	$objPrepStat = $objConn->getPreparedStatement();    

        try
        {               
            //lock prevents save
            if ($this->getTableUseLock())
                if ($bCheckForLock)
                    if (!$this->getNew()) //you can't lock records that are not yet in the database
                    {
                        if ($this->getLocked())
                        {
                            error_log('TSysModel(): record lock prevented save of record with id '.$this->getID());
                            return false;
                        }
                    }

            //run checks automatically on the internal data array, so we don't have to in the checkNeededValues() manually
            if (!$this->areValuesValidInternal())
            {
                error_log('TSysModel(): areValuesValidInternal() failed');
                return false;
            }

            //run check child class
            if (!$this->areValuesValid())
                    return false;
            
              
            if ($this->getDirty() || $this->getNew())
            {              
                //transaction start
                if ($bStartOwnDatabaseTransaction)
                     $objConn->beginTransaction();

                //if no auto increment dan id opvragen en met 1 ophogen
                if ($this->getTableIDFieldType() != CT_AUTOINCREMENT)
                    $this->setID($objPrepStat->getIDPlus1($this::getTable(), TSysModel::FIELD_ID ) );
                
                //generate a new unique-id
                if ($this->getNew())
                {                
					//randomid
                    if ($this->getTableUseRandomID())
                    {
                        $iRID = 0;

						//just add to 'find'-array
						// if (!$this->findExists(TSysModel::FIELD_RANDOMID))
                        //    $this->findRandomID($this->getRandomID());
                      
                        //generate a new randomid
                        
                        //@todo check for too large integer value
                        
                        //check for duplicates
                        $bDuplicateID = true;
                        $objTempModel = clone $this;
                        $iDuplicateTries = 0;
                        while($bDuplicateID)
                        {
                            $iRID = rand();
                            $objTempModel->clear();
                            $objTempModel->findRandomID($iRID);
                            $objTempModel->countResults(TSysModel::FIELD_RANDOMID, 'countids');
                            $objTempModel->loadFromDB();
                            $bDuplicateID = ($objTempModel->getAsInt('countids') > 0);
                            
                            $iDuplicateTries++;
                            if ($iDuplicateTries > 1000) //try only 1000 times to prevent endless loop
                            {
                                $bDuplicateID = false;
                                error_log(__CLASS__.' '.__LINE__.' STOPPED RANDOMID LOOP to prevent application from hanging, after 1000 tries no unique randomid found');
                            }   
                        }
                        
                        $this->setRandomID($iRID);
                    }    
					
					//uniqueid
                    if ($this->getTableUseUniqueID())
                    {
                        $sUID = '';
						// if (!$this->findExists(TSysModel::FIELD_UNIQUEID))
                        //    $this->findUniqueID($this->getUniqueID());
                      
                        //generate a new randomid
                                                
                        //check for duplicates
                        $bDuplicateID = true;
                        $objTempModel = clone $this;
                        $iDuplicateTries = 0;
                        while($bDuplicateID)
                        {
                            $sUID = uniqidReal(50);
                            $objTempModel->clear();
                            $objTempModel->findUniqueID($sUID);
                            $objTempModel->countResults(TSysModel::FIELD_UNIQUEID, 'countids');
                            $objTempModel->loadFromDB();
                            $bDuplicateID = ($objTempModel->getAsInt('countids') > 0);
                            
                            $iDuplicateTries++;
                            if ($iDuplicateTries > 1000) //try only 1000 times to prevent endless loop
                            {
                                $bDuplicateID = false;
                                error_log(__CLASS__.' '.__LINE__.' STOPPED UNIQUEID LOOP to prevent application from hanging, after 1000 tries no unique uniqueid found');
                            }   
                        }
                        
                        $this->setUniqueID($sUID);
                    }      
					
					//niceid
                    if ($this->getTableUseNiceID())
                    {
                        $sNiceID = '';
						$iLengthNiceID = 0;

						// if (!$this->findExists(TSysModel::FIELD_NICEID))
                        //    $this->findNiceID($this->getNiceID());
                      
						//check size needed for niceid
                        $objTempModel = clone $this;
						$objTempModel->clear();
						$objTempModel->max(TSysModel::FIELD_ID, 'maxid');	
						$objTempModel->loadFromDB();					
						$iLengthNiceID = strlen($objTempModel->get('maxid'));
						if ($iLengthNiceID < 6)
							$iLengthNiceID = 6; //min size is 6
						unset($objTempModel);

                        //generate a new niceid                        
                        //check for duplicates
                        $bDuplicateID = true;
                        $objTempModel = clone $this;
                        $iDuplicateTries = 0;
                        while($bDuplicateID)
                        {
                            $sNiceID = generateNiceID($iLengthNiceID, 3, true);
                            $objTempModel->clear();
                            $objTempModel->findNiceID($sNiceID);
                            $objTempModel->countResults(TSysModel::FIELD_NICEID, 'countids');
                            $objTempModel->loadFromDB();
                            $bDuplicateID = ($objTempModel->getAsInt('countids') > 0);
                            
                            $iDuplicateTries++;
                            if ($iDuplicateTries > 1000) //try only 1000 times to prevent endless loop
                            {
                                $bDuplicateID = false;
                                error_log(__CLASS__.' '.__LINE__.' STOPPED NICEID LOOP to prevent application from hanging, after 1000 tries no unique niceid found');
                            }   
                        }
                        
                        $this->setNiceID($sNiceID);
                    }   					
					
					

                }

                //update changed date
                if ($this->getTableUseDateCreatedChangedField())
                    $this->setDateChanged(new TDateTime(time()));

                //update order
                if ($this->getTableUseOrderField())
				{
                    if ($this->getPosition() == 0) //new records have zero. Also, don't overwrite new records with custom assigned order numbers which are always higher than 0 (custom order numbers can happen)
                        $this->setPosition($objPrepStat->getIDPlus1($this::getTable(), TSysModel::FIELD_POSITION ) );
				}

                //checksum
                if ($this->getTableUseChecksumField())
                    $this->setChecksumEncrypted($this->calculateChecksum());

                //is record new?
                if ($this->getNew())
                {
                    $bSuccess = $objPrepStat->insertModel($this);

                    
                    if ($bSuccess)
                    {
                        //als auto-increment dan id setten
                        if ($this->getTableIDFieldType() == CT_AUTOINCREMENT)
							$this->setID( $objPrepStat->getLastInsertID() );    
							
                    }
                }
                else //if NOT new
                {                    

                    //is record maybe changed?
                    if ($this->getDirty())
                    {		
						//use randomid as primary key?
						if ($this->getTableUseRandomIDAsPrimaryKey())
						{
							if (!$this->findExists(TSysModel::FIELD_ID))
							{
								$this->find(TSysModel::FIELD_RANDOMID,  $this->getRandomID()); //temporarily add, remove later otherwise we can't save multiple records (they would all have the same WHERE id=X)
							}
							
							if (is_numeric($this->getRandomID())) //tested on numeric. er wordt getest op numeriek, dus prepared statements niet nodig (geeft onnodig overhead)
							{            
								$bSuccess = $objPrepStat->updateModel($this); //we hebben al gefilterd of het record gewijzigd is, dus als ik gewijzigd is, moet er in de database iets anders staan, met als gevolg dat er minimaal 1 affected row is. Op deze manier is het redelijk nauwkeurig te bepalen of het updaten gelukt is                                                      
								$this->clearFind(); //restore Find, otherwise we can't save multiple records (they would all have the same WHERE id=X)
							}
							else
							{
								$bSuccess = false;
								throw new \Exception('saveToDB(): randomid is not numeric. Possible SQL injection. Save record NOT completed');
							}
						}
						else //use regular id or no id at all
						{
                            if ($this->getTableUseIDField())//with id field: extra check
                            {
								if (!$this->findExists(TSysModel::FIELD_ID))
								{
									$this->findID($this->getID()); //temporarily add, remove later otherwise we can't save multiple records (they would all have the same WHERE id=X)
								}
                                
                                if (is_numeric($this->getID())) //er wordt getest op numeriek, dus prepared statements niet nodig (geeft onnodig overhead)
                                {            
									$bSuccess = $objPrepStat->updateModel($this); //we hebben al gefilterd of het record gewijzigd is, dus als ik gewijzigd is, moet er in de database iets anders staan, met als gevolg dat er minimaal 1 affected row is. Op deze manier is het redelijk nauwkeurig te bepalen of het updaten gelukt is                                                      
									$this->clearFind(); //restore Find, otherwise we can't save multiple records (they would all have the same WHERE id=X)
                                }
                                else
                                {
                                    $bSuccess = false;
									logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'saveToDB(): id is not numeric ('.$this->getID().'). Possible SQL injection. Save record NOT completed');
                                    throw new \Exception('saveToDB(): id is not numeric ('.$this->getID().'). Possible SQL injection. Save record NOT completed');
                                }
                            }
                            else//without id field no extra checks, just save
                            {
                                    $bSuccess = $objPrepStat->updateModel($this); //we hebben al gefilterd of het record gewijzigd is, dus als ik gewijzigd is, moet er in de database iets anders staan, met als gevolg dat er minimaal 1 affected row is. Op deze manier is het redelijk nauwkeurig te bepalen of het updaten gelukt is                        		
							}

						}
                    }
                }


				//there can only be one record default
				if ($this->getTableUseIsDefault() && $bSuccess)
				{
					if ($this->getIsDefault())
					{
						$objPrepStat->updateField($this->getTable(), TSysModel::FIELD_ISDEFAULT, '0', TSysModel::FIELD_ID, $this->getID(), COMPARISON_OPERATOR_NOT_EQUAL_TO);
					}
				}

                //transactie doorvoeren
                if ($bStartOwnDatabaseTransaction)
                {
                    if ($bSuccess)
                        $objConn->commit();
                    else
                        $objConn->rollback();
                }

                //dirty em new flags resetten
                if ($bResetDirtyNewOnSuccess && $bSuccess)
                {
                    $this->setDirty(false);
                    $this->setNew(false);
                }
                
                if (!$bSuccess)
                {
                    error_log(__CLASS__.' '.__FUNCTION__.' '.__LINE__.' SQL execution NOT successful');
                    
                    if ($this->getTableUseRandomID())
                        error_log(__CLASS__.' '.__FUNCTION__.' '.__LINE__.' regarding the previous error: random generated randomid is used in this model. Could that be the problem? Double IDs? generated value too big???');
                }


            } //EINDE if ($this->getDirty() || $this->getNew())
            else
                $bSuccess = true;


        }//try
        catch (Exception $objException)
        {       
            //transactie terugdraaien
            if ($bStartOwnDatabaseTransaction)
                 $objConn->rollback();

            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            
            error_log(__CLASS__.' '.__FUNCTION__.' '.__LINE__.' exception occurred');
            
            if ($this->getTableUseRandomID())
                error_log(__CLASS__.' '.__FUNCTION__.' '.__LINE__.' regarding the previous error: random generated randomID is used in this model. Could that be the problem? Double IDs? generated value too big???');
            
            return false;
        }

        return $bSuccess;
    }
	
      /**
       * save all items/records in model to database 
       * (in stead of just one record that saveToDB() does)
       *
       * @param bool $bResetDirtyNewOnSuccess reset de dirty en new flags na het opslaan
       * @param bool $bStartOwnDatabaseTransaction start 1 transactie voor alle items in de objectlist (dus niet individueel)
       * @return bool
       */
      public function saveToDBAll($bResetDirtyNewOnSuccess = true, $bStartOwnDatabaseTransaction = false)
      {
          $bSuccess = true;
          $objConn = $this->getConnectionObject();

          try
          {             
			if ($this->getDirtyAll() || $this->getNewAll())
			{
				//transactie starten
				if ($bStartOwnDatabaseTransaction)
						$objConn->startTransaction();
				
				$this->resetRecordPointer();                                                    
				while($this->next())
				{
					if($this->saveToDB(false, false) == false)
						$bSuccess = false;
				}

				//transactie doorvoeren
				if ($bStartOwnDatabaseTransaction)
				{
					if ($bSuccess)
						$objConn->commit();
					else
						$objConn->rollback();
				}

			}
			else
				$bSuccess = true;
                        
          }
          catch (Exception $objException)
          {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
                return false;
          }

          return $bSuccess;
      }	
    
	
	  /**
	   * make an encrypted checksum with salt and pepper
	   *
	   * @param $sSalt if empty a new salt of max 100 chars is generated
	   * @return void
	   */
      public function calculateChecksum($sSalt = '')
      {
		if ($sSalt == '')
			$sSalt = generateRandomString(50,100, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*(){}?<>;_+=.,^ ');//exclude chars that are in TSysModel::CHECKSUM_SALTSEPARATOR

    	return $sSalt.TSysModel::CHECKSUM_SALTSEPARATOR.md5($this->getChecksumUncrypted().$sSalt.APP_PEPPER);
      }
      
      
      /**
       * does the match the calculated checksum of the TSysModel fields calculated
       * with calculateChecksum() match the checksum field?
       *
       * can be used to see if record is manipulated from outside, for example on loading a record
       *
       * @return bool true=checksums match; false=NO match or no salt found
       */
      public function isChecksumValid()
      {
		$sChecksumClass = '';
		$arrExplChecksumClass = array();
		$sSalt = '';
		$sChecksumCalculated = '';


		if ($this->getTableUseChecksumField())
		{      			
			$sChecksumClass = $this->getChecksumEncrypted();

			//determine what the salt is of the encrypted checksum stored in this class
			$arrExplChecksumClass = explode(TSysModel::CHECKSUM_SALTSEPARATOR, $sChecksumClass);
			$sSalt = $arrExplChecksumClass[0];

			//calculate a checksum with the same salt
			$sChecksumCalculated = $this->calculateChecksum($sSalt);

			//compare calculated checksum with internally stored one
			$bResult = (hash_equals($sChecksumClass, $sChecksumCalculated)); //hash_equals() = timing attack safe string comparison
			if (!$bResult)
			{
				if ($this->getTableUseIDField())
					error_log(__FILE__.':'.__LINE__.' checksum failed of record in table '.$this::getTable().' with ID='.$this->getID().' : "'.$sChecksumClass.'" (from getChecksum()) == "'.$sChecksumCalculated.'" (from calculateChecksum())');
				else
					error_log(__FILE__.':'.__LINE__.' checksum failed of record in table '.$this::getTable().': "'.$sChecksumClass.'" (from getChecksum()) == "'.$sChecksumCalculated.'" (from calculateChecksum())');	
			
			}

			return $bResult;
		}
		else //geen checksum berekenen als niet nodig (ivm performance)
			return true;

			
		// error_log(get_class($this).'isChecksumValid() RETURNS ALWAYS TRUE. commented out check') ;
		// 	return true;
	  }     
	   

	  /**
	   * the same as isChecksumValid(), however it checks all loaded records instead of only the current
	   */
	  public function isChecksumValidAllRecords()
	  {
		$iOldRecordPointer = $this->getRecordPointer();

		$this->resetRecordPointer();
		while($this->next())
		{
			if (!$this->isChecksumValid())
				return false;
		}
		$this->setRecordPointerToIndex($iOldRecordPointer);

		return true;
	  }
      
      
      
      
	//===== defining the fields 
	
    /**
     * with this function you can set de properties of a field (by supplying a field to copy the properties from)
     * for quick and fast database field defining
     * if you defined a field once, you can copy the properties to new fields with this function
     * 
     * @param string $sDestinationFieldName
     * @param string $sSourceFieldName
     */
    protected function setFieldCopyProps($sDestinationFieldName, $sSourceFieldName)
    {
		$this->setFieldDefaultValue($sDestinationFieldName, $this->getFieldDefaultValue($sSourceFieldName));
		$this->setFieldType($sDestinationFieldName, $this->getFieldType($sSourceFieldName));
		$this->setFieldLength($sDestinationFieldName, $this->getFieldLength($sSourceFieldName));
		$this->setFieldDecimalPrecision($sDestinationFieldName, $this->getFieldDecimalPrecision($sSourceFieldName));
		$this->setFieldPrimaryKey($sDestinationFieldName, $this->getFieldPrimaryKey($sSourceFieldName));
		$this->setFieldNullable($sDestinationFieldName, $this->getFieldNullable($sSourceFieldName));
		$this->setFieldEnumValues($sDestinationFieldName, $this->getFieldEnumValues($sSourceFieldName));
		$this->setFieldUnique($sDestinationFieldName, $this->getFieldUnique($sSourceFieldName));
		$this->setFieldIndexed($sDestinationFieldName, $this->getFieldIndexed($sSourceFieldName));
		$this->setFieldFulltext($sDestinationFieldName, $this->getFieldFulltext($sSourceFieldName)); 
		$this->setFieldForeignKeyClass($sDestinationFieldName, $this->getFieldForeignKeyClass($sSourceFieldName));
		$this->setFieldForeignKeyTable($sDestinationFieldName, $this->getFieldForeignKeyTable($sSourceFieldName));
		$this->setFieldForeignKeyField($sDestinationFieldName, $this->getFieldForeignKeyField($sSourceFieldName));
		$this->setFieldForeignKeyJoin($sDestinationFieldName, $this->getFieldForeignKeyJoin($sSourceFieldName));
		$this->setFieldForeignKeyActionOnUpdate($sDestinationFieldName, $this->getFieldForeignKeyActionOnUpdate($sSourceFieldName));
		$this->setFieldForeignKeyActionOnDelete($sDestinationFieldName, $this->getFieldForeignKeyActionOnDelete($sSourceFieldName));
		$this->setFieldAutoIncrement($sDestinationFieldName, $this->getFieldAutoIncrement($sSourceFieldName));
		$this->setFieldUnsigned($sDestinationFieldName, $this->getFieldUnsigned($sSourceFieldName));    		
		$this->setFieldEncryptionCypher($sDestinationFieldName, $this->getFieldEncryptionCypher($sSourceFieldName));    		
		$this->setFieldEncryptionDigest($sDestinationFieldName, $this->getFieldEncryptionDigest($sSourceFieldName));    		
		$this->setFieldEncryptionPassphrase($sDestinationFieldName, $this->getFieldEncryptionPassphrase($sSourceFieldName));    		
    }
      
    /** 
     * with this function you can set all the default properties for a string/varchar property with:
	 * - maxlength of 100
	 * - NOT primary key
	 * - NOT unique
	 * - NOT indexed
	 * - no external table
	 * - no encryption
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */
    protected function setFieldDefaultsVarChar($sFieldName, $iFieldLength = 100)
    {
		$this->setFieldDefaultValue($sFieldName, '');
		$this->setFieldType($sFieldName, CT_VARCHAR);
		$this->setFieldLength($sFieldName, $iFieldLength);
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, true);
		$this->setFieldEnumValues($sFieldName, false);
		$this->setFieldUnique($sFieldName, false);
		$this->setFieldIndexed($sFieldName, false);
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null);
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);    		
		$this->setFieldEncryptionDisabled($sFieldName);
    }

    /** 
	 * alias for setFieldDefaultsVarChar()
	 * 
     * with this function you can set all the default properties for a string/varchar property with:
	 * - maxlength of 100
	 * - NOT primary key
	 * - NOT unique
	 * - NOT indexed
	 * - no external table
	 * - no encryption
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */
    protected function setFieldDefaultsString($sFieldName, $iFieldLength = 100)	
	{
		$this->setFieldDefaultsVarChar($sFieldName, $iFieldLength);
	}

    /** 
     * with this function you can set all the default properties for a string/varchar property with:
	 * - NOT primary key
	 * - NOT unique
	 * - NOT indexed
	 * - no external table
	 * - no encryption
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */
    protected function setFieldDefaultsLongText($sFieldName)
    {
		$this->setFieldDefaultValue($sFieldName, '');
		$this->setFieldType($sFieldName, CT_LONGTEXT);
		$this->setFieldLength($sFieldName, 0);
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, true);
		$this->setFieldEnumValues($sFieldName, false);
		$this->setFieldUnique($sFieldName, false);
		$this->setFieldIndexed($sFieldName, false);
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null);
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);    		
		$this->setFieldEncryptionDisabled($sFieldName);
    }

    /** 
     * with this function you can set all the default properties for a boolean property
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    protected function setFieldDefaultsBoolean($sFieldName)
    {
		$this->setFieldDefaultValue($sFieldName, 0);
		$this->setFieldType($sFieldName, CT_BOOL);
		$this->setFieldLength($sFieldName, 1);
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, false);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false); 
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }

    /** 
     * with this function you can set all the default properties for a TDecimal property
	 * 
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    protected function setFieldDefaultsTDecimal($sFieldName, $iFieldLength = 10, $iDecimalPrecision = 4)
    {
		$this->setFieldDefaultValue($sFieldName, 0);
		$this->setFieldType($sFieldName, CT_DECIMAL);
		$this->setFieldLength($sFieldName, $iFieldLength);
		$this->setFieldDecimalPrecision($sFieldName, $iDecimalPrecision);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, false);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false); 
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }

    /** 
     * same as setFieldDefaultsTDecimal() but with different defaults: 13, 4
	 * 	  
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    protected function setFieldDefaultsTDecimalCurrency($sFieldName, $iFieldLength = 13, $iDecimalPrecision = 4)
    {
		$this->setFieldDefaultValue($sFieldName, 0);
		$this->setFieldType($sFieldName, CT_DECIMAL);
		$this->setFieldLength($sFieldName, $iFieldLength);
		$this->setFieldDecimalPrecision($sFieldName, $iDecimalPrecision);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, false);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false); 
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }

    /** 
     * with this function you can set all the default properties for a TDecimal property
	 * 
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    // protected function setFieldDefaultsCurrency($sFieldName, $iFieldLength = 13, $iDecimalPrecision = 4)	
	// {
	// 	$this->setFieldDefaultValue($sFieldName, 0);
	// 	$this->setFieldType($sFieldName, CT_CURRENCY);
	// 	$this->setFieldLength($sFieldName, $iFieldLength);
	// 	$this->setFieldDecimalPrecision($sFieldName, $iDecimalPrecision);
	// 	$this->setFieldPrimaryKey($sFieldName, false);
	// 	$this->setFieldNullable($sFieldName, false);
	// 	$this->setFieldEnumValues($sFieldName, null);
	// 	$this->setFieldUnique($sFieldName, false); 
	// 	$this->setFieldIndexed($sFieldName, false); 
	//  $this->setFieldFulltext($sFieldName, false); 
	// 	$this->setFieldForeignKeyClass($sFieldName, null);
	// 	$this->setFieldForeignKeyTable($sFieldName, null);
	// 	$this->setFieldForeignKeyField($sFieldName, null);
	// 	$this->setFieldForeignKeyJoin($sFieldName);
	// 	$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
	// 	$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
	// 	$this->setFieldAutoIncrement($sFieldName, false);
	// 	$this->setFieldUnsigned($sFieldName, false);
    //     $this->setFieldEncryptionDisabled($sFieldName);		
	// }

    /** 
     * with this function you can set all the default properties for a 64 bit unsigned integer:
	 * - NOT primary key
	 * - NOT unique
	 * - NOT indexed
	 * - no external table
	 * - no encryption
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    protected function setFieldDefaultsInteger($sFieldName)
    {
		$this->setFieldDefaultValue($sFieldName, 0);
		$this->setFieldType($sFieldName, CT_INTEGER64);
		$this->setFieldLength($sFieldName, 0);//is automatically set
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, false);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false);
		$this->setFieldFulltext($sFieldName, false); 		 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, true);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }

    /** 
     * with this function you can set all the default properties for a 64 bit unsigned integer that references another table
	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
	 * 
	 * example: setFieldDefaultsIntegerForeignKey(TProducts::FIELD_VATCLASSESID, TVATClasses::class, TVATClasses::getTable(), TVATClasses::FIELD_ID)
     * 
     * @param string $sFieldName
     * @param string $sForeignClass
     * @param string $sForeignTable
     * @param string $sForeignField
     */	
    protected function setFieldDefaultsIntegerForeignKey($sFieldName, $sForeignClass, $sForeignTable, $sForeignField)
    {
		$this->setFieldDefaultValue($sFieldName, 0);
		$this->setFieldType($sFieldName, CT_INTEGER64);
		$this->setFieldLength($sFieldName, 0);//is automatically set
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, false);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false); 
		$this->setFieldFulltext($sFieldName, false); 		
		$this->setFieldForeignKeyClass($sFieldName, $sForeignClass); //==> fill out
		$this->setFieldForeignKeyTable($sFieldName, $sForeignTable);  //==> fill out
		$this->setFieldForeignKeyField($sFieldName, $sForeignField);  //==> fill out
		$this->setFieldForeignKeyJoin($sFieldName);  //==> fill out
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);  //==> fill out
		$this->setFieldForeignKeyActionOnDelete($sFieldName, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);  //==> fill out
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, true);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }
    
    /** 
     * with this function you can set all the default properties for a TDateTime type

	 * 
     * Used for quick and fast database field defining: just set this as default and overwrite any values you don't like
     * 
     * @param string $sFieldName
     */	
    protected function setFieldDefaultsTDateTime($sFieldName, $bAllowNullDates = true)
    {
		if (!$bAllowNullDates)
			$objDate = new TDateTime();

		if ($bAllowNullDates)
			$this->setFieldDefaultValue($sFieldName, null);
		else
			$this->setFieldDefaultValue($sFieldName, $objDate);
		$this->setFieldType($sFieldName, CT_DATETIME);
		$this->setFieldLength($sFieldName, 0);//is automatically set
		$this->setFieldDecimalPrecision($sFieldName, 0);
		$this->setFieldPrimaryKey($sFieldName, false);
		$this->setFieldNullable($sFieldName, true);
		$this->setFieldEnumValues($sFieldName, null);
		$this->setFieldUnique($sFieldName, false); 
		$this->setFieldIndexed($sFieldName, false); 
		$this->setFieldFulltext($sFieldName, false); 
		$this->setFieldForeignKeyClass($sFieldName, null);
		$this->setFieldForeignKeyTable($sFieldName, null);
		$this->setFieldForeignKeyField($sFieldName, null);
		$this->setFieldForeignKeyJoin($sFieldName, null);
		$this->setFieldForeignKeyActionOnUpdate($sFieldName, null);
		$this->setFieldForeignKeyActionOnDelete($sFieldName, null); 
		$this->setFieldAutoIncrement($sFieldName, false);
		$this->setFieldUnsigned($sFieldName, false);
        $this->setFieldEncryptionDisabled($sFieldName);		
    }
    
	/**
	 * set the default value for a field
	 * 
	 * function can be called in the define() function of the child class
	 * 
	 * @param $sFieldName string name of the field
	 * @param $mDefaultValue mixed default value
	 */
	protected function setFieldDefaultValue($sFieldName, $mDefaultValue = '')
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_DEFAULTVALUE] = $mDefaultValue;
	}
	
	public function getFieldDefaultValue($sFieldName)
	{
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_DEFAULTVALUE];
	}
	
	/**
	 * set the field type
	 * 
	 * function can be called in the define() function of the child class
	 * 
	 * @param $sFieldName string name of the field
	 * @param $iType integer columntype defined in lib_types, ie CT_VARCHAR
	 */
	public function setFieldType($sFieldName, $iType = CT_VARCHAR)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_TYPE] = $iType;
	}
	
	/**
	 * return field type of field
	 */
	public function getFieldType($sFieldName)
	{
		if (isset($this->arrFieldInfo[$sFieldName]))//replaced 27jan2020 isset is faster
			return $this->arrFieldInfo[$sFieldName][TSysModel::FI_TYPE];
		if (isset($this->arrQBFieldTypesExt[$sFieldName]))//added  30-5 -2025
			return $this->arrQBFieldTypesExt[$sFieldName];
		
		return TP_UNDEFINED;
		
		// if ($mResult)
		// 	return $mResult;  
		// else
		// { 
        //     error_log(__CLASS__.' '.__LINE__.' '.__METHOD__.' returned CT_VARCHAR because $sFieldName ('.$sFieldName.') doesnt exist in arrFieldInfo');
		// 	return CT_VARCHAR; //default
		// }		
	}
	
	/**
	 * set the field length
	 * 
	 * function can be called in the define() function of the child class
	 * 
	 * @param $sFieldName string name of the field
	 * @param $iLength integer maximum length of the field
	 */
	public function setFieldLength($sFieldName, $iLength = 255)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_LENGTH] = $iLength;
	}
	
	public function getFieldLength($sFieldName)
	{
		$mResult = 0;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_LENGTH];
		
		if ($mResult)
			return $mResult;  
		else
			return 0; //default		
	}
	
	/**
	 * set the decimal precision (of the decimal type)
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $iDigitsAfterDecimalSeparator integer the precision in digits after decimal separator
	 */
	public function setFieldDecimalPrecision($sFieldName, $iDigitsAfterDecimalSeparator = TDecimal::DECIMALPRECISIONDEFAULT)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_DECIMALPRECISION] = $iDigitsAfterDecimalSeparator;
	}	
	
	public function getFieldDecimalPrecision($sFieldName)
	{
		$mResult = 0;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_DECIMALPRECISION];
		
		if ($mResult)
			return $mResult;  
		else
			return 0; //default
		
	}
	
	/**
	 * set if the field is a primary key in the table
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $bPrimary boolean primary key or not
	 */
	public function setFieldPrimaryKey($sFieldName, $bPrimary = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_PRIMARYKEY] = $bPrimary;
	}	
	
	public function getFieldPrimaryKey($sFieldName)
	{
		$mResult = true;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_PRIMARYKEY];
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default	
	}
	
	/**
	 * set if the field can be null
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $bCanBeNull boolean if the field can be null or not
	 */
	public function setFieldNullable($sFieldName, $bCanBeNull = true)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_NULLABLE] = $bCanBeNull;
	}
	
	public function getFieldNullable($sFieldName)
	{
		$mResult = true;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_NULLABLE];

		if ($mResult !== null)
			return $mResult;  
		else
			return true; //default		
	}
	
	
	/**
	 * set the enum values of the field
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $arrEnumValues array 1d with enum values (array('CHOICE ONE', 'CHOICE TWO', 'CHOICE THREE'))
	 */
	public function setFieldEnumValues($sFieldName, $arrEnumValues)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_ENUMVALUES] = $arrEnumValues;
	}	
	
	public function getFieldEnumValues($sFieldName)
	{
		$mResult = array();
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_ENUMVALUES];
		
		if ($mResult)
			return $mResult;  
		else
			return array(); //default			
	}
	
	/**
	 * set if fieldvalue has to be unique in table
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $bUnique boolean 
	 */
	public function setFieldUnique($sFieldName, $bUnique = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_UNIQUE] = $bUnique;
	}	
	
	public function getFieldUnique($sFieldName)
	{
		$mResult = false;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_UNIQUE];
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default			
	}
	
	/**
	 * set if column is indexed
	 *
	 * indexes on columns takes more memory, but also allows for faster searching
	 *
	 * @param $sFieldName string name of the field
	 * @param $bAutoIncrement boolean
	 */
	public function setFieldIndexed($sFieldName, $bIndex = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_INDEXED] = $bIndex;
	}	
	
	/**
	 * get if column is indexed
	 *
	 * indexes on columns takes more memory, but also allows for faster searching
	 *
	 * @param $sFieldName string name of the field
	 */	
	public function getFieldIndexed($sFieldName)
	{
		$mResult = false;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_INDEXED]; 
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default
	}

	/**
	 * get if column has a 'fulltext' index for fuzzy searching
	 *
	 * fuzzy search columns takes up more memory, but also allows for faster searching
	 *
	 * @param $sFieldName string name of the field
	 */	
	public function getFieldFulltext($sFieldName)
	{
		$mResult = false;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_FULLTEXT]; 
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default
	}

	/**
	 * set if column is fulltext for fuzzy searching
	 *
	 * fuzzy search columns takes up more memory, but also allows for faster searching
	 *
	 *
	 * @param $sFieldName string name of the field
	 * @param $bAutoIncrement boolean
	 */
	public function setFieldFulltext($sFieldName, $bIndex = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FULLTEXT] = $bIndex;
	}	
	


	/**
	 * set class of foreign key
	 * this class is a php class, wich will be instantiated when reading
	 * the classname is the shortname (not the namespaced one)
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $sForeignKeyTable string table of the foreign key
	 */
	public function setFieldForeignKeyClass($sFieldName, $sForeignKeyClass)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_CLASS] = $sForeignKeyClass;
	}
	
	public function getFieldForeignKeyClass($sFieldName)
	{
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_CLASS];
	}	
	

	
	/**
	 * set table of foreign key
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $sForeignKeyTable string table of the foreign key
	 */
	public function setFieldForeignKeyTable($sFieldName, $sForeignKeyTable)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_TABLE] = $sForeignKeyTable;
	}	
	
	public function getFieldForeignKeyTable($sFieldName)
	{
//var_dump($sFieldName);
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_TABLE];
	}
	

	/**
	 * set field of foreign key in foreign table
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $sFieldInForeignTable string table of the foreign key
	 */
	public function setFieldForeignKeyField($sFieldName, $sFieldInForeignTable = TSysModel::FIELD_ID)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_FIELD] = $sFieldInForeignTable;
	}
	
	public function getFieldForeignKeyField($sFieldName)
	{
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_FIELD];
	}
	
	
	/**
	 * set action on update
	 *
	 * function can be called in the define() function of the child class
	 * 
	 * When you update the row from the external table, automatically updates the matching rows in the current table
	 *
	 * @param string $sFieldName name of the field
	 * @param int $iActionOnUpdate with constant values like FOREIGNKEY_REFERENCE_NOACTION
	 */
	public function setFieldForeignKeyActionOnUpdate($sFieldName, $iActionOnUpdate = TSysModel::FOREIGNKEY_REFERENCE_NOACTION)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_ACTIONONUPDATE] = $iActionOnUpdate;
	}
	
	/**
	 * get action on update
	 * 
	 * When you update the row from the external table, automatically updates the matching rows in the current table
	 *
	 * @param string $sFieldName name of the field
	 * @return boolean
	 */
	public function getFieldForeignKeyActionOnUpdate($sFieldName)
	{
		$mResult = '';
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_ACTIONONUPDATE];
		
		if ($mResult)
			return $mResult;  
		else
			return TSysModel::FOREIGNKEY_REFERENCE_NOACTION; //default		
	}
		
	
	/**
	 * set action on delete
	 *
	 * function can be called in the define() function of the child class
	 * 
	 * When you delete a record from the external table, automatically deletes the matching records in the current table
	 *
	 * @param string $sFieldName name of the field
	 * @param int $iActionOnDelete with constant values like FOREIGNKEY_REFERENCE_NOACTION
	 */
	public function setFieldForeignKeyActionOnDelete($sFieldName, $iActionOnDelete = TSysModel::FOREIGNKEY_REFERENCE_NOACTION)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_ACTIONONDELETE] = $iActionOnDelete;
	}	

	/**
	 * get action on delete
	 *
	 * When you delete a record from the external table, automatically deletes the matching records in the current table
	 *
	 * @param string $sFieldName name of the field
	 * @return boolean
	 */	
	public function getFieldForeignKeyActionOnDelete($sFieldName)
	{
		$mResult = '';
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_ACTIONONDELETE];
		
		if ($mResult)
			return $mResult;  
		else
			return TSysModel::FOREIGNKEY_REFERENCE_NOACTION; //default		
	}
	
	
	/**
	 * set join type of foreign key
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $sJoinType string constant from lib_types, like JOIN_INNER
	 */
	public function setFieldForeignKeyJoin($sFieldName, $sJoinType = JOIN_INNER)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_JOIN] = $sJoinType;
	}
	
	public function getFieldForeignKeyJoin($sFieldName)
	{
		$mResult = '';
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_FOREIGNKEY_JOIN];
		
		if ($mResult)
			return $mResult;  
		else
			return JOIN_INNER; //default		
	}	
	
	/**
	 * set auto increment
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $bAutoIncrement boolean
	 */
	public function setFieldAutoIncrement($sFieldName, $bAutoIncrement = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_AUTOINCREMENT] = $bAutoIncrement;
	}	
	
	public function getFieldAutoIncrement($sFieldName)
	{
		$mResult = false;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_AUTOINCREMENT]; 
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default
	}
	
	
	/**
	 * set unsigned (signed is with leading positive or negative bit)
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param $sFieldName string name of the field
	 * @param $bUnsigned boolean
	 */
	public function setFieldUnsigned($sFieldName, $bUnsigned = false)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_UNSIGNED] = $bUnsigned;
	}	
	
	public function getFieldUnsigned($sFieldName)
	{
		$mResult = false;
		$mResult = $this->arrFieldInfo[$sFieldName][TSysModel::FI_UNSIGNED];
		
		if ($mResult)
			return $mResult;  
		else
			return false; //default		
	}
	



	/**
	 * set encryption cypher
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param string $sFieldName name of the field 
	 * @param string $sCypher empty means: no encryption
	 */
	public function setFieldEncryptionCypher($sFieldName, $sCypher = ENCRYPTION_CYPHERMETHOD_DEFAULT)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONCYPHER] = $sCypher;
	}	
	
	/**
	 * get encryption cypher
	 *
	 * @param string $sFieldName
	 * @return string
	 */
	public function getFieldEncryptionCypher($sFieldName)
	{		
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONCYPHER];
	}

	/**
	 * set encryption digest
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param string $sFieldName name of the field 
	 * @param string $sDigest empty means: no encryption
	 */
	public function setFieldEncryptionDigest($sFieldName, $sDigest = ENCRYPTION_DIGESTALGORITHM_DEFAULT)
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONDIGEST] = $sDigest;
	}	
	
	/**
	 * get encryption digest
	 *
	 * @param string $sFieldName
	 * @return string
	 */
	public function getFieldEncryptionDigest($sFieldName)
	{		
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONDIGEST];
	}

	
	/**
	 * set encryption digest
	 *
	 * function can be called in the define() function of the child class
	 *
	 * @param string $sFieldName name of the field 
	 * @param string $sDigest empty means: no encryption
	 */
	public function setFieldEncryptionPassphrase($sFieldName, $sEncryptionKey = '')
	{
		$this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONPASSPHRASE] = $sEncryptionKey;
	}	
	
	/**
	 * get encryption digest
	 *
	 * @param string $sFieldName
	 * @return string
	 */
	public function getFieldEncryptionPassphrase($sFieldName)
	{		
		return $this->arrFieldInfo[$sFieldName][TSysModel::FI_ENCRYPTIONPASSPHRASE];
	}	

	/**
	 * set all encryption fields to: NO ENCRYPTION
	 * 
	 * this function calls all the encryption functions at once and sets them to NO ENCRYPTION:
	 * setFieldEncryptionCypher($sFieldName, '');
	 * setFieldEncryptionDigest($sFieldName, '');
	 * setFieldEncryptionPassphrase($sFieldName, '');
	 *
	 * @return void
	 */
	public function setFieldEncryptionDisabled($sFieldName)
	{
		$this->setFieldEncryptionCypher($sFieldName, '');
		$this->setFieldEncryptionDigest($sFieldName, '');
		$this->setFieldEncryptionPassphrase($sFieldName, '');
	}

	/**
	 * returns if encryption is disabled or not on a field
	 *
	 * @param string $sFieldName
	 * @return bool
	 */
	public function getFieldEncryptionDisabled($sFieldName)
	{
		return ($this->getFieldEncryptionPassphrase($sFieldName) == '');
	}

	/**
	 * returns if encryption is enabled or not on a field
	 *
	 * @param string $sFieldName
	 * @return bool
	 */	
	public function getFieldEncryptionEnabled($sFieldName)
	{
		return ($this->getFieldEncryptionPassphrase($sFieldName) != '');
	}	


	/**
	 * A debug function to find missing setField...()-instructions in defineTable()
	 * 
	 * When copy-pasting field information it is easy to overlook a setField...() instruction.
	 * This debug function helps you to find it!
	 * 
	 * You can use this function in 2 ways:
	 * 1) checking every field individually (use the $sFieldName parameter)
	 * 2) cheching all fields at a time. One downside: if you forgot to define a field at all, this function won't find it
	 * 
	 * @param string $sFieldName if '' then all fields are assumed
	 */
	public function debugDefineTable($sFieldName = '')
	{	
		$arrFields = array();	
		
		//only in development environment
		if (!APP_DEBUGMODE)
		{
			echo 'defineTableDebug(): not in development environment';
			return;
		}

		//hunt for each fieldname (if no fieldname was supplied as parameter)
		if ($sFieldName == '')
		{
			$arrFields = array_keys($this->arrFieldInfo);
			foreach($arrFields as $sCurrField)
			{
				if ($sCurrField != '') //preventing infinite loop
					$this->debugDefineTable($sCurrField);
			}

			return;
		}


		echo '<br><br>';

		//does field exist at all?
		echo '<br><b>Checking '.$this::getTable().'.'.$sFieldName.'</b>';
		echo '<br>does array key "'.$sFieldName.'" exist at all in $this->arrFieldInfo[] ? --> ';
		if (array_key_exists($sFieldName, $this->arrFieldInfo))
			echo 'yes';
		else
		{
			echo '<font color="red"><b>NO!!!.</b></font>';
			echo '<br>-Did you use an abstract class as parent? did you inherit defineTable() by using parent::defineTable(); as the first statement in defineTable() of the child?';
			echo '<br>-Did you include the field at all in defineTable()?';
			echo '<br>-Copy/paste mistake: Did you actually CHANGE the field (like TSysContacts::FIELD_1 ==> TSysContacts::FIELD_2) ';
			echo '<br>-Copy/paste mistake: Did you actually CHANGE the class (like CLASS1::FIELD_NAME ==> CLASS2::FIELD_NAME) ';

			return;
		}

		//define array with required attributes
		$arrRequired = array(
				TSysModel::FI_DEFAULTVALUE, 
				TSysModel::FI_TYPE,
				TSysModel::FI_LENGTH,
				TSysModel::FI_DECIMALPRECISION,
				TSysModel::FI_PRIMARYKEY,
				TSysModel::FI_NULLABLE,
				TSysModel::FI_ENUMVALUES,
				TSysModel::FI_UNIQUE,
				TSysModel::FI_FOREIGNKEY_CLASS,
				TSysModel::FI_FOREIGNKEY_TABLE,
				TSysModel::FI_FOREIGNKEY_FIELD,
				TSysModel::FI_FOREIGNKEY_ACTIONONUPDATE,
				TSysModel::FI_FOREIGNKEY_ACTIONONDELETE,
				TSysModel::FI_FOREIGNKEY_JOIN,
				TSysModel::FI_AUTOINCREMENT,
				TSysModel::FI_UNSIGNED,
				TSysModel::FI_ENCRYPTIONCYPHER,
				TSysModel::FI_ENCRYPTIONDIGEST,
				TSysModel::FI_ENCRYPTIONPASSPHRASE,
				TSysModel::FI_INDEXED,
				TSysModel::FI_FULLTEXT,
			);


		//check if all the required fields exist in $this->arrFieldInfo[$sFieldName]
		foreach($arrRequired as $sReqAtt) //required attributes
		{
			echo '<br>does attribute "'.$sReqAtt.'" exists in $this->arrFieldInfo['.$sFieldName.'] --> ';
			if (array_key_exists($sReqAtt,$this->arrFieldInfo[$sFieldName]))
				echo 'yes';
			else
				echo '<font color="red"><b>NO!!!</b>: "'.$sReqAtt.'" misses for '.$sFieldName.' </font>';			
		}


		// echo '<br><br>vardump() of '.$sFieldName.':<br>';
		// vardump($this->arrFieldInfo[$sFieldName]);		

		echo '<br><br>';
	}


	//END ===== DEFINING THE FIELDS ========


	//=============== CHANGE FIELDS ============

	/**
	 * add a column to the database
	 * 
	 * this function uses the database table definition in this TSysModel object to add the column
	 * 
	 * IMPORTANT: this action is handled immediately in the database
	 * 
	 * @return bool
	 */
	public function alterFieldDBAdd($sFieldName)
	{
		$objPrepStat = null;
		$objConn = null;
		$objConn = $this->getConnectionObject();
		$objPrepStat = $objConn->getPreparedStatement();  

		return $objPrepStat->alterTableAdd($this, $sFieldName);
	}

	/**
	 * modify a column in a database table
	 * 
	 * this function uses the database table definition in this TSysModel object to modify the column to
	 *
	 * IMPORTANT: this action is handled immediately in the database
	 * 
	 * @return bool
	 */
	public function alterFieldDBModify($sFieldName)
	{
		$objPrepStat = null;
		$objConn = null;
		$objConn = $this->getConnectionObject();
		$objPrepStat = $objConn->getPreparedStatement();  

		return $objPrepStat->alterTableModify($this, $sFieldName);
	}

	/**
	 * rename a column in a database table
	 * 
	 * this function uses the database table definition in this TSysModel object to modify the column to
	 *
	 * IMPORTANT: this action is handled immediately in the database
	 * 
	 * @return bool
	 */
	public function alterFieldDBRename($sOldFieldName, $sNewFieldName)
	{
		$objPrepStat = null;
		$objConn = null;
		$objConn = $this->getConnectionObject();
		$objPrepStat = $objConn->getPreparedStatement();  

		return $objPrepStat->alterTableRenameField($this, $sOldFieldName, $sNewFieldName);
	}

	/**
	 * drop a column in a database table
	 *
	 * IMPORTANT: this action is handled immediately in the database
	 * 
	 * @param string $sFieldName name of the field to drop
	 * @param string $sTable table of the field, default = '', TSysModel::getTable() assumed
	 * @return bool
	 */
	public function alterFieldDBDrop($sFieldName, $sTable = '')
	{
		$objPrepStat = null;
		$objConn = null;
		$objConn = $this->getConnectionObject();
		$objPrepStat = $objConn->getPreparedStatement();  

		if ($sTable == '')
			$sTable = $this::getTable();

		return $objPrepStat->alterTableDrop($sTable, $sFieldName);
	}

	//END ===================== ALTER FIELDS ========================



	/**
	 * used for adding an associated array from the database to the internal data array
	 * 
	 * @param array $arrRow
	 */
// 	public function addAssoc($arrRow)
// 	{
// 		$this->arrData[] = $arrRow;
// 		++$this->iCachedCountArrData;
// 		$this->iRecordPointer = $this->iCachedCountArrData -1;
// 	}
	
	/**
	 * clears resultset and recordpointer, 
	 * doe NOT CLEAR not the sql query
	 * 
	 * @param string $bNewQuery
         * @return TSysModel so you can stack
	 */
	public function clear($bNewQuery = true)
	{
//		unset($this->arrData);
//		$this->arrData = array();
		unset($this->arrDataNew);
		$this->arrDataNew = array();
		$this->iCachedCountArrData = 0;
		$this->iRecordPointer = 0;
		
		if ($bNewQuery)
			$this->newQuery();
                
        return $this;
	}
	
	/**
	 * the number of rows in this resultset-object
	 * (no database action)
	 * 
	 */
	public function count()
	{
		return $this->iCachedCountArrData;
	}
	
	/**
	 * reset record pointer
	 */
	public function resetRecordPointer()
	{
		$this->iRecordPointer = 0;
		$this->bFirstTimeNextCalled = true;
	}
	
	/**
	 * wrapper for newQuery()
	 * dont use this funcion, use newQuery() instead
	 */
	public function resetQueryBuilder()
	{
		$this->newQuery();
	}
	
	
	/**
	 * point the recordpointer to record with index $iIndex
	 * if index not a valid value it calls resetRecordPointer() 
	 * 
	 * @param integer $iIndex
	 * @return boolean true if index was valid (so you know if the recordpointer is indeed set)
	 */
	public function setRecordPointerToIndex($iIndex)
	{
		if (is_numeric($iIndex))
		{
			if (($iIndex < $this->count()) && ($iIndex >=0))
			{
				$this->iRecordPointer = $iIndex;
				$this->bFirstTimeNextCalled = true;
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * set recordpointer to a record where the first occurrence of $mValue is found
	 * if not found, recordpointer is not set and function returns false, otherwise true
         * 
         * with this function you can search in the resultset
	 *
	 * @param string $sFieldName
	 * @param mixed $mValue value to match -> be careful with objects, they have to be the same object to return true!
	 * @return bool true if value was found and recordpointer was set
	 */
	public function setRecordPointerToValue($sFieldName, $mValue)
	{
		$iCount = $this->count();
		 
		//we dont want to mess with the internal recordpointer,
		//because it can be in use within a loop wich is calling this function
		//so we want to loop manually
		for ($iPointer = 0; $iPointer < $iCount; ++$iPointer)
		{
//			if (isset($this->arrData[$iPointer][$sFieldName]))
//			{
//				if ($this->arrData[$iPointer][$sFieldName] == $mValue)
//				{
//					$this->iRecordPointer = $iPointer;
//					$this->bFirstTimeNextCalled = true;
//					return true;						
//				}
//			}
			if (isset($this->arrDataNew[$iPointer][''][$sFieldName]))
			{
				if ($this->arrDataNew[$iPointer][''][$sFieldName][TSysModel::DATA_VALUE] == $mValue)
				{
					$this->iRecordPointer = $iPointer;
					$this->bFirstTimeNextCalled = true;
					return true;						
				}
			}
		}
		return false;
	}
		
	/**
	 * return a copy of the model 
	 * the difference between cloning and copying is  that a clone a 100%
	 * duplicate is of an object, while a copy is for the current record with a new id
	 *
	 * @return TSysModel
	 */
	public function getCopy()
	{
		$objCopy = clone $this;
		$objCopy->set(TSysModel::FIELD_ID, TSysModel::FIELD_ID_VALUE_DEFAULT);//setting to default id
		$objCopy->setNew(true);
		$objCopy->setDirty(true);
		
		return $objCopy;
	}	
	
	/**
	 * adds a copy from record(s) from $objModelToCopy to current model
	 * (no database action, purely internal)
	 * 
	 * @param TSysModel $objModelToCopy model you want to copy a record of
	 * @param bool $bOnlyCurrentRecord false=copy all, true = current record pointed to by recordpointer
	 */
	public function addCopy(TSysModel $objModelToCopy, bool $bOnlyCurrentRecord = false)
	{
		if ($bOnlyCurrentRecord)
		{
			if (count($objModelToCopy->arrDataNew) > 0)
				$this->arrDataNew[] = $objModelToCopy->arrDataNew[$objModelToCopy->iRecordPointer];
		}
		else //copy all
		{
			$iCountCopy = count($objModelToCopy->arrDataNew);
			for ($iIndexCopy = 0; $iIndexCopy < $iCountCopy; $iIndexCopy++)
				$this->arrDataNew[] = $objModelToCopy->arrDataNew[$iIndexCopy];
		}

		//update count
		$this->iCachedCountArrData = count($this->arrDataNew);
	}

	/**
	 * removes current record
	 */
	public function removeRecord()
	{
		$this->removeRecordAtRecordpointer($this->iRecordPointer);
		$this->iRecordPointer--;

		//prevent recordpointer pointing to invalid value
		if ($this->iRecordPointer < 0)
			$this->iRecordPointer = 0;
	}

	/**
	 * Removes record from internal data array at index $iRecordPointer
	 * (no database action)
	 */
	public function removeRecordAtRecordpointer($iRecordPointer)
	{
		unset($this->arrDataNew[$iRecordPointer]);
		$this->iCachedCountArrData = count($this->arrDataNew);		
	}

	/**
	 * return recordpointer
	 */
	public function getRecordPointer()
	{
		return $this->iRecordPointer;
	}
        
        /**
         * Does the recordpointer point to the first record?
		 * 
		 * (handy for example for comma separated lists, so you know
		 * to add a comma or not)
         * 
         * @return boolean
         */
        public function isFirstRecord()
        {
            return ($this->iRecordPointer == 0);                    
        }
        
        /**
         * Does the recordpointer point to the last record?
		 * 
		 * if you can, use isFirstRecord() especially in loops, 
		 * because it's faster
         * 
         * @return boolean
         */
        public function isLastRecord()
        { 
            return ($this->iRecordPointer == ($this->iCachedCountArrData -1));
        }
	
	/**
	 * returns fieldnames of the table that are defined in this class
	 * 
	 * when you ask for a resulset with referenced tables, 
	 * the referenced fields in external tables are missing, 
	 * use getFieldnames() instead
	 */
	public function getFieldsDefined()
	{
		return array_keys($this->arrFieldInfo);
	}
	
	/**
	 * returns if the field is defined
	 * 
	 * @param string $sFieldName
	 * @return boolean
	 */
	public function getFieldsDefinedExists($sFieldName)
	{
		return isset($this->arrFieldInfo[$sFieldName][$this->arrFieldInfo]); //changed from array_key_exists => isset on 17-10-2025
	}
	
	/**
	 * looks in the defined fields for the ones that refer to external tables
	 * @return array of strings with defined fields that refer to external tables
	 */
	public function getFieldsDefinedJoined()
	{
		$arrFields = array_keys($this->arrFieldInfo);
		$arrReturn = array();

		foreach ($arrFields as $sField)
		{
			if (isset($this->arrFieldInfo[$sField][TSysModel::FI_FOREIGNKEY_FIELD]))
				$arrReturn[] = $sField;
		}

		return $arrReturn;
	}
	

	/**
	 * return all fieldnames of the resulset (including fields of external tables)
	 * 
	 * use when you want the values of the referenced tables as well
	 * when you want only the defined values, use getFieldnamesDefined() instead
	 *
	 *
	 * @param $bFilterSystemFields filters system fields from array (lower performance), otherwise it returns also system fields
	 */
	public function getFields($bFilterSystemFields = false, $sDBTable = '')
	{
		if ($bFilterSystemFields)
		{
			$arrSys = $this->getFieldsSystem();
//			$arrAllFields = array_keys($this->arrData[$this->iRecordPointer]);
			$arrAllFields = array_keys($this->arrDataNew[$this->iRecordPointer][$sDBTable]);
			$arrReturn = array();
			$arrReturn = array_merge(array_diff($arrAllFields, $arrSys));//array_dif removes elements but preserves arrayindexes, therefore array_merge
			return $arrReturn;
		}
		else
                {
//			return array_keys($this->arrData[$this->iRecordPointer]);
			return array_keys($this->arrDataNew[$this->iRecordPointer][$sDBTable]);
                }
	}	
	
	/**
	 * @return array of string with system fieldnames (bDirty, bNew)
	 */
	public function getFieldsSystem()
	{
		return array(TSysModel::FIELD_SYS_DIRTY, TSysModel::FIELD_SYS_NEW);
	}
	



	/**
	 * looping through the rows
	 * 
	 * this function supposed to be called in a while loop.
	 * while ($objRst->next())
	 * { //do stuff }
	 * 
	 */
	public function next()
	{
		/**
		 * this function is a bit tricky because the recordpointer is always pointing to the first record
		 * so if we call next() in a while loop, we set the recordpointer to the second record instead of the first  
		 */
		
		if ($this->bFirstTimeNextCalled == false)
			++$this->iRecordPointer;
		$this->bFirstTimeNextCalled = false;
		if ($this->iRecordPointer < $this->iCachedCountArrData)
			return true;
		else 
		{
			--$this->iRecordPointer;//point to a valid record, not beyond the last
			return false;
		}
	}

    /**
     * return an array with fields to search from a website.
     * i.e. for users you can search a remark field, but not the password field
     *
     * function can return null
     *
	 * @return array function returns array WITHOUT tablename
     */
    public function getFieldsSearchable()
    {
    	return $this->getFieldsPublic();
    }
    

    
    /**
     * return an array with fields that can be exported
     * i.e. for indexing fields, so you dont have to use the real fieldnames in urls
     *
     * without overloading it returns the getPublicFields() values
     *
     * function can return null
     *
	 * @return array function returns array WITHOUT tablename
     */
    public function getFieldsExportable()
    {
    	return $this->getFieldsPublic();
    }    
    
    
    /**
     * uitvoeren van een query
	 *
     * @return bool is alles goed gegaan ? (true = no errors)
     */
    protected function executeSelectQueryDB($sSQL, $iMaxResultCount = 0, $iOffset = 0)
    {
    
	    	try
	    	{
	    		//check permissions
	    			$objPrepStat = $this->getConnectionObject()->getPreparedStatement();    
	    			if ($objPrepStat->executeSelect($this, $sSQL, $iMaxResultCount, $iOffset))
	    			{
	    
		    			if (!$this->checksumMatch())
		    			{
		    				error_log('checksum doesn\'t match of 1 or more items');
		    				//error('possible corrupt data: checksum of 1 or more items doesnt match');
		    				return false;
		    			}
		    			else
		    				return true;
	    			}
	    			else 
	    				return false;
	    	}
	    	catch (Exception $objException)
	    	{
	    		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
	    		return false;
	    	}
    
    }    
    
    
    /**
     * create database table
     *
     * @return bool create table successful?
     */
    public function createDBTable()
    {
    	if (!$this->getConnectionObject()->getPreparedStatement()->tableExists($this::getTable()))
   		{   			
   			return $this->getConnectionObject()->getPreparedStatement()->createTableFromModel($this);
   		}
     
   		return true;
    }    
    
    /**
     * drop the created table from database
     *
     * @return bool drop successful?
     */
    public function dropDBTable()
    {
    	if ($this->getConnectionObject()->getPreparedStatement()->tableExists($this::getTable()))
    		return $this->getConnectionObject()->getPreparedStatement()->dropTable($this::getTable());
   		else
   			return true;
    }
        
    
    /**
     * make a HTML dropdown select-box with records from this model
     * the getDisplayRecordShort() is used for displaying
     *
	 * <select name="cars" id="cars">
     *   <option value="1">Volvo</option>
     *   <option value="2">Saab</option>
     *   <option value="3">Mercedes</option>
     *   <option value="4">Audi</option>
	 * </select>
	 * 
     * @param mixed $sValueSelectedItem this value is selected in dropdown select-box (can be a string or int)
     * @param Select $objHTMLSelectbox (if null then a new object is returned from this function)
	 * @param string $sFieldAsValue what field to use as value? default is FIELD_ID: <option value="$sFieldValue">Volvo</option>
	 * @param string $sFieldText what field to use as description text of that <option>-tag? When empty getGUIItem() is assumed: <option value="1">$sFieldText</option>. 
     */
    public function generateHTMLSelect($mValueSelectedItem = TSysModel::FIELD_ID_VALUE_DEFAULT, 
									&$objHTMLSelectbox = null, 									
									$sFieldValue = TSysModel::FIELD_ID,
									$sFieldText = '')
    {
    	if ($objHTMLSelectbox == null)
    		$objHTMLSelectbox = new Select();

    	$iPreviousRecordPointer = $this->iRecordPointer; //temp save recordpointer
    	$this->resetRecordPointer();
    	while($this->next())
    	{    		
    		$objOption = new Option();
    		$objOption->setValue ( $this->get($sFieldValue) );

			if ($sFieldText == '')
    			$objOption->setText ( $this->getDisplayRecordShort() );
			else
				$objOption->setText ( $this->get($sFieldText) );

    		if ($mValueSelectedItem != TSysModel::FIELD_ID_VALUE_DEFAULT)
    		{
	    		if ($this->getID() == $mValueSelectedItem)
	    			$objOption->setSelected ( true );
    		}	    		
	    	$objHTMLSelectbox->appendChild ( $objOption );    		
    	}  
    	$this->iRecordPointer = $iPreviousRecordPointer;//restore recordpointer
    	
    	return $objHTMLSelectbox;
    }
    
    /**
     * make a HTML dropdown select-box from the enum values of field $sFieldName 
     *
     * @param string $sFieldName the fieldname with the enum values
     * @param string $sSelectedEnumValue (can be empty)
     * @param  select $objHTMLSelectbox (if null then a new object is returned from this function)
     * @return select (the existing ($objExistingHTMLSelectboxToFill from the parameter) or a new object if parameter was null)
     */
    public function generateHTMLSelectEnum($sFieldName, $sSelectedEnumValue = '', $objHTMLSelectbox = null)
    {
    	if ($objHTMLSelectbox == null)
    		$objHTMLSelectbox = new Select ();
    	
    	$objPrepStat = $this->getConnectionObject()->getPreparedStatement();
    	
    	$arrEnum = $this->getFieldEnumValues($sFieldName);
    	if ($arrEnum)
    	{
    		foreach($arrEnum as $sValue)
    		{
    			$objOption = new Option ();
    			$objOption->setValue ( $sValue );
    			$objOption->setText ( $objPrepStat->getSafeFieldValue($sValue) );
    			$objHTMLSelectbox->appendChild ( $objOption );
    		}
    	}
    	else
    		error_log ('generateHTMLSelectEnum(): no enum values defined for field '.$sFieldName);
    	return $objHTMLSelectbox;
    }    
    
    
    /**
     * change order of record in table: one up, or one down
     *
     * uses (TSysModel::FIELD_POSITION)
     *
     * manipulates database directly, does no loading and not filling internal fields
     *
     * Change up or down is relative to the sort order of the list.
     *
     * @param int $iID
     * @param int $bGoUp change UP , FALSE = change DOWN
     * @param string $sSortOrder
     */
    public function positionChangeOneUpDownDB($iID, $bGoUp = true, $sSortOrder = SORT_ORDER_ASCENDING)
    {
        if (!is_numeric($iID)) //prevent injection
            return false;
        if (is_int($bGoUp))
            $bGoUp = intToBool($bGoUp);
        if ($sSortOrder != SORT_ORDER_DESCENDING)
            $sSortOrder = SORT_ORDER_ASCENDING; //default is ascending
        
    	//$objPrepStat = $this->getConnectionObject()->getPreparedStatement();
    	
    	//wissel sort order
    	if ($sSortOrder == SORT_ORDER_DESCENDING) 
    		$bGoUp = !$bGoUp; //flip sort order
    		
    	if ($this->getTableUseOrderField())
    	{
			$objConn = null;
			$objConn = $this->getConnectionObject();
			$objConn->beginTransaction();
                
    		//load current
    		$objCurrRecord = clone $this;
    		$objCurrRecord->newQuery();
    		$objCurrRecord->clear();
//     		$objCurrRecord->select($this->getFieldsDefined());
    		$objCurrRecord->find(TSysModel::FIELD_ID, $iID);
    		$objCurrRecord->limitOne();
    		if (!$objCurrRecord->loadFromDB(false))
    			return false;
    		$iCurrOrder = $objCurrRecord->get(TSysModel::FIELD_POSITION);

	
    		//load PREVIOUS or NEXT record (depending on sort order)
    		$objOtherRecord = clone $this;
    		$objOtherRecord->newQuery();
    		$objOtherRecord->clear();
//     		$objOtherRecord->select($this->getFieldsDefined());
    		if ($bGoUp)
    		{
    			$objOtherRecord->find(TSysModel::FIELD_POSITION, $iCurrOrder, COMPARISON_OPERATOR_LESS_THAN);
    			$objOtherRecord->sort(TSysModel::FIELD_POSITION, SORT_ORDER_DESCENDING);
    		}
    		else
    		{ 
    			$objOtherRecord->find(TSysModel::FIELD_POSITION, $iCurrOrder, COMPARISON_OPERATOR_GREATER_THAN);
    			$objOtherRecord->sort(TSysModel::FIELD_POSITION, SORT_ORDER_ASCENDING);
    		}
    		if (!$objOtherRecord->loadFromDB(false))
    			return false;
    		if ($objOtherRecord->count() == 0)//there is no record, no party
    			return false;
    		    	    			
    		//swap orders and save
    		$objCurrRecord->set(TSysModel::FIELD_POSITION, $objOtherRecord->get(TSysModel::FIELD_POSITION));
    		if ($objCurrRecord->saveToDB()) //only continue if save ok
    		{
				$objOtherRecord->set(TSysModel::FIELD_POSITION, $iCurrOrder);
				if ($objOtherRecord->saveToDB())
					$objConn->commit();
   			}
   			
            $objConn->rollback();
   			return false;//hij hoort hier niet te komen

    	}
    	else
    		logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'TSysModel is not set on using the order field (overloading function getTableUseOrderField())');
    
    	return false;
    }    

	 /**
     * change order of record in table to a new position
     *
     * uses (TSysModel::FIELD_POSITION)
     *
     * WARNING: manipulates database directly, does no loading and not filling internal fields
     *
     * The reason we use $iAfterID instead of $iBeforeID is because for inserting at the beginning we can use -1 as a fixed number, 
	 * when we would use $iBeforeID we run into trouble with the last item, because that can be literally any number
	 * 
     * @param int $iCurrentID id of the current record
     * @param int $iAfterID id of record $iID will be inserted after. 0 means: at the beginning
     * @return bool change successful?
     */
    public function positionChangeDB($iCurrentID, $iAfterID)
    {
		$iPosCurr = 0;
		$iPosAfter = 0;
		$objModelAfter = clone $this;
		$objModelCurr = clone $this;
		global $objDBConnection;

		//checks
		if ((!is_numeric($iCurrentID)) || (!is_numeric($iAfterID)))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'positionChangeDB() id or after id is not numeric');
			return;
		}		

		//get position number $iID
		$objModelCurr->newQuery();
		$objModelCurr->clear();
		// $objModelCurr->select(array(TSysModel::FIELD_ID, TSysModel::FIELD_POSITION));//auto selects all
		$objModelCurr->find(TSysModel::FIELD_ID, $iCurrentID);
		$objModelCurr->limitOne();
		if (!$objModelCurr->loadFromDB(false))
			return false;
		$iPosCurr = $objModelCurr->get(TSysModel::FIELD_POSITION);

		//get position number $iAfterID. if $iAfterID == 0, then pos $iAfterID = 0
		if ($iAfterID == 0) //beginning
			$iPosAfter = 0;
		else //not beginning
		{
			$objModelAfter->newQuery();
			$objModelAfter->clear();
			// $objModelAfter->select(array(TSysModel::FIELD_ID, TSysModel::FIELD_POSITION)); //auto select all
			$objModelAfter->find(TSysModel::FIELD_ID, $iAfterID);
			$objModelAfter->limitOne();
			if (!$objModelAfter->loadFromDB(false))
				return false;
			$iPosAfter = $objModelAfter->get(TSysModel::FIELD_POSITION);
		}

		//if moving up (pos $iID > $iAfterID):
		//	all records between pos $iID and pos $iAfterID INcrement by 1
		if ($iPosCurr > $iPosAfter)
		{
			$objQuery = $objDBConnection->getQuery();
			$objQuery->updateFieldIncrementBetween($this->getTable(), TSysModel::FIELD_POSITION, TSysModel::FIELD_POSITION, $iPosCurr, $iPosAfter);
		}

		//if moving down (pos $iID < $iAfterID):
		//	all records betweem pos $iID and pos $iAfterID DEcrement by 1
		if ($iPosCurr < $iPosAfter)
		{
			$objQuery = $objDBConnection->getQuery();
			$objQuery->updateFieldDecrementBetween($this->getTable(), TSysModel::FIELD_POSITION, TSysModel::FIELD_POSITION, $iPosCurr, $iPosAfter);
		}

		//new position of $iID becomes position of $iAfterID + 1 (if -1, it becomes 0)
		if ($iPosAfter == 0)
			$objModelCurr->setPosition(1);
		else
		{
			if ($iPosCurr < $iPosAfter) //moving up or down?
			{ //moving down
				$objModelCurr->setPosition($iPosAfter); 
			}
			else
			{ //moving up
				$objModelCurr->setPosition($iPosAfter + 1); // I have to add +1 to position
				$objModelAfter->setPosition($objModelAfter->getPosition()); //this doesn't seem to do anything, but it triggers the setDirty(). it's here to emphasize that the position is restored to original position
			}
		}			
		if (!$objModelCurr->saveToDB())
			return false;
		if ($iPosAfter != 0)
			if ($objModelAfter->saveToDB()) //save the position again to original position, because it is changed with the updateFieldDecrementBetween()
				return true;

		return true;
	}

    /**
     * this function creates table in database and calls all foreign key classes to do the same
     * 
     * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
     *
	 * after install() propagateData() is called (this is done because if installation takes too long/broken off/canceled, the likelyhood of the database being corrupt is lower (data propagation is less important than install))
	 * 
	 * DIFFERENCE: install VS propagate 
	 * - install installs the minimum necessary data for database to function 
	 * - propagate installs convenient data
	 * 
	 * for example: languages
	 * You want to install at a minimum 1 language (=install), 
	 * but it is convenient to have all languages in the world in the database (=propagate)
	 * 
	 * 
     * @param array $arrPreviousDependenciesModelClasses with classnames. if null, dependencies not installed
     * @return bool success?
     */
    public function install($arrPreviousDependenciesModelClasses = null)
	{
		$arrFieldsDefined = $this->getFieldsDefined();
    	
		//first the foreign tables
		foreach ($arrFieldsDefined as $sFieldDefined)
		{

			if (($this->getFieldForeignKeyTable($sFieldDefined)) && ($this->getFieldForeignKeyField($sFieldDefined)))
			{
				$sClass = $this->getFieldForeignKeyClass($sFieldDefined);
			
				if ($arrPreviousDependenciesModelClasses != null)
				{
					//prevent unnessary endless loop
					if (!in_array($sClass, $arrPreviousDependenciesModelClasses))
					{
							$arrPreviousDependenciesModelClasses[] = $sClass;

							//retrieve external model and install it
							$sClass = $this->getFieldForeignKeyClass($sFieldDefined);
							$objModelExt = new $sClass();
							if (!$objModelExt->install($arrPreviousDependenciesModelClasses))
									return false;
							unset($objModelExt);					
					}
				}

			}	
		}		
        
    	//creating the table itself for this class
    	error_log('creating '.$this::getTable().' ...');
    	if (!$this->createDBTable())
    	{
    		error_log($this::getTable().' create table error');
    		return false;
    	}
    
    
    	return true;
    }    
    
    
    /**
     * removes the table and its dependencies from the database
     *
     * @var array $arrPreviousDependenciesModelClasses deze parameter is er om te voorkomen dat er oneindige recursie onstaat en klassen 2x aangeroepen worden. if null then dependencies not deinstalled
     * @return bool success?
     */
    public function uninstallDB($arrPreviousDependenciesModelClasses = null)
    {
    	$arrFieldsDefined = $this->getFieldsDefined();
    	 
    	//first delete this object
    	error_log('dropping table '.$this::getTable().' ...');
    	if (!$this->dropDBTable())
    	{
    		error_log($this::getTable().' drop table error');
    		return false;
    	}
    	 
    	
    	//then the foreign tables
    	foreach ($arrFieldsDefined as $sFieldDefined)
    	{
    	
    		if (($this->getFieldForeignKeyTable($sFieldDefined)) && ($this->getFieldForeignKeyField($sFieldDefined)))
    		{
    			$sClass = $this->getFieldForeignKeyClass($sFieldDefined);
    				
    			//prevent unnessary endless loop
                        if ($arrPreviousDependenciesModelClasses != null)
                        {
                            if (!in_array($sClass, $arrPreviousDependenciesModelClasses))
                            {
                                    $arrPreviousDependenciesModelClasses[] = $sClass;

                                    //retrieve external model and install it
                                    $sClass = $this->getFieldForeignKeyClass($sFieldDefined);
                                    $objModelExt = new $sClass();
                                    if (!$objModelExt->uninstallDB($arrPreviousDependenciesModelClasses))
                                            return false;
                                    unset($objModelExt);
                            }
                        }
    	
    		}
    	}    	
    	    
    	return true;
    }    
    
    
    /**
     * update the table and its dependencies in the database
     *
     * @var array $arrPreviousDependenciesModelClasses  = null when no previous dependencies exist. deze parameter is er om te voorkomen dat er oneindige recursie onstaat en klassen 2x aangeroepen worden
     * @return bool success?
     */
    public function refactorDB($arrPreviousDependenciesModelClasses, TSysTableVersions $objTableVersionsFromDB)
    {
    	$arrFieldsDefined = $this->getFieldsDefined();
    
    	 
    	//first the foreign tables
    	foreach ($arrFieldsDefined as $sFieldDefined)
    	{
    		 
    		if (($this->getFieldForeignKeyTable($sFieldDefined)) && ($this->getFieldForeignKeyField($sFieldDefined)))
    		{
    			$sClass = $this->getFieldForeignKeyClass($sFieldDefined);
    
                        if ($arrPreviousDependenciesModelClasses != null)
                        {
                            //prevent unnessary endless loop
                            if (!in_array($sClass, $arrPreviousDependenciesModelClasses))
                            {
                                    $arrPreviousDependenciesModelClasses[] = $sClass;

                                    //retrieve external model and install it
                                    $sClass = $this->getFieldForeignKeyClass($sFieldDefined);
                                    $objModelExt = new $sClass();
                                    if (!$objModelExt->refactorDB($arrPreviousDependenciesModelClasses, $objTableVersionsFromDB))
                                            return false;
                                    unset($objModelExt);
                            }
                        }
    			 
    		}
    	}
    	 
    	//nu deze klasse/tabel zelf updaten
    	error_log('lookup if table '.$this::getTable().' needs update ...');
    	if ($objTableVersionsFromDB->setRecordPointerToValue(TSysTableVersions::FIELD_TABLENAME, $this::getTable()))
    	{
    		$iOldVersion = $objTableVersionsFromDB->get(TSysTableVersions::FIELD_VERSIONNUMBER);
    		if (is_numeric($iOldVersion))
    		{
    			if ($iOldVersion < $this->getVersion())
    			{
    				error_log('updating table '.$this::getTable().' from version '.$iOldVersion. ' to '.$this->getVersion());
			    	if (!$this->refactorDBTable($iOldVersion))
			    	{
			    		error_log($this::getTable().' update table error');
			    		return false;
			    	}
			    	$objTableVersionsFromDB->set(TSysTableVersions::FIELD_VERSIONNUMBER, $this->getVersion());
			    	$objTableVersionsFromDB->saveToDB();//update version number in database
    			}
    		}
    	}
    
    	return true;
    }    
    
    /**
     * returns if value already exists in resultset of model for a field
     * it loops through all the records, 
     * NO database action involved
     * 
     * @param string $sFieldName
     * @param mixed $mValue value to match -> be careful with objects, they have to be the same object to return true!
     */
    public function existsValue($sFieldName, $mValue)
    {
    	$iCount = $this->count();
    	
    	//we dont want to mess with the internal recordpointer, 
    	//because it can be in use within a loop wich is calling this function
    	//so we want to loop manually
    	for ($iPointer = 0; $iPointer < $iCount; ++$iPointer)
    	{
//    		if (isset($this->arrData[$iPointer][$sFieldName]))
//    		{
//    			if ($this->arrData[$iPointer][$sFieldName] == $mValue)
//    				return true;  
//    		}
    		if (isset($this->arrDataNew[$iPointer][''][$sFieldName][TSysModel::DATA_VALUE]))
    		{
    			if ($this->arrDataNew[$iPointer][''][$sFieldName][TSysModel::DATA_VALUE] == $mValue)
    				return true;  
    		}
    	}
    	return false;
    }
    
    
	/**
	 * start an atomic database transaction
	 * 1) don't forget to use commit() or rollback(), 
	 * otherwise the database connection is kept open and queries not executed
	 * 2) don't forget to switch off database transactions with saveToDB(false)!
	 *
	 * @return void
	 */
	public function startTransaction()
	{
		$this->getConnectionObject()->startTransaction();
	}

	/**
	 * commit an atomic database transaction
	 * don't forget to use startTransaction() before committing it 
	 *
	 * @return void
	 */	
	public function commitTransaction()
	{
		$this->getConnectionObject()->commit();
	}

	/**
	 * rollback an atomic database transaction
	 * don't forget to use startTransaction() before rolling it back
	 *
	 * @return void
	 */	
	public function rollbackTransaction()
	{
		$this->getConnectionObject()->rollback();
	}



	/**
	 * show the resultset of the query in <table></table> format
	 * dump table
	 * 
	 * @return string with tabledump
	 */
	public function debugResultset($bDumpToScreen = true)
	{
		$sResult = '';

		if (!APP_DEBUGMODE)
		{
			$sResult .= 'debugResultset(): sorry, not in development mode';
			return;
		}
		
		$sResult .= '<br><br><br><b>TSysModel:debugResultset()</b><br>';
		if ($this->count() == 0)
		{
			$sResult .= '<font color="red"><b>[NO RECORDS TO DISPLAY]</b></font>';

			if ($bDumpToScreen)
				echo $sResult;		
			
			return $sResult;
		}


		$this->resetRecordPointer();

// $this->getFields
// vardump($this->arrDataNew);

		//tableheader
		$sResult .= '<table>'."\n";
		$sResult .= '<thead>'."\n";
		//while($this->next()) --> only first record
		{	
			$arrTables = array_keys($this->arrDataNew[$this->iRecordPointer]);

			foreach($arrTables as $sDBTable)
			{
				$arrFieldNames = array_keys($this->arrDataNew[$this->iRecordPointer][$sDBTable]);

				foreach($arrFieldNames as $sFieldName)
				{
					$sResult .= '<td><b>'. $sFieldName.'</b></td>'."\n";
				}
	
			}
		}
		$sResult .= '</thead>'."\n";
		
		//table body	
		$sResult .= '<tbody>'."\n";
		while($this->next()) 
		{	
			$sResult .= '<tr>'."\n";
			$arrTables = array_keys($this->arrDataNew[$this->iRecordPointer]);

			foreach($arrTables as $sDBTable)
			{
				$arrFieldNames = array_keys($this->arrDataNew[$this->iRecordPointer][$sDBTable]);

				foreach($arrFieldNames as $sFieldName)
				{
					$sResult .= '<td>';

					$iFType = $this->getFieldType($sFieldName);

					switch ($iFType)
					{
						case CT_DATETIME:
							$sResult .= $this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE]->getDateAsString('d-m-Y h:m:i');
							break;
						case CT_CURRENCY:
						case CT_DECIMAL:								
							$sResult .= $this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE]->getValue();
							break;
						default:
							$sResult .=$this->arrDataNew[$this->iRecordPointer][$sDBTable][$sFieldName][TSysModel::DATA_VALUE];
					}					
					echo '</td>';
				}
			}
			$sResult .= '</tr>'."\n";
		}
		$sResult .= '</tbody>'."\n";
		$sResult .= '</table>'."\n";


		if ($bDumpToScreen)
			echo $sResult;

		return $sResult;
			
	}

	/**
	 * generate data to insert into database
	 * This is called after install()	 
	 * this is done because if installation takes too long/broken off/canceled, the likelyhood of the database being corrupt is lower (data propagation is less important than install)
	 *
	 * DIFFERENCE: install VS propagate 
	 * - install installs the minimum necessary data for database to function 
	 * - propagate installs convenient data
	 * 
	 * for example: languages
	 * You want to install at a minimum 1 language (=install), 
	 * but it is convenient to have all languages in the world in the database (=propagate)
	 * 
	 * PLEASE OVERLOAD THIS FUNCTION IN CHILD CLASS!!!
	 * 
	 * @return bool true = propagation succeeded, false = error
	 */
	public function propagateData()
	{
		return true;
	}


	/**
	 * This function compares 2 models and takes necessary database actions to update the old model.
	 * $this class is the OLD model, parameter $objNewModel is the NEW TSysModel to compare to.
	 * 
	 * WARNING:
	 * The synchronisation happens based on FIELD_ID, so database records must have a FIELD_ID
	 * 
	 * WHAT HAPPENS:
	 * 1. when old model has other records than new: it deletes the records that do not exist in new model
	 * 2. when the new model has new records: it adds these records to database
	 * 3. existing records will be saved (thus updated)
	 * 
	 * HOW IS THIS USEFUL:
	 * When a record has subrecords, you need to update the subrecords otherwise you have to delete everything and add new (wasting id's in database)
	 * Example 1: for invoices you store invoice-lines. When a user edits the invoice they can remove a line or add a line on the invoice. You want to synchronize the differences instead of creating new invoice-lines each time.
	 * Example 2: for products you store multiple images. A user can edit a product and add or remove images. You want to synchronize the differences instead of creating new image-records each time.
	 */
	public function synchronizeDifferenceDB(TSysModel $objNewModel)
	{
		if (!$this->getTableUseIDField())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Table doesnt use id field');
			return false;
		}

		//@todo: make
	}
    
//=====================================================================================

	// ************************************************
	// ====== ONLY ABSTRACT FUNCTIONS BELOW ==========
	//
	// *   for easy copy/pasting in child classes     *
	// ************************************************
	
//=====================================================================================    
    
    
    
	
	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
	 * 
	 * initialize values
	 */
	abstract public function initRecord();
    
    
    
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	 */
    abstract public function defineTable();
    
	
	/**
	 * returns an array with fields that are publicly viewable
	 * sometimes (for security reasons the password-field for example) you dont want to display all table fields to the user
	 *
	 * i.e. it can be used for searchqueries, sorting, filters or exports
	 * 
	 * @return array function returns array WITHOUT tablename
	 */
	abstract public function getFieldsPublic();
	
	/**
	 * use the auto-added id-field ?
         * you need explicitly use getTableUseRandomID() to use it as primary field
	 * @return bool
	*/
	abstract public function getTableUseIDField();
        

	/**
	 * use a second numeric id that has no logically follow-up numbers? 
	 * 
	 * a second unique key (randomid) can be useful for security reasons
	 * @return bool
	 */
	abstract public function getTableUseRandomID();
	
	/**
	 * is randomid field a primary key?
	 * @return bool
	 */
	abstract public function getTableUseRandomIDAsPrimaryKey();


	/**
	 * use a third character-based id that has no logically follow-up numbers
	 * 
	 * a tertiary unique key (uniqueid) can be useful for security reasons like login sessions: you don't want to _POST the follow up numbers in url
	 * @return bool
	 */
	abstract public function getTableUseUniqueID();
	

	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	abstract public function getTableUseNiceID();
	
        
	/**
	 * use the auto-added date-changed & date-created field ?
	 * @return bool
	*/
	abstract public function getTableUseDateCreatedChangedField();
	
	
	/**
	 * use the checksum field ?
	 * @return bool
	*/
	abstract public function getTableUseChecksumField();
	
	/**
	 * order field to switch order between records
 	 * @return bool
	*/
	abstract public function getTableUseOrderField();
	
	/**
	 * use checkout for locking file for editing
     * a lock won't expire, a checkout will
	 * @return bool
	*/
	abstract public function getTableUseCheckout();
		 
	/**
	 * use lock for locking file for editing
    * a lock won't expire, a checkout will
	* @return bool
	*/
	abstract public function getTableUseLock();
        
	/**
	 * use image in your record?
	 * Then the image_thumbnail, image_medium, image_large and image_max fields are used
    * if you don't want a small and large version, use this one
	* @return bool
	*/
	abstract public function getTableUseImageFile();        
        
	/**
	 * is this model a translation model?
	 *
	 * @return bool is this model a translation model?
	 */
	abstract public function getTableUseTranslationLanguageID();
        
	/**
	 * is this record the default selected record in the database?
	 *
	 * @return bool 
	 */
	abstract public function getTableUseIsDefault();
        
	/**
	 * is this record favorited by the user?
	 *
	 * @return bool
	 */
	abstract public function getTableUseIsFavorite();
        
	/**
	 * can record be transcanned?
	 * Trashcan is an extra step in for deleting a record
	 *
	 * @return bool
	 */
	abstract public function getTableUseTrashcan();
        
	/**
	 * use a field for search keywords?
	 * (also known als tags or labels)
	 *
	 * @return bool
	 */
	abstract public function getTableUseSearchKeywords();
        
        
	/**
	 * do records have to be deleted from table or just hidden?
	 * 
	 *
	 * returnvalue interpretation:
	 * true = remove fysically from table
	 * false = uses bIsDeleted field to hide record
	 *
	 * @return bool moeten records fysiek verwijderd worden ?
	*/
	abstract public function getTablePhysicalDeleteRecord();
	
	
	
	
	/**
	 * type of primary key field
	 *
	 * @return integer with constant CT_AUTOINCREMENT or CT_INTEGER32 or something else that is not recommendable
	*/
	abstract public function getTableIDFieldType();
	
	
	/**
	 * de child moet deze overerven
	 *
	 * @return string name of database table
	*/
    //abstract protected function getTable(); ->> a static function with the same name needs to be defined in the child class (this is checked in the constructor of TSysModel)
	
	
	
	/**
	 *
	 * Used for GUI functions (like making <OPTION>-boxes and abbreviated records on mobile phones)
	 * which value is to be displayed in the <OPTION>welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 * 
     * PREVIOUSLY KNOWN AS: getGUIItem()
     * 
 	* @return string name of record abreviated
	*/
	abstract public function getDisplayRecordShort();
	
		
	/**
	 * inherit function to make a checksum for your own table
	 * do this by returning the values of the most important fields
	 * When saving this value will be hashed
	 *
	 * WARNING:
	 * NEVER use getID(), getRandomID(), getPosition(), getIsDefault()
	 * because these values will be assigned a value AFTER saving (which value is not known yet BEFORE saving),
	 * thus the checksum will fail!!!
	 *
	 * @return string
	*/
	abstract public function getChecksumUncrypted();
	
	
	/**
	 *
	 * checken of alle benodigde waardes om op te slaan wel aanwezig zijn
	 * 
	 * @return bool true=ok, false=not ok
	*/
	abstract public function areValuesValid();	
	
	/**
	 * for the automatic database table upgrade system to work this function
	 * returns the version number of this class
	 * The update system can compare the version of the database with the Business Logic
	 *
	 * default with no updates = 0
	 * first update = 1, second 2 etc
	 *
	 * @return int
	 */
	abstract public function getVersion();
	
	/**
	 * DATABASE REFACTORING
	 * update the table in the database 
	 * (may have been changes to fieldnames, fields added or removed etc)
	 *
	 * @param int $iFromVersion upgrade vanaf welke versie ?
	 * @return bool is alles goed gegaan ? true = ok (of er is geen upgrade gedaan)
	 */
	abstract protected function refactorDBTable($iFromVersion);
	

}
?>