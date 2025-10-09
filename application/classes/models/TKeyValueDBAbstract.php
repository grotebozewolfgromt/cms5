<?php
namespace dr\classes\models;

use dr\classes\models\TSysModel;

/**
 * This class represents a key-value pair in the database
 */
abstract class TKeyValueDBAbstract extends TSysModel
{
        const FIELD_KEY = 'sKey';
		const FIELD_VALUE = 'sValue'; 
		const FIELD_TYPE = 'sType';


        public function getKey()
        {
                return $this->get(TKeyValueDBAbstract::FIELD_KEY);
        }

        public function setKey($sKey)
        {
                $this->set(TKeyValueDBAbstract::FIELD_KEY, $sKey);
        }
        
        public function getValue()
        {
                return $this->get(TKeyValueDBAbstract::FIELD_VALUE);
        }
        
        public function setValue($sValue)
        {
                $this->set(TKeyValueDBAbstract::FIELD_VALUE, $sValue);
        }
		
		/**
		 * what type is the value?
		 * these are the TP_ values defined in libtypedef,
		 * for example: TP_STRING
		 */
        public function getType()
        {
                return  $this->get(TKeyValueDBAbstract::FIELD_TYPE);
        }
		
		/**
		 * Set type of the value
		 * these are the TP_ values defined in libtypedef,
		 * for example: TP_STRING
		 */
        public function setType($sType = TP_STRING)
        {
                $this->set(TKeyValueDBAbstract::FIELD_TYPE, $sType);
        }        
                
	
	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
         * 
	 * initialize values
	 */
	public function initRecord()
	{
		// $this->setType(TP_STRING);
	}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
            
		//variable/key
		$this->setFieldDefaultValue(TKeyValueDBAbstract::FIELD_KEY, '');
		$this->setFieldType(TKeyValueDBAbstract::FIELD_KEY, CT_VARCHAR);
		$this->setFieldLength(TKeyValueDBAbstract::FIELD_KEY, 255);
		$this->setFieldDecimalPrecision(TKeyValueDBAbstract::FIELD_KEY, 0);
		$this->setFieldPrimaryKey(TKeyValueDBAbstract::FIELD_KEY, false);
		$this->setFieldNullable(TKeyValueDBAbstract::FIELD_KEY, false);
		$this->setFieldEnumValues(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldUnique(TKeyValueDBAbstract::FIELD_KEY, true);
		$this->setFieldForeignKeyClass(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldForeignKeyTable(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldForeignKeyField(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldForeignKeyJoin(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldForeignKeyActionOnUpdate(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldForeignKeyActionOnDelete(TKeyValueDBAbstract::FIELD_KEY, null);
		$this->setFieldAutoIncrement(TKeyValueDBAbstract::FIELD_KEY, false);
		$this->setFieldUnsigned(TKeyValueDBAbstract::FIELD_KEY, false);
		$this->setFieldEncryptionDisabled(TKeyValueDBAbstract::FIELD_KEY);				         		

		//value
		$this->setFieldDefaultValue(TKeyValueDBAbstract::FIELD_VALUE, '');
		$this->setFieldType(TKeyValueDBAbstract::FIELD_VALUE, );
		$this->setFieldLength(TKeyValueDBAbstract::FIELD_VALUE, 0);
		$this->setFieldDecimalPrecision(TKeyValueDBAbstract::FIELD_VALUE, 0);
		$this->setFieldPrimaryKey(TKeyValueDBAbstract::FIELD_VALUE, false);
		$this->setFieldNullable(TKeyValueDBAbstract::FIELD_VALUE, true);
		$this->setFieldEnumValues(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldUnique(TKeyValueDBAbstract::FIELD_VALUE, false);
		$this->setFieldForeignKeyClass(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldForeignKeyTable(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldForeignKeyField(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldForeignKeyJoin(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldForeignKeyActionOnUpdate(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldForeignKeyActionOnDelete(TKeyValueDBAbstract::FIELD_VALUE, null);
		$this->setFieldAutoIncrement(TKeyValueDBAbstract::FIELD_VALUE, false);
		$this->setFieldUnsigned(TKeyValueDBAbstract::FIELD_VALUE, false);
		$this->setFieldEncryptionDisabled(TKeyValueDBAbstract::FIELD_VALUE);				         				

		//type
		$this->setFieldDefaultValue(TKeyValueDBAbstract::FIELD_TYPE, 0);
		$this->setFieldType(TKeyValueDBAbstract::FIELD_TYPE, CT_INTEGER32);
		$this->setFieldLength(TKeyValueDBAbstract::FIELD_TYPE, 0);
		$this->setFieldDecimalPrecision(TKeyValueDBAbstract::FIELD_TYPE, 0);
		$this->setFieldPrimaryKey(TKeyValueDBAbstract::FIELD_TYPE, false);
		$this->setFieldNullable(TKeyValueDBAbstract::FIELD_TYPE, false);
		$this->setFieldEnumValues(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldUnique(TKeyValueDBAbstract::FIELD_TYPE, false);
		$this->setFieldForeignKeyClass(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldForeignKeyTable(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldForeignKeyField(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldForeignKeyJoin(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldForeignKeyActionOnUpdate(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldForeignKeyActionOnDelete(TKeyValueDBAbstract::FIELD_TYPE, null);
		$this->setFieldAutoIncrement(TKeyValueDBAbstract::FIELD_TYPE, false);
		$this->setFieldUnsigned(TKeyValueDBAbstract::FIELD_TYPE, false);
		$this->setFieldEncryptionDisabled(TKeyValueDBAbstract::FIELD_TYPE);				         						

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
		return array(TKeyValueDBAbstract::FIELD_KEY, TKeyValueDBAbstract::FIELD_VALUE);
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
		return true;
	}        
		
	/**
	 * use image in your record?
         * if you don't want a small and large version, use this one
	*/
	public function getTableUseImage()
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
	
	
	// /**
	//  * de child moet deze overerven
	//  *
	//  * @return string naam van de databasetabel
	// */
	// public static function getTable()
	// {
	// 	return APP_DB_TABLEPREFIX.'SysSettings';
	// }
	
	
	
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
		return $this->get(TKeyValueDBAbstract::FIELD_KEY).' = '.$this->get(TKeyValueDBAbstract::FIELD_VALUE).'';
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
		return 'henkieisgek'.$this->get(TKeyValueDBAbstract::FIELD_KEY).'maarnietheus'.$this->get(TKeyValueDBAbstract::FIELD_VALUE).'echtwel';
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

	/********************************************************************
	 * ABSTRACT FUNCTIONS
	 * ******************************************************************
	 */

	 /**
	  * should this class use the 'type' field?
	  *
	  * @return bool
	  */
	abstract public function getUseType();

}

?>