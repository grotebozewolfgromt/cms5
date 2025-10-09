<?php
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Localisation\models;

use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;
use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Websites\models\TSysWebsites;

/**
 * This class represents languages that are enable per site.
 * For example: mysite.com can be in English and French.
 * This table stores the relationship between the languages and wesites
 * 
 * if a record exist in the table, then the language is enabled for the site
 * */
class TSysActiveLanguagesPerSite extends TSysModel
{
        const FIELD_WEBSITEID   = 'iWebsiteID';
        const FIELD_LANGUAGEID  = 'iLanguageID'; 

        public function getWebsiteID()
        {
                return $this->get(TSysActiveLanguagesPerSite::FIELD_WEBSITEID);
        }

        public function setWebsiteID($iID)
        {
                $this->set(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, $iID);
        }
        
        public function getLanguageID()
        {
                return $this->get(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID);
        }
        
        public function setLanguageID($iID)
        {
                $this->set(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, $iID);
        }

        
        /**
         * 
         * @return boolean load ok?
         */
        public function loadFromDBByLanguageID($iID)
        {              
                $this->clear();
                $this->find(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, $iID);
                return $this->loadFromDB();                
        }
        
        /**
         * 
         * @param string $sLocale
         * @return boolean load ok?
         */
        public function loadFromDBByWebsiteID($iID)
        {
                $this->clear();
                $this->find(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, $iID);
                return $this->loadFromDB();
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
		
                //==== create default language for each site
		if ($bSuccess)
		{
                        $this->clear();
                        $objWebsites = new TSysWebsites();    
                        $objWebsites->loadFromDB();
                        $objCloneActiveLangs = clone $this;
                    
                        //first retrieve languageid of default locale
                        $objLangs = new TSysLanguages();
                        if ($objLangs->loadFromDBByLocale(APP_LOCALE_DEFAULT))
                        {
                        
                            while ($objWebsites->next())
                            {
                                if ($objCloneActiveLangs->loadFromDBByWebsiteID($objWebsites->getID()))
                                {

                                    if ($objCloneActiveLangs->count() == 0) //when no languages are added for this site, create default
                                    {
                                            $this->newRecord();
                                            $this->set(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, $objWebsites->getID());
                                            $this->set(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, $objLangs->getID());

                                            if (!$this->saveToDB())
                                            {       
                                                    $bSuccess = false;
                                                    logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.' '.__LINE__.' error saving language-per-site on install');                                        
                                            }
                                    }
                                }
                                else//end: loadFromDBByWebsiteID
                                {
                                        $bSuccess = false;
                                        error_log(__CLASS__.' '.__LINE__.' loadFromDBByWebsiteID() failed');
                                }
                            } //end: while $objWebsites->next()
                        }
                        else
                        {
                                $bSuccess = false;
                                error_log(__CLASS__.' '.__LINE__.' loadFromDBByLocale() failed');
                        }
                        
                        unset($objCloneActiveLangs);
                        unset($objLangs);
                        unset($objWebsites);
		}//end: if bSuccess
                
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
            
		//language id
		$this->setFieldDefaultValue(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, '');
		$this->setFieldType(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, CT_INTEGER64);
		$this->setFieldLength(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, 0);
		$this->setFieldDecimalPrecision(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, 0);
		$this->setFieldPrimaryKey(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, false);
		$this->setFieldNullable(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, false);
		$this->setFieldEnumValues(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, null);
		$this->setFieldUnique(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, false);
		$this->setFieldIndexed(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, true);
		$this->setFieldFulltext(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, false);
		$this->setFieldForeignKeyClass(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, TSysLanguages::class);
		$this->setFieldForeignKeyTable(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, TSysLanguages::getTable());
		$this->setFieldForeignKeyField(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID);
		$this->setFieldForeignKeyActionOnUpdate(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, false);
		$this->setFieldUnsigned(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID, true);
		$this->setFieldEncryptionDisabled(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID);

		
		//website id
		$this->setFieldDefaultValue(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, '');
		$this->setFieldType(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, CT_INTEGER64);
		$this->setFieldLength(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, 0);
		$this->setFieldDecimalPrecision(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, 0);
		$this->setFieldPrimaryKey(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, false);
		$this->setFieldNullable(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, false);
		$this->setFieldEnumValues(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, null);
		$this->setFieldUnique(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, false);
		$this->setFieldIndexed(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, true);
		$this->setFieldFulltext(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, false);
		$this->setFieldForeignKeyClass(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysWebsites::class);
		$this->setFieldForeignKeyTable(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysWebsites::getTable());
		$this->setFieldForeignKeyField(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TSysActiveLanguagesPerSite::FIELD_WEBSITEID);
		$this->setFieldForeignKeyActionOnUpdate(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, false);
		$this->setFieldUnsigned(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, true);       
		$this->setFieldEncryptionDisabled(TSysActiveLanguagesPerSite::FIELD_WEBSITEID);

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
		return array(TSysActiveLanguagesPerSite::FIELD_WEBSITEID, TSysActiveLanguagesPerSite::FIELD_LANGUAGEID);
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
		return APP_DB_TABLEPREFIX.'SysActiveLanguagesPerSite';
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
		return $this->get(TSysActiveLanguagesPerSite::FIELD_WEBSITEID).' '.$this->get(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID).'';
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
		return '6h4'.$this->get(TSysActiveLanguagesPerSite::FIELD_WEBSITEID).'appglapot'.$this->get(TSysActiveLanguagesPerSite::FIELD_LANGUAGEID);
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