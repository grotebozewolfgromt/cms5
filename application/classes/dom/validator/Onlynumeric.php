<?php

namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * alleen numerieke karaters (0-9)
 */
class Onlynumeric extends ValidatorAbstract
{
    /**
     * @param boolean $bIgnoreEmpty ignore validation when email field is empty
     */
    public function __construct()
    {
		$sErrorMessage = '';

		$sErrorMessage = transg('validator_onlynumeric_error_notnumeric', 'This value is not numeric');

		parent::__construct($sErrorMessage);
    }


	public function isValid($mFormInput)
	{
		return parent::isValidRegex("/^[0-9]+$/", $mFormInput);
	}

	public function filterValue($mFormInput)
	{
		return filterBadCharsWhiteList($mFormInput, "0123456789");
	}
}


?>