<?php
namespace dr\classes\models;

use dr\classes\models\TSysModel;


/**
 * This class stores version numbers of databasetables so the update function of 
 * the framework can determine if a table should be updated
 * 
 * This class is designed to replace TTableVersions wich is based on TRecordList
 * 
 * @author dennisrenirie
 * created: 7 sept 2015
 * 
 */
class TSysTableVersions extends TSysModel
{
	const FIELD_TABLENAME = 'sTableName';
	const FIELD_VERSIONNUMBER = 'iVersionNumber';
	
	
    /**
     * gets all the tables in the database, looks if they are present in this table
     * if not, adds them with updatenumber=0
     * 
	 * NOTE: does database action
	 * 
     * @return bool true=successful
     */
    public function synchronizeTablesDB()
    {
        $this->clear();
        
        if ($this->loadFromDB(false)) //als laden goed gegaan
        {
            $arrTablesInDB = $this->getConnectionObject()->getQuery()->getTablesInDatabase();
                                    
            $iCount = count($arrTablesInDB);
            foreach ($arrTablesInDB as $sTable)
            {
                $iLenghtPrefix = strlen(APP_DB_TABLEPREFIX);
                $sPrefixCurrentTable = substr($sTable, 0, $iLenghtPrefix);
                if ($sPrefixCurrentTable == APP_DB_TABLEPREFIX) //only the tables with the right prefix
                {
                    if (!$this->existsValue(TSysTableVersions::FIELD_TABLENAME, $sTable))//als niet bestaat
                    {
                    	$this->newRecord();
                    	$this->set(TSysTableVersions::FIELD_TABLENAME, $sTable);
                    	$this->set(TSysTableVersions::FIELD_VERSIONNUMBER, 0);
                        if (!$this->saveToDB())
                            return false;
                    }
                }
            }
            return true;
        }
        else
            return false;        
    }	
	
	
	
	
	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
         * 
	 * initialize values
	 */
	public function initRecord() {}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{		
		//table name
		$this->setFieldDefaultValue(TSysTableVersions::FIELD_TABLENAME, '');
		$this->setFieldType(TSysTableVersions::FIELD_TABLENAME, CT_VARCHAR);
		$this->setFieldLength(TSysTableVersions::FIELD_TABLENAME, 100);
		$this->setFieldDecimalPrecision(TSysTableVersions::FIELD_TABLENAME, 0);
		$this->setFieldPrimaryKey(TSysTableVersions::FIELD_TABLENAME, false);
		$this->setFieldNullable(TSysTableVersions::FIELD_TABLENAME, false);
		$this->setFieldEnumValues(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldUnique(TSysTableVersions::FIELD_TABLENAME, true);
		$this->setFieldIndexed(TSysTableVersions::FIELD_TABLENAME, false);//it is already unique
		$this->setFieldFulltext(TSysTableVersions::FIELD_TABLENAME, false);//it is already unique
		$this->setFieldForeignKeyClass(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldForeignKeyTable(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldForeignKeyField(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldForeignKeyJoin(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysTableVersions::FIELD_TABLENAME, null);
		$this->setFieldAutoIncrement(TSysTableVersions::FIELD_TABLENAME, false);
		$this->setFieldUnsigned(TSysTableVersions::FIELD_TABLENAME, false);
		$this->setFieldEncryptionDisabled(TSysTableVersions::FIELD_TABLENAME);					

		//version number
		$this->setFieldDefaultValue(TSysTableVersions::FIELD_VERSIONNUMBER, 0);
		$this->setFieldType(TSysTableVersions::FIELD_VERSIONNUMBER, CT_INTEGER64);
		$this->setFieldLength(TSysTableVersions::FIELD_VERSIONNUMBER, 0);
		$this->setFieldDecimalPrecision(TSysTableVersions::FIELD_VERSIONNUMBER, 0);
		$this->setFieldPrimaryKey(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldNullable(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldEnumValues(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldUnique(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldIndexed(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldFulltext(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldForeignKeyClass(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldForeignKeyTable(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldForeignKeyField(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldForeignKeyJoin(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldForeignKeyActionOnDelete(TSysTableVersions::FIELD_VERSIONNUMBER, null);
		$this->setFieldAutoIncrement(TSysTableVersions::FIELD_VERSIONNUMBER, false);
		$this->setFieldUnsigned(TSysTableVersions::FIELD_VERSIONNUMBER, true);
		$this->setFieldEncryptionDisabled(TSysTableVersions::FIELD_VERSIONNUMBER);							
		
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
		return array(TSysTableVersions::FIELD_TABLENAME, TSysTableVersions::FIELD_VERSIONNUMBER);
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
		return APP_DB_TABLEPREFIX.'SysTableVersions';
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
		return $this->get(TSysTableVersions::FIELD_TABLENAME);
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
}
?>
