<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * this checkbox needs to be checked
 */
class CheckboxChecked extends ValidatorAbstract
{
	private $sCheckboxValueChecked = ''; //the vaue of the checkbox when checked

	public function __construct($sCheckboxValue)
	{
		$sErrorMessage = '';

		$this->setCheckboxValue($sCheckboxValue);
		$sErrorMessage = transg('validator_checkboxchecked_error_checkboxnotchecked', 'Checkbox needs to be checked in order to continue');		

		parent::__construct($sErrorMessage);
	}

	public function setCheckboxValue($sCheckboxValue)
	{
		$this->sCheckboxValueChecked = $sCheckboxValue;
	}

	public function isValid($mFormInput)
	{
		return ($mFormInput == $this->sCheckboxValueChecked);
	}

	public function filterValue($mFormInput)
	{
		return $mFormInput;
	}
}
?>