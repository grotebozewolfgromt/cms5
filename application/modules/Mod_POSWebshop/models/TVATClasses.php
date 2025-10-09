<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;


/**
 * VAT classes (Dutch: BTW tarief)
 * The actual percentage of VAT is stored in TVATClassesCountries because you can define percentages of VAT for each country
 * 
 * 
 * 30 apr 2025: TVatClasses
 * 
 * @author Dennis Renirie
 */

class TVATClasses extends TSysModel
{
	const FIELD_NAME					= 'sName'; //name of the type 
	const FIELD_DESCRIPTION				= 'sDescription'; //describes what this transaction type does
	// const FIELD_ISDEFAULT				= 'bIsDefault'; //is selected by default?


	/**
	 * get invoice type name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get(TVATClasses::FIELD_NAME);
	}
	
	/**
	 * set invoice type name
	 * 
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->set(TVATClasses::FIELD_NAME, $sName);
	}        
	
	/**
	 * get description
	 * used to describe what this transaction type does as an extension of name
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->get(TVATClasses::FIELD_DESCRIPTION);
	}
	
	/**
	 * set description
	 * used to describe what this transaction type does as an extension of name
	 * 
	 * @param string $sDescription
	 */
	public function setDescription($sDescription)
	{
		$this->set(TVATClasses::FIELD_DESCRIPTION, $sDescription);
	}    

	// /**
	//  * get is default selected
	//  * 
	//  * @return boolean
	//  */
	// public function getIsDefault()
	// {
	// 	return $this->get(TVATClasses::FIELD_ISDEFAULT);
	// }
	
	// /**
	//  * set is default selected
	//  * 
	//  * @param boolean $bDefault
	//  */
	// public function setIsDefault($bDefault)
	// {
	// 	$this->set(TVATClasses::FIELD_ISDEFAULT, $bDefault);
	// }        



	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
     * 
	 * initialize values
	 */
	public function initRecord()
	{
		$this->setName("new ".date("Y-m-d H:i:s")); //preventing empy name being written to database resulting in duplicate name when this happened before
	}
		
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//transaction type name
		$this->setFieldDefaultValue(TVATClasses::FIELD_NAME, '');
		$this->setFieldType(TVATClasses::FIELD_NAME, CT_VARCHAR);
		$this->setFieldLength(TVATClasses::FIELD_NAME, 50);
		$this->setFieldDecimalPrecision(TVATClasses::FIELD_NAME, 0);
		$this->setFieldPrimaryKey(TVATClasses::FIELD_NAME, false);
		$this->setFieldNullable(TVATClasses::FIELD_NAME, false);
		$this->setFieldEnumValues(TVATClasses::FIELD_NAME, null);
		$this->setFieldUnique(TVATClasses::FIELD_NAME, true); 
		$this->setFieldIndexed(TVATClasses::FIELD_NAME, false); 
		$this->setFieldFulltext(TVATClasses::FIELD_NAME, true); 
		$this->setFieldForeignKeyClass(TVATClasses::FIELD_NAME, null);
		$this->setFieldForeignKeyTable(TVATClasses::FIELD_NAME, null);
		$this->setFieldForeignKeyField(TVATClasses::FIELD_NAME, null);
		$this->setFieldForeignKeyJoin(TVATClasses::FIELD_NAME);
		$this->setFieldForeignKeyActionOnUpdate(TVATClasses::FIELD_NAME, null);
		$this->setFieldForeignKeyActionOnDelete(TVATClasses::FIELD_NAME, null); 
		$this->setFieldAutoIncrement(TVATClasses::FIELD_NAME, false);
		$this->setFieldUnsigned(TVATClasses::FIELD_NAME, false);
        $this->setFieldEncryptionDisabled(TVATClasses::FIELD_NAME);

		//description
        $this->setFieldCopyProps(TVATClasses::FIELD_DESCRIPTION, TVATClasses::FIELD_NAME);
		$this->setFieldUnique(TVATClasses::FIELD_DESCRIPTION, false); 		
		$this->setFieldLength(TVATClasses::FIELD_DESCRIPTION, 100);

		//default
		$this->setFieldDefaultValue(TVATClasses::FIELD_ISDEFAULT, false);
		$this->setFieldType(TVATClasses::FIELD_ISDEFAULT, CT_BOOL);
		$this->setFieldLength(TVATClasses::FIELD_ISDEFAULT, 1);
		$this->setFieldDecimalPrecision(TVATClasses::FIELD_ISDEFAULT, 0);
		$this->setFieldPrimaryKey(TVATClasses::FIELD_ISDEFAULT, false);
		$this->setFieldNullable(TVATClasses::FIELD_ISDEFAULT, false);
		$this->setFieldEnumValues(TVATClasses::FIELD_ISDEFAULT, null);
		$this->setFieldUnique(TVATClasses::FIELD_ISDEFAULT, false); 
		$this->setFieldIndexed(TVATClasses::FIELD_ISDEFAULT, false); 
		$this->setFieldFulltext(TVATClasses::FIELD_ISDEFAULT, false); 
		$this->setFieldForeignKeyClass(TVATClasses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyTable(TVATClasses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyField(TVATClasses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyJoin(TVATClasses::FIELD_ISDEFAULT);
		$this->setFieldForeignKeyActionOnUpdate(TVATClasses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyActionOnDelete(TVATClasses::FIELD_ISDEFAULT, null); 
		$this->setFieldAutoIncrement(TVATClasses::FIELD_ISDEFAULT, false);
		$this->setFieldUnsigned(TVATClasses::FIELD_ISDEFAULT, false);
        $this->setFieldEncryptionDisabled(TVATClasses::FIELD_ISDEFAULT);

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
		return array(TVATClasses::FIELD_NAME, 
			TVATClasses::FIELD_DESCRIPTION, 
			TVATClasses::FIELD_POSITION
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
		return true;
	}
	
	/**
	 * use checkout for locking file for editing
	*/
	public function getTableUseCheckout()
	{
		return true;
	}
		
	/**
	 * use record locking to prevent record editing
	*/
	public function getTableUseLock()
	{
		return true;
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
		return false;
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
		return APP_DB_TABLEPREFIX.'VATClasses';
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
		return $this->get(TVATClasses::FIELD_NAME);
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
		return '23kjh4kjh23'.
			$this->getName().
			$this->getDescription().
			'asdf33r3r3r3'.
			$this->getName();			
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
		return true;
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
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = true;
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		//==check if at least one Transaction type exists, if not, then add it
		$this->newQuery();
		$this->clear();    
		$this->limitOne(); //we need just one record to be returned
		if (!$this->loadFromDB())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loadFromDB() failed in install()');
			return false;
		}
		
		//==if no category exists, add a default one
		if($this->count() == 0)
		{
			//high
			$this->clear();
			$this->newRecord();
			$this->setName('High');
			$this->setDescription('High VAT class');
			$this->setIsDefault(true);
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice: high');
				return false;
			}    

			//low
			$this->clear();
			$this->newRecord();
			$this->setName('Low');
			$this->setDescription('Low VAT class');
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice: low');
				return false;
			}   

			//none
			$this->clear();
			$this->newRecord();
			$this->setName('None');
			$this->setDescription('No VAT applied');
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice: none');
				return false;
			}    	
		}
			
		return $bSuccess;
	}        	
} 
?>