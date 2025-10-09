<?php
/**
 * the allowed sessions of all the users that are logged in stored in the database
 * 
 * THESE SESSIONS HAVE NOTHING TO DO WITH $_SESSION[]!
 * WE MATCH THESE SESSIONS WITH VALUES STORED IN _SESSION or _COOKIE IN A 
 * LOGIN CONTROLLER TO SEE IF A USER HAS ACCESS
 * 
 * TSysUsersHistoryAbstract resembles TSysUserSessionsAbstract in many ways,
 * the big difference is that sessions are more 'fluid': they represent the current valid sessions,
 * history represents all sessions, including those in the past.
 * A USER CAN DELETE SESSSIONS, NOT HISTORY for security reasons.
 * This way we always have a comprehensive log of what has happened in the past
 * User sessions can be continued (=reused). The history makes a record on every new login.
 * 
 * TSysUsersSessionsAbstract: 11 sept 2021: getFingerprint() renamed: getFingerprintBrowser() to avoid confusion with emailaddressfingerprint
 */
namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

abstract class TSysUsersSessionsAbstract extends TSysModel
{
    const FIELD_IPADDRESS           = 'binIPAddress'; //ip address
    const FIELD_USERID              = 'iUserID'; //userid from TSysUsersAbstract    
    const FIELD_LOGINTOKEN          = 'sToken'; //logintoken is stored plain text (uncrypted) in database but will be stored ENcrypted in the session and cookie
    const FIELD_FINGERPRINTBROWSER  = 'sFPB'; //FingerPrint of the Users computer (browser)
    const FIELD_USERAGENT           = 'sUA'; //User Agent
    const FIELD_SESSIONSTARTED      = 'dtSessionStarted'; //when was the session started
    const FIELD_SESSIONUPDATED      = 'dtSessionUpdated'; //when was the session last updated
    const FIELD_OPERATINGSYSTEM     = 'sOperatingSystem';//os
    const FIELD_BROWSER             = 'sBrowser';//browser
    
    /**
     * get ip address
     * 
     * @return string
     */
    public function getIPAddress()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_IPADDRESS);
    }    
    
    /**
     * set ip address token 
     * 
     * @param string $sIP
     */
    public function setIPAddress($sIP)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_IPADDRESS, $sIP);            
    }
    
    /**
     * get user id
     * 
     * @return string
     */
    public function getUserID()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_USERID);
    }    
    
    /**
     * set user id
     * 
     * @param int $iID
     */
    public function setUserID($iID)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_USERID, $iID);            
    }    
     
    
    /**
     * set login token 
     * 
     * @param string $sToken
     */
    public function setLoginToken2($sToken)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, $sToken);            
    }
    
    /**
     * get login token
     * 
     * @return string
     */
    public function getLoginToken2()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN);
    }       

    
     /**
     * get login token but with hash seed added
     * 
     * @return string
     */
    public function getLoginToken2WithHashSeed()
    {
        return $this->getHashSeed().$this->get(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN).$this->getHashSeed();
    }   

    /**
     * set fingerprint of browser/user
     * 
     * @param string $sMiddleFinger
     */
    public function setFingerprintBrowser($sMiddleFinger)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER, $sMiddleFinger);            
    }
    
    /**
     * get fingerprint of browser/user
     * 
     * @return string
     */
    public function getFingerprintBrowser()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER);
    }  

    /**
     * set user agent of browser/user
     * 
     * @param string $sAgent
     */
    public function setUserAgent($sAgent)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_USERAGENT, $sAgent);            
    }
    
    /**
     * get user agent of browser/user
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_USERAGENT);
    }      
    
    /**
     * set session start date
     * 
     * @param TDateTime $objDate
     */
    public function setSessionStarted($objDate)
    {
        $this->setTDateTime(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, $objDate);            
    }
    
    /**
     * get session start date
     * 
     * @return TDateTime
     */
    public function getSessionStarted()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED);
    }       
    
    /**
     * set session update date
     * 
     * @param TDateTime $objDate
     */
    public function setSessionUpdated($objDate)
    {
        $this->setTDateTime(TSysUsersSessionsAbstract::FIELD_SESSIONUPDATED, $objDate);            
    }
    
    /**
     * get session update date
     * 
     * @return TDateTime
     */
    public function getSessionUpdated()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_SESSIONUPDATED);
    }     
    
    /**
     * get operating system
     * (windows, mac, linux)
     * 
     * 
     * @return string
     */
    public function getOperatingSystem()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM);
    }    
    
    /**
     * set operating system 
     * 
     * @param string $sOS windows, mac, linux
     */
    public function setOperatingSystem($sOSP)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, $sOSP);            
    }


    /**
     * get browser
     * (chrome, firefox, opera)
     * 
     * 
     * @return string
     */
    public function getBrowser()
    {
        return $this->get(TSysUsersSessionsAbstract::FIELD_BROWSER);
    }    
    
    /**
     * set operating system 
     * 
     * @param string $sBrowser windows, mac, linux
     */
    public function setBrowser($sBrowser)
    {
        $this->set(TSysUsersSessionsAbstract::FIELD_BROWSER, $sBrowser);            
    }    




    /**
     * assign new values to the tokens
     * 
     * 
     * @param $bOnlyReplaceExisting leave the tokens empty when they were already empty
     */
    public function generateTokens()
    {
        $this->setLoginToken2(generatePassword(10,255));                  
    }
    
    /**
     * because the field in the session/cookie is an exact copy of the database, 
     * (and that is pretty easy to figure out)
     * we add a seed to the hash function
     * 
     * this function returns that seed
     */
    public function getHashSeed()
    {
        return '30fj-03jhdlw_9j3';
    }
    
    /**
     * this function loads user with 3 login tokens
     * and determines if the current loaded user 
     * is allowed to log in based on the $sUsername and $sPassword.
     * it takes into account the password, login expired and enabled
     * (password expiration is not taken into account)
     * 
     * this method is safe for sql injections
     * 
     * returns false if $sLoginToken1, $sLoginToken2 or $sLoginToken3 is empty
     * 
     * TRUE = verified and allowed to login, record loaded into memory
     * FALSE = not allowed to log , RECORD NOT LOADED into memory
     * 
     * @param int $iLoginTokenRandomID randomid requested from database
     * @param string $sFingerprint needs to be an exact match with database
     * @param string $sLoginTokenEncrypted is in plaintext in db and encrypted in session or cookie
     * @return boolean allowed
     */
    public function loadFromDBByTokensLoginAllowed(TSysUsersAbstract &$objUsers, TSysLanguages &$objLanguages,$iLoginTokenRandomID, $sFingerprint, $sLoginTokenEncrypted)
    {
//logDev('loadFromDBByTokensLoginAllowed('.$iLoginTokenRandomID.'    ,    '.$sFingerprint.'    ,    '.$sLoginTokenEncrypted)        ;
        if (!is_numeric($iLoginTokenRandomID))
            return false;


        if ($sLoginTokenEncrypted == '')
            return false;
                
 
        

        $this->clear();
        $this->findRandomID($iLoginTokenRandomID);
        if ($this->loadFromDB(1)) //changed 15-11-2022         we need to load in the entire user (not possible because of duplicate fields) because we need to be able to save it later when we update fields like last login
        {            
            $objUsers->setInternalDataJoinedTable($this);
            $objLanguages->setInternalDataJoinedTable($this);
            
            if ($this->count() > 0)
            {
                //logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'nu this->count() > 0');    

                if (($this->getFingerprintBrowser() == $sFingerprint) || (APP_DEBUGMODE == true)) //fingerprint (ignores fingerprint when developing)
                {
                    // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'if ($this->getFingerprintBrowser() == $sFingerprint) == true')  ;
                    
                    if (password_verify($this->getLoginToken2WithHashSeed(), $sLoginTokenEncrypted)) //only token 2 is checked
                    {
                        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'password_verify($this->getLoginToken2WithHashSeed(), $sLoginToken2Encrypted) == true')  ;
                        // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $this->getLoginToken2WithHashSeed().'=='.$sLoginTokenEncrypted)  ;
                        
                        if ($objUsers->get(TSysUsersAbstract::FIELD_LOGINENABLED)) //--> we are in session, bu TSysUsersAbstract works because of the inner join fields
                        {
                            // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->get(TSysUsersAbstract::FIELD_LOGINENABLED) == true')  ;
    
                            if ($objUsers->get(TSysUsersAbstract::FIELD_LOGINEXPIRES)->isInTheFuture() || $objUsers->get(TSysUsersAbstract::FIELD_LOGINEXPIRES)->isZero()) //--> we are in session, bu TSysUsersAbstract works because of the inner join fields
                            {             
                                // logDebug(__CLASS__.': '.__FUNCTION__.': '.__LINE__, '$this->get(TSysUsersAbstract::FIELD_LOGINEXPIRES)->isInTheFuture() || $this->get(TSysUsersAbstract::FIELD_LOGINEXPIRES)->isZero() == true')  ;
                                return true;
                            }
                        }
                    }
                    else
                        logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'login token auth failed: '. $this->getFingerprintBrowser(). ' != '.$sFingerprint);
                }
                else
                    logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'fingerprint auth failed: db:'. $this->getFingerprintBrowser(). ' != live-from-browser: '.$sFingerprint);

            }    
            
        }
        $this->clear();
        return false;
    }        
    
    /**
	 * delete logs older than 6 months from database
	 * (you can set the number of days as parameter)
	 *
	 * @param int $iDaysOld number of days that the logs be old before they get deleted, default is 60 (that is 2 months)
	 * @return boolean true = success, false is error
     */
    public function deleteOldSessionsFromDB($iDaysOld = 60)
    {
        $bResult = false;
        $objCopy = $this->getCopy();
        $objTime = new TDateTime(time());
        $objTime->subtractDays($iDaysOld);
        $objCopy->newQuery();
        $objCopy->find(TSysUsersSessionsAbstract::FIELD_SESSIONUPDATED, $objTime, COMPARISON_OPERATOR_LESS_THAN);
        $bResult = $objCopy->deleteFromDB(true);

        unset($objCopy);
        unset($objTime);
        return $bResult;
    }

    
    /**
     * This function is called in the constructor and the clear() function
     * this is used to define default values for fields
     * 
     * initialize values
     */
    public function initRecord()
    {}
	
	
	
    /**
     * defines the fields in the tables
     * i.e. types, default values, enum values, referenced tables etc
    */
    public function defineTable()
    {
        //Ip address
        $this->setFieldDefaultValue(TSysUsersSessionsAbstract::FIELD_IPADDRESS, '');
        $this->setFieldType(TSysUsersSessionsAbstract::FIELD_IPADDRESS, CT_IPADDRESS);
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_IPADDRESS, 0);
        $this->setFieldDecimalPrecision(TSysUsersSessionsAbstract::FIELD_IPADDRESS, 0);
        $this->setFieldPrimaryKey(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldNullable(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldEnumValues(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldUnique(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_IPADDRESS, true);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldForeignKeyClass(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyTable(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyField(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyJoin(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersSessionsAbstract::FIELD_IPADDRESS, null);
        $this->setFieldAutoIncrement(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldUnsigned(TSysUsersSessionsAbstract::FIELD_IPADDRESS, false);
        $this->setFieldEncryptionDisabled(TSysUsersSessionsAbstract::FIELD_IPADDRESS);

        //userid
        $objUsers = $this->getNewUsersModel();		
        $this->setFieldDefaultValue(TSysUsersSessionsAbstract::FIELD_USERID, '');
        $this->setFieldType(TSysUsersSessionsAbstract::FIELD_USERID, CT_INTEGER64);
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_USERID, 0);
        $this->setFieldDecimalPrecision(TSysUsersSessionsAbstract::FIELD_USERID, 0);
        $this->setFieldPrimaryKey(TSysUsersSessionsAbstract::FIELD_USERID, false);
        $this->setFieldNullable(TSysUsersSessionsAbstract::FIELD_USERID, false);
        $this->setFieldEnumValues(TSysUsersSessionsAbstract::FIELD_USERID, null);
        $this->setFieldUnique(TSysUsersSessionsAbstract::FIELD_USERID, false);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_USERID, true);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_USERID, false);
        $this->setFieldForeignKeyClass(TSysUsersSessionsAbstract::FIELD_USERID, get_class($objUsers));
        $this->setFieldForeignKeyTable(TSysUsersSessionsAbstract::FIELD_USERID, $objUsers::getTable());
        $this->setFieldForeignKeyField(TSysUsersSessionsAbstract::FIELD_USERID, TSysModel::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysUsersSessionsAbstract::FIELD_USERID);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersSessionsAbstract::FIELD_USERID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersSessionsAbstract::FIELD_USERID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldAutoIncrement(TSysUsersSessionsAbstract::FIELD_USERID, false);
        $this->setFieldUnsigned(TSysUsersSessionsAbstract::FIELD_USERID, true);
        $this->setFieldEncryptionDisabled(TSysUsersSessionsAbstract::FIELD_USERID);        
        unset($objUsers);      
        
        //logintoken
        $this->setFieldDefaultValue(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, '');
        $this->setFieldType(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, CT_VARCHAR);
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, 255);
        $this->setFieldDecimalPrecision(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, 0);
        $this->setFieldPrimaryKey(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldNullable(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldEnumValues(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldUnique(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, true);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldForeignKeyClass(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldForeignKeyTable(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldForeignKeyField(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldForeignKeyJoin(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, null);
        $this->setFieldAutoIncrement(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldUnsigned(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN, false);
        $this->setFieldEncryptionDisabled(TSysUsersSessionsAbstract::FIELD_LOGINTOKEN);
        
        //fingerprint
        $this->setFieldCopyProps(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER, TSysUsersSessionsAbstract::FIELD_LOGINTOKEN);       
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER, LENGTH_STRING_MD5);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER, true);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER, false);

        //useragent
        $this->setFieldCopyProps(TSysUsersSessionsAbstract::FIELD_USERAGENT, TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER);       
		$this->setFieldLength(TSysUsersSessionsAbstract::FIELD_USERAGENT, 255);
		$this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_USERAGENT, false);
		$this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_USERAGENT, false);

        //start session
        $this->setFieldDefaultValue(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldType(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, CT_DATETIME);
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, 0);
        $this->setFieldDecimalPrecision(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, 0);
        $this->setFieldPrimaryKey(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);
        $this->setFieldNullable(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, true);
        $this->setFieldEnumValues(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldUnique(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);
        $this->setFieldForeignKeyClass(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldForeignKeyTable(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldForeignKeyField(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldForeignKeyJoin(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, null);
        $this->setFieldAutoIncrement(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);
        $this->setFieldUnsigned(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, false);   
        $this->setFieldEncryptionDisabled(TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED);   
        
        //session updates
        $this->setFieldCopyProps(TSysUsersSessionsAbstract::FIELD_SESSIONUPDATED, TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED);         

        //operating system
        $this->setFieldDefaultValue(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, '');
        $this->setFieldType(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, CT_VARCHAR);
        $this->setFieldLength(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, 50);
        $this->setFieldDecimalPrecision(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, 0);
        $this->setFieldPrimaryKey(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);
        $this->setFieldNullable(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, true);
        $this->setFieldEnumValues(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldUnique(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);
        $this->setFieldIndexed(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);
        $this->setFieldFulltext(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);
        $this->setFieldForeignKeyClass(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldForeignKeyTable(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldForeignKeyField(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldForeignKeyJoin(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, null);
        $this->setFieldAutoIncrement(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);
        $this->setFieldUnsigned(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM, false);   
        $this->setFieldEncryptionDisabled(TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM);           
        
        //browser
        $this->setFieldCopyProps(TSysUsersSessionsAbstract::FIELD_BROWSER, TSysUsersSessionsAbstract::FIELD_OPERATINGSYSTEM);         
        
    }
	
	
    /**
     * returns an array with fields that are publicly viewable
     * sometimes (for security reasons the password-field for example) you dont want to display all table fields to the user
     *
     * i.e. it can be used for searchqueries, sorting, filters or exports
     *
     * @return array function returns array WITHOUT tablename
    */
    public function getFieldsPublic()
    {
            return array(TSysUsersSessionsAbstract::FIELD_IPADDRESS, TSysUsersSessionsAbstract::FIELD_SESSIONSTARTED, TSysUsersSessionsAbstract::FIELD_SESSIONUPDATED);
    }

    /**
     * use the auto-added id-field ?
     * @return bool
    */
    public function getTableUseIDField()
    {
            return false;	
    }


    /**
     * use the auto-added date-changed & date-created field ?
     * @return bool
    */
    public function getTableUseDateCreatedChangedField()
    {
            return false;
    }


    /**
     * use the checksum field ?
     * @return bool
    */
    public function getTableUseChecksumField()
    {
            return true;
    }

    /**
     * order field to switch order between records
    */
    public function getTableUseOrderField()
    {
            return false;
    }

    /**
     * use checkout for locking record for editing
    */
    public function getTableUseCheckout()
    {
            return false;
    }

    /**
     * use record locking to prevent record editing
    */
    public function getTableUseLock()
    {
            return false;
    }           
    
    /**
     * use image in your record?
     * if you don't want a small and large version, use this one
    */
    public function getTableUseImageFile()
    {
            return false;
    }


    /**
     * opvragen of records fysiek uit de databasetabel verwijderd moeten worden
     *
     * returnwaarde interpretatie:
     * true = fysiek verwijderen uit tabel
     * false = record-hidden-veld gebruiken om bij te houden of je het record kan zien in overzichten
     *
     * @return bool moeten records fysiek verwijderd worden ?
    */
    public function getTablePhysicalDeleteRecord()
    {
            return true;
    }




    /**
     * type of primary key field
     *
     * @return integer with constant CT_AUTOINCREMENT or CT_INTEGER32 or something else that is not recommendable
    */
    public function getTableIDFieldType()
    {
            return CT_AUTOINCREMENT;
    }




    /**
     * OVERSCHRIJF DOOR CHILD KLASSE ALS NODIG
     *
     * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
     * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
     *
     *
     * return '??? - functie niet overschreven door child klasse';
    */
    public function getDisplayRecordShort()
    {
            return $this->get(TSysUsersSessionsAbstract::FIELD_IPADDRESS);
    }


    /**
     * erf deze functie over om je eigen checksum te maken voor je tabel.
     * je berekent deze de belangrijkste velden te pakken, wat strings toe te
     * voegen en alles vervolgens de coderen met een hash algoritme
     * zoals met sha1 (geen md5, gezien deze makkelijk te breken is)
     * de checksum mag maar maximaal 50 karakters lang zijn
     *
     * BELANGRIJK: je mag NOOIT het getID() en getChecksum()-field meenemen in
     * je checksum berekening (id wordt pas toegekend na de save in de database,
     * dus is nog niet bekend ten tijde van het checksum berekenen)
     *
     * @return string
    */
    public function getChecksumUncrypted()
    {          
            return 'sessiondomg'.$this->get(TSysUsersSessionsAbstract::FIELD_FINGERPRINTBROWSER).'ne'.$this->get(TSysUsersSessionsAbstract::FIELD_USERID).''.$this->get(TSysUsersSessionsAbstract::FIELD_RANDOMID).'jaja'.$this->get(TSysUsersSessionsAbstract::FIELD_IPADDRESS).'kaka';            
    }


    /**
     * DEZE FUNCTIE MOET OVERGEERFD WORDEN DOOR DE CHILD KLASSE
     *
     * checken of alle benodigde waardes om op te slaan wel aanwezig zijn
     *
     * @return bool true=ok, false=not ok
    */
    public function areValuesValid()
    {
            return true;
    }

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
    public function getVersion()
    {
            return 0;
    }

    /**
     * update the table in the database
     * (may have been changes to fieldnames, fields added or removed etc)
     *
     * @param int $iFromVersion upgrade vanaf welke versie ?
     * @return bool is alles goed gegaan ? true = ok (of er is geen upgrade gedaan)
    */
    protected function refactorDBTable($iFromVersion)
    {
            return true;
    }	


    /**
     * use a second id that has no follow-up numbers?
     */
    public function getTableUseRandomID()
    {
            return true;
    }
            
        
    /**
     * is randomid field a primary key?
     */        
    public function getTableUseRandomIDAsPrimaryKey()
    {
            return true;
    }   
         
	/**
	 * use a third character-based id that has no logically follow-up numbers?
	 * 
	 * a tertiary unique key (uniqueid) can be useful for security reasons like login sessions: you don't want to _POST the follow up numbers in url
	 */
	public function getTableUseUniqueID()
	{
		return false;
	}    
    
	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	public function getTableUseNiceID()
	{
		return false;
	}	
        
    /**
     * is this model a translation model?
     *
     * @return bool is this model a translation model?
     */
    public function getTableUseTranslationLanguageID()
    {
        return false;
    }    
    
	/**
	 * Want to use the 'isdefault' field in database table?
	 * Returning true allows 1 record to be the default record in a table
	 * This is useful for creating records with foreign fields without user interference OR 
	 * selecting records in GUI elements like comboboxes
	 * 
	 * example: select the default language in a combobox
	 * 
	 * @return bool
	 */
	public function getTableUseIsDefault()
	{
		return false;
	}

	/**
	 * can a record be favorited by the user?
	 *
	 * @return bool
	 */
	public function getTableUseIsFavorite()
	{
		return false;
	}    

	/**
	 * can record be transcanned?
	 * Trashcan is an extra step in for deleting a record
	 *
	 * @return bool
	 */
	public function getTableUseTrashcan()
	{
		return false;
	}	

	/**
	 * use a field for search keywords?
	 * (also known als tags or labels)
	 *
	 * @return bool
	 */
	public function getTableUseSearchKeywords()
	{
		return false;
	}	

    /****************************************************************************
     *              ABSTRACT METHODS
    ****************************************************************************/
    
    /**
     * for the function defineTable() we need a TSysUsersAbstract instantiated
     * object to define the database tables
     * 
     * @return TSysUsersAbstract user object
     */
    abstract protected function getNewUsersModel();
            
}

?>