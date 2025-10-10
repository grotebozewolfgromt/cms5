<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * form validator for a regular expression
 */
class TRegex extends ValidatorAbstract
{
	private $sRegEx = '';

	public function __construct($sRegEx)
	{
		$sErrorMessage = '';

		$this->setPattern($sRegEx);
		$sErrorMessage = transg('validator_regex_error_valuenotvalid', 'This is not a valid value');

		parent::__construct($sErrorMessage);
	}

	public function setPattern($sRegEx)
	{
		$this->sRegEx = $sRegEx;
	}

	public function isValid($mFormInput)
	{
		parent::isValidRegex($this->sRegEx, $mFormInput);
	}

	public function filterValue($mFormInput)
	{
		return preg_replace($this->sRegEx, '', $mFormInput);
	}
}

?>