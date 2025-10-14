<?php
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Contacts\models;

use dr\classes\models\TSysModel;
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


class TSysContacts extends TSysModel
{
	const FIELD_ISCLIENT 						= 'bIsClient';
	const FIELD_ISSUPPLIER 						= 'bIsSupplier';
	const FIELD_ALLOWEDPURCHASEONCREDIT			= 'bAllowedPurchaseOnCredit';
	const FIELD_CUSTOMID 						= 'sCustomID'; //Custom identifier, anything that refers to a contact in a reasonably unique way: a postal code, the first 6 letters of the last name. This will be stored in plain text in database for searching purposes. Make sure it is not identifyable enough when the database gets breached
	const FIELD_COMPANYNAME 					= 'sCompanyName';
	const FIELD_SALUTATIONID 					= 'iSalutationID'; //how to address someone? sir, madam
	const FIELD_FIRSTNAMEINITALS 				= 'sFirstNameInitials'; //first name or initials
	const FIELD_LASTNAME 						= 'sLastName'; 
	const FIELD_LASTNAMEPREFIXID 				= 'sLastNamePrefixID';  //tussenvoegsel: Van Der,Van, Le, Von etc.
	const FIELD_EMAILADDRESSENCRYPTED   		= 'sEAE';//Email Address Encrypted internally stored in encrypted form - 2 way encrypted email address
	const FIELD_EMAILADDRESSFINGERPRINT 		= 'sEAF';//Fingerprint Email Address, so we can lookup a record based on email address. We can't salt this, because we need to be able to search on it in the database for password recovery
	const FIELD_ONMAILINGLIST					= 'bOnMailingList';
	const FIELD_ONBLACKLIST						= 'bOnBlackList';
	const FIELD_COUNTRYIDCODEPHONE1				= 'iCountryIDCodePhone1'; //country code of phone 1
	const FIELD_PHONENUMBER1					= 'sPhoneNumber1';
	const FIELD_PHONENUMBER1NOTE				= 'sPhoneNumber1Note';
	const FIELD_COUNTRYIDCODEPHONE2				= 'iCountryIDCodePhone2'; //country code of phone 2
	const FIELD_PHONENUMBER2					= 'sPhoneNumber2';
	const FIELD_PHONENUMBER2NOTE				= 'sPhoneNumber2Note';
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
	const FIELD_BILLINGBICSWIFT 				= 'sBICSWIFT';//bank BIC or SWIFT code

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




	public function getIsClient()
	{
		return $this->get(TSysContacts::FIELD_ISCLIENT);
	}

	public function setIsClient($bIsClient)
	{
		$this->set(TSysContacts::FIELD_ISCLIENT, $bIsClient);
	}

	public function getIsSupplier()
	{
		return $this->get(TSysContacts::FIELD_ISSUPPLIER);
	}

	public function setIsSupplier($bIsSupplier)
	{
		$this->set(TSysContacts::FIELD_ISSUPPLIER, $bIsSupplier);
	}
		

	public function getAllowedPurchaseOnCredit()
	{
		return $this->get(TSysContacts::FIELD_ALLOWEDPURCHASEONCREDIT);
	}

	public function setAllowedPurchaseOnCredit($bAllowed)
	{
		$this->set(TSysContacts::FIELD_ALLOWEDPURCHASEONCREDIT, $bAllowed);
	}
		
	public function getCustomIdentifier()
	{
		return $this->get(TSysContacts::FIELD_CUSTOMID);
	}

	public function setCustomIdentifier($sRefNo)
	{
		$this->set(TSysContacts::FIELD_CUSTOMID, $sRefNo);
	}

	public function getCompanyName()
	{
		return $this->get(TSysContacts::FIELD_COMPANYNAME);
	}

	public function setCompanyName($sCompanyName)
	{
		$this->set(TSysContacts::FIELD_COMPANYNAME, $sCompanyName);
	}

	public function getSalutationID()
	{
		return $this->get(TSysContacts::FIELD_SALUTATIONID);
	}

	public function setSalutationID($iID)
	{
		$this->set(TSysContacts::FIELD_SALUTATIONID, $iID);
	}

	public function getFirstNameInitials()
	{
		return $this->get(TSysContacts::FIELD_FIRSTNAMEINITALS);
	}

	public function setFirstNameInitials($sFirstName)
	{
		$this->set(TSysContacts::FIELD_FIRSTNAMEINITALS, $sFirstName);
	}


	public function getLastName()
	{
		return $this->get(TSysContacts::FIELD_LASTNAME, '', true);
	}

	public function setLastName($sLastName)
	{
		$this->set(TSysContacts::FIELD_LASTNAME, $sLastName, '', true);
	}

	public function getLastNamePrefixID()
	{
		return $this->get(TSysContacts::FIELD_LASTNAMEPREFIXID);
	}

	public function setLastNamePrefixID($iID)
	{
		$this->set(TSysContacts::FIELD_LASTNAMEPREFIXID, $iID);
	}


    /**
     * get email address and decrypt it
     * 
     * @return string
     */
    public function getEmailAddressDecrypted()
    {
        return $this->get(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, '', true);
    }

    /**
     * encrypts and sets email address AND email identifier
     * 
     * @param string $sEmail
     */
    public function setEmailAddressDecrypted($sUncryptedEmail)
    {
        $this->set(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, $sUncryptedEmail, '', true);
        $this->set(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, getFingerprintEmail($sUncryptedEmail, TSysContacts::SEED_EMAILADDRESSFINGERPRINT, TSysContacts::DIGEST_EMAILADDRESSFINGERPRINT));
    }

	public function getOnMailingList()
	{
		return $this->get(TSysContacts::FIELD_ONMAILINGLIST);
	}

	public function setOnMailingList($bValue)
	{
		$this->set(TSysContacts::FIELD_ONMAILINGLIST, $bValue);
	}

	public function getOnBlackList()
	{
		return $this->get(TSysContacts::FIELD_ONBLACKLIST);
	}

	public function setOnBlackList($bValue)
	{
		$this->set(TSysContacts::FIELD_ONBLACKLIST, $bValue);
	}	

	public function getCountryIDCodePhoneNumber1()
	{
		return $this->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE1);
	}

	public function setCountryIDCodePhoneNumber1($iCountryID)
	{
		$this->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, $iCountryID);
	}		


	public function getPhoneNumber1()
	{
		return $this->get(TSysContacts::FIELD_PHONENUMBER1);
	}

	public function setPhoneNumber1($sPhone)
	{
		$this->set(TSysContacts::FIELD_PHONENUMBER1, $sPhone);
	}	

	public function getPhoneNumber1Note()
	{
		return $this->get(TSysContacts::FIELD_PHONENUMBER1NOTE);
	}

	public function setPhoneNumber1Note($sNote)
	{
		$this->set(TSysContacts::FIELD_PHONENUMBER1NOTE, $sNote);
	}		

	public function getCountryIDCodePhoneNumber2()
	{
		return $this->get(TSysContacts::FIELD_COUNTRYIDCODEPHONE2);
	}

	public function setCountryIDCodePhoneNumber2($iCountryID)
	{
		$this->set(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, $iCountryID);
	}	

	public function getPhoneNumber2()
	{
		return $this->get(TSysContacts::FIELD_PHONENUMBER2);
	}

	public function setPhoneNumber2($sPhone)
	{
		$this->set(TSysContacts::FIELD_PHONENUMBER2, $sPhone);
	}	

	public function getPhoneNumber2Note()
	{
		return $this->get(TSysContacts::FIELD_PHONENUMBER2NOTE);
	}

	public function setPhoneNumber2Note($sNote)
	{
		$this->set(TSysContacts::FIELD_PHONENUMBER2NOTE, $sNote);
	}		

	public function getChamberOfCommerceNoDecrypted()
	{
		return $this->get(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, '', true);
	}

	public function setChamberCommerceNoEncrypted($sNO)
	{
		$this->set(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, $sNO, '', true);
	}		

	public function getNotes()
	{
		return $this->get(TSysContacts::FIELD_NOTES);
	}

	public function setNotes($sNotes)
	{
		$this->set(TSysContacts::FIELD_NOTES, $sNotes);
	}


	public function getFirstContact()
	{
		return $this->get(TSysContacts::FIELD_FIRSTCONTACT);
	}

	public function setFirstContact($objDate)
	{
		$this->set(TSysContacts::FIELD_FIRSTCONTACT, $objDate);
	}

	public function getLastContact()
	{
		return $this->get(TSysContacts::FIELD_FIRSTCONTACT);
	}

	public function setLastContact($objDate)
	{
		$this->set(TSysContacts::FIELD_LASTCONTACT, $objDate);
	}


	public function getBillingAddressMisc()
	{
		return $this->get(TSysContacts::FIELD_BILLINGADDRESSMISC, '', true);
	}

	public function setBillingAddressMisc($sLine1)
	{
		$this->set(TSysContacts::FIELD_BILLINGADDRESSMISC, $sLine1, '', true);
	}

	
	public function getBillingAddressStreet()
	{
		return $this->get(TSysContacts::FIELD_BILLINGADDRESSSTREET, '', true);
	}

	public function setBillingAddressStreet($sLine2)
	{
		$this->set(TSysContacts::FIELD_BILLINGADDRESSSTREET, $sLine2, '', true);
	}

	public function getBillingPostalCodeZip()
	{
		return $this->get(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, '', true);
	}

	public function setBillingPostalCodeZip($sZip)
	{
		$this->set(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, $sZip, '', true);
	}

	public function getBillingCity()
	{
		return $this->get(TSysContacts::FIELD_BILLINGCITY);
	}

	public function setBillingCity($sCity)
	{
		$this->set(TSysContacts::FIELD_BILLINGCITY, $sCity);
	}

	public function getBillingStateRegion()
	{
		return $this->get(TSysContacts::FIELD_BILLINGSTATEREGION);
	}

	public function setBillingStateRegion($sRegion)
	{
		$this->set(TSysContacts::FIELD_BILLINGSTATEREGION, $sRegion);
	}

	public function getBillingCountryID()
	{
		return $this->get(TSysContacts::FIELD_BILLINGCOUNTRYID);
	}

	public function setBillingCountryID($iCountryID)
	{
		$this->set(TSysContacts::FIELD_BILLINGCOUNTRYID, $iCountryID);
	}

	public function getBillingVATNumberDecrypted()
	{
		return $this->get(TSysContacts::FIELD_BILLINGVATNUMBER, '', true);
	}

	public function setBillingVATNumberEncrypted($sVATNumber)
	{
		$this->set(TSysContacts::FIELD_BILLINGVATNUMBER, $sVATNumber, '', true);
	}	

    /**
     * get email address and decrypt it
     * 
     * @return string
     */
    public function getBillingEmailAddressDecrypted()
    {
        return $this->get(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', true);
    }

    /**
     * encrypts and sets email address AND email identifier
     * 
     * @param string $sEmail
     */
    public function setBillingEmailAddressDecrypted($sUncryptedEmail)
    {
        $this->set(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, $sUncryptedEmail, '', true);
        $this->set(TSysContacts::FIELD_BILLINGEMAILADDRESSFINGERPRINT, getFingerprintEmail($sUncryptedEmail, TSysContacts::SEED_BILLINGEMAILADDRESSFINGERPRINT, TSysContacts::DIGEST_BILLINGEMAILADDRESSFINGERPRINT));
    }

    /**
     * get bank account number and decrypt it
     * 
     * @return string
     */
    public function getBillingBankAccountNoDecrypted()
    {
        return $this->get(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, '', true);
    }

    /**
     * encrypts and sets bank account number
     * 
     * @param string $sUncryptedBankAccountNo
     */
    public function setBillingBankAccountNoDecrypted($sUncryptedBankAccountNo)
    {
        $this->set(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, $sUncryptedBankAccountNo, '', true);
    }	

	public function getBillingBICSWIFT()
	{
		return $this->get(TSysContacts::FIELD_BILLINGBICSWIFT);
	}

	public function setBillingBICSWIFT($sBIC)
	{
		$this->set(TSysContacts::FIELD_BILLINGBICSWIFT, $sBIC);
	}

	public function getDeliveryAddressMisc()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYADDRESSMISC, '', true);
	}

	public function setDeliveryAddressMisc($sLine1)
	{
		$this->set(TSysContacts::FIELD_DELIVERYADDRESSMISC, $sLine1, '', true);
	}

	
	public function getDeliveryAddressStreet()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYADDRESSSTREET, '', true);
	}

	public function setDeliveryAddressStreet($sLine2)
	{
		$this->set(TSysContacts::FIELD_DELIVERYADDRESSSTREET, $sLine2, '', true);
	}

	public function getDeliveryPostalCodeZip()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, '', true);
	}

	public function setDeliveryPostalCodeZip($sZip)
	{
		$this->set(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, $sZip, '', true);
	}

	public function getDeliveryCity()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYCITY);
	}

	public function setDeliveryCity($sCity)
	{
		$this->set(TSysContacts::FIELD_DELIVERYCITY, $sCity);
	}

	public function getDeliveryStateRegion()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYSTATEREGION);
	}

	public function setDeliveryStateRegion($sRegion)
	{
		$this->set(TSysContacts::FIELD_DELIVERYSTATEREGION, $sRegion);
	}

	public function getDeliveryCountryID()
	{
		return $this->get(TSysContacts::FIELD_DELIVERYCOUNTRYID);
	}

	public function setDeliveryCountryID($iCountryID)
	{
		$this->set(TSysContacts::FIELD_DELIVERYCOUNTRYID, $iCountryID);
	}




	

	/**
	 * this function creates table in database and calls all foreign key classes to do the same
	 * 
	 * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
	 *
	 * @param array $arrPreviousDependenciesModelClasses with classnames. 
	 * @return bool success?
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		
		if ($bSuccess)
		{
			$this->limitOne();
			$this->loadFromDB(false);
			if ($this->count() == 0) //only add when table is empty
			{
				$this->createContactDefaultsDB();
				
				$this->setIsDefault(true);
				$this->setIsFavorite(true);
				$this->setCustomIdentifier(TSysContacts::VALUE_DEFAULT);
				$this->setIsClient(true);
				$this->setIsSupplier(true);
				$this->setLastName(TSysContacts::VALUE_DEFAULT);
				$this->setFirstNameInitials(TSysContacts::VALUE_DEFAULT);
				$this->setCompanyName(TSysContacts::VALUE_DEFAULT);
				$this->setBillingCity(TSysContacts::VALUE_DEFAULT);
				$this->setDeliveryCity(TSysContacts::VALUE_DEFAULT);
				
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving default contact on install TSysContacts');

			}
		}
		
		return $bSuccess;
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
		//is client
		$this->setFieldDefaultValue(TSysContacts::FIELD_ISCLIENT, false);
		$this->setFieldType(TSysContacts::FIELD_ISCLIENT, CT_BOOL);
		$this->setFieldLength(TSysContacts::FIELD_ISCLIENT, 0);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_ISCLIENT, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_ISCLIENT, false);
		$this->setFieldNullable(TSysContacts::FIELD_ISCLIENT, false);
		$this->setFieldEnumValues(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldUnique(TSysContacts::FIELD_ISCLIENT, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContacts::FIELD_ISCLIENT, false); 
		$this->setFieldFulltext(TSysContacts::FIELD_ISCLIENT, false); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_ISCLIENT, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_ISCLIENT, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_ISCLIENT, true);
        $this->setFieldEncryptionDisabled(TSysContacts::FIELD_ISCLIENT);		


		//is supplier
		$this->setFieldCopyProps(TSysContacts::FIELD_ISSUPPLIER, TSysContacts::FIELD_ISCLIENT);

		//allowed purchase on credit
		$this->setFieldDefaultsBoolean(TSysContacts::FIELD_ALLOWEDPURCHASEONCREDIT);


		//custom identifier
		$this->setFieldDefaultValue(TSysContacts::FIELD_CUSTOMID, '');
		$this->setFieldType(TSysContacts::FIELD_CUSTOMID, CT_VARCHAR);
		$this->setFieldLength(TSysContacts::FIELD_CUSTOMID, 50);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_CUSTOMID, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_CUSTOMID, false);
		$this->setFieldNullable(TSysContacts::FIELD_CUSTOMID, true);
		$this->setFieldEnumValues(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldUnique(TSysContacts::FIELD_CUSTOMID, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContacts::FIELD_CUSTOMID, true); 
		$this->setFieldFulltext(TSysContacts::FIELD_CUSTOMID, true); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_CUSTOMID, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_CUSTOMID, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_CUSTOMID, false);
        $this->setFieldEncryptionDisabled(TSysContacts::FIELD_CUSTOMID);		


		//company name
		$this->setFieldDefaultValue(TSysContacts::FIELD_COMPANYNAME, '');
		$this->setFieldType(TSysContacts::FIELD_COMPANYNAME, CT_VARCHAR);
		$this->setFieldLength(TSysContacts::FIELD_COMPANYNAME, 100);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_COMPANYNAME, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_COMPANYNAME, false);
		$this->setFieldNullable(TSysContacts::FIELD_COMPANYNAME, true);
		$this->setFieldEnumValues(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldUnique(TSysContacts::FIELD_COMPANYNAME, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContacts::FIELD_COMPANYNAME, true); 
		$this->setFieldFulltext(TSysContacts::FIELD_COMPANYNAME, true); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_COMPANYNAME, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_COMPANYNAME, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_COMPANYNAME, false);
        $this->setFieldEncryptionDisabled(TSysContacts::FIELD_COMPANYNAME);		


		//salutation
		$this->setFieldDefaultsIntegerForeignKey(TSysContacts::FIELD_SALUTATIONID, TSysContactsSalutations::class, TSysContactsSalutations::getTable(), TSysContactsSalutations::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_SALUTATIONID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_SALUTATIONID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);


		//first name and initials
		$this->setFieldCopyProps(TSysContacts::FIELD_FIRSTNAMEINITALS, TSysContacts::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContacts::FIELD_FIRSTNAMEINITALS, 50);
		$this->setFieldIndexed(TSysContacts::FIELD_FIRSTNAMEINITALS, false);
		$this->setFieldFulltext(TSysContacts::FIELD_FIRSTNAMEINITALS, false);

		//last name
		$this->setFieldDefaultValue(TSysContacts::FIELD_LASTNAME, '');
		$this->setFieldType(TSysContacts::FIELD_LASTNAME, CT_LONGTEXT);
		$this->setFieldLength(TSysContacts::FIELD_LASTNAME, 0);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_LASTNAME, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_LASTNAME, false);
		$this->setFieldNullable(TSysContacts::FIELD_LASTNAME, true);
		$this->setFieldEnumValues(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldUnique(TSysContacts::FIELD_LASTNAME, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContacts::FIELD_LASTNAME, false); 
		$this->setFieldFulltext(TSysContacts::FIELD_LASTNAME, false); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_LASTNAME, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_LASTNAME, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_LASTNAME, false);
		$this->setFieldEncryptionCypher(TSysContacts::FIELD_LASTNAME, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContacts::FIELD_LASTNAME, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_LASTNAME, TSysContacts::ENCRYPTION_LASTNAME_PASSPHRASE);			                          
		
		//last name prefix
		$this->setFieldDefaultsIntegerForeignKey(TSysContacts::FIELD_LASTNAMEPREFIXID, TSysContactsLastNamePrefixes::class, TSysContactsLastNamePrefixes::getTable(), TSysContactsLastNamePrefixes::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_LASTNAMEPREFIXID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_LASTNAMEPREFIXID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);


        //2-way encrypted email address
        $this->setFieldDefaultValue(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, '');
        $this->setFieldType(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, CT_LONGTEXT);
        $this->setFieldLength(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldDecimalPrecision(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, 0);
        $this->setFieldPrimaryKey(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldNullable(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, true);
        $this->setFieldEnumValues(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldUnique(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldIndexed(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldFulltext(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldForeignKeyClass(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyTable(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyField(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyJoin(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, null);
        $this->setFieldAutoIncrement(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
        $this->setFieldUnsigned(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, false);
		$this->setFieldEncryptionCypher(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_EMAILADDRESSENCRYPTED, TSysContacts::ENCRYPTION_EMAIL_PASSPHRASE);			                          

        //email fingerprint, so we can lookup the record based on email address
        $this->setFieldDefaultValue(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, '');
        $this->setFieldType(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, CT_VARCHAR);
        $this->setFieldLength(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, 255);
        $this->setFieldDecimalPrecision(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, 0);
        $this->setFieldPrimaryKey(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldNullable(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldEnumValues(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldUnique(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldIndexed(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, true);
        $this->setFieldFulltext(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldForeignKeyClass(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyTable(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyField(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyJoin(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, null);
        $this->setFieldAutoIncrement(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, false);
        $this->setFieldUnsigned(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT, false);
		$this->setFieldEncryptionDisabled(TSysContacts::FIELD_EMAILADDRESSFINGERPRINT);	

		//on mailing list
		$this->setFieldDefaultValue(TSysContacts::FIELD_ONMAILINGLIST, false);
		$this->setFieldType(TSysContacts::FIELD_ONMAILINGLIST, CT_BOOL);
		$this->setFieldLength(TSysContacts::FIELD_ONMAILINGLIST, 0);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_ONMAILINGLIST, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_ONMAILINGLIST, false);
		$this->setFieldNullable(TSysContacts::FIELD_ONMAILINGLIST, false);
		$this->setFieldEnumValues(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldUnique(TSysContacts::FIELD_ONMAILINGLIST, false); 
		$this->setFieldIndexed(TSysContacts::FIELD_ONMAILINGLIST, false); 
		$this->setFieldFulltext(TSysContacts::FIELD_ONMAILINGLIST, false); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_ONMAILINGLIST, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_ONMAILINGLIST, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_ONMAILINGLIST, true);
        $this->setFieldEncryptionDisabled(TSysContacts::FIELD_ONMAILINGLIST);	

		//on blacklist
		$this->setFieldCopyProps(TSysContacts::FIELD_ONBLACKLIST, TSysContacts::FIELD_ONMAILINGLIST);

		//countryid countrycode phone 1
		$this->setFieldDefaultsIntegerForeignKey(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, TSysCountries::class, TSysCountries::getTable(), TSysCountries::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_COUNTRYIDCODEPHONE1, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//phone1
        $this->setFieldDefaultValue(TSysContacts::FIELD_PHONENUMBER1, '');
        $this->setFieldType(TSysContacts::FIELD_PHONENUMBER1, CT_LONGTEXT);
        $this->setFieldLength(TSysContacts::FIELD_PHONENUMBER1, 0);
        $this->setFieldDecimalPrecision(TSysContacts::FIELD_PHONENUMBER1, 0);
        $this->setFieldPrimaryKey(TSysContacts::FIELD_PHONENUMBER1, false);
        $this->setFieldNullable(TSysContacts::FIELD_PHONENUMBER1, true);
        $this->setFieldEnumValues(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldUnique(TSysContacts::FIELD_PHONENUMBER1, false);
        $this->setFieldIndexed(TSysContacts::FIELD_PHONENUMBER1, false);
        $this->setFieldFulltext(TSysContacts::FIELD_PHONENUMBER1, false);
        $this->setFieldForeignKeyClass(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyTable(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyField(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyJoin(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_PHONENUMBER1, null);
        $this->setFieldAutoIncrement(TSysContacts::FIELD_PHONENUMBER1, false);
        $this->setFieldUnsigned(TSysContacts::FIELD_PHONENUMBER1, false);
		$this->setFieldEncryptionCypher(TSysContacts::FIELD_PHONENUMBER1, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContacts::FIELD_PHONENUMBER1, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_PHONENUMBER1, TSysContacts::ENCRYPTION_PHONE1_PASSPHRASE);	

		//phone 1 note
		$this->setFieldDefaultsVarChar(TSysContacts::FIELD_PHONENUMBER1NOTE, 50);

		//countryid countrycode phone 2
		$this->setFieldDefaultsIntegerForeignKey(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, TSysCountries::class, TSysCountries::getTable(), TSysCountries::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_COUNTRYIDCODEPHONE2, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//phone2
		$this->setFieldCopyProps(TSysContacts::FIELD_PHONENUMBER2, TSysContacts::FIELD_PHONENUMBER1);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_PHONENUMBER2, TSysContacts::ENCRYPTION_PHONE2_PASSPHRASE);	

		//phone 2 note
		$this->setFieldDefaultsVarChar(TSysContacts::FIELD_PHONENUMBER2NOTE, 50);


       	//2-way encrypted chamber of commerce number
	   	$this->setFieldCopyProps(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, TSysContacts::FIELD_PHONENUMBER1);
	   	$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_CHAMBEROFCOMMERCENO, TSysContacts::ENCRYPTION_CHAMBERCOMMERCE_PASSPHRASE);			                          


		//notes
		$this->setFieldCopyProps(TSysContacts::FIELD_NOTES, TSysContacts::FIELD_COMPANYNAME);
		$this->setFieldType(TSysContacts::FIELD_NOTES, CT_LONGTEXT);
		$this->setFieldLength(TSysContacts::FIELD_NOTES, 0);
		$this->setFieldIndexed(TSysContacts::FIELD_NOTES, false);
		$this->setFieldFulltext(TSysContacts::FIELD_NOTES, true);


		//first contact date
		$this->setFieldDefaultValue(TSysContacts::FIELD_FIRSTCONTACT, '');
		$this->setFieldType(TSysContacts::FIELD_FIRSTCONTACT, CT_DATETIME);
		$this->setFieldLength(TSysContacts::FIELD_FIRSTCONTACT, 0);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_FIRSTCONTACT, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_FIRSTCONTACT, false);
		$this->setFieldNullable(TSysContacts::FIELD_FIRSTCONTACT, true);
		$this->setFieldEnumValues(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldUnique(TSysContacts::FIELD_FIRSTCONTACT, false); //it is annoying when you dont fill it in for 2 customers, you get an error
		$this->setFieldIndexed(TSysContacts::FIELD_FIRSTCONTACT, false); 
		$this->setFieldFulltext(TSysContacts::FIELD_FIRSTCONTACT, false); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyField(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_FIRSTCONTACT, null);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_FIRSTCONTACT, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_FIRSTCONTACT, false);
		$this->setFieldEncryptionDisabled(TSysContacts::FIELD_FIRSTCONTACT);	

		//last contact date
		$this->setFieldCopyProps(TSysContacts::FIELD_LASTCONTACT, TSysContacts::FIELD_FIRSTCONTACT);


		//Billing: addressline1
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGADDRESSMISC, TSysContacts::FIELD_LASTNAME);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_BILLINGADDRESSMISC, TSysContacts::ENCRYPTION_BILL_ADDR1_PASSPHRASE);			                          

		//Billing: addressline2
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGADDRESSSTREET, TSysContacts::FIELD_LASTNAME);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_BILLINGADDRESSSTREET, TSysContacts::ENCRYPTION_BILL_ADDR2_PASSPHRASE);			                          

		//Billing: postal code / zip code
		// $this->setFieldCopyProps(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, TSysContacts::FIELD_LASTNAME);
        $this->setFieldDefaultValue(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, '');
        $this->setFieldType(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, CT_LONGTEXT);
        $this->setFieldLength(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, 0);
        $this->setFieldDecimalPrecision(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, 0);
        $this->setFieldPrimaryKey(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldNullable(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, true);
        $this->setFieldEnumValues(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldUnique(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldIndexed(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldFulltext(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldForeignKeyClass(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyTable(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyField(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyJoin(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, null);
        $this->setFieldAutoIncrement(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
        $this->setFieldUnsigned(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, false);
		$this->setFieldEncryptionCypher(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_BILLINGPOSTALCODEZIP, TSysContacts::ENCRYPTION_BILL_POSTAL_PASSPHRASE);			                          


		//Billing: city
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGCITY, TSysContacts::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContacts::FIELD_BILLINGCITY, 50);
		$this->setFieldIndexed(TSysContacts::FIELD_BILLINGCITY, false);
		$this->setFieldFulltext(TSysContacts::FIELD_BILLINGCITY, false);

		//Billing: state/region
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGSTATEREGION, TSysContacts::FIELD_COMPANYNAME);
		$this->setFieldLength(TSysContacts::FIELD_BILLINGSTATEREGION, 50);
		$this->setFieldIndexed(TSysContacts::FIELD_BILLINGSTATEREGION, false);
		$this->setFieldFulltext(TSysContacts::FIELD_BILLINGSTATEREGION, false);

        //Billing: country id (from country table)
		$this->setFieldDefaultValue(TSysContacts::FIELD_BILLINGCOUNTRYID, '');
		$this->setFieldType(TSysContacts::FIELD_BILLINGCOUNTRYID, CT_INTEGER64);
		$this->setFieldLength(TSysContacts::FIELD_BILLINGCOUNTRYID, 0);
		$this->setFieldDecimalPrecision(TSysContacts::FIELD_BILLINGCOUNTRYID, 0);
		$this->setFieldPrimaryKey(TSysContacts::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldNullable(TSysContacts::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldEnumValues(TSysContacts::FIELD_BILLINGCOUNTRYID, null);
		$this->setFieldUnique(TSysContacts::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldIndexed(TSysContacts::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldFulltext(TSysContacts::FIELD_BILLINGCOUNTRYID, false); 
		$this->setFieldForeignKeyClass(TSysContacts::FIELD_BILLINGCOUNTRYID, TSysCountries::class);
		$this->setFieldForeignKeyTable(TSysContacts::FIELD_BILLINGCOUNTRYID, TSysCountries::getTable());
		$this->setFieldForeignKeyField(TSysContacts::FIELD_BILLINGCOUNTRYID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TSysContacts::FIELD_BILLINGCOUNTRYID);
		$this->setFieldForeignKeyActionOnUpdate(TSysContacts::FIELD_BILLINGCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysContacts::FIELD_BILLINGCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TSysContacts::FIELD_BILLINGCOUNTRYID, false);
		$this->setFieldUnsigned(TSysContacts::FIELD_BILLINGCOUNTRYID, true);
        $this->setFieldEncryptionDisabled(TSysContacts::FIELD_BILLINGCOUNTRYID);		

		//Billing: VAT number
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGVATNUMBER, TSysContacts::FIELD_BILLINGADDRESSSTREET);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_BILLINGVATNUMBER, TSysContacts::ENCRYPTION_BILL_VATNO_PASSPHRASE);			                          

        //Billing: 2-way encrypted email address
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, TSysContacts::FIELD_EMAILADDRESSENCRYPTED);

        //Billing: email fingerprint, so we can lookup the record based on email address
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGEMAILADDRESSFINGERPRINT, TSysContacts::FIELD_EMAILADDRESSFINGERPRINT);
	
        //Billing: bank account number
		$this->setFieldCopyProps(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, TSysContacts::FIELD_CHAMBEROFCOMMERCENO);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_BILLINGBANKACCOUNTNO, TSysContacts::ENCRYPTION_BILL_BANKACC_PASSPHRASE);			                          

		///billing: bic
		$this->setFieldDefaultsVarChar(TSysContacts::FIELD_BILLINGBICSWIFT, 20);

		//Delivery: addressline1
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYADDRESSMISC, TSysContacts::FIELD_BILLINGADDRESSMISC);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_DELIVERYADDRESSMISC, TSysContacts::ENCRYPTION_DELI_ADDR1_PASSPHRASE);			                          

		//Delivery: addressline2
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYADDRESSSTREET, TSysContacts::FIELD_BILLINGADDRESSSTREET);
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_DELIVERYADDRESSSTREET, TSysContacts::ENCRYPTION_DELI_ADDR2_PASSPHRASE);			                          

		//Delivery: postal code / zip code
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, TSysContacts::FIELD_BILLINGPOSTALCODEZIP);
		$this->setFieldEncryptionCypher(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, ENCRYPTION_CYPHERMETHOD_AES256CBC);			                          
		$this->setFieldEncryptionDigest(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, ENCRYPTION_DIGESTALGORITHM_SHA512);			                          
		$this->setFieldEncryptionPassphrase(TSysContacts::FIELD_DELIVERYPOSTALCODEZIP, TSysContacts::ENCRYPTION_DELI_POSTAL_PASSPHRASE);			                          
	
		//Delivery: city
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYCITY, TSysContacts::FIELD_BILLINGCITY);

		//Delivery: state/region
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYSTATEREGION, TSysContacts::FIELD_BILLINGSTATEREGION);

        //Delivery: country id (from country table)
		$this->setFieldCopyProps(TSysContacts::FIELD_DELIVERYCOUNTRYID, TSysContacts::FIELD_BILLINGCOUNTRYID);
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
		return array(TSysContacts::FIELD_ID, 
			TSysContacts::FIELD_CUSTOMID, 
			TSysContacts::FIELD_UNIQUEID, 
			TSysContacts::FIELD_NICEID,
			TSysContacts::FIELD_COMPANYNAME, 
			TSysContacts::FIELD_FIRSTNAMEINITALS, 
			TSysContacts::FIELD_NOTES, 
			TSysContacts::FIELD_BILLINGSTATEREGION,
			TSysContacts::FIELD_BILLINGCITY,
			TSysContacts::FIELD_DELIVERYSTATEREGION,
			TSysContacts::FIELD_DELIVERYCITY,
			TSysContacts::FIELD_SEARCHKEYWORDS
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
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysContacts';
	}
	
	
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


		$sCompany =  $this->get(TSysContacts::FIELD_COMPANYNAME);
		$bCompanyExists = ($sCompany != '');
		$sFirst =  $this->get(TSysContacts::FIELD_FIRSTNAMEINITALS);
		$bFirstExists = ($sFirst != '');
		$sLast =  $this->get(TSysContacts::FIELD_LASTNAME, '', true);
		$bLastExists = ($sLast != '');
		$sBillingCity =  $this->get(TSysContacts::FIELD_BILLINGCITY);
		$bBillingCityExists = ($sBillingCity != '');
		$sIdentifier =  $this->get(TSysContacts::FIELD_CUSTOMID);
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
		// 	$sResult.= ' '.$this->get(TSysContacts::FIELD_BILLINGEMAILADDRESSENCRYPTED, '', true);
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
		return 'fikiefleflop'.$this->get(TSysContacts::FIELD_COMPANYNAME).'isop'.$this->get(TSysContacts::FIELD_LASTNAME).'sdf4f'.$this->get(TSysContacts::FIELD_FIRSTNAMEINITALS).'ajwop'.$this->get(TSysContacts::FIELD_BILLINGCITY).'nonkietonk';
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
	 * creates a record in memory (NOT IN DATABASE!) with default values for country, last-name prefixes and salutations
	 * 
	 * this makes it easier to quickly create a contact without having to worry about all the external tables
	 * 
	 * NOTE: this method consults the database to find defaults for external tables
	 * 
	 * @return bool true=success, false=error
	 */
	public function createContactDefaultsDB()
	{
		$this->clear(true);

		//countries
		$objCountries = new TSysCountries();
		if ($objCountries->loadFromDBByIsDefault())
		{
			$this->setCountryIDCodePhoneNumber1($objCountries->getID());
			$this->setCountryIDCodePhoneNumber2($objCountries->getID());
			$this->setDeliveryCountryID($objCountries->getID());
			$this->setBillingCountryID($objCountries->getID());
		}
		else
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'load failed: countries default');
			return false;
		}


		//salutations
		$objSalutations = new TSysContactsSalutations();
		if ($objSalutations->loadFromDBByIsDefault())
			$this->setSalutationID($objSalutations->getID());
		else
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'load failed: salutation default');
			return false;
		}

		//last name prefixes
		$objPrefix = new TSysContactsLastNamePrefixes();
		if ($objPrefix->loadFromDBByIsDefault())
			$this->setLastNamePrefixID($objPrefix->getID());
		else
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'load failed: last-name-prefixes default');
			return false;
		}

		return true;
	}


	/**
	 * create demo contacts in database for DEMO MODE
	 * this clears the current records in memory and adds the new ones
	 * This function is called in Mod_sys_CMSUsers
	 * 
	 * NOTE: uses function: createContactDefaultsDB() to start with defaults
	 */
	public function resetDemoContactsDB()
	{
		$this->createContactDefaultsDB();

		//@todo cleanen existing records, deleten toegevoegde records door users

		$this->createContactQuickDB('Hendrix Billboards', 'Johny', 'Hendrix', 'Churchilstreet 15', '223512', 'Fantasy Town', '123456789', $this->getBillingCountryID());
		$this->createContactQuickDB('Bills cars', 'Coiny', 'Bill', 'Paperstreet 12', '225623', 'Fantasy Town', '911', $this->getBillingCountryID());
		$this->createContactQuickDB('De flappentapper', 'Geld', 'Wolf', 'Reineard den Vos plantsoen 9', '225623', 'Dieren', '06-784043743', $this->getBillingCountryID());
	}

	/**
	 * creates a new record in the database and in memory
	 */
	public function createContactQuickDB($sCompany, $sFirstName, $sLastName, $sStreet, $sPostalCode, $sCity, $sTelephone, $iCountryID)
	{
		$bSuccess = true;

        if (!$this->recordExistsTableDB(TSysContacts::FIELD_LASTNAME, $sLastName)) //@todo --> will not work becuase field is encrypted
        {    
			$this->setCustomIdentifier($sCompany.'('.$sLastName.')');
			$this->setCompanyName($sCompany);
			$this->getFirstNameInitials($sFirstName);
			$this->setLastName($sLastName);
			$this->setDeliveryAddressStreet($sStreet);
			$this->setBillingAddressStreet($sStreet);
			$this->setDeliveryPostalCodeZip($sPostalCode);
			$this->setBillingPostalCodeZip($sPostalCode);
			$this->setDeliveryCity($sCity);
			$this->setBillingCity($sCity);
			$this->setCountryIDCodePhoneNumber1($iCountryID);
			$this->setPhoneNumber1($sTelephone);
			$this->setCountryIDCodePhoneNumber2($iCountryID);
			$this->setDeliveryCountryID($iCountryID);
			$this->setBillingCountryID($iCountryID);

            if (!$this->saveToDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': saving new contact '.$sLastName);
                $bSuccess = false;
            }                  
        }
        else //load organisation, we need it later
        {
            $this->find(TSysContacts::FIELD_LASTNAME, $sLastName);
            $this->limitOne();
            if (!$this->loadFromDB())
            {
                logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loading contact failed for name: '.$sLastName);
                return true;//EXIT, because we need a role id to create a new user
            }                   
        } 

		return $bSuccess;
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


	/**
	 * overwrites all personal identifyable information
	 * Used for GDPR
	 */
	public function anonymizeData()
	{
		$this->setFirstNameInitials($this::VALUE_ANONYMOUS);
		$this->setLastName($this::VALUE_ANONYMOUS);
		$this->setCompanyName($this::VALUE_ANONYMOUS);
		$this->setChamberCommerceNoEncrypted($this::VALUE_ANONYMOUS);
		$this->setEmailAddressDecrypted($this::VALUE_ANONYMOUS);
		$this->setPhoneNumber1($this::VALUE_ANONYMOUS);
		$this->setPhoneNumber2($this::VALUE_ANONYMOUS);

		$this->setBillingAddressStreet($this::VALUE_ANONYMOUS);
		$this->setBillingPostalCodeZip($this::VALUE_ANONYMOUS);
		$this->setBillingAddressMisc($this::VALUE_ANONYMOUS);
		$this->setBillingBankAccountNoDecrypted($this::VALUE_ANONYMOUS);
		$this->setBillingEmailAddressDecrypted($this::VALUE_ANONYMOUS);
		$this->setBillingVATNumberEncrypted($this::VALUE_ANONYMOUS);	

		$this->setDeliveryAddressStreet($this::VALUE_ANONYMOUS);
		$this->setDeliveryPostalCodeZip($this::VALUE_ANONYMOUS);
		$this->setDeliveryAddressMisc($this::VALUE_ANONYMOUS);	
	}
		

}

?>