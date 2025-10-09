<?php

namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;

/**
 * Description of TSysIPBlackWhitelistAbstract
 * This class represents a list of Blacklisted and Whitelisted IP addresses 
 * 
 * You can use this for logging-in, but also for form submissions for example
 * 
 * MEANING OF THE WORDS:
 * Whitelisted: 
 * 			which IP addresses are allowed to do something - pessimistic security approach.
 * 			you whitelist people to give only a handful of people that you explicitly know, 
 * 			access to something.
 * 			Like a CMS or CRM
 * Blacklisted: 
 * 			which IP addresses are NOT allowed to do something - optimistic security approach
 * 			You blacklist people that have done something wrong
 * 			This applies mostly to publicly accessible websites.
 * 
 *************************
 * function isAllowedLogic()
 *************************
 * function isAllowedLogic() has the logic to determine whether the blacklist or whitelist applies
 * 
 * 
 *************************
 * CONFLICT RESOLUTION
 *************************
 * An item can not be whitelist AND blacklist at the same time, they would conflict.
 * If Blacklisted and Whitelisted items conflict:
 * BLACKLISTED WILL ALWAYS TAKE PRECEDENT OVER WHITELISTED!
 * This is done for security reasons.
 * 
 *************************
 * NULL DATE
 *************************
 * When the startdate is null, it means ALWAYS!
 * 
 *
 *************************
 * VERSION HISTORY
 * 30 okt 2024: TSysIPBlackWhitelistAbstract: created
 * 10 nov 2024: TSysIPBlackWhitelistAbstract: created
 * 14 nov 2024: isWhitelistAllowed() checkt nu op checksum
 * 14 nov 2024: isWhitelistAllowed() bugfix: 0-datums gingen nog niet goed
 */
abstract class TSysIPBlackWhitelistAbstract extends TSysModel
{
	const FIELD_STARTDATE	= 'dtStartDate'; //start date on which the ban goes in effect
	const FIELD_ENDDATE		= 'dtEndDate'; //start date on which the ban expires
	const FIELD_IPADDRESS 	= 'binIPAddress'; //ipv4 or ipv6 ip address (stored are binary in db)
	const FIELD_NOTES		= 'sNotes'; //description of why the ban or who this ip address belongs to
	const FIELD_ENABLED		= 'bEnabled'; //enabled=ban applied, disabled=no ban. it is sometimes helpful to temporary enable/disable certain IPs without removing them from the list
	const FIELD_BLACKLISTED	= 'bBlacklisted'; //is blacklisted?
	const FIELD_WHITELISTED	= 'bWhitelisted'; //is whitelisted?
	

    /**
	 * from which moment does the ban apply?
     * when the datetime object is null, which means: always
     * 
     * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set
     */
    public function setStartDate($objDateTime = null)
    {
        $this->setTDateTime(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, $objDateTime);
    }        

    /**
	 * from which moment does the ban apply?
     * when the datetime object is null, which means: always
	 * 
     * @return TDateTime
     */
    public function getStartDate()
    {
        return $this->get(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE);
    }   

    /**
	 * from which moment does the ban stop?
     * when the datetime object is null, which means: no end date (always)
     * 
     * @param TDateTime $objDateTime when null then an invalid date (timestamp 0) will be set
     */
    public function setEndDate($objDateTime = null)
    {
        $this->setTDateTime(TSysIPBlackWhitelistAbstract::FIELD_ENDDATE, $objDateTime);
    }        

    /**
	 * from which moment does the ban stop?
     * when the datetime object is null, which means: no end date (always)
	 * 
     * @return TDateTime
     */
    public function getEndDate()
    {
        return $this->get(TSysIPBlackWhitelistAbstract::FIELD_ENDDATE);
    }   

	/**
	 * get ip address (ipv4 or ipv6)
	 * 
	 * @return string
	 */
	public function getIPAddress()
	{
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS);
	}

	
	/**
	 * set ip adress (ipv4 or ipv6)
	 * 
	 * @param string $sIPAddr
	 */
	public function setIPAddress($sIPAddr)
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, $sIPAddr);
	}        
	
	/**
	 * set notes for the entry
	 * like: the reason for ban (blacklisted), or who this ip address belongs to (whitelisted)
	 * 
	 * @return string
	 */
	public function getNotes()
	{
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_NOTES);
	}

	/**
	 * set notes for the entry
	 * like: the reason for ban (blacklisted), or who this ip address belongs to (whitelisted)
	 * 
	 * @param string $sNotes
	 */
	public function setNotes($sNotes)
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_NOTES, $sNotes);
	}           

	
	/**
	 * get ban applied or not?
	 * 
	 * @return bool
	 */
	public function getEnabled()
	{
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_ENABLED);
	}

	/**
	 * set if ban applies or not
	 * 
	 * @param bool $bEnabled
	 */
	public function setEnabled($bEnabled)
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, $bEnabled);
	}   


	/**
	 * get is blacklisted?
	 * 
	 * @return bool
	 */
	public function getBlacklisted()
	{
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_BLACKLISTED);
	}

	/**
	 * set is blacklisted?
	 * 
	 * @param bool $bBlack
	 */
	public function setBlacklisted($bBlack)
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_BLACKLISTED, $bBlack);
	}   


	/**
	 * get is whitelisted?
	 * 
	 * @return bool
	 */
	public function getWhitelisted()
	{
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_WHITELISTED);
	}

	/**
	 * set is whitelisted?
	 * 
	 * @param bool $bWhite
	 */
	public function setWhitelisted($bWhite)
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_WHITELISTED, $bWhite);
	}   

	/**
	 * determines whether a IP address is allowed or not.
	 * 
	 * RECORDS MUST BE LOADED FROM DATABASE BEFORE CALLING THIS FUNCTION!
	 * use authenticateIPAddressDB() to do this automatically
	 * This function does NOT do a database load, it only provides the business logic!
	 * 
	 * For the best performance: load only records for ip address in question
	 * 
	 * When whitelist and blacklist contradict, blacklist has priority
	 * 
	 * @param string $sIPAddress if '' ip address of client is assumed
	 * @return bool allowed or not
	 */
	public function isAllowedLogic($sIPAddress = '')
	{
		$bAllowed = false;

		if ($sIPAddress === '')
			$sIPAddress = getIPAddress();

		//==== FIRST: WHITELIST
		if ($this->getWhitelistEnabled())
		{	
			$bAllowed = $this->isWhitelistAllowed($sIPAddress, false);
		}

		//==== LAST: BLACKLIST
		//blacklist overrules whitelist
		if ($this->getWhitelistEnabled() && $bAllowed)
			$bAllowed = $this->isBlacklistAllowed($sIPAddress, true); //when list applies it's blacklisted: so, not allowed, hence the not-sign(!)
		elseif (!$this->getWhitelistEnabled())
			$bAllowed = $this->isBlacklistAllowed($sIPAddress, true); //when list applies it's blacklisted: so, not allowed, hence the not-sign(!)
	

		return $bAllowed;
	}

	/**
	 * determines if whitelist allows access
	 * used by isAllowedLogic()
	 */
	private function isWhitelistAllowed(&$sIPAddress)
	{
		$bAllowed = false; //==> default NOT allowed, we try to prove otherwise with the procedure below

		if (!$this->isChecksumValidAllRecords())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Checksum failed blackwhitelist');
			logAccess(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Checksum failed blackwhitelist');
			return false;
		}

		$this->resetRecordPointer();
		while ($this->next()) //iterate records
		{
			if ($this->getIPAddress() === $sIPAddress)
			{
				if ($this->getEnabled())
				{
					//start date
					if (!$this->getStartDate()->isZero()) //start date applies
					{
						if ($this->getStartDate()->isInThePast())
							$bAllowed = true;
					}
					else
						$bAllowed = true;

					//end date
					if (!$this->getEndDate()->isZero()) //end date applies
					{
						if ($bAllowed)
						{
							if ($this->getEndDate()->isInTheFuture())
								$bAllowed = true;
							else 
								$bAllowed  = false;
						}
					}		
					else
						$bAllowed = true;								

					//whitelisted flag
					if ($bAllowed === true)
					{
						if ($this->getWhitelisted() === true)
							$bAllowed = true;
						else
							$bAllowed = false;
					}
				}
			}
		}

		return $bAllowed;
	}

	/**
	 * determines if blacklist allows access
	 * used by isAllowedLogic()
	 */
	private function isBlacklistAllowed(&$sIPAddress)
	{
		$bAllowed = true; //==> default allowed, we try to prove otherwise with the procedure below


		$this->resetRecordPointer();
		while ($this->next()) //iterate records
		{
			if (($this->getIPAddress() === $sIPAddress))
			{
				if ($this->getEnabled())
				{
					//start date
					if (!$this->getStartDate()->isZero()) //start date applies
					{
						if ($this->getStartDate()->isInThePast())
							$bAllowed = false;
					}
					else
						$bAllowed = false;

					//end date
					if (!$this->getEndDate()->isZero()) //end date applies
					{
						if (!$bAllowed)
						{
							if ($this->getEndDate()->isInTheFuture())
								$bAllowed = false;
							else
								$bAllowed = true;
						}
					}	
					else
						$bAllowed = false;				
					
					//blacklisted flag
					if ($bAllowed === false)
					{
						if ($this->getBlacklisted() === true)
							$bAllowed = false;					
						else
							$bAllowed = true;
					}
				}
			}
			else
				return false; //1 false ip address detected and we're out
		}

		return $bAllowed;		
	}

    /**
     * loads blacklist/whitelist from database and 
	 * authenticate IP address with login blackwhitelist object
     * 
	 * @param $sIPAddress if '' ip address of client is assumed
     * @return boolean
     */
    public function authenticateIPAddressDB($sIPAddress = '')
    {
		if ($sIPAddress === '')
			 $sIPAddress = getIPAddress();

        $this->clear();
        if ($this->loadFromDBByIPAddress($sIPAddress))
		{
// vardumpdie($this->count(), 'hoerenjong1-remote:'.$_SERVER['REMOTE_ADDR'].'-server:'.$_SERVER['SERVER_ADDR']);
		    return $this->isAllowedLogic($sIPAddress);
		}
		else
			return false;
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
			if (!$this->recordExistsTableDB(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, getIPAddressClient()))
			{
				$this->clear();
				$this->setIPAddress(getIPAddressClient());
				$this->setWhitelisted(true);
				$this->setBlacklisted(false);
				$this->setEnabled(true);
				$this->setNotes('Auto added on install framework');
				if (!$this->saveToDB()) 
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving whitelist current ip address on install: '. getIPAddressClient()); 
			}
		}

		return $bSuccess;
	}	

	/**
	 * load record by looking at ip address
	 * 
	 * @return boolean load ok?
	 */
	public function loadFromDBByIPAddress($sIPAddress)
	{
		$this->clear();
		$this->find(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, $sIPAddress);
		return $this->loadFromDB();
	}	




	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
         * 
	 * initialize values
	 */
	public function initRecord()
	{
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, new TDateTime(0));
		$this->set(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, true);
	}
		
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//start date
		$this->setFieldDefaultValue(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldType(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, CT_DATETIME);
		$this->setFieldLength(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, 0);
		$this->setFieldDecimalPrecision(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, 0);
		$this->setFieldPrimaryKey(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldNullable(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldEnumValues(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldUnique(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldIndexed(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldFulltext(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldForeignKeyClass(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldForeignKeyTable(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldForeignKeyField(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldForeignKeyJoin(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, null);
		$this->setFieldAutoIncrement(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldUnsigned(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, false);
		$this->setFieldEncryptionDisabled(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE);									

		//end date
		$this->setFieldCopyProps(TSysIPBlackWhitelistAbstract::FIELD_ENDDATE, TSysIPBlackWhitelistAbstract::FIELD_STARTDATE);
		
		//ip address
		$this->setFieldDefaultValue(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldType(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, CT_IPADDRESS);
		$this->setFieldLength(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, 0);
		$this->setFieldDecimalPrecision(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, 0);
		$this->setFieldPrimaryKey(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldNullable(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldEnumValues(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldUnique(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldIndexed(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, true);
		$this->setFieldFulltext(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldForeignKeyClass(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyTable(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyField(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyJoin(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldForeignKeyActionOnDelete(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, null);
		$this->setFieldAutoIncrement(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldUnsigned(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, false);
		$this->setFieldEncryptionDisabled(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS);									


		//notes
		$this->setFieldDefaultValue(TSysIPBlackWhitelistAbstract::FIELD_NOTES, '');
		$this->setFieldType(TSysIPBlackWhitelistAbstract::FIELD_NOTES, CT_VARCHAR);
		$this->setFieldLength(TSysIPBlackWhitelistAbstract::FIELD_NOTES, 255);
		$this->setFieldDecimalPrecision(TSysIPBlackWhitelistAbstract::FIELD_NOTES, 0);
		$this->setFieldPrimaryKey(TSysIPBlackWhitelistAbstract::FIELD_NOTES, false);
		$this->setFieldNullable(TSysIPBlackWhitelistAbstract::FIELD_NOTES, true);
		$this->setFieldEnumValues(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldUnique(TSysIPBlackWhitelistAbstract::FIELD_NOTES, false);
		$this->setFieldIndexed(TSysIPBlackWhitelistAbstract::FIELD_NOTES, false); 
		$this->setFieldFulltext(TSysIPBlackWhitelistAbstract::FIELD_NOTES, true); 
		$this->setFieldForeignKeyClass(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldForeignKeyTable(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldForeignKeyField(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldForeignKeyJoin(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldForeignKeyActionOnDelete(TSysIPBlackWhitelistAbstract::FIELD_NOTES, null);
		$this->setFieldAutoIncrement(TSysIPBlackWhitelistAbstract::FIELD_NOTES, false);
		$this->setFieldUnsigned(TSysIPBlackWhitelistAbstract::FIELD_NOTES, false);
		$this->setFieldEncryptionDisabled(TSysIPBlackWhitelistAbstract::FIELD_NOTES);	


		//enabled
		$this->setFieldDefaultValue(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldType(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, CT_BOOL);
		$this->setFieldLength(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, 0);
		$this->setFieldDecimalPrecision(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, 0);
		$this->setFieldPrimaryKey(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldNullable(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldEnumValues(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldUnique(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldIndexed(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldFulltext(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldForeignKeyClass(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldForeignKeyTable(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldForeignKeyField(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldForeignKeyJoin(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldForeignKeyActionOnDelete(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, null);
		$this->setFieldAutoIncrement(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);
		$this->setFieldUnsigned(TSysIPBlackWhitelistAbstract::FIELD_ENABLED, false);	
        $this->setFieldEncryptionDisabled(TSysIPBlackWhitelistAbstract::FIELD_ENABLED);		
		
		//blacklisted: bool
		$this->setFieldCopyProps(TSysIPBlackWhitelistAbstract::FIELD_BLACKLISTED, TSysIPBlackWhitelistAbstract::FIELD_ENABLED);

		//whitelisted: bool
		$this->setFieldCopyProps(TSysIPBlackWhitelistAbstract::FIELD_WHITELISTED, TSysIPBlackWhitelistAbstract::FIELD_ENABLED);

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
		return array(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, 
				TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS, 
				TSysIPBlackWhitelistAbstract::FIELD_NOTES, 
				TSysIPBlackWhitelistAbstract::FIELD_ENABLED, 
				TSysIPBlackWhitelistAbstract::FIELD_BLACKLISTED, 
				TSysIPBlackWhitelistAbstract::FIELD_WHITELISTED, 
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
		return $this->get(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS);
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
		return 'helpendehandjes'.
			$this->getDateAsString(TSysIPBlackWhitelistAbstract::FIELD_STARTDATE, 'd-m-Y H:i').
			$this->getDateAsString(TSysIPBlackWhitelistAbstract::FIELD_ENDDATE, 'd-m-Y H:i').
			'maken'.
			$this->get(TSysIPBlackWhitelistAbstract::FIELD_IPADDRESS).
			'veel'.
			$this->get(TSysIPBlackWhitelistAbstract::FIELD_NOTES).
			'licht'.
			boolToStr($this->get(TSysIPBlackWhitelistAbstract::FIELD_ENABLED)).
			'werk';
			boolToStr($this->get(TSysIPBlackWhitelistAbstract::FIELD_BLACKLISTED)).
			boolToStr($this->get(TSysIPBlackWhitelistAbstract::FIELD_WHITELISTED));
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
		if ($this->getIPAddress() == '')
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'IP address is empty');
			return false;
		}

		if (($this->getBlacklisted() === true) && ($this->getWhitelisted() === true))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'Record can\'t be blacklisted and whitelisted at the same time. They conflict.');
			return false;
		}		

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
		return false;
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

	/********************************** ABSTRACT FUNCTIONS ************************ */

	/**
	 * Returns whether the whitelist is enabled or not
	 * A whitelist is not desireable in every situation, for example logging in into a webshop: everybody must be able to log in
	 */
	abstract public function getWhitelistEnabled();


} 
?>