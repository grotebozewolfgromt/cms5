<?php
namespace dr\classes\dom\validator;




use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * alleen alfanumerieke karaters (0-9) en a-z
 */
class Onlyalphanumeric extends ValidatorAbstract
{

    /**
     * @param boolean $bIgnoreEmpty ignore validation when email field is empty
     */
    public function __construct()
    {
		$sErrorMessage = '';

		$sErrorMessage = transg('validator_onlyalphanumeric_error_notalphanumeric', 'This value is not alphanumeric');

		parent::__construct($sErrorMessage);
    }

	public function isValid($mFormInput)
	{
		return parent::isValidRegex("/^[0-9aA-zZ]+$/", $mFormInput);
	}

	public function filterValue($mFormInput)
	{		
		return filterBadCharsWhiteList($mFormInput, "0123456789abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
	}
}
?>