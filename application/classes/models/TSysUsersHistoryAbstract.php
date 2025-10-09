<?php
/**
 * User login history
 * 
 * TSysUsersHistoryAbstract resembles TSysUserSessionsAbstract in many ways,
 * the big difference is that sessions are more 'fluid': they represent the current valid sessions,
 * history represents all sessions, including those in the past.
 * A USER CAN DELETE SESSSIONS, NOT HISTORY for security reasons.
 * This way we always have a comprehensive log of what has happened in the past.
 * User sessions can be continued (=reused). The history makes a record on every new login.
 * 
 * The history can be used for 2-factor authentication (2FA) if this device is used before to log in,
 * if not used before, then ask for 2FA
 */
namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;



abstract class TSysUsersHistoryAbstract extends TSysModel
{
    const FIELD_IPADDRESS           = 'binIPAddress'; //ip address
    const FIELD_USERID              = 'iUserID'; //userid from TSysUsersAbstract    
    const FIELD_FINGERPRINTBROWSER  = 'sFPB'; //FingerPrint of the Users computer (browser)
    const FIELD_USERAGENT           = 'sUA'; //User Agent
	const FIELD_USERNAMEENCRYPTED   = 'sUNE'; //we store submitted usernames because users can change usernames which show up in the floodlogs under different usernames, while it's the same user
	const FIELD_NOTES               = 'sNotes'; //what happened to justify this history entry? Can be logging in or changing username
    const FIELD_ISLOGIN             = 'bIsLogin';//ability to distinguish succeeded login attempts from the rest

	const ENCRYPTION_USERNAME_PASSPHRASE 		= 'shallo_blathoe673-blerk-blaat1$%^&'; //passphrase for the encryption algo

    /**
     * get ip address
     * 
     * @return string
     */
    public function getIPAddress()
    {
        return $this->get(TSysUsersHistoryAbstract::FIELD_IPADDRESS);
    }    
    
    /**
     * set ip address token 
     * 
     * @param string $sIP
     */
    public function setIPAddress($sIP)
    {
        $this->set(TSysUsersHistoryAbstract::FIELD_IPADDRESS, $sIP);            
    }
    
    /**
     * get user id
     * 
     * @return string
     */
    public function getUserID()
    {
        return $this->get(TSysUsersHistoryAbstract::FIELD_USERID);
    }    
    
    /**
     * set user id
     * 
     * @param int $iID
     */
    public function setUserID($iID)
    {
        $this->set(TSysUsersHistoryAbstract::FIELD_USERID, $iID);            
    }    
     

    /**
     * set fingerprint of browser/user
     * 
     * @param string $sMiddleFinger
     */
    public function setFingerprintBrowser($sMiddleFinger)
    {
        $this->set(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, $sMiddleFinger);            
    }
    
    /**
     * get fingerprint of browser/user
     * 
     * @return string
     */
    public function getFingerprintBrowser()
    {
        return $this->get(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER);
    }  

    /**
     * set user agent of browser/user
     * 
     * @param string $sAgent
     */
    public function setUserAgent($sAgent)
    {
        $this->set(TSysUsersHistoryAbstract::FIELD_USERAGENT, $sAgent);            
    }
    
    /**
     * get user agent of browser/user
     * 
     * @return string
     */
    public function getUserAgent()
    {
        return $this->get(TSysUsersHistoryAbstract::FIELD_USERAGENT);
    }      
    


	/**
	 * get username
	 * @return string 
	 */
	public function getUsername()
	{
		return $this->get(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, '', true);
	}
	
	/**
	 * 
	 * @param string $sUser
	 */
	public function setUsername($sUser)
	{
		$this->set(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, $sUser, '', true);
	}    


	/**
	 * get is succeeded login attempt
	 * @return string 
	 */
	public function getNotes()
	{
		return $this->get(TSysUsersHistoryAbstract::FIELD_NOTES);
	}
	
	/**
	 * 
	 * @param string $sNotes
	 */
	public function setNotes($sNotes)
	{
		$this->set(TSysUsersHistoryAbstract::FIELD_NOTES, $sNotes);
	}       

	/**
	 * get is succeeded login attempt
	 * @return string 
	 */
	public function getIsLogin()
	{
		return $this->get(TSysUsersHistoryAbstract::FIELD_ISLOGIN);
	}
	
	/**
	 * 
	 * @param bool $bLogin
	 */
	public function setIsLogin($bLogin)
	{
		$this->set(TSysUsersHistoryAbstract::FIELD_ISLOGIN, $bLogin);
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
        return '30fj-er34034fmsdf34r-34r343';
    }
     
    
    /**
	 * delete logs older than X days from database
	 * (you can set the number of days as parameter)
     * 
     * ONLY LOGINS ARE REMOVED, NOT THE REST
	 *
	 * @param int $iDaysOld number of days that the logs be old before they get deleted, default is 1095 (that is 3 years)
	 * @return boolean true = success, false is error
     */
    public function deleteOldHistoryFromDB($iDaysOld = 1095)
    {
        $bResult = false;
        $objCopy = $this->getCopy();
        $objTime = new TDateTime(time());
        $objTime->subtractDays($iDaysOld);
        $objCopy->newQuery();
        $objCopy->find(TSysUsersHistoryAbstract::FIELD_RECORDCREATED, $objTime, COMPARISON_OPERATOR_LESS_THAN);
        $objCopy->find(TSysUsersHistoryAbstract::FIELD_ISLOGIN, true);
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
        $this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_IPADDRESS, '');
        $this->setFieldType(TSysUsersHistoryAbstract::FIELD_IPADDRESS, CT_IPADDRESS);
        $this->setFieldLength(TSysUsersHistoryAbstract::FIELD_IPADDRESS, 0);
        $this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_IPADDRESS, 0);
        $this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_IPADDRESS, true);
        $this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_IPADDRESS, null);
        $this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_IPADDRESS, false);
        $this->setFieldEncryptionDisabled(TSysUsersHistoryAbstract::FIELD_IPADDRESS);

        //userid
        $objUsers = $this->getNewUsersModel();		
        $this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_USERID, '');
        $this->setFieldType(TSysUsersHistoryAbstract::FIELD_USERID, CT_INTEGER64);
        $this->setFieldLength(TSysUsersHistoryAbstract::FIELD_USERID, 0);
        $this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_USERID, 0);
        $this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_USERID, false);
        $this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_USERID, false);
        $this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_USERID, null);
        $this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_USERID, false);
        $this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_USERID, true);
        $this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_USERID, false);
        $this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_USERID, get_class($objUsers));
        $this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_USERID, $objUsers::getTable());
        $this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_USERID, TSysModel::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_USERID);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_USERID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_USERID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_USERID, false);
        $this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_USERID, true);
        $this->setFieldEncryptionDisabled(TSysUsersHistoryAbstract::FIELD_USERID);        
        unset($objUsers);      
        
		//fingerprint browser
		$this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, '');
		$this->setFieldType(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, CT_VARCHAR);
		$this->setFieldLength(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, LENGTH_STRING_MD5);
		$this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, 0);
		$this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, true);//is possible to be empty
		$this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldEncryptionDisabled(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER);				

        //useragent
        $this->setFieldCopyProps(TSysUsersHistoryAbstract::FIELD_USERAGENT, TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER);       
		$this->setFieldLength(TSysUsersHistoryAbstract::FIELD_USERAGENT, 255);
		$this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_USERAGENT, false);
		$this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_USERAGENT, false);
        
		//username encrypted
		$this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, '');
		$this->setFieldType(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, CT_LONGTEXT);
		$this->setFieldLength(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, 0);
		$this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, 0);
		$this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, true);
		$this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldEncryptionCypher(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysUsersHistoryAbstract::FIELD_USERNAMEENCRYPTED, TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED);			                          

        //notes
        $this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_NOTES, '');
        $this->setFieldType(TSysUsersHistoryAbstract::FIELD_NOTES, CT_VARCHAR);
        $this->setFieldLength(TSysUsersHistoryAbstract::FIELD_NOTES, 255);
        $this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_NOTES, 0);
        $this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_NOTES, false);
        $this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_NOTES, true);
        $this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_NOTES, false);
        $this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_NOTES, false);
        $this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_NOTES, true);
        $this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_NOTES, null);
        $this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_NOTES, false);
        $this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_NOTES, false);   
        $this->setFieldEncryptionDisabled(TSysUsersHistoryAbstract::FIELD_NOTES);           

       //is successful login attempt
       $this->setFieldDefaultValue(TSysUsersHistoryAbstract::FIELD_ISLOGIN, '');
       $this->setFieldType(TSysUsersHistoryAbstract::FIELD_ISLOGIN, CT_BOOL);
       $this->setFieldLength(TSysUsersHistoryAbstract::FIELD_ISLOGIN, 0);
       $this->setFieldDecimalPrecision(TSysUsersHistoryAbstract::FIELD_ISLOGIN, 0);
       $this->setFieldPrimaryKey(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldNullable(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldEnumValues(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldUnique(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldIndexed(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldFulltext(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldForeignKeyClass(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldForeignKeyTable(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldForeignKeyField(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldForeignKeyJoin(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldForeignKeyActionOnUpdate(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldForeignKeyActionOnDelete(TSysUsersHistoryAbstract::FIELD_ISLOGIN, null);
       $this->setFieldAutoIncrement(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);
       $this->setFieldUnsigned(TSysUsersHistoryAbstract::FIELD_ISLOGIN, false);   
       $this->setFieldEncryptionDisabled(TSysUsersHistoryAbstract::FIELD_ISLOGIN);          
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
        return array(TSysUsersHistoryAbstract::FIELD_IPADDRESS, TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER, TSysUsersHistoryAbstract::FIELD_USERAGENT, TSysUsersHistoryAbstract::FIELD_USERID, TSysUsersHistoryAbstract::FIELD_IPADDRESS);
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
            return true;
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
            return $this->get(TSysUsersHistoryAbstract::FIELD_IPADDRESS);
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
            return 'historieetjelekker'.$this->get(TSysUsersHistoryAbstract::FIELD_FINGERPRINTBROWSER).'f234'.$this->get(TSysUsersHistoryAbstract::FIELD_USERID).'f44ffff'.$this->get(TSysUsersHistoryAbstract::FIELD_USERAGENT).'112e'.$this->get(TSysUsersHistoryAbstract::FIELD_IPADDRESS).'woe';            
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