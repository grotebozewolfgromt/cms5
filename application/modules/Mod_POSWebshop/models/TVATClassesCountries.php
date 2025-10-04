<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;

/**
 * VAT classes (Dutch: BTW tarief)
 * The actual percentage of VAT is stored in TVATClassesCountries because you can define percentages of VAT for each country
 * 
 * 
 * 30 apr 2025: TVatClasses
 * 30 may 2025: TVatClasses: createMissingCountries() bugfix
 * 
 * @author Dennis Renirie
 */

class TVATClassesCountries extends TSysModel
{
	const FIELD_VATCLASSESID		= 'sVATClassesID'; //id of the VAT class
	const FIELD_VATPERCENT			= 'dVATPercent'; //percent of VAT applied
	const FIELD_VATPERCENTINTRAEEA	= 'dVATPercentIntraEEA'; //intracommunautaire leveringen binnen de European Economic Area.  this field indicates that transactions processed using this VAT Class are contained within a community (i.e. the EU). The VAT is calculated and recorded as both incoming and outgoing with no net impact on the transaction amounts ==> see: https://rootstock.my.site.com/Trailblazer/s/article/Value-Added-Tax-VAT-Class?language=en_US
	const FIELD_COUNTRYID			= 'iCountryID';//country

	/**
	 * get ID of the VAT class
	 * 
	 * @return string
	 */
	public function getVATClassesID()
	{
		return $this->get(TVATClassesCountries::FIELD_VATCLASSESID);
	}
	
	/**
	 * set invoice type name
	 * 
	 * @param string $sName
	 */
	public function setVATClassesID($iID)
	{
		$this->set(TVATClassesCountries::FIELD_VATCLASSESID, $iID);
	}        
	
	/**
	 * get VAT percent
	 * 
	 * @return TDecimal
	 */
	public function getVATPercent()
	{
		return $this->get(TVATClassesCountries::FIELD_VATPERCENT);
	}
	
	/**
	 * set VAT percent
	 * 
	 * @param TDecimal
	 */
	public function setVATPercent($objPercent)
	{
		$this->set(TVATClassesCountries::FIELD_VATPERCENT, $objPercent);
	}      

	/**
	 * get VAT percent
	 * 
	 * @return TDecimal
	 */
	public function getVATPercentIntraEEA()
	{
		return $this->get(TVATClassesCountries::FIELD_VATPERCENTINTRAEEA);
	}
	
	/**
	 * set VAT percent
	 * 
	 * @param TDecimal
	 */
	public function setVATPercentIntraEEA($objPercent)
	{
		$this->set(TVATClassesCountries::FIELD_VATPERCENTINTRAEEA, $objPercent);
	}       

	/**
	 * get country ID
	 * 
	 * @return integer
	 */
	public function getCountryID()
	{
		return $this->get(TVATClassesCountries::FIELD_COUNTRYID);
	}
	
	/**
	 * set VAT percent
	 * 
	 * @param integer $Id
	 */
	public function setCountryID($iID)
	{
		$this->set(TVATClassesCountries::FIELD_COUNTRYID, $iID);
	}       	


    /**
     * adds countries from TSysCountries to TVATClassesCountries that don't exist yet
	 * 
     * @param integer $iVATClassesID
     */
    public function createMissingCountries($iVATClassesID)
    {

		//load all vatclasscountries of iVATClassesID
        $objVATClassesCountries = new TVATClassesCountries();
        $objVATClassesCountries->select(array(
            TVATClassesCountries::FIELD_ID, 
            TVATClassesCountries::FIELD_COUNTRYID,
                                    ));
		$objVATClassesCountries->find(TVATClassesCountries::FIELD_VATCLASSESID, $iVATClassesID, COMPARISON_OPERATOR_EQUAL_TO); 									
        $objVATClassesCountries->loadFromDB(false);            


		//load all countries except existing ones in vatclasscountries
        $objCountries = new TSysCountries();
        $objCountries->limitNone();//no limit
        $objCountries->select(array(
            TSysCountries::FIELD_ID,
            TSysCountries::FIELD_COUNTRYNAME,
            TSysCountries::FIELD_ISEEA
                                    ));  
        while ($objVATClassesCountries->next()) //exclude countries not associated
            $objCountries->find(TSysCountries::FIELD_ID, $objVATClassesCountries->getCountryID(), COMPARISON_OPERATOR_NOT_EQUAL_TO); 
        $objCountries->loadFromDB(false);  
		

		//add new countries to vatclasscountries
 		$objVATClassesCountries = new TVATClassesCountries();	
		$objVATClassesCountries->startTransaction();								
		while($objCountries->next()) //add new vatclasscountry for each country
		{
			$objVATClassesCountries->newRecord();
			$objVATClassesCountries->setCountryID($objCountries->getID());
			$objVATClassesCountries->setVATClassesID($iVATClassesID);
			$objVATClassesCountries->setVATPercent(new TDecimal('0.0', 4));
			$objVATClassesCountries->setVATPercentIntraEEA(new TDecimal('0.0', 4));
			if (!$objVATClassesCountries->saveToDB(false))
			{
				$objVATClassesCountries->rollbackTransaction();
				return;					
			}
		}
		$objVATClassesCountries->commitTransaction();


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
		//vat class id
		$this->setFieldDefaultValue(TVATClassesCountries::FIELD_VATCLASSESID, 0);
		$this->setFieldType(TVATClassesCountries::FIELD_VATCLASSESID, CT_INTEGER64);
		$this->setFieldLength(TVATClassesCountries::FIELD_VATCLASSESID, 0);
		$this->setFieldDecimalPrecision(TVATClassesCountries::FIELD_VATCLASSESID, 0);
		$this->setFieldPrimaryKey(TVATClassesCountries::FIELD_VATCLASSESID, false);
		$this->setFieldNullable(TVATClassesCountries::FIELD_VATCLASSESID, false);
		$this->setFieldEnumValues(TVATClassesCountries::FIELD_VATCLASSESID, null);
		$this->setFieldUnique(TVATClassesCountries::FIELD_VATCLASSESID, false); 
		$this->setFieldIndexed(TVATClassesCountries::FIELD_VATCLASSESID, false); 
		$this->setFieldFulltext(TVATClassesCountries::FIELD_VATCLASSESID, false); 
		$this->setFieldForeignKeyClass(TVATClassesCountries::FIELD_VATCLASSESID, TVATClasses::class);
		$this->setFieldForeignKeyTable(TVATClassesCountries::FIELD_VATCLASSESID, TVATClasses::getTable());
		$this->setFieldForeignKeyField(TVATClassesCountries::FIELD_VATCLASSESID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TVATClassesCountries::FIELD_VATCLASSESID);
		$this->setFieldForeignKeyActionOnUpdate(TVATClassesCountries::FIELD_VATCLASSESID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TVATClassesCountries::FIELD_VATCLASSESID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldAutoIncrement(TVATClassesCountries::FIELD_VATCLASSESID, false);
		$this->setFieldUnsigned(TVATClassesCountries::FIELD_VATCLASSESID, false);
        $this->setFieldEncryptionDisabled(TVATClassesCountries::FIELD_VATCLASSESID);
		

		//vat percent
		$this->setFieldDefaultValue(TVATClassesCountries::FIELD_VATPERCENT, 0);
		$this->setFieldType(TVATClassesCountries::FIELD_VATPERCENT, CT_DECIMAL);
		$this->setFieldLength(TVATClassesCountries::FIELD_VATPERCENT, 10);
		$this->setFieldDecimalPrecision(TVATClassesCountries::FIELD_VATPERCENT, 4);
		$this->setFieldPrimaryKey(TVATClassesCountries::FIELD_VATPERCENT, false);
		$this->setFieldNullable(TVATClassesCountries::FIELD_VATPERCENT, false);
		$this->setFieldEnumValues(TVATClassesCountries::FIELD_VATPERCENT, null);
		$this->setFieldUnique(TVATClassesCountries::FIELD_VATPERCENT, false); 
		$this->setFieldIndexed(TVATClassesCountries::FIELD_VATPERCENT, false);
		$this->setFieldFulltext(TVATClassesCountries::FIELD_VATPERCENT, false);
		$this->setFieldForeignKeyClass(TVATClassesCountries::FIELD_VATPERCENT, null);
		$this->setFieldForeignKeyTable(TVATClassesCountries::FIELD_VATPERCENT, null);
		$this->setFieldForeignKeyField(TVATClassesCountries::FIELD_VATPERCENT, null);
		$this->setFieldForeignKeyJoin(TVATClassesCountries::FIELD_VATPERCENT);
		$this->setFieldForeignKeyActionOnUpdate(TVATClassesCountries::FIELD_VATPERCENT, null);
		$this->setFieldForeignKeyActionOnDelete(TVATClassesCountries::FIELD_VATPERCENT, null); 
		$this->setFieldAutoIncrement(TVATClassesCountries::FIELD_VATPERCENT, false);
		$this->setFieldUnsigned(TVATClassesCountries::FIELD_VATPERCENT, false);
        $this->setFieldEncryptionDisabled(TVATClassesCountries::FIELD_VATPERCENT);

		//vat percent intra EEA
		$this->setFieldCopyProps(TVATClassesCountries::FIELD_VATPERCENTINTRAEEA, TVATClassesCountries::FIELD_VATPERCENT);
		
		//country id
		$this->setFieldDefaultValue(TVATClassesCountries::FIELD_COUNTRYID, 0);
		$this->setFieldType(TVATClassesCountries::FIELD_COUNTRYID, CT_INTEGER64);
		$this->setFieldLength(TVATClassesCountries::FIELD_COUNTRYID, 0);
		$this->setFieldDecimalPrecision(TVATClassesCountries::FIELD_COUNTRYID, 0);
		$this->setFieldPrimaryKey(TVATClassesCountries::FIELD_COUNTRYID, false);
		$this->setFieldNullable(TVATClassesCountries::FIELD_COUNTRYID, false);
		$this->setFieldEnumValues(TVATClassesCountries::FIELD_COUNTRYID, null);
		$this->setFieldUnique(TVATClassesCountries::FIELD_COUNTRYID, false); 
		$this->setFieldIndexed(TVATClassesCountries::FIELD_COUNTRYID, false); 
		$this->setFieldFulltext(TVATClassesCountries::FIELD_COUNTRYID, false); 
		$this->setFieldForeignKeyClass(TVATClassesCountries::FIELD_COUNTRYID, TSysCountries::class);
		$this->setFieldForeignKeyTable(TVATClassesCountries::FIELD_COUNTRYID, TSysCountries::getTable());
		$this->setFieldForeignKeyField(TVATClassesCountries::FIELD_COUNTRYID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TVATClassesCountries::FIELD_COUNTRYID);
		$this->setFieldForeignKeyActionOnUpdate(TVATClassesCountries::FIELD_COUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TVATClassesCountries::FIELD_COUNTRYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		$this->setFieldAutoIncrement(TVATClassesCountries::FIELD_COUNTRYID, false);
		$this->setFieldUnsigned(TVATClassesCountries::FIELD_COUNTRYID, false);
        $this->setFieldEncryptionDisabled(TVATClassesCountries::FIELD_COUNTRYID);	
		
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
		return array(TVATClassesCountries::FIELD_VATCLASSESID, 
					TVATClassesCountries::FIELD_VATPERCENT,
					TVATClassesCountries::FIELD_COUNTRYID
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
		return APP_DB_TABLEPREFIX.'VATClassesCountries';
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
		return $this->get(TVATClassesCountries::FIELD_VATPERCENT);
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
		return 'w3dd4'.
			$this->getVATClassesID().
			$this->getVATPercent()->getValue().
			'sijeikjf'.
			$this->getCountryID();			
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
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = true;
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		
		//==check if at least one record exists, if not, then add it
		$this->newQuery();
		$this->clear();    
		$this->limitOne(); //we need just one record to be returned
		if (!$this->loadFromDB())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loadFromDB() failed in install()');
			return false;
		}
		
		//==if no record exists, add a default one
		if($this->count() == 0)
		{
			//request default country
			$objCountry = new TSysCountries();
			$objCountry->loadFromDBByIsDefault();
			if ($objCountry->count() == 0)
				return;

			//for each VAT class add the default country
			$objVATClass = new TVATClasses();
			$objVATClass->loadFromDB();			
			while ($objVATClass->next())
			{
				$this->clear();
				$this->newRecord();
				$this->setVATClassesID($objVATClass->getID());
				$this->setVATPercent(new TDecimal('0', 4));
				$this->setVATPercentIntraEEA(new TDecimal('0', 4));
				$this->setCountryID($objCountry->getID());
				
				if (!$this->saveToDB())
				{
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice: high');
					return false;
				}    
			}

		}
		
		return $bSuccess;
	}        	
} 
?>