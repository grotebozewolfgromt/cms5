<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * waarde moet minimale lengte hebben
 */
class MinimumLength extends ValidatorAbstract
{
	private $iMinLength = 0;
	private $bIgnoreEmpty = false;	

	public function __construct($iMinLength, $bIgnoreEmpty = false)
	{
		$sErrorMessage = '';

		$this->bIgnoreEmpty = $bIgnoreEmpty;
		$this->setMinimumLength($iMinLength);
		$sErrorMessage = transg('validator_minlength_error_minlengthatleast', 'Input needs to be at least [length] characters', 'length', $iMinLength);

		parent::__construct($sErrorMessage);
	}

	public function setMinimumLength($iMinLength = 0)
	{
		$this->iMinLength = $iMinLength;
	}

	public function isValid($mFormInput)
	{
		//can be empty --> cancel further execution
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput  == '')
				return true;
		}

		return (strlen($mFormInput) >= $this->iMinLength);
	}

	public function filterValue($mFormInput)
	{
		//can be empty --> cancel further execution
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput  == '')
				return '';
		}

		return $mFormInput;
	}
}
?>