<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Websites\models;

use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Localisation\models\TSysLanguages;

/**
 * 29 mrt 2016 : TSysWebsites: synchronizeDBWithFileSystem() removed
 * 15 april 2015: TSysWebsites: install() zorgt dat er minimaal 1 website in de db staat
 */

class TSysWebsites extends TSysModel
{
	const FIELD_WEBSITENAME = 'sWebsiteName';
	const FIELD_URL = 'sURL';
	const FIELD_DEFAULTLANGUAGEID = 'iDefaultLanguageID'; //id of the defaultlanguage
		
	const DEFAULT_WEBSITENAME = 'defaultsite';	

	
	/**
	 * get name of the website
	 * 
	 * @return string
	 */
	public function getWebsiteName()
	{
		return $this->get(TSysWebsites::FIELD_WEBSITENAME);
	}

	
	/**
	 * set name of the website
	 * 
	 * @param string $sName
	 */
	public function setWebsiteName($sName)
	{
		$this->set(TSysWebsites::FIELD_WEBSITENAME, $sName);
	}        
	
	/**
	 * get url of website without post url slash (/)
	 * 
	 * @return string
	 */
	public function getURL()
	{
		return $this->get(TSysWebsites::FIELD_URL);
	}

	/**
	 * set url of website without post url slash (/)
	 * 
	 * @param string $sURL
	 */
	public function setURL($sURL)
	{
		$this->set(TSysWebsites::FIELD_URL, $sURL);
	}           
	
	/**
	 * get the id of the default language
	 * 
	 * @return string
	 */
	public function getDefaultLanguageID()
	{
		return $this->get(TSysWebsites::FIELD_DEFAULTLANGUAGEID);
	}

	/**
	 * set the id of the default language
	 * 
	 * @param string $sURL
	 */
	public function setDefaultLangueID($iLanguageID)
	{
		$this->set(TSysWebsites::FIELD_DEFAULTLANGUAGEID, $iLanguageID);
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
					//@todo remove, only here for downwards compatibility with cms2/4
					/*
					$this->clear()->setWebsiteName('beterevenementen.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('quizexperts.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('--delete--');//we need to skip an id
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }
					$this->newQuery();
					$this->findID($this->getID());
					$this->deleteFromDB(true);

					$this->clear()->setWebsiteName('goedkoopbedrijfsuitje.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('wieisdesaboteur.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('bedrijfsuitje.events');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('pubquiz.events');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }                

					$this->clear()->setWebsiteName('--delete--');//we need to skip an id
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }
					$this->newQuery();
					$this->findID($this->getID());
					$this->deleteFromDB(true);

					$this->clear()->setWebsiteName('keizerstadevenementen.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('hertogevenementen.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('dejongenstegendemeisjesquiz.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('opeigenlocatie.nl');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('learnhowtoproducemusic.com');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('dexxterclark.com');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('socialvideoplaza.com');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('cdj2000course.com');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }

					$this->clear()->setWebsiteName('deejayplaza.com');
					$this->setURL('https://www.'.$this->getWebsiteName());
					if (!$this->saveToDB()) { logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install: '. $this->getWebsiteName()); }
					//end: @todo remove
					*/

					//insert default website
					$this->limitOne();
					$this->loadFromDB(false);
					if ($this->count() == 0) //only add when table is empty
					{
						$this->clear();

						$this->newRecord();
						$this->setWebsiteName(TSysWebsites::DEFAULT_WEBSITENAME);
						$this->setURL(dirname(dirname(dirname((APP_URLTHISSCRIPT))))); //probably in install directory
						$this->setIsDefault(true);

						if (!$this->saveToDB())
								logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving website on install');

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
	{
		$this->set(TSysWebsites::FIELD_URL, 'https://www.');
	}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//website name
		$this->setFieldDefaultValue(TSysWebsites::FIELD_WEBSITENAME, '');
		$this->setFieldType(TSysWebsites::FIELD_WEBSITENAME, CT_VARCHAR);
		$this->setFieldLength(TSysWebsites::FIELD_WEBSITENAME, 100);
		$this->setFieldDecimalPrecision(TSysWebsites::FIELD_WEBSITENAME, 0);
		$this->setFieldPrimaryKey(TSysWebsites::FIELD_WEBSITENAME, false);
		$this->setFieldNullable(TSysWebsites::FIELD_WEBSITENAME, false);
		$this->setFieldEnumValues(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldUnique(TSysWebsites::FIELD_WEBSITENAME, true);
		$this->setFieldIndexed(TSysWebsites::FIELD_WEBSITENAME, false);
		$this->setFieldFulltext(TSysWebsites::FIELD_WEBSITENAME, true);
		$this->setFieldForeignKeyClass(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldForeignKeyTable(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldForeignKeyField(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldForeignKeyJoin(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysWebsites::FIELD_WEBSITENAME, null);
		$this->setFieldAutoIncrement(TSysWebsites::FIELD_WEBSITENAME, false);
		$this->setFieldUnsigned(TSysWebsites::FIELD_WEBSITENAME, false);
		$this->setFieldEncryptionDisabled(TSysWebsites::FIELD_WEBSITENAME);									
		
		//url
		$this->setFieldCopyProps(TSysWebsites::FIELD_URL, TSysWebsites::FIELD_WEBSITENAME);
                
		//default language id
		$this->setFieldDefaultValue(TSysWebsites::FIELD_DEFAULTLANGUAGEID, '');
		$this->setFieldType(TSysWebsites::FIELD_DEFAULTLANGUAGEID, CT_INTEGER64);
		$this->setFieldLength(TSysWebsites::FIELD_DEFAULTLANGUAGEID, 0);
		$this->setFieldDecimalPrecision(TSysWebsites::FIELD_DEFAULTLANGUAGEID, 0);
		$this->setFieldPrimaryKey(TSysWebsites::FIELD_DEFAULTLANGUAGEID, false);
		$this->setFieldNullable(TSysWebsites::FIELD_DEFAULTLANGUAGEID, false);
		$this->setFieldEnumValues(TSysWebsites::FIELD_DEFAULTLANGUAGEID, null);
		$this->setFieldUnique(TSysWebsites::FIELD_DEFAULTLANGUAGEID, false);
		$this->setFieldIndexed(TSysWebsites::FIELD_DEFAULTLANGUAGEID, true);
		$this->setFieldFulltext(TSysWebsites::FIELD_DEFAULTLANGUAGEID, false);
		$this->setFieldForeignKeyClass(TSysWebsites::FIELD_DEFAULTLANGUAGEID, TSysLanguages::class);
		$this->setFieldForeignKeyTable(TSysWebsites::FIELD_DEFAULTLANGUAGEID, TSysLanguages::getTable());
		$this->setFieldForeignKeyField(TSysWebsites::FIELD_DEFAULTLANGUAGEID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TSysWebsites::FIELD_DEFAULTLANGUAGEID);
		$this->setFieldForeignKeyActionOnUpdate(TSysWebsites::FIELD_DEFAULTLANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TSysWebsites::FIELD_DEFAULTLANGUAGEID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TSysWebsites::FIELD_DEFAULTLANGUAGEID, false);
		$this->setFieldUnsigned(TSysWebsites::FIELD_DEFAULTLANGUAGEID, true);    
		$this->setFieldEncryptionDisabled(TSysWebsites::FIELD_DEFAULTLANGUAGEID);			  
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
		return array(TSysWebsites::FIELD_WEBSITENAME, TSysWebsites::FIELD_URL, TSysWebsites::FIELD_DEFAULTLANGUAGEID);
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
		return APP_DB_TABLEPREFIX.'SysWebsites';
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
		return $this->get(TSysWebsites::FIELD_WEBSITENAME);
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
		//there needs to be a default language id
		if (!$this->getDefaultLanguageID())
		{
			$objLangs = new TSysLanguages();
			if ($objLangs->loadFromDBByLocale(APP_LOCALE_DEFAULT))
			{
				$this->setDefaultLangueID($objLangs->getID());
			}
			else
			{
				error_log(__CLASS__.' '.__LINE__.' couldnt load default language id for website with id '.$this->getID());
				return false;
			}
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
		return false;
	}		
} 
?>