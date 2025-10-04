<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;


/**
 * Transaction Types
 * 
 * TTransactionsTypes
 * Transaction types make the distiction wether a transaction is an invoice, order, offer etc
 * 
 * DEFAULTS:
 * -default: 			the one that is selected by default in <SELECT> box
 * -order-default: 		if an order is needed (for example webshop), this type is used
 * -invoice-default:	if an invoice is needed (for example webshop), this type is used
 * 
 * COLOR:
 * The color is used for visual identification for the user.
 * for example: blue = invoice, green = order etc.
 * This helps to prevent mistakes by confusing different transaction types
 * 
 * 
 * created 3 november 2023
 * 3 nov 2023: TTransactionsTypes: created
 * 
 * @author Dennis Renirie
 */

class TTransactionsTypes extends TSysModel
{
	const FIELD_NAME					= 'sName'; //name of the type 
	const FIELD_DESCRIPTION				= 'sDescription'; //describes what this transaction type does
	const FIELD_AVAILABLESTOCKADD		= 'bAvailableStockAdd'; //is current stock being added for this transaction? Invoices are, orders and offers are not
	const FIELD_AVAILABLESTOCKSUBSTRACT	= 'bAvailableStockSubtract'; //is current stock being subtracted for this transaction? Invoices are, orders and offers are not
	const FIELD_RESERVEDSTOCKADD		= 'bReservedStockAdd'; //is reserved stock being added for this transaction? Invoices are, orders and offers are not
	const FIELD_RESERVEDSTOCKSUBTRACT	= 'bReservedStockSubtract'; //is reserved stock being subtracted for this transaction? Invoices are, orders and offers are not
	const FIELD_FINANCIALADD			= 'bFinancialAdd'; //adds this transaction money? Invoices do, credit-invoices do not (orders and offers also don't)
	const FIELD_FINANCIALSUBTRACT 		= 'bFinancialSubtract'; //subtracts this transaction money? Credit-invoices do, invoices do not (orders and offers also don't)
	// const FIELD_ISDEFAULTSELECTED		= 'bIsDefaultSelected'; //When <SELECT> box shown, is this the transaction-type selected by default?
	const FIELD_ISDEFAULTINVOICE		= 'bIsDefaultInvoice'; //When website/webshop makes an invoice, is this the one to use?
	const FIELD_ISDEFAULTORDER			= 'bIsDefaultOrder'; //When website/webshop makes an order, is this the one to use?
	const FIELD_COLORFOREGROUND			= 'sColorForeground'; //foreground color value in hexadecimals with #, for example: #ffffff = white
	const FIELD_COLORBACKGROUND			= 'sColorBackground'; //background color value in hexadecimals with #, for example: #ffffff = white
	const FIELD_NEWNUMBERINCREMENT		= 'iNewNumberIncrement'; //new transaction starts at this number. For example: invoice number
	const FIELD_PAYMENTMADEWITHINDAYS	= 'iPaymentMadeWithinDays';//Payment should be made within [X] days of the invoice date. 21 = 21 days

	const ENCRYPTION_ADDRSELL_PASSPHRASE	= 'LpmDvF4#g_ldpwh4';
	const ENCRYPTION_VATNO_PASSPHRASE		= '3fP_ew3$rfs3d213d';

	const DEFAULT_TYPE_INVOICE_NAME								= 'Invoice';
	const DEFAULT_TYPE_INVOICE_DESCRIPTION						= 'Money + | available stock -';
	const DEFAULT_TYPE_INVOICECREDIT_NAME						= 'Credit Invoice';
	const DEFAULT_TYPE_INVOICECREDIT_DESCRIPTION				= 'Money - | available stock +';
	const DEFAULT_TYPE_CLIENTORDER_NAME							= 'Client purchase Order';
	const DEFAULT_TYPE_CLIENTORDER_DESCRIPTION					= 'Available stock -> reserved stock';
	const DEFAULT_TYPE_TRANSFERRESERVEDTOAVAILABLE_NAME			= 'Reserved stock -> available stock';
	const DEFAULT_TYPE_TRANSFERRESERVEDTOAVAILABLE_DESCRIPTION	= 'Reserved stock -> available stock';
	const DEFAULT_TYPE_INCOMINGGOODS_NAME						= 'Incoming goods';
	const DEFAULT_TYPE_INCOMINGGOODS_DESCRIPTION				= 'Available stock +';
	const DEFAULT_TYPE_QUOTE_NAME								= 'Client quote';
	const DEFAULT_TYPE_QUOTE_DESCRIPTION						= 'Give client a price estimate';

	/**
	 * get invoice type name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get(TTransactionsTypes::FIELD_NAME);
	}
	
	/**
	 * set invoice type name
	 * 
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->set(TTransactionsTypes::FIELD_NAME, $sName);
	}        
	
	/**
	 * get description
	 * used to describe what this transaction type does as an extension of name
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		return $this->get(TTransactionsTypes::FIELD_DESCRIPTION);
	}
	
	/**
	 * set description
	 * used to describe what this transaction type does as an extension of name
	 * 
	 * @param string $sDescription
	 */
	public function setDescription($sDescription)
	{
		$this->set(TTransactionsTypes::FIELD_DESCRIPTION, $sDescription);
	}        

	/**
	 * get if stock is added for this transaction
	 * 
	 * @return bool
	 */
	public function getCurrentStockAdd()
	{
		return $this->get(TTransactionsTypes::FIELD_AVAILABLESTOCKADD);
	}
	
	/**
	 * set if stock is added for this transaction 
	 * 
	 * @param bool $bIsStock
	 */
	public function setCurrentStockAdd($bIsStock)
	{
		$this->set(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, $bIsStock);
	} 

	/**
	 * get if stock is subtracted for this transaction
	 * 
	 * @return bool
	 */
	public function getCurrentStockSubtract()
	{
		return $this->get(TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT);
	}
	
	/**
	 * set if stock is subtracted for this transaction 
	 * 
	 * @param bool $bIsStock
	 */
	public function setCurrentStockSubtract($bIsStock)
	{
		$this->set(TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, $bIsStock);
	} 

	/**
	 * get if stock is added for this transaction
	 * 
	 * @return bool
	 */
	public function getReservedStockAdd()
	{
		return $this->get(TTransactionsTypes::FIELD_RESERVEDSTOCKADD);
	}
	
	/**
	 * set if stock is added for this transaction 
	 * 
	 * @param bool $bIsStock
	 */
	public function setReservedStockAdd($bIsStock)
	{
		$this->set(TTransactionsTypes::FIELD_RESERVEDSTOCKADD, $bIsStock);
	} 

	/**
	 * get if stock is subtracted for this transaction
	 * 
	 * @return bool
	 */
	public function getReservedStockSubtract()
	{
		return $this->get(TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT);
	}
	
	/**
	 * set if stock is subtracted for this transaction 
	 * 
	 * @param bool $bIsStock
	 */
	public function setReservedStockSubtract($bIsStock)
	{
		$this->set(TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, $bIsStock);
	} 

	/**
	 * get if transaction adds money
	 * 
	 * @return bool
	 */
	public function getFinancialAdd()
	{
		return $this->get(TTransactionsTypes::FIELD_FINANCIALADD);
	}
	
	/**
	 * set if transaction adds money
	 * 
	 * @param bool $bIsFinancial
	 */
	public function setFinancialAdd($bIsFinancial)
	{
		$this->set(TTransactionsTypes::FIELD_FINANCIALADD, $bIsFinancial);
	} 

	/**
	 * get if transaction subtracts money
	 * 
	 * @return bool
	 */
	public function getFinancialSubtract()
	{
		return $this->get(TTransactionsTypes::FIELD_FINANCIALSUBTRACT);
	}
	
	/**
	 * set if transaction adds money
	 * 
	 * @param bool $bIsFinancial
	 */
	public function setFinancialSubtract($bIsFinancial)
	{
		$this->set(TTransactionsTypes::FIELD_FINANCIALSUBTRACT, $bIsFinancial);
	} 


	// /**
	//  * get if transaction is selected by default in <SELECT> box
	//  * 
	//  * @return bool
	//  */
	// public function getIsDefaultSelected()
	// {
	// 	return $this->get(TTransactionsTypes::FIELD_ISDEFAULTSELECTED);
	// }
	
	
	// /**
	//  * set if transaction is selected by default in <SELECT> box
	//  * 
	//  * @param bool $bIsDefault
	//  */
	// public function setIsDefaultSelected($bIsDefault)
	// {
	// 	$this->set(TTransactionsTypes::FIELD_ISDEFAULTSELECTED, $bIsDefault);
	// } 

	
	/**
	 * get if invoice transaction type is needed, this is selected automatically
	 * (for example in a webshop)
	 * 
	 * @return bool
	 */
	public function getIsDefaultInvoice()
	{
		return $this->get(TTransactionsTypes::FIELD_ISDEFAULTINVOICE);
	}
	
	
	/**
	 * set if invoice transaction type is needed, this is selected automatically
	 * (for example in a webshop)
	 * 
	 * @param bool $bIsDefault
	 */
	public function setIsDefaultInvoice($bIsDefault)
	{
		$this->set(TTransactionsTypes::FIELD_ISDEFAULTINVOICE, $bIsDefault);
	} 	


	/**
	 * get if order transaction type is needed, this is selected automatically
	 * (for example in a webshop)
	 * 
	 * @return bool
	 */
	public function getIsDefaultOrder()
	{
		return $this->get(TTransactionsTypes::FIELD_ISDEFAULTORDER);
	}
	
	
	/**
	 * set if order transaction type is needed, this is selected automatically
	 * (for example in a webshop)
	 * 
	 * @param bool $bIsDefault
	 */
	public function setIsDefaultOrder($bIsDefault)
	{
		$this->set(TTransactionsTypes::FIELD_ISDEFAULTORDER, $bIsDefault);
	} 	


	/**
	 * get foreground color value in hexadecimals
	 * 
	 * @return bool
	 */
	public function getColorForeground()
	{
		return $this->get(TTransactionsTypes::FIELD_COLORFOREGROUND);
	}
	
	
	/**
	 * set foreground color value in hexadecimals
	 * 
	 * @param string $sColorHex in hexadecimals
	 */
	public function setColorForeground($sHexValue)
	{
		$this->set(TTransactionsTypes::FIELD_COLORFOREGROUND, $sHexValue);
	} 	

	/**
	 * get background color value in hexadecimals
	 * 
	 * @return bool
	 */
	public function getColorBackground()
	{
		return $this->get(TTransactionsTypes::FIELD_COLORBACKGROUND);
	}
	
	
	/**
	 * set background color value in hexadecimals
	 * 
	 * @param string $sColorHex in hexadecimals
	 */
	public function setColorBackground($sHexValue)
	{
		$this->set(TTransactionsTypes::FIELD_COLORBACKGROUND, $sHexValue);
	} 		

	/**
	 * get new number increment
	 * (order number, invoice number etc)
	 * 
	 * @return bool
	 */
	public function getNewNumberIncrement()
	{
		return $this->get(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT);
	}
	
	
	/**
	 * set new number increment
	 * (order number, invoice number etc)
	 * 
	 * @param int $iNewNumber
	 */
	public function setNewNumberIncrement($iNewNumber)
	{
		$this->set(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, $iNewNumber);
	} 		


	/**
	 * get payment must be made in X days
	 * 
	 * @return bool
	 */
	public function getPaymentMadeWithInDays()
	{
		return $this->get(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS);
	}
	
	
	/**
	 * set payment must be made in X days
	 * 
	 * @param int $iDays
	 */
	public function setPaymentMadeWithInDays($iDays)
	{
		$this->set(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, $iDays);
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
		//transaction type name
		$this->setFieldDefaultValue(TTransactionsTypes::FIELD_NAME, '');
		$this->setFieldType(TTransactionsTypes::FIELD_NAME, CT_VARCHAR);
		$this->setFieldLength(TTransactionsTypes::FIELD_NAME, 50);
		$this->setFieldDecimalPrecision(TTransactionsTypes::FIELD_NAME, 0);
		$this->setFieldPrimaryKey(TTransactionsTypes::FIELD_NAME, false);
		$this->setFieldNullable(TTransactionsTypes::FIELD_NAME, false);
		$this->setFieldEnumValues(TTransactionsTypes::FIELD_NAME, null);
		$this->setFieldUnique(TTransactionsTypes::FIELD_NAME, true); 
		$this->setFieldIndexed(TTransactionsTypes::FIELD_NAME, false); 
		$this->setFieldFulltext(TTransactionsTypes::FIELD_NAME, true); 
		$this->setFieldForeignKeyClass(TTransactionsTypes::FIELD_NAME, null);
		$this->setFieldForeignKeyTable(TTransactionsTypes::FIELD_NAME, null);
		$this->setFieldForeignKeyField(TTransactionsTypes::FIELD_NAME, null);
		$this->setFieldForeignKeyJoin(TTransactionsTypes::FIELD_NAME);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsTypes::FIELD_NAME, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsTypes::FIELD_NAME, null); 
		$this->setFieldAutoIncrement(TTransactionsTypes::FIELD_NAME, false);
		$this->setFieldUnsigned(TTransactionsTypes::FIELD_NAME, false);
        $this->setFieldEncryptionDisabled(TTransactionsTypes::FIELD_NAME);

		//description
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_DESCRIPTION, TTransactionsTypes::FIELD_NAME);
		$this->setFieldUnique(TTransactionsTypes::FIELD_DESCRIPTION, false); 		
		$this->setFieldLength(TTransactionsTypes::FIELD_DESCRIPTION, 100);

        //adds to available stock
        $this->setFieldDefaultValue(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldType(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, CT_BOOL);
        $this->setFieldLength(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, 0);
        $this->setFieldDecimalPrecision(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, 0);
        $this->setFieldPrimaryKey(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldNullable(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldEnumValues(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldUnique(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldIndexed(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldFulltext(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldForeignKeyClass(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldForeignKeyTable(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldForeignKeyField(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldForeignKeyJoin(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldForeignKeyActionOnUpdate(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldForeignKeyActionOnDelete(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, null);
        $this->setFieldAutoIncrement(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);
        $this->setFieldUnsigned(TTransactionsTypes::FIELD_AVAILABLESTOCKADD, false);	
		$this->setFieldEncryptionDisabled(TTransactionsTypes::FIELD_AVAILABLESTOCKADD);	

		//subtracts from available stock
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //adds to reserved stock
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_RESERVEDSTOCKADD, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //subtracts from reserved stock
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_RESERVEDSTOCKSUBTRACT, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //adds money
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_FINANCIALADD, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //subtracts money
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_FINANCIALSUBTRACT, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //is default
        // $this->setFieldCopyProps(TTransactionsTypes::FIELD_ISDEFAULTSELECTED, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //is default invoice
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_ISDEFAULTINVOICE, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);

        //is default order
        $this->setFieldCopyProps(TTransactionsTypes::FIELD_ISDEFAULTORDER, TTransactionsTypes::FIELD_AVAILABLESTOCKADD);
		
		//foreground color
		$this->setFieldDefaultValue(TTransactionsTypes::FIELD_COLORBACKGROUND, '000000');
		$this->setFieldType(TTransactionsTypes::FIELD_COLORBACKGROUND, CT_COLOR);
		$this->setFieldLength(TTransactionsTypes::FIELD_COLORBACKGROUND, 0); //default length
		$this->setFieldDecimalPrecision(TTransactionsTypes::FIELD_COLORBACKGROUND, 0);
		$this->setFieldPrimaryKey(TTransactionsTypes::FIELD_COLORBACKGROUND, false);
		$this->setFieldNullable(TTransactionsTypes::FIELD_COLORBACKGROUND, false);
		$this->setFieldEnumValues(TTransactionsTypes::FIELD_COLORBACKGROUND, null);
		$this->setFieldUnique(TTransactionsTypes::FIELD_COLORBACKGROUND, false); 
		$this->setFieldIndexed(TTransactionsTypes::FIELD_COLORBACKGROUND, false); 
		$this->setFieldFulltext(TTransactionsTypes::FIELD_COLORBACKGROUND, false); 
		$this->setFieldForeignKeyClass(TTransactionsTypes::FIELD_COLORBACKGROUND, null);
		$this->setFieldForeignKeyTable(TTransactionsTypes::FIELD_COLORBACKGROUND, null);
		$this->setFieldForeignKeyField(TTransactionsTypes::FIELD_COLORBACKGROUND, null);
		$this->setFieldForeignKeyJoin(TTransactionsTypes::FIELD_COLORBACKGROUND);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsTypes::FIELD_COLORBACKGROUND, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsTypes::FIELD_COLORBACKGROUND, null); 
		$this->setFieldAutoIncrement(TTransactionsTypes::FIELD_COLORBACKGROUND, false);
		$this->setFieldUnsigned(TTransactionsTypes::FIELD_COLORBACKGROUND, false);
        $this->setFieldEncryptionDisabled(TTransactionsTypes::FIELD_COLORBACKGROUND);	

		//background color
		$this->setFieldCopyProps(TTransactionsTypes::FIELD_COLORFOREGROUND, TTransactionsTypes::FIELD_COLORBACKGROUND);
		$this->setFieldDefaultValue(TTransactionsTypes::FIELD_COLORFOREGROUND, 'FFFFFF');

		//new number increment
		$this->setFieldDefaultValue(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, 0);
		$this->setFieldType(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, CT_INTEGER64);
		$this->setFieldLength(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, 0);
		$this->setFieldDecimalPrecision(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, 0);
		$this->setFieldPrimaryKey(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false);
		$this->setFieldNullable(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false);
		$this->setFieldEnumValues(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null);
		$this->setFieldUnique(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false); 
		$this->setFieldIndexed(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false); 
		$this->setFieldFulltext(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false); 
		$this->setFieldForeignKeyClass(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null);
		$this->setFieldForeignKeyTable(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null);
		$this->setFieldForeignKeyField(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null);
		$this->setFieldForeignKeyJoin(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, null); 
		$this->setFieldAutoIncrement(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false);
		$this->setFieldUnsigned(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT, false);
        $this->setFieldEncryptionDisabled(TTransactionsTypes::FIELD_NEWNUMBERINCREMENT);
		
		//payment made within days
		$this->setFieldDefaultValue(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, 14);
		$this->setFieldType(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, CT_INTEGER32);
		$this->setFieldLength(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, 0);
		$this->setFieldDecimalPrecision(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, 0);
		$this->setFieldPrimaryKey(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false);
		$this->setFieldNullable(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false);
		$this->setFieldEnumValues(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null);
		$this->setFieldUnique(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false); 
		$this->setFieldIndexed(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false); 
		$this->setFieldFulltext(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false); 
		$this->setFieldForeignKeyClass(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null);
		$this->setFieldForeignKeyTable(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null);
		$this->setFieldForeignKeyField(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null);
		$this->setFieldForeignKeyJoin(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS);
		$this->setFieldForeignKeyActionOnUpdate(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null);
		$this->setFieldForeignKeyActionOnDelete(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, null); 
		$this->setFieldAutoIncrement(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false);
		$this->setFieldUnsigned(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS, false);
        $this->setFieldEncryptionDisabled(TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS);
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
		return array(TTransactionsTypes::FIELD_NAME, 
					TTransactionsTypes::FIELD_DESCRIPTION,
					TTransactionsTypes::FIELD_AVAILABLESTOCKADD,
					TTransactionsTypes::FIELD_AVAILABLESTOCKSUBSTRACT,
					TTransactionsTypes::FIELD_RESERVEDSTOCKADD,
					TTransactionsTypes::FIELD_FINANCIALADD,
					TTransactionsTypes::FIELD_FINANCIALSUBTRACT,
					TTransactionsTypes::FIELD_ISDEFAULT,
					TTransactionsTypes::FIELD_ISDEFAULTINVOICE,
					TTransactionsTypes::FIELD_ISDEFAULTORDER,
					TTransactionsTypes::FIELD_COLORFOREGROUND,
					TTransactionsTypes::FIELD_COLORBACKGROUND,
					TTransactionsTypes::FIELD_NEWNUMBERINCREMENT,
					TTransactionsTypes::FIELD_PAYMENTMADEWITHINDAYS
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
		return APP_DB_TABLEPREFIX.'TransactionsTypes';
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
		return $this->get(TTransactionsTypes::FIELD_NAME);
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
		return 'typetjeblaatberlk'.
			$this->getName().
			$this->getDescription().
			'hreiiesdflksjdflsjkdfl'.
			boolToStr($this->getCurrentStockAdd()).
			boolToStr($this->getCurrentStockSubtract()).
			boolToStr($this->getReservedStockAdd()).
			boolToStr($this->getReservedStockSubtract()).
			boolToStr($this->getFinancialAdd()).
			boolToStr($this->getFinancialSubtract()).
			boolToStr($this->getPaymentMadeWithInDays()).
			'whodl,emikjsdf'.
			$this->getNewNumberIncrement();			
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

	/**
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		$bSuccess = true;
		$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
		//==check if at least one Transaction type exists, if not, then add it
		$this->newQuery();
		$this->clear();    
		$this->limitOne(); //we need just one record to be returned
		if (!$this->loadFromDB())
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': loadFromDB() failed in install()');
			return false;
		}
		
		//==if no category exists, add a default one
		if($this->count() == 0)
		{
			//client invoice
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_INVOICE_NAME);
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_INVOICE_DESCRIPTION);
			$this->setCurrentStockAdd(false);
			$this->setCurrentStockSubtract(true);
			$this->setReservedStockAdd(false);
			$this->setReservedStockSubtract(false);
			$this->setFinancialAdd(true);
			$this->setFinancialSubtract(false);
			$this->setIsDefault(true);
			$this->setIsFavorite(true);
			$this->setIsDefaultInvoice(true);
			$this->setColorBackground('8B0000'); //red 
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(14); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice:'.TTransactionsTypes::DEFAULT_TYPE_INVOICE_NAME);
				return false;
			}    

			//credit invoice
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_INVOICECREDIT_NAME);
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_INVOICECREDIT_DESCRIPTION);				
			$this->setCurrentStockAdd(true);
			$this->setCurrentStockSubtract(false);
			$this->setReservedStockAdd(false);
			$this->setReservedStockSubtract(false);
			$this->setFinancialAdd(false);
			$this->setFinancialSubtract(true);
			$this->setIsFavorite(true);			
			$this->setColorBackground('989AFF'); //purple 
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(90); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type invoice:'.TTransactionsTypes::DEFAULT_TYPE_INVOICECREDIT_NAME);
				return false;
			}    				
			
			//client order: transfers current stock to reserved stock
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_CLIENTORDER_NAME); //default order type
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_CLIENTORDER_DESCRIPTION);				
			$this->setCurrentStockAdd(false);
			$this->setCurrentStockSubtract(true);
			$this->setReservedStockAdd(true);
			$this->setReservedStockSubtract(false);
			$this->setFinancialAdd(false);
			$this->setFinancialSubtract(false);
			$this->setIsFavorite(true);			
			$this->setIsDefaultOrder(true);
			$this->setColorBackground('009b17'); //green
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(14); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type order:'.TTransactionsTypes::DEFAULT_TYPE_CLIENTORDER_NAME);
				return false;
			}    

			//transfer reserved to available stock
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_TRANSFERRESERVEDTOAVAILABLE_NAME); //default order type
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_TRANSFERRESERVEDTOAVAILABLE_DESCRIPTION);				
			$this->setCurrentStockAdd(true);
			$this->setCurrentStockSubtract(false);
			$this->setReservedStockAdd(false);
			$this->setReservedStockSubtract(true);
			$this->setFinancialAdd(false);
			$this->setFinancialSubtract(false);
			$this->setIsDefaultOrder(false);
			$this->setIsFavorite(true);			
			$this->setColorBackground('FF6739'); //orange
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(0); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type order:'.TTransactionsTypes::DEFAULT_TYPE_TRANSFERRESERVEDTOAVAILABLE_NAME);
				return false;
			}    

			//incoming goods
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_INCOMINGGOODS_NAME); //default order type
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_INCOMINGGOODS_DESCRIPTION);				
			$this->setCurrentStockAdd(true);
			$this->setCurrentStockSubtract(false);
			$this->setReservedStockAdd(false);
			$this->setReservedStockSubtract(false);
			$this->setFinancialAdd(false);
			$this->setFinancialSubtract(false);
			$this->setIsDefaultOrder(false);
			$this->setColorBackground('007880'); //dark green
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(0); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type order:'.TTransactionsTypes::DEFAULT_TYPE_INCOMINGGOODS_NAME);
				return false;
			}  	

			//quote for purchase
			$this->clear();
			$this->newRecord();
			$this->setName(TTransactionsTypes::DEFAULT_TYPE_QUOTE_NAME); //default order type
			$this->setDescription(TTransactionsTypes::DEFAULT_TYPE_QUOTE_DESCRIPTION);				
			$this->setCurrentStockAdd(false);
			$this->setCurrentStockSubtract(false);
			$this->setReservedStockAdd(false);
			$this->setReservedStockSubtract(false);
			$this->setFinancialAdd(false);
			$this->setFinancialSubtract(false);
			$this->setIsDefaultOrder(false);
			$this->setIsFavorite(true);			
			$this->setColorBackground('9931a8'); //purple
			$this->setColorForeground('ffffff'); //white
			$this->setPaymentMadeWithInDays(0); 
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving default transaction type order:'.TTransactionsTypes::DEFAULT_TYPE_QUOTE_NAME);
				return false;
			}  				

		}
			
		return $bSuccess;
	}        	
} 
?>