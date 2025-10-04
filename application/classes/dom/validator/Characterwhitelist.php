<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * alleen bepaalde karakaters zijn toegestaan
 */
class Characterwhitelist extends ValidatorAbstract
{
	private $sCharsAllowed;

	public function __construct($sCharWhitelist)
	{
		$sErrorMessage = '';

		$this->setCharactersAllowed($sCharWhitelist);
		$sErrorMessage = transg('validator_characterwhitelist_error_dirtyinput', 'Only the following characters are allowed [chars] characters', 'chars', $sCharWhitelist);

		parent::__construct($sErrorMessage);
	}

	public function setCharactersAllowed($sCharsAllowed)
	{
		$this->sCharsAllowed = $sCharsAllowed;
	}


	public function isValid($mFormInput)
	{
		return (filterBadCharsWhiteListLiteral($mFormInput, $this->sCharsAllowed) == $mFormInput);
	}

	public function filterValue($mFormInput)
	{
		return filterBadCharsWhiteListLiteral($mFormInput, $this->sCharsAllowed);
	}
}
?>