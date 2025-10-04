<?php
namespace dr\classes\patterns;

use dr\classes\dom\tag\Ul;
use dr\classes\dom\tag\Li;
use dr\classes\dom\tag\HTMLTag;

/**
 * This class simplifies handeling and representing recursion in a tree-like construction
 * The recursive structure of objects/ strings/ categories can be very complicated,
 * therefore you can use this class.
 *
 *
 * You can simply find nodes in the tree with the getNodeByID function
 *
 * To gather overview, you can simply use the .displayNode function to print the entire tree to the screen
 *
 *
 * Each treenode can contain one or more child-nodes
 * 
 * 5 mei 2015: TTree opnieuw opgebouwd (zonder de TRecordlist en TRecord parents)
*/

class TTree
{
	private $arrChildNodes = null; //array van subnodes van type TTree
	private $sName = '[node]'; //string van de weer te geven tekst
	private $iID = 0;
	private $objObject = null;
	private $objParentNode = null;
	private $sAHREF = ''; //for storing a HTML link
	private $bVisible = true; //is node visible ?
	private $sPreNodeNameText = '';
	private $sPostNodeNameText = '';
	private $bCollapsed = false;
	private $bSelected = false;
		
	
	public function __construct($objParentNode = null) 
	{
		$this->objParentNode = $objParentNode;
	}
	
	public function setID($iID)
	{
		$this->iID = $iID;
	}
	
	public function getID()
	{
		return $this->iID;
	}
	
	/**
	 * setting the (display)name for the node
	 */
	public function setName($sValue)
	{
		$this->sName = $sValue;
	}

	/**
	 * getting the (display)name for the node
	 */
	public function getName()
	{
		return $this->sName;
	}


	/**
	 * set if selected
	 *
	 * @param bool $bSelect
	 */
	public function setSelected($bSelect)
	{
		if (is_bool($bSelect))
			$this->bSelected = $bSelect;
	}

	/**
	 * getting if this node is selected
	 *
	 * @return boolean
	 */
	public function getSelected()
	{
		return $this->bSelected;
	}

	/**
	 * attaching an object for this node
	 */
	public function setObject($objObject)
	{
		$this->objObject = $objObject;
	}

	/**
	 * getting the attached object for this node
	 */
	public function getObject()
	{
		return $this->objObject;
	}

	/**
	 * setting the parent node
	 * DO NOT USE THIS FUNCTION!!!
	 * SETTING THE PARENT NODE IS DONE AUTOMATICALLY BY addChildNode()
	 * @param TTree $objParent
	 */
	protected function setParentNode($objParent)
	{
		$this->objParentNode = $objParent;
	}

	/**
	 * getting the parent node for this node
	 * @return TTree
	 */
	public function getParentNode()
	{
		return $this->objParentNode;
	}

	/**
	 * sets the HTML link to another page of this node
	 * @param string $sLink
	 */
	public function setAHREF($sLink)
	{
		$this->sAHREF = $sLink;
	}

	/**
	 * gets the HTML link to another page of this node
	 * @return string
	 */
	public function getAHREF()
	{
		return $this->sAHREF;
	}

	/**
	 * set if node is visible yes (true) or no (false)
	 * @param boolean $bVisible
	 */
	public function setVisible($bVisible)
	{
		$this->bVisible = $bVisible;
	}

	/**
	 * get if node is visible
	 * @return boolean
	 */
	public function getVisible()
	{
		return $this->bVisible;
	}

	/**
	 * setting the text wich is displayed BEFORE displaying the node name
	 * @param string $sText
	 */
	public function setPreNodeNameText($sText)
	{
		$this->sPreNodeNameText = $sText;
	}

	/**
	 * setting the text wich is displayed BEFORE displaying the node name
	 * @return string
	 */
	public function getPreNodeNameText()
	{
		return $this->sPreNodeNameText;
	}

	/**
	 * setting the text wich is displayed AFTER displaying the node name
	 * @param string $sText
	 */
	public function setPostNodeNameText($sText)
	{
		$this->sPostNodeNameText = $sText;
	}

	/**
	 * getting the text wich is displayed AFTER displaying the node name
	 * @return string
	 */
	public function getPostNodeNameText()
	{
		return $this->sPostNodeNameText;
	}

	/**
	 * set if node is collapsed (otherwise it's expanded)
	 * when collapsed, the child nodes are not visible
	 * @param boolean $bCollapsed
	 */
	public function setCollapsed($bCollapsed)
	{
		$this->bCollapsed = $bCollapsed;
	}

	/**
	 * expand or collapse all parent nodes of this node
	 * Dit kan handig zijn bij categorien op een website
	 * als je in een categorie bent, dat de parent categorien
	 * zichtbaar moeten zijn, omdat je zo weet waar je in de
	 * tree zit.
	 * @param boolean $bCollapsed
	 */
	public function setCollapsedParentNodes($bCollapsed = false)
	{
		if ($this->getParentNode() != null) //als er parent node is
			$this->getParentNode()->setCollapsedParentNodes($bCollapsed);
		$this->setCollapsed($bCollapsed);
	}
	
	/**
	 * setting all childnodes (including this node) collapsed or expanded
	 * when collapsed, the child nodes are not visible
	 * 
	 * @param boolean $bCollapsed        	
	 */
	public function setCollapsedAllChildNodes($bCollapsed) 
	{
		$this->setCollapsed ( $bCollapsed );
		for($iTeller = 0; $iTeller < $this->countChildNodes (); $iTeller ++) 
		{
			if ($this->getChildNodeByIndex ( $iTeller ) != null)
				$this->getChildNodeByIndex ( $iTeller )->setCollapsedAllChildNodes ( $bCollapsed );
			else
				error ( 'child node with index ' . $iTeller . ' doesn\'t exist! ' );
		}
	}
	
	/**
	 * get if node is collapsed (otherwise it's expanded)
	 * when collapsed, the child nodes are not visible
	 * 
	 * @return boolean
	 */
	public function getCollapsed() {
		return $this->bCollapsed;
	}
	
	/**
	 * adding a childnode to this node
	 * 
	 * @param TTree $objTreeNode        	
	 *
	 */
	public function addChildNode(TTree $objTreeNode) 
	{
		if ($objTreeNode === $this) // als je jezelf als subnode toevoegt
		{
			error ( 'TTree.addNode() : cannot add yourself as subnode' );
		} 
		else 
		{
			// standaard waardes zetten voor childnode
			$objTreeNode->setParentNode ( $this );
			
			// toevoegen aan array
			$this->arrChildNodes [] = $objTreeNode;
		}
	}
	
	/**
	 * adds a node to the underlying tree with parentnode-id $iParentNodeID
	 * 
	 * @param TTree $objTreeNode
	 *        	- attach wich node
	 * @param integer $iParentNodeID
	 *        	- id of the parent node
	 */
	public function addChildNodeByParentNodeID(TTree $objTreeNode, $iParentNodeID = -1) 
	{
		$objParentNode = $this->getChildNodeByID ( $iParentNodeID );
		
		if ($objParentNode == null) // attach to this node
			$this->addChildNode ( $objTreeNode );
		else // anders aan de parent node toevoegen
			$objParentNode->addChildNode ( $objTreeNode );
	}
	
	/**
	 * count the child nodes in this node
	 * 
	 * @return integer
	 *
	 */
	public function countChildNodes() 
	{
		return count ( $this->arrChildNodes );
	}
	
	/**
	 * get a node by a given index number
	 * WARNING : searches ONLY the direct childnodes, not down the tree
	 * (the childnodes of the childnodes will not be searched)
	 * 
	 * @param integer $iIndex        	
	 * @return TTree (returns null if not found)
	 */
	public function getChildNodeByIndex($iIndex) 
	{
		$objReturn = null;
		
		if (is_int ( $iIndex )) 
		{
			if ($iIndex < $this->countChildNodes ()) 
			{
				if ($iIndex >= 0) 
				{
					$objReturn = $this->arrChildNodes [$iIndex];
				} 
				else
					error ( 'getNode() : argument is negative (' . $iIndex . ')' );
			} 
			else
				error ( 'getNode() : argument to big (' . $iIndex . '). Maximum is ' . $this->countChildNodes () );
		} 
		else
			error ( 'getNode() : argument not an integer' );
		
		return $objReturn;
	}


	/**
	* finds node by the given id-parameter
	* searches in childnodes and also the childnodes of the childnodes etc...
	* @param integer $iID
	* @return TTree (returns null if not found)
     */
    public function getChildNodeByID($iID)
	{
		$objFoundNode = null;
		
		// door de subnodes heen lopen op zoek naar het ID
		$iCountChilds = $this->countChildNodes();
		for($iTeller = 0; $iTeller < $iCountChilds; $iTeller ++) 
		{
			if ($objFoundNode == null) // niet meer verder zoeken als gevonden
			{
				$objChildNode = $this->getChildNodeByIndex ( $iTeller );
				
				if ($objChildNode->getID () == $iID)
					$objFoundNode = $objChildNode;
				else
					$objFoundNode = $objChildNode->getChildNodeByID ( $iID );
			}
		}
		
		return $objFoundNode;
	}
	
	/**
	 * on wich level is this node positioned ?
	 * DO NOT SUPPLY A PARAMETER WITH THIS FUNCTION, OTHERWISE IT FUCKS UP THE LEVEL COUNTING!
	 *
	 * function calls itself recursively and therefore needs the parameter
	 *
	 * 0 = rootnode
	 * 1 = first subnode under the rootnode
	 * 2 = subnode of the subnode
	 * etc...
	 *
	 * @param integer $iCurrentLevel        	
	 */
	public function getLevel($iCurrentLevel = 0) 
	{
		if ($this->getParentNode () != null) 
		{
			$iCurrentLevel = $this->getParentNode ()->getLevel ( $iCurrentLevel + 1 );
		}
		
		return $iCurrentLevel;
	}
	
	/**
	 * make a HTML unordered list of this tree
	 *
	 * @param string $sCSSClassSelectedLI
	 *        	the css class of the <LI> node wich is selected
	 * @return ul
	 */
	public function generateHTMLList($sCSSClassSelectedLI = 'selected') 
	{
		$objUL = new ul ();
		
		for($iIndexCounter = 0; $iIndexCounter < $this->countChildNodes (); $iIndexCounter ++) 
		{
			$objNode = $this->getChildNodeByIndex ( $iIndexCounter );
			
			// if ($objNode->getVisible() && (!$objNode->getCollapsed()))
			{
				// creating new list item
				$objLI = new li ();
				if ($objNode->getSelected ())
					$objLI->setClass ( $sCSSClassSelectedLI );
					
					// creating link (or not)
				if ($objNode->getAHREF () != '') // is a link?
				{
					$objA = new a ();
					$objA->setHref ( $objNode->getAHREF () );
					$objA->setText ( $objNode->getName () );
					$objLI->addNode ( $objA );
				} 
				else
					$objLI->setText ( $objNode->getName () );
					
					// call childs recursively
				if ($objNode->countChildNodes () > 0)
					$objLI->addNode ( $objNode->generateHTMLList ( $sCSSClassSelectedLI ) );
				
				$objUL->addNode ( $objLI );
			}
		}
		
		return $objUL;
	}


	/**
	* generates a selectbox woth the tree inside
	*
	* @return select
	*/
	public function generateHTMLSelectBox()
	{
		$objSelect = new select();
	
		$this->getHTMLSelectBoxItemsRecursive($objSelect);
	
		return $objSelect;
	}
	
	/**
	 *
	 * this function is used by generateHTMLSelectBox()
	 * DO NOT USE THIS FUNCTION OUTSITE THIS CLASS
	 *
	 * this functions walks through all the subnodes and adds it to the parent $objSelect
	 *
	 * @param select $objSelect        	
	 * @param $iLevelDepth int
	 *        	for determining the number of spaces the leveldepth is supplied, this is faster than getLevel(), because is cascades the whole tree for every node to determine the depth
	 */
	protected function getHTMLSelectBoxItemsRecursive(select $objSelect, $iLevelDepth = 0) 
	{
		for($iCounter = 0; $iCounter < $this->countChildNodes (); $iCounter ++) 
		{
			$objNode = $this->getChildNodeByIndex ( $iCounter );
			
			// generating node
			if ($objNode->getVisible () && (! $objNode->getCollapsed ())) {
				// cal spaces
				$iIdentSpaces = $iLevelDepth * 4;
				
				// generating html-option node
				$objOption = new option ();
				$objOption->setValue ( $objNode->getID () );
				$objOption->setSelected ( $objNode->getSelected () );
				for($iSpacesCounter = 0; $iSpacesCounter < $iIdentSpaces; $iSpacesCounter ++) // generating spaces
				{
					$objEnt = new ENTITYREFERENCE ();
					$objEnt->setEntity ( 'nbsp' );
					$objOption->addNode ( $objEnt );
				}
				$objOption->addText ( $this->getPreNodeNameText () . $objNode->getName () . $this->getPostNodeNameText () );
				$objSelect->addNode ( $objOption );
				
				// call childs recursively
				if ($objNode->countChildNodes () > 0)
					$objNode->getHTMLSelectBoxItemsRecursive ( $objSelect, $iLevelDepth + 1 );
			}
		}
	}
	
	/**
	 * displaying a path of breadcrumbs as a unnumbered list in HTML
	 * call this method on the lowest child of the tree
	 * This method will walk through it's parents to get their names
	 * You must provide this function with a breadcrumb separator
	 *
	 * function accepts textnodes of TTree and objects wich are an instance of aBSTRACT
	 *
	 * @param string $sCSSClassSelectedLI
	 *        	- CSS class of the selected
	 * @param string $sBreadCrumbSeparator
	 *        	- name of the UL component
	 * @return ul
	 */
	public function generateBreadcrumbHTMLList($sBreadCrumbSeparator = '>', $sCSSClassSelectedLI = 'selected') 
	{
		$objUL = new ul ();
		
		$this->getHTMLBreadCrumbRecursive ( $objUL, $sBreadCrumbSeparator, $sCSSClassSelectedLI );
		
		return $objUL;
	}
	
	/**
	 * this function is used by displayBreadCrumbsPath()
	 * DO NOT USE THIS FUNCTION OUTSITE THIS CLASS
	 *
	 * function accepts textnodes of TTree and objects wich are an instance of aBSTRACT
	 *
	 *
	 * @param string $sHTMLBreadCrumbSeparator
	 *        	- separator to separate the nodes
	 * @param string $sPrePath
	 *        	-
	 */
	protected function getHTMLBreadCrumbRecursive(ul $objParentUL, $sBreadCrumbSeparator = '>', $sCSSClassSelectedLI = 'selected') 
	{
		// new node
		$objLI = new li ();
		if (strlen ( $this->getAHREF () ) > 0) // if a link is added
		{
			$objA = new a ();
			$objA->setHref ( $this->getAHREF () );
			$objA->setText ( $this->getPreNodeNameText () . $this->getName () . $this->getPostNodeNameText () );
			$objLI->addNode ( $objA );
		} 
		else // no link
		{
			if (strlen ( $this->getName () ) > 0)
				$objLI->addText ( $this->getName () );
		}
		
		if ($this->getObject () != null) // object gedetecteerd ?
		{
			if ($this->getObject () instanceof HTMLTag) // herkennen html object
			{
				$objLI->addNode ( $this->getObject () );
			}
		}
		
		if ($this->getSelected ())
			$objLI->setClass ( $sCSSClassSelectedLI );
		
		$objParentUL->insertNode ( $objLI, 0 ); // inserten omdat deze bij een add() achteraan komt, en dat willen we niet
		                                        // separator
		$objCrumbSep = new li ();
		$objCrumbSep->setText ( $sBreadCrumbSeparator );
		//if ($this->countChildNodes () == 0)
		//	$objCrumbSep->setVisible ( false );
		$objParentUL->insertNode ( $objCrumbSep, 1 ); // inserten omdat deze bij een add() achteraan komt, en dat willen we niet
		
		if ($this->getParentNode () != null) // alleen als er een parent node is
			$this->getParentNode ()->getHTMLBreadCrumbRecursive ( $objParentUL, $sBreadCrumbSeparator, $sCSSClassSelectedLI );
	}
	
	/**
	 * displaying a path of breadcrumbs in clean text
	 * You must provide this function with a breadcrumb separator
	 *
	 *
	 * @param string $sBreadCrumbSeparator        	
	 */
	public function displayBreadCrumbsPath($sBreadCrumbSeparator) {
		echo $this->getName ();
		
		if ($this->getParentNode () != null)
			echo $sBreadCrumbSeparator;
		if ($this->getParentNode () != null) // alleen als er een parent node is
			$iCurrentLevel = $this->getParentNode ()->displayBreadCrumbsPath ( $sBreadCrumbSeparator );
	}
	
	/**
	 * dump the contents of this node to the screen
	 * This is a alternative for the var_dump function of php
	 * because the recursiviness of this class it is impossible
	 * to use var_dump
	 */
	public function dump($iIdentSpaces = 0) {
		// als ongeldige parameter
		if (! is_int ( $iIdentSpaces ))
			$iIdentSpaces = 0;
			
			// aantal spaties genereren
		$sSpaties = '';
		for($iSpaceTeller = 0; $iSpaceTeller < $iIdentSpaces; $iSpaceTeller ++)
			$sSpaties .= ' ';
		
		if ($this->arrChildNodes != null) {
			
			// door de subnodes heen lopen
			for($iTeller = 0; $iTeller < $this->countChildNodes (); $iTeller ++) {
				$objChildNode = $this->getChildNodeByIndex ( $iTeller );
				
				if ($objChildNode->getVisible ())
					$sVisible = 'true';
				else
					$sVisible = 'false';
				
				if ($objChildNode->getCollapsed ())
					$sCollapsed = 'true';
				else
					$sCollapsed = 'false';
					
					// weergeven
				echo $sSpaties . $objChildNode->getName () . ' (id:' . $objChildNode->getID () . '; visible:' . $sVisible . '; collapsed:' . $sCollapsed . '; level-depth:' . $this->getLevel () . '; childnode-count:' . $objChildNode->countChildNodes () . ")\n";
				
				// subnodes weergeven
				$objChildNode->dump ( $iIdentSpaces + 4 );
			}
		} else
			echo $sSpaties . '[no child nodes present]' . "\n";
	}
	
	/**
	 * generate a XML sitemap with the nodes in the tree
	 * according to the protocol v0.9 of http://www.sitemaps.org
	 *
	 * Don't forget to modity the header of you php file with the command : header ("content-type: text/xml; charset=UTF-8");
	 */
	public function getXMLSitemap() 
	{
		$sResult = '';
		
		$sResult .= '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
		// $sResult .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
		$sResult .= '<urlset
	 xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
	 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	 xsi:schemaLocation="
	 http://www.sitemaps.org/schemas/sitemap/0.9
	 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
		$sResult .= $this->getXMLSitemapNodeRecursive ();
		$sResult .= '</urlset>' . "\n";
		
		return $sResult;
	}
	
	/**
	 * this function is used by getXMLSitemap() to generate a
	 * XML sitemap
	 * DO NOT USE THIS FUNCTION OUTSITE THIS CLASS
	 *
	 * @param string $sLastModifiedText
	 *        	: date in YYYY-MM-DD format , else the date of today will be used
	 * @param string $sChangefrequency
	 *        	: possible values : always * hourly * daily * weekly * monthly * yearly * never
	 */
	protected function getXMLSitemapNodeRecursive($sChangefrequency = '', $sLastModifiedText = '') 
	{
		$sResult = '';
		
		if ($this->arrChildNodes != null) {
			
			// door de subnodes heen lopen
			for($iTeller = 0; $iTeller < $this->countChildNodes (); $iTeller ++) {
				$objChildNode = $this->getChildNodeByIndex ( $iTeller );
				
				// weergeven
				$sResult .= "    <url>\n";
				
				$sResult .= "        <loc>" . $this->getXMLSitemapEscaped ( $objChildNode->getAHREF () ) . "</loc>\n";
				
				if ($sLastModifiedText != '')
					$sResult .= "        <lastmod>" . $sLastModifiedText . "</lastmod>\n"; /* optional */
				else
					$sResult .= "        <lastmod>" . date ( 'Y-m-d' ) . "</lastmod>\n"; /* optional */
				
				if ($sChangefrequency != '')
					$sResult .= "        <changefreq>" . $sChangefrequency . "</changefreq>\n"; /* optional */
					
				// level converteren naar priority
				$fPriority = ($objChildNode->getLevel () - 1) / 10;
				$fPriority = 1 - $fPriority;
				if ($fPriority < 0.1) // voorkomen dat je beneden de 0.1 komt
					$fPriority = 0.1;
				if ($fPriority == 1) // als 1 dan moet het worden 1.0
					$fPriority = '1.0';
				
				$sResult .= "        <priority>" . $fPriority . "</priority>\n"; /* optional */
				
				$sResult .= "    </url>\n";
				
				// childnodes bevragen
				$sResult .= $objChildNode->getXMLSitemapNodeRecursive ( $sChangefrequency, $sLastModifiedText );
			}
		}
		
		return $sResult;
	}
	
	/**
	 * Escaping the problem characters in a XML sitemap
	 * DO NOT USE THIS FUNCTION OUTSITE THIS CLASS
	 *
	 * Character Escape Code
	 * Ampersand & &amp;
	 * Single Quote ' &apos;
	 * Double Quote " &quot;
	 * Greater Than > &gt;
	 * Less Than < &lt;
	 *
	 * @param string $sInput
	 *        	: input string with unescaped characters
	 * @return string input string with escaped characters
	 */
	private function getXMLSitemapEscaped($sInput) 
	{
		$sResult = $sInput;
		$sResult = str_replace ( '&', '&amp;', $sResult );
		$sResult = str_replace ( "'", '&apos;', $sResult );
		$sResult = str_replace ( '"', '&quot;', $sResult );
		$sResult = str_replace ( '>', '&gt;', $sResult );
		$sResult = str_replace ( '<', '&lt;', $sResult );
		return $sResult;
	}
}
