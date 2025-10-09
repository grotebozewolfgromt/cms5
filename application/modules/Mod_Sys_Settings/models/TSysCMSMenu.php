<?php

namespace dr\modules\Mod_Sys_Settings\models;

use dr\classes\dom\tag\A;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Ul;
use dr\classes\models\TSysModel;
use dr\classes\models\TTreeModel;
use dr\classes\patterns\TModuleAbstract;
use dr\classes\types\TCurrency;
use dr\classes\types\TDateTime;
use dr\modules\Mod_Sys_Localisation\models\TSysCountries;
use dr\modules\Mod_POSWebshop\models\TVATClasses;

/**
 * Menu in the CMS
 * 
 * 8 aug 2025: TSysCMSMenu created
 * 12 aug 2025: TSysCMSMenu bugfix moveToParentNodeDB(): record werd niet gecleared, dus niet goed geladen uit db
 * 14 aug 2025: TSysCMSMenu rename velden modulecontroller => controller
 * 14 aug 2025: TSysCMSMenu veld modulenameinternal toegevoegd
 * 14 aug 2025: TSysCMSMenu veld opennewtab
 * 14 aug 2025: TSysCMSMenu removeMenuItemsForModuleDB() rewrite for module field
 * 
 * @author Dennis Renirie
 */

class TSysCMSMenu extends TTreeModel
{
	const FIELD_NAMEDEFAULT 		= 'sNameDefault'; //initial name in English. The language files of the module will provide the translation to other languages. This helps us also to identify an record in the database
	const FIELD_CONTROLLER 			= 'sController'; //MVC controller. the name of the module and controller to call (this string includes module name + controller: 'module/controller').Will be overwritten by FIELD_URL when it's in the menu
	const FIELD_MODULENAMEINTERNAL 	= 'sModuleNameInternal'; //internal framework name of the module (=class)
	const FIELD_OPENNEWTAB 			= 'bOpenNewTab'; //open in new tab
	const FIELD_URL 				= 'sURL'; //user can define a custom URL that is used instead of FIELD_CONTROLLER. When FIELD_URL is not empty, FIELD_CONTROLLER is used instead
	const FIELD_SVGICON 			= 'sSVGIcon'; //<svg>-html-tag that defines an icon
	const FIELD_PERMISSIONRESOURCE 	= 'sPermissionResource'; //contains the resource identifier from TSysPermissionsAbstract::FIELD_RESOURCE. If permission is empty, everybody can use it
	const FIELD_ISVISIBLEMENU 		= 'bIsVisibleMenu'; //is visibly shown in menu (left on screen). This way you can disable items without deleting them (preserving the module controller, url and permission-resource)
	const FIELD_ISVISIBLETOOLBAR 	= 'bIsVisibleToolbar'; //is visibly shown in toolbar (top of screen). This way you can disable items without deleting them (preserving the module controller, url and permission-resource)

	public function getNameDefault()
	{
		return $this->get(TSysCMSMenu::FIELD_NAMEDEFAULT);
	}

	public function setNameDefault($sName)
	{
		$this->set(TSysCMSMenu::FIELD_NAMEDEFAULT, $sName);
	}

	public function getController()
	{
		return $this->get(TSysCMSMenu::FIELD_CONTROLLER);
	}

	public function setController($sModuleAndController)
	{
		$this->set(TSysCMSMenu::FIELD_CONTROLLER, $sModuleAndController);
	}	

	public function getModuleNameInternal()
	{
		return $this->get(TSysCMSMenu::FIELD_MODULENAMEINTERNAL);
	}

	public function setModuleNameInternal($sInternalModuleName)
	{
		$this->set(TSysCMSMenu::FIELD_MODULENAMEINTERNAL, $sInternalModuleName);
	}	

	public function getOpenNewTab()
	{
		return $this->get(TSysCMSMenu::FIELD_OPENNEWTAB);
	}

	public function setOpenNewTab($bOpenNewTab)
	{
		$this->set(TSysCMSMenu::FIELD_OPENNEWTAB, $bOpenNewTab);
	}	

	public function getURL()
	{
		return $this->get(TSysCMSMenu::FIELD_URL);
	}

	public function setURL($sURL)
	{
		$this->set(TSysCMSMenu::FIELD_URL, $sURL);
	}	

	public function getSVGIcon()
	{
		return $this->get(TSysCMSMenu::FIELD_SVGICON);
	}

	public function setSVGIcon($sSVGHTMLTag)
	{
		$this->set(TSysCMSMenu::FIELD_SVGICON, $sSVGHTMLTag);
	}	

	public function getPermissionResource()
	{
		return $this->get(TSysCMSMenu::FIELD_PERMISSIONRESOURCE);
	}


	public function setPermissionResource($sResource)
	{
		$this->set(TSysCMSMenu::FIELD_PERMISSIONRESOURCE, $sResource);
	}	

	public function getIsVisibleMenu()
	{
		return $this->get(TSysCMSMenu::FIELD_ISVISIBLEMENU);
	}

	public function setIsVisibleMenu($bVisibleInMenu)
	{
		$this->set(TSysCMSMenu::FIELD_ISVISIBLEMENU, $bVisibleInMenu);
	}	

	public function getIsVisibleToolbar()
	{
		return $this->get(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR);
	}

	public function setIsVisibleToolbar($bVisibleInToolbar)
	{
		$this->set(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, $bVisibleInToolbar);
	}	

	/**
	 * creates a menu based on ALL modules in the framework in the module directory
	 * NOTE: does database action
	 * 
	 * This is not the most performant action, it's not optimized for that.
	 * It's optimized for reusability with the function createMenuItemsForModuleDB() (for 1 module)
	 * 
	 * @return boolean successful. false = error
	 */
	public function createMenuItemsForModulesDB()
	{
		//declare + init
		$iCountModuleFolders = 0;
		$arrModuleFolders = getModuleFolders();
		$bSuccess = true;
		$sCurrMod = '';


		$iCountModuleFolders = count($arrModuleFolders);
        for ($iModIndex = 0; $iModIndex < $iCountModuleFolders; $iModIndex++)
        {
            $sCurrMod = getModuleFullNamespaceClass($arrModuleFolders[$iModIndex]);            
            $objCurrMod = new $sCurrMod;
			
			if (!$this->createMenuItemsForModuleDB($objCurrMod))
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error creating cms menu items for module '.$objCurrMod->getNameDefault() );
				$bSuccess = false; //i dont want to halt, thus I don't 'return' here				
			}
        }

		return $bSuccess;
	}

	/**
	 * creates menu items for 1 module
	 * NOTE: does database action
	 */
	public function createMenuItemsForModuleDB(TModuleAbstract $objModule)
	{
		//init + declare
		$bSuccess = true;
		$objMenuCats = clone $this;
		$objMenuMod = clone $this;
		$objMenuSub = clone $this;


		//==== CATEGORY: retrieve or create		
		$objMenuCats->clear();
		$objMenuCats->loadFromDBByNameDefault($objModule->getCategoryDefault());
		if ($objMenuCats->count() == 0) //if not exists, add it
		{
			$objMenuCats->newRecord();
			$objMenuCats->setNameDefault($objModule->getCategoryDefault());
			$objMenuCats->setModuleNameInternal('');
			$objMenuCats->setURL('');
			$objMenuCats->setSVGIcon('');
			$objMenuCats->setPermissionResource('');
			$objMenuCats->setIsVisibleMenu(true);
			$objMenuCats->setIsVisibleToolbar(false);
			if (!$objMenuCats->saveToDB()) //I want to have record id's
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'error saving category for module to cms menu '.$objModule->getNameDefault() );
				$bSuccess = false; //i dont want to halt, thus I don't 'return' here
			}		
		}

		//==== MODULE LEVEL: create 1 item for the module
		$objMenuMod->clear();
		$objMenuMod->loadFromDBByNameDefault($objModule->getNameDefault());
		if ($objMenuMod->count() == 0) //if not exists, add it		
		{
			$objMenuMod->newRecord();
			$objMenuMod->setNameDefault($objModule->getNameDefault());
			$objMenuMod->setModuleNameInternal(get_class_short($objModule));
			$objMenuMod->setController(''); //no controller
			$objMenuMod->setURL('');
			$objMenuMod->setSVGIcon($objModule->getIconSVG());
			$objMenuMod->setPermissionResource(getAuthResourceString(get_class_short($objModule), AUTH_CATEGORY_MODULEACCESS, AUTH_OPERATION_MODULEACCESS));
			$objMenuMod->setIsVisibleMenu($objModule->isVisibleCMS());			
			$objMenuMod->setIsVisibleToolbar(false);			
			if (!$objMenuMod->saveToDB())
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: error saving module to menu '.$objModule->getNameDefault() );
				$bSuccess = false; //i dont want to halt, thus I don't 'return' here
			}

			//move to proper position in menu (under category)
			//because we first need to save before we can move
			if (!$objMenuMod->moveToParentNodeDB($objMenuMod->getID(), $objMenuCats->getID()))
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: error moving module '.$objModule->getNameDefault().' to parent');
				$bSuccess = false; //i dont want to halt, thus I don't 'return' here
			}
		}


		//==== MODULE SUBITEMS: create 0, 1 or multiple item for the module
		$arrSubMenu = $objModule->getMenuItems();
		if (!is_array($arrSubMenu)) //not array
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: $objModule->getMenuItems() doesnt return an array');
			return false; //quit, is the last item
		}

		if (count($arrSubMenu) > 0) //items exist
		{
			foreach ($arrSubMenu as $arrSubMenuItem)
			{
				$objMenuSub->clear();
				if (!$objMenuSub->loadFromDBByNameDefault($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_NAMEDEFAULT])) //check if exists
				{
					logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: $objMenuSub->loadFromDBByNameDefault() failed');
					$bSuccess = false; //i dont want to halt, thus I don't 'return' here
				}

				if ($objMenuSub->count() == 0)
				{
					$objMenuSub->newRecord();
					$objMenuSub->setNameDefault($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_NAMEDEFAULT]);
					$objMenuSub->setModuleNameInternal(get_class_short($objModule));
					$objMenuSub->setController($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_CONTROLLER]);
					$objMenuSub->setURL('');
					$objMenuSub->setSVGIcon($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_SVGICON]);
					$objMenuSub->setPermissionResource(getAuthResourceString(get_class_short($objModule), $arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_PERMISSIONCATEGORY], TModuleAbstract::PERM_OP_VIEW));
					$objMenuSub->setIsVisibleMenu($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_ISVISIBLEMENU]);			
					$objMenuSub->setIsVisibleToolbar($arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_ISVISIBLETOOLBAR]);			
					if (!$objMenuSub->saveToDB())
					{
						logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: error saving subitem to menu '.$objModule->getNameDefault() );
						$bSuccess = false; //i dont want to halt, thus I don't 'return' here
					}

					//move to proper position in menu (under category)
					//because we first need to save before we can move
					if (!$objMenuSub->moveToParentNodeDB($objMenuSub->getID(), $objMenuMod->getID()))
					{
						logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'cms menu: error moving subitem '.$arrSubMenuItem[TModuleAbstract::AK_CMSMENUITEM_NAMEDEFAULT].' to parent '.$objModule->getNameDefault());
						$bSuccess = false; //i dont want to halt, thus I don't 'return' here
					}			
				}
			}
		}

		return $bSuccess;
	}

	/**
	 * unified function to create a string to input in FIELD_CONTROLLER
	 */
	private function makeModuleControllerString(TModuleAbstract $objModule, $sController = '')
	{
		return get_class_short($objModule).'/'.$sController;
	}

	/**
	 * removes menu items for 1 module from menu
	 * NOTE: does database action
	 */
	public function removeMenuItemsForModuleDB(TModuleAbstract $objModule)
	{

		$objMenu = new TSysCMSMenu();
		$arrMenuItems = $objModule->getMenuItems();
		$bSuccess = true;
		foreach ($arrMenuItems as $arrItem)
		{
			$objMenu->clear();
			$objMenu->where(TSysCMSMenu::FIELD_MODULENAMEINTERNAL, get_class_short($objModule));
			if (!$objMenu->deleteFromDB(true))
			{
				$bSuccess = false;
				error_log('TModuleAbstract->uninstallModule(): deleteFromDB() failed for '.$this->getNameDefault());
			}
			
		}

		return $bSuccess;
	}


	/**
	 * OVERRIDES generateHTMLUlTreeChild() from parent.
	 * function used by generateHTMLUlTreeParent() for recursive calls
	 * 
	 * 
		@todo check aut()
	 * @return Li
	 */
	protected function generateHTMLUlTreeChild(&$objClone)
	{
		//==== PRE EXECUTION CHECKS		
		if (!$objClone->getIsVisibleMenu())
			return null;

		if ($objClone->getPermissionResource() !== '') //skip when resource is empty, then everyone has access
			if (!authRes($objClone->getPermissionResource()))
				return null;
		
		// if (!$objClone->isChecksumValid())
		// 	return null;

		//==== DECLARE + INIT
		$objLi = new Li();
		$sTransItem = '';


		//=== TRANSLATION
		if ($objClone->getModuleNameInternal() !== '') //is empty for everything, except modules
		{
			if ($objClone->getController() === '') //module entry itself
				$sTransItem = transm($objClone->getModuleNameInternal(), TRANS_MODULENAME_MENU, $objClone->getNameDefault());
			else //subitem of module where there is a controller defined
				$sTransItem = transm($objClone->getModuleNameInternal(), 'menuitem_'.$objClone->getNameDefault(), $objClone->getNameDefault());
		}
		else
		{
			$sTransItem = transcms('cmsmenu_item_'.$objClone->getNameDefault(), $objClone->getNameDefault());
		}


		//==== ADD LINK: <a href=""> and text
		if (($objClone->getModuleNameInternal() !== '') || ($objClone->getURL() !== '')) //url exists
		{
			$objA = new A();		
			$objA->setInnerHTML($objClone->getSVGIcon().$sTransItem);

			//link to controller or custom url?
			if ($objClone->getURL() !== '')//custom url overwrites module + controller
			{
				$objA->setHref($objClone->getURL());
			}
			else
			{
				if ($objClone->getController() !== '') //controller exists
					$objA->setHref(getURLModule($objClone->getModuleNameInternal()).'/'.$objClone->getController()); //call controller
				else //no controller
					$objA->setHref(getURLModule($objClone->getModuleNameInternal())); //call index.php
			}
				

			if ($objClone->getOpenNewTab())
				$objA->setTarget(A::TARGET_BLANK);

			$objLi->appendChild($objA);
		}
		else //add only text
		{
			$objLi->setTextContent($objClone->getSVGIcon().$sTransItem);
		}


		//=== RECURSIVE CALL PARENT FUNCTION for the children of this node
		$objULChildren = $this->generateHTMLUlTree($objClone->getID());
		if ($objULChildren !== null)
			$objLi->appendChild($objULChildren);

		return $objLi;
	}	
	
	/**
	 * load from database when you have the id
	 * The result is always one row
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_ID, id)
	 * model->loadfromdb()
	 * 
	 * @param mixed $mAutoJoinDefinedTables: either int or bool. 
	 * 				-1 		= unlimited levels
	 * 				false 	= 0 levels deep --> no auto join
	 * 				0 		= 0 levels deep --> no auto join
	 * 				true 	= 1 level deep 
	 * 				1		= 1 level deep 
	 * 				2		= 2 levels deep
	 * 				3		= 3 levels deep
	 * 				etc.
	 */
	public function loadFromDBByNameDefault($sNameDefault, $mAutoJoinDefinedTables = 0)
	{
		$this->find(TSysCMSMenu::FIELD_NAMEDEFAULT, $sNameDefault, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), CT_VARCHAR);
		$this->limitOne();
		return $this->loadFromDB($mAutoJoinDefinedTables);
	}		

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

		//name
		$this->setFieldDefaultsVarChar(TSysCMSMenu::FIELD_NAMEDEFAULT, 50);		

		//module controller
		$this->setFieldDefaultsVarChar(TSysCMSMenu::FIELD_CONTROLLER, 100);

		//module
		$this->setFieldDefaultsVarChar(TSysCMSMenu::FIELD_MODULENAMEINTERNAL, 100);

		//open new tab
		$this->setFieldDefaultsBoolean(TSysCMSMenu::FIELD_OPENNEWTAB);

		//url
		$this->setFieldDefaultsVarChar(TSysCMSMenu::FIELD_URL, 100);

		//svg icon
		$this->setFieldDefaultsLongText(TSysCMSMenu::FIELD_SVGICON);

		//permission resource from TSysPermissionsAbstract::FIELD_RESOURCE (so must have the same length)
		$this->setFieldDefaultsVarChar(TSysCMSMenu::FIELD_PERMISSIONRESOURCE, 255);

		//visible menu
		$this->setFieldDefaultsBoolean(TSysCMSMenu::FIELD_ISVISIBLEMENU);

		//visible toolbar
		$this->setFieldDefaultsBoolean(TSysCMSMenu::FIELD_ISVISIBLETOOLBAR);
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
			TSysCMSMenu::FIELD_NAMEDEFAULT, 
			TSysCMSMenu::FIELD_CONTROLLER, 
			TSysCMSMenu::FIELD_MODULENAMEINTERNAL, 
			TSysCMSMenu::FIELD_OPENNEWTAB, 
			TSysCMSMenu::FIELD_URL, 
			TSysCMSMenu::FIELD_SVGICON, 
			TSysCMSMenu::FIELD_PERMISSIONRESOURCE, 
			TSysCMSMenu::FIELD_ISVISIBLEMENU, 
			TSysCMSMenu::FIELD_ISVISIBLETOOLBAR, 
			TTreeModel::FIELD_PARENTID, 
			TTreeModel::FIELD_POSITION,
			TTreeModel::FIELD_META_DEPTHLEVEL,
					);
	}
	

	
	/**
	 * de child moet deze overerven
	 *
	 * @return string naam van de databasetabel
	*/
	public static function getTable()
	{
		return APP_DB_TABLEPREFIX.'SysCMSMenu';
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
		return $this->get(TSysCMSMenu::FIELD_NAMEDEFAULT);
	}
	
	/**
	 * use image in your record?
	 * Then the image_thumbnail, image_medium, image_large and image_max fields are used
    * if you don't want a small and large version, use this one
	*/
	public function getTableUseImageFile()
	{
		return false;
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
	public function install($arrPreviousDependenciesModelClasses = null)
	{
		if (!parent::install($arrPreviousDependenciesModelClasses))
			return false;

		if (!$this->createMenuItemsForModulesDB())
			return false;

		return true;
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
		// return 'df23r23rfsadvfg34ghmj'.$this->get(TSysCMSMenu::FIELD_NAMEDEFAULT).'dhj34oijfd'.$this->get(TTreeModel::FIELD_PARENTID).'122233pelk,'.$this->get(TTreeModel::FIELD_POSITION).'dcfsedf'.$this->get(TSysCMSMenu::FIELD_URL).$this->get(TSysCMSMenu::FIELD_PERMISSIONRESOURCE).$this->get(TSysCMSMenu::FIELD_SVGICON).$this->get(TSysCMSMenu::FIELD_MODULENAMEINTERNAL);
		return 'df23r23rfsadvfg34ghmj'.$this->get(TSysCMSMenu::FIELD_NAMEDEFAULT).'dhj34oijfd'.$this->get(TTreeModel::FIELD_PARENTID).'122233pelk,'.$this->get(TTreeModel::FIELD_POSITION).'dcfsedf'.$this->get(TSysCMSMenu::FIELD_URL).$this->get(TSysCMSMenu::FIELD_PERMISSIONRESOURCE).$this->get(TSysCMSMenu::FIELD_MODULENAMEINTERNAL);
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
		return false;
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

	public function getDisplayRecordColumn()
	{
		return TSysCMSMenu::FIELD_NAMEDEFAULT;
	}

	public function getDisplayRecordTable()
	{
		return $this::getTable();
	}
} 
?>