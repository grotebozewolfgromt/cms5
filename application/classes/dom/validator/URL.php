<?php
namespace dr\classes\dom\validator;

use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * is a valid url
 */
class URL extends ValidatorAbstract
{
	private $bIgnoreEmpty = false;


    /**
     * @param boolean $bIgnoreEmpty ignore validation when email field is empty
     */	
	public function __construct($bIgnoreEmpty = false)
	{
		$sErrorMessage = '';

		$this->setIgnoreEmpty($bIgnoreEmpty);
		$sErrorMessage = transg('validator_url_error_notvalidrul', 'This is not a valid URL');

		parent::__construct($sErrorMessage);
	}	


    public function setIgnoreEmpty($bIgnore)
    {
        $this->bIgnoreEmpty = $bIgnore;
    }
		
	public function isValid($mFormInput)
	{
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput == '')
				return true;
		}

		return (isValidURL($mFormInput));
	}

	public function filterValue($mFormInput)
	{
		return sanitizeURL($mFormInput);
	}
}

?>