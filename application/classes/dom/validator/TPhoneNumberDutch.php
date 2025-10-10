<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\dom\tag\I;

/**
 * nederlands telefoonnummer
 */
class TPhoneNumberDutch extends ValidatorAbstract
{
    private $bIgnoreEmpty = false;

    /**
     * constructor
     */
    public function __construct($bIgnoreEmpty = false)
	{   
		$sErrorMessage = '';

        $this->bIgnoreEmpty = $bIgnoreEmpty;		
		$sErrorMessage = transg('validator_dutchphonenumber_error_notvalidphonenumber', 'This is not a valid Dutch phone number');
                
	    parent::__construct($sErrorMessage);
	}

	public function isValid($mFormInput)
	{
		if ($this->bIgnoreEmpty)
			if ($mFormInput == '')
				return true;

		$sCountDigits = filterBadCharsWhiteList($mFormInput, "0123456789");
		
		if ((strlen($sCountDigits) == 10) || (strlen($sCountDigits) == 12)) //10 digits or 12 when counting countrycode +31
		{
			return parent::isValidRegex("/^(\d{3}-?\d{7}|\d{4}-?\d{6})$/", $mFormInput);
		}
		else
			return false;
	}

	public function filterValue($mFormInput)
	{	
		return formatPhoneNumberDutch($mFormInput, $this->bIgnoreEmpty);
	}
}
?>