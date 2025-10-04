<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDecimal;


/**
 * Transactions Lines
 * 
 * TInvoicesLines representing the actual lines on an invoice (TInvoice)
 * 
 * 
 * created 21 october 2023
 * 24 oct 2023: TInvoicesLines: created
 * 26 oct 2023: TInvoicesLines: finished
 * 3 nov 2023: TInvoicesLines renamed to TTransactionsLines
 * 
 * @author Dennis Renirie
 */

class TTransactionsLines extends TSysModel
{
	const FIELD_TRANSACTIONSID 				= 'iTransactionsID'; //the id of the parent invoice record
	const FIELD_QUANTITY					= 'dQuantity'; //TDecimal: amount of good/serviceconsumed
	const FIELD_DESCRIPTION 				= 'sDescription'; //description of the good/service
	const FIELD_UNITPRICEEXCLVAT			= 'dUnitPriceExclVAT'; //TCurrency: price per unit
	const FIELD_UNITPURCHASEPRICEEXCLVAT	= 'dUnitPurchasePriceExclVAT'; //TCurrency: purchase price per unit
	const FIELD_VATPERCENTAGE				= 'dVATPercentage'; //TDecimal vat percentage
	const FIELD_UNITDISCOUNTEXCLVAT			= 'dUnitDiscountExclVAT'; //TDecimal amount of discount deducted from unit price


	// const FIELD_META_TOTALPRICEINCLVAT	= 'crTotalPriceInclVAT'; //calculated total amount of invoice INCLUDING VAT
	// const FIELD_META_TOTALPRICEEXCLVAT	= 'crTotalPriceExclVAT'; //calculated total amount of invoice EXCLUDING VAT
	// const FIELD_META_TOTALVAT			= 'crTotalVAT'; //calculated total amount VAT on invoice
	

	/**
	 * get transaction id
	 * 
	 * @return int
	 */
	public function getTransactionsID()
	{
		return $this->get(TTransactionsLines::FIELD_TRANSACTIONSID);
	}
	
	/**
	 * set transaction id
	 * 
	 * @param int $iTrxID
	 */
	public function setTransactionsID($iTrxID)
	{
		$this->set(TTransactionsLines::FIELD_TRANSACTIONSID, $iTrxID);
	}        
	

	/**
	 * get quantity 
	 * (the amount of a product that is sold)
	 * 
	 * @return TDecimal
	 */
	public function getQuantity()
	{
		return $this->get(TTransactionsLines::FIELD_QUANTITY);
	}
	
	/**
	 * set quantity
	 * (the amount of a product that is sold)
	 * 
	 * @param TDecimal $objQuantity
	 */
	public function setQuantity($objQuantity)
	{
		$this->set(TTransactionsLines::FIELD_QUANTITY, $objQuantity);
	} 


	/**
	 * get product description
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->get(TTransactionsLines::FIELD_DESCRIPTION);
	}
	
	/**
	 * set description
	 * 
	 * @param string $sDescription
	 */
	public function setDescription($sDescription)
	{
		$this->set(TTransactionsLines::FIELD_DESCRIPTION, $sDescription);
	} 	
		


	/**
	 * get unit price
	 * 
	 * @return TDecimal
	 */
	public function getUnitPriceExclVAT()
	{
		return $this->get(TTransactionsLines::FIELD_UNITPRICEEXCLVAT);
	}

	/**
	 * set unit price
	 * 
	 * @param TDecimal $objPrice
	 */
	public function setUnitPriceExclVAT($objPrice)
	{
		$this->set(TTransactionsLines::FIELD_UNITPRICEEXCLVAT, $objPrice);
	} 		


	/**
	 * get unit purchase price
	 * 
	 * @return TDecimal
	 */
	public function getUnitPurchasePriceExclVAT()
	{
		return $this->get(TTransactionsLines::FIELD_UNITPURCHASEPRICEEXCLVAT);
	}

	/**
	 * set unit price
	 * 
	 * @param TDecimal $objPrice
	 */
	public function setUnitPurchasePriceExclVAT($objPrice)
	{
		$this->set(TTransactionsLines::FIELD_UNITPURCHASEPRICEEXCLVAT, $objPrice);
	} 		

	/**
	 * get vat percentage
	 * 
	 * @return TDecimal
	 */
	public function getVATPercentage()
	{
		return $this->get(TTransactionsLines::FIELD_VATPERCENTAGE);
	}

	/**
	 * set vat percentage
	 * 
	 * @param TDecimal $objPercentage
	 */
	public function setVATPercentage($objPercentage)
	{
		$this->set(TTransactionsLines::FIELD_VATPERCENTAGE, $objPercentage);
	} 	

	/**
	 * get amount of discount deducted from unit price
	 * 
	 * @return TDecimal
	 */
	public function getUnitDiscountExclVat()
	{
		return $this->get(TTransactionsLines::FIELD_UNITDISCOUNTEXCLVAT);
	}

	/**
	 * set amount of discount deducted from unit price
	 * 
	 * @param TDecimal $objDiscount
	 */
	public function setUnitDiscountExclVat($objDiscount)
	{
		$this->set(TTransactionsLines::FIELD_UNITDISCOUNTEXCLVAT, $objDiscount);
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
		//invoices id
		$this->setFieldDefaultValue(TTransactionsLines::FIELD_TRANSACTIONSID, 0);
		$this->setFieldType(TTransactionsLines::FIELD_TRANSACTIONSID, CT_INTEGER64);
		$this->setFieldLength(TTransactionsLines::FIELD_TRANSACTIONSID, 0);
		$this->setFieldDecimalPrecision(TTransactionsLines::FIELD_TRANSACTIONSID, 0);
		$this->setFieldPrimaryKey(TTransactionsLines::FIELD_TRANSACTIONSID, false);
		$this->setFieldNullable(TTransactionsLines::FIELD_TRANSACTIONSID, false);
		$this->setFieldEnumValues(TTransactionsLines::FIELD_TRANSACTIONSID, null);
		$this->setFieldUnique(TTransactionsLines::FIELD_TRANSACTIONSID, false); 
		$this->setFieldIndexed(TTransactionsLines::FIELD_TRANSACTIONSID, false); 
		$this->setFieldFulltext(TTransactionsLines::FIELD_TRANSACTIONSID, false); 
		$this->setFieldForeignKeyClass(TTransactionsLines::FIELD_TRANSACTIONSID, TTransactions::class);
		$this->setFieldForeignKeyTable(TTransactionsLines::FIELD_TRANSACTIONSID, TTransactions::getTable());
		$this->setFieldForeignKeyField(TTransactionsLines::FIELD_TRANSACTIONSID, TSysModel::FIELD_ID);
		$this->setFieldForeignKeyJoin(TTransactionsLines::FIELD_TRANSACTIONSID);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsLines::FIELD_TRANSACTIONSID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsLines::FIELD_TRANSACTIONSID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); 
		$this->setFieldAutoIncrement(TTransactionsLines::FIELD_TRANSACTIONSID, false);
		$this->setFieldUnsigned(TTransactionsLines::FIELD_TRANSACTIONSID, true);
        $this->setFieldEncryptionDisabled(TTransactionsLines::FIELD_TRANSACTIONSID);


        //quantity
        $this->setFieldDefaultValue(TTransactionsLines::FIELD_QUANTITY, 0);
        $this->setFieldType(TTransactionsLines::FIELD_QUANTITY, CT_DECIMAL);
        $this->setFieldLength(TTransactionsLines::FIELD_QUANTITY, 13);
        $this->setFieldDecimalPrecision(TTransactionsLines::FIELD_QUANTITY, 4);
        $this->setFieldPrimaryKey(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldNullable(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldEnumValues(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldUnique(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldIndexed(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldFulltext(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldForeignKeyClass(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldForeignKeyTable(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldForeignKeyField(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldForeignKeyJoin(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldForeignKeyActionOnUpdate(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldForeignKeyActionOnDelete(TTransactionsLines::FIELD_QUANTITY, null);
        $this->setFieldAutoIncrement(TTransactionsLines::FIELD_QUANTITY, false);
        $this->setFieldUnsigned(TTransactionsLines::FIELD_QUANTITY, false);	
		$this->setFieldEncryptionDisabled(TTransactionsLines::FIELD_QUANTITY);	
			

		//description
		$this->setFieldDefaultValue(TTransactionsLines::FIELD_DESCRIPTION, '');
		$this->setFieldType(TTransactionsLines::FIELD_DESCRIPTION, CT_VARCHAR);
		$this->setFieldLength(TTransactionsLines::FIELD_DESCRIPTION, 100);
		$this->setFieldDecimalPrecision(TTransactionsLines::FIELD_DESCRIPTION, 0);
		$this->setFieldPrimaryKey(TTransactionsLines::FIELD_DESCRIPTION, false);
		$this->setFieldNullable(TTransactionsLines::FIELD_DESCRIPTION, true);
		$this->setFieldEnumValues(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldUnique(TTransactionsLines::FIELD_DESCRIPTION, false); 
		$this->setFieldIndexed(TTransactionsLines::FIELD_DESCRIPTION, false); 
		$this->setFieldFulltext(TTransactionsLines::FIELD_DESCRIPTION, false); 
		$this->setFieldForeignKeyClass(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyTable(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyField(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyJoin(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsLines::FIELD_DESCRIPTION, null);
		$this->setFieldAutoIncrement(TTransactionsLines::FIELD_DESCRIPTION, false);
		$this->setFieldUnsigned(TTransactionsLines::FIELD_DESCRIPTION, false);
        $this->setFieldEncryptionDisabled(TTransactionsLines::FIELD_DESCRIPTION);	
		

		//unit price excluding vat
		$this->setFieldDefaultsTDecimalCurrency(TTransactionsLines::FIELD_UNITPRICEEXCLVAT);
		
		//purchase price per unit
		$this->setFieldCopyProps(TTransactionsLines::FIELD_UNITPURCHASEPRICEEXCLVAT, TTransactionsLines::FIELD_UNITPRICEEXCLVAT);

		//vat percentage
		$this->setFieldDefaultValue(TTransactionsLines::FIELD_VATPERCENTAGE, 0);
		$this->setFieldType(TTransactionsLines::FIELD_VATPERCENTAGE, CT_DECIMAL);
		$this->setFieldLength(TTransactionsLines::FIELD_VATPERCENTAGE, 13);
		$this->setFieldDecimalPrecision(TTransactionsLines::FIELD_VATPERCENTAGE, 4);
		$this->setFieldPrimaryKey(TTransactionsLines::FIELD_VATPERCENTAGE, false);
		$this->setFieldNullable(TTransactionsLines::FIELD_VATPERCENTAGE, false);
		$this->setFieldEnumValues(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldUnique(TTransactionsLines::FIELD_VATPERCENTAGE, false); 
		$this->setFieldIndexed(TTransactionsLines::FIELD_VATPERCENTAGE, false); 
		$this->setFieldFulltext(TTransactionsLines::FIELD_VATPERCENTAGE, false); 
		$this->setFieldForeignKeyClass(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldForeignKeyTable(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldForeignKeyField(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldForeignKeyJoin(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsLines::FIELD_VATPERCENTAGE, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsLines::FIELD_VATPERCENTAGE, null); 
		$this->setFieldAutoIncrement(TTransactionsLines::FIELD_VATPERCENTAGE, false);
		$this->setFieldUnsigned(TTransactionsLines::FIELD_VATPERCENTAGE, true);
        $this->setFieldEncryptionDisabled(TTransactionsLines::FIELD_VATPERCENTAGE);	

		//discount per unit
		$this->setFieldCopyProps(TTransactionsLines::FIELD_UNITDISCOUNTEXCLVAT, TTransactionsLines::FIELD_UNITPRICEEXCLVAT);
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
		return array(TTransactionsLines::FIELD_TRANSACTIONSID, 
					TTransactionsLines::FIELD_QUANTITY,
					TTransactionsLines::FIELD_DESCRIPTION,
					TTransactionsLines::FIELD_UNITPRICEEXCLVAT,
					TTransactionsLines::FIELD_UNITPURCHASEPRICEEXCLVAT,
					TTransactionsLines::FIELD_VATPERCENTAGE,
					TTransactionsLines::FIELD_UNITDISCOUNTEXCLVAT
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
		return APP_DB_TABLEPREFIX.'TransactionsLines';
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
		return $this->get(TTransactionsLines::FIELD_DESCRIPTION);
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
		return 'lekkerlijntje'.
			$this->getTransactionsID().
			$this->getQuantity()->getValue().
			'bliekblat'.
			$this->getDescription().
			$this->getUnitPriceExclVAT()->getValue().
			$this->getVATPercentage()->getValue();
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
	 * calculate the total price of the transaction including vat
	 * 
	 * @return TDecimal retuns new currency object
	 */
	public function calculateTotalPriceInclVat()
	{
		$objTotal = new TDecimal(0, 4);
		$objInclVat = null;

		$this->resetRecordPointer();
		while($this->next())
		{			
			$objInclVat = $this->getUnitPriceExclVAT()->getIncludingVAT($this->getVATPercentage());
			$objInclVat->multiply($this->getQuantity()); //calculate total per line
			
			$objTotal->add($objInclVat);			
		}

		return $objTotal;
	}

	/**
	 * calculate the total price of the transaction excluding vat
	 * 
	 * @return TDecimal retuns new currency object
	 */
	public function calculateTotalPriceExclVat()
	{
		$objTotal = new TDecimal(0, 4);
		$objExclVat = null;

		$this->resetRecordPointer();
		while($this->next())
		{			
			$objExclVat = clone $this->getUnitPriceExclVAT(); //we don't want to calculate on the actual object
			$objExclVat->multiply($this->getQuantity()); //calculate total per line
			
			$objTotal->add($objExclVat);			
		}

		return $objTotal;
	}	


	/**
	 * calculate the total PURCHASE price of the transaction excluding vat
	 * 
	 * @return TCurTDecimalrency retuns new currency object
	 */
	public function calculateTotalPurchasePriceExclVat()
	{
		$objTotal = new TDecimal(0, 4);
		$objExclVat = null;

		$this->resetRecordPointer();
		while($this->next())
		{			
			$objExclVat = clone $this->getUnitPurchasePriceExclVAT(); //we don't want to calculate on the actual object
			$objExclVat->multiply($this->getQuantity()); //calculate total per line
			
			$objTotal->add($objExclVat);			
		}

		return $objTotal;
	}	
	
	/**
	 * calculate the total VAT of the transaction
	 * 
	 * @return TDecimal retuns new currency object
	 */
	public function calculateTotalVat()
	{
		$objVat = null;

		$objVat = $this->calculateTotalPriceInclVat();
		$objVat->subtract($this->calculateTotalPriceExclVat()); //total excl vat
	
		return $objVat;
	}	
        
} 
?>