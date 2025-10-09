<?php

namespace dr\classes\models;

use dr\classes\models\TSysModel;

/**
 * An abstract class for usergroup permissions
 * 
 * when you want to inherit this class: add a field to link to a user/role/usergroup
 * 
 * the resource is the resource you want to protect, for example: "books/authors/view"
 * 
 * created 30-10-2020
 * 30 okt 2020: TSysPermissionsAbstract: created
 * 
 */

abstract class TSysPermissionsAbstract extends TSysModel
{
    const FIELD_RESOURCE                = 'sResource';
    const FIELD_ALLOWED                 = 'bAllowed';     
           
        
    /**
     * get resource
     * 
     * @return string
     */
    public function getResource()
    {
        return $this->get(TSysPermissionsAbstract::FIELD_RESOURCE);
    }

    /**
     * set resource
     * 
     * @param string $sName
     */
    public function setResource($sName)
    {
        $this->set(TSysPermissionsAbstract::FIELD_RESOURCE, $sName);
    }


    /**
     * get allowed
     * 
     * @return bool
     */
    public function getAllowed()
    {
        return $this->get(TSysPermissionsAbstract::FIELD_ALLOWED);
    }

    /**
     * set allowed
     * 
     * @param string $sName
     */
    public function setAllowed($bAllowed)
    {
        $this->set(TSysPermissionsAbstract::FIELD_ALLOWED, $bAllowed);
    }


    
    
    /**
     * This function is called in the constructor and the clear() function
     * this is used to define default values for fields
     * 
     * initialize values
     */
    public function initRecord()
    {
        $this->setResource('');
        $this->setAllowed(false);    
    }
	
	
	
    /**
     * defines the fields in the tables
     * i.e. types, default values, enum values, referenced tables etc
    */
    public function defineTable()
    {
       

        //resource
        $this->setFieldDefaultValue(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldType(TSysPermissionsAbstract::FIELD_RESOURCE, CT_VARCHAR);
        $this->setFieldLength(TSysPermissionsAbstract::FIELD_RESOURCE, 255);
        $this->setFieldDecimalPrecision(TSysPermissionsAbstract::FIELD_RESOURCE, 0);
        $this->setFieldPrimaryKey(TSysPermissionsAbstract::FIELD_RESOURCE, false);
        $this->setFieldNullable(TSysPermissionsAbstract::FIELD_RESOURCE, true);
        $this->setFieldEnumValues(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldUnique(TSysPermissionsAbstract::FIELD_RESOURCE, false);
        $this->setFieldIndexed(TSysPermissionsAbstract::FIELD_RESOURCE, false);
        $this->setFieldFulltext(TSysPermissionsAbstract::FIELD_RESOURCE, false);
        $this->setFieldForeignKeyClass(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldForeignKeyTable(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldForeignKeyField(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldForeignKeyJoin(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldForeignKeyActionOnDelete(TSysPermissionsAbstract::FIELD_RESOURCE, null);
        $this->setFieldAutoIncrement(TSysPermissionsAbstract::FIELD_RESOURCE, false);
        $this->setFieldUnsigned(TSysPermissionsAbstract::FIELD_RESOURCE, false);
		$this->setFieldEncryptionDisabled(TSysPermissionsAbstract::FIELD_RESOURCE);

        //allowed
        $this->setFieldDefaultValue(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldType(TSysPermissionsAbstract::FIELD_ALLOWED, CT_BOOL);
        $this->setFieldLength(TSysPermissionsAbstract::FIELD_ALLOWED, 0);
        $this->setFieldDecimalPrecision(TSysPermissionsAbstract::FIELD_ALLOWED, 0);
        $this->setFieldPrimaryKey(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldNullable(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldEnumValues(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldUnique(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldIndexed(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldFulltext(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldForeignKeyClass(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldForeignKeyTable(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldForeignKeyField(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldForeignKeyJoin(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldForeignKeyActionOnDelete(TSysPermissionsAbstract::FIELD_ALLOWED, null);
        $this->setFieldAutoIncrement(TSysPermissionsAbstract::FIELD_ALLOWED, false);
        $this->setFieldUnsigned(TSysPermissionsAbstract::FIELD_ALLOWED, false);	
		$this->setFieldEncryptionDisabled(TSysPermissionsAbstract::FIELD_ALLOWED);

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
        return array(TSysPermissionsAbstract::FIELD_RESOURCE);
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
        //out of security reasons disabled, otherwise you could see the time a user changed his password, which makes the time element in a password vulnerable
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
        return $this->get(TSysPermissionsAbstract::FIELD_RESOURCE);
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

    /****************************************************************************
     *              ABSTRACT METHODS
    ****************************************************************************/
    

    
}

?>