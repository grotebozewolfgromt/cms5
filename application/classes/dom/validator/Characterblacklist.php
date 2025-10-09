<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * alleen bepaalde karakaters zijn toegestaan
 */
class Characterblacklist extends ValidatorAbstract
{
	private $sCharsNotAllowed;

	public function __construct($sCharsNotAllowed)
	{
		$sErrorMessage = '';

		$this->setCharactersNotAllowed($sCharsNotAllowed);
		$sErrorMessage = transg('validator_characterblacklist_error_charactersnotallowed', 'The following characters are not allowed: [badchars]', 'badchars', $sCharsNotAllowed);

		parent::__construct($sErrorMessage);
	}

	public function setCharactersNotAllowed($sCharsNotAllowed)
	{
		$this->sCharsNotAllowed = $sCharsNotAllowed;
	}

	public function isValid($mFormInput)
	{
		for ($iTeller = 0; $iTeller < strlen($this->sCharsNotAllowed); $iTeller++)
		{
			$cChar = $this->sCharsNotAllowed[$iTeller];

			if (strpos($mFormInput, $cChar))
				return false;
		}

		return true;
	}

	public function filterValue($mFormInput)
	{
		return filterBadCharsBlackList($mFormInput, $this->sCharsNotAllowed);
	}
}
?>