<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * waarde mag maximale lengte hebben
 */
class Maximumlength extends ValidatorAbstract
{
	private $iMaxLength = 0;
	private $bIgnoreEmpty = false;

	public function __construct($iMaxLength, $bIgnoreEmpty = false)
	{
		$sErrorMessage = '';

		$this->bIgnoreEmpty = $bIgnoreEmpty;
		$this->setMaximumLength($iMaxLength);
		$sErrorMessage = transg('validator_maxlength_error_maxlengthexceeded', 'The maximumlength [length] of field is exceeded', 'length', $iMaxLength);
		
		parent::__construct($sErrorMessage);
	}

	public function setMaximumLength($iMaxLength = 0)
	{
		$this->iMaxLength = $iMaxLength;
	}

	public function isValid($mFormInput)
	{
        if ($this->bIgnoreEmpty)
        {
            if ($mFormInput == '')
                return true;
        }

		return (strlen($mFormInput) <= $this->iMaxLength);
	}

	public function filterValue($mFormInput)
	{
        if ($this->bIgnoreEmpty)
        {
            if ($mFormInput == '')
                return '';
        }

		return substr($mFormInput, 0, $this->iMaxLength);
	}
}
?>