<?php

namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;


/**
 * represents all (failed) login attempts of users to prevent brute force attack
 * Also password recovery and account creation is counted as a login attempt to prevent auto creation of accounts
 *
 ********************************* 
 * HASHED VS ENCRYPTED USERNAMES
 *********************************
 * There are hashed and encrypted usernames.
 * I don't want to store literal usernames, because it gives away how often and which users are logging in to avoid
 * an attacker gets ahold of user names.
 * I use the hashed version as identifier (because it gives the same result every time)
 * But we want to be able to inspect usernames in the floodlogs to determine patterns,
 * so we store also 2-way encrypted usernames
 * 
 * 
 * 
 * 16 jan 2020 created
 * 23 jun 2021 empty usernames allowed
 * 23 juni 2021 rename TUserLoginAttempts -> TSysUsersFloodDetectAbstract
 * 23 juni 2021 added fields FIELD_EMAILADDRESS, FIELD_ISFAILEDLOGINATTEMPT, FIELD_ISPASSWORDRESET, FIELD_ISCREATEACCOUNTATTEMPT, FIELD_ISDUPLICATEUSERNAMEATTEMPT
 * 11 sept: TSysUsersFloodDetectAbstract: getFingerprint() renamed getFingerprintBrowser
 * 2 okt: TSysUsersFloodDetectAbstract: encryptUsername() gebruikt local pepper en SHA256
 * 2 okt: TSysUsersFloodDetectAbstract: bugfix: hashed username sql failed omdat veld te kort was
 * 15 nov 2024: TSysUsersFloodDetectAbstract: FIELD_USERNAMEHASHEDENCRYPTED renamed to FIELD_USERNAMEHASHED om duidelijker verschil te maken tussen de hashed en nog-te-implementeren encrypted username 
 * 15 nov 2024: TSysUsersFloodDetectAbstract: velden en functies gerenamed die met encrypted genaamd waren, maar in werkelijkheid met hashen te maken hadden
 * 15 nov 2024: TSysUsersFloodDetectAbstract: FIELD_USERNAMEENCRYPTED toegevoegd
 *  */

abstract class TSysUsersFloodDetectAbstract extends TSysModel
{
	// const FIELD_USERNAME = 'sUsername'; //replaced by username hashed
	const FIELD_USERNAMEHASHED = 'sUNH'; //we can't use id of a user, because the user is not yet identified (so we don't have a userid). we use a md5 hashed username as identifier for flood detection (md5 always gives the same result) and it is safer than storing a plain username in the database
	const FIELD_USERNAMEENCRYPTED = 'sUNE'; //we store submitted usernames so we can recognize username patterns by looking at the floodlogs
	const FIELD_FINGERPRINTEMAIL = 'sFPE'; 
	const FIELD_IPADDRESS = 'binIPAddress';
	const FIELD_DATEATTEMPT = 'dtDateAttempt';
	const FIELD_FINGERPRINTBROWSER = 'sFPB'; //fingerprint of the users computer (browser)	
	const FIELD_USERAGENT = 'sUA';//user
	const FIELD_ISFAILEDLOGINATTEMPT = 'bIsFailedLoginAttempt';
	const FIELD_ISSUCCEEDEDLOGINATTEMPT = 'bIsSucceededLoginAttempt'; //although we rather have succeeded attempts than failed. it is weird when someone logs in 200 times a day
	const FIELD_ISPASSWORDRESET = 'bIsPasswordReset';
	const FIELD_ISCREATEACCOUNTATTEMPT = 'bIsCreateAccountAttempt';
	const FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT = 'bIsCreateDuplicateUsernameAttempt'; //prevent people from trying too much accounts that are already taken
	const FIELD_ISDIRECTORYTRAVERSALATTEMPT = 'bIsDirectoryTraversalAttempt'; //prevent people from trying too much accounts that are already taken
	const FIELD_NOTE = 'sNote'; //describe what happened in 256 chars

	const PEPPER_HASHEDUSERNAME = 'PlW1@%hikM.,'; //we use a local pepper, because all the hashed usernames are the same for the same user (we can't change this otherwise we can't search for it). if we would have a systemwide pepper, if you would brute force the hash by hashing all usernames from the user table, you know the systemwide pepper
	const DIGEST_HASHEDUSERNAME = ENCRYPTION_DIGESTALGORITHM_MD5; //doesnt matter that much, it only needs to obfuscate a bit and be somewhat unique

	const PEPPER_EMAILADDRESSFINGERPRINT = '94nD_&%#igQW'; //we use a local pepper, because all the hashed email addresses are the same for the same user (we can't change this otherwise we can't search for it). if we would have a systemwide pepper, if you would brute force the hash by looking for email characteristics (@ and . [domain, most likely gmail.com]), you know the systemwide pepper
	const DIGEST_EMAILFINGERPRINT = ENCRYPTION_DIGESTALGORITHM_MD5; //doesnt matter that much, it only needs to obfuscate a bit and be somewhat unique

	const ENCRYPTION_USERNAME_PASSPHRASE 		= 'sdfBmds_d#sdsMv+=sdf'; //passphrase for the encryption algo

	/**
	 * get username
	 * @return string 
	 */
	public function getUsername()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, '', true);
	}
	
	/**
	 * 
	 * @param string $sUser
	 */
	public function setUsername($sUser)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, $sUser, '', true);
	}
	
	/**
	 * get hashed username
	 * @return string 
	 */
	public function getUsernameHashed()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED);
	}
	
	/**
	 * set username
	 * @param string $sUser
	 */
	public function setUsernameHashed($sUser)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, TSysUsersFloodDetectAbstract::hashUsername($sUser));
	}

	/**
	 * hash username
	 *
	 * @return void
	 */
	public static function hashUsername($sUsernameUncrypted)
	{
		$sResult = '';
		$sResult = $sUsernameUncrypted.TSysUsersFloodDetectAbstract::PEPPER_HASHEDUSERNAME;
		return hash(TSysUsersFloodDetectAbstract::DIGEST_HASHEDUSERNAME, $sResult);
	}

	/**
	 * get ip address
	 * @return string 
	 */
	public function getIP()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS);
	}
	
	/**
	 * set ip address
	 * @param string $sIP
	 */
	public function setIP($sIP)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, $sIP);
	}        
	
	/**
	 * get date login attempt
	 * 
	 * @return TDateTime 
	 */
	public function getDateAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT);
	}
	
	/**
	 * set date login attempt
	 * 
	 * @param string $sIP
	 */
	public function setDateAttempt(TDateTime $objDateTime)
	{
		$this->setTDateTime(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objDateTime);
	}          
        
	/**
	 * set email address fingerprint (encrypted)
	 *
	 * @param string $sAddress
	 * @return void
	 */
	public function setFingerprintEmail($sFingerprint)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, $sFingerprint);
	}

	/**
	 * just make email fingerprint with seed and digest from this class
	 * WITHOUT setting or getting it in this class
	 *
	 * @param string $sEmailAddress
	 * @return string
	 */
	public function generateEmailAddressFingerprint($sEmailAddress)
	{
		return getFingerprintEmail($sEmailAddress, TSysUsersFloodDetectAbstract::PEPPER_EMAILADDRESSFINGERPRINT, TSysUsersFloodDetectAbstract::DIGEST_EMAILFINGERPRINT);
	}

	/**
	 * set email address uncrypted
	 *
	 * @param string $sAddress
	 * @return void
	 */
	public function setEmailAddressUncrypted($sEmailAddress)
	{
		$this->setFingerprintEmail($this->generateEmailAddressFingerprint($sEmailAddress));
	}	

	/**
	 * get email address
	 * 
	 * @return string 
	 */
	public function getFingerprintEmail()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL);
	}

	/**
	 * set fingerprint browser
	 *
	 * @param string $sFingerPrint
	 * @return void
	 */
	public function setFingerprintBrowser($sFingerPrint)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, $sFingerPrint);
	}

	/**
	 * get fingerprint browser
	 * 
	 * @return string 
	 */
	public function getFingerprintBrowser()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER);
	}

	/**
	 * set user agent
	 *
	 * @param string $sAgent
	 * @return void
	 */
	public function setUserAgent($sAgent)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_USERAGENT, $sAgent);
	}

	/**
	 * get user agent
	 * 
	 * @return string 
	 */
	public function getUserAgent()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_USERAGENT);
	}

	/**
	 * set failed login attempt
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsFailedLoginAttempt($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, $bStatus);
	}

	/**
	 * get failed login attempt
	 * 
	 * @return bool 
	 */
	public function getIsFailedLoginAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);
	}

	/**
	 * set succeeded login attempt
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsSucceededLoginAttempt($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISSUCCEEDEDLOGINATTEMPT, $bStatus);
	}

	/**
	 * get succeeded login attempt
	 * 
	 * @return bool 
	 */
	public function getIsSucceededLoginAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISSUCCEEDEDLOGINATTEMPT);
	}	

	/**
	 * set password reset
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsPasswordReset($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISPASSWORDRESET, $bStatus);
	}

	/**
	 * get password reset
	 * 
	 * @return bool 
	 */
	public function getIsPasswordReset()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISPASSWORDRESET);
	}	

	/**
	 * set create account attempt
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsCreateAccountAttempt($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISCREATEACCOUNTATTEMPT, $bStatus);
	}

	/**
	 * get create account attempt
	 * 
	 * @return string 
	 */
	public function getIsCreateAccountAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISCREATEACCOUNTATTEMPT);
	}	


	/**
	 * set create account duplicate username attempt
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsCreateDuplicateUsernameAttempt($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT, $bStatus);
	}

	/**
	 * get create account duplicate username attempt
	 * 
	 * @return bool 
	 */
	public function getIsCreateDuplicateUsernameAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT);
	}		

	/**
	 * set directory traversal attempt
	 *
	 * @param bool $bStatus
	 * @return void
	 */
	public function setIsDirectoryTraversalAttempt($bStatus)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_ISDIRECTORYTRAVERSALATTEMPT, $bStatus);
	}

	/**
	 * get directory traversal attempt
	 * 
	 * @return bool 
	 */
	public function getIsDirectoryTraversalAttempt()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_ISDIRECTORYTRAVERSALATTEMPT);
	}		

	/**
	 * set note
	 *
	 * @param int $sAddress
	 * @return void
	 */
	public function setNote($sNote)
	{
		$this->set(TSysUsersFloodDetectAbstract::FIELD_NOTE, $sNote);
	}

	/**
	 * get directory traversal attempt
	 * 
	 * @return string 
	 */
	public function getNote()
	{
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_NOTE);
	}		


	/**
	 * delete logs older than X days
	 * (you can set the number of days as parameter)
	 *
	 * @param int $iDaysOld number of days that the logs be old before they get deleted, default is 1095 (that is 3 years)
	 * @return boolean true = success, false is error
	 */
	public function deleteOldLogsFromDB($iDaysOld = 1095)
	{
		$bResult = false;
		$objCopy = $this->getCopy();
        $objTime = new TDateTime(time());
        $objTime->subtractDays($iDaysOld);
        $objCopy->newQuery();
        $objCopy->find(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, $objTime, COMPARISON_OPERATOR_LESS_THAN);
        $bResult = $objCopy->deleteFromDB(true);
        unset($objUsersAttempts);
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
	{
		$objToday = new TDateTime(time());
		$this->setDateAttempt($objToday);
		unset($objToday);
	}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//username ===> replaced by username hashed
		// $this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_USERNAME, '');
		// $this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_USERNAME, CT_VARCHAR);
		// $this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_USERNAME, 100);
		// $this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_USERNAME, 0);
		// $this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_USERNAME, true);//is possible to be empty, sometimes we want to register attempts for password recovery and account creation
		// $this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_USERNAME, null);
		// $this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_USERNAME, false);
		// $this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_USERNAME);			                                              

		//username hashed
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, CT_VARCHAR);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, 255);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, true);//is possible to be empty, sometimes we want to register attempts for password recovery and account creation
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED, false);		
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_USERNAMEHASHED);

		//username encrypted
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, CT_LONGTEXT);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, 0);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, true);
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false); 
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, false);
		$this->setFieldEncryptionCypher(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED, TSysUsersFloodDetectAbstract::FIELD_USERNAMEENCRYPTED);			                          


		//email address fingerprint
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, CT_VARCHAR);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, LENGTH_STRING_MD5);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, true);//is possible to be empty, sometimes we want to register attempts for password recovery and account creation
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, true);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, true);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL, false);		
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTEMAIL);

		
		//ip
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, CT_IPADDRESS);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 0);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, true);//is possible to be empty
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, true);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, false);		
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS);
                
		//datetime of attempt
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, CT_DATETIME);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, 0);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, true);
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT, false);    
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT);		
		
		
		//fingerprint browser
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, CT_VARCHAR);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, LENGTH_STRING_MD5);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, true);//is possible to be empty
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, true);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER, false);
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER);				

		//user agent
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_USERAGENT, TSysUsersFloodDetectAbstract::FIELD_FINGERPRINTBROWSER);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_USERAGENT, 255);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_USERAGENT, false);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_USERAGENT, false);

		//failed login attempt
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, 0);
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, CT_BOOL);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, 0);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);		
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT, false);	
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);							

		//is succeeded login attempt
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_ISSUCCEEDEDLOGINATTEMPT, TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);
		
		//is password reset
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_ISPASSWORDRESET, TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);

		//is create account attempt
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_ISCREATEACCOUNTATTEMPT, TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);

		//Is Create Duplicate Username Attempt
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_ISCREATEDUPLICATEUSERNAMEATTEMPT, TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);

		//Is Create Duplicate Username Attempt
		$this->setFieldCopyProps(TSysUsersFloodDetectAbstract::FIELD_ISDIRECTORYTRAVERSALATTEMPT, TSysUsersFloodDetectAbstract::FIELD_ISFAILEDLOGINATTEMPT);


		//note
		$this->setFieldDefaultValue(TSysUsersFloodDetectAbstract::FIELD_NOTE, '');
		$this->setFieldType(TSysUsersFloodDetectAbstract::FIELD_NOTE, CT_VARCHAR);
		$this->setFieldLength(TSysUsersFloodDetectAbstract::FIELD_NOTE, 255);
		$this->setFieldDecimalPrecision(TSysUsersFloodDetectAbstract::FIELD_NOTE, 0);
		$this->setFieldPrimaryKey(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);
		$this->setFieldNullable(TSysUsersFloodDetectAbstract::FIELD_NOTE, true);//is possible to be empty, sometimes we want to register attempts without notes
		$this->setFieldEnumValues(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldUnique(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);
		$this->setFieldIndexed(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);
		$this->setFieldFulltext(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);
		$this->setFieldForeignKeyClass(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldForeignKeyTable(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldForeignKeyField(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldForeignKeyJoin(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysUsersFloodDetectAbstract::FIELD_NOTE, null);
		$this->setFieldAutoIncrement(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);
		$this->setFieldUnsigned(TSysUsersFloodDetectAbstract::FIELD_NOTE, false);		
		$this->setFieldEncryptionDisabled(TSysUsersFloodDetectAbstract::FIELD_NOTE);		
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
		return array(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS, TSysUsersFloodDetectAbstract::FIELD_DATEATTEMPT);
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
		return false;
	}
	
	/**
	 * order field to switch order between records
	*/
	public function getTableUseOrderField()
	{
		return false;
	}
	
	/**
	 * use checkout for locking file for editing
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
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysWebsites';
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
		return $this->get(TSysUsersFloodDetectAbstract::FIELD_IPADDRESS);
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
		return '';
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
} 
?>