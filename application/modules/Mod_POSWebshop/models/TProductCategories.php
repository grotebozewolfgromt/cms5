<?php

namespace dr\modules\Mod_POSWebshop\models;

use dr\classes\models\TSysModel;
use dr\classes\models\TTreeModel;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_POSWebshop\models\TVATClasses;

/**
 * Product categories.
 * Products can be stored in 0,1 or more categories (although it makes no sense not to store a product in a category)
 *  
 * 
 * 13 jun 2025: TProductCategories
 * 
 * @author Dennis Renirie
 */

class TProductCategories extends TTreeModel
{
	//no fields except standard
		
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
		parent::defineTable();

		//no field except standard
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
			TProductCategories::FIELD_IMAGEFILE_THUMBNAIL, 
			TProductCategories::FIELD_IMAGEFILE_MEDIUM, 
			TProductCategories::FIELD_IMAGEFILE_LARGE,
			TProductCategories::FIELD_IMAGEFILE_MAX,
			TTreeModel::FIELD_PARENTID, 
			TTreeModel::FIELD_POSITION,
			TTreeModel::FIELD_META_DEPTHLEVEL
					);
	}
	

	
	/**
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'ProductCategories';
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
		return $this->get(TProductCategories::FIELD_ID);
	}
	
	/**
	 * use image in your record?
	 * Then the image_thumbnail, image_medium, image_large and image_max fields are used
    * if you don't want a small and large version, use this one
	*/
	public function getTableUseImageFile()
	{
		return true;
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
	 * additions to the install procedure
	 * 
	 * @param array $arrPreviousDependenciesModelClasses
	 */
	// public function install($arrPreviousDependenciesModelClasses = null)
	// {
	// 	$bSuccess = true;
	// 	$bSuccess = parent::install($arrPreviousDependenciesModelClasses);
		
	// 	return $bSuccess;
	// }        	

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
		return 'helaholahouderdemoedmaarin'.$this->get(TProductCategories::FIELD_ID).'maarnietheus'.$this->get(TTreeModel::FIELD_PARENTID).'echtwel'.$this->get(TTreeModel::FIELD_POSITION);
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

	public function getDisplayRecordColumn()
	{
		return TProductCategoriesLanguages::FIELD_NAME;
	}

	public function getDisplayRecordTable()
	{
		return TProductCategoriesLanguages::getTable();
	}

	/**
	 * can record be trashcanned?
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
		return true;
	}		
} 
?>