<?php
namespace dr\classes\dom\tag\webcomponents;

use dr\classes\dom\tag\form\Form;
use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDecimal;

/**
 * represents a <dr-input-checkbox>
 * PHP counterpart for web component <dr-input-checkbox>
 * 
 * TIP:
 * - use function getCheckedSubmitted() to read (from $_POST or $_GET) if checkbox is checked when submitting a form
 * - to set text for the checkbox use setLabel('my label text');
 * 
 * @author Dennis Renirie
 * 1 may 2025: DRInputCheckbox created
 *
 */
class DRInputCheckbox extends FormInputAbstract
{
	const TYPE_CHECKBOX = 'checkbox';
	const TYPE_RADIO = 'radio';
	const TYPE_SWITCH = 'switch';

	public function __construct($objParentNode = null)
	{
		parent::__construct($objParentNode);
		$this->setTagName('dr-input-checkbox');

		//the label is internal text		

		// $this->setAttribute('disabled', ''); 
		// $this->setAttribute('checked', false); 
		$this->setAttribute('value', 1); //the webcomponent assumes "on" because it's how <input type="checkbox"> behaves
		$this->setAttribute('valueunchecked', 0); //the webcomponent assumes "" because it's how <input type="checkbox"> behaves
		$this->setAttribute('type', DRInputCheckbox::TYPE_CHECKBOX); 

		//proper includes
		includeCSS(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox'.DIRECTORY_SEPARATOR.'style.css');
		includeJSDOMEnd(APP_PATH_CMS_JAVASCRIPTS.DIRECTORY_SEPARATOR.'webcomponents'.DIRECTORY_SEPARATOR.'dr-input-checkbox'.DIRECTORY_SEPARATOR.'dr-input-checkbox.js');
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
	 * set label (the text after the checkbox)
	 * 
	 * @param string $sText text for the label
	 */
	public function setLabel($sText)
	{	
		$this->setInnerHTML($sText, true);
	}

	/**
	 * get label (the text after the checkbox)
	 * 
	 * @return string
	 */
	public function getLabel()
	{
		return $this->getInnerHTML();
	}	

	/**
	 * set checked
	 */
	public function setChecked($bChecked)
	{
		if ($bChecked)
			$this->setAttribute('checked', true);
		else
			$this->removeAttribute('checked');
	}

	/**
	 * get checked
	 */
	public function getChecked()
	{
		return $this->getAttributeAsBool('checked');
	}

	/**
	 * read value from either $_POST or $_GET: if checkbox was checked 
	 */
	public function getCheckedSubmitted($sFormMethod = Form::METHOD_POST)
	{
		$sFieldName = '';
		$sFieldName = $this->getName(false); //we want the name without parentheses
		

		//read the values from the proper array
		if ($sFormMethod == Form::METHOD_POST)
		{
			if (isset($_POST[$sFieldName]))  //only when exists at all
			{
				if ($_POST[$sFieldName] == $this->getValue())
					return true;
				else
					return false;
			}

			if (isset($_FILES[$sFieldName])) //only when exists at all
			{
				if ($_FILES[$sFieldName] == $this->getValue())
					return true;
				else
					return false;
			}
		}
		elseif ($sFormMethod == Form::METHOD_GET)
		{
			if (isset($_GET[$sFieldName])) //only when exists at all
			{
				if ($_GET[$sFieldName] == $this->getValue())
					return true;
				else
					return false;
			}
		}

		return false; //should not be able to come here
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
	 * set value when checkbox is unchecked
	 */
	public function setValueUnchecked($sValue)
	{
		$this->setAttribute('valueunchecked', $sValue);
	}

	/**
	 * get value when checkbox is unchecked
	 * 
	 * @return string
	 */
	public function getValueUnchecked()
	{
		return $this->getAttribute('valueunchecked');
	}	

	/**
	 * set type of checkbox TYPE_CHECKBOX, TYPE_RADIO or TYPE_SWITCH
	 */
	public function setType($sType = DRInputCheckbox::TYPE_CHECKBOX)
	{
		$this->setAttribute('type', $sType);
	}

	/**
	 * get type of checkbox TYPE_CHECKBOX, TYPE_RADIO or TYPE_SWITCH
	 */
	public function getType()
	{
		return $this->getAttribute('type');
	}
	

	
}

?>
