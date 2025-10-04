<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_POSWebshop\models\TVATClasses;

/**
 * VAT classes (Dutch: BTW tarief)
 * The actual percentage of VAT is stored in TVATClassesCountries because you can define percentages of VAT for each country
 * 
 * 
 * 30 apr 2025: TVatClasses
 * 
 * @author Dennis Renirie
 */

class TProducts extends TSysModel
{
	const FIELD_TRACKSTOCK						= 'bTrackStock';
	const FIELD_ASKSERIALNUMBER					= 'bAskSerialNumber';
	const FIELD_MANUFACTURERCOUNTRYID			= 'iManufacturerCountryID';
	const FIELD_AVAILABLESALEFROM				= 'dtAvailableSaleFrom'; //date from which a product is available for sale, null = never/not available
	const FIELD_SALESPRICEBASEEXCLVAT			= 'dSalesPriceBaseExclVAT'; //base salesprice excluding vat. There is a "Base"-price and an "Impact"-price for each individual SKU
	const FIELD_ISPRICEDISCOUNTED				= 'bIsPriceDiscounted'; //is product available at discounted price?
	const FIELD_DISCOUNTSTART					= 'dtDiscountStart';//when does the discounted price start?
	const FIELD_DISCOUNTEND						= 'dtDiscountEnd';//when does the discounted price end?
	const FIELD_DISCOUNTSALESPRICEBASEEXCLVAT	= 'dDiscountSalesPriceBaseExclVAT';//what is the discounted price
	const FIELD_VATCLASSESID					= 'iVATClassesID';


	/**
	 * get track stock
	 * 
	 * @return boolean
	 */
	public function getTrackStock()
	{
		return $this->get(TProducts::FIELD_TRACKSTOCK);
	}
	
	/**
	 * set track stock
	 * 
	 * @param boolean $bTrack
	 */
	public function setTrackStock($bTrack)
	{
		$this->set(TProducts::FIELD_TRACKSTOCK, $bTrack);
	}          	


	/**
	 * get ask user for serial number
	 * 
	 * @return boolean
	 */
	public function getAskSerialNumber()
	{
		return $this->get(TProducts::FIELD_ASKSERIALNUMBER);
	}
	
	/**
	 * set ask user for serial number
	 * 
	 * @param boolean $bTrack
	 */
	public function setAskSerialNumber($bTrack)
	{
		$this->set(TProducts::FIELD_ASKSERIALNUMBER, $bTrack);
	}    

	/**
	 * get manufacturer
	 * 
	 * @return integer
	 */
	public function getManufacturerCountryID()
	{
		return $this->get(TProducts::FIELD_MANUFACTURERCOUNTRYID);
	}
	
	/**
	 * set manufacturer
	 * 
	 * @param integer $iCountryID
	 */
	public function setManufacturerCountryID($iCountryID)
	{
		$this->set(TProducts::FIELD_MANUFACTURERCOUNTRYID, $iCountryID);
	}        

	/**
	 * get available for sale from date
	 * 
	 * @return integer
	 */
	public function getAvailableSaleFrom()
	{
		return $this->get(TProducts::FIELD_AVAILABLESALEFROM);
	}
	
	/**
	 * set available for sale from date
	 * 
	 * @param TDateTime $objDateTime
	 */
	public function setAvailableSaleFrom($objDateTime)
	{
		$this->set(TProducts::FIELD_AVAILABLESALEFROM, $objDateTime);
	}     

	/**
	 * get sales price
	 * 
	 * @return TDecimal
	 */
	public function getSalesPriceBaseExclVAT()
	{
		return $this->get(TProducts::FIELD_SALESPRICEBASEEXCLVAT);
	}
	
	/**
	 * set available for sale from date
	 * 
	 * @param TDecimal $objCurrPrice
	 */
	public function setSalesPriceBaseExclVAT($objPrice)
	{
		$this->set(TProducts::FIELD_SALESPRICEBASEEXCLVAT, $objPrice);
	}     	


	/**
	 * get vat classes id
	 * 
	 * @return integer
	 */
	public function getVATClassesID()
	{
		return $this->get(TProducts::FIELD_VATCLASSESID);
	}
	
	/**
	 * set vat classes id
	 * 
	 * @param integer $iID
	 */
	public function setVATClassesID($iID)
	{
		$this->set(TProducts::FIELD_VATCLASSESID, $iID);
	}     	


	/**
	 * get if product is available for a discounted price
	 * 
	 * @return boolean
	 */
	public function getIsPriceDiscounted()
	{
		return $this->get(TProducts::FIELD_ISPRICEDISCOUNTED);
	}	

	/**
	 * set if product is available for a discounted price
	 * 
	 * @param boolean $bOnSale
	 */
	public function setIsPriceDiscounted($bOnSale)
	{
		$this->set(TProducts::FIELD_ISPRICEDISCOUNTED, $bOnSale);
	}    

	/**
	 * get start date of sale
	 * 
	 * @return TDateTime
	 */
	public function getDiscountStart()
	{
		return $this->get(TProducts::FIELD_DISCOUNTSTART);
	}	

	/**
	 * set start date of sale
	 * 
	 * @param TDateTime $dtDate
	 */
	public function setDiscountStart($dtDate)
	{
		$this->set(TProducts::FIELD_DISCOUNTSTART, $dtDate);
	}  

	/**
	 * get end date of sale
	 * 
	 * @return TDateTime
	 */
	public function getDiscountEnd()
	{
		return $this->get(TProducts::FIELD_DISCOUNTEND);
	}	

	/**
	 * set end date of sale
	 * 
	 * @param TDateTime $dtDate
	 */
	public function setDiscountEnd($dtDate)
	{
		$this->set(TProducts::FIELD_DISCOUNTEND, $dtDate);
	}    
	
	/**
	 * get sales price including VAT when product is available at discounted price
	 * 
	 * @return TDecimal
	 */
	public function getDiscountSalesPriceBaseExclVAT()
	{
		return $this->get(TProducts::FIELD_DISCOUNTSALESPRICEBASEEXCLVAT);
	}	


	/**
	 * set sales price including VAT when product is available at discounted price
	 * 
	 * @param TDecimal $objCurrency
	 */
	public function setDiscountSalesPriceBaseExclVAT($objCurrency)
	{
		$this->set(TProducts::FIELD_DISCOUNTSALESPRICEBASEEXCLVAT, $objCurrency);
	}    	

	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
     * 
	 * initialize values
	 */
	public function initRecord()
	{
		// $this->setName("new ".date("Y-m-d H:i:s")); //preventing empy name being written to database resulting in duplicate name when this happened before
	}
		
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{
		//track stock
		$this->setFieldDefaultsBoolean(TProducts::FIELD_TRACKSTOCK);

		//ask serial number
		$this->setFieldDefaultsBoolean(TProducts::FIELD_ASKSERIALNUMBER);		

		//manufacturer country id
		$this->setFieldDefaultsIntegerForeignKey(TProducts::FIELD_MANUFACTURERCOUNTRYID, TSysCountries::class,  TSysCountries::getTable(), TSysCountries::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TProducts::FIELD_MANUFACTURERCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TProducts::FIELD_MANUFACTURERCOUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//date product available for sale
		$this->setFieldDefaultsTDateTime(TProducts::FIELD_AVAILABLESALEFROM, true);	

		//sales price
		$this->setFieldDefaultsTDecimal(TProducts::FIELD_SALESPRICEBASEEXCLVAT, 13, 4);	

		//vat classes id
		$this->setFieldDefaultsIntegerForeignKey(TProducts::FIELD_VATCLASSESID, TVATClasses::class, TVATClasses::getTable(), TVATClasses::FIELD_ID);
		$this->setFieldForeignKeyActionOnUpdate(TProducts::FIELD_VATCLASSESID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TProducts::FIELD_VATCLASSESID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);

		//discounted sales price
		$this->setFieldDefaultsBoolean(TProducts::FIELD_ISPRICEDISCOUNTED);

		//discount start
		$this->setFieldDefaultsTDateTime(TProducts::FIELD_DISCOUNTSTART, true);
		
		//discount end
		$this->setFieldDefaultsTDateTime(TProducts::FIELD_DISCOUNTEND, true);

		//discounted sales price
		$this->setFieldDefaultsTDecimalCurrency(TProducts::FIELD_DISCOUNTSALESPRICEBASEEXCLVAT);		
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
		return array( 
			TProducts::FIELD_TRACKSTOCK, 
			TProducts::FIELD_ASKSERIALNUMBER, 
			TProducts::FIELD_MANUFACTURERCOUNTRYID,
			TProducts::FIELD_SALESPRICEBASEEXCLVAT,
			TProducts::FIELD_DISCOUNTSALESPRICEBASEEXCLVAT,
			TProducts::FIELD_DISCOUNTSTART,
			TProducts::FIELD_DISCOUNTEND,
			TProducts::FIELD_ISPRICEDISCOUNTED,
			TProducts::FIELD_VATCLASSESID
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
		return APP_DB_TABLEPREFIX.'Products';
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
		return $this->get(TProducts::FIELD_ID);
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
		return 'd3dhj_;45r'.
			$this->getTrackStock().
			$this->getManufacturerCountryID().
			boolToStr($this->getTrackStock()).
			boolToStr($this->getIsPriceDiscounted()).
			$this->getSalesPriceBaseExclVAT()->getValue().
			$this->getDiscountSalesPriceBaseExclVAT()->getValue().
			'd46t5hw';			
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
		return true;
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