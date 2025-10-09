<?php

namespace dr\modules\Mod_ContactForm\models;

use dr\classes\models\TSysModel;
// use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
// use dr\classes\types\TCurrency;
// use dr\classes\types\TDateTime;


/**
 * Contact form submissions
 * 
 * When a website user hits the "submit" button in a contact form, the submission is saved by this class
 * 
 * 
 * we work with fingerprints in order to be able to find information:
 * -The email address is encrypted ==> we use a fingerprint to find the same email address
 * -the message is fingerprinted in order to find exact copies of the same message
 * 
 * TICKETID
 * =========
 * for the ticketid we use the TSysModel::FIELD_RANDOMID field.
 * we can communicate this id to the user and email this id to the user safely
 * 
 * 
 * 
 * created 12 maart 2024
 * 12 mrt 2024: TContactFormSubmissions: created
 * 
 * @author Dennis Renirie
 */

class TContactFormSubmissions extends TSysModel
{
	const FIELD_CONTACTFORMCATEGORYID		= 'iContactFormCategoryID'; //what kind of contact is this? Legal matter? Feature request?
	const FIELD_NAMEENCRYPTED				= 'sNE'; //name of sender
	const FIELD_EMAILADDRESSENCRYPTED		= 'sEAE'; //email address of sender ENCRYPTED
	const FIELD_EMAILADDRESSFINGERPRINT		= 'sEAF'; //email address fingerprint
	const FIELD_TOPIC						= 'sTopic'; //reason of contacting
	const FIELD_MESSAGE						= 'sMessage'; 
	const FIELD_MESSAGEFINGERPRINT			= 'sMFP'; //message fingerprint
	const FIELD_IPADDRESSSENDERENCRYPTED	= 'sIAS'; 
	const FIELD_IPADDRESSSFINGERPRINT		= 'sIAFP'; 
	const FIELD_SPAMLIKELYHOOD				= 'iSpamLikelyHood';// percentage of likelyhood that this message is spam
	const FIELD_SPAMMARKEDMANUALLY			= 'iSpamMarkedManually';// manually marked spam is seen as gospel (in contrast to spam likelyhood because it is auto detected)
	//const FIELD_DATECREATED				= 'dtDateCreated';//==> TSysModel::FIELD_RECORDCREATED is used
	const FIELD_EMAILCONFIRMATIONSENTDATE	= 'dtEmailConfirmationSentDate';// date that confirmation email was sent
	const FIELD_EMAILCONFIRMATIONLINKCLICKED = 'bEmailConfirmationLinkClicked';// register if the link in the confirmation email is clicked to validate the contact form
	const FIELD_EMAILCONFIRMATIONTOKEN		= 'sEmailConfirmationToken'; //the unique token that validates this contact-form-submission via email
	const FIELD_NOTESINTERNAL				= 'sNotesInternal';//internal notes, for example from the spam detector mechanism: why it is marked as spam
	//const FIELD_TICKETID					= 'iTicketID';//==> TSysModel::FIELD_RANDOMID is used instead
	
	const SEED_EMAILADDRESSFINGERPRINT 			= 'sdfo34jkerl^3!@sjeowskdkjsd'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
	const DIGEST_EMAILADDRESSFINGERPRINT 		= ENCRYPTION_DIGESTALGORITHM_MD5; //--> doesn't need to be THAT secure. Rainbow tables won't work for this

	const SEED_MESSAGEFINGERPRINT 				= '23sddfoj3Dm$3501d'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
	const DIGEST_MESSAGEFINGERPRINT 			= ENCRYPTION_DIGESTALGORITHM_MD5; //--> doesn't need to be THAT secure, just to be calculated quickly

	const SEED_IPADDRESSFINGERPRINT 			= 'fLkvPl3$5d#456$350sss1d'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
	const DIGEST_IPADDRESSFINGERPRINT 			= ENCRYPTION_DIGESTALGORITHM_SHA512; 


	const ENCRYPTION_NAME_PASSPHRASE 			= '23ijn4olsdkmkjn0784h_dhue'; //passphrase for the encryption algo	
	const ENCRYPTION_EMAIL_PASSPHRASE 			= 'w349f3f3049fjlkjfasf___wefwefwef+='; //passphrase for the encryption algo	
	const ENCRYPTION_IPADDR_PASSPHRASE 			= '9Pldjo3jdkjn4_d'; //passphrase for the encryption algo	


	/**
	 * get category id
	 * 
	 * @return int
	 */
	public function getContactFormCategoryID()
	{
		return $this->get(TContactFormSubmissions::FIELD_ID);
	}
	
	/**
	 * set category id
	 * 
	 * @param int $iTypeID
	 */
	public function setContactFormCategoryID($iCategoryID)
	{
		$this->set(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, $iCategoryID);
	}  

	/**
	 * get name
	 * 
	 * @return string
	 */
	public function getNameDecrypted()
	{
		return $this->get(TContactFormSubmissions::FIELD_NAMEENCRYPTED, '', true);
	}
	
	/**
	 * set name
	 * 
	 * @param string $sName
	 */
	public function setNameDecrypted($sName)
	{
		$this->set(TContactFormSubmissions::FIELD_NAMEENCRYPTED, $sName, '', true);
	}        

	
	/**
	 * get email address
	 * 
	 * @return string
	 */
	public function getEmailAddressDecrypted()
	{
		return $this->get(TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, '', true);
	}
	
	/**
	 * set email address
	 * 
	 * @param string $sEmail
	 */
	public function setEmailAddressDecrypted($sEmail)
	{
		$sEmail = filter_var($sEmail, FILTER_SANITIZE_EMAIL);
		$this->set(TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, $sEmail, '', true);
		$this->set(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, getFingerprintEmail($sEmail, TContactFormSubmissions::SEED_EMAILADDRESSFINGERPRINT, TContactFormSubmissions::DIGEST_EMAILADDRESSFINGERPRINT));		
	} 	

	/**
	 * get email topic
	 * 
	 * @return string
	 */
	public function getTopic()
	{
		return $this->get(TContactFormSubmissions::FIELD_TOPIC);
	}
	
	/**
	 * set topic
	 * 
	 * @param string $sTopic
	 */
	public function setTopic($sTopic)
	{
		$this->set(TContactFormSubmissions::FIELD_TOPIC, $sTopic);
	} 


	/**
	 * get message
	 * 
	 * @return string
	 */
	public function getMessage()
	{
		return $this->get(TContactFormSubmissions::FIELD_MESSAGE);
	}
	
	/**
	 * set message
	 * 
	 * @param string $sMessage
	 */
	public function setMessage($sMessage)
	{
		$this->set(TContactFormSubmissions::FIELD_MESSAGE, $sMessage);
		$this->set(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, hash(TContactFormSubmissions::DIGEST_MESSAGEFINGERPRINT, $sMessage.TContactFormSubmissions::SEED_MESSAGEFINGERPRINT));
	} 	
		

	/**
	 * get ip address sender
	 *  
	 * @return string
	 */
	public function getIPAddressDecryped()
	{
		return $this->get(TContactFormSubmissions::FIELD_IPADDRESSSENDERENCRYPTED, '', true);
	}

	/**
	 * set ip address sender
	 * 
	 * @param string $sIP
	 */
	public function setIPAddressDecrypted($sIP)
	{
		$this->set(TContactFormSubmissions::FIELD_IPADDRESSSENDERENCRYPTED, $sIP, '', true);
		$this->set(TContactFormSubmissions::FIELD_IPADDRESSSFINGERPRINT, hash(TContactFormSubmissions::DIGEST_IPADDRESSFINGERPRINT, $sIP.TContactFormSubmissions::SEED_IPADDRESSFINGERPRINT));
	} 		


	/**
	 * get spam likelyhood in percent
	 * 
	 * @return int
	 */
	public function getSpamLikelyHood()
	{
		return $this->get(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD);
	}

	/**
	 * set spam likelyhood
	 * 
	 * @param int $iSpamLikelyHoodPercent
	 */
	public function setSpamLikelyHood($iSpamLikelyHoodPercent)
	{
		$this->set(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, $iSpamLikelyHoodPercent);
	} 	


	/**
	 * get manually marked spam
	 * 
	 * @return bool
	 */
	public function getSpamMarkedManually()
	{
		return $this->get(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY);
	}

	/**
	 * set manually marked spam
	 * 
	 * @param int $bIsSpam
	 */
	public function setSpamMarkedManually($bIsSpam)
	{
		$this->set(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, $bIsSpam);
	} 	

	/**
	 * get if the link in the confirmation email is clicke
	 * 
	 * @return bool
	 */
	public function getEmailConfirmationLinkClicked()
	{
		return $this->get(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED);
	}

	/**
	 * set if the link in the confirmation email is clicked
	 * 
	 * @param bool $bClicked
	 */
	public function setEmailConfirmationLinkClicked($bClicked)
	{
		$this->set(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, $bClicked);
	} 	

	/**
	 * get the email confirmation token
	 * 
	 * @return string
	 */
	public function getEmailConfirmationToken()
	{
		return $this->get(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONTOKEN);
	}

	/**
	 * set the email confirmation token
	 * 
	 * @param string $sToken
	 */
	public function setEmailConfirmationToken($sToken)
	{
		$this->set(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONTOKEN, $sToken);
	} 


	/**
	 * get the internal notes
	 * 
	 * @return string
	 */
	public function getNotesInternal()
	{
		return $this->get(TContactFormSubmissions::FIELD_NOTESINTERNAL);
	}

	/**
	 * set the internal notes
	 * 
	 * @param string $sNotes
	 */
	public function setNotesInternal($sNotes)
	{
		$this->set(TContactFormSubmissions::FIELD_NOTESINTERNAL, $sNotes);
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
		//contact form category
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, 0);
		$this->setFieldType(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, CT_INTEGER64);
		$this->setFieldLength(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, true); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, TContactFormCategories::class);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, TContactFormCategories::getTable());
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, true);
        $this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID);

		//sender name
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_NAMEENCRYPTED, 0);
		$this->setFieldType(TContactFormSubmissions::FIELD_NAMEENCRYPTED, CT_LONGTEXT);
		$this->setFieldLength(TContactFormSubmissions::FIELD_NAMEENCRYPTED, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_NAMEENCRYPTED, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_NAMEENCRYPTED, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_NAMEENCRYPTED, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_NAMEENCRYPTED, true);
		$this->setFieldEncryptionCypher(TContactFormSubmissions::FIELD_NAMEENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TContactFormSubmissions::FIELD_NAMEENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TContactFormSubmissions::FIELD_NAMEENCRYPTED, TContactFormSubmissions::ENCRYPTION_NAME_PASSPHRASE);			                          


		//email encrypted
		$this->setFieldCopyProps(TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, TContactFormSubmissions::FIELD_NAMEENCRYPTED);
		$this->setFieldEncryptionPassphrase(TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED, TContactFormSubmissions::ENCRYPTION_EMAIL_PASSPHRASE);			                          

		//email fingerprint
        $this->setFieldDefaultValue(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, '');
        $this->setFieldType(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, CT_VARCHAR);
        $this->setFieldLength(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, LENGTH_STRING_MD5);
        $this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, 0);
        $this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldNullable(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldEnumValues(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldUnique(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldIndexed(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldFulltext(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldUnsigned(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT, false);	
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT);	
			
		//email topic
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_TOPIC, '');
		$this->setFieldType(TContactFormSubmissions::FIELD_TOPIC, CT_VARCHAR);
		$this->setFieldLength(TContactFormSubmissions::FIELD_TOPIC, 100);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_TOPIC, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_TOPIC, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_TOPIC, true);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_TOPIC, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_TOPIC, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_TOPIC, null);
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_TOPIC, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_TOPIC, false);
        $this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_TOPIC);	
		
		//message
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_MESSAGE, '');
		$this->setFieldType(TContactFormSubmissions::FIELD_MESSAGE, CT_LONGTEXT);
		$this->setFieldLength(TContactFormSubmissions::FIELD_MESSAGE, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_MESSAGE, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_MESSAGE, null);
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_MESSAGE, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_MESSAGE, false);	
        $this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_MESSAGE);		
		
		//message fingerprint
        $this->setFieldDefaultValue(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, '');
        $this->setFieldType(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, CT_VARCHAR);
        $this->setFieldLength(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, LENGTH_STRING_MD5);
        $this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, 0);
        $this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);
        $this->setFieldNullable(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);
        $this->setFieldEnumValues(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldUnique(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);
        $this->setFieldIndexed(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, true);
        $this->setFieldFulltext(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);
        $this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, null);
        $this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);
        $this->setFieldUnsigned(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT, false);	
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_MESSAGEFINGERPRINT);	

		//ip address encrypted
		$this->setFieldCopyProps(TContactFormSubmissions::FIELD_IPADDRESSSENDERENCRYPTED, TContactFormSubmissions::FIELD_NAMEENCRYPTED);
		$this->setFieldEncryptionPassphrase(TContactFormSubmissions::FIELD_IPADDRESSSENDERENCRYPTED, TContactFormSubmissions::ENCRYPTION_IPADDR_PASSPHRASE);			                          
		

		//ip address fingerprint
		$this->setFieldCopyProps(TContactFormSubmissions::FIELD_IPADDRESSSFINGERPRINT, TContactFormSubmissions::FIELD_EMAILADDRESSFINGERPRINT);		
		$this->setFieldLength(TContactFormSubmissions::FIELD_IPADDRESSSFINGERPRINT, 255);

		//spam likelyhood
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, 0);
		$this->setFieldType(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, CT_INTEGER32);
		$this->setFieldLength(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_SPAMLIKELYHOOD);	

		//spam marked manually
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false);
		$this->setFieldType(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, CT_BOOL);
		$this->setFieldLength(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY);			

		//email confirmation sent date
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldType(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, CT_DATETIME);
		$this->setFieldLength(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, true);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONSENTDATE);	

		//email confirmation link clicked
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldType(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, CT_BOOL);
		$this->setFieldLength(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED);			
		
		//email confirmation token
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, '');
		$this->setFieldType(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, CT_VARCHAR);
		$this->setFieldLength(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, LENGTH_STRING_MD5);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, true); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED);	

		//notes internal
		$this->setFieldDefaultValue(TContactFormSubmissions::FIELD_NOTESINTERNAL, '');
		$this->setFieldType(TContactFormSubmissions::FIELD_NOTESINTERNAL, CT_LONGTEXT);
		$this->setFieldLength(TContactFormSubmissions::FIELD_NOTESINTERNAL, 0);
		$this->setFieldDecimalPrecision(TContactFormSubmissions::FIELD_NOTESINTERNAL, 0);
		$this->setFieldPrimaryKey(TContactFormSubmissions::FIELD_NOTESINTERNAL, false);
		$this->setFieldNullable(TContactFormSubmissions::FIELD_NOTESINTERNAL, false);
		$this->setFieldEnumValues(TContactFormSubmissions::FIELD_NOTESINTERNAL, null);
		$this->setFieldUnique(TContactFormSubmissions::FIELD_NOTESINTERNAL, false); 
		$this->setFieldIndexed(TContactFormSubmissions::FIELD_NOTESINTERNAL, false); 
		$this->setFieldFulltext(TContactFormSubmissions::FIELD_NOTESINTERNAL, true); 
		$this->setFieldForeignKeyClass(TContactFormSubmissions::FIELD_NOTESINTERNAL, null);
		$this->setFieldForeignKeyTable(TContactFormSubmissions::FIELD_NOTESINTERNAL, null);
		$this->setFieldForeignKeyField(TContactFormSubmissions::FIELD_NOTESINTERNAL, null);
		$this->setFieldForeignKeyJoin(TContactFormSubmissions::FIELD_NOTESINTERNAL);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormSubmissions::FIELD_NOTESINTERNAL, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormSubmissions::FIELD_NOTESINTERNAL, null); 
		$this->setFieldAutoIncrement(TContactFormSubmissions::FIELD_NOTESINTERNAL, false);
		$this->setFieldUnsigned(TContactFormSubmissions::FIELD_NOTESINTERNAL, false);
		$this->setFieldEncryptionDisabled(TContactFormSubmissions::FIELD_NOTESINTERNAL);		

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
		return array(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID, 
					TContactFormSubmissions::FIELD_NAMEENCRYPTED,
					TContactFormSubmissions::FIELD_TOPIC,
					TContactFormSubmissions::FIELD_MESSAGE,
					TContactFormSubmissions::FIELD_IPADDRESSSENDERENCRYPTED,
					TContactFormSubmissions::FIELD_SPAMLIKELYHOOD,
					TContactFormSubmissions::FIELD_SPAMMARKEDMANUALLY,
					TContactFormSubmissions::FIELD_EMAILCONFIRMATIONLINKCLICKED,
					TContactFormSubmissions::FIELD_EMAILCONFIRMATIONTOKEN,
					TContactFormSubmissions::FIELD_NOTESINTERNAL,
				);
	}
	
	/**
	 * use the auto-added id-field ?
	 * @return bool
	*/
	public function getTableUseIDField()
	{
		return true;
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
		return APP_DB_TABLEPREFIX.'ContactFormSubmissions';
	}
	
	
	
	/**
	 * OVERRIDE BY CHILD CLASS IF necessary
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		return $this->getTopic();
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
		return 'yoppie'.
			$this->get(TContactFormSubmissions::FIELD_CONTACTFORMCATEGORYID).
			$this->get(TContactFormSubmissions::FIELD_NAMEENCRYPTED).
			'pwloed'.
			$this->get(TContactFormSubmissions::FIELD_EMAILADDRESSENCRYPTED).
			$this->get(TContactFormSubmissions::FIELD_TOPIC).
			$this->get(TContactFormSubmissions::FIELD_MESSAGE);
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
		return false;
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