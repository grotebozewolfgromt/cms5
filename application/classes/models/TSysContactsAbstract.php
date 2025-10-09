<?php
namespace dr\classes\models;

use dr\classes\controllers\TControllerAbstract;
use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Contacts\models\TSysContacts;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

/**
 * This class represents contacts in an address book.
 *  
 * This is an abstract class that you can reuse for webshop customers, CMS etc, making an invoicing system for clients etc
 * 
 * ENCRYPTION
 * ==========
 * A lot in this class is encrypted for privacy reasons.
 * If an attacker ever gets a hold of the database
 * the most crucial privacy sentive data is encrypted.
 * The idea: if an attacker gets the database it is worthless to them because everything worthwhile is encrypted and takes too much effort to decrypt, so they move on to something easier
 * 
 * BILLING fields
 * =================
 * There is made a clear distinction between billing address fields, residential address fields, or delivery address fields (residential, delivery: inherit to add)
 * For example: a webshop order can have a delivery and a separate billing address.
 * Since billing addresses are the ones used most often, these are defined in this abstract class
 * 
 * CUSTOM IDENTIFIER
 * =================
 * Because so much is encrypted, it may be hard to search and find contacts 
 * (you can't search on encrypted fields, it defies the whole purpose of encryption)
 * Therefore: the custom identifier.
 * This can be anything that refers to a contact in a reasonably unique way: a postal code, the first 6 letters of the last name. 
 * This will be stored in plain text in database for searching purposes. 
 * Make sure it is not uniquely identifyable enough when the database gets breached
 * 
 * 
 * 2022 Dennis Renirie
 */


abstract class TSysContactsAbstract extends TSysModel
{
	const FIELD_CUSTOMID 						= 'sCustomID'; //Custom identifier, anything that refers to a contact in a reasonably unique way: a postal code, the first 6 letters of the last name. This will be stored in plain text in database for searching purposes. Make sure it is not identifyable enough when the database gets breached
	const FIELD_COMPANYNAME 					= 'sCompanyName';
	const FIELD_FIRSTNAMEINITALS 				= 'sFirstNameInitials'; //first name or initials
	const FIELD_LASTNAME 						= 'sLastName'; 
	const FIELD_LASTNAMEPREFIX 					= 'sLastNamePrefix';  //tussenvoegsel: Van, Le etc.
	const FIELD_EMAILADDRESSENCRYPTED   		= 'sEAE';//Email Address Encrypted internally stored in encrypted form - 2 way encrypted email address
	const FIELD_EMAILADDRESSFINGERPRINT 		= 'sEAF';//Fingerprint Email Address, so we can lookup a record based on email address. We can't salt this, because we need to be able to search on it in the database for password recovery
	const FIELD_ONMAILINGLIST					= 'bOnMailingList';
	const FIELD_ONBLACKLIST						= 'bOnBlackList';
	const FIELD_COUNTRYIDCODEPHONE1				= 'iCountryIDCodePhone1'; //country code of phone 1
	const FIELD_PHONENUMBER1					= 'sPhoneNumber1';
	const FIELD_COUNTRYIDCODEPHONE2				= 'iCountryIDCodePhone2'; //country code of phone 2
	const FIELD_PHONENUMBER2					= 'sPhoneNumber2';
	const FIELD_CHAMBEROFCOMMERCENO				= 'sChamberOfCommerceNO'; //chamber of commerce number encrypted
	const FIELD_NOTES 							= 'sNotes'; //internal notes about the client only seen by the user (not client)
	const FIELD_FIRSTCONTACT 					= 'dtFirstContact'; //data entry occurred contact
	const FIELD_LASTCONTACT 					= 'dtLastContact'; //when had last contact with this contact? (default date last changed doesn't cover this, because you can update client data without having contact with him)

	const FIELD_BILLINGADDRESSMISC 				= 'sBillingAddressMisc'; //appartment building/ company department
	const FIELD_BILLINGADDRESSSTREET 			= 'sBillingAddressStreet'; //street + housenumber
	const FIELD_BILLINGPOSTALCODEZIP 			= 'sBillingZipPostalCode'; //postal code / zip code
	const FIELD_BILLINGCITY 					= 'sBillingCity';
	const FIELD_BILLINGSTATEREGION 				= 'sBillingStateRegion'; //state, region, province
	const FIELD_BILLINGCOUNTRYID 				= 'iBillingCountryID'; //contryid from the system
	const FIELD_BILLINGVATNUMBER 				= 'sBillingVatNumber';//vat number encrypted
	const FIELD_BILLINGEMAILADDRESSENCRYPTED   	= 'sBEAE';//Email Address Encrypted internally stored in encrypted form - 2 way encrypted email address
	const FIELD_BILLINGEMAILADDRESSFINGERPRINT 	= 'sBEAF';//Fingerprint Email Address, so we can lookup a record based on email address. We can't salt this, because we need to be able to search on it in the database for password recovery
	const FIELD_BILLINGBANKACCOUNTNO 			= 'sBBANO';//bank account number encrypted

	const FIELD_DELIVERYADDRESSMISC 			= 'sDeliveryAddressMisc'; //appartment building/ company department
	const FIELD_DELIVERYADDRESSSTREET 			= 'sDeliveryAddressStreet'; //street
	const FIELD_DELIVERYPOSTALCODEZIP 			= 'sDeliveryZipPostalCode'; //postal code / zip code
	const FIELD_DELIVERYCITY 					= 'sDeliveryCity';
	const FIELD_DELIVERYSTATEREGION 			= 'sDeliveryStateRegion'; //state, region, province
	const FIELD_DELIVERYCOUNTRYID 				= 'iDeliveryCountryID'; //contryid from the system

	const SEED_EMAILADDRESSFINGERPRINT 			= 'sdfio34msd_#dlwejkwen23ED3eddq_212$dsdf'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
	const DIGEST_EMAILADDRESSFINGERPRINT 		= ENCRYPTION_DIGESTALGORITHM_SHA512;
	const SEED_BILLINGEMAILADDRESSFINGERPRINT 	= '49fr040M9834rjiVb34LW4Lmdjsoi89rhf03hern34r'; //seed to make it harder to decrypt, when we change it up per class not every table has the same seed
	const DIGEST_BILLINGEMAILADDRESSFINGERPRINT = ENCRYPTION_DIGESTALGORITHM_SHA512;

	const ENCRYPTION_LASTNAME_PASSPHRASE 		= '34r98dvjef9034fiuefjidf_giwsdf#d_sdf'; //passphrase for the encryption algo
	const ENCRYPTION_EMAIL_PASSPHRASE 			= 'e33FPL@dMbDfewd_=EwcqP()#d_sdf'; //passphrase for the encryption algo
	const ENCRYPTION_PHONE1_PASSPHRASE 			= '1dPlodk_ede=weldk3mSw2'; //passphrase for the encryption algo
	const ENCRYPTION_PHONE2_PASSPHRASE 			= '3d4gfggwrty5_==+2m4C3LPd'; //passphrase for the encryption algo
	const ENCRYPTION_CHAMBERCOMMERCE_PASSPHRASE = '6d434349kfkflkPLDSss$3___e'; //passphrase for the encryption algo
	const ENCRYPTION_BILL_ADDR1_PASSPHRASE 		= 'adf83fbwcoisdbjk23bnwe_shwojewdwef_'; //passphrase for the encryption algo: bill=billing
	const ENCRYPTION_BILL_ADDR2_PASSPHRASE 		= 'asdflijn34%$132sdfjsf_sdfkj34ro3m'; //passphrase for the encryption algo
	const ENCRYPTION_BILL_POSTAL_PASSPHRASE 	= 'a56ertyuisdfghjkcvbnmm'; //passphrase for the encryption algo
	const ENCRYPTION_BILL_VATNO_PASSPHRASE 		= '234234sdkfsj4__++#+_23me90djd5r'; //passphrase for the encryption algo
	const ENCRYPTION_BILL_EMAIL_PASSPHRASE 		= 'wefoi3fnfriorfnerkp+_wewerjwer'; //passphrase for the encryption algo
	const ENCRYPTION_BILL_BANKACC_PASSPHRASE 	= 'whodlsd finsdfi3 3 dBMa2_2'; //passphrase for the encryption algo
	const ENCRYPTION_DELI_ADDR1_PASSPHRASE 		= 'sdfg4-4fkdfgk333rfs_Plds'; //passphrase for the encryption algo: deli = delivery
	const ENCRYPTION_DELI_ADDR2_PASSPHRASE 		= 'ae444h%3_jsf_sdfkj34ro3m'; //passphrase for the encryption algo
	const ENCRYPTION_DELI_POSTAL_PASSPHRASE 	= 'rtsdfgvbzertsdgzfb456wertyh'; //passphrase for the encryption algo	

	const VALUE_ANONYMOUS = '[anonymous]';
	const VALUE_DEFAULT = '[default]';

	public function getCustomIdentifier()
	{
		return $this->get(TSysContactsAbstract::FIELD_CUSTOMID);
	}

	public function setCustomIdentifier($sRefNo)
	{
		$this->set(TSysContactsAbstract::FIELD_CUSTOMID, $sRefNo);
	}

	public function getCompanyName()
	{
		return $this->get(TSysContactsAbstract::FIELD_COMPANYNAME);
	}

	public function setCompanyName($sCompanyName)
	{
		$this->set(TSysContactsAbstract::FIELD_COMPANYNAME, $sCompanyName);
	}

	public function getFirstNameInitials()
	{
		return $this->get(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS);
	}

	public function setFirstNameInitials($sFirstName)
	{
		$this->set(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, $sFirstName);
	}

	public function getLastName()
	{
		return $this->get(TSysContactsAbstract::FIELD_LASTNAME, '', true);
	}

	public function setLastName($sLastName)
	{
		$this->set(TSysContactsAbstract::FIELD_LASTNAME, $sLastName, '', true);
	}

	public function getLastNamePrefix()
	{
		return $this->get(TSysContactsAbstract::FIELD_LASTNAMEPREFIX);
	}

	public function setLastNamePrefix($sPrefix)
	{
		$this->set(TSysContactsAbstract::FIELD_LASTNAMEPREFIX, $sPrefix);
	}


    /**
     * get email address and decrypt it
     * 
     * @return string
     */
    public function getEmailAddressDecrypted()
    {
        return $this->get(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, '', true);
    }

    /**
     * encrypts and sets email address AND email identifier
     * 
     * @param string $sEmail
     */
    public function setEmailAddressDecrypted($sUncryptedEmail)
    {
        $this->set(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, $sUncryptedEmail, '', true);
        $this->set(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, getFingerprintEmail($sUncryptedEmail, TSysContactsAbstract::SEED_EMAILADDRESSFINGERPRINT, TSysContactsAbstract::DIGEST_EMAILADDRESSFINGERPRINT));
    }

	public function getOnMailingList()
	{
		return $this->get(TSysContactsAbstract::FIELD_ONMAILINGLIST);
	}

	public function setOnMailingList($bValue)
	{
		$this->set(TSysContactsAbstract::FIELD_ONMAILINGLIST, $bValue);
	}

	public function getOnBlackList()
	{
		return $this->get(TSysContactsAbstract::FIELD_ONBLACKLIST);
	}

	public function setOnBlackList($bValue)
	{
		$this->set(TSysContactsAbstract::FIELD_ONBLACKLIST, $bValue);
	}	

	public function getCountryIDCodePhoneNumber1()
	{
		return $this->get(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE1);
	}

	public function setCountryIDCodePhoneNumber1($iCountryID)
	{
		$this->set(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE1, $iCountryID);
	}		


	public function getPhoneNumber1()
	{
		return $this->get(TSysContactsAbstract::FIELD_PHONENUMBER1);
	}

	public function setPhoneNumber1($sPhone)
	{
		$this->set(TSysContactsAbstract::FIELD_PHONENUMBER1, $sPhone);
	}		

	public function getCountryIDCodePhoneNumber2()
	{
		return $this->get(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE2);
	}

	public function setCountryIDCodePhoneNumber2($iCountryID)
	{
		$this->set(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE2, $iCountryID);
	}	

	public function getPhoneNumber2()
	{
		return $this->get(TSysContactsAbstract::FIELD_PHONENUMBER2);
	}

	public function setPhoneNumber2($sPhone)
	{
		$this->set(TSysContactsAbstract::FIELD_PHONENUMBER2, $sPhone);
	}	


	public function getChamberOfCommerceNoDecrypted()
	{
		return $this->get(TSysContactsAbstract::FIELD_CHAMBEROFCOMMERCENO, '', true);
	}

	public function setChamberCommerceNoEncrypted($sNO)
	{
		$this->set(TSysContactsAbstract::FIELD_CHAMBEROFCOMMERCENO, $sNO, '', true);
	}		

	public function getNotes()
	{
		return $this->get(TSysContactsAbstract::FIELD_NOTES);
	}

	public function setNotes($sNotes)
	{
		$this->set(TSysContactsAbstract::FIELD_NOTES, $sNotes);
	}


	public function getFirstContact()
	{
		return $this->get(TSysContactsAbstract::FIELD_FIRSTCONTACT);
	}

	public function setFirstContact($objDate)
	{
		$this->set(TSysContactsAbstract::FIELD_FIRSTCONTACT, $objDate);
	}

	public function getLastContact()
	{
		return $this->get(TSysContactsAbstract::FIELD_FIRSTCONTACT);
	}

	public function setLastContact($objDate)
	{
		$this->set(TSysContactsAbstract::FIELD_LASTCONTACT, $objDate);
	}


	public function getBillingAddressMisc()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGADDRESSMISC, '', true);
	}

	public function setBillingAddressMisc($sLine1)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGADDRESSMISC, $sLine1, '', true);
	}

	
	public function getBillingAddressStreet()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET, '', true);
	}

	public function setBillingAddressStreet($sLine2)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET, $sLine2, '', true);
	}

	public function getBillingPostalCodeZip()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, '', true);
	}

	public function setBillingPostalCodeZip($sZip)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, $sZip, '', true);
	}

	public function getBillingCity()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGCITY);
	}

	public function setBillingCity($sCity)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGCITY, $sCity);
	}

	public function getBillingStateRegion()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGSTATEREGION);
	}

	public function setBillingStateRegion($sRegion)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGSTATEREGION, $sRegion);
	}

	public function getBillingCountryID()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID);
	}

	public function setBillingCountryID($iCountryID)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, $iCountryID);
	}

	public function getBillingVATNumberDecrypted()
	{
		return $this->get(TSysContactsAbstract::FIELD_BILLINGVATNUMBER, '', true);
	}

	public function setBillingVATNumberEncrypted($sVATNumber)
	{
		$this->set(TSysContactsAbstract::FIELD_BILLINGVATNUMBER, $sVATNumber, '', true);
	}	

    /**
     * get email address and decrypt it
     * 
     * @return string
     */
    public function getBillingEmailAddressDecrypted()
    {
        return $this->get(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', true);
    }

    /**
     * encrypts and sets email address AND email identifier
     * 
     * @param string $sEmail
     */
    public function setBillingEmailAddressDecrypted($sUncryptedEmail)
    {
        $this->set(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSENCRYPTED, $sUncryptedEmail, '', true);
        $this->set(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSFINGERPRINT, getFingerprintEmail($sUncryptedEmail, TSysContactsAbstract::SEED_BILLINGEMAILADDRESSFINGERPRINT, TSysContactsAbstract::DIGEST_BILLINGEMAILADDRESSFINGERPRINT));
    }

    /**
     * get bank account number and decrypt it
     * 
     * @return string
     */
    public function getBillingBankAccountNoDecrypted()
    {
        return $this->get(TSysContactsAbstract::FIELD_BILLINGBANKACCOUNTNO, '', true);
    }

    /**
     * encrypts and sets bank account number
     * 
     * @param string $sUncryptedBankAccountNo
     */
    public function setBillingBankAccountNoDecrypted($sUncryptedBankAccountNo)
    {
        $this->set(TSysContactsAbstract::FIELD_BILLINGBANKACCOUNTNO, $sUncryptedBankAccountNo, '', true);
    }	

	public function getDeliveryAddressMisc()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYADDRESSMISC, '', true);
	}

	public function setDeliveryAddressMisc($sLine1)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYADDRESSMISC, $sLine1, '', true);
	}

	
	public function getDeliveryAddressStreet()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYADDRESSSTREET, '', true);
	}

	public function setDeliveryAddressStreet($sLine2)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYADDRESSSTREET, $sLine2, '', true);
	}

	public function getDeliveryPostalCodeZip()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, '', true);
	}

	public function setDeliveryPostalCodeZip($sZip)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, $sZip, '', true);
	}

	public function getDeliveryCity()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYCITY);
	}

	public function setDeliveryCity($sCity)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYCITY, $sCity);
	}

	public function getDeliveryStateRegion()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYSTATEREGION);
	}

	public function setDeliveryStateRegion($sRegion)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYSTATEREGION, $sRegion);
	}

	public function getDeliveryCountryID()
	{
		return $this->get(TSysContactsAbstract::FIELD_DELIVERYCOUNTRYID);
	}

	public function setDeliveryCountryID($iCountryID)
	{
		$this->set(TSysContactsAbstract::FIELD_DELIVERYCOUNTRYID, $iCountryID);
	}




	

	/**
	 * this function creates table in database and calls all foreign key classes to do the same
	 * 
	 * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
	 *
	 * @param array $arrPreviousDependenciesModelClasses with classnames. 
	 * @return bool success?
	 */
	// public function install($arrPreviousDependenciesModelClasses = null)
	// {
	// 	$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		
	// 	if ($bSuccess)
	// 	{
	// 		$this->limitOne();
	// 		$this->loadFromDB(false);
	// 		if ($this->count() == 0) //only add when table is empty
	// 		{
	// 			$this->clear();

	// 			$this->newRecord();
	// 			$this->setCustomIdentifier('DEFAULT');
	// 			$this->setCompanyName('DEFAULT');
				
	// 			if (!$this->saveToDB())
	// 				error('error saving default contact on install');

	// 		}
	// 	}
		
	// 	return $bSuccess;
	// }
	

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
		//custom identifier
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_CUSTOMID, '');
		$this->setFieldType(TSysContactsAbstract::FIELD_CUSTOMID, CT_VARCHAR);
		$this->setFieldLength(TSysContactsAbstract::FIELD_CUSTOMID, 50);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_CUSTOMID, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_CUSTOMID, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_CUSTOMID, true);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_CUSTOMID, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_CUSTOMID, true); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_CUSTOMID, true); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_CUSTOMID, null);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_CUSTOMID, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_CUSTOMID, false);
        $this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_CUSTOMID);		


		//company name
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_COMPANYNAME, '');
		$this->setFieldType(TSysContactsAbstract::FIELD_COMPANYNAME, CT_VARCHAR);
		$this->setFieldLength(TSysContactsAbstract::FIELD_COMPANYNAME, 100);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_COMPANYNAME, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_COMPANYNAME, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_COMPANYNAME, true);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_COMPANYNAME, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_COMPANYNAME, true); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_COMPANYNAME, true); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_COMPANYNAME, null);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_COMPANYNAME, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_COMPANYNAME, false);
        $this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_COMPANYNAME);		


		//first name and initials
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, TSysContactsAbstract::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, 50);
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, false);
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, false);

		//last name
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_LASTNAME, '');
		$this->setFieldType(TSysContactsAbstract::FIELD_LASTNAME, CT_LONGTEXT);
		$this->setFieldLength(TSysContactsAbstract::FIELD_LASTNAME, 0);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_LASTNAME, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_LASTNAME, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_LASTNAME, true);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_LASTNAME, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_LASTNAME, false); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_LASTNAME, false); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_LASTNAME, null);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_LASTNAME, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_LASTNAME, false);
		$this->setFieldEncryptionCypher(TSysContactsAbstract::FIELD_LASTNAME, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContactsAbstract::FIELD_LASTNAME, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_LASTNAME, TSysContactsAbstract::ENCRYPTION_LASTNAME_PASSPHRASE);			                          
		
		//last name prefix
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_LASTNAMEPREFIX, TSysContactsAbstract::FIELD_LASTNAME);
		$this->setFieldLength(TSysContactsAbstract::FIELD_LASTNAMEPREFIX, 20);


        //2-way encrypted email address
        $this->setFieldDefaultValue(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, '');
        $this->setFieldType(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, CT_LONGTEXT);
        $this->setFieldLength(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldNullable(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, true);
        $this->setFieldEnumValues(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldUnique(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldIndexed(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldFulltext(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldUnsigned(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, false);
		$this->setFieldEncryptionCypher(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED, TSysContactsAbstract::ENCRYPTION_EMAIL_PASSPHRASE);			                          

        //email fingerprint, so we can lookup the record based on email address
        $this->setFieldDefaultValue(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, '');
        $this->setFieldType(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, CT_VARCHAR);
        $this->setFieldLength(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, 255);
        $this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, 0);
        $this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldNullable(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldEnumValues(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldUnique(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldIndexed(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldFulltext(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldUnsigned(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT, false);
		$this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT);	

		//on mailing list
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_ONMAILINGLIST, false);
		$this->setFieldType(TSysContactsAbstract::FIELD_ONMAILINGLIST, CT_BOOL);
		$this->setFieldLength(TSysContactsAbstract::FIELD_ONMAILINGLIST, 0);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_ONMAILINGLIST, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_ONMAILINGLIST, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_ONMAILINGLIST, false);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_ONMAILINGLIST, false); 
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_ONMAILINGLIST, false); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_ONMAILINGLIST, false); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_ONMAILINGLIST, null);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_ONMAILINGLIST, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_ONMAILINGLIST, true);
        $this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_ONMAILINGLIST);	

		//on blacklist
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_ONBLACKLIST, TSysContactsAbstract::FIELD_ONMAILINGLIST);

		//countryid countrycode phone 1
		$this->setFieldDefaultsIntegerForeignKey(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE1, TSysCountries::class, TSysCountries::getTable(), TSysCountries::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE1, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE1, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//phone1
        $this->setFieldDefaultValue(TSysContactsAbstract::FIELD_PHONENUMBER1, '');
        $this->setFieldType(TSysContactsAbstract::FIELD_PHONENUMBER1, CT_LONGTEXT);
        $this->setFieldLength(TSysContactsAbstract::FIELD_PHONENUMBER1, 0);
        $this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_PHONENUMBER1, 0);
        $this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
        $this->setFieldNullable(TSysContactsAbstract::FIELD_PHONENUMBER1, true);
        $this->setFieldEnumValues(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldUnique(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
        $this->setFieldIndexed(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
        $this->setFieldFulltext(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
        $this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_PHONENUMBER1, null);
        $this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
        $this->setFieldUnsigned(TSysContactsAbstract::FIELD_PHONENUMBER1, false);
		$this->setFieldEncryptionCypher(TSysContactsAbstract::FIELD_PHONENUMBER1, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContactsAbstract::FIELD_PHONENUMBER1, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_PHONENUMBER1, TSysContactsAbstract::ENCRYPTION_PHONE1_PASSPHRASE);	

		//countryid countrycode phone 1
		$this->setFieldDefaultsIntegerForeignKey(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE2, TSysCountries::class, TSysCountries::getTable(), TSysCountries::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE2, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_COUNTRYIDCODEPHONE2, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//phone2
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_PHONENUMBER2, TSysContactsAbstract::FIELD_PHONENUMBER1);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_PHONENUMBER2, TSysContactsAbstract::ENCRYPTION_PHONE2_PASSPHRASE);	

       	//2-way encrypted chamber of commerce number
	   	$this->setFieldCopyProps(TSysContactsAbstract::FIELD_CHAMBEROFCOMMERCENO, TSysContactsAbstract::FIELD_PHONENUMBER1);
	   	$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_CHAMBEROFCOMMERCENO, TSysContactsAbstract::ENCRYPTION_CHAMBERCOMMERCE_PASSPHRASE);			                          


		//notes
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_NOTES, TSysContactsAbstract::FIELD_COMPANYNAME);
		$this->setFieldType(TSysContactsAbstract::FIELD_NOTES, CT_LONGTEXT);
		$this->setFieldLength(TSysContactsAbstract::FIELD_NOTES, 0);
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_NOTES, false);
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_NOTES, true);


		//first contact date
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_FIRSTCONTACT, '');
		$this->setFieldType(TSysContactsAbstract::FIELD_FIRSTCONTACT, CT_DATETIME);
		$this->setFieldLength(TSysContactsAbstract::FIELD_FIRSTCONTACT, 0);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_FIRSTCONTACT, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_FIRSTCONTACT, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_FIRSTCONTACT, true);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_FIRSTCONTACT, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_FIRSTCONTACT, false); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_FIRSTCONTACT, false); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_FIRSTCONTACT, null);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_FIRSTCONTACT, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_FIRSTCONTACT, false);
		$this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_FIRSTCONTACT);	

		//last contact date
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_LASTCONTACT, TSysContactsAbstract::FIELD_FIRSTCONTACT);


		//Billing: addressline1
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGADDRESSMISC, TSysContactsAbstract::FIELD_LASTNAME);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_BILLINGADDRESSMISC, TSysContactsAbstract::ENCRYPTION_BILL_ADDR1_PASSPHRASE);			                          

		//Billing: addressline2
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET, TSysContactsAbstract::FIELD_LASTNAME);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET, TSysContactsAbstract::ENCRYPTION_BILL_ADDR2_PASSPHRASE);			                          

		//Billing: postal code / zip code
		// $this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, TSysContactsAbstract::FIELD_LASTNAME);
        $this->setFieldDefaultValue(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, '');
        $this->setFieldType(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, CT_LONGTEXT);
        $this->setFieldLength(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, 0);
        $this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, 0);
        $this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldNullable(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, true);
        $this->setFieldEnumValues(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldUnique(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldIndexed(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldFulltext(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldUnsigned(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, false);
		$this->setFieldEncryptionCypher(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP, TSysContactsAbstract::ENCRYPTION_BILL_POSTAL_PASSPHRASE);			                          


		//Billing: city
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGCITY, TSysContactsAbstract::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContactsAbstract::FIELD_BILLINGCITY, 50);
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_BILLINGCITY, false);
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_BILLINGCITY, false);

		//Billing: state/region
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGSTATEREGION, TSysContactsAbstract::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContactsAbstract::FIELD_BILLINGSTATEREGION, 50);
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_BILLINGSTATEREGION, false);
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_BILLINGSTATEREGION, false);

        //Billing: country id (from country table)
		$this->setFieldDefaultValue(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, '');
		$this->setFieldType(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, CT_INTEGER64);
		$this->setFieldLength(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, 0);
		$this->setFieldDecimalPrecision(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, 0);
		$this->setFieldPrimaryKey(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldNullable(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldEnumValues(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, null);
		$this->setFieldUnique(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldIndexed(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldFulltext(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldForeignKeyClass(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, TSysCountries::class);
		$this->setFieldForeignKeyTable(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, TSysCountries::getTable());
		$this->setFieldForeignKeyField(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldUnsigned(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID, true);
        $this->setFieldEncryptionDisabled(TSysContactsAbstract::FIELD_BILLINGCOUNTRYID);		

		//Billing: VAT number
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGVATNUMBER, TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_BILLINGVATNUMBER, TSysContactsAbstract::ENCRYPTION_BILL_VATNO_PASSPHRASE);			                          

        //Billing: 2-way encrypted email address
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSENCRYPTED, TSysContactsAbstract::FIELD_EMAILADDRESSENCRYPTED);

        //Billing: email fingerprint, so we can lookup the record based on email address
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSFINGERPRINT, TSysContactsAbstract::FIELD_EMAILADDRESSFINGERPRINT);
	
        //Billing: bank account number
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_BILLINGBANKACCOUNTNO, TSysContactsAbstract::FIELD_CHAMBEROFCOMMERCENO);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_BILLINGBANKACCOUNTNO, TSysContactsAbstract::ENCRYPTION_BILL_BANKACC_PASSPHRASE);			                          

		//Delivery: addressline1
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYADDRESSMISC, TSysContactsAbstract::FIELD_BILLINGADDRESSMISC);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_DELIVERYADDRESSMISC, TSysContactsAbstract::ENCRYPTION_DELI_ADDR1_PASSPHRASE);			                          

		//Delivery: addressline2
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYADDRESSSTREET, TSysContactsAbstract::FIELD_BILLINGADDRESSSTREET);
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_DELIVERYADDRESSSTREET, TSysContactsAbstract::ENCRYPTION_DELI_ADDR2_PASSPHRASE);			                          

		//Delivery: postal code / zip code
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, TSysContactsAbstract::FIELD_BILLINGPOSTALCODEZIP);
		$this->setFieldEncryptionCypher(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContactsAbstract::FIELD_DELIVERYPOSTALCODEZIP, TSysContactsAbstract::ENCRYPTION_DELI_POSTAL_PASSPHRASE);			                          
	
		//Delivery: city
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYCITY, TSysContactsAbstract::FIELD_BILLINGCITY);

		//Delivery: state/region
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYSTATEREGION, TSysContactsAbstract::FIELD_BILLINGSTATEREGION);

        //Delivery: country id (from country table)
		$this->setFieldCopyProps(TSysContactsAbstract::FIELD_DELIVERYCOUNTRYID, TSysContactsAbstract::FIELD_BILLINGCOUNTRYID);
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
		return array(TSysContactsAbstract::FIELD_ID, 
			TSysContactsAbstract::FIELD_CUSTOMID, 
			TSysContactsAbstract::FIELD_UNIQUEID, 
			TSysContactsAbstract::FIELD_NICEID,
			TSysContactsAbstract::FIELD_COMPANYNAME, 
			TSysContactsAbstract::FIELD_FIRSTNAMEINITALS, 
			TSysContactsAbstract::FIELD_NOTES, 
			TSysContactsAbstract::FIELD_BILLINGSTATEREGION,
			TSysContactsAbstract::FIELD_BILLINGCITY,
			TSysContactsAbstract::FIELD_DELIVERYSTATEREGION,
			TSysContactsAbstract::FIELD_DELIVERYCITY,
			TSysContactsAbstract::FIELD_SEARCHKEYWORDS
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
	 * use checkout for locking file for editing
	*/
	public function getTableUseCheckout()
	{
		return true;
	}
        
	/**
	 * use locking file for editing
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
	// public static function getTable()
	// {
	// 	return APP_DB_TABLEPREFIX.'SysLanguages';
	// }
	
	
	
	/**
	 * OVERRIDE BY CHILD CLASS
	 *
	 * Voor de gui functies (zoals het maken van comboboxen) vraagt deze functie op
	 * welke waarde er in het gui-element geplaatst moet worden, zoals de naam bijvoorbeeld
	 *
	 *
	 * return '??? - functie niet overschreven door child klasse';
	*/
	public function getDisplayRecordShort()
	{
		$sResult = '';
		$sCompany = '';
		$bCompanyExists = true;
		$sFirst = '';
		$bFirstExists = true;
		$sLast = '';
		$bLastExists = true;
		$sCity = '';
		$bCityExists = true;


		$sCompany =  $this->get(TSysContactsAbstract::FIELD_COMPANYNAME);
		$bCompanyExists = ($sCompany != '');
		$sFirst =  $this->get(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS);
		$bFirstExists = ($sFirst != '');
		$sLast =  $this->get(TSysContactsAbstract::FIELD_LASTNAME, '', true);
		$bLastExists = ($sLast != '');
		$sBillingCity =  $this->get(TSysContactsAbstract::FIELD_BILLINGCITY);
		$bBillingCityExists = ($sBillingCity != '');
		$sIdentifier =  $this->get(TSysContactsAbstract::FIELD_CUSTOMID);
		$bIndentifierExists = ($sIdentifier != '');		

		//start building the result
		$sResult.= $sIdentifier;

		if ($bCompanyExists)
		$sResult.= ' - '.$sCompany;

		if ($bFirstExists || $bLastExists)
		{
			if ($bCompanyExists)
				$sResult.= ' (';

			$sResult.= $sFirst;
			if ($bFirstExists && $bLastExists)//only add space when first and lastname exist
				$sResult.= ' ';

			$sResult.= $sLast;

			if ($bCompanyExists)
				$sResult.= ')';			
		}

		if ($bBillingCityExists)
		{
			$sResult.= ', '.$sBillingCity;			
		}

		// if ((!$bFirstExists) && (!$bLastExists) && (!$bCompanyExists) && (!$bBillingCityExists))
		// {
		// 	$sResult.= ' '.$this->get(TSysContactsAbstract::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', true);
		// }

		return $sResult;
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
		return 'fikiefleflop'.$this->get(TSysContactsAbstract::FIELD_COMPANYNAME).'isop'.$this->get(TSysContactsAbstract::FIELD_LASTNAME).'sdf4f'.$this->get(TSysContactsAbstract::FIELD_FIRSTNAMEINITALS).'ajwop'.$this->get(TSysContactsAbstract::FIELD_BILLINGCITY).'nonkietonk';
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
		return true;
	}	

	/**
	 * use a random string id that has no logically follow-up numbers
	 * 
	 * this is used to produce human readable identifiers
	 * @return bool
	 */
	public function getTableUseNiceID()
	{
		return true;
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
		return true;
	}			

	/**
	 * can a record be favorited by the user?
	 *
	 * @return bool
	 */
	public function getTableUseIsFavorite()
	{
		return true;
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
		return true;
	}	

	/**
	 * creates a search field out of the encrypted fields
	 * which fields are included are specified in the config file
	 * 
	 * @param string $sExistingValuesCSV values from a field. these values are separated with a comma (,) ==> a user can specify their own keywords
	 * @return string string with values separated by comma (,)
	 */
	public function generateSearchKeywordsField($sExistingValuesCSV = '')
	{
		//declare and init
		$arrExistingValues = array();
		$iCountExistingValues = 0;
		$arrFields = array();
		$iCountFields = 0;
		$arrKeywords = array();
		$mFieldValue = null;

		if ($sExistingValuesCSV !== '')
			$arrExistingValues = explode(',', $sExistingValuesCSV);
		$arrFields = explode(',', APP_DATAPROTECTION_CONTACTS_SEARCHFIELDS);

		//add existing values
		$iCountExistingValues = count($arrExistingValues);
		for ($iIndex = 0; $iIndex < $iCountExistingValues; ++$iIndex)
		{
			if ($arrExistingValues[$iIndex] !== '')
			{
				//sanitize
				$arrExistingValues[$iIndex] = collateString($arrExistingValues[$iIndex]);
				$arrExistingValues[$iIndex] = sanitizeWhitelist($arrExistingValues[$iIndex], WHITELIST_ALPHANUMERIC.' .@', true); //dot (.) and at (@) because of email addresses

				//add existing values
				$arrKeywords[] = trim(strtolower($arrExistingValues[$iIndex]));
			}
		}

		//add encrypted fields
		$iCountFields = count($arrFields);
		for ($iIndex = 0; $iIndex < $iCountFields; ++$iIndex)
		{
			$mFieldValue = $this->get($arrFields[$iIndex], '', true);//they are all encrypted fields

			if (($mFieldValue != '') && ($mFieldValue != null))
			{

				//correct phone numbers
				if (($arrFields[$iIndex] == TSysContacts::FIELD_PHONENUMBER1) || ($arrFields[$iIndex] == TSysContacts::FIELD_PHONENUMBER2))
				{
					if ($mFieldValue[0] != '0') //first character needs to be a zero
						$mFieldValue = '0'.$mFieldValue; //add zero
					$mFieldValue = sanitizeWhitelist($mFieldValue, WHITELIST_NUMERIC); //filters everything except numbers
				}
				elseif (($arrFields[$iIndex] == TSysContacts::FIELD_EMAILADDRESSENCRYPTED))
				{
					$mFieldValue = sanitizeEmailAddress(collateString($mFieldValue));
				}
				else //all other fields we didnt specify
				{
					$mFieldValue = sanitizeWhitelist($mFieldValue, WHITELIST_ALPHANUMERIC);
				}

				//add
				if (strlen($mFieldValue) > 2) //at least 3 characters (otherwise searching makes no sense)
					$arrKeywords[] = trim(strtolower(collateString($mFieldValue)));
			}
		}


		//filter duplicates
		$arrKeywords = array_unique($arrKeywords);

		return implode(',', $arrKeywords);
	}

}

?>