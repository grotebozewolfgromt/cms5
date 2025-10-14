<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\HTMLTag;
use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TFloat;

/**
 * <dr-db-filter>
 * 
 * PHP counterpart for web component <dr-db-filter>
 * this is a HTML tag where you can add database filters and this class converts it to the format that <dr-db-filter> needs
 * 
 * WARNING:
 * 1. this class replaces table names and field with indexes for security reasons (prevents nefarious actor from messing with it client side)\
 * 
 * @author Dennis Renirie
 * 4 apr 2025 DRDBFilters created
 * 24 apr 2025 DRDBFilters.js bugfix: eerst werd event ge-dispatched en daarna verwijderd van DOM. hierdoor gebeurde er niks wanneer je een chip verwijderde
 *
 */
class DRDBFilters extends HTMLTag
{
	private $arrFilters = array();

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-db-filters');

		//proper includes
		// includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-db-filters'.DIRECTORY_SEPARATOR.'style.css');
		// includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-context-menu'.DIRECTORY_SEPARATOR.'style.css');
		// includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'style.css');

		includeJSWebcomponent('dr-popover');
		includeJSWebcomponent('dr-input-combobox');
		includeJSWebcomponent('dr-input-checkbox-group');
		includeJSWebcomponent('dr-input-checkbox');
		includeJSWebcomponent('dr-context-menu');
		includeJSWebcomponent('dr-input-date'); 
		includeJSWebcomponent('dr-input-text'); 
		includeJSWebcomponent('dr-input-number'); 
		includeJSWebcomponent('dr-db-filters'); 
		// includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'dr-input-combobox.js');
		// includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-context-menu'.DIRECTORY_SEPARATOR.'dr-context-menu.js');
		// includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-db-filters'.DIRECTORY_SEPARATOR.'dr-db-filters.js');
	}

	/**
	 * @param DRDBFilter $objFilter 
	 */
	public function addFilter(DRDBFilter $objFilter)
	{
		$this->arrFilters[] = $objFilter;
	}

	/**
	 * reads JSON data produced by javascript web component: <dr-db-filter>
	 * and convert it into internal filter objects in internal array $arrFilters
	 * 
	 * @param bool $bPostArray true=$_POST and false=$_GET
	 * @param string the field in $_POST[$sField] or $_GET[$sField] array that contains the JSON data
	 */
	public function readJSON($b_POSTArray, $sField)
	{
		$arrJSON = array();
		$iIndex = 0;
		$iCountFilters = 0;
		$iCountJSON = 0;
		$objFilter = null;
		$objFiltersCopy = array(); //stores temp copy of internal filters
		$iFilterIndex = 0;


		if ($b_POSTArray)
		{
			if (isset($_POST[$sField]))
				$arrJSON = json_decode($_POST[$sField], true);
		}
		else
		{
			if (isset($_GET[$sField]))
				$arrJSON = json_decode($_GET[$sField], true);
		} 

		//read JSON
		//and add these filters
		$iCountJSON = count($arrJSON);
		if ($iCountJSON > 0)
		{
			//reset/remove internal filters, 
			//but keep the ones with status "available", 
			//otherwise they are also removed from the GUI and the user can't see them
			$objFiltersCopy = $this->arrFilters;//copy
			$this->arrFilters = array();
			$iCountFilters = count($objFiltersCopy);
			for ($iIndex = 0; $iIndex < $iCountFilters; $iIndex++)
			{
				//keep the ones with status "available"
				if ($objFiltersCopy[$iIndex]->getStatus() == DRDBFilter::STATUS_AVAILABLE)
					$this->arrFilters[] = $objFiltersCopy[$iIndex];
			}
						
			//loop JSON array
			for ($iIndex = 0; $iIndex < $iCountJSON; $iIndex++)
			{
				$objFilter = new DRDBFilter();

				//find corresponding filter as "available" filter
				//this allows us to find the database field (that we don't want to send to the GUI for everyone to read)
				//but also allows us to do extra security checks
				$objAvailableFilter = null;
				$iFilterIndex = strToInt($arrJSON[$iIndex][DRDBFilter::ATTR_FILTERINDEX], true); //WARNING: Quicksearch doesn't give filterindex back!
				if (($iFilterIndex >= $iCountFilters) || ($iFilterIndex < 0)) //looking for invalid indexes
					return;
				else
					$objAvailableFilter = $this->arrFilters[$iFilterIndex];

				//status
				switch ($arrJSON[$iIndex][DRDBFilter::ATTR_STATUS]) //prevent injection by checking validity of values
				{
					case DRDBFilter::STATUS_AVAILABLE:
					case DRDBFilter::STATUS_APPLIED:
						$objFilter->setStatus($arrJSON[$iIndex][DRDBFilter::ATTR_STATUS]);
						break;
					default:
						$objFilter->setStatus(DRDBFilter::STATUS_APPLIED);
				}

				//type
				switch ($arrJSON[$iIndex][DRDBFilter::ATTR_FILTERTYPE]) //prevent injection by checking validity of values
				{
					case DRDBFilter::TYPE_BOOLEAN:
					case DRDBFilter::TYPE_DATE:
					case DRDBFilter::TYPE_NUMBER:
					case DRDBFilter::TYPE_QUICKSEARCH:
					case DRDBFilter::TYPE_STRING:
					case DRDBFilter::TYPE_HTMLELEMENT:
						$objFilter->setType($arrJSON[$iIndex][DRDBFilter::ATTR_FILTERTYPE]);
						break;
					default:
						$objFilter->setType(DRDBFilter::TYPE_STRING);
				}

				//database field --> for security reasons use the database fields from available filters instead of storing it in the publicly available filters
				if ($arrJSON[$iIndex][DRDBFilter::ATTR_FILTERTYPE] != DRDBFilter::TYPE_QUICKSEARCH) //quicksearch has no fields
					$objFilter->setDBTableField($objAvailableFilter->getDBTable(), $objAvailableFilter->getDBField());


				//disabled
				if ($arrJSON[$iIndex][DRDBFilter::ATTR_DISABLED] === false)
					$objFilter->setDisabled(false);
				else
					$objFilter->setDisabled(true);

				//comparison operator
				if ($arrJSON[$iIndex][DRDBFilter::ATTR_FILTERTYPE] != DRDBFilter::TYPE_QUICKSEARCH) //quicksearch has always comparison operator "like" 
				{				
					if (array_key_exists($arrJSON[$iIndex][DRDBFilter::ATTR_COMPARISONOPERATOR], DRDBFilter::COMP_OP_TRANSLATION)) //prevent injection by checking validity of values
						$objFilter->setComparisonOperator($arrJSON[$iIndex][DRDBFilter::ATTR_COMPARISONOPERATOR]);
					else
						$objFilter->setComparisonOperator(DRDBFilter::COMPARISONOPERATOR_EQUALTO);
				}
				else
					$objFilter->setComparisonOperator(DRDBFilter::COMPARISONOPERATOR_LIKE);

				//value and endvalue
				switch ($objAvailableFilter->getType()) //prevent injection by checking validity of values
				{
					case DRDBFilter::TYPE_BOOLEAN:
						$objFilter->setValue(strToBool($arrJSON[$iIndex][DRDBFilter::ATTR_VALUE]));
						// $objFilter->setValueEnd(strToBool($arrJSON[$iIndex]['valueend'])); ==> range of booleans doesn't exist
						break;
					case DRDBFilter::TYPE_DATE:
						$objFilter->setValue(filterBadCharsWhiteListLiteral($arrJSON[$iIndex][DRDBFilter::ATTR_VALUE], WHITELIST_ISO8601));
						$objFilter->setValueEnd(filterBadCharsWhiteListLiteral($arrJSON[$iIndex][DRDBFilter::ATTR_VALUEEND], WHITELIST_ISO8601));
						break;
					case DRDBFilter::TYPE_NUMBER:
						$objFloat = new TDecimal($arrJSON[$iIndex][DRDBFilter::ATTR_VALUE], 4);
						$objFilter->setValue($objFloat->getValue());
						$objFloat->setValue($arrJSON[$iIndex][DRDBFilter::ATTR_VALUEEND]);
						$objFilter->setValueEnd($objFloat->getValue());
						break;
					default: //quicksearch and string and everything else
						$objFilter->setValue(filterBadCharsWhiteList($arrJSON[$iIndex][DRDBFilter::ATTR_VALUE], REGEX_ALPHANUMERIC_UNDERSCORE_MINUS, true));
				}

				//name nice
				$objFilter->setNameNice($objAvailableFilter->getNameNice());
				
				$this->arrFilters[] = $objFilter;
			}
			// vardumpdie($arrJSON, "froietmetfrikandellen");

		}

	}

	/**
	 * create html code of this node
	 *
	 * @param int $iLevel leveldepth of the node (used for formatting the html code)
	 * @return string html of this node
	 */
	public function renderHTMLNode($iLevel = 0) 
	{
		$this->renderChildren();
		return parent::renderHTMLNode($iLevel);
	}
	
	/**
	 * renders the filter chips inside DRDBFilters
	 */
	private function renderChildren()
	{
		$iChildCount = 0;

		//remove child nodes
		$this->arrChildNodes = array();

		//loop filters
		$iChildCount = count($this->arrFilters);
		for ($iIndex = 0; $iIndex < $iChildCount; $iIndex++)
		{
			$objChildNode = new HTMLTag($this);
			$objChildNode->setTagName('div');
			$objChildNode->setAttribute(DRDBFilter::ATTR_STATUS, $this->arrFilters[$iIndex]->getStatus());
			if ($this->arrFilters[$iIndex]->getDisabled())//only append when disabled=true (otherwise it will always be disabled)
				$objChildNode->setAttribute(DRDBFilter::ATTR_DISABLED, $this->arrFilters[$iIndex]->getDisabled());
			$objChildNode->setAttribute(DRDBFilter::ATTR_FILTERTYPE, $this->arrFilters[$iIndex]->getType());				
			$objChildNode->setAttribute(DRDBFilter::ATTR_VALUE, $this->arrFilters[$iIndex]->getValue());				
			$objChildNode->setAttribute(DRDBFilter::ATTR_VALUEEND, $this->arrFilters[$iIndex]->getValueEnd());		
			$objChildNode->setAttribute(DRDBFilter::ATTR_FILTERINDEX, $iIndex);				
			$objChildNode->setAttribute(DRDBFilter::ATTR_COMPARISONOPERATOR, $this->arrFilters[$iIndex]->getComparisonOperator());				
			$objChildNode->setAttribute(DRDBFilter::ATTR_NAMENICE, $this->arrFilters[$iIndex]->getNameNice());
			$objChildNode->setTextContent($this->arrFilters[$iIndex]->getNameNice());
			if ($this->arrFilters[$iIndex]->getHTMLElement())
				$objChildNode->appendChild($this->arrFilters[$iIndex]->getHTMLElement());
			$this->appendChild($objChildNode);			
		}
	}

	/**
	 * create database query based on the filters
	 * 
	 * @param TSysModel $objModel
	 */
	public function createDBQuery($objModel)
	{
		$objFilter = null;

		//loop filters
		$iChildCount = count($this->arrFilters);
		for ($iIndex = 0; $iIndex < $iChildCount; $iIndex++)
		{
			$objFilter =  $this->arrFilters[$iIndex];

			if (($objFilter->getStatus() == DRDBFilter::STATUS_APPLIED) && ($objFilter->getDisabled() == false))
			{
				$sType = $objFilter->getType();//temp debug
				switch ($objFilter->getType()) 
				{
					case DRDBFilter::TYPE_BOOLEAN:
						$objModel->find($objFilter->getDBField(), $objFilter->getValue(), $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_BOOL);
						break;
					case DRDBFilter::TYPE_DATE:
						$objDateStart = new TDateTime();
						$objDateStart->setISOString($objFilter->getValue());
						$objDateStart->setHour(0);
						$objDateStart->setMinute(0);
						$objDateStart->setSecond(0);
						$objDateEnd = new TDateTime();
						if ($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_BETWEEN)							
							$objDateEnd->setISOString($objFilter->getValueEnd()); //take end-value
						else
							$objDateEnd->setISOString($objFilter->getValue());	//take the begin-value and make it the end of the day of the begin date
						$objDateEnd->setHour(23);
						$objDateEnd->setMinute(59);											
						$objDateEnd->setSecond(59);
						if (($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_BETWEEN) || ($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_EQUALTO))	// also do a between for equal-to to represent the entire day from 0:00 to 23:59
							$objModel->findBetween($objFilter->getDBField(), $objDateStart, $objDateEnd, $objFilter->getDBTable(), TP_DATETIME); 
						elseif ($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_LESSEQUAL) //includes day its, so I use the end date
							$objModel->find($objFilter->getDBField(), $objDateEnd, $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_DATETIME);						
						elseif ($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_GREATEREQUAL) //includes day its, so I use the begin date
							$objModel->find($objFilter->getDBField(), $objDateStart, $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_DATETIME);						
						break;
					case DRDBFilter::TYPE_NUMBER:
						if ($objFilter->getComparisonOperator() === DRDBFilter::COMPARISONOPERATOR_BETWEEN) 
							$objModel->findBetween($objFilter->getDBField(), $objFilter->getValue(), $objFilter->getValueEnd(), $objFilter->getDBTable(), TP_DECIMAL);
						else
							$objModel->find($objFilter->getDBField(), $objFilter->getValue(), $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_DECIMAL);						
						break;
					case DRDBFilter::TYPE_QUICKSEARCH:
						$objModel->findQuickSearch($objFilter->getValue());
						break;
					default:
						if (($objFilter->getComparisonOperator() == DRDBFilter::COMPARISONOPERATOR_LIKE) || ($objFilter->getComparisonOperator() == DRDBFilter::COMPARISONOPERATOR_NOTLIKE))
							$objModel->find($objFilter->getDBField(), '%'.$objFilter->getValue().'%', $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_STRING);
						else
							$objModel->find($objFilter->getDBField(), $objFilter->getValue(), $objFilter->getComparisonOperator(true), $objFilter->getDBTable(), TP_STRING);
				}
			}
		}
	}
}

?>
