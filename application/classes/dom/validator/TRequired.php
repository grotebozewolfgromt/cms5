<?php
namespace dr\classes\dom\validator;

/**
 * doet hetzelfde als notempty
 */
class TRequired extends ValidatorAbstract
{  

    /**
     * @param boolean $bIgnoreEmpty
     */
    public function __construct()
    {
		$sErrorMessage = '';

		$sErrorMessage = transg('validator_required_error_fieldrequired', 'This is a required field');

		parent::__construct($sErrorMessage);
    }

	public function isValid($mFormInput)
	{
		return (strlen($mFormInput) > 0);
	}

	public function filterValue($mFormInput)
	{
		return $mFormInput;
	}
}

?>