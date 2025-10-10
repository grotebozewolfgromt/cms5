<?php
namespace dr\classes\dom\validator;




use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * if 2 fields have to be the same value
 * (for example password or email fields)
 * 
 *  1 apr 2016: characterwhitelist created
 */
class TRepeatFieldValue extends ValidatorAbstract
{
	private $objOtherField = null;


	/**
	 * constructor 
	 * 
	 * generally speaking you want to customize the error message to give the user more information about the other field
	 * (we don't know what the other field is, only the id)
	 * 
	 * @param string $sErrorMessage when empty it generates a super generenic
	 */
	public function __construct($sErrorMessage, FormInputAbstract &$objOtherField)
	{

		$this->setOtherField($objOtherField);
		if ($sErrorMessage === '')
			$sErrorMessage = transg('validator_repeatfieldvalue_error_valuesdonotmatch', 'Value of field [field] does not match this value', 'field', $objOtherField->getID());

		parent::__construct($sErrorMessage);
	}

	public function setOtherField(FormInputAbstract &$objOtherField)
	{
		$this->objOtherField = $objOtherField;
	}

	public function isValid($mFormInput)
	{
// vardump($objField->getContentsSubmitted()->getValue(), 'frikantel')		;
// vardumpdie($objField->objOtherField()->getValue(), 'frikantel2')		;
		if (!$this->objOtherField)
			return false;

		return ($mFormInput == $this->objOtherField->getValueSubmitted());
	}

	public function filterValue($mFormInput)
	{
		// return $this->objOtherField->getValueSubmitted();
		return '';
	}
}
?>