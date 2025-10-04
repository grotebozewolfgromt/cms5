<?php

namespace dr\classes\models;

use dr\classes\models\TSysModel;
use dr\classes\types\TDateTime;

/**
 * abstract class for redemption codes
 * 
 * this class provides a blueprint for redemption codes, like cms users
 * or in a webshop environment
 * 
 * 
 * created 4 maart 2022
 * 4 mrt 2022: TSysRedemptionCodesAbstract: 
 */

abstract class TSysRedemptionCodesAbstract extends TSysModel
{
	const FIELD_CODENAME = 'sCodeName'; //name of the code (just for reference for the user)
	const FIELD_REDEMPTIONCODE = 'sRedemptionCode'; //the invitation code itself
	const FIELD_CURRENTREDEEMS = 'iCurrentRedeems'; //counter for how many codes are already redeemed
	const FIELD_MAXREDEEMS = 'iMaxRedeems'; //how many codes can be redeemed in total? 0=unlimited
	const FIELD_DATESTART = 'dtDateStart';	//start date of the code. 0=no start
	const FIELD_DATEEND = 'dtDateEnd';	//end date of the code. 0=no end
	const FIELD_ISENABLED = 'bIsEnabled';//is invitation code enabled? When NOT enabled, customers can not redeem code
		
	const ALLOWEDCHARSCODE = '-abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ1234567890'; //the characters that are allowed in a redemption code. Characters like lower case L/O are excluded because they resemble digits 1 and 0 too much. additional security issues (xss/sql injection) can be prevented by filtering characters on this string


	/**
	 * get name of the code
	 * This is the internal name of the code for reference by the user
	 * 
	 * @return string
	 */
	public function getCodeName()
	{
		return $this->get(TSysRedemptionCodesAbstract::FIELD_CODENAME);
	}

	
	/**
	 * set name of the code
	 * This is the internal name of the code for reference by the user
	 * 
	 * @param string $sName
	 */
	public function setCodeName($sName)
	{
		$this->set(TSysRedemptionCodesAbstract::FIELD_CODENAME, $sName);
	}        
	
	/**
	 * get redemption code
	 * 
	 * @return string
	 */
	public function getRedemptionCode()
	{
		return $this->get(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE);
	}

	/**
	 * set redemption code
	 * 
	 * @param string $sCode
	 */
	public function setRedemptionCode($sCode)
	{
		$this->set(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, $sCode);
	}           

	
	/**
	 * get current amount of code redeems
	 * 
	 * @return int
	 */
	public function getCurrentRedeems()
	{
		return $this->get(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS);
	}

	/**
	 * set current amount of code redeems
	 * 
	 * @param int $iNumberOfRedeems
	 */
	public function setCurrentRedeems($iNumberOfRedeems)
	{
		$this->set(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, $iNumberOfRedeems);
	}   


	/**
	 * get maximum amount of code redeems
	 * 0 = unlimited (no maximum)
	 * 
	 * @return int
	 */
	public function getMaxRedeems()
	{
		return $this->get(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS);
	}

	/**
	 * set maximum amount of code redeems
	 * 0 = unlimited (no maximum)
	 * 
	 * @param int $iNumberOfRedeems
	 */
	public function setMaxRedeems($iNumberOfRedeems)
	{
		$this->set(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, $iNumberOfRedeems);
	}  


    /**
     * set the START date (and time) on which the redemption code is valid     
     * 
     * if date is zero = no begin
     *  
     * @param TDateTime $objDateTime
     */
    public function setDateStart($objDateTime = null)
    {
        $this->setTDateTime(TSysRedemptionCodesAbstract::FIELD_DATESTART, $objDateTime);
    }        

    /**
     * get the START date (and time) on which the redemption code is valid 
	 * 
	 * if date is zero = no begin
     * 
     * @return TDateTime
     */
    public function getDateStart()
    {
        return $this->get(TSysRedemptionCodesAbstract::FIELD_DATESTART);
    } 


    /**
     * set the END date (and time) on which the redemption code is valid     
     * 
     * if date is zero = no begin
     *  
     * @param TDateTime $objDateTime
     */
    public function setDateEnd($objDateTime = null)
    {
        $this->setTDateTime(TSysRedemptionCodesAbstract::FIELD_DATEEND, $objDateTime);
    }        

    /**
     * get the END date (and time) on which the redemption code is valid 
	 * 
	 * if date is zero = no begin
     * 
     * @return TDateTime
	 * 
	 * 
     */
    public function getDateEnd()
    {
        return $this->get(TSysRedemptionCodesAbstract::FIELD_DATEEND);
    } 	


	/**
	 * database field that registers if a redemption code is enabled or not.
	 * Customers can't redeem unactive codes
	 *
	 * @return void
	 */
	public function getIsEnabled()
	{
		return  $this->get(TSysRedemptionCodesAbstract::FIELD_ISENABLED);
	}

	/**
	 * database field that registers if a redemption code is enabled or not.
	 * Customers can't redeem unactive codes
	 *
	 * @param bool $bEnabled
	 * @return void
	 */
	public function setIsEnabled($bEnabled)
	{
		$this->set(TSysRedemptionCodesAbstract::FIELD_ISENABLED, $bEnabled);
	} 	

	/**
	 * is redemption code valid?
	 * Checks all database records to see if $sCode is valid according to following conditions:
	 * 1) max redeems not exceeded
	 * 2) code enabled
	 * 3) start date
	 * 4) end data
	 *
	 * @param string $sCode
	 * @return boolean false is invalid
	 */
	public function isCodeValidDB($sCode)
	{
		$objNow = new TDateTime(time());

		//filter input on validity
		$sCodeFiltered = '';
		$sCodeFiltered = preg_replace( '/[^'.TSysRedemptionCodesAbstract::ALLOWEDCHARSCODE.']/', '', $sCode);
		if ($sCodeFiltered != $sCode)
			return false;

		//exists in database?
		$objClone = clone $this;
		$objClone->find(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, $sCodeFiltered);
		if (!$objClone->loadFromDB()) //db error
			return false;
		if ($objClone->count() == 0) //no records
			return false;

		//check: max redeems not exceeded
		if ($objClone->getCurrentRedeems() >= $objClone->getMaxRedeems())
			return false;

		//check: enabled
		if (!$objClone->getIsEnabled())
			return false;

		//check: start date
		if ($objNow->isEarlier($objClone->getDateStart()))
			return false;

		//check: end date
		if ($objNow->isLater($objClone->getDateEnd()))
			return false;

		return true;
	}

	/**
	 * increase the number of redeems by 1 of code $sCode in database
	 *
	 * @param string $sCode
	 * @return boolean false is not found
	 */
	public function increaseCurrentRedeemsDB($sCode)
	{
		//filter input on validity
		$sCodeFiltered = '';
		$sCodeFiltered = preg_replace( '/[^'.TSysRedemptionCodesAbstract::ALLOWEDCHARSCODE.']/', '', $sCode);
		if ($sCodeFiltered != $sCode)
			return false;

		//exists in database? then update it
		$objClone = clone $this;
		$objClone->startTransaction();
		$objClone->find(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, $sCodeFiltered);
		if (!$objClone->loadFromDB()) //db error
		{
			$objClone->rollbackTransaction();
			return false;
		}
		if ($objClone->count() == 0) //no records
		{
			$objClone->rollbackTransaction();
			return false;
		}

		$objClone->setCurrentRedeems($objClone->getCurrentRedeems()+1);

		if (!$objClone->saveToDB(true, false))
		{
			$objClone->rollbackTransaction();
			return false;
		}
		else
		{
			$objClone->commitTransaction();
		}
			

		return true;
	}


	/**
	 * this function creates table in database and calls all foreign key classes to do the same
	 *
	 * the $arrPreviousDependenciesModelClasses prevents a endless loop by storing all the classnames that are already installed
	 *
	 * @param array $arrPreviousDependenciesModelClasses with classnames.
	 * @return bool success?
	 */
	// public function install($arrPreviousDependenciesModelClasses = null)
	// {
	// 	return true;
	// }




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
		//code name
		$this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_CODENAME, '');
		$this->setFieldType(TSysRedemptionCodesAbstract::FIELD_CODENAME, CT_VARCHAR);
		$this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_CODENAME, 100);
		$this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_CODENAME, 0);
		$this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_CODENAME, false);
		$this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_CODENAME, false);
		$this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_CODENAME, true);
		$this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_CODENAME, false);
		$this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_CODENAME, true);
		$this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_CODENAME, null);
		$this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_CODENAME, false);
		$this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_CODENAME, false);
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_CODENAME);									
		
		//redemption code itself
		$this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, '');
		$this->setFieldType(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, CT_VARCHAR);
		$this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, 100);
		$this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, 0);
		$this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, false);
		$this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, false);
		$this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, true);
		$this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, false); //it's already unique'd
		$this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, true); //it's already unique'd
		$this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, null);
		$this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, false);
		$this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, false);
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE);									

		//current redeems
		$this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, 0);
		$this->setFieldType(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, CT_INTEGER64);
		$this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, 0);
		$this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, 0);
		$this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, null);
		$this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS, false);
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS);									

		//max redeems
		$this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, 0);
		$this->setFieldType(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, CT_INTEGER64);
		$this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, 0);
		$this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, 0);
		$this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, null);
		$this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS, false);
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS);									

        //start date
        $this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldType(TSysRedemptionCodesAbstract::FIELD_DATESTART, CT_DATETIME);
        $this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_DATESTART, 0);
        $this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_DATESTART, 0);
        $this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);
        $this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_DATESTART, true);
        $this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);
        $this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);
        $this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);
        $this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_DATESTART, null);
        $this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);
        $this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_DATESTART, false);	
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_DATESTART);			                          

        //end date
        $this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldType(TSysRedemptionCodesAbstract::FIELD_DATEEND, CT_DATETIME);
        $this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_DATEEND, 0);
        $this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_DATEEND, 0);
        $this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);
        $this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_DATEEND, true);
        $this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);
        $this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);
        $this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);
        $this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_DATEEND, null);
        $this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);
        $this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_DATEEND, false);	
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_DATEEND);			

		//enabled
		$this->setFieldDefaultValue(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldType(TSysRedemptionCodesAbstract::FIELD_ISENABLED, CT_BOOL);
		$this->setFieldLength(TSysRedemptionCodesAbstract::FIELD_ISENABLED, 0);
		$this->setFieldDecimalPrecision(TSysRedemptionCodesAbstract::FIELD_ISENABLED, 0);
		$this->setFieldPrimaryKey(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldNullable(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldEnumValues(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldUnique(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldIndexed(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldFulltext(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldForeignKeyClass(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldForeignKeyTable(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldForeignKeyField(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldForeignKeyJoin(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldForeignKeyActionOnDelete(TSysRedemptionCodesAbstract::FIELD_ISENABLED, null);
		$this->setFieldAutoIncrement(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);
		$this->setFieldUnsigned(TSysRedemptionCodesAbstract::FIELD_ISENABLED, false);	
		$this->setFieldEncryptionDisabled(TSysRedemptionCodesAbstract::FIELD_ISENABLED);			                  
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
		return array(TSysRedemptionCodesAbstract::FIELD_CODENAME, 
					TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE, 
					TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS,
					TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS,
					TSysRedemptionCodesAbstract::FIELD_DATESTART,
					TSysRedemptionCodesAbstract::FIELD_DATEEND,
					TSysRedemptionCodesAbstract::FIELD_ISENABLED
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
		return $this->get(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE);
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
		return 'harryvent'.$this->get(TSysRedemptionCodesAbstract::FIELD_CODENAME).'hoe'.$this->get(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE).'is'.$this->get(TSysRedemptionCodesAbstract::FIELD_CURRENTREDEEMS).'het'.$this->get(TSysRedemptionCodesAbstract::FIELD_MAXREDEEMS).'met'.boolToStr($this->get(TSysRedemptionCodesAbstract::FIELD_ISENABLED).'je');
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
		if (strlen($this->get(TSysRedemptionCodesAbstract::FIELD_CODENAME)) == 0)
			return false;

		if (strlen($this->get(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE)) == 0)
			return false;

		//check for invalid chars
		$sCode = '';
		$sCodeFiltered = '';
		$sCode = $this->get(TSysRedemptionCodesAbstract::FIELD_REDEMPTIONCODE);
		$sCodeFiltered = preg_replace( '/[^'.TSysRedemptionCodesAbstract::ALLOWEDCHARSCODE.']/', '', $sCode);
		if ($sCode != $sCodeFiltered)
			return false;

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