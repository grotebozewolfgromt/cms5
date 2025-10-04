<?php
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Contacts\models;

use dr\classes\controllers\TControllerAbstract;
use dr\classes\models\TSysContactsAbstract;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\classes\models\TSysModel;

/**
 * This class represents contacts in an address book for the CMS
 *  
 * This is deemed a System-class because it is used by the system-accounts module
 * 
 */


class TSysContacts extends TSysContactsAbstract
{
	const FIELD_ISCLIENT = 'bIsClient';
	const FIELD_ISSUPPLIER = 'bIsSupplier';


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

	/**
	 * create demo contacts in database for DEMO MODE
	 * this clears the current records in memory and adds the new ones
	 * This function is called in Mod_sys_CMSUsers
	 */
	public function resetDemoContactsDB()
	{
        $this->clear(true);

		$objCountries = new TSysCountries();
		$objCountries->loadFromDBByIsDefault();

		//@todo cleanen existing records, deleten toegevoegde records door users

		$this->createContactQuickDB('Hendrix Billboards', 'Johny', 'Hendrix', 'Churchilstreet 15', '223512', 'Fantasy Town', '123456789', $objCountries->getID());
		$this->createContactQuickDB('Bills cars', 'Coiny', 'Bill', 'Paperstreet 12', '225623', 'Fantasy Town', '911', $objCountries->getID());
		$this->createContactQuickDB('De flappentapper', 'Geld', 'Wolf', 'Reineard de Vos plantsoen 9', '225623', 'Dieren', '06-784043743', $objCountries->getID());
	}

	/**
	 * creates a new record in the database and in memory
	 */
	public function createContactQuickDB($sCompany, $sFirstName, $sLastName, $sStreet, $sPostalCode, $sCity, $sTelephone, $iCountryID)
	{
		$bSuccess = true;

        if (!$this->recordExistsTableDB(TSysContacts::FIELD_LASTNAME, $sLastName))
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
				$this->clear();

				$this->newRecord();
				$this->setCustomIdentifier(TSysContacts::VALUE_DEFAULT);
				$this->setIsClient(true);
				$this->setIsSupplier(true);
				$this->setLastName(TSysContacts::VALUE_DEFAULT);
				$this->setFirstNameInitials(TSysContacts::VALUE_DEFAULT);
				$this->setCompanyName(TSysContacts::VALUE_DEFAULT);
				$this->setBillingCity(TSysContacts::VALUE_DEFAULT);
				$this->setDeliveryCity(TSysContacts::VALUE_DEFAULT);
				
				//getting country id
				$objCountries = new TSysCountries();
				$objCountries->limitNone();
				$objCountries->loadFromDBByIsDefault();	
				$this->setCountryIDCodePhoneNumber1($objCountries->getID());
				$this->setCountryIDCodePhoneNumber2($objCountries->getID());
				$this->setBillingCountryID($objCountries->getID());
				$this->setDeliveryCountryID($objCountries->getID());
				unset($objCountries);

				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving default contact on install TSysContacts');

			}
		}
		
		return $bSuccess;
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
		parent::defineTable();


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


		// $this->defineTableDebug(TSysContacts::FIELD_BILLINGADDRESSMISC);
		// $this->defineTableDebug();
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
	
	
	

			
}

?>