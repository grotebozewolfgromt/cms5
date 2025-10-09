<?php
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Settings\models;

use dr\classes\models\TSysModel;

/**
 * This class represents the settings for the framework
 * 
 * I choose not to inherit from TSysSettings because due to php speed issues with OOP inheritance
 * since the TSysSettings class doesn't add too much, and this class
 * can be used a lot (once every page-load), an extra layer to slow down the system is undesired
 */
class TSysSettings extends TSysModel
{
	const FIELD_RESOURCE = 'sResource'; //this is the "key" in key-value pair. this is called resource for consistency reasons with the auth() system
	const FIELD_VALUE = 'sValue'; 
	const FIELD_TYPE = 'iType';
	const FIELD_DESCRIPTION = 'sDescription';//this is DefaultEnglish



	public function getResource()
	{
			return $this->get(TSysSettings::FIELD_RESOURCE);
	}

	public function setResource($sResource)
	{
			$this->set(TSysSettings::FIELD_RESOURCE, $sResource);
	}
	
	public function getValue()
	{
			return $this->get(TSysSettings::FIELD_VALUE);
	}
	
	public function setValue($sValue)
	{
			$this->set(TSysSettings::FIELD_VALUE, $sValue);
	}
	
	/**
	 * what type is the value?
	 * these are the TP_ values defined in libtypedef,
	 * for example: TP_STRING
	 */
	public function getType()
	{
			return  $this->get(TSysSettings::FIELD_TYPE);
	}
	
	/**
	 * Set type of the value
	 * these are the TP_ values defined in libtypedef,
	 * for example: TP_STRING
	 */
	public function setType($sType = TP_STRING)
	{
		$this->set(TSysSettings::FIELD_TYPE, $sType);
	}        
			

	public function getDescription()
	{
		return $this->get(TSysSettings::FIELD_DESCRIPTION);
	}


	public function setDescription($sDescription)
	{
		$this->set(TSysSettings::FIELD_DESCRIPTION, $sDescription);
	}


	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
		 * 
	 * initialize values
	 */
	public function initRecord()
	{
		$this->setType(TP_STRING);
	}



	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
			
		//resource
		$this->setFieldDefaultValue(TSysSettings::FIELD_RESOURCE, '');
		$this->setFieldType(TSysSettings::FIELD_RESOURCE, CT_VARCHAR);
		$this->setFieldLength(TSysSettings::FIELD_RESOURCE, 255);
		$this->setFieldDecimalPrecision(TSysSettings::FIELD_RESOURCE, 0);
		$this->setFieldPrimaryKey(TSysSettings::FIELD_RESOURCE, false);
		$this->setFieldNullable(TSysSettings::FIELD_RESOURCE, false);
		$this->setFieldEnumValues(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldUnique(TSysSettings::FIELD_RESOURCE, true);
		$this->setFieldIndexed(TSysSettings::FIELD_RESOURCE, false); //it is already unique
		$this->setFieldFulltext(TSysSettings::FIELD_RESOURCE, false);
		$this->setFieldForeignKeyClass(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldForeignKeyTable(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldForeignKeyField(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldForeignKeyJoin(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysSettings::FIELD_RESOURCE, null);
		$this->setFieldAutoIncrement(TSysSettings::FIELD_RESOURCE, false);
		$this->setFieldUnsigned(TSysSettings::FIELD_RESOURCE, false);
		$this->setFieldEncryptionDisabled(TSysSettings::FIELD_RESOURCE);						

		//value
		$this->setFieldDefaultValue(TSysSettings::FIELD_VALUE, '');
		$this->setFieldType(TSysSettings::FIELD_VALUE, CT_LONGTEXT);
		$this->setFieldLength(TSysSettings::FIELD_VALUE, 0);
		$this->setFieldDecimalPrecision(TSysSettings::FIELD_VALUE, 0);
		$this->setFieldPrimaryKey(TSysSettings::FIELD_VALUE, false);
		$this->setFieldNullable(TSysSettings::FIELD_VALUE, true);
		$this->setFieldEnumValues(TSysSettings::FIELD_VALUE, null);
		$this->setFieldUnique(TSysSettings::FIELD_VALUE, false);
		$this->setFieldIndexed(TSysSettings::FIELD_VALUE, false);
		$this->setFieldFulltext(TSysSettings::FIELD_VALUE, false);
		$this->setFieldForeignKeyClass(TSysSettings::FIELD_VALUE, null);
		$this->setFieldForeignKeyTable(TSysSettings::FIELD_VALUE, null);
		$this->setFieldForeignKeyField(TSysSettings::FIELD_VALUE, null);
		$this->setFieldForeignKeyJoin(TSysSettings::FIELD_VALUE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysSettings::FIELD_VALUE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysSettings::FIELD_VALUE, null);
		$this->setFieldAutoIncrement(TSysSettings::FIELD_VALUE, false);
		$this->setFieldUnsigned(TSysSettings::FIELD_VALUE, false);
		$this->setFieldEncryptionDisabled(TSysSettings::FIELD_VALUE);								

		//type
		$this->setFieldDefaultValue(TSysSettings::FIELD_TYPE, 0);
		$this->setFieldType(TSysSettings::FIELD_TYPE, CT_INTEGER32);
		$this->setFieldLength(TSysSettings::FIELD_TYPE, 0);
		$this->setFieldDecimalPrecision(TSysSettings::FIELD_TYPE, 0);
		$this->setFieldPrimaryKey(TSysSettings::FIELD_TYPE, false);
		$this->setFieldNullable(TSysSettings::FIELD_TYPE, false);
		$this->setFieldEnumValues(TSysSettings::FIELD_TYPE, null);
		$this->setFieldUnique(TSysSettings::FIELD_TYPE, false);
		$this->setFieldIndexed(TSysSettings::FIELD_TYPE, false);
		$this->setFieldFulltext(TSysSettings::FIELD_TYPE, false);
		$this->setFieldForeignKeyClass(TSysSettings::FIELD_TYPE, null);
		$this->setFieldForeignKeyTable(TSysSettings::FIELD_TYPE, null);
		$this->setFieldForeignKeyField(TSysSettings::FIELD_TYPE, null);
		$this->setFieldForeignKeyJoin(TSysSettings::FIELD_TYPE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysSettings::FIELD_TYPE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysSettings::FIELD_TYPE, null);
		$this->setFieldAutoIncrement(TSysSettings::FIELD_TYPE, false);
		$this->setFieldUnsigned(TSysSettings::FIELD_TYPE, false);
		$this->setFieldEncryptionDisabled(TSysSettings::FIELD_TYPE);										


		//description
		$this->setFieldDefaultValue(TSysSettings::FIELD_DESCRIPTION, '');
		$this->setFieldType(TSysSettings::FIELD_DESCRIPTION, CT_VARCHAR);
		$this->setFieldLength(TSysSettings::FIELD_DESCRIPTION, 255);
		$this->setFieldDecimalPrecision(TSysSettings::FIELD_DESCRIPTION, 0);
		$this->setFieldPrimaryKey(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldNullable(TSysSettings::FIELD_DESCRIPTION, true);
		$this->setFieldEnumValues(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldUnique(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldIndexed(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldFulltext(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldForeignKeyClass(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyTable(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyField(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyJoin(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyActionOnDelete(TSysSettings::FIELD_DESCRIPTION, null);
		$this->setFieldAutoIncrement(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldUnsigned(TSysSettings::FIELD_DESCRIPTION, false);
		$this->setFieldEncryptionDisabled(TSysSettings::FIELD_DESCRIPTION);			

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
		return array(TSysSettings::FIELD_RESOURCE, TSysSettings::FIELD_VALUE, TSysSettings::FIELD_DESCRIPTION);
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
		return APP_DB_TABLEPREFIX.'SysSettings';
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
		return $this->get(TSysSettings::FIELD_RESOURCE).' = '.$this->get(TSysSettings::FIELD_VALUE).'';
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
		return 'henkieisgek'.$this->get(TSysSettings::FIELD_RESOURCE).'maarnietheus'.$this->get(TSysSettings::FIELD_VALUE).'echtwel';
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

	/**
	 * create settings in database by the Business Logic array from modules/framework/cms
	 *
	 * @return void
	 */
	public function createSettingsDBByBLArr($sModule, $arrSettingsEntriesBL)
	{
		$objSettings = new TSysSettings();
		$sResource = '';
		$sDefaultVal = '';
		$sType = TP_UNDEFINED;
		$arrKeys = array_keys($arrSettingsEntriesBL);

		foreach ($arrKeys as $sArrKey)
		{
			$sDefaultVal = $arrSettingsEntriesBL[$sArrKey][0];
			$sType = $arrSettingsEntriesBL[$sArrKey][1];
			$sDescription = $arrSettingsEntriesBL[$sArrKey][2];
			$sResource = getSettingsResourceString($sModule, $sArrKey);

			//create new one, when doesn't exist
			if (!$objSettings->recordExistsTableDB(TSysSettings::FIELD_RESOURCE, $sResource))
			{
				$objSettings->newRecord();
				$objSettings->setResource($sResource);
				$objSettings->setValue($sDefaultVal);
				$objSettings->setType($sType);
				$objSettings->setDescription($sDescription);
				if (!$objSettings->saveToDB())
					return false;
			}
		}

		return true;
	}

	/**
	 * delete settings from database
	 */
	public function deleteSettingsDBForModule($sModule)
	{
		$objSettings = new TSysSettings();
		// $objSettings->findLike(TSysSettings::FIELD_RESOURCE, $sModule.SETTINGS_RESOURCESEPARATOR.'%'); --> 16-4-2025 replaced by line below
		$objSettings->find(TSysSettings::FIELD_RESOURCE, $sModule.SETTINGS_RESOURCESEPARATOR.'%', COMPARISON_OPERATOR_LIKE, '', TP_STRING); 

		return $objSettings->deleteFromDB(true);

		// return true;
	}


    /**
     * create permissions in database for the CMS
     *
     * @return bool creation succesful?
     */
    private function createSettingsForSystemAndCMS()
    {
		if (!$this->createSettingsDBByBLArr(SETTINGS_MODULE_SYSTEM, getSettingsEntriesSystem()))
			return false;
		if (!$this->createSettingsDBByBLArr(SETTINGS_MODULE_CMS, getSettingsEntriesCMS()))
			return false;

		return true;
	}


	/**
	 * delete or add settings
	 * @todo
	 * @return bool
	 */
	public function updateSettingsDB()
	{
		//==== ADD SETTINGS 
		//that are in the business logic, but not in database

		//is it in the cms?
		if (!$this->addSettingsToDBFromBL(SETTINGS_MODULE_CMS, getSettingsEntriesCMS()))//CMS
		{
			error_log('creating settings for CMS FAILED!<br>');
			logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'creating settings for CMS FAILED!<br>');
			return false;
		}

		//is it in the system?
		if (!$this->addSettingsToDBFromBL(SETTINGS_MODULE_SYSTEM, getSettingsEntriesSystem()))//SYSTEM
		{
			error_log('creating settings for System FAILED!<br>');
			logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'creating settings for System FAILED!<br>');
			return false;
		}

		//is it in one of the modules?
		$arrModuleFolders = getModuleFolders();
		foreach($arrModuleFolders as $sModName)
		{
			$sModFullClassBL = getModuleFullNamespaceClass($sModName);
            $objModBL = new $sModFullClassBL;
            $arrSetModBL = $objModBL->getSettingsEntries();

            if ($this->addSettingsToDBFromBL($sModName, $arrSetModBL))
				logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'created settings in database for module '.$sModName.' (if they didn\'t exist already)<br>');
			else
			{
				logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'creating settings in database for module '.$sModName.' FAILED!!<br>');
				return false;
			}

			unset($objModBL);

		}


		//==== DELETE SETTINGS
		//delete orphaned settings that are still in the database but not used anymore in the business logic
		$objSettings = new TSysSettings();
		$objSettings->limitNone();//we want it all
		$objSettings->loadFromDB();
		$objSettingsDelete = new TSysSettings();

		//for every setting, see if we can find it in the business logic
		while($objSettings->next())
		{
			$arrResource = getSettingsResourceArray($objSettings->getResource());

			//is it in the cms settings?			
			if ($arrResource['module'] == SETTINGS_MODULE_CMS)
			{
				logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'looking for outdated settings for CMS ...<br>');
				if (!$this->deleteSettingDBIfNotExistsInBLArr($objSettings->getResource(), getSettingsEntriesCMS()))
				{
					logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleting outdated settings for CMS FAILED!<br>');
					return false;
				}
			}

			//is it in the framework settings?
			if ($arrResource['module'] == SETTINGS_MODULE_SYSTEM)
			{
				logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'looking for outdated settings for System ...<br>');
				if (!$this->deleteSettingDBIfNotExistsInBLArr($objSettings->getResource(), getSettingsEntriesSystem()))
				{
					logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'deleting settings for System FAILED!<br>');
					return false;
				}
			}
								
			//is it in one of the modules?
			$arrModuleFolders = getModuleFolders();
			foreach($arrModuleFolders as $sModName)
			{
				if ($arrResource['module'] == $sModName)
				{
					$sModFullClassBL = getModuleFullNamespaceClass($sModName);
					$objModBL = new $sModFullClassBL;

					logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'looking for outdated settings for module '.$sModName.'<br>');					
					if (!$this->deleteSettingDBIfNotExistsInBLArr($objSettings->getResource(),$objModBL->getSettingsEntries(), 'nsoekep' ))	
					{						
						error_log('error deleting outdated settings module '.$sModName);
						logCronjob(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error deleting outdated settings module '.$sModName);
						return false;
					}
					
					unset($objModBL);
				}
			}	


		}
		unset($objSettings);
		unset($objSettingsDelete);


		return true;
	}

	/**
	 * checks if the $arrSettingsEntriesBL exist in database, 
	 * if not: add to db
	 */
	private function addSettingsToDBFromBL($sModBL, $arrSettingsEntriesBL)
	{
		$objSettingsDBExist = new TSysSettings();
		$objSettingsDBExist->limitNone();
		// $objSettingsDBExist->findLike(TSysSettings::FIELD_RESOURCE, $sModBL.SETTINGS_RESOURCESEPARATOR.'%'); --> 16-4-2025 replaced by line below
		$objSettingsDBExist->find(TSysSettings::FIELD_RESOURCE, $sModBL.SETTINGS_RESOURCESEPARATOR.'%', COMPARISON_OPERATOR_LIKE, '', TP_STRING);
		$objSettingsDBExist->loadFromDB();
		$objSettingsDBNew = new TSysSettings();
		$arrKeys = array_keys($arrSettingsEntriesBL);


		foreach ($arrKeys as $sKey)
		{
			$sDefaultVal = $arrSettingsEntriesBL[$sKey][0];
			$sType = $arrSettingsEntriesBL[$sKey][1];
			$sDescription = $arrSettingsEntriesBL[$sKey][2];
			$sResource = getSettingsResourceString($sModBL, $sKey);
			$bFoundInDB = false;

			$objSettingsDBExist->resetRecordPointer();
			while($objSettingsDBExist->next())
			{
				if ($objSettingsDBExist->getResource() == $sResource)
					$bFoundInDB = true;
			}

			if (!$bFoundInDB)
			{
				$objSettingsDBNew->newRecord();		
				$objSettingsDBNew->setValue($sDefaultVal);
				$objSettingsDBNew->setType($sType);
				$objSettingsDBNew->setDescription($sDescription);
				$objSettingsDBNew->setResource($sResource);
			}
		}

		if (!$objSettingsDBNew->saveToDBAll())
			return false;

		unset($objSettingsDBNew);
		unset($objSettingsDBExist);

		return true;
	}

	/**
	 * searches a settingsentries array from module, system or cms
	 */
	private function deleteSettingDBIfNotExistsInBLArr($sResource, $arrSettingsEntriesBL)
	{
		$arrKeys = array();
		$arrResource = array();
		$sSettingname = '';

		$arrKeys = array_keys($arrSettingsEntriesBL);
		$arrResource = getSettingsResourceArray($sResource);
		$sSettingname = $arrResource['settingname'];

		$bExists = true;
		$bExists = in_array($sSettingname, $arrKeys);

		if (!$bExists)
		{
			$objSettingsDelete = new TSysSettings();
			$objSettingsDelete->find(TSysSettings::FIELD_RESOURCE, $sResource);
			if (!$objSettingsDelete->deleteFromDB(true))
			{
				error_log('delete setting "'.$sResource.'" FAILED');
				echo 'delete setting failed';
				return false;
			}
		}

		return true;
	}


     /**
     * additions to the install procedure
     * 
     * @param array $arrPreviousDependenciesModelClasses
     */
    public function install($arrPreviousDependenciesModelClasses = null)
    {
        $bSuccess = true;
        $bSuccess = parent::install($arrPreviousDependenciesModelClasses);

		if (!$this->createSettingsForSystemAndCMS())
			return false;

		return $bSuccess;
	}



}

?>