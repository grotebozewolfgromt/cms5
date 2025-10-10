<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * postcode
 */
class TPostalCodeDutch extends ValidatorAbstract
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
		return formatPostalCodeDutch($mFormInput, $this->bIgnoreEmpty);
	}
}
?>