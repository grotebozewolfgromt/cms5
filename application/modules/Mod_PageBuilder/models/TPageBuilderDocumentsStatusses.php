<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_PageBuilder\models;

use dr\classes\models\TSysModel;


/**
 * statusses of a pagebuilder document
 * 
 * 29 may 2024: TPageBuilderStatusses : created
 * 
 * @author Dennis Renirie
 */

class TPageBuilderDocumentsStatusses extends TSysModel
{
	const FIELD_NAME 		= 'sName'; 
	// const FIELD_ISDEFAULT	= 'bIsDefault';

	/**
	 * get name of the status
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get(TPageBuilderDocumentsStatusses::FIELD_NAME);
	}

	
	/**
	 * set name of the currency
	 * 
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, $sName);
	}        
	


	// public function getIsDefault()
	// {
	// 	return  $this->get(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT);
	// }
	
	// public function setIsDefault($bDefault)
	// {
	// 	$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, $bDefault);
	// } 	


	/**
	 * load default currency
	 * 
	 * @return boolean load ok?
	 */
	// public function loadFromDBByIsDefault()
	// {
	// 	$this->clear();
	// 	$this->find(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, true);
	// 	return $this->loadFromDB();
	// }	


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

				//Not started
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Not started'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, true); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');


				//research
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Research'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//work in progress
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Work in progress'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//read through
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Read through'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//up for review
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Up for review'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//needs work
				// $this->newRecord();
				// $this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Needs work'); 
				// $this->set(TPageBuilderDocumentsStatusses::FIELD_ISUNLISTED, false); 					
				// $this->set(TPageBuilderDocumentsStatusses::FIELD_ISPUBLIC, false); 					
				// $this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				// if (!$this->saveToDB())
				// 	logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//ready to publish
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Ready to publish'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');

				//published
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Published'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');					

				//don't know
				$this->newRecord();
				$this->set(TPageBuilderDocumentsStatusses::FIELD_NAME, 'Unknown'); 
				$this->set(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false); 					
				if (!$this->saveToDB())
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving pagebuilder document statusses on install');					

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
		//name
		$this->setFieldDefaultValue(TPageBuilderDocumentsStatusses::FIELD_NAME, '');
		$this->setFieldType(TPageBuilderDocumentsStatusses::FIELD_NAME, CT_VARCHAR);
		$this->setFieldLength(TPageBuilderDocumentsStatusses::FIELD_NAME, 50);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsStatusses::FIELD_NAME, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsStatusses::FIELD_NAME, false);
		$this->setFieldNullable(TPageBuilderDocumentsStatusses::FIELD_NAME, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldUnique(TPageBuilderDocumentsStatusses::FIELD_NAME, true);
		$this->setFieldIndexed(TPageBuilderDocumentsStatusses::FIELD_NAME, false);
		$this->setFieldFulltext(TPageBuilderDocumentsStatusses::FIELD_NAME, true);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsStatusses::FIELD_NAME, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsStatusses::FIELD_NAME, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsStatusses::FIELD_NAME, false);
		$this->setFieldEncryptionDisabled(TPageBuilderDocumentsStatusses::FIELD_NAME);									

		//default
		$this->setFieldDefaultValue(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldType(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, CT_BOOL);
		$this->setFieldLength(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, 0);
		$this->setFieldDecimalPrecision(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, 0);
		$this->setFieldPrimaryKey(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldNullable(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldEnumValues(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldUnique(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldIndexed(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldFulltext(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldForeignKeyClass(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyTable(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyField(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyJoin(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyActionOnUpdate(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldForeignKeyActionOnDelete(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, null);
		$this->setFieldAutoIncrement(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);
		$this->setFieldUnsigned(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT, false);	
        $this->setFieldEncryptionDisabled(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT);	
		
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
		return array(TPageBuilderDocumentsStatusses::FIELD_NAME, TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT);
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
		return APP_DB_TABLEPREFIX.'PagebuilderDocumentStatusses';
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
		return $this->get(TPageBuilderDocumentsStatusses::FIELD_NAME);
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
		return 'hokieooe'.
		$this->get(TPageBuilderDocumentsStatusses::FIELD_NAME).
		'bpol'.
		boolToStr($this->get(TPageBuilderDocumentsStatusses::FIELD_ISDEFAULT));
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
} 
?>