<?php
namespace dr\classes\models;

use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\Ul;
use dr\classes\models\TSysModel;


/**
 * This class represents a hierarchical data tree in a database table
 * 
 * ==== STATE =====
 * There are 2 states that this object can be in:
 * 1. Flat	- Default state. Resembles any other TSysModel. 2d flat representation of database table
 * 2. DOM 	- Document Object Model. Just like HTML where there can be children within children within
 * 
 * To convert a STATE_FLAT to STATE_DOM:
 * Do a loadFromDB() and then convert it to a DOM with $this->convertStateToDOM()
 * 
 * 
 * ==== WARNING ====
 * - The position field is crucial to this class working properly !!!
 * - this class assumes you call orderBy(TTreeModel::FIELD_POSITION) BEFORE loadFromDB():
 *   otherwise items are out of order
 * - if you want to insert a new record somewhere in the tree, you first need to add it (saveToDB()), then you can move it
 * 
 * 
 * 14 aug 2025: TTreeModel: added generateHTMLUlTree(), generateHTMLUlTreeParent(), generateHTMLUlTreeChild()
 * 15 aug 2025: TTreeModel: added deleteFromDB() overloaded from parent
 * 
 * @todo on delete remove also children
 */
abstract class TTreeModel extends TSysModel
{
	const FIELD_PARENTID 		= 'iParentID'; //parentnode in same table. when ID=0 it is root level
	const FIELD_META_DEPTHLEVEL = 'iMetaDepthLevel'; //0 = root level. I cache the depth level of a node to make constructing the tree in code easier. When constructing a tree it can happen that a parent-id is not loaded yet, thus you can't attach the node to it. By storing the depth you make sql queries that do: SORT BY iDepthLevel, iOrderDepth. If you want to display nodes, you can use this number also for identation (tabs x level)
	
	const STATE_FLAT 			= 0; //2d flat model like any other TSysModel(), representing a database table
	const STATE_DOM				= 1; //dom-like hierarchy where there can be child-nodes within child-nodes within child nodes

	private $iTreeState 		= TTreeModel::STATE_FLAT; //state of this tree model. State has nothing to do with the datase, only the internal representation of the data in the tree
	
	protected $objChildNodes 	= null; //contains TTreeModel with child nodes, which each can have a TTreeMode() with objChildNodes. only filled when $iTreeState == STATE_DOM

	/**
	 * parent node id in this table. 
	 * 0 = root node
	 */
	public function getParentID()
	{
		return $this->get(TTreeModel::FIELD_PARENTID);
	}

	/**
	 * parent node id in this table. 
	 * 0 = root node
	 */	
	public function setParentID($iID)
	{
		$this->set(TTreeModel::FIELD_PARENTID, $iID);
	}
	
	/**
	 * Stores the order of nodes on a certain depth level.
	 * Each level has its own order
	 */
	public function getMetaDepthLevel()
	{
		return $this->get(TTreeModel::FIELD_META_DEPTHLEVEL);
	}

	/**
	 * Stores the order of nodes on a certain depth level.
	 * Each level has its own order
	 */
	public function setMetaDepthLevel($iLevel)
	{
		$this->set(TTreeModel::FIELD_META_DEPTHLEVEL, $iLevel);
	}
	

	/**
	 * set tree state
	 * either flat or DOM
	 * 
	 * state has nothing to do with the datase, only the internal representation of the tree
	 */
	public function setTreeState($iState = TTreeModel::STATE_FLAT)
	{
		$this->iTreeState = $iState;
	}

	/**
	 * return tree state
	 * either flat or DOM
	 * 
	 * state has nothing to do with the datase, only the internal representation of the tree
	 */
	public function getTreeState()
	{
		return $this->iTreeState;
	}



	/**
	 * This function is called in the constructor and the clear() function
	 * this is used to define default values for fields
     * 
	 * initialize values
	 */
	public function initRecord()
	{
		$this->setParentID(0); //redundant, but just to be sure it is always set to root level
	}
	
	
	
	/**
	 * defines the fields in the tables
	 * i.e. types, default values, enum values, referenced tables etc
	*/
	public function defineTable()
	{            
		//parent id
		$this->setFieldDefaultsInteger(TTreeModel::FIELD_PARENTID); //i opted for a regular integer, because referencing our own table gives problems, because parent needs to be an existing ID, so it's impossible to insert the first record when the table is empty
		// $this->setFieldDefaultsIntegerForeignKey(TTreeModel::FIELD_PARENTID, $this::class, $this::getTable(), TSysModel::FIELD_ID); 
        // $this->setFieldForeignKeyActionOnDelete(TTreeModel::FIELD_PARENTID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE); //delete node when parent gets deleted
		// $this->setFieldForeignKeyActionOnUpdate(TTreeModel::FIELD_PARENTID, TSysModel::FOREIGNKEY_REFERENCE_CASCADE);

		//depth level
		$this->setFieldDefaultsInteger(TTreeModel::FIELD_META_DEPTHLEVEL);
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
			TTreeModel::FIELD_PARENTID, 
			TTreeModel::FIELD_META_DEPTHLEVEL,
			TTreeModel::FIELD_POSITION,
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
		return true;
	}
        
	/**
	 * use locking file for editing
	*/
	public function getTableUseLock()
	{
		return true;
	}        
		
	/**
	* use image in your record?
    * if you don't want a small and large version, use this one
	*/
	public function getTableUseImage()
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
	
	
	// /**
	//  * de child moet deze overerven
	//  *
	//  * @return string naam van de databasetabel
	// */
	// public static function getTable()
	// {
	// 	return APP_DB_TABLEPREFIX.'SysSettings';
	// }
	
	

	

	
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
	 * load from database: the child nodes from a parent
	 * 
	 * it is a shortcut for:
	 * model->find(FIELD_PARENTID. )
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
	public function loadFromDBByParentID($iParentID, $mAutoJoinDefinedTables = 0)
	{
		$this->find(TTreeModel::FIELD_PARENTID, $iParentID, COMPARISON_OPERATOR_EQUAL_TO, $this::getTable(), TP_INTEGER);
		return $this->loadFromDB($mAutoJoinDefinedTables);
	}	
	


	/**
	 * returns a dump of the loaded tree as string
	 * 
	 * @return string
	 */
	public function dumpTree($bRenderToScreen = true)
	{
		$sReturn = '';

		while($this->next())
		{
			//add identation
			for ($iLevel = 0; $iLevel < $this->getMetaDepthLevel(); $iLevel++)
				$sReturn.= '    ';//4 spaces

			$sReturn.= $this->getDisplayRecordShort()." (id:{$this->getID()} parentid:{$this->getParentID()} depth:{$this->getMetaDepthLevel()})\n";
		}

		//return value
		if ($sReturn == '')
			$sReturn = '[empty tree, or not called loadFromDB()]';

		if ($bRenderToScreen)
		{
			$sReturn = str_replace(' ', '&nbsp;', $sReturn);
			echo nl2br($sReturn);
		}

		return $sReturn;
	}


	 /**
     * change order of record in table to a new position
	 * WARNING: OVERRIDES PARENT to set depthlevel and parentid
     *
     * uses TSysModel::FIELD_POSITION, TTreeModel::FIELD_META_DEPTH,  TTreeModel::FIELD_META_PARENT
     *
     * WARNING: manipulates database directly, does no loading and not filling internal fields
     *
     * The reason we use $iAfterID instead of $iBeforeID is because for inserting at the beginning we can use -1 as a fixed number, 
	 * when we would use $iBeforeID we run into trouble with the last item, because that can be literally any number
	 * 
     * @param int $iCurrentID id of the current record
     * @param int $iAfterID id of record $iID will be inserted after. 0 means: at the beginning
     * @return bool change successful?
     */
    public function positionChangeDB($iCurrentID, $iAfterID)
    {
		$objCurr = clone $this;
		$objAfter = clone $this;
		$bMoveFirstChild = false;//is this a move on the first child?
		$iNewParentID = 0;
		$iNewDepthLevel = 0;

		//check: detect move on parent on own child
		$objAfter->clear(true);
		$objAfter->loadFromDBByID($iAfterID);
		if ($this->isMoveFromParentToOwnChildDB($iCurrentID, $objAfter->getParentID()))			
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'positionChangeDB() id can not move parent to its own child');
			return false;
		}		

		//check: detect move to first child
		//moving a node before first child, will result in the item moved before the first child, instead of after the last child
		//the "if"-function below will place it after the last child
		if ($this->hasChildNodesDB($iAfterID))			
		{
			$objChildNode = clone $this;
			$iHighestPos = $this->getPositionLastChild($iAfterID, $iCurrentID); //retrieve position last node
			$objChildNode->clear(true);
			if (!$objChildNode->loadFromDBByPosition($iHighestPos))
			{
				logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'loadFromDBByPosition() failed');
				return false;
			}

			//replace values
			$bMoveFirstChild = true;
			$iNewParentID = $objAfter->getParentID();
			$iNewDepthLevel = $objAfter->getMetaDepthLevel(); //we did a $objAfter->loadFromDB() already above here
			$iAfterID = $objChildNode->getID(); //new after id (=last child)
		}		



		//call parent (pure position move)
		if (!parent::positionChangeDB($iCurrentID, $iAfterID))
			return false;

		//====update depth + parent (because that is not done in the parent)

			//(re)load (values can be changed in parent::positionChangeDB())
			$objCurr->clear(true);
			$objCurr->loadFromDBByID($iCurrentID);
			$objAfter->clear(true);
			$objAfter->loadFromDBByID($iAfterID);

			//copy parent
			$objCurr->setParentID($objAfter->getParentID());
			if ($bMoveFirstChild)
				$objCurr->setParentID($iNewParentID);

			//copy depth level
			$objCurr->setMetaDepthLevel($objAfter->getMetaDepthLevel());
			if ($bMoveFirstChild)
				$objCurr->setMetaDepthLevel($iNewDepthLevel);
			
			//save
			if (!$objCurr->saveToDB())
				return false;

		//==== update children (after saveToDB())

		if (!$this->updatePositionAndDepthByParentDB($iCurrentID))
			return false;


		return true;
	}




	/**
	 * moves a node to another parent (used with dragging and dropping)
	 * but also:
	 * -updates depth
	 * -updates order
	 * 
     * WARNING: manipulates database directly, does no loading and not filling internal fields
	 * 
	 * @param int $iCurrentID id of the node you want to move
	 * @param int $iParentID id of new parent node. 0=root
	 * @return bool
	 **/
	public function moveToParentNodeDB($iCurrentID, $iParentID)
	{
		$objCurr = clone $this;
		$objParent = clone $this;

		//checks
		if ((!is_numeric($iCurrentID)) || (!is_numeric($iParentID)))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'moveToParentNodeDB() id or parent id is not numeric');
			return false;
		}

		//check: detect move on parent on own child
		if ($this->isMoveFromParentToOwnChildDB($iCurrentID, $iParentID))			
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'moveToParentNodeDB() id can not move parent to its own child');
			return false;
		}

		//load
		$objCurr->clear(true);
		$objCurr->loadFromDBByID($iCurrentID);
		if ($iParentID > 0)
		{
			$objParent->clear(true);
			$objParent->loadFromDBByID($iParentID);
		}

		//set parent
		$objCurr->setParentID($iParentID);

		//set depth
		if ($iParentID > 0)
			$objCurr->setMetaDepthLevel($objParent->getMetaDepthLevel() + 1);
		else
			$objCurr->setMetaDepthLevel(0);


		//save BEFORE setting position (because it manipulates database directly)
		if (!$objCurr->saveToDB())
			return false;

		//set position
		if (!parent::positionChangeDB($iCurrentID, $iParentID))//WARNING: I use the PARENT positionChangeDB() instead of the local positionChangeDB() in this class, because I DON'T want to update the parentid and depthlevel
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'moveToParentNodeDB(): positionChangeDB() failed');
			return false;
		}

		//==== update children (after saveToDB())

		if (!$this->updatePositionAndDepthByParentDB($iCurrentID))
			return false;

		return true;
	}

	/**
	 * recursive function that updates child nodes (position and depth) based on parent node information
	 * 
	 * @param int $iParentID id of parent node
	 * @return bool
	 */
	private function updatePositionAndDepthByParentDB($iParentID)
	{
		$objParent = clone $this;
		$objChildren = clone $this;

		//checks
		if ($iParentID == 0) //should not happen
			return false;

		//load
		$objParent->clear(true);			
		if (!$objParent->loadFromDBByID($iParentID))
			return false;

		$objChildren->clear(true);
		if (!$objChildren->loadFromDBByParentID($iParentID))
			return false;

		//no children: exit (nothing to update)
		if ($objChildren->count() == 0) 
			return true;

		//load direct children
		$iAfterID = $iParentID; //default is the parent id
		while ($objChildren->next())
		{
			//update depth level
			$objChildren->setMetaDepthLevel($objParent->getMetaDepthLevel() + 1);

			//save
			if (!$objChildren->saveToDB())
				return false;

			//update position (after saveToDB() because it does a database action)
			if (!parent::positionChangeDB($objChildren->getID(), $iAfterID))
				return false;

			//recursive update children
			$this->updatePositionAndDepthByParentDB($objChildren->getID());

			//update AfterID
			$iAfterID = $objChildren->getID();
		}

		return true;
	}	

	/**
	 * This function detects if a parent node is moved on one of its own child nodes.
	 * when you drop a parent node on it's own child, it will result in a endless recursive loop.
	 * this function is the check to prevent the endless recursive loop
	 * 
	 * this function walks up the tree starting at the child and ending at the root
	 * this is an expensive operation, but regretfully no alternative
	 * 
	 * @param int $iDetectMasterParentID hunting for the id we hope not to find (doesnt change in recursion)
	 * @param int $iCurrentChildID the id of the node we are currently on (changes in recursion)
	 * @return bool true=detected (=bad), false=not detected (=good)
	 */
	private function isMoveFromParentToOwnChildDB($iDetectMasterParentID, $iCurrentChildID)
	{
		$objChild = clone $this;	

		//checks
		if ($iDetectMasterParentID == 0) //should not happen
			return false;

		if ($iCurrentChildID == 0) //stop recursion when landed at the root
			return false;

		//load
		$objChild->clear(true);	
		if (!$objChild->loadFromDBByID($iCurrentChildID))
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'isMoveFromParentToOwnChildDB() load failed');
			return false;
		}

		//detect id
		if ($objChild->getParentID() == $iDetectMasterParentID) //look for $iDetectMasterParentID
		{
			return true; //detected!
		}
		else //recursive call parent
		{
			//Recursion is stopped above when detecting iCurrentChildID == 0
			return $this->isMoveFromParentToOwnChildDB($iDetectMasterParentID, $objChild->getParentID());
		}

		return false;
	}

	/**
	 * this function checks if the iNodeID has any children by looking into the database
	 * 
	 * @param int $iNodeID
	 * @return bool true=detected (=bad), false=not detected (=good) ==> when database fails, it also returns false
	 */
	private function hasChildNodesDB($iNodeID)
	{
		$objNode = clone $this;	
	

		//load
		$objNode->clear(true);
		if ($objNode->loadFromDBByParentID($iNodeID))
		{
			if ($objNode->count() > 0)
				return true;
		}		

		return false;
	}	

	/**
	 * retrieves the position number of the last child 
	 * function goes down the tree recursively in database
	 * 
	 * this is used to determine a position below the last child
	 * 
	 * @param int iNodeID 
	 * @param int iExcludeNodeID exclude current node to prevent finding the highest number on itself
	 * @param int iHighestNumberYet 
	 * @return int if 0 is returned, something went wrong
	 */
	private function getPositionLastChild($iNodeID, $iExcludeNodeID, $iHighestNumberYet = 0)
	{
		$objChildren = clone $this;
		$iHighestChild = 0;
		
		//load
		$objChildren->clear(true);
		$objChildren->find(TTreeModel::FIELD_PARENTID, $iNodeID, COMPARISON_OPERATOR_EQUAL_TO, '', TP_INTEGER);
		$objChildren->find(TTreeModel::FIELD_PARENTID, $iExcludeNodeID, COMPARISON_OPERATOR_NOT_EQUAL_TO, '', TP_INTEGER);
		if (!$objChildren->loadFromDB())
			return $iHighestNumberYet;

		//no children, then exit (to prevent endless recursive loop)
		if ($objChildren->count() == 0)
			return $iHighestNumberYet;

		//inspect children for highest number
		while ($objChildren->next())
		{
			if ($objChildren->getPosition() > $iHighestNumberYet) //new highest number
				$iHighestNumberYet = $objChildren->getPosition();

			//recursive call children
			$iHighestChild = $this->getPositionLastChild($objChildren->getID(), $iExcludeNodeID, $iHighestNumberYet);
			if ($iHighestChild > $iHighestNumberYet)
				$iHighestNumberYet = $iHighestChild;
		}

		return $iHighestNumberYet;
	}


	/**
	 * this function resets the whole tree hierarchy, but leaves the data of the records itself intact
	 * This function resets the depth and position of ALL records
	 * 
	 * useful when a bug occurred in dragging and dropping
	 */
	public function resetTreeHierarchy($bImSureToReset)
	{
		//declarations
		$objClone = clone $this;

		//check
		if (!$bImSureToReset)
			return false;

		//load
		$objClone->loadFromDB();

		//loop and reset each record
		while ($objClone->next())
		{
			$objClone->setPosition($objClone->getID());
			$objClone->setParentID(0);
			$objClone->setMetaDepthLevel(0);
			if (!$objClone->saveToDB())
				return false;
		}

		return true;
	}

	/**
	 * converts a flat tree structure to a Document Object Model where the can be children within children
	 * (instead of only )
	 * It changes the internal state from STATE_FLAT to STATE_DOM
	 * 
	 * WARNING:
	 * For this to be effective there need to be records loaded from the database, because we need the record id's
	 * 
	 * @return boolean true=success, false=error
	 */
	public function convertStateToDOM()
	{
		//declare and init
		$arrRemoveIDs = array();
		$objClone = clone $this;

		//checks
		if ($this->iTreeState == TTreeModel::STATE_DOM) //nothing to do, is already converted
			return true;

		//loop records to find its children
		$this->resetRecordPointer();
		while($this->next())
		{
			if ($this->objChildNodes == null) 
			{
				$this->objChildNodes = clone $this;//we can only do this here to prevent a endless recursive loop
				$this->objChildNodes->clear();
			}

			//find out if current node has children (meaning: other records have the its id as parent-id)
			$objClone->resetRecordPointer();
			while($objClone->next())
			{
				if ($this->getID() == $objClone->getParentID()) //current record has children
				{
					//add this record to children 
					$this->objChildNodes->addCopy($objClone, true);

					$arrRemoveIDs[] = $this->getID();
				}
			}
		}

		//remove records from current TTreeModel that where added as children
		foreach ($arrRemoveIDs as $iID)
		{
			$this->setRecordPointerToValue(TSysModel::FIELD_ID, $iID);
			$this->removeRecord();
		}

		//change state
		$this->iTreeState = TTreeModel::STATE_DOM;
	}

	/**
	 * return childnodes object
	 */
	public function getChildNodes()
	{
		return $this->objChildNodes;
	}

	/**
	 * render a tree of <div>-s.
	 * This is a FLAT list of divs, in other words: there are no divs inside other divs
	 * 
	 * Each div will be assigned a "value"-attribute with the id of the item
	 * 
	 * WARNING: 
	 * Tree needs to be ->orderby(TSysModel::FIELD_POSITION) for this function to work!!!!!!!
	 */
	public function generateHTMLDivTreeFlat($objParentNode = null)
	{
		$sIdent = '';

		$this->resetRecordPointer();

		if ($objParentNode == null)
			$objParentNode = new Div();

		$objItem = new Div();
		$objItem->setTextContent(transg('ttreemodel_renderdivtree_parentnode_name', '[root node]'));
		$objItem->setAttributeAsInt('value', 0);
		$objParentNode->appendChild($objItem);

		while ($this->next())
		{
			$objItem = new Div();
			
			$objItem->setInnerHTML(''.generateChars('&nbsp;', 4 * $this->getMetaDepthLevel() + 4). ' '.$this->getDisplayRecordShort());//we do +3 because everything falls under the root (which has no identation)
			$objItem->setAttributeAsInt('value', $this->getID());
			$objParentNode->appendChild($objItem);
		}

		return $objParentNode;
	}

	/**
	 * renders menu in hierarcal <ul><li><ul><li></li></ul></li></ul>-form
	 * based in the records in memory (does no database action)
	 * 
	 * PREQUISITES:
	 * 1. the records need to be ordered by position in order for this to work:
	 * 		$objMenu->orderBy(TTreeModel::FIELD_POSITION, SORT_ORDER_ASCENDING);
	 * 2. all records need to have IDs and parentIDs. 
	 * 		this can be achieved by saving first or loading records (loadFromDB())
	 * 3. only works in TTreeModel::STATE_FLAT state
	 * 
	 * 
	 * @param int $iParentID leave empty when calling. The recursive call from generateHTMLUlTreeChild() needs this parameter
	 * @return Ul returns null when no children with parentid $iParentID
	 */
	public function generateHTMLUlTree($iParentID = 0)
	{
		//quit function when no children
		if ($this->existsValue(TTreeModel::FIELD_PARENTID, $iParentID) === null)
			return null; //return null when no children found

		//declare + init
		$objClone = clone $this;		
		$objUL = new Ul();
		$bFavoExist = false;

		//==== FAVORITES ON TOP
		if (($iParentID == 0) && ($this->getTableUseIsFavorite()))//only in root
		{
			//create category with a new <li>, and a new <ul> within that
			$objLIFavorites = new Li();
			$objULFavorites = new Ul();
			$objLIFavorites->setTextContent(transg('TTreeModel_favorites_title', 'Favorites'));
			
			//retrieve favorites (fill the new <ul> with <li>-items)
			$objClone->resetRecordPointer();
			while($objClone->next())
			{
				if ($objClone->getIsFavorite())
				{
					$objLI = $objClone->generateHTMLUlTreeChild($objClone);
					if ($objLI !== null) //can return null when not the proper authorisation for example
					{
						$objULFavorites->appendChild($objLI);
						$bFavoExist = true;
					}
				}
			}		

			//add when at least 1 item
			if ($bFavoExist)
			{
				$objUL->appendChild($objLIFavorites);
				$objLIFavorites->appendChild($objULFavorites);			
			}
		}

		//==== REGULAR ITEMS
		$objClone->resetRecordPointer();
		while($objClone->next())
		{
			if ($objClone->getParentID() == $iParentID)
			{
				$objLI = $objClone->generateHTMLUlTreeChild($objClone);
				if ($objLI !== null) //can return null when not the proper authorisation for example
					$objUL->appendChild($objLI);
			}
		}
		return $objUL;
	}

	/**
	 * function used by generateHTMLUlTreeParent() for recursive calls
	 * 
	 * override this function when you need a specific representation implementation for your class
	 * 
	 * @return Li
	 */
	protected function generateHTMLUlTreeChild(&$objClone)
	{
		$objLi = new Li();
		$sTable = '';

		$sTable = $this->getDisplayRecordTable();
		if ($sTable == $this::getTable())
			$objLi->setTextContent($objClone->get($this->getDisplayRecordColumn())); //set table implicitly
		else
			$objLi->setTextContent($objClone->get($this->getDisplayRecordColumn(), $this->getDisplayRecordTable()));

		//recursive call parent function for the children of this node
		$objULChildren = $this->generateHTMLUlTree($objClone->getID());
		if ($objULChildren !== null)
			$objLi->appendChild($objULChildren);

		return $objLi;
	}	

	/**
	 * delete from database according to where conditions given in TSysModel
	 * 
	 * you need to specify the SQL WHERE part otherwise you will delete the
	 * ENTIRE contents of the table.
	 * You can specify the SQL WHERE with $objModel->find();
	 * 
	 * NOTE: this function replaces the function in parent, because we need to recursively delete children as well
	 * 
	 * @param boolean $bYesISpecifiedAWhereInMyModel to prevent you from deleting the contents of the whole table by accident
	 * @param boolean $bCheckAffectedRows if true this function checks if any records were deleted
	 * @return boolean
	 */
	public function deleteFromDB($bYesISpecifiedAWhereInMyModel, $bCheckAffectedRows = false)
	{
		//FIRST: handle deletion of children
		if ($bYesISpecifiedAWhereInMyModel)
		{
			$objToBeDeleted = clone $this;
			if (!$objToBeDeleted->loadFromDB())//load from database, which includes the 'WHERE'-part of SQL
				return false;

			while($objToBeDeleted->next())
			{
				$objChild = clone $this;
				$objChild->clear(true);
				$objChild->where(TTreeModel::FIELD_PARENTID, $objToBeDeleted->getID());
				$objChild->orderBy(TTreeModel::FIELD_POSITION);
				$objChild->deleteFromDB(true, $bCheckAffectedRows); //I don't check return value because children can have no children of their own, which results in nothing being deleted, which results in return value false
			}
		}

		//SECOND: handle the actual deletion of current item
		return parent::deleteFromDB($bYesISpecifiedAWhereInMyModel, $bCheckAffectedRows);
	}
	


	/**
	 * return the table & column that displays the record in the GUI
	 * 
	 * return either '' or $this::getTable() for current table
	 * 
	 * getDisplayRecordColumn() and getDisplayRecordTable() belong together!
	 */
	abstract public function getDisplayRecordColumn();
	abstract public function getDisplayRecordTable();

}

?>