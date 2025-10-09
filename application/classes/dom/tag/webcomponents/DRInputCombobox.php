<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\Div;
use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\models\TSysModel;
use dr\classes\types\TDecimal;

/**
 * represents a <dr-input-combobox>
 * part of PHP counterpart for web component <dr-input-combobox>
 * 
 * 
 * @author Dennis Renirie
 * 21 may 2025: DRInputCombobox created
 * 15 aug 2025: DRInputCombobox: addItemsFromModel() and addItem()
 *
 */
class DRInputCombobox extends FormInputAbstract
{
	const TYPE_SELECTONE = 'selectone';//selects only one items at a time. Corresponds with arrTypes = {selectone: "selectone", selectmultiple: "selectmultiple"} 
	const TYPE_SELECTMULTIPLE = 'selectmultiple';//selects multiple items with checkboxes. Corresponds with arrTypes = {selectone: "selectone", selectmultiple: "selectmultiple"} 


	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-input-combobox');

		// $this->setAttribute('disabled', ''); 
		// $this->setAttribute('value', ''); 
		$this->setAttribute('placeholder', transg('dr-input-combobox_placeholder_default', 'Select an item')); //when no default value is selected, this is shown
		$this->setAttribute('valueseparator', ','); //separates values that are being set() and get() from this component. this only is used when selecting multiple items
		$this->setAttribute('placeholdersearch', transg('dr-input-combobox_placeholder_search_default', 'Search')); //placeholder for search textbox

		//proper includes
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-popover'.DIRECTORY_SEPARATOR.'dr-popover.js');
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-combobox'.DIRECTORY_SEPARATOR.'dr-input-combobox.js');
	}

	/**
	 * set disabled
	 */
	public function setDisabled($bDisabled)
	{
		if ($bDisabled)
			$this->setAttribute('disabled', '');
		else
			$this->removeAttribute('disabled');
	}

	/**
	 * get disabled
	 * 
	 * @return boolean
	 */
	public function getDisabled()
	{
		if ($this->hasAttribute('disabled'))
			return $this->getAttributeAsBool('disabled');
		else
			return false;
	}	

	/**
	 * set value with a string where the decimal separator is ALWAYS a dot (.)
	 */
	public function setValue($sValue)
	{
		$this->setAttribute('value', $sValue);
	}

	/**
	 * get value. the return value is a string where the decimal separator is ALWAYS a dot (.)
	 * 
	 * @return string
	 */
	public function getValue()
	{
		return $this->getAttribute('value');
	}

	/**
	 * set placeholder text like: "select an item"
	 */
	public function setPlaceholder($sPlaceholder)
	{
		$this->setAttribute('placeholder', $sPlaceholder);
	}

	/**
	 * get placeholder text like "Select an item"
	 */
	public function getPlaceholder()
	{
		return $this->getAttribute('placeholder');
	}

	/**
	 * set value separator of values that are returned by component
	 * (this only is used when selecting multiple items)
	 */
	public function setValueSeparator($sSeparator = ',')
	{
		$this->setAttribute('valueseparator', $sSeparator);
	}

	/**
	 * get value separator of values that are returned by component
	 * (this only is used when selecting multiple items)
	 */
	public function getValueSeparator()
	{
		return $this->getAttribute('valueseparator');
	}

	/**
	 * set placeholder for search textbox
	 */
	public function setPlaceholderSearch($sSeparator = 'Search')
	{
		$this->setAttribute('placeholdersearch', $sSeparator);
	}

	/**
	 * get placeholder for search textbox
	 */
	public function getPlaceholderSearch()
	{
		return $this->getAttribute('placeholdersearch');
	}


	/**
	 * set type: DRInputCombobox::TYPE_SELECTONE or DRInputCombobox::TYPE_SELECTMULTIPLE
	 */
	public function setType($sType = DRInputCombobox::TYPE_SELECTONE)
	{
		$this->setAttribute('type', $sType);
	}

	/**
	 * get type: DRInputCombobox::TYPE_SELECTONE or DRInputCombobox::TYPE_SELECTMULTIPLE
	 */
	public function getType()
	{
		return $this->getAttribute('type');
	}	

	/**
	 * adds item to the combobox
	 * (this is flat, like a 1d list)
	 * 
	 * @param string $sValue return value when asked for the value of this object with $_POST/$_GET. This is often the id of a database record
	 * @param string $sGUIText text that is displayed to the user
	 * @param bool $bIsSelected is the item you are adding the selected item in the combobox?
	 */
	public function addItem($sValueOrID, $sGUIText, $bIsSelected = false)
	{
		$objDiv = new Div();
		$objDiv->setAttribute('value', $sValueOrID);
		if ($bIsSelected)
			$objDiv->setAttribute('selected', true);
		$objDiv->setTextContent($sGUIText);

		$this->appendChild($objDiv);
	}

	/**
	 * put items from a TSysModel into a combobox
	 * uses getID() as html 'value' attribute and getDisplayRecordShort() methods from TSysModel
	 * 
	 * WARNING: ONLY WORKS WITH IDs, not uniqueid or randomid
	 */
	public function addItemsFromModel(TSysModel &$objModel, $iSelectedID = 0)
	{
		$objModel->resetRecordPointer();
		while($objModel->next())
			$this->addItem($objModel->getID(), $objModel->getDisplayRecordShort(), ($iSelectedID == $objModel->getID()));
	}
	
}

?>
