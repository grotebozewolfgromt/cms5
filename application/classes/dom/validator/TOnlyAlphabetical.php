<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * alfabetische karakters
 */
class TOnlyAlphabetical extends ValidatorAbstract
{
    /**
     * @param boolean $bIgnoreEmpty ignore validation when email field is empty
     */
    public function __construct()
    {
		$sErrorMessage = '';

		$sErrorMessage = transg('validator_onlyalphabetical_error_notonlyalphabetical', 'Only alphabetical characters allowed');

		parent::__construct($sErrorMessage);
    }

	public function isValid($mFormInput)
	{
		return parent::isValidRegex("/^[aA-zZ]+$/", $mFormInput);
	}

	public function filterValue($mFormInput)
	{
		return filterBadCharsWhiteList($mFormInput, "abcdefghijklnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ");
	}
}

?>