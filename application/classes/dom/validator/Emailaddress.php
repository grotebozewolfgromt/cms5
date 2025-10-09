<?php

namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * valid emailaddress
 * 
 * it can ignore when the field is empty
 * 
 * 11jan 2020- Emailaddress validator - can ignore an empty field
 * 
 */
class Emailaddress extends ValidatorAbstract
{
    private $bIgnoreEmpty = false;
    private $bCheckDNS = false;
    private $bCheckLatinChars = false;
    
    /**
     * ignore validation when email field is empty
     * 
     * @param string $sErrorMessage
     * @param boolean $bIgnoreEmpty
     */
    public function __construct($bIgnoreEmpty = false, $bCheckDNS= false, $bCheckLatinChars =false)
    {
        $sErrorMessage = '';

        $this->setIgnoreEmpty($bIgnoreEmpty);
        $this->setCheckDNSRecord($bCheckDNS);
        $this->setCheckLatinChars($bCheckLatinChars);
		$sErrorMessage = transg('validator_emailaddress_error_notvalidemailaddress', 'This is not a valid email address');

        parent::__construct($sErrorMessage);
    }

    public function isValid($mFormInput)
    {
        if ($this->bIgnoreEmpty)
        {
            if ($mFormInput == '')
                return true;
        }

        return isValidEmail($mFormInput, $this->bCheckDNS, $this->bCheckLatinChars);
    }

    public function filterValue($mFormInput)
    {
        if ($this->bIgnoreEmpty)
        {
            if ($mFormInput == '')
                return '';
        }

        return filterBadCharsWhiteList($mFormInput, "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXUZ_-@.");
    }
    
    public function setIgnoreEmpty($bIgnore)
    {
        $this->bIgnoreEmpty = $bIgnore;
    }

    public function setCheckDNSRecord($bCheck)
    {
        $this->bCheckDNS = $bCheck;
    }

    public function setCheckLatinChars($bCheck)
    {
        $this->bCheckLatinChars = $bCheck;
    }

}
?>