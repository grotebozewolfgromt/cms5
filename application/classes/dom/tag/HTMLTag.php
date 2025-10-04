<?php
namespace dr\classes\dom\tag;


use dr\classes\dom\tag\Text;
use dr\classes\dom\tag\form\InputDate;
use dr\classes\locale\TLocalisation;

/**
 * HTMLTag
 * This class represents a HTML tag, like <input type="hidden">
 * parent class for all HTMLDOM classes
 * -> It can generate html code
 * -> and interpret/parse html
 * 
 * 
 * -> You can inherit this class to assign custom behavior for that tag
 * -> This class has in internal array for all the attributes and their values
 * -> It also has an internal array for child-nodes in this node
 * -> the idea is to stay as close to the javascript DOM equivalent as possible
 * 
 * ========== name VS tag name ===========
 * Don't confuse tagName with the attribute: name
 * tag name: 	div : 		<div></div>  						setTagName('div');
 * attribute: 	name: 		<input type="text" name="value">	setName('value');
 * 
 * ============== ARRAY NAMES =================
 * this class supports html-names that are arrays: <input type="checkbox" name="namearray[]">
 * You need to enable this feature explicitly with setIsArray(true)!
 * 
 * =============== IMPORTANT ==================
 * tags with an EMPTY tagname are not rendered! 
 * But child-nodes of the tag ARE!!!
 * 
 * 
 * 
 * 20 apr 2016: TagAbstract: $objSubnodes (TObjectList) replaced by a faster array structure (no extra expensive object allocation anymore)
 * 6 jan 2020: TagAbstract: isArray added
 * 9 jan 2020: TagAbstract: resetNodespointer() added
 * 28 may 2020: TagAbstract: contentEditable added
 * 6 nov 2020: TagAbstract: getNodeByID added
 * 27 nov 2020: TagAbstract: removed prefixes
 * 8 apr 2024: TagAbstract: added support for data attributes <div data-*="value">
 * 8 apr 2024: TagAbstract: added support for onmousedown event]
 * 15 apr 2024: rename TagAbstract ==> HTMLTag + not abstract anymore
 * 15 apr 2024: HTMLTag: conversion to internal array with attributes instead of individual variables
 * 15 apr 2024: HTMLTag: addAttributeToHTML() uses new sanitize functions
 * 15 apr 2024: HTMLTag: specific methods for setting and getting values as booleans ===> THE DEFAULT BEHAVIOR IS STRINGS INSTEAD OF BOOLS
 * 15 apr 2024: HTMLTag: supports array names
 * 16/17 apr 2024: HTMLTag: added html parser!
 * 17 apr 2024: HTMLTag: fix: text node teveel geparst
 * 17 apr 2024: HTMLTag: tag contents is being processed now
 * 17 apr 2024: HTMLTag: removed separate dataset array for data-* attributes
 * 17 apr 2024: HTMLTag: hasclosingtags methods en internal variable removed
 * 17 apr 2024: HTMLTag: cleanup: unnessary comments removed
 * 17 apr 2024: HTMLTag: fix: renderHTMLNode(): rendered a useless </> after the last tag
 * 17 apr 2024: HTMLTag: fix: parserDoNewTagsExist(): didnt support <!-- comments and <!DOCTYPE
 * 18 apr 2024: HTMLTag: name change: subnodes -> child nodes
 * 18 apr 2024: HTMLTag: constructor heeft nu $objParentNode
 * 18 apr 2024: HTMLTag: verschillende renames 1. om het duidelijker te maken dat 't childnodes zijn 2. betere overeenkomsten met javascript DOM-manipulation
 * 18 apr 2024: HTMLTag: rename renderHTMLNodeSpecific() --> renderChild()
 * 18 apr 2024: HTMLTag: renderChild() is now a protected function
 * 22 apr 2024: HTMLTag: fix: addAttributeToHTML() strlen() doesn't expect null (php8)
 * 6 jun 2024: HTMLTag: fix: tagname werd niet gesanitized met sanitizeHTMLTagIdName()
 * 30 aug 2024: HTMLTag: onkeyup added
 * 30 jan 2025: HTMLTag: several functions added
 * 30 jan 2025: HTMLTag: FIX: eerste karakter input was foetsie in render
 * 30 jan 2025: HTMLTag: FIX: wanneer alleen text (geen nodes), dan is return van de render leeg. dit wordt nu gedetecteerd en als textnode toegevoegd
 * 9 may 2025: HTMLTag: ADD: getTextContent();
 * 13 aug 2025: HTMLTag: sanitizeHTMLTagAttributeValue() replaces " with &quot;
 * 13 aug 2025: HTMLTag: parserProcessTag() rewritten. The old one didn't allow spaces in attribute value
 * 14 aug 2025: HTMLTag: bugfix: setInnerHTML(), setTextContent() didnt reset cache count children
 * 14 aug 2025: HTMLTag: optimalisaties doorgevoerd aan renderHTMLNode();
 * 25 sept 2025: HTMLTag: setAttributeAsBool() extra parameter voor implicit booleans zoals disabled en checked
 * 26 sept 2025: HTMLTag: >getInnerHTML() added
 * 
 */
class HTMLTag
{
	protected $sTagName = '';//name of the tag: i.e. div (for <div>-tag)
	protected $arrChildNodes = array(); //the subnodes of this node
	protected $arrAttributes = array(); //key-value pairs of attributes and attribute-values: i.e. in <div id="1"> ====> arrAttributes['id'] = '1'
	protected $objParentNode = null; //if parent node = null, then it is a root node

	private $bSourceFormattingNewlineAfterOpenTag = false; //enter after open tag?
	private $bSourceFormattingNewLineAfterCloseTag = false; //enter after closing tag?
	private $bSourceFormattingIdentForOpenTag = false; //identation before open tag?
	private $bSourceFormattingIdentForCloseTag = false; //identation before closing tag?
	private $iChildNodePointer = 0; //points to index in $arrChildNodes
	private $iCacheCountChildNodes = 0;
	private $iCacheCountAttributes = 0;
    private $bIsArray = false; //is the html name is an array: <input type="text" name="edtName[]"> .PHP converts the brackets [] after submitting to an array and CHANGES THE HTML NAME to the name without the brackets, so 'edtName[]' becomes 'edtName'. this gives problems when reading the values from the _GET and _POST array

	const ATTRIBUTE_STYLE 			= 'style';
	const ATTRIBUTE_CLASS 			= 'class';
	const ATTRIBUTE_NAME 			= 'name';
	const ATTRIBUTE_ID 				= 'id';
	const ATTRIBUTE_ONCLICK 		= 'onclick';
	const ATTRIBUTE_ONCHANGE 		= 'onchange';
	const ATTRIBUTE_ONKEYDOWN 		= 'onkeydown';
	const ATTRIBUTE_ONKEYUP 		= 'onkeyup';
	const ATTRIBUTE_ONMOUSEDOWN 	= 'onmousedown';
	const ATTRIBUTE_CONTENTEDITABLE = 'contenteditable';
	const ATTRIBUTE_DRAGGABLE 		= 'draggable';

	const ATTRIBUTE_VALUE_BOOL_TRUE 	= 'true';
	const ATTRIBUTE_VALUE_BOOL_FALSE 	= '';

	const VOIDTAGS = array('area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr'); //Singleton Tags with no closing tags

	public function __construct($objParentNode = null)
	{
		$this->objParentNode = $objParentNode;
		$this->arrChildNodes = array(); //init
        $this->resetChildNodePointer();
	}

	public function  __destruct()
	{
		unset($this->arrChildNodes);
	}

	public function resetChildNodePointer()
	{
		$this->iChildNodePointer = 0;
	}

	/**
	 * removes all child notes
	 */
	public function removeAllChildNodes()
	{
		unset($this->arrChildNodes);
		$this->arrChildNodes = array();
		$this->resetChildNodePointer();
	}

	/**
	 * removes child node from parent node in DOM
	 * 
	 * @return bool true = child is found, false = child not found
	 */
	public function removeChild($objChildNode)
	{
		$iDeleteIndex = -1;

		//retrieve childnode index
		for ($iIndex = 0; $iIndex < $this->iCacheCountChildNodes; ++$iIndex)
		{
			if ($this->arrChildNodes[$iIndex] == $objChildNode)
			{
				$iDeleteIndex = $iIndex;
				$iIndex = $this->iCacheCountChildNodes; //escape loop, we found index;
			}
		}

		//if not found
		if ($iDeleteIndex == -1) 
			return false;

		return $this->removeChildAtIndex($iDeleteIndex);
	}

	/**
	 * removes child node from parent node in DOM
	 */
	public function removeChildAtIndex($iIndex)
	{
		//declare
		$arrPart1 = array();
		$arrPart2 = array();

		//look for invalid indexes
		if (($iIndex < 0) || ($iIndex >= $this->iCacheCountChildNodes))
			return false;

		//construct new internal array
		if ($iIndex !== 0) //skip first element, because first part is always empty
			$arrPart1 = array_slice($this->arrChildNodes, 0, $iIndex);
		$arrPart2 = array_slice($this->arrChildNodes, $iIndex+1, $this->iCacheCountChildNodes+1);
		$this->arrChildNodes = array_merge($arrPart1, $arrPart2);

		//update administration
		--$this->iCacheCountChildNodes; //update count
		if ($this->iChildNodePointer >= $this->iCacheCountChildNodes) //last element deleted
			--$this->iCacheCountChildNodes; //update index of last element

		return true;
	}

	/**
	 * removes attribute from tag
	 * 
	 * @return bool true = attribute is found, false = attribute not found
	 */
	public function removeAttribute($sAttributeName)
	{
		$bKeyFound = false;
		$arrKeys = array();
		$arrNewAttr = array();

		//retrieve childnode index
		$arrKeys = array_keys($this->arrAttributes);
		foreach ($arrKeys as $sKey)
		{
			if ($sKey === $sAttributeName)
			{
				$bKeyFound = true;
			}
			else
			{
				$arrNewAttr[$sKey] = $this->arrAttributes[$sKey];
			}
		}

		//if found then remove
		if ($bKeyFound)
			$this->arrAttributes = $arrNewAttr; //replace internal array with new one

		return $bKeyFound;
	}



	protected function getSourceFormattingNewLineAfterOpenTag()
	{
		return $this->bSourceFormattingNewlineAfterOpenTag;
	}

	protected function setSourceFormattingNewLineAfterOpenTag($bNewline)
	{
		$this->bSourceFormattingNewlineAfterOpenTag = $bNewline;
	}

	protected function getSourceFormattingNewLineAfterCloseTag()
	{
		return $this->bSourceFormattingNewLineAfterCloseTag;
	}

	protected function setSourceFormattingNewLineAfterCloseTag($bNewline)
	{
		$this->bSourceFormattingNewLineAfterCloseTag = $bNewline;
	}

	protected function getSourceFormattingIdentForOpenTag()
	{
		return $this->bSourceFormattingIdentForOpenTag;
	}

	protected function setSourceFormattingIdentForOpenTag($bIdent)
	{
		$this->bSourceFormattingIdentForOpenTag = $bIdent;
	}

	protected function getSourceFormattingIdentForCloseTag()
	{
		return $this->bSourceFormattingIdentForCloseTag;
	}

	protected function setSourceFormattingIdentForCloseTag($bIdent)
	{
		$this->bSourceFormattingIdentForCloseTag = $bIdent;
	}


	/**
	 * setting the name of the HTML tag, so what TYPE it is
	 * i.e.: 'div' for <div>-tag, 'table' for <table>
	 * 
	 * is sanitized with sanitizeHTMLTagIdName()
	 * 
	 * DONT CONFUSE TAGNAME WITH THE ATTRIBUTE: 'name'!!!
	 *
	 * @param string $sName
	 */
	public function setTagName($sName)
	{
		$this->sTagName = $sName;
	}

	/**
	 * getting the name of this HTML tag
	 * i.e.: div (for <div>-tag)
	 * 
	 * DONT CONFUSE TAGNAME WITH THE ATTRIBUTE: 'name'!!!
	 * 
	 * @return string
	 */
	public function getTagName()
	{
		return $this->sTagName;
	}

	/**
	 * setting the name for the HTML tag
	 * bijvoorbeeld: frmContact in  <form name="frmContact">
	 * 
	 * this name is sanitized with sanitizeHTMLTagAttributeValue()
	 * 
	 * @param string $sName
	 */
	public function setName($sName)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_NAME] = $sName;
	}
        
        
	/**
	 * getting the name of this HTML tag
	 * example: frmContact in  <form name="frmContact">
	 *
	 * returns '' even if not set
	 * 
	 * @return string
	 */
	public function getName()
	{
		if (isset($this->arrAttributes[HTMLTag::ATTRIBUTE_NAME]))
			return $this->arrAttributes[HTMLTag::ATTRIBUTE_NAME];
		else
			return '';
	}

	/**
	* setting the attribute 'name' for the HTML tag, but indicates it at the same time as an array
	* (only needed in forms)
	* 
	* PHP converts tag names given as names with brackets to an array after 
	* form submission, this results in problems reading the _GET and _POST 
	* array, because it reads 'edtName[]' (which not exists) instead of 'edtName'
	* 
	* example: edtFirstName in  <input type="text" name="edtFirstName[]">
    * 
	* this name is sanitized with sanitizeHTMLTagIdName()
	*
    * @param string $sName name of the html tag WITHOUT BRACKETS, so setName('edtFirstName'), this will render as 'edtFirstName[]'
	*/        
	public function setNameArray($sName)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_NAME] = $sName;                           
        $this->bIsArray = true;
	}        
        
    /**
	* indicate the tag as an array, which adds [] to the attribute 'name' of the tag when rendering
	* This is only needed in forms.
	* 
	* PHP converts tag names given as names with brackets to an array after 
	* form submission, this results in problems reading the _GET and _POST 
	* array, because it reads 'edtName[]' (which not exists) instead of 'edtName'
	* 
	* example: edtName as array in <input type="text" name="edtName[]">
	* 
	* @param bool $bArray
	*/
	public function setIsArray($bArray)
	{
		$this->bIsArray = $bArray;
	}
        
    /**
	* getting the if the element is an array
	* (only needed in forms)
	* 
	* PHP converts tag names given as names with brackets to an array after 
	* form submission, this results in problems reading the _GET and _POST 
	* array, because it reads 'edtName[]' (which not exists) instead of 'edtName'
	* 
	* example: edtFirstName in  <input type="text" name="edtFirstName[]">
	*
	* @return bool
	*/
	public function getIsArray()
	{
		return $this->bIsArray;
	}

	/**
	 * setting the name for the HTML form tag.
	 * example: frmContact in  <form name="frmContact">
     * 
	 * this id/name is sanitized with sanitizeHTMLTagIdName()
	 *
	 * @param string $sName name of the html tag
     * @param boolean $bSetAlsoIDWithName this function sets also setID($sName) if true
	 */
	public function setNameAndID($sName)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_NAME] = $sName;		
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ID] = $sName;		
	}        
        
	/**
	 * setting the javascript onclick property for the HTML tag
	 * bijvoorbeeld:  <input type="button" onclick="doeIets()">
	 *
	 * @param string $sOnclickEvent
	 */
	public function setOnclick($sOnclickEvent)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ONCLICK] = $sOnclickEvent;	
	}

	/**
	 * getting the javascript onclick property of this HTML tag
	 * bijvoorbeeld: <input type="button" onclick="doeIets()">
	 *
	 * @return string
	 */
	public function getOnclick()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_ONCLICK];
	}

	/**
	 * setting the javascript onchange property for the HTML tag
	 * bijvoorbeeld:  <select onchange="doeIets()">
	 *
	 * @param string $sOnchangeEvent
	 */
	public function setOnchange($sOnchangeEvent)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ONCHANGE] = $sOnchangeEvent;	
	}

	/**
	 * getting the javascript onchange property of this HTML tag
	 * bijvoorbeeld: <select onchange="doeIets()">
	 *
	 * @return string
	 */
	public function getOnchange()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_ONCHANGE];
	}

	/**
	 * setting the javascript onkeydown property for the HTML tag
	 * bijvoorbeeld:  <input type="button" onkeydown="doeIets()">
	 *
	 * @param string $sOnkeydownEvent
	 */
	public function setOnkeydown($sOnkeydownEvent)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ONKEYDOWN] = $sOnkeydownEvent;	
	}
	
	/**
	 * getting the javascript onkeydown property of this HTML tag
	 * bijvoorbeeld: <input type="button" onkeydown="doeIets()">
	 *
	 * @return string
	 */
	public function getOnkeydown()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_ONKEYDOWN];
	}

	/**
	 * setting the javascript onkeyup property for the HTML tag
	 * bijvoorbeeld:  <input type="button" onkeyup="doeIets()">
	 *
	 * @param string $sOnkeyupEvent
	 */
	public function setOnkeyup($sOnkeyupEvent)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ONKEYUP] = $sOnkeyupEvent;	
	}
	
	/**
	 * getting the javascript onkeyup property of this HTML tag
	 * bijvoorbeeld: <input type="button" onkeyup="doeIets()">
	 *
	 * @return string
	 */
	public function getOnkeyup()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_ONKEYUP];
	}
        

	/**
	 * setting the javascript onmouse property for the HTML tag
	 * bijvoorbeeld:  <input type="button" onmousedown="doeIets()">
	 *
	 * @param string $sOnkeydownEvent
	 */
	public function setOnmousedown($sOnmousedownEvent)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ONMOUSEDOWN] = $sOnmousedownEvent;
	}
	
	/**
	 * getting the javascript onmousedown property of this HTML tag
	 * bijvoorbeeld: <input type="button" onmousedown="doeIets()">
	 *
	 * @return string
	 */
	public function getOnmousedown()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_ONMOUSEDOWN];
	}
        
	
	/**
	 * <p contenteditable="true">This is an editable paragraph.</p>
	 * @param string $sEditable 
	 */
	public function setContentEditable($sEditable)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_CONTENTEDITABLE] = $sEditable;
	}

	/**
	 * <p contenteditable="true">This is an editable paragraph.</p>
	 * @param string $sEditable 
	 */
	public function setContentEditableAsBool($bEditable)
	{
		$this->setAttributeAsBool(HTMLTag::ATTRIBUTE_CONTENTEDITABLE, $bEditable);
	}	
        
	/**
	 * set is content editable?
	 * <p contenteditable="true">This is an editable paragraph.</p>
	 * @return string
	 */
	public function getContentEditable()
	{
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_CONTENTEDITABLE];
	}
    
	/**
	 * get is content editable?
	 * <p contenteditable="true">This is an editable paragraph.</p>
	 * @return bool
	 */
	public function getContentEditableAsBool()
	{
		return $this->getAttributeAsBool(HTMLTag::ATTRIBUTE_CONTENTEDITABLE);
	}	

	/**
	 * setting the id for the HTML tag
	 * bijvoorbeeld: edtEmail in <input id="edtEmail>
	 *
	 * this sID is unsanitized for speed reasons, sanitize it with sanitizeHTMLTagIdName()
	 * 
	 * @param string $sID
	 */
	public function setID($sID)
	{
		$this->arrAttributes[HTMLTag::ATTRIBUTE_ID] = $sID;
	}

	/**
	 * getting the id of this HTML tag
	 * bijvoorbeeld: edtEmail in <input id="edtEmail>
	 * 
	 * returns '' even if not set
	 *
	 * @return string
	 */
	public function getID()
	{
		if (isset($this->arrAttributes[HTMLTag::ATTRIBUTE_ID]))
			return $this->arrAttributes[HTMLTag::ATTRIBUTE_ID];
		else
			return '';
	}

	/**
	 * setting the style
	 *
	 * @param string $sStyle        	
	 */
	public function setStyle($sStyle) 
    {
		$this->arrAttributes[HTMLTag::ATTRIBUTE_STYLE] = $sStyle;
	}
	
	/**
	 * getting the style of the component
	 *
	 * @return string  
	 */
	public function getStyle() 
    {
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_STYLE];
	}
	
	/**
	 * set the CSS class
	 * 
	 * @param string $sClass        	
	 */
	public function setClass($sClass) 
    {
		$this->arrAttributes[HTMLTag::ATTRIBUTE_CLASS] = $sClass;
	}
	
	/**
	 * get the CSS class
	 * 
	 * @return string
	 */
	public function getClass() 
    {
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_CLASS];
	}
	
	/**
	 * set <div draggable="true">
	 * 
	 * @param string $sDrag        	
	 */
	public function setDraggable($sDrag) 
    {
		$this->arrAttributes[HTMLTag::ATTRIBUTE_DRAGGABLE] = $sDrag;
	}

	/**
	 * set <div draggable="true">
	 * 
	 * @param bool $bDrag        	
	 */
	public function setDraggableAsBool($bDrag) 
    {
		$this->setAttributeAsBool(HTMLTag::ATTRIBUTE_DRAGGABLE, $bDrag);
	}


	
	/**
	 * get <div draggable="true">
	 * 
	 * @return string
	 */
	public function getDraggable() 
    {
		return $this->arrAttributes[HTMLTag::ATTRIBUTE_DRAGGABLE];
	}


	/**
	 * get <div draggable="true">
	 * 
	 * @return bool
	 */
	public function getDraggableAsBool() 
    {
		return $this->getAttributeAsBool(HTMLTag::ATTRIBUTE_DRAGGABLE);
	}	

	/**
	 * set value of html attribute
	 * for <div id="1"> use:
	 * $this->setAttribute('id', '1');
	 * 
	 * @param string $sName name of the attribute
	 * @param string $sValue value of the attribute
	 */
	public function setAttribute($sName, $sValue)
	{
// vardump('', 'bloekbloek name='.$sName.' value='.$sValue)		;
		$this->arrAttributes[$sName] = $sValue;
	}

	/**
	 * set value of html attribute as bool
	 * for <div draggable="true"> use:
	 * $this->setAttribute('draggable', true);
	 * 
	 * @param string $sName name of the attribute
	 * @param bool $bValue value of the attribute
	 * @param bool $bRemoveAttributeWhenFalse some attributes are implicit, like 'disabled' or 'checked'. When they are false the attribute needs to be removed from the html tag
	 */
	public function setAttributeAsBool($sName, $bValue, $bRemoveAttributeWhenFalse = true)
	{	
		if ($bValue)
		{
			$this->arrAttributes[$sName] = HTMLTag::ATTRIBUTE_VALUE_BOOL_TRUE;
		}
		else
		{
			if ($bRemoveAttributeWhenFalse)
				$this->removeAttribute($sName);
			else
				$this->arrAttributes[$sName] = HTMLTag::ATTRIBUTE_VALUE_BOOL_FALSE;		
		}
	}	

	/**
	 * set attribute as int
	 */
	public function setAttributeAsInt($sName, $iValue)
	{
		$this->arrAttributes[$sName] = $iValue;
	}

	/**
	 * get value html attribute
	 * for <div id="1"> use:
	 * $this->getAttribute('id') ====> returns '1'
	 * 
	 * @param string $sName name of the attribute
	 * @return string value of the attribute
	 */	
	public function getAttribute($sName)
	{
		return $this->arrAttributes[$sName];
	}

	/**
	 * get value html attribute
	 * for <input type="checkbox" checked="true"> use:
	 * $this->getAttribute('checked') ====> returns true
	 * 
	 * followed the guidelines of: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html
	 * 
	 * @param string $sName name of the attribute
	 * @return boolean value of the attribute
	 */	
	public function getAttributeAsBool($sName)
	{
		if (($this->arrAttributes[$sName] == HTMLTag::ATTRIBUTE_VALUE_BOOL_TRUE) || ($this->arrAttributes[$sName] == '')) //empty ==> <input type="checkbox" checked=""> evaluates as true
			return true;
		else
			return false;
	}	

	/**
	 * get attribute as integer
	 */
	public function getAttributeAsInt($sName)
	{
		if (is_numeric($this->arrAttributes[$sName]))
			return (int)$this->arrAttributes[$sName];
		else
			return 0;
	}

	/**
	 * checks is attribute exists of tags
	 */
	public function hasAttribute($sName)
	{
		return isset($this->arrAttributes[$sName]);
	}


	/**
	 * add a node to this node
	 *
	 * @param THTMLTr $objRow
	 * @deprecated
	 */
	// public function addNode(HTMLTag &$objSubNode)
	// {
	// 	// $this->arrChildNodes[] = $objSubNode;
	// 	// ++$this->iCacheCountChildNodes;
	// 	$this->appendChild($objSubNode);
	// }

	/**
	 * add child node
	 */
	public function appendChild(HTMLTag $objChildNode)
	{
		$this->arrChildNodes[] = $objChildNode;
		++$this->iCacheCountChildNodes;
	}


	/**
	 * alias for appendChildTextNode
	 *
	 * @param type $sText
	 */
	public function addText($sText, $bConvertToHTMLSpecialChars = true)
	{
		$this->appendChildTextNode($sText, $bConvertToHTMLSpecialChars);
	}


	/**
	 * add text to an element
	 */
	public function appendChildTextNode($sText, $bConvertToHTMLSpecialChars = true)
	{
		$objText = new Text($this);
		$objText->setText ( $sText, $bConvertToHTMLSpecialChars );
		$this->appendChild ( $objText );
	}


	/**
	 * set text to an element and replaces all child nodes
	 */
	public function setTextContent($sText, $bConvertToHTMLSpecialChars = true)
	{
		$this->arrChildNodes = array(); //remove all child nodes
		$this->iCacheCountChildNodes = 0; //reset cache count

		$objText = new Text($this);
		$objText->setText ( $sText, $bConvertToHTMLSpecialChars );
		$this->appendChild ( $objText );
	}

	/**
	 * returns ALL text within a node (of all child nodes)
	 */
	public function getTextContent()
	{
		$sText = '';

		foreach ($this->arrChildNodes as $objChild)
		{
			$sText .= $objChild->getText();
		}

		return $sText;
	}

	/**
	 * set text to an element and replaces all child nodes
	 */
	public function setInnerHTML($sHTML)
	{
		$this->arrChildNodes = array(); //remove all child nodes
		$this->iCacheCountChildNodes = 0; //reset cache count
		
		$objText = new Text($this);
		$objText->setText ( $sHTML, false );
		$this->appendChild ( $objText );
	}

	/**
	 * returns ALL html within a node (of all child nodes)
	 */
	public function getInnerHTML()
	{
		$sText = '';

		foreach ($this->arrChildNodes as $objChild)
		{
			$sText .= $objChild->getTextContent();
		}

		return $sText;
	}


	/**
	 * returns if this is a text node
	 */
	public function isTextNode()
	{
		return ($this instanceof Text);
	}
	

	/**
	 * returns parent node
	 */
	public function getParentNode()
	{
		return $this->objParentNode;
	}
	
	/**
	 * get the node at index $iIndex
	 *
	 * @param int $iIndex        	
	 * @return HTMLTag
	 */
	public function getChildNode($iIndex) 
    {
		return $this->arrChildNodes[$iIndex];
	}

	/**
	 * get all children as array
	 *	
	 * @return HTMLTag
	 */
	public function getChildren() 
    {
		return $this->arrChildNodes;
	}	

	/**
	 * returns attribute names as array
	 */
	public function getAttributes()
	{
		return array_keys($this->arrAttributes);
	}

	/**
	 * return all attributes in associative array
	 * 
	 * layout array:
	 * arrAttributes['id'] => 'edtFirstName'
	 * arrAttributes['type'] => 'hidden' (like in: <input type="hidden">)
	 */
	public function getAttributesAss()
	{
		return $this->arrAttributes;
	}

	/**
	 * returns if there are attributes
	 */
	public function hasAttributes()
	{
		return ($this->iCacheCountAttributes > 0);
	}
	
	/**
	 * returns if there are child nodes
	 */
	public function hasChildNodes()
	{
		return ($this->iCacheCountChildNodes > 0);
	}

	/**
	 * return element with id $sHTMLElementID
	 * returns null if not found
	 * the php version of getElementById()
	 * 
	 * this function is recursive
	 */
	public function getNodeByID($sHTMLElementID)
	{
		$objTempNode = null;
		// tracepoint('volendam:'. $sHTMLElementID)			;
		// vardump($this->arrChildNodes);
		foreach ($this->arrChildNodes as $objNode)
		{
			if ($objNode->getID() == $sHTMLElementID)
			{
				return $objNode;
			}
			else //if not find dive deeper
			{
				$objTempNode = $objNode->getNodeByID($sHTMLElementID);
				if ($objTempNode)
					return $objTempNode;
			}
		}

		return null;
	}

	/**
	 * get next node
	 * (so you can easily use it in a while loop)
	 * 
	 * returns false if THE END IS NEAR!
	 * @return HTMLTag
	 */
	public function getNextChildNode()
	{		
		if ($this->iChildNodePointer < $this->iCacheCountChildNodes)
		{
			$objReturn = null;
			$objReturn = $this->arrChildNodes[$this->iChildNodePointer];
			++$this->iChildNodePointer;
			return $objReturn;
		}
		else
		{
			$this->iChildNodePointer = 0;
			return false;
		}
	}
	
	/**
	 * count the number of subnodes
	 *
	 * @return int number of nodes
	 *        
	 */
	public function countChildNodes() 
    {
		return $this->iCacheCountChildNodes;
	}
	

	/**
	 * count the number of subnodes
	 *
	 * @return int number of nodes
	 *        
	 */
	public function childElementCount() 
    {
		return $this->iCacheCountChildNodes;
	}	
	
	/**
	 * alias for renderHTMLNode(), but this name makes more sense if you want to render a whole DOM tree
	 * 
	 * @param $iLevel
	 */
	public function render($iLevel = 0) 
	{
		return $this->renderHTMLNode($iLevel);
	}
	
	/**
	 * create html code of this node
	 *
	 * @param int $iLevel leveldepth of the node (used for formatting the html code)
	 * @return string html of this node
	 */
	public function renderHTMLNode($iLevel = 0) 
    {
        // global $objLocalisation;
		$sHTML = '';         
		$iChildCount = 0;   
		$sTagName = '';
		$sTagName = $this->sTagName;    
                		
		// opening tag
		if ($sTagName != '') 
        {
			// ident
			if ($this->getSourceFormattingIdentForOpenTag ())
			{
				$sHTML.= $this->getIdentString($iLevel, $sTagName);
			}
				
			//open
			$sHTML .= '<' . sanitizeHTMLTagIdName($sTagName) ;
			
			//go through attributes
			$arrKeysAttr = array_keys($this->arrAttributes);
			foreach ($arrKeysAttr as $sAttribute)
			{
				$sHTML .= ' '.sanitizeHTMLTagAttribute($sAttribute).'="'.sanitizeHTMLTagAttributeValue($this->arrAttributes[$sAttribute]);
				if ($this->bIsArray)
					if ($sAttribute == HTMLTag::ATTRIBUTE_NAME)
						$sHTML .= '[]';
				$sHTML .= '"';
			}
					                                                                                     
			// add specific elements
			$sHTML .= $this->renderChild ();
			
			// close
			$sHTML .= '>';
			
			if ($this->getSourceFormattingNewLineAfterOpenTag ())
                $sHTML .= "\n";
                        
		}
		
		// loop child tags
		$iChildCount =  $this->countChildNodes();
		for($iChildIndex = 0; $iChildIndex < $iChildCount; $iChildIndex ++) 
        {
			// $objChild = $this->getChildNode ( $iChildIndex );
			$objChild = $this->arrChildNodes[$iChildIndex];
			$sHTML .= $objChild->renderHTMLNode ( $iLevel + 1 );
		}
		
		// closing tag
		if ($sTagName != '') 
        {
			if (!in_array($sTagName, HTMLTag::VOIDTAGS))
			{
				// ident
				if ($this->getSourceFormattingIdentForCloseTag ())
				{
					$sHTML.= $this->getIdentString($iLevel, $sTagName);
				}
				
				$sHTML .= '</' .  $sTagName . '>';
				
				if ($this->getSourceFormattingNewLineAfterCloseTag ())
					$sHTML .= "\n";
			}
		}           
                
		return $sHTML;
	}
	
	/**
	 * generate identation string
	 */
	private function getIdentString(&$iLevel)
	{
		$sResult = '';
		for($iIdentCounter = 0; $iIdentCounter < $iLevel; $iIdentCounter ++)
			$sResult .= "\t";
			
		return $sResult;
	}

	/**
	 * displays the rendered result of the render() function
	 */
	public function display() 
    {
		echo $this->renderHTMLNode ();
	}
	
	/**
	 * save rendered result to file
	 * 
	 * @param string $sFileName
	 *        	path of the file
	 * @return bool file save success ?
	 */
	public function saveToFile($sFileName) 
    {
		$sRendered = $this->renderHTMLNode ();
		return saveToFileString ( $sRendered, $sFileName );
	}
	
	/**
	 * internal function for easy and html-safe adding attributes to HTML string
	 * it checks if the attribute name is not empty (if empty, it will not add)
	 * 
	 * @param string $sAttributeName        	
	 * @param string $sAttributeValue        	
	 * @param boolean $AlwaysRender if true the attribute will allways be rendered (regardless if the value is empty)
	 * @return string
	 */
	protected function addAttributeToHTML($sAttributeName, $sAttributeValue, $AlwaysRender = false)
    {
		if (! $AlwaysRender) 
		{
			if ($sAttributeValue !== null)
			{
				if (strlen ( $sAttributeValue ) > 0)
					return ' ' . sanitizeHTMLTagAttribute ( $sAttributeName ) . '="' . sanitizeHTMLTagAttributeValue ( $sAttributeValue ) . '"';
			}
			return '';
		} 
		else
			return ' ' . sanitizeHTMLTagAttribute ( $sAttributeName ) . '="' . sanitizeHTMLTagAttributeValue ( $sAttributeValue ) . '"';
	}

	/**
	 * set key-value pair for <div data-*="value">. 
	 * For example: for <div data-animalsound="bark"> this corresponds setData('animalsound', 'bark')
	 * 
	 * This function exists for the javascript folks who try to access object->dataset->variable
	 * But it just manipulates the internal attributes with this name (prefixed by 'data-')
	 */
	public function setDataset($sKey, $sValue)
	{
		$this->arrAttributes['data-'.$sKey] = $sValue;
	}

	/**
	 * get value for <div data-*="value">. 
	 * For example: for <div data-animalsound="bark"> this corresponds get('animalsound')
	 * 
	 * This function exists for the javascript folks who try to access object->dataset->variable
	 * But it just manipulates the internal attributes with this name (prefixed by 'data-')
	 */
	public function getDataset($sKey)
	{
		return $this->arrAttributes['data-'.$sKey];
	}
	




	/**
	 * converts plain text into html nodes and subnodes objects of type HTMLTag
	 * 
	 * @param string $sInput plain text
	 * @param int $iLevel max depth level of children to parse. Really handy if you run into performance issues
	 */
	public function parse(&$sInput, $iMaxDepth = 100)
	{
		$iStartPos = 0;
		$this->parseNode($sInput, $iStartPos, $iMaxDepth, $this);
	}


	/**
	 * recursive function to parse nodes
	 * 
	 * $iPosReadHead updates constantly position after the tag GT-symbol (>) which can be a begin tag or end tag!
	 */
	private function parseNode(&$sInput, &$iPosReadHead, $iDepthLevel, $objParentNode)
	{
		$iPosLTBeginTag = 0;
		$iPosGTBeginTag = 0;
		$iPosSpace = 0;
		$sTextForTextNode = ''; //temp var for textNodes
		$iPosOpeningCaret = 0;
		$bAddClosingTag = false;
		$sCompleteTag = '';

		//==== depth
		--$iDepthLevel;
		if ($iDepthLevel < 0)
		{
			logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'html parse depth reached');
			return;
		}


		//===== loop through tags on same depth-level
		while ($this->parserDoNewTagsExist($sInput, $iPosReadHead))
		{		
			$bAddClosingTag = false;

			//==== begin tag
			$iPosLTBeginTag = strpos($sInput, '<', $iPosReadHead); 
			$iPosGTBeginTag = strpos($sInput, '>', $iPosLTBeginTag); 


			//==== text BEFORE tag
			if ($iPosLTBeginTag - $iPosReadHead) //is there text before tag?
			{
				if ($iPosReadHead == 0) //exception for first position (otherwise we miss the first character at the beginning)
					$sTextForTextNode = trim(substr($sInput, $iPosReadHead, ($iPosLTBeginTag - $iPosReadHead - 1))); //take iPosReadHead
				else
					$sTextForTextNode = trim(substr($sInput, ($iPosReadHead + 1), ($iPosLTBeginTag - $iPosReadHead - 1))); //take iPosReadHead+1

				if ($sTextForTextNode != '') //can be empty after trim
					$objParentNode->appendChildTextNode($sTextForTextNode, false);
			}



			$iPosReadHead = $iPosGTBeginTag; //update to: after begin tag



			//==== get tag name
			$iPosSpace = strpos($sInput, ' ', ($iPosLTBeginTag + 1));
			if ($iPosSpace === false) //not found
				$iPosSpace = $iPosGTBeginTag; //make it bigger than the tag
			if ($iPosSpace < $iPosGTBeginTag)//space within the tag
				$sTagName = substr($sInput, ($iPosLTBeginTag + 1), ($iPosSpace - $iPosLTBeginTag - 1)); //space in tag: then take the part before the space
			else
				$sTagName = substr($sInput, ($iPosLTBeginTag + 1), ($iPosGTBeginTag - $iPosLTBeginTag - 1)); //no space in tag:


			//==== create node
			$objChildNode = new HTMLTag($objParentNode);
			$objChildNode->setTagName($sTagName);
			$objParentNode->appendChild($objChildNode);
			
			
			//==== processing tag contents
			$sCompleteTag = substr($sInput, $iPosLTBeginTag + 1, ($iPosGTBeginTag - $iPosLTBeginTag - 1));
			$this->parserProcessTag($sCompleteTag, $objChildNode);


			//==== parse subnodes 
			if ($this->parserDoNewTagsExist($sInput, $iPosGTBeginTag)) //are there new nodes at all?
			{
				$this->parseNode($sInput, $iPosReadHead, $iDepthLevel, $objChildNode);
			}
			else //text-only node?
			{
				$iPosOpeningCaret = strpos($sInput, '<', $iPosReadHead);
				$sTextForTextNode = trim(substr($sInput, ($iPosReadHead + 1), ($iPosOpeningCaret - $iPosReadHead - 1)));
				if ($sTextForTextNode != '') //can be empty after trim
					$objChildNode->appendChildTextNode($sTextForTextNode, false);
			}

			//==== search for closing tag
			if ($sInput[$iPosGTBeginTag-1] != '/') //was opening tag self-closing?
			{
				if (!in_array($sTagName, HTMLTag::VOIDTAGS)) //not void tag
				{
					$bAddClosingTag = true;
					$iPosLTEndTag = strpos($sInput,  '</'.$sTagName, $iPosReadHead); 
					$iPosGTEndTag = strpos($sInput, '>', $iPosLTEndTag); 
					$iPosReadHead = $iPosGTEndTag; //update to: after end tag

					if ($iPosLTEndTag === false) //no end tag: parser error
						logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'HTML Parser error: can\'t find end-tag for <'.$sTagName.'>. Depth='.$iDepthLevel);
				}
			}

			//==== text AFTER tag
			if ($bAddClosingTag) 
			{
				//when it is a text-only node, then there is already a textnode added (see above: parse subnodes)
				$iPosOpeningCaret = strpos($sInput, '<', $iPosReadHead); 
				if ($iPosOpeningCaret !== false)
				{
					if ($iPosOpeningCaret - $iPosReadHead) //is there text before next tag?
					{
						$sTextForTextNode = trim(substr($sInput, ($iPosReadHead + 1), ($iPosOpeningCaret - $iPosReadHead - 1)));
						if ($sTextForTextNode != '') //can be empty after trim
							$objParentNode->appendChildTextNode($sTextForTextNode, false);
					}			
				}
			}
		}

		//==== is only text, but no tags
		if ((strlen($sInput) > 0) && ($iPosReadHead == 0)) //when there is input, but the readhead is untouched, there are no tags, only text
		{
			$sTextForTextNode = trim($sInput); //tru

			if ($sTextForTextNode != '') //can be empty after trim
				$objParentNode->appendChildTextNode($sTextForTextNode, false);
		}

	}


	/**
	 * determines if new opening tag exist after position $iAfterPos
	 */
	private function parserDoNewTagsExist(&$sInput, $iAfterPos)
	{
		$iPosOpeningCaret = strpos($sInput, '<', $iAfterPos); 
		if ($iPosOpeningCaret === false)
			return false;
		if (($sInput[$iPosOpeningCaret+1] != '/') && ($sInput[$iPosOpeningCaret+1] != '!'))//it's only an opening tag when the next character is NOT a /
			return true;

		return false;
	}


	/**
	 * process a tag attributes for the parser
	 * 
	 * I'm using the analogy of recording audio. I 'record' either a name or a value, or nothing
	 */
	private function parserProcessTag($sTagText, $objChildNode)
	{
		/* before 13-8-2025
		$arrTagParts = array();
		$arrAttrNameValue = array();
		$iAttrNameValueCount = 0;
		$iPosTemp = 0;
		$iLoopCounter = 0;


		$arrTagParts = explode(' ', $sTagText);//==> BUG: when space in value of attribute, it will explode on that as well
		foreach ($arrTagParts as $sTagPart) //read HTML attributes
		{		
			if ($iLoopCounter > 0) //skip the first part (because that is the tag-name)
			{
				$arrAttrNameValue = explode('=', $sTagPart); //explode is faster than strpos(), strlen(), substr()
				$iAttrNameValueCount = count($arrAttrNameValue);

				//implicit true, like: <input type="checkbox" checked>
				if ($iAttrNameValueCount == 1) 
					$objChildNode->setAttributeAsBool($arrAttrNameValue[0], true);

				//normal structure, like: name="value"
				if ($iAttrNameValueCount == 2)
				{
					$arrAttrNameValue[1] = str_replace('"', '', $arrAttrNameValue[1]); //strip the quotes (")

					//if array indicator ([]) in name-attribute, we need to strip the array indicator (like in: <input type="checkbox" name="chkSelected[]">)
					if ($arrAttrNameValue[0] == HTMLTag::ATTRIBUTE_NAME)
					{

						if (str_ends_with($arrAttrNameValue[1], '[]')) //is array?
						{
							$iPosTemp = strpos($arrAttrNameValue[1], '[');
							$arrAttrNameValue[1] = substr($arrAttrNameValue[1], 0, $iPosTemp); //strip the [] in the name
							$objChildNode->setIsArray(true); 
						}
					}

					//add to internal array with attributes
					$objChildNode->setAttribute($arrAttrNameValue[0], $arrAttrNameValue[1]);
					++$this->iCacheCountAttributes;
				}					
			}
			++$iLoopCounter;
		} 	*/
			

		/* new version: after 13-8-2025 */	
		
		//declare + init
		$iLenTagText = 0;
		$bRecordAttributeName = false;
		$bRecordAttributeValue = false;
		$iPosTemp = 0;
		$sAttributeName = '';
		$sAttributeValue = '';
		$bPreviousCharSpace = false;//previous character was a space
		$bPreviousCharIs = false;//previous character was an '='



		$iLenTagText = strlen($sTagText);
		for ($iIndex = 0; $iIndex < $iLenTagText; ++$iIndex)
		{			
			//detect multiple empty spaces between attributes
			if ((!$bRecordAttributeName) && (!$bRecordAttributeValue))
				if ($bPreviousCharSpace && ($sTagText[$iIndex] === ' ')) //current char AND previous char a space (there are multiple spaces between attributes)
					++$iIndex;//skip cycle
	
			//START RECORDING NAME
			//when it is the first character after a space when NOT recording a name or value
			if ((!$bRecordAttributeName) && (!$bRecordAttributeValue))
			{
				if ($bPreviousCharSpace && ($sTagText[$iIndex] !== ' ')) //encounter space when recording name
					$bRecordAttributeName = true;
			}
			//STOP RECORDING NAME
			//when recording a name and current character is an '='
			elseif ($bRecordAttributeName && ($sTagText[$iIndex] === '='))
			{
				$bRecordAttributeName = false;
			}
			//can be implicit true, like: <input type="checkbox" checked name="bert">
			//implicit true can be in the middle ($sTagText[$iIndex] === ' ') or at the end ($iIndex == ($iLenTagText-1))
			elseif ($bRecordAttributeName && (($sTagText[$iIndex] === ' ') || ($iIndex == ($iLenTagText-1))))
				$bRecordAttributeName = false;

			//START RECORDING VALUE
			//when not recording a name or attribute
			//when previous character is an '=' and current an opening quote (")
			if ((!$bRecordAttributeName) && (!$bRecordAttributeValue))
			{
				if ($bPreviousCharIs && ($sTagText[$iIndex] === '"'))
				{
					++$iIndex;//skip to next character, otherwise we record the start quote '"'
					$bRecordAttributeValue = true;
				}
			}
			//STOP RECORDING VALUE
			//when we are recording a value and we encounter an end quote (")
			elseif ($bRecordAttributeValue && ($sTagText[$iIndex] === '"'))
			{
				$bRecordAttributeValue = false;					

				//name attribute can be array
				if (strtolower($sAttributeName) == 'name')
				{
					if (str_ends_with($sAttributeValue, '[]')) //is array?
					{
						$iPosTemp = strpos($sAttributeValue, '[');
						$sAttributeValue = substr($sAttributeValue, 0, $iPosTemp); //strip the [] in the name
						$objChildNode->setIsArray(true); 
					}
				}

				//we have now enough info to set the attribute and clean the name and value we were recording
				$objChildNode->setAttribute($sAttributeName, $sAttributeValue);
				$sAttributeName = '';
				$sAttributeValue = '';
			}

			//record name
			if ($bRecordAttributeName)
				$sAttributeName.= $sTagText[$iIndex];

			//record value
			if ($bRecordAttributeValue)
				$sAttributeValue.= $sTagText[$iIndex];

			//remember previous characters
			$bPreviousCharSpace = ($sTagText[$iIndex] === ' ');
			$bPreviousCharIs = ($sTagText[$iIndex] === '=');
		}
	}
		

	/**
	 * this function is called by render() for rendering specific html code inside the tag
	 * (mostly adding attributes)
	 *
	 * @return string with html code in node
	 */
	protected function renderChild() {}
}


?>