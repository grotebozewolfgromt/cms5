<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * all characters are converted to uppercase
 */
class TUppercase extends ValidatorAbstract
{

	public function __construct()
	{
		$sErrorMessage = '';

		parent::__construct($sErrorMessage);
	}

	public function isValid($mFormInput)
	{
		return true;
	}

	public function filterValue($mFormInput)
	{
		return strtoupper($mFormInput);
	}
}
?>