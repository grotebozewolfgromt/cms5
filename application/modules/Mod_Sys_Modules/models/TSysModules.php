<?php
/**
 * This is the model representing the modules in the database
 * this is so we can have a categorized structure
 * 
 * Don't confuse this with TModuleAbstract: the business logic class the represents 1 module in the root of a module directory
 */
// namespace dr\classes\models;
namespace dr\modules\Mod_Sys_Modules\models;

use dr\classes\models\TSysModel;
use dr\modules\Mod_Sys_Modules\models\TSysModulesCategories;


class TSysModules extends TSysModel
{
	const FIELD_NAMEINTERNAL 		= 'sNameInternal'; //the internal name for a module (equal to class name and directory name)
	const FIELD_MODULETYPE			= 'sModuleType';
	// const FIELD_MODULECATEGORYID 	= 'iModuleCategoryID';
	const FIELD_VISIBLECMS 			= 'bVisibleCMS';
	const FIELD_VISIBLEFRONTEND 	= 'bVisibleFrontEnd';
	const FIELD_NAMENICETRANSDEFAULT= 'sNameNiceTransDefault'; //the default english translation for a module name
	const FIELD_ICONSVG				= 'sIconSVG'; //the svg icon

	
	/**
	 * set the internal module name (the name that is internally used)
	 * (namedefault is a default english translated name)
	 */
	public function setNameInternal($sName)
	{
		$this->set(TSysModules::FIELD_NAMEINTERNAL, $sName);
	}
		
	/**
	 * get the internal module name (the name that is internally used)
	 * (namedefault is a default english translated name)
	 */
	public function getNameInternal()
	{
        return $this->get(TSysModules::FIELD_NAMEINTERNAL);
	}
	
	/**
	 * set the module type
	 * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
	 */
	public function setModuleType($sModuleType)
	{
		$this->set(TSysModules::FIELD_MODULETYPE, $sModuleType);
	}
		
	/**
	 * get the module type
	 * MOD_TYPE_SYSTEM, MOD_TYPE_REGULAR, MOD_TYPE_PAGEBUILDER
	 */
	public function getModuleType()
	{
        return $this->get(TSysModules::FIELD_MODULETYPE);
	}	
	 
	// /**
	//  * get category of module
	//  */
	// public function getCategoryID()
	// {
    //     return $this->get(TSysModules::FIELD_MODULECATEGORYID);
	// }
	
	// /**
	//  * set category of module
	//  */
	// public function setCategoryID($iID)
	// {
	// 	$this->set(TSysModules::FIELD_MODULECATEGORYID, $iID);
	// }        

	/**
	 * get if module is visible in menu
	 *
	 * @return void
	 */	
	public function getVisibleCMS()
	{
        return $this->get(TSysModules::FIELD_VISIBLECMS);
	}
	
	/**
	 * set if module is visible in menu
	 *
	 * @param bool $bVisible
	 * @return void
	 */
	public function setVisibleCMS($bVisible)
	{
		$this->set(TSysModules::FIELD_VISIBLECMS, $bVisible);
	}	

	/**
	 * get if module is visible in menu
	 *
	 * @return void
	 */	
	public function getVisibleFrontEnd()
	{
        return $this->get(TSysModules::FIELD_VISIBLEFRONTEND);
	}
	
	/**
	 * set if module is visible in menu
	 *
	 * @param bool $bVisible
	 * @return void
	 */
	public function setVisibleFrontEnd($bVisible)
	{
		$this->set(TSysModules::FIELD_VISIBLEFRONTEND, $bVisible);
	}		

	/**
	 * get the default english name for a module
	 * this name is used by transm() as default english translatop
	 *
	 * @return string
	 */

	public function getNameDefault()
	{
		return $this->get(TSysModules::FIELD_NAMENICETRANSDEFAULT);
	}

	/**
	 * set the default english name for a module
	 * this name is used by transm() as default english translatop
	 *
	 * @param string $sDefaultEnglish
	 * @return void
	 */
	public function setNameDefault($sDefaultEnglish)
	{
		$this->set(TSysModules::FIELD_NAMENICETRANSDEFAULT, $sDefaultEnglish);
	}		

	/**
	 * get the svg icon of a module
	 * 
	 * example: return '<svg><path="blabla"></svg>';
	 *
	 * @return string
	 */
	public function getIconSVG()
	{
		return $this->get(TSysModules::FIELD_ICONSVG);
	}

	/**
	 * get the svg icon of a module
	 * 
	 * example: return '<svg><path="blabla"></svg>';
	 *
	 *
	 * @param string $sIcon
	 * @return void
	 */
	public function setIconSVG($sIcon)
	{
		$this->set(TSysModules::FIELD_ICONSVG, $sIcon);
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
			//internal name
		$this->setFieldDefaultValue(TSysModules::FIELD_NAMEINTERNAL, '');
		$this->setFieldType(TSysModules::FIELD_NAMEINTERNAL, CT_VARCHAR);
		$this->setFieldLength(TSysModules::FIELD_NAMEINTERNAL, 100);
		$this->setFieldDecimalPrecision(TSysModules::FIELD_NAMEINTERNAL, 0);
		$this->setFieldPrimaryKey(TSysModules::FIELD_NAMEINTERNAL, false);
		$this->setFieldNullable(TSysModules::FIELD_NAMEINTERNAL, false);
		$this->setFieldEnumValues(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldUnique(TSysModules::FIELD_NAMEINTERNAL, true);
		$this->setFieldIndexed(TSysModules::FIELD_NAMEINTERNAL, false); //already unique
		$this->setFieldFulltext(TSysModules::FIELD_NAMEINTERNAL, true);
		$this->setFieldForeignKeyClass(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyTable(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyField(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyJoin(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_NAMEINTERNAL, null);
		$this->setFieldAutoIncrement(TSysModules::FIELD_NAMEINTERNAL, false);
		$this->setFieldUnsigned(TSysModules::FIELD_NAMEINTERNAL, false);
        $this->setFieldEncryptionDisabled(TSysModules::FIELD_NAMEINTERNAL);				

			//module type
		$this->setFieldDefaultValue(TSysModules::FIELD_MODULETYPE, '');
		$this->setFieldType(TSysModules::FIELD_MODULETYPE, CT_VARCHAR);
		$this->setFieldLength(TSysModules::FIELD_MODULETYPE, 20);
		$this->setFieldDecimalPrecision(TSysModules::FIELD_MODULETYPE, 0);
		$this->setFieldPrimaryKey(TSysModules::FIELD_MODULETYPE, false);
		$this->setFieldNullable(TSysModules::FIELD_MODULETYPE, false);
		$this->setFieldEnumValues(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldUnique(TSysModules::FIELD_MODULETYPE, false);
		$this->setFieldIndexed(TSysModules::FIELD_MODULETYPE, true);
		$this->setFieldFulltext(TSysModules::FIELD_MODULETYPE, false);
		$this->setFieldForeignKeyClass(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldForeignKeyTable(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldForeignKeyField(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldForeignKeyJoin(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_MODULETYPE, null);
		$this->setFieldAutoIncrement(TSysModules::FIELD_MODULETYPE, false);
		$this->setFieldUnsigned(TSysModules::FIELD_MODULETYPE, false);
        $this->setFieldEncryptionDisabled(TSysModules::FIELD_MODULETYPE);				
                
        //         //category	
		// $this->setFieldDefaultValue(TSysModules::FIELD_MODULECATEGORYID, 0);
		// $this->setFieldType(TSysModules::FIELD_MODULECATEGORYID, CT_INTEGER64);
		// $this->setFieldLength(TSysModules::FIELD_MODULECATEGORYID, 0);
		// $this->setFieldDecimalPrecision(TSysModules::FIELD_MODULECATEGORYID, 0);
		// $this->setFieldPrimaryKey(TSysModules::FIELD_MODULECATEGORYID, false);
		// $this->setFieldNullable(TSysModules::FIELD_MODULECATEGORYID, false);
		// $this->setFieldEnumValues(TSysModules::FIELD_MODULECATEGORYID, null);
		// $this->setFieldUnique(TSysModules::FIELD_MODULECATEGORYID, false);
		// $this->setFieldIndexed(TSysModules::FIELD_MODULECATEGORYID, true);
		// $this->setFieldFulltext(TSysModules::FIELD_MODULECATEGORYID, true);
		// $this->setFieldForeignKeyClass(TSysModules::FIELD_MODULECATEGORYID, TSysModulesCategories::class);
		// $this->setFieldForeignKeyTable(TSysModules::FIELD_MODULECATEGORYID, TSysModulesCategories::getTable());
		// $this->setFieldForeignKeyField(TSysModules::FIELD_MODULECATEGORYID, TSysModel::FIELD_ID);
		// $this->setFieldForeignKeyJoin(TSysModules::FIELD_MODULECATEGORYID);
		// $this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_MODULECATEGORYID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);
		// $this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_MODULECATEGORYID, TSysModel::FOREIGNKEY_REFERENCE_RESTRICT);
		// $this->setFieldAutoIncrement(TSysModules::FIELD_MODULECATEGORYID, false);
		// $this->setFieldUnsigned(TSysModules::FIELD_MODULECATEGORYID, true);  
        // $this->setFieldEncryptionDisabled(TSysModules::FIELD_MODULECATEGORYID);						            


		//visible backend
		$this->setFieldDefaultValue(TSysModules::FIELD_VISIBLECMS, true);
		$this->setFieldType(TSysModules::FIELD_VISIBLECMS, CT_BOOL);
		$this->setFieldLength(TSysModules::FIELD_VISIBLECMS, 0);
		$this->setFieldDecimalPrecision(TSysModules::FIELD_VISIBLECMS, 0);
		$this->setFieldPrimaryKey(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldNullable(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldEnumValues(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldUnique(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldIndexed(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldFulltext(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldForeignKeyClass(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldForeignKeyTable(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldForeignKeyField(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldForeignKeyJoin(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_VISIBLECMS, null);
		$this->setFieldAutoIncrement(TSysModules::FIELD_VISIBLECMS, false);
		$this->setFieldUnsigned(TSysModules::FIELD_VISIBLECMS, false);	
        $this->setFieldEncryptionDisabled(TSysModules::FIELD_VISIBLECMS);	
		
		//visible frontend
		$this->setFieldCopyProps(TSysModules::FIELD_VISIBLEFRONTEND, TSysModules::FIELD_VISIBLECMS);

		
		//default english name
		$this->setFieldDefaultValue(TSysModules::FIELD_NAMENICETRANSDEFAULT, '');
		$this->setFieldType(TSysModules::FIELD_NAMENICETRANSDEFAULT, CT_VARCHAR);
		$this->setFieldLength(TSysModules::FIELD_NAMENICETRANSDEFAULT, 255);
		$this->setFieldDecimalPrecision(TSysModules::FIELD_NAMENICETRANSDEFAULT, 0);
		$this->setFieldPrimaryKey(TSysModules::FIELD_NAMENICETRANSDEFAULT, false);
		$this->setFieldNullable(TSysModules::FIELD_NAMENICETRANSDEFAULT, false);
		$this->setFieldEnumValues(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldUnique(TSysModules::FIELD_NAMENICETRANSDEFAULT, false);
		$this->setFieldIndexed(TSysModules::FIELD_NAMENICETRANSDEFAULT, true);
		$this->setFieldFulltext(TSysModules::FIELD_NAMENICETRANSDEFAULT, true);
		$this->setFieldForeignKeyClass(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldForeignKeyTable(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldForeignKeyField(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldForeignKeyJoin(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_NAMENICETRANSDEFAULT, null);
		$this->setFieldAutoIncrement(TSysModules::FIELD_NAMENICETRANSDEFAULT, false);
		$this->setFieldUnsigned(TSysModules::FIELD_NAMENICETRANSDEFAULT, false);
        $this->setFieldEncryptionDisabled(TSysModules::FIELD_NAMENICETRANSDEFAULT);				
		
		//icon
		$this->setFieldDefaultValue(TSysModules::FIELD_ICONSVG, '');
		$this->setFieldType(TSysModules::FIELD_ICONSVG, CT_LONGTEXT);
		$this->setFieldLength(TSysModules::FIELD_ICONSVG, 0);
		$this->setFieldDecimalPrecision(TSysModules::FIELD_ICONSVG, 0);
		$this->setFieldPrimaryKey(TSysModules::FIELD_ICONSVG, false);
		$this->setFieldNullable(TSysModules::FIELD_ICONSVG, true);
		$this->setFieldEnumValues(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldUnique(TSysModules::FIELD_ICONSVG, false);
		$this->setFieldIndexed(TSysModules::FIELD_ICONSVG, false);
		$this->setFieldFulltext(TSysModules::FIELD_ICONSVG, false);
		$this->setFieldForeignKeyClass(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldForeignKeyTable(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldForeignKeyField(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldForeignKeyJoin(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldForeignKeyActionOnUpdate(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldForeignKeyActionOnDelete(TSysModules::FIELD_ICONSVG, null);
		$this->setFieldAutoIncrement(TSysModules::FIELD_ICONSVG, false);
		$this->setFieldUnsigned(TSysModules::FIELD_ICONSVG, false);
        $this->setFieldEncryptionDisabled(TSysModules::FIELD_ICONSVG);			
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
		return array(TSysModules::FIELD_NAMEINTERNAL, TSysModules::FIELD_POSITION);
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
	 * use checkout for locking record for editing
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
		return APP_DB_TABLEPREFIX.'SysModules';
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
		return $this->get(TSysModules::FIELD_NAMEINTERNAL);
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
		return 'picasso'.$this->get(TSysModules::FIELD_NAMEINTERNAL).'schilderij'.$this->get(TSysModules::FIELD_NAMEINTERNAL).''.$this->get(TSysModules::FIELD_NAMEINTERNAL).'strepen'.$this->get(TSysModules::FIELD_NAMEINTERNAL).'schilderijen';
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
         * additions to the install procedure
         * 
         * @param array $arrPreviousDependenciesModelClasses
         */
        // public function install($arrPreviousDependenciesModelClasses = null)
        // {
        //     $bSuccess = true;
        //     $bSuccess = parent::install($arrPreviousDependenciesModelClasses);
            
        //     error_log('TSysModules: running sychronizeDirStructure()');
        //     if (!$this->sychronizeDirStructure())
        //     {
        //         error('TSysModules: sychronizeDirStructure() failed');
        //         return false;
        //     }
                
        //     return $bSuccess;
        // }
         
        /**
         * synchronize the databasetable with the directory structure.
         * 
         * this will do 2 things:
         * 1) reads the directory structure, checks if all directories have a record in the database, if record does not exists, it will be added
         * 2) reads the modules from the table and checks in all directories exist. if directory not exists, it removes the record from the database
         */
		/* removed 25-4-2024 wasnt used anywhere
        public function sychronizeDirStructure()
        {
            $bSuccess = true;
            
            //====read modules from database and check if directories exist
            $this->resetQueryBuilder();
            $this->loadFromDB();
                    
            while ($this->next())        
            {
                if (!is_dir(APP_PATH_MODULES.DIRECTORY_SEPARATOR.$this->getNameInternal()))
                {
                    if (!$this->deleteFromDB_OLD($this->getID()))
                    {
                        error('database-table removal of module '.$this->getNameInternal().' failed in '.$this::getTable());
                        return false;
                    }
                }
            }
            
            //====read directory structure and add new modules that not exist
            $arrFolders = getFileFolderArray(APP_PATH_MODULES, true);
            $bRecordFoundInDB = false;
            $iCatID = 0;
            
            //==request default category to put the module in (just the first one in the list)
            $objTempCat = new TSysModulesCategories();  
            $objTempCat->limitOne(); //we need just one record to be returned
            if (!$objTempCat->loadFromDB())
            {
                error('TSysModules: $objTempCat->loadFromDB() failed in sychronizeDirStructure()');
                return false;
            }
            $iCatID = $objTempCat->getID();
            unset($objTempCat);
            
            
            //==check if at least one category exists, if not, then add it
            foreach($arrFolders as $sFolder)
            {
                $bRecordFoundInDB = false;
                $this->resetRecordPointer();
                while($this->next())//look if we can find the name in the resultset
                {
                    if ($this->getNameInternal() == $sFolder)
                        $bRecordFoundInDB = true;
                }
                
                if (!$bRecordFoundInDB) //folder not found in db, add it
                {
                    $objTempNewMod = new TSysModules();
                    $objTempNewMod->newRecord();
                    $objTempNewMod->setNameInternal($sFolder);
                    $objTempNewMod->setCategoryID($iCatID);
                    if (!$objTempNewMod->saveToDB())
                        return false;
                    unset($objTempNewMod);
                }
                
            }
            
            return $bSuccess;
        }
		*/
        
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