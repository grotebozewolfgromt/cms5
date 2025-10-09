<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

/**
 * VAT classes (Dutch: BTW tarief)
 * The actual percentage of VAT is stored in TVATClassesCountries because you can define percentages of VAT for each country
 * 
 * 
 * 30 apr 2025: TVatClasses
 * 
 * @author Dennis Renirie
 */

class TProductsLanguages extends TSysModel
{
	const FIELD_PRODUCTID			= 'iProductID'; 
	const FIELD_NAME				= 'sName'; 
	const FIELD_NAMESHORT			= 'sNameShort'; //for small receipts it's useful to have a short name
	const FIELD_DESCRIPTION			= 'sDescription';
	const FIELD_CONTENTSBOX			= 'sContentsBox';
	const FIELD_BRAND				= 'sBrand'; 
	const FIELD_MANUFACTURER		= 'sManufacturer'; 
	const FIELD_INFOPOPUP			= 'sInfoPopup';

	/**
	 * get product id
	 * 
	 * @return string
	 */
	public function getLanguageID()
	{
		return $this->get(TProductsLanguages::FIELD_PRODUCTID);
	}
	
	/**
	 * set product id
	 * 
	 * @param integer $iID
	 */
	public function setProductID($iID)
	{
		$this->set(TProductsLanguages::FIELD_PRODUCTID, $iID);
	}    

	/**
	 * get name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get(TProductsLanguages::FIELD_NAME);
	}
	
	/**
	 * set name
	 * 
	 * @param string $sManufacturer
	 */
	public function setName($sName)
	{
		$this->set(TProductsLanguages::FIELD_NAME, $sName);
	}   

	/**
	 * get short name
	 * 
	 * @return boolean
	 */
	public function getNameShort()
	{
		return $this->get(TProductsLanguages::FIELD_NAMESHORT);
	}
	
	/**
	 * set short name
	 * 
	 * @param string $sName
	 */
	public function setNameShort($sName)
	{
		$this->set(TProductsLanguages::FIELD_NAMESHORT, $sName);
	}        

	/**
	 * get product description
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->get(TProductsLanguages::FIELD_DESCRIPTION);
	}
	
	/**
	 * set product description
	 * 
	 * @param string $sDescription
	 */
	public function setDescription($sDescription)
	{
		$this->set(TProductsLanguages::FIELD_DESCRIPTION, $sDescription);
	}   	


	/**
	 * get contents box
	 * 
	 * @return string
	 */
	public function getContentsBox()
	{
		return $this->get(TProductsLanguages::FIELD_CONTENTSBOX);
	}
	
	/**
	 * set contents box
	 * 
	 * @param string $sContents
	 */
	public function setContentsBox($sContents)
	{
		$this->set(TProductsLanguages::FIELD_CONTENTSBOX, $sContents);
	}    

	/**
	 * get brand
	 * 
	 * @return string
	 */
	public function getBrand()
	{
		return $this->get(TProductsLanguages::FIELD_BRAND);
	}
	
	/**
	 * set brand
	 * 
	 * @param string $sBrand
	 */
	public function setBrand($sBrand)
	{
		$this->set(TProductsLanguages::FIELD_BRAND, $sBrand);
	}  

  

	/**
	 * get manufacturer
	 * 
	 * @return string
	 */
	public function getManufacturer()
	{
		return $this->get(TProductsLanguages::FIELD_MANUFACTURER);
	}
	
	/**
	 * set manufacturer
	 * 
	 * @param string $sManufacturer
	 */
	public function setManufacturer($sManufacturer)
	{
		$this->set(TProductsLanguages::FIELD_MANUFACTURER, $sManufacturer);
	}   	

	/**
	 * get info popup message for user
	 * 
	 * @return string
	 */
	public function getInfoPopup()
	{
		return $this->get(TProductsLanguages::FIELD_INFOPOPUP);
	}
	
	/**
	 * set info popup message for user
	 * 
	 * @param string $sMessage
	 */
	public function setInfoPopup($sMessage)
	{
		$this->set(TProductsLanguages::FIELD_INFOPOPUP, $sMessage);
	} 

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
		//product id
		$this->setFieldDefaultsIntegerForeignKey(TProductsLanguages::FIELD_PRODUCTID, TProducts::class, TProducts::getTable(), TProducts::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TProductsLanguages::FIELD_PRODUCTID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TProductsLanguages::FIELD_PRODUCTID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//product name
		$this->setFieldDefaultsVarChar(TProductsLanguages::FIELD_NAME, 255);

		//product name short
		$this->setFieldDefaultsVarChar(TProductsLanguages::FIELD_NAMESHORT, 20);

		//product description
		$this->setFieldDefaultsLongText(TProductsLanguages::FIELD_DESCRIPTION);

		//contents box
		$this->setFieldDefaultsLongText(TProductsLanguages::FIELD_CONTENTSBOX);

		//brand
		$this->setFieldDefaultsVarChar(TProductsLanguages::FIELD_BRAND, 100);

		//manufacturer
		$this->setFieldDefaultsVarChar(TProductsLanguages::FIELD_MANUFACTURER, 100);

		//info popup
		$this->setFieldDefaultsVarChar(TProductsLanguages::FIELD_INFOPOPUP, 100);	


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
		return array(TProductsLanguages::FIELD_NAME, 
			TProductsLanguages::FIELD_NAMESHORT, 
			TProductsLanguages::FIELD_DESCRIPTION, 
			TProductsLanguages::FIELD_CONTENTSBOX, 
			TProductsLanguages::FIELD_BRAND, 
			TProductsLanguages::FIELD_MANUFACTURER,
			TProductsLanguages::FIELD_INFOPOPUP
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
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'ProductsLanguages';
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
		return $this->get(TProductsLanguages::FIELD_NAME);
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
		return 'xdcffl;45r'.
			$this->getBrand().
			$this->getManufacturer().
			$this->getInfoPopup().
			$this->getDescription().
			$this->getContentsBox().
			'0fcdjkfr';			
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
		return true;
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
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = true;
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		return $bSuccess;
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