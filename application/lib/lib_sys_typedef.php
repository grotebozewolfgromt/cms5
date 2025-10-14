<?php
/**
 * In this library exist only type related functions, such as type conversion
 *
 * IMPORTANT:
 * This library is language independant, so don't use language specific element
 *
 * 4 april 2019: split lib_types into lib_types and lib_typedef
 * 15 aug 2025: lib_sys_typedef: added SESSIONARRAYKEY_CACHE
 * 
 * @author Dennis Renirie
 */


//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_file.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_img.php'); 
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_inet.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_math.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_misc.php');
//include_once(APP_PATH_CMS_LIBRARIES.DIRECTORY_SEPARATOR.'lib_string.php');



define('CHARSET_UTF8', 'UTF-8');//the mb_detect_encoding function returns this value when UTF8


/**
 * Extra defined datatypes
 * 
 * Soms is het handig om een datatype te kunnen definieren (detecteren is niet altijd een haalbare optie)
 * zoals bijvoorbeeld als parameter in een functie
 * 
 * prefix TP_ stands for TyPe. the prefix T_ is already used by PHP self
 * prefix CT_ stands for Column Type, because it is explicitly used in databases
 * the reason for the different prefixes is the database can have different types than php, for example TP_INTEGER is always 64 bits, while CT_INTEGER32 means 32 bits
 */

//prefix TP_ (general php) --> prefix 'TP' as in TyPe
define('TP_UNDEFINED', 0);//use this as default parameter when no type is defined
define('TP_STRING', 100);//can't be 0 because of default parameters
define('TP_INTEGER', 1); // 64 bits integer
define('TP_BOOLEAN', 2); 
define('TP_BOOL', TP_BOOLEAN); //alias for boolean
define('TP_DOUBLE', 3);
define('TP_FLOAT', TP_DOUBLE); //alias for double
define('TP_BLOB', 4); //Binary Large OBject
define('TP_BINARY', 15); //just binary (not blob)
define('TP_DATETIME', 5);
define('TP_ARRAY', 6); 
define('TP_OBJECT', 7); 
define('TP_DECIMAL', 8); //decimal is more precize than float (float suffers from rounding errors because of bit representation)
define('TP_CURRENCY', 9); //for money --> basically same as TP_DECIMAL but visual representation is different: decimal 14,0 is currency 14,00
define('TP_HEX', 17); //hex value
define('TP_COLOR', 18); //hex value for colors


//prefix CP_ (db only) --> prefix: 'CT' as in Column Type
//defining the database column-types (php has no concept of a lot of data types like auto increment, enum, and the difference between 32 en 64 bits integer  )
define('CT_VARCHAR', 10);//max 265 characters
define('CT_INTEGER32', 11);
define('CT_INTEGER64', TP_INTEGER);
define('CT_LONGTEXT', TP_STRING); //varchar with unlimited amount of char
define('CT_FLOAT', 12);
define('CT_DOUBLE', TP_DOUBLE);
define('CT_BLOB', TP_BLOB);
define('CT_BINARY', TP_BINARY); //data type for binary data, which is not a blob
define('CT_ENUM', 13);
define('CT_BOOL', TP_BOOLEAN);
define('CT_DATETIME', TP_DATETIME);
define('CT_AUTOINCREMENT', 14); //for databases like MS Access this is a separate datatype
define('CT_DECIMAL', TP_DECIMAL); //decimal is more precize than float (float suffers from rounding errors because of bit representation)
define('CT_CURRENCY', TP_CURRENCY); //data type for money
define('CT_IPADDRESS', 16); //IPv4 or IPv6 ip-address: VARBINARY of 16 bytes (4=ipv4, 16=ipv6)
define('CT_HEX', TP_HEX); //hex value
define('CT_COLOR', TP_COLOR); //hex value for colors

//time related
define('MINUTE_IN_SECS',     60);
define('HOUR_IN_SECS',     3600);//60*60
define('DAY_IN_SECS',     86400);//60*60*24
define('WEEK_IN_SECS',   604800);//60*60*24*7
define('MONTH_IN_SECS', 2592000);//60*60*24*30 --> we assume 30 days
define('YEAR_IN_SECS', 31536000);//60*60*24*365

//define standard lengths
define('LENGTH_STRING_IPV6', 50);//As indicated a standard ipv6 address is at most 45 chars, but an ipv6 address can also include an ending % followed by a "scope" or "zone" string, which has no fixed length but is generally a small positive integer or a network interface name, so in reality it can be bigger than 45 characters. Network interface names are typically "eth0", "eth1", "wlan0", so choosing 50 as the limit is likely good enough.
define('LENGTH_STRING_IPV4', 15);//192.168.000.000
define('LENGTH_STRING_MD5', 32);//79054025255fb1a26e4bc422aef54eb4

//define chars uniqueid
define('UNIQUEID_CHARS', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_'); //these MUST be sql/url injection safe characters

//common used regular expressions -->predefined types for telephone number, zipcodes etc on http://search.cpan.org/dist/Regexp-Common/
define('REGEX_NUMERIC', '01234567890');
define('REGEX_NUMERIC_NEGATIVEFLOAT', REGEX_NUMERIC.'\.\-');
define('REGEX_HEXADECIMAL', '01234567890ABCDEFabcdef');
define('REGEX_ALPHABETICAL', 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('REGEX_DATES_NUMERIC', REGEX_NUMERIC.' \.\-\/');//internationale numeric dates have a dot (.), a dash (-), or a slash (/);
define('REGEX_ALPHANUMERIC', REGEX_ALPHABETICAL.REGEX_NUMERIC);
define('REGEX_ALPHANUMERIC_NEGATIVEFLOAT', REGEX_ALPHABETICAL.REGEX_NUMERIC_NEGATIVEFLOAT);
define('REGEX_ALPHANUMERIC_SPACE', REGEX_ALPHABETICAL.REGEX_NUMERIC.' ');
define('REGEX_ALPHANUMERIC_UNDERSCORE', REGEX_ALPHABETICAL.REGEX_NUMERIC.'_');
define('REGEX_ALPHANUMERIC_UNDERSCORE_MINUS', REGEX_ALPHANUMERIC_UNDERSCORE.'\-');
define('REGEX_LATIN', '\p{Latin}'); //expand please for more language support
define('REGEX_INTERPUNCTION', '¡!\?“”‘’‟\.,‚„\'"′″´˝^`:;&_­¦\|\/\-\+‒~\*\@# '.'\\\\');
define('REGEX_PARENTHESES', '<>\(\)\[\]\{\}');
define('REGEX_CURRENCYSYMBOLS', '\$€¥£');
define('REGEX_CONTROLCHARACTERS_HARMLESS', '\n\t');
define('REGEX_READINGSYMBOLS', REGEX_INTERPUNCTION . REGEX_PARENTHESES . REGEX_CURRENCYSYMBOLS);
define('REGEX_TEXT_NORMAL', REGEX_LATIN . REGEX_NUMERIC . REGEX_READINGSYMBOLS . REGEX_CONTROLCHARACTERS_HARMLESS);
define('REGEX_TEXT_SIMPLE', REGEX_ALPHANUMERIC_UNDERSCORE_MINUS . REGEX_CONTROLCHARACTERS_HARMLESS . '\.\?\! :;\/' . '\\\\');
define('WHITELIST_ISO8601', '0123456789-ZT:+W.');//whitelisted characters for ISO 8601 date
define('WHITELIST_NUMERIC', '0123456789');//whitelisted numbers
define('WHITELIST_ALPHABETICAL_LOWERCASE', 'abcdefghijklmnopqrstuvwxyz');
define('WHITELIST_ALPHABETICAL_UPPERCASE', 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
define('WHITELIST_ALPHABETICAL', WHITELIST_ALPHABETICAL_LOWERCASE.WHITELIST_ALPHABETICAL_UPPERCASE);//whitelisted alphabetical characters
define('WHITELIST_ALPHABETICAL_ACCENTS', 'éëèęėēúüûùūíïìióöôòáäâàẞ');//whitelisted variations of alphabetical characters used in normal languages
define('WHITELIST_ALPHANUMERIC', WHITELIST_NUMERIC.WHITELIST_ALPHABETICAL);//whitelisted alphabetical and numerical characters
define('WHITELIST_FILENAME', WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.'-_. ()');//local filesystem filename. This name does NOT have directory separator for windows or linux (thus safe for directory traversal)
define('WHITELIST_DIRECTORY', WHITELIST_FILENAME.DIRECTORY_SEPARATOR.':');//semicolon exists in c:\
define('WHITELIST_PATH', WHITELIST_DIRECTORY); //directory + file = path
define('WHITELIST_URL', WHITELIST_ALPHANUMERIC.'()+?_-=%&./'); //includes url directory separator
define('WHITELIST_URLSLUG', WHITELIST_ALPHANUMERIC.'()+?_-=%&.'); //doesn't include url directory separator (/)
define('WHITELIST_URLSLUG_NODOT', WHITELIST_ALPHANUMERIC.'()+?_-=%&'); //doesn't include url directory separator (/) and dot (.)
define('WHITELIST_EMAIL', WHITELIST_ALPHANUMERIC.'-_.@');
define('WHITELIST_HTML', WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.'., ~`!@#$%^&*()_+=-|[]{}"\':;<>?/');//whitelist for allowing html, but filters MultiByte injections (\)
define('WHITELIST_SAFE', WHITELIST_ALPHANUMERIC.WHITELIST_ALPHABETICAL_ACCENTS.'., ~!@#$%^*()_+=-[]|{}:?/');//DEFAULT!!! safe whitelist that doesn't allow special characters used in SQL, XSS, HTML & MultiByte injections


//filters for filter_varext() in lib_string
//define our custom (extended) filters
//we use numbers so the switch statement is faster
define('FILTEREXT_SANITIZE_CLASS', 		    9991);
define('FILTEREXT_SANITIZE_FUNCTION', 		9992);
define('FILTEREXT_SANITIZE_FILE', 		    9993);
define('FILTEREXT_SANITIZE_DIRECTORY', 		9994);
define('FILTEREXT_SANITIZE_URL_FILE', 		9995); //url that has to be ending with a filename
define('FILTEREXT_SANITIZE_URL_DIRECTORY', 	9996); //url general
define('FILTEREXT_SANITIZE_URL_STRICT', 	9997);//strict means: we dont accept funny business like $-_.+!*'(),{}|\\^~[]`<>#%";?@&=.


//url safe (!!!) comparison operators. prefixed because you can translate them. translation of LIKE-operator maybe 'contains', translating like in a normal translation would be 'i love it'
define('COMPARISON_OPERATOR_EQUAL_TO',                  'compare_equal_to'); 	// =
define('COMPARISON_OPERATOR_NOT_EQUAL_TO', 		        'compare_not_equal_to'); // !=
define('COMPARISON_OPERATOR_IS', 			            'compare_is'); 			// in sql: operator 'IS' is ONLY used for comparing with NULL or NOT NULL. i.e. 'is NULL' or 'is TRUE' --> http://www.sql.org/sql-database/postgresql/manual/functions-comparison.html
define('COMPARISON_OPERATOR_IS_VALUE_NULL', 		    'compare_is_value_null'); //
define('COMPARISON_OPERATOR_IS_VALUE_NOTNULL',          'compare_is_value_notnull'); // 
define('COMPARISON_OPERATOR_IN', 			            'compare_in'); 	//SELECT first_name, last_name, subject FROM student_details WHERE games IN ('Cricket', 'Football');
define('COMPARISON_OPERATOR_NOT_IN', 			        'compare_not_in'); 	//SELECT first_name, last_name, subject FROM student_details WHERE games NOT IN ('Cricket', 'Football');
define('COMPARISON_OPERATOR_LESS_THAN', 		        'compare_less_than');	// <
define('COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO',     'compare_less_than_or_equal_to');// <=
define('COMPARISON_OPERATOR_GREATER_THAN', 		        'compare_greater_than');	// >
define('COMPARISON_OPERATOR_GREATER_THAN_OR_EQUAL_TO',  'compare_greater_than_or_equal_to');// >=;
define('COMPARISON_OPERATOR_LIKE', 			            'compare_contains');// LIKE -- less picky than equal to (useful for searching in databases)
define('COMPARISON_OPERATOR_NOT_LIKE', 			        'compare_not_contains');// denial of LIKE
define('COMPARISON_OPERATOR_BETWEEN',                   'compare_between');// BETWEEN (2 values)


define('COMPARISON_OPERATOR_EQUAL_TO_TRANSLATIONDEFAULT',                   'equal to'); 	// =
define('COMPARISON_OPERATOR_NOT_EQUAL_TO_TRANSLATIONDEFAULT',               'not equal to'); // !=
define('COMPARISON_OPERATOR_IS_TRANSLATIONDEFAULT',                         'is'); 			// in sql: operator 'IS' is used for comparing with NULL or NOT NULL. i.e. 'is NULL' or 'is TRUE' --> http://www.sql.org/sql-database/postgresql/manual/functions-comparison.html
define('COMPARISON_OPERATOR_IS_VALUE_NULL_TRANSLATIONDEFAULT',              'is null'); //
define('COMPARISON_OPERATOR_IS_VALUE_NOTNULL_TRANSLATIONDEFAULT',           'is not null'); // 
define('COMPARISON_OPERATOR_IN_TRANSLATIONDEFAULT',                         'in'); 	//SELECT first_name, last_name, subject FROM student_details WHERE games IN ('Cricket', 'Football');
define('COMPARISON_OPERATOR_NOT_IN_TRANSLATIONDEFAULT',                     'not in'); 	//SELECT first_name, last_name, subject FROM student_details WHERE games NOT IN ('Cricket', 'Football');
define('COMPARISON_OPERATOR_LESS_THAN_TRANSLATIONDEFAULT',                  'less than');	// <
define('COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO_TRANSLATIONDEFAULT',      'less than or equal to');// <=
define('COMPARISON_OPERATOR_GREATER_THAN_TRANSLATIONDEFAULT',               'greater than');	// >
define('COMPARISON_OPERATOR_GREATER_THAN_OR_EQUAL_TO_TRANSLATIONDEFAULT',   'less than');// >=;
define('COMPARISON_OPERATOR_LIKE_TRANSLATIONDEFAULT',                       'contains');//  LIKE -- less picky than equal to (useful for searching in databases)
define('COMPARISON_OPERATOR_NOT_LIKE_TRANSLATIONDEFAULT',                   'doesn\'t contain');// denial of LIKE
define('COMPARISON_OPERATOR_BETWEEN_TRANSLATIONDEFAULT',                    'between');// BETWEEN (2 values)


//url safe (!!!) logical operators. prefixed because they can be uniquely translated 
define('LOGICAL_OPERATOR_AND', 				'logic_and');
define('LOGICAL_OPERATOR_OR', 				'logic_or');
define('LOGICAL_OPERATOR_XOR', 				'logic_exclusive_or'); //$a xor $b	Xor	TRUE if either $a or $b is TRUE, but not both.
define('LOGICAL_OPERATOR_NOT', 				'logic_not');

//url safe (!!!) sort orders
define('SORT_ORDER_ASCENDING', 	'ASC');
define('SORT_ORDER_DESCENDING', 'DESC');
define('SORT_ORDER_NONE', 	''); //no sort order 

//url safe (!!!) join types
define('JOIN_INNER', 	'join_inner');
define('JOIN_OUTER', 	'join_outer');
define('JOIN_LEFT', 	'join_left');
define('JOIN_RIGHT', 	'join_right');


//url safe (!!!)  standard values for bulk actions
define('BULKACTION_VARIABLE_CHECKBOX_RECORDID','chkRecordID'); //names of the checkboxes
define('BULKACTION_VARIABLE_SELECT_ACTION','selBulkAction'); //names of the select boxes
define('BULKACTION_VALUE_DELETE','delete');
define('BULKACTION_VALUE_DUPLICATE','duplicate');
define('BULKACTION_VALUE_CHECKOUT','checkout');
define('BULKACTION_VALUE_CHECKIN','checkin');
define('BULKACTION_VALUE_EXPORTCSV','exportcsv');
define('BULKACTION_VALUE_EXPORTHTML','exporthtml');
define('BULKACTION_VALUE_LOCK','lockrecord');
define('BULKACTION_VALUE_UNLOCK','unlockrecord');

//authorize constants (also for cms, although it would probably be better to move them to boostrap_cms_auth)
define('AUTH_RESOURCESEPARATOR','/'); //for example: books/authors/delete
define('SESSIONARRAYKEY_PERMISSIONS','sPeRePa'); //$_SESSION[SESSIONARRAYKEY_AUTH]: this is a weird value because of security reasons (stands for "permissions resource paths")

define('AUTH_OPERATION_DELETE', 'delete');
define('AUTH_OPERATION_DELETEOWN', 'delete own');
define('AUTH_OPERATION_CREATE', 'create');
define('AUTH_OPERATION_CHANGE', 'change');
define('AUTH_OPERATION_CHANGEOWN', 'change own');
define('AUTH_OPERATION_VIEW', 'view');
define('AUTH_OPERATION_VIEWOWN', 'viewown');
define('AUTH_OPERATION_EXECUTE', 'execute');
define('AUTH_OPERATION_EXECUTEOWN', 'execute own');
define('AUTH_OPERATION_CHECKINOUT', 'check-in check-out');//needs to be variable-safe (space is replaced by dash (-))
define('AUTH_OPERATION_LOCKUNLOCK', 'lock unlock');//needs to be variable-safe (space is replaced by dash (-))
define('AUTH_OPERATION_CHANGEPOSITION', 'change order');//needs to be variable-safe (space is replaced by dash (-))
define('AUTH_OPERATION_CHANGEPOSITIONOWN', 'change order own');//needs to be variable-safe (space is replaced by dash (-))
// define('AUTH_OPERATION_CHANGESETTINGS', 'change settings');//needs to be variable-safe (space is replaced by dash (-))

define('AUTH_MODULE_CMS', 'cms');// what represents the system in the authorise resources? (that part of the resource is called "module"). for example: cms/settings/view

define('AUTH_CATEGORY_SYSSETTINGS', 'system settings');// what represents the system in the authorise resources? (that part of the resource is called "module"). for example: cms/settings/view
// define('AUTH_OPERATION_SYSSETTINGS_VIEW', 'view system settings'); ==> replaced by AUTH_OPERATION_VIEW in lib_sys_cms.php
// define('AUTH_OPERATION_SYSSETTINGS_VIEWSYSTEM', 'view system');//view settings of user itself //needs to be variable-safe (space is replaced by dash (-))
// define('AUTH_OPERATION_SYSSETTINGS_VIEWUSER', 'view user');//view settings of user itself //needs to be variable-safe (space is replaced by dash (-))
// define('AUTH_OPERATION_SYSSETTINGS_CHANGESYSTEM', 'change system');//change settings system-wide //needs to be variable-safe (space is replaced by dash (-))
// define('AUTH_OPERATION_SYSSETTINGS_CHANGEUSER', 'change user');//change settings of the user itself //needs to be variable-safe (space is replaced by dash (-))

define('AUTH_CATEGORY_PAGEBUILDER', 'page builder');// what represents the system in the authorise resources? (that part of the resource is called "module"). for example: cms/settings/view
// define('AUTH_OPERATION_PAGEBUILDER_VIEW', 'view pagebuilder'); ==> replaced by AUTH_OPERATION_VIEW in lib_sys_cms.php

define('AUTH_CATEGORY_SYSSITES', 'websites-top-screen');// the "sites" listing on the left side of the screen
define('AUTH_OPERATION_SYSSITES_VISIBILITY', 'visible'); //view websites right side of screen
define('AUTH_OPERATION_SYSSITES_SWITCH', 'able to switch site'); //able to change sites

define('AUTH_CATEGORY_MODULEACCESS', '__MODULE__'); //the general access to a module is a separate permission //needs to be variable-safe (space is replaced by dash (-))
define('AUTH_OPERATION_MODULEACCESS', 'access'); //the general access to a module is a separate permission

define('AUTH_CATEGORY_UPLOADFILEMANAGER', 'upload file manager');
define('AUTH_OPERATION_UPLOADFILEMANAGER_ACCESS', 'access'); //the general access to uploadfilemanager
define('AUTH_OPERATION_UPLOADFILEMANAGER_UPLOADFILES', 'uploading files'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_GODIRUP', 'go directory up'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_GODIRDOWN', 'go directory down'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_CREATEDIR', 'create directory'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_DELDIR', 'delete directory'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_DELFILE', 'delete file'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_DELDIR_OWN', 'delete own directory'); 
define('AUTH_OPERATION_UPLOADFILEMANAGER_DELFILE_OWN', 'delete own file'); 




//settings
define('SETTINGS_RESOURCESEPARATOR','/'); //for example: books/authors/delete
define('SESSIONARRAYKEY_SETTINGS','sSeRePa'); //$_SESSION[SESSIONARRAYKEY_SETTINGS]: this is a weird value because of security reasons (stands for "resource paths")

define('SETTINGS_MODULE_CMS', 'cms');// what represents the system in the settings resources? (that part of the resource is called "module"). for example: cms/query_limit_default
define('SETTINGS_MODULE_SYSTEM', 'system');// what represents the system in the settings resources? (that part of the resource is called "module"). for example: cms/query_limit_default

define('SETTINGS_CMS_MEMBERSHIP_ANYONECANREGISTER', 'anyone_can_register');
define('SETTINGS_CMS_MEMBERSHIP_NEWUSER_ROLEID', 'new_user_roleid');
define('SETTINGS_CMS_MEMBERSHIP_USERPASSWORDEXPIRES_DAYS', 'user_password_expires_days');
define('SETTINGS_CMS_PAGINATOR_MAXRESULTSPERPAGE', 'paginator_maxresults_page');
define('SETTINGS_CMS_SYSTEMMAILBOT_FROM_EMAILADDRESS', 'systemmailbot_from_emailaddress');
define('SETTINGS_CMS_SYSTEMMAILBOT_FROM_NAME', 'systemmailbot_from_name');
define('SETTINGS_SYSTEM_EMAILSYSADMIN', 'email_sysadmin');

//url safe (!!!)  standard variablenames + values for other many used actions
define('ACTION_VARIABLE_ID', 'id'); //if you want to pass an id to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_UNIQUEID', 'uid'); //if you want to pass a id to another page that is not the regular id, use this constant, so it's the same on every page
define('ACTION_VARIABLE_PARENTID', 'pid'); //if you want to pass a parent id (i.e. you want to create a new translation of an existing record - existing record is parentid) to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_CANCEL', 'cancel'); //if you want to pass a cancel to another page, use this constant, so it's the same on every page
define('ACTION_VALUE_CANCEL', '1'); //if you want to pass a cancel to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_LANGUAGEID', 'lid'); //if you want to pass a languageid to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_DELETE', 'delete'); //if you want to pass a delete-action to another page, use this constant, so it's the same on every page
define('ACTION_VALUE_DELETE', '1'); //if you want to pass a cancel to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_SAVE', 'save'); //if you want to pass a save-action to another page, use this constant, so it's the same on every page
define('ACTION_VALUE_SAVE', '1'); //if you want to pass a cancel to another page, use this constant, so it's the same on every page
define('ACTION_VARIABLE_PREVIEW', 'preview'); //if you want to pass a delete-action to another page, use this constant, so it's the same on every page
define('ACTION_VALUE_PREVIEW', '1'); 
define('ACTION_VARIABLE_CHECKIN', 'checkin'); //if you want to pass a check-in-action to another page, use this constant, so it's the same on every page
define('ACTION_VALUE_CHECKIN', '1'); 
define('ACTION_VARIABLE_ANTICSRFTOKEN', 'act'); //anti CSRF-token (Cross Site Request Forgery)

define('ACTION_VARIABLE_ORDERONEUPDOWN', 'changeOrderOneUpDown'); //old method wich shifts a record 1 position up or down
define('ACTION_VALUE_ORDERONEUPDOWN', '1'); //enable change updown, old method

define('ACTION_VARIABLE_ORDERONEUP', 'changeUp'); 
define('ACTION_VALUE_ORDERONEUP', '1'); 
define('ACTION_VALUE_ORDERONEDOWN', '0'); 

define('ACTION_VARIABLE_CHANGEPOSITION', 'changePosition'); //enable change position, new method with drag and drop
define('ACTION_VALUE_CHANGEPOSITION', '1'); //enable change position, new method with drag and drop
define('ACTION_VARIABLE_CHANGEPOSITION_AFTERID', 'changePositionAfterID'); //the record id after which the current record should be inserted. if -1 it means: at the beginning
define('ACTION_VARIABLE_CHANGEPOSITION_ONID', 'changePositionOnID'); //for tree structures, you can drag an element onto another element to make it the child of that element


define('ACTION_VARIABLE_SORTORDER', 'sortorder'); 
define('ACTION_VARIABLE_SORTCOLUMNINDEX', 'sortorderindex'); //because passing fieldnames via url is dangerous, we use an index we can check against numeric to prevent sql injection

define('ACTION_VARIABLE_RETURNURL', 'returnurl'); //if you want to pass a delete-action to another page, use this constant, so it's the same on every page

define('ACTION_VARIABLE_RENDERVIEW', 'renderview'); //what kind of view to render? html, xml, json, svg
define('ACTION_VALUE_RENDERVIEW_HTMLPAGE', 'htmlpage'); 
define('ACTION_VALUE_RENDERVIEW_JSONDATA', 'jsondata'); //By standardizing JSON errorcodes and messages, the customErrorHandler() can output JSON!
define('ACTION_VALUE_RENDERVIEW_SVG', 'svg'); 

//default commonly used translation keys (those are KEYS, NOT TRANSLATIONS -> these keys you feed into the transc(), transg() or transm() functions)
define('TRANS_MODULENAME_MENU', 'menuitem_modulename'); //needs to be short
define('TRANS_MODULENAME_TITLE', 'modulename_title'); //can be longer
define('TRANS_DETAILSAVE_EDITRECORD_TITLE', 'detailsave_title_editrecord'); //super generic edit record
define('TRANS_DETAILSAVE_CREATERECORD_TITLE', 'detailsave_title_createrecord'); //super generic create record

define('TRANS_WEEKDAY_MONDAY_FULL_KEY',      'weekday_monday_full'); 
define('TRANS_WEEKDAY_MONDAY_FULL_VALUE',    'Monday'); //default value
define('TRANS_WEEKDAY_TUESDAY_FULL_KEY',     'weekday_tuesday_full'); 
define('TRANS_WEEKDAY_TUESDAY_FULL_VALUE',   'Tuesday'); //default value
define('TRANS_WEEKDAY_WEDNESDAY_FULL_KEY',   'weekday_wednesday_full'); 
define('TRANS_WEEKDAY_WEDNESDAY_FULL_VALUE', 'Wednesday'); //default value
define('TRANS_WEEKDAY_THURSDAY_FULL_KEY',    'weekday_thursday_full'); 
define('TRANS_WEEKDAY_THURSDAY_FULL_VALUE',  'Thursday'); //default value
define('TRANS_WEEKDAY_FRIDAY_FULL_KEY',      'weekday_friday_full'); 
define('TRANS_WEEKDAY_FRIDAY_FULL_VALUE',    'Friday'); //default value
define('TRANS_WEEKDAY_SATURDAY_FULL_KEY',    'weekday_saturday_full'); 
define('TRANS_WEEKDAY_SATURDAY_FULL_VALUE',  'Saturday'); //default value
define('TRANS_WEEKDAY_SUNDAY_FULL_KEY',      'weekday_sunday_full'); 
define('TRANS_WEEKDAY_SUNDAY_FULL_VALUE',    'Sunday'); //default value

define('TRANS_WEEKDAY_MONDAY_SHORT_KEY',      'weekday_monday_short'); 
define('TRANS_WEEKDAY_MONDAY_SHORT_VALUE',    'Mon'); //default value
define('TRANS_WEEKDAY_TUESDAY_SHORT_KEY',     'weekday_tuesday_short'); 
define('TRANS_WEEKDAY_TUESDAY_SHORT_VALUE',   'Tue'); //default value
define('TRANS_WEEKDAY_WEDNESDAY_SHORT_KEY',   'weekday_wednesday_short'); 
define('TRANS_WEEKDAY_WEDNESDAY_SHORT_VALUE', 'Wed'); //default value
define('TRANS_WEEKDAY_THURSDAY_SHORT_KEY',    'weekday_thursday_short'); 
define('TRANS_WEEKDAY_THURSDAY_SHORT_VALUE',  'Thu'); //default value
define('TRANS_WEEKDAY_FRIDAY_SHORT_KEY',      'weekday_friday_short'); 
define('TRANS_WEEKDAY_FRIDAY_SHORT_VALUE',    'Fri'); //default value
define('TRANS_WEEKDAY_SATURDAY_SHORT_KEY',    'weekday_saturday_short'); 
define('TRANS_WEEKDAY_SATURDAY_SHORT_VALUE',  'Sat'); //default value
define('TRANS_WEEKDAY_SUNDAY_SHORT_KEY',      'weekday_sunday_short'); 
define('TRANS_WEEKDAY_SUNDAY_SHORT_VALUE',    'Sun'); //default value

define('TRANS_MONTH_JANUARY_FULL_KEY',       'month_january_full'); 
define('TRANS_MONTH_JANUARY_FULL_VALUE',     'January'); //default value
define('TRANS_MONTH_FEBRUARY_FULL_KEY',      'month_february_full'); 
define('TRANS_MONTH_FEBRUARY_FULL_VALUE',    'February'); //default value
define('TRANS_MONTH_MARCH_FULL_KEY',         'month_march_full'); 
define('TRANS_MONTH_MARCH_FULL_VALUE',       'March'); //default value
define('TRANS_MONTH_APRIL_FULL_KEY',         'month_april_full'); 
define('TRANS_MONTH_APRIL_FULL_VALUE',       'April'); //default value
define('TRANS_MONTH_MAY_FULL_KEY',           'month_may_full'); 
define('TRANS_MONTH_MAY_FULL_VALUE',         'May'); //default value
define('TRANS_MONTH_JUNE_FULL_KEY',          'month_june_full'); 
define('TRANS_MONTH_JUNE_FULL_VALUE',        'June'); //default value
define('TRANS_MONTH_JULY_FULL_KEY',          'month_july_full'); 
define('TRANS_MONTH_JULY_FULL_VALUE',        'July'); //default value
define('TRANS_MONTH_AUGUST_FULL_KEY',        'month_august_full'); 
define('TRANS_MONTH_AUGUST_FULL_VALUE',      'August'); //default value
define('TRANS_MONTH_SEPTEMBER_FULL_KEY',     'month_september_full'); 
define('TRANS_MONTH_SEPTEMBER_FULL_VALUE',   'September'); //default value
define('TRANS_MONTH_OCTOBER_FULL_KEY',       'month_october_full'); 
define('TRANS_MONTH_OCTOBER_FULL_VALUE',     'October'); //default value
define('TRANS_MONTH_NOVEMBER_FULL_KEY',      'month_november_full'); 
define('TRANS_MONTH_NOVEMBER_FULL_VALUE',    'November'); //default value
define('TRANS_MONTH_DECEMBER_FULL_KEY',      'month_december_full'); 
define('TRANS_MONTH_DECEMBER_FULL_VALUE',    'December'); //default value

define('TRANS_MONTH_JANUARY_SHORT_KEY',       'month_january_short'); 
define('TRANS_MONTH_JANUARY_SHORT_VALUE',     'Jan'); //default value
define('TRANS_MONTH_FEBRUARY_SHORT_KEY',      'month_february_short'); 
define('TRANS_MONTH_FEBRUARY_SHORT_VALUE',    'Feb'); //default value
define('TRANS_MONTH_MARCH_SHORT_KEY',         'month_march_short'); 
define('TRANS_MONTH_MARCH_SHORT_VALUE',       'Mar'); //default value
define('TRANS_MONTH_APRIL_SHORT_KEY',         'month_april_short'); 
define('TRANS_MONTH_APRIL_SHORT_VALUE',       'Apr'); //default value
define('TRANS_MONTH_MAY_SHORT_KEY',           'month_may_short'); 
define('TRANS_MONTH_MAY_SHORT_VALUE',         'May'); //default value
define('TRANS_MONTH_JUNE_SHORT_KEY',          'month_june_short'); 
define('TRANS_MONTH_JUNE_SHORT_VALUE',        'Jun'); //default value
define('TRANS_MONTH_JULY_SHORT_KEY',          'month_july_short'); 
define('TRANS_MONTH_JULY_SHORT_VALUE',        'Jul'); //default value
define('TRANS_MONTH_AUGUST_SHORT_KEY',        'month_august_short'); 
define('TRANS_MONTH_AUGUST_SHORT_VALUE',      'Aug'); //default value
define('TRANS_MONTH_SEPTEMBER_SHORT_KEY',     'month_september_short'); 
define('TRANS_MONTH_SEPTEMBER_SHORT_VALUE',   'Sept'); //default value
define('TRANS_MONTH_OCTOBER_SHORT_KEY',       'month_october'); 
define('TRANS_MONTH_OCTOBER_SHORT_VALUE',     'Oct'); //default value
define('TRANS_MONTH_NOVEMBER_SHORT_KEY',      'month_november_short'); 
define('TRANS_MONTH_NOVEMBER_SHORT_VALUE',    'Nov'); //default value
define('TRANS_MONTH_DECEMBER_SHORT_KEY',      'month_december_short'); 
define('TRANS_MONTH_DECEMBER_SHORT_VALUE',    'Dec'); //default value

define('TRANS_AMPM_AM_KEY',                 'ampm_am');
define('TRANS_AMPM_AM_VALUE',               'AM'); //default value
define('TRANS_AMPM_PM_KEY',                 'ampm_pm');
define('TRANS_AMPM_PM_VALUE',               'PM'); //default value
define('TRANS_AMPM_PM_SHORTHAND_KEY',       'ampm_shorthand');
define('TRANS_AMPM_PM_SHORTHAND_VALUE',     'AM/PM'); //default value: placeholder in edit boxes. meaning either am or pm

define('TRANS_DAY_SHORTHAND_KEY',           'day_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_DAY_SHORTHAND_VALUE',         'd'); //default value
define('TRANS_MONTH_SHORTHAND_KEY',         'month_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_MONTH_SHORTHAND_VALUE',       'm'); //default value
define('TRANS_YEAR_SHORTHAND_KEY',          'year_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_YEAR_SHORTHAND_VALUE',        'y'); //default value
define('TRANS_HOUR_SHORTHAND_KEY',          'hour_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_HOUR_SHORTHAND_VALUE',        'h'); //default value
define('TRANS_MINUTE_SHORTHAND_KEY',        'minute_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_MINUTE_SHORTHAND_VALUE',      'm'); //default value
define('TRANS_SECOND_SHORTHAND_KEY',        'second_shorthand'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_SECOND_SHORTHAND_VALUE',      's'); //default value

define('TRANS_DAY_YESTERDAY_KEY_FULL',          'day_yesterday_full'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_DAY_YESTERDAY_VALUE_FULL',        'yesterday');
define('TRANS_DAY_TODAY_KEY_FULL',              'day_today_full'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_DAY_TODAY_VALUE_FULL',            'today');
define('TRANS_DAY_TOMORROW_KEY_FULL',           'day_tomorrow_full'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_DAY_TOMORROW_VALUE_FULL',         'tomorrow');
define('TRANS_TIME_NOW_KEY_FULL',               'time_now_full'); //used in date boxes to indicate date and time notation like dd-mm-yyyy hh:mm
define('TRANS_TIME_NOW_VALUE_FULL',             'now');


//Mime types
define('MIME_TYPE_OCTETSTREAM', 'application/octet-stream');
define('MIME_TYPE_MULTIPART_ALTERNATIVE', 'multipart/alternative');
define('MIME_TYPE_MULTIPART_MIXED', 'multipart/mixed');
define('MIME_TYPE_MULTIPART_RELATED', 'multipart/related');
define('MIME_TYPE_7Z','application/x-7z-compressed');
define('MIME_TYPE_AAC','audio/aac');
define('MIME_TYPE_AVI','video/x-msvideo');
define('MIME_TYPE_AVIF','image/avif');
define('MIME_TYPE_BMP','image/bmp');
define('MIME_TYPE_CSS','text/css');
define('MIME_TYPE_CSV','text/csv');
define('MIME_TYPE_DOC','application/msword');
define('MIME_TYPE_DOCX','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
define('MIME_TYPE_EPUB','application/epub+zip');
define('MIME_TYPE_GIF','image/gif');
define('MIME_TYPE_GZ','application/gzip');
define('MIME_TYPE_HTML','text/html');
define('MIME_TYPE_ICO','image/vnd.microsoft.icon');
define('MIME_TYPE_ICS','text/calendar');
define('MIME_TYPE_JPEG','image/jpeg');
define('MIME_TYPE_JS','text/javascript');
define('MIME_TYPE_JSON','application/json');
define('MIME_TYPE_MD','text/markdown');
define('MIME_TYPE_MID','audio/midi audio/x-midi');
define('MIME_TYPE_MP3','"audio/mpeg');
define('MIME_TYPE_MP4','video/mp4');
define('MIME_TYPE_MPG','video/mpeg');
define('MIME_TYPE_ODP','application/vnd.oasis.opendocument.presentation');
define('MIME_TYPE_ODS','application/vnd.oasis.opendocument.spreadsheet');
define('MIME_TYPE_ODT','application/vnd.oasis.opendocument.text');
define('MIME_TYPE_OTF','font/otf');
define('MIME_TYPE_PDF','application/pdf');
define('MIME_TYPE_PHP','application/x-httpd-php');
define('MIME_TYPE_PNG','image/png');
define('MIME_TYPE_PPT','application/vnd.ms-powerpoint');
define('MIME_TYPE_PPTX','application/vnd.openxmlformats-officedocument.presentationml.presentation');
define('MIME_TYPE_PSD','image/psd');
define('MIME_TYPE_RAR','application/vnd.rar');
define('MIME_TYPE_RTF','application/rtf');
define('MIME_TYPE_SVG','image/svg+xml');
define('MIME_TYPE_TAR','application/x-tar');
define('MIME_TYPE_TEXT','text/plain');
define('MIME_TYPE_TIFF','image/tiff');
define('MIME_TYPE_TTF','font/ttf');
define('MIME_TYPE_TXT','text/plain');
define('MIME_TYPE_VSD','application/vnd.visio');
define('MIME_TYPE_WAV','audio/wav');
define('MIME_TYPE_WEBP','image/webp');
define('MIME_TYPE_XHTML','application/xhtml+xml');
define('MIME_TYPE_XLS','application/vnd.ms-excel');
define('MIME_TYPE_XLSX','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
define('MIME_TYPE_XML','application/xml');
define('MIME_TYPE_ZIP','application/zip');
define('MIME_TYPES_IMAGES_IMAGICK', array(MIME_TYPE_AVIF, MIME_TYPE_JPEG, MIME_TYPE_GIF, MIME_TYPE_SVG, MIME_TYPE_PNG, MIME_TYPE_WEBP));
define('MIME_TYPES_IMAGES_GD', array(MIME_TYPE_AVIF, MIME_TYPE_JPEG, MIME_TYPE_GIF, MIME_TYPE_PNG, MIME_TYPE_WEBP, MIME_TYPE_BMP));

//Operating Systems
define('OS_WINDOWS', 'Windows');
define('OS_IPAD', 'iPad');
define('OS_IPOD', 'iPod');
define('OS_IPHONE', 'iPhone');
define('OS_MAC', 'Mac');
define('OS_ANDROID', 'Android');
define('OS_LINUX', 'Linux');
define('OS_NOKIA', 'Nokia');
define('OS_BLACKBERRY', 'BlackBerry');
define('OS_FREEBSD', 'FreeBSD');
define('OS_OPENBSD', 'OpenBSD');
define('OS_NETBSD', 'NetBSD');
define('OS_OPENSOLARIS', 'OpenSolaris');
define('OS_SUNOS', 'SunOS');
define('OS_OS2', 'OS\/2');
define('OS_BEOS', 'BeOS');

//Browsers
define('BROWSER_CHROME', 'Chrome');
define('BROWSER_FIREFOX', 'Firefox');
define('BROWSER_EDGE', 'Edge');
define('BROWSER_OPERA', 'Opera');
define('BROWSER_SAFARI', 'Safari');
define('BROWSER_LYNX', 'Lynx');
define('BROWSER_INTERNETEXPLORER', 'Lynx');
define('BROWSER_GOOGLEBOT', 'Google Bot');

//encryption methods
define('ENCRYPTION_CYPHERMETHOD_AES256CBC', 'aes-256-cbc'); //normal
define('ENCRYPTION_CYPHERMETHOD_AES128CBC', 'aes-128-cbc'); //less secure
define('ENCRYPTION_CYPHERMETHOD_DEFAULT', ENCRYPTION_CYPHERMETHOD_AES256CBC);
define('ENCRYPTION_DIGESTALGORITHM_MD5', 'MD5');
define('ENCRYPTION_DIGESTALGORITHM_SHA3512', 'sha3-512');
define('ENCRYPTION_DIGESTALGORITHM_SHA512', 'sha512');
define('ENCRYPTION_DIGESTALGORITHM_DEFAULT', ENCRYPTION_DIGESTALGORITHM_SHA512);

//misc
define('INSTALLED_POSTFIX', '____INSTALLED____'); //this is used for example for themes to mark directory names of the currently installed theme

//standard JSON constants (AK = ArrayKey)
define('JSONAK_RESPONSE_HEADER', 'Content-Type: application/json; charset=utf-8'); //html header for outputting JSON
define('JSONAK_RESPONSE_ERRORCODE', 'errorcode'); // By standardizing JSON errorcodes and messages, the customErrorHandler() can output JSON!
define('JSONAK_RESPONSE_HALTONERROR', 'haltonerror'); //abort current action when error occurred or not?
define('JSONAK_RESPONSE_MESSAGE', 'message'); //can be an error message, but also a normal message like 'ok'.  By standardizing JSON errorcodes and messages, the customErrorHandler() can output JSON!
define('JSONAK_RESPONSE_ERRORCOUNT', 'errorcount'); //the amount of errors produced
define('JSONAK_RESPONSE_ERRORS', 'errors'); //array with all the errors (these are specific errors, while message gives general message for all errors combined)
define('JSONAK_RESPONSE_RECORDID', 'recordid'); //id of a record
define('JSONAK_RESPONSE_OK', 0); //0 means: ok, anything other than 0 means: error. By standardizing JSON errorcodes and messages, the customErrorHandler() can output JSON!
define('JSONAK_RESPONSE_ERRORCODE_UNKNOWN', 999); //999 means: unknown error. By standardizing JSON errorcodes and messages, the customErrorHandler() can output JSON!

//google api
define('SESSIONARRAYKEY_GOOGLEAPI_TOKEN', 'googleapitoken'); //$_SESSION[SESSIONARRAYKEY_GOOGLEAPI_TOKEN]

//caching
define('SESSIONARRAYKEY_CACHE', 'cacheframework'); //$_SESSION[SESSIONARRAYKEY_CACHE]. the array key where to store session cache. I choose the name faily long and unique. 'cache' is so generic, it might interfere with other (3rd party) components

//php modules
define('PHP_EXT_GD', 'gd'); 
define('PHP_EXT_ZIP', 'zip'); 
define('PHP_EXT_MYSQLI', 'mysqli'); 
define('PHP_EXT_MBSTRING', 'mbstring'); 
define('PHP_EXT_JSON', 'json'); 
define('PHP_EXT_BCMATH', 'bcmath'); 
define('PHP_EXT_OPENSSL', 'openssl'); 
define('PHP_EXT_INTL', 'intl'); 
define('PHP_EXT_FILEINFO', 'fileinfo'); 
define('PHP_EXT_EXIF', 'exit'); 

?>
