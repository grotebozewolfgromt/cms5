<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_CMSUsers\models;

use dr\modules\Mod_Sys_CMSUsers\models\TSysCMSUsersRoles;
use dr\classes\models\TSysModel;

/**
 * Description of TSysCMSUsersRolesAssignUsers
 * 
 * This database table gives roles the permission to assign users to the other roles 
 * 
 * For example:
 * The role "PRO account admin" (id 4), must be able to add users with the role "PRO account script writer" (id: 6).
 * In this entry the data would be: FIELD_ROLEID = 4 and FIELD_ALLOWEDTOASSIGNUSERSTOROLEID = 6
 *
 * 
 * 28 nov 2022: TSysCMSUsersRolesAddDeleteUsers created
 * 
 * 
 * @author drenirie
 */
class TSysCMSUsersRolesAssignUsers extends TSysModel
{
    const FIELD_ROLEID                      = 'iRoleID'; //id of current role
    const FIELD_ALLOWEDASSIGNUSERSROLEID    = 'iAllowedAssingUsersRoleID';  //FIELD_ROLEID is allowed to assign users to FIELD_ALLOWEDASSIGNUSERSROLEID
    

    /**
     * get role id
     * 
     * @return int
     */
    public function getRoleID()
    {
        return $this->get(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID);
    }

    /**
     * set role id
     * 
     * @param int $iID
     */
    public function setRoleID($iID)
    {
        $this->set(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, $iID);
    }


    /**
     * get role id that this role is allowed to assign users to
     * 
     * @return int
     */
    public function getAllowedAssignUsersRoleID()
    {
        return $this->get(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID);
    }

    /**
     * set role id that this role is allowed to assign users to
     * 
     * @param int $iID
     */
    public function setAllowedAssignUsersRoleID($iID)
    {
        $this->set(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, $iID);
    }


    /**
     * This function is called in the constructor and the clear() function
     * this is used to define default values for fields
     * 
     * initialize values
     */
    public function initRecord()
    {    
    }


    /**
     * defines the fields in the tables
     * i.e. types, default values, enum values, referenced tables etc
    */
    public function defineTable()
    {
        //role id
        $this->setFieldDefaultValue(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, '');
        $this->setFieldType(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, CT_INTEGER64);
        $this->setFieldLength(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, 0);
        $this->setFieldDecimalPrecision(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, 0);
        $this->setFieldPrimaryKey(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, false);
        $this->setFieldNullable(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, false);
        $this->setFieldEnumValues(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, null);
        $this->setFieldUnique(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, false);
        $this->setFieldIndexed(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, true);
        $this->setFieldFulltext(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, false);
        $this->setFieldForeignKeyClass(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, TSysCMSUsersRoles::class);
        $this->setFieldForeignKeyTable(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, TSysCMSUsersRoles::getTable());
        $this->setFieldForeignKeyField(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, TSysCMSUsersRoles::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldAutoIncrement(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, false);
        $this->setFieldUnsigned(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID, true);
		$this->setFieldEncryptionDisabled(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID);


        //role id of users allowed to assign
        $this->setFieldDefaultValue(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, '');
        $this->setFieldType(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, CT_INTEGER64);
        $this->setFieldLength(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, 0);
        $this->setFieldDecimalPrecision(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, 0);
        $this->setFieldPrimaryKey(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, false);
        $this->setFieldNullable(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, false);
        $this->setFieldEnumValues(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, null);
        $this->setFieldUnique(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, false);
        $this->setFieldIndexed(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, true);
        $this->setFieldFulltext(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, false);
        $this->setFieldForeignKeyClass(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, TSysCMSUsersRoles::class);
        $this->setFieldForeignKeyTable(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, TSysCMSUsersRoles::getTable());
        $this->setFieldForeignKeyField(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, TSysCMSUsersRoles::FIELD_ID);
        $this->setFieldForeignKeyJoin(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, null);
        $this->setFieldForeignKeyActionOnUpdate(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldForeignKeyActionOnDelete(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
        $this->setFieldAutoIncrement(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, false);
        $this->setFieldUnsigned(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID, true);
		$this->setFieldEncryptionDisabled(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID);


    }

    public static function getTable()
    {
        return APP_DB_TABLEPREFIX.'SysCMSUsersRolesAssignUsers';
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
        return array(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID);
    }


    /**
     * use the auto-added id-field ?
     * @return bool
    */
    public function getTableUseIDField()
    {
        return false;	
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
        return $this->get(TSysCMSUsersRolesAssignUsers::FIELD_ID);
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
        return 'af44tpfm_'.$this->get(TSysCMSUsersRolesAssignUsers::FIELD_ROLEID).'hoenk'.$this->get(TSysCMSUsersRolesAssignUsers::FIELD_ALLOWEDASSIGNUSERSROLEID);
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
