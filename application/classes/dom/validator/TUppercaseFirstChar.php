<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * first character is converted to uppercase
 */
class TUppercaseFirstChar extends ValidatorAbstract
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
		$iInputLen = 0;
		$sFirstChar = '';
		$sRestChar = '';

		$iInputLen = strlen($mFormInput);
		if ($iInputLen > 0)
		{
			$sFirstChar = substr($mFormInput, 0, 1);
			$sFirstChar = strtoupper($sFirstChar);
			if ($iInputLen > 1)
				$sRestChar = substr($mFormInput, 1, $iInputLen-1);
		}

		return $sFirstChar.$sRestChar;
	}
}
?>