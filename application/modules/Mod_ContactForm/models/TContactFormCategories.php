<?php

namespace dr\modules\Mod_ContactForm\models;

use dr\classes\models\TSysModel;


/**
 * Contact Form Categories
 * 
 * TContactFormCategories
 * 
 * Categories are for example:
 * -Product support
 * -Request new features
 * -Marketing
 * -Sales
 * -Legal matter
 * -Report an issue
 * -Pricing
 * -Other
 * 
 * This way, you can easily filter contact form requests
 * 
 * created 12 maart 2024
 * 12 mrt 2024: TContactFormCategories
 * 
 * @author Dennis Renirie
 */

class TContactFormCategories extends TSysModel
{
	const FIELD_CATEGORYNAME			= 'sCategoryName'; //name of the type 
	const FIELD_COLORFOREGROUND			= 'sColorForeground'; //foreground color value in hexadecimals with #, for example: #ffffff = white
	const FIELD_COLORBACKGROUND			= 'sColorBackground'; //background color value in hexadecimals with #, for example: #ffffff = white

	/**
	 * get category name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->get(TContactFormCategories::FIELD_CATEGORYNAME);
	}
	
	/**
	 * set category name
	 * 
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->set(TContactFormCategories::FIELD_CATEGORYNAME, $sName);
	}        
	

	/**
	 * get foreground color value in hexadecimals
	 * 
	 * @return bool
	 */
	public function getColorForeground()
	{
		return $this->get(TContactFormCategories::FIELD_COLORFOREGROUND);
	}
	
	
	/**
	 * set foreground color value in hexadecimals
	 * 
	 * @param string $sColorHex in hexadecimals
	 */
	public function setColorForeground($sHexValue)
	{
		$this->set(TContactFormCategories::FIELD_COLORFOREGROUND, $sHexValue);
	} 	

	/**
	 * get background color value in hexadecimals
	 * 
	 * @return bool
	 */
	public function getColorBackground()
	{
		return $this->get(TContactFormCategories::FIELD_COLORBACKGROUND);
	}
	
	
	/**
	 * set background color value in hexadecimals
	 * 
	 * @param string $sColorHex in hexadecimals
	 */
	public function setColorBackground($sHexValue)
	{
		$this->set(TContactFormCategories::FIELD_COLORBACKGROUND, $sHexValue);
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
		//transaction type name
		$this->setFieldDefaultValue(TContactFormCategories::FIELD_CATEGORYNAME, '');
		$this->setFieldType(TContactFormCategories::FIELD_CATEGORYNAME, CT_VARCHAR);
		$this->setFieldLength(TContactFormCategories::FIELD_CATEGORYNAME, 50);
		$this->setFieldDecimalPrecision(TContactFormCategories::FIELD_CATEGORYNAME, 0);
		$this->setFieldPrimaryKey(TContactFormCategories::FIELD_CATEGORYNAME, false);
		$this->setFieldNullable(TContactFormCategories::FIELD_CATEGORYNAME, false);
		$this->setFieldEnumValues(TContactFormCategories::FIELD_CATEGORYNAME, null);
		$this->setFieldUnique(TContactFormCategories::FIELD_CATEGORYNAME, true); 
		$this->setFieldIndexed(TContactFormCategories::FIELD_CATEGORYNAME, false); 
		$this->setFieldFulltext(TContactFormCategories::FIELD_CATEGORYNAME, true); 
		$this->setFieldForeignKeyClass(TContactFormCategories::FIELD_CATEGORYNAME, null);
		$this->setFieldForeignKeyTable(TContactFormCategories::FIELD_CATEGORYNAME, null);
		$this->setFieldForeignKeyField(TContactFormCategories::FIELD_CATEGORYNAME, null);
		$this->setFieldForeignKeyJoin(TContactFormCategories::FIELD_CATEGORYNAME);
		$this->setFieldForeignKeyActionOnUpdate(TContactFormCategories::FIELD_CATEGORYNAME, null);
		$this->setFieldForeignKeyActionOnDelete(TContactFormCategories::FIELD_CATEGORYNAME, null); 
		$this->setFieldAutoIncrement(TContactFormCategories::FIELD_CATEGORYNAME, false);
		$this->setFieldUnsigned(TContactFormCategories::FIELD_CATEGORYNAME, false);
        $this->setFieldEncryptionDisabled(TContactFormCategories::FIELD_CATEGORYNAME);

		
		//foreground color
		$this->setFieldCopyProps(TContactFormCategories::FIELD_COLORFOREGROUND, TContactFormCategories::FIELD_CATEGORYNAME);
		$this->setFieldLength(TContactFormCategories::FIELD_COLORFOREGROUND, 7);
		$this->setFieldDefaultValue(TContactFormCategories::FIELD_COLORFOREGROUND, '#000000');
		$this->setFieldUnique(TContactFormCategories::FIELD_COLORFOREGROUND, false);

		//background color
		$this->setFieldCopyProps(TContactFormCategories::FIELD_COLORBACKGROUND, TContactFormCategories::FIELD_COLORFOREGROUND);
		$this->setFieldDefaultValue(TContactFormCategories::FIELD_COLORBACKGROUND, '#FFFFFF');
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
		return array(TContactFormCategories::FIELD_CATEGORYNAME, 
					TContactFormCategories::FIELD_COLORFOREGROUND,
					TContactFormCategories::FIELD_COLORBACKGROUND
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
		return false;
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
		return APP_DB_TABLEPREFIX.'ContactFormCategories';
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
		return $this->get(TContactFormCategories::FIELD_CATEGORYNAME);
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
		return 'blaatgaatje'.
			$this->getName().
			$this->getName().
			'12343fe5y5gdfgh67ufsrgrtg';	
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




			//product support
			$this->clear();
			$this->newRecord();
			$this->setName('Product support'); 
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}

			//request feature
			$this->clear();
			$this->newRecord();
			$this->setName('Request new feature'); 
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}

			
			//marketing
			$this->clear();
			$this->newRecord();
			$this->setName('Marketing'); //category
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}

			//sales
			$this->clear();
			$this->newRecord();
			$this->setName('Sales'); 
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}

			//legal matter
			$this->clear();
			$this->newRecord();
			$this->setName('Legal matter');
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}			

			//report an issue
			$this->clear();
			$this->newRecord();
			$this->setName('Report an issue');
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}
			
			//pricing
			$this->clear();
			$this->newRecord();
			$this->setName('Pricing'); 
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}	
			
			//refund
			$this->clear();
			$this->newRecord();
			$this->setName('Refund'); 
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}				
			
			//other
			$this->clear();
			$this->newRecord();
			$this->setName('Other ...'); //category
			$this->setColorBackground('#000000'); //black 
			$this->setColorForeground('#ffffff'); //white
			if (!$this->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, __CLASS__.': error saving contact form category: '.$this->getName($this->getName()));
				return false;
			}    
			
  

		}
			
		return $bSuccess;
	}        	
} 
?>