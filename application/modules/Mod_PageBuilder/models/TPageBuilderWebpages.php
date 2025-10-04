<?php

// namespace dr\classes\models;
namespace dr\modules\Mod_PageBuilder\models;

use dr\classes\dom\tag\form\Select;
use dr\modules\Mod_PageBuilder\models\TPageBuilderDocumentsAbstract;
use dr\classes\models\TSysModel;
use dr\modules\Mod_PageBuilder\controllers\pagebuilder;

/**
 * Pagebuilder pages
 * Inherited for use to create webpages
 * 
 * created 25-4-2024
 * 25 apr 2024: TPageBuilderWebpages: created
 * 
 * @author Dennis Renirie
 */

class TPageBuilderWebpages extends TPageBuilderDocumentsAbstract
{


	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{				
		parent::defineTable();
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
		return parent::getFieldsPublic();
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
		return parent::getChecksumUncrypted();
	}


	/**
	 * the child has to inherit this
	 *
	 * @return string name of database table
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'Webpages';
	}    	
} 
?>