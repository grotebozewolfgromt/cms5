<?php


// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\classes\models\TSysModel;
use dr\classes\patterns\TModuleAbstract;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

/** 
 * TSysCMSPermissionsCountries is a whitelist of countries that 
 * are permitted to use the CMS.
 * 
 * This class has no relation with TSysCMSPermissions!
 * 
 * TSysCMSPermissionsCountries created 3 march 2022
 * 
 * 
 * @author drenirie
 */
class TSysCMSPermissionsCountries extends TSysModel
{
    const FIELD_COUNTRYID     = 'iCountryID';
    
            
    public function getCountryID()
    {
        return $this->get(TSysCMSPermissionsCountries::FIELD_COUNTRYID);
    }
    
    public function setCountryID($iID)
    {
        $this->set(TSysCMSPermissionsCountries::FIELD_COUNTRYID, $iID);
    }

    /**
     * looks in database if country already exists in database
     * this function excludes the current record 
     * (it looks at all records except with current id if it's an existing record)
     *  
     * @param int $iCountryID
     */
    public function doesCountryExistDB($iCountryID)
    {
        $bResult = false;
        $objClone = clone $this;
        $objClone->clear();

        //exclude current record
        if (!$this->getNew())
            $objClone->find(TSysModel::FIELD_ID, $this->getID(), COMPARISON_OPERATOR_NOT_EQUAL_TO);
		$objClone->find(TSysCMSPermissionsCountries::FIELD_COUNTRYID, $iCountryID);			
        

        if ($objClone->loadFromDB())
        {
            if ($objClone->count() > 0) //username taken
                $bResult = true;
        }
        
        unset($objClone);
        return $bResult;        
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
	
        //== add default country
        $objCountries = new TSysCountries();
		$objCountries->find(TSysCountries::FIELD_ISDEFAULT, true);
        if ($objCountries->loadFromDB())
		{
			//add new when not exists
			if (!$this->recordExistsTableDB(TSysCMSPermissionsCountries::FIELD_COUNTRYID, $objCountries->getID()))
			{
				$objClone = clone $this;
				$objClone->clear();
				$objClone->setCountryID($objCountries->getID());
				$objClone->saveToDB();
			}
		}
		else
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, ': objCountries->loadFromDB() failed');
            $bSuccess = false;
        }   

		unset($objCountries);
		unset($objClone);
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
        //country id		
        $this->setFieldDefaultValue(TSysCMSPermissionsCountries::FIELD_COUNTRYID, '');
        $this->setFieldType(TSysCMSPermissionsCountries::FIELD_COUNTRYID, CT_INTEGER64);
        $this->setFieldLength(TSysCMSPermissionsCountries::FIELD_COUNTRYID, 0);
        $this->setFieldDecimalPrecision(TSysCMSPermissionsCountries::FIELD_COUNTRYID, 0);
        $this->setFieldPrimaryKey(TSysCMSPermissionsCountries::FIELD_COUNTRYID, false);
        $this->setFieldNullable(TSysCMSPermissionsCountries::FIELD_COUNTRYID, false);
        $this->setFieldEnumValues(TSysCMSPermissionsCountries::FIELD_COUNTRYID, null);
        $this->setFieldUnique(TSysCMSPermissionsCountries::FIELD_COUNTRYID, false);
        $this->setFieldIndexed(TSysCMSPermissionsCountries::FIELD_COUNTRYID, true);
        $this->setFieldFulltext(TSysCMSPermissionsCountries::FIELD_COUNTRYID, false);
        $this->setFieldForeignKeyClass(TSysCMSPermissionsCountries::FIELD_COUNTRYID, TSysCountries::class);
        $this->setFieldForeignKeyTable(TSysCMSPermissionsCountries::FIELD_COUNTRYID, TSysCountries::getTable());
        $this->setFieldForeignKeyField(TSysCMSPermissionsCountries::FIELD_COUNTRYID, TSysModel::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysCMSPermissionsCountries::FIELD_COUNTRYID);
        $this->setFieldForeignKeyActionOnUpdate(TSysCMSPermissionsCountries::FIELD_COUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysCMSPermissionsCountries::FIELD_COUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldAutoIncrement(TSysCMSPermissionsCountries::FIELD_COUNTRYID, false);
        $this->setFieldUnsigned(TSysCMSPermissionsCountries::FIELD_COUNTRYID, true);
        $this->setFieldEncryptionDisabled(TSysCMSPermissionsCountries::FIELD_COUNTRYID);		
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
		return array(TSysCMSPermissionsCountries::FIELD_COUNTRYID);
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
		return APP_DB_TABLEPREFIX.'SysCMSPermissionsCountries';
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
		return $this->get(TSysCMSPermissionsCountries::FIELD_ID);
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
