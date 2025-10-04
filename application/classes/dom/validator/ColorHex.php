<?php

namespace dr\classes\dom\validator;


use dr\classes\dom\tag\form\FormInputAbstract;

/**
 * valid hex
 * 
 * it can ignore when the field is empty
 * 
 * 3 nov 2023- Color hex validator
 * 
 */
class ColorHex extends ValidatorAbstract
{
    private $bIgnoreEmpty = false;
    private $bTrailingHash = false;
        
    /**
     * ignore validation when email field is empty
     * 
     * @param boolean $bIgnoreEmpty
     * @param boolean $bTrailingHash  to include #-sign before hex value?
     */
    public function __construct($bIgnoreEmpty = false, $bTrailingHash = false)
    {        
		$sErrorMessage = '';

        $this->setTrailingHash($bTrailingHash);
        $this->bIgnoreEmpty = $bIgnoreEmpty;
		$sErrorMessage = transg('validator_colorhox_error_nothexcolor', 'This is not a valid hexadecimal color value');
        
        parent::__construct($sErrorMessage);
    }

    public function isValid($mFormInput)
    {
        $sUserInput = '';
        $sFilteredInput = '';

        //can be empty
        if ($this->bIgnoreEmpty)
        {
            if ($mFormInput  == '')
                return true;
        }

        //compare validated input with user input
        $sFilteredInput = $this->filterValue($mFormInput);
        return ($sFilteredInput == $mFormInput);
    }

    public function filterValue($mFormInput)
    {
        //declaration
        $iAllowedLength = 6;
        $sAllowedChars = 'abcdefABCDEF0123456789';

		if ($this->bIgnoreEmpty)
			if ($mFormInput == '')
				return '';

        //init
        if ($this->bTrailingHash)
        {
            $iAllowedLength = 9; //1 x # + 6 x normal + 2 x transparency
            $sAllowedChars = $sAllowedChars.'#';
        }

        //check length              
        if (strlen($mFormInput) > $iAllowedLength)
              substr($mFormInput, 0, $iAllowedLength);

        //now filter input        
        return filterBadCharsWhiteList($mFormInput, $sAllowedChars);
    }
    
    public function setTrailingHash($bHash)
    {
        $this->bTrailingHash = $bHash;
    }


}
?>