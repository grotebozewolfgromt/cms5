<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * postcode
 */
class Dutchzipcode extends ValidatorAbstract
{
	private $bIgnoreEmpty = false;

    /**
     * @param boolean $bIgnoreEmpty ignore validation when email field is empty
     */
    public function __construct($bIgnoreEmpty = false)
    {
		$sErrorMessage = '';

		$this->bIgnoreEmpty = $bIgnoreEmpty;
		$sErrorMessage = transg('validator_dutchzipcode_error_notvalidzipcode', 'This is not a valid Dutch zip code');

		parent::__construct($sErrorMessage);
    }


	public function isValid($mFormInput)
	{
		//can be empty
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput  == '')
				return true;
		}
		
		return ($this->filterValue($mFormInput) == $mFormInput);
	}

	/**
	 * format: 1234 AB
	 */
	public function filterValue($mFormInput)
	{
		$sDigits = '1234';
		$sChars = 'AB';
		$iStartPosChars = 0;

		//can be empty --> cancel further execution
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput  == '')
				return '';
		}

		//restrict length to 7 max
		if (strlen($mFormInput) > 7)
		{
			$mFormInput = substr($mFormInput, 0, 7);
		}

		//uppercase
		$mFormInput = strtoupper($mFormInput);

		//digits
		$sDigits = substr($mFormInput, 0, 4);
		$sDigits = filterBadCharsWhiteList($sDigits, '0123456789');
		$sDigits = str_pad($sDigits, 4, '0');


		//alphabetical chars
		if (strlen($mFormInput) > 4)
		{
			if ($mFormInput[4] == ' ')
				$iStartPosChars = 5;
			else
				$iStartPosChars = 4;
			$sChars = substr($mFormInput, $iStartPosChars, 2);
			$sChars = filterBadCharsWhiteList($sChars, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ');
		}
		$sChars = str_pad($sChars, 2, 'A');


		return $sDigits.' '.$sChars; 
	}
}
?>