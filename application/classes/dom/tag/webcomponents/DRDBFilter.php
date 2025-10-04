<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * represents a filter in <dr-db-filter>
 * 1. The applied filters are represented in the UI with a chip
 * 2. But the user can also select available filters from the menu.
 * Both are supplied as <div>s to <dr-db-filter>
 * 
 * part of PHP counterpart for web component <dr-db-filter>
 * 
 * 
 * @author Dennis Renirie
 * 4 apr 2025 created
 *
 */
class DRDBFilter
{
	
	private $sStatus = DRDBFilter::STATUS_AVAILABLE;
	private $bDisabled = false;
	private $sType = DRDBFilter::TYPE_STRING;
	private $sValue = ""; //either value or start value (depending on whether it's a range or not)
	private $sValueEnd = ""; //end value in a range
	private $sDBTable = ""; //don't use actual field names to prevent malicious actors from doing shady shizzle, use column indexes instead for example
	private $sDBField = ""; //don't use actual field names to prevent malicious actors from doing shady shizzle, use column indexes instead for example
	private $sComparisonOperator = DRDBFilter::COMPARISONOPERATOR_EQUALTO; //internally stores the comparison operator values internal to this class, you can translate them to system-wide comparison operators with COMP_OP_TRANSLATION
	private $sNameNice = "";//a nice name for the database column
	private $objHTMLElement = null; //FormInputAbstract object. HTML element like <dr-input-combobox>
	
	const ATTR_FILTERINDEX			= "filterindex"; //index of the filter in the filter list. This is how we identify the filters in the GUI, so PHP and Javascript can communicate
	const ATTR_STATUS				= "status"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_DISABLED				= "disabled"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_FILTERTYPE			= "filtertype"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_VALUE				= "value"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_VALUEEND				= "valueend"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_VALUESCOMBOBOX		= "valuescombobox"; //corresponds with the array value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_COMPARISONOPERATOR	= "comparisonoperator"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 
	const ATTR_NAMENICE				= "namenice"; //corresponds with the value in <dr-db-filter>:  arrChipAttributes = {status: "status", disabled: "disabled", filtertype: "filtertype", value: "value", valueend: "valueend", dbfield: "dbfield", comparisonoperator: "comparisonoperator", namenice:"namenice"}; 

	const TYPE_DATE					= 'date'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement""};
	const TYPE_NUMBER				= 'number'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement""};
	const TYPE_STRING				= 'string'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement""};
	const TYPE_QUICKSEARCH			= 'quicksearch'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement""};
	const TYPE_BOOLEAN				= 'boolean'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement"};
	const TYPE_HTMLELEMENT			= 'htmlelement'; //corresponds with the value in <dr-db-filter>: arrFilterTypes = {date: "date", number: "number", string: "string", quicksearch: "quicksearch", boolean: "boolean", htmlelement:"htmlelement"};

	const STATUS_AVAILABLE			= 'available'; //available is displayed in new-filter-menu (but not visible as chip). Corresponds with value in <dr-db-filter>: arrFilterStatus = {available: "available", applied: "applied"}; 
	const STATUS_APPLIED    		= 'applied'; //applied is visible as chip in the user interface. Corresponds with value in <dr-db-filter>: arrFilterStatus = {available: "available", applied: "applied"}; 
	
	const COMPARISONOPERATOR_EQUALTO 		= 'equalto'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_NOTEQUALTO 	= 'notequalto'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_LIKE 			= 'like'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_NOTLIKE 		= 'notlike'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_LESSEQUAL 		= 'lessequal'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_GREATEREQUAL 	= 'greaterequal'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}
	const COMPARISONOPERATOR_BETWEEN 		= 'between'; //corresponds with value in <dr-db-filter>: arrComparisonOperators = {equalto: "equalto", notequalto: "notequalto", like: "like", notlike: "notlike", lessequal: "lessequal", greaterequal: "greaterequal", between: "between"}

	const COMP_OP_TRANSLATION = array(
			DRDBFilter::COMPARISONOPERATOR_EQUALTO => COMPARISON_OPERATOR_EQUAL_TO,
			DRDBFilter::COMPARISONOPERATOR_NOTEQUALTO => COMPARISON_OPERATOR_NOT_EQUAL_TO,
			DRDBFilter::COMPARISONOPERATOR_LIKE => COMPARISON_OPERATOR_LIKE,
			DRDBFilter::COMPARISONOPERATOR_NOTLIKE => COMPARISON_OPERATOR_NOT_LIKE,
			DRDBFilter::COMPARISONOPERATOR_LESSEQUAL => COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO,
			DRDBFilter::COMPARISONOPERATOR_GREATEREQUAL => COMPARISON_OPERATOR_LESS_THAN_OR_EQUAL_TO,
			DRDBFilter::COMPARISONOPERATOR_BETWEEN => COMPARISON_OPERATOR_BETWEEN,
	); //lib_sys_typedef to webcomponent translation (not language translation)

	public function __construct()
	{
	}


	/**
	 * set status of filter.
	 * it is either STATUS_AVAILABLE or STATUS_APPLIED
	 */
	public function setStatus($sStatus = DRDBFilter::STATUS_AVAILABLE)
	{
		$this->sStatus = $sStatus;
	}

	/**
	 * get status of filter.
	 * it is either STATUS_AVAILABLE or STATUS_APPLIED
	 */
	public function getStatus()
	{
		return $this->sStatus;
	}

	/**
	 * set disabled
	 */
	public function setDisabled($bDisabled = false)
	{
		$this->bDisabled = $bDisabled;
	}

	/**
	 * get disabled
	 */
	public function getDisabled()
	{
		return $this->bDisabled;
	}

	/**
	 * set type:
	 * TYPE_DATE, TYPE_NUMBER, TYPE_STRING, TYPE_QUICKSEARCH, TYPE_BOOLEAN
	 */
	public function setType($sType = DRDBFilter::TYPE_STRING)
	{
		$this->sType = $sType;
	}

	/**
	 * set type:
	 * TYPE_DATE, TYPE_NUMBER, TYPE_STRING, TYPE_QUICKSEARCH, TYPE_BOOLEAN
	 */
	public function getType()
	{
		return $this->sType;
	}

	/**
	 * set start value
	 * 
	 * @param string sValue
	 */
	public function setValue($sValue)
	{
		$this->sValue = $sValue;
	}

	/**
	 * get start value
	 */
	public function getValue()
	{
		return $this->sValue;
	}

	/**
	 * set end value
	 */
	public function setValueEnd($sValue)
	{
		$this->sValueEnd = $sValue;
	}

	/**
	 * get end value
	 */
	public function getValueEnd()
	{
		return $this->sValueEnd;
	}

	/**
	 * sets HTML element for filter
	 * 
	 * @param FormInputAbstract $objHTMLElement
	 */
	public function setHTMLElement(FormInputAbstract $objHTMLElement)
	{
		$this->objHTMLElement = $objHTMLElement;
	}

	/**
	 * gets HTML element for filter
	 * 
	 * @return FormInputAbstract
	 */
	public function getHTMLElement()
	{
		return $this->objHTMLElement;
	}


	/**
	 * set database field
	 * 
	 * the actual field names aren't used in javascript to prevent malicious actors from doing shady shizzle, column indexes are used instead
	 */
	public function setDBTableField($sDBTable, $sField)
	{
		$this->sDBTable = $sDBTable;
		$this->sDBField = $sField;
	}

	/**
	 * set database field
	 * 
	 * the actual field names aren't used in javascript to prevent malicious actors from doing shady shizzle, column indexes are used instead
	 */
	public function setDBField($sField)
	{
		$this->sDBField = $sField;
	}

	/**
	 * get database field
	 * 
	 * the actual field names aren't used in javascript to prevent malicious actors from doing shady shizzle, column indexes are used instead
	 */
	public function getDBField()
	{
		return $this->sDBField;
	}	

	/**
	 * set database table
	 * 
	 * the actual table names aren't used in javascript to prevent malicious actors from doing shady shizzle, column indexes are used instead
	 */
	public function setDBTable($sTable)
	{
		$this->sDBTable = $sTable;
	}


	/**
	 * get database table
	 * 
	 * the actual field names aren't used in javascript to prevent malicious actors from doing shady shizzle, column indexes are used instead
	 */
	public function getDBTable()
	{
		return $this->sDBTable;
	}	

	/**
	 * set comparison operator
	 * 
	 * values are internally stored as DRDBFilter::COMPARISONOPERATOR_EQUALTO etc
	 * 
	 * @param string $sOperator internal comparison operator value
	 * @param bool $bInputIsSystemWideComparisonOperator set value as system wide comparison operator value (this function translates to internal value)
	 */
	public function setComparisonOperator($sOperator = DRDBFilter::COMPARISONOPERATOR_EQUALTO, $bInputIsSystemWideComparisonOperator = false)
	{
		if ($bInputIsSystemWideComparisonOperator)
		{
			foreach(DRDBFilter::COMP_OP_TRANSLATION as $sKey)
			{
				if ($sKey == $sOperator)
				{
					$this->sComparisonOperator = DRDBFilter::COMP_OP_TRANSLATION[$sKey];
					return;
				}
			}
		}
		else
			$this->sComparisonOperator = $sOperator;
	}

	/**
	 * get comparison operator
	 * COMPARISONOPERATOR_EQUALTO, COMPARISONOPERATOR_NOTEQUALTO, COMPARISONOPERATOR_LIKE, COMPARISONOPERATOR_NOTLIKE, COMPARISONOPERATOR_LESSEQUAL, COMPARISONOPERATOR_GREATEREQUAL = COMPARISON_OPERATOR_NOT_LIKE, COMPARISONOPERATOR_BETWEEN
	 */
	public function getComparisonOperator($bTranslateToSystemWideComparisonOperator = false)
	{
		if ($bTranslateToSystemWideComparisonOperator)
		{
			return DRDBFilter::COMP_OP_TRANSLATION[$this->sComparisonOperator];
		}

		return $this->sComparisonOperator;
	}	

	/**
	 * set user friendly name for this filter
	 */
	public function setNameNice($sName)
	{
		$this->sNameNice = $sName;
	}

	/**
	 * get user friendly name for this filter
	 **/
	public function getNameNice()
	{
		return $this->sNameNice;
	}		
	
}

?>
