<?php
namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * IPv4 or IPv6 IP address
 */
class TIPAddress extends ValidatorAbstract
{
	private $bIgnoreEmpty = false;

    /**
     * ignore validation when email field is empty
     * 
     * @param string $sErrorMessage
     * @param boolean $bIgnoreEmpty
     */
    public function __construct($bIgnoreEmpty = false)
    {
		$sErrorMessage = '';

		$this->bIgnoreEmpty = $bIgnoreEmpty;
		$sErrorMessage = transg('validator_ipaddress_error_notvalidipaddress', 'This is not a valid IP address');

		parent::__construct($sErrorMessage);
    }

	public function isValid($mFormInput)
	{
		if ($this->bIgnoreEmpty)
			if ($mFormInput == '')
				return true;
			
		//smallest ip address: ::1
		if (strlen($mFormInput) < 3)
			return false; 

		return filter_var($mFormInput, FILTER_VALIDATE_IP);
	}

	public function filterValue($mFormInput)
	{
		if ($this->bIgnoreEmpty)
			if ($mFormInput == '')
				return '';

		return filterBadCharsWhiteList($mFormInput, "0123456789abcdef.:");
	}
}
?>