<?php
namespace dr\classes\dom\validator;




use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;


/**
 * is a strong password
 * 
 * a strong password has:
 * -at least 1 uppercase letter
 * -at least 1 lowercase letter
 * -at least 1 digit
 * -at least one special character
 * -min 8 characters long, max 255
 * 
 */
class TStrongPassword extends ValidatorAbstract
{	
        private $bIgnoreEmpty = false;

        const LOWERCASELETTERS = 'abcdefghijklmnopqrstuvwxyz';
        const UPPERCASELETTERS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        const DIGITS = '0123456789';
        const SPECIALCHARS = ':;_+=.,!@#$%^&*(){}?<>';        

        /**
         * 
         * @param bool $bIgnoreEmpty
         */        
    	public function __construct($bIgnoreEmpty = false)
	{           
                $sErrorMessage = '';

                $this->bIgnoreEmpty = $bIgnoreEmpty;
                $sErrorMessage = transg('validator_strongpassword_error_notbypasswordrules', 'This is not a strong password. Here are the rules: [rules]', 'rules', implode('<br>\n', $this->getRules()));

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

                //==== check on lengths
                if ($mFormInput == '')
                {
                        $this->setErrorMessage('validator_strongpassword_error_inputempty', 'Input is empty');
                        return false;
                }

                if (strlen($mFormInput) <= 8)
                {
                        $this->setErrorMessage('validator_strongpassword_error_lessthan8chars', 'Input has less than 8 characters');
                        return false;
                }

                if (strlen($mFormInput) > 255)
                {
                        $this->setErrorMessage('validator_strongpassword_error_morethan255chars', 'Input has more than 255 characters');
                        return false;
                }

                //==== characters that are not allowed
                //filter the password and compare with the original string
                $sPasswordFiltered = filterBadCharsWhiteListLiteral($mFormInput, 
                                                                TStrongPassword::LOWERCASELETTERS.
                                                                TStrongPassword::UPPERCASELETTERS.
                                                                TStrongPassword::DIGITS.
                                                                TStrongPassword::SPECIALCHARS);
                if ($sPasswordFiltered != $mFormInput)    
                {
                        $this->setErrorMessage(transg('validator_strongpassword_error_notbypasswordrules', 'This is not a strong password. Here are the rules: [rules]', 'rules', implode('<br>\n', $this->getRules())));
                        return false;     
                }

                //==== at least one lowercase character
                if (!$this->doCharsExistInPassword($mFormInput, TStrongPassword::LOWERCASELETTERS))
                {
                        $this->setErrorMessage('validator_strongpassword_error_needslowercasecharacter', 'Input needs at least 1 lowercase character');
                        return false;
                }
                
                //==== at least one uppercase character
                if (!$this->doCharsExistInPassword($mFormInput, TStrongPassword::UPPERCASELETTERS))
                {
                        $this->setErrorMessage('validator_strongpassword_error_needsuppercasecharacter', 'Input needs at least 1 uppercase character');
                        return false;
                }
                        
                //==== at least one digit
                if (!$this->doCharsExistInPassword($mFormInput, TStrongPassword::DIGITS))
                {
                        $this->setErrorMessage('validator_strongpassword_error_needs1digit', 'Input needs at least 1 digit');
                        return false;
                }

                //==== at least one special character
                if (!$this->doCharsExistInPassword($mFormInput, TStrongPassword::SPECIALCHARS))
                {
                        $this->setErrorMessage('validator_strongpassword_error_needs1specialchar', 'Input needs at least 1 special character');
                        return false;                        
                }

                return true;
	}

        /**
         * find occurence of $sCharacters in $sPassword
         * 
         * @return int
         */
        private function doCharsExistInPassword($sPassword, $sCharacters)
        {
                $iLengthChars= 0;
                $iLengthChars = strlen($sCharacters);
                $arrPieces = array();

                for ($iIndexChars = 0; $iIndexChars < $iLengthChars; $iIndexChars++)
                {
                        //explode is faster than strpos
                        $arrPieces = explode($sCharacters[$iIndexChars], $sPassword);
                        
                        if (count($arrPieces) > 1) //if not found, it is 1, else its > 1
                                return true;
                }

                return false;
        }

	public function filterValue($mFormInput)
	{
		//can be empty --> cancel further execution
		if ($this->bIgnoreEmpty)
		{
			if ($mFormInput  == '')
				return '';
		}

                return $mFormInput;
	}

        /**
         * explains the rules of a strong password.
         * returns the rules as array (every rule is 1 element in the array)
         * the text in the array is already translated with transg()
         *
         * @return array
         */
        static public function getRules()
        {
                //space out the special characters
                $sSpecialCharsWithSpaces = '';
                $iStrLen = 0;
                $iStrLen = strlen(TStrongPassword::SPECIALCHARS);
                $sSpecialChars = TStrongPassword::SPECIALCHARS;
                for ($iCounter = 0; $iCounter < $iStrLen; $iCounter++)
                {
                        if ($iCounter > 0) //skip the first space
                                $sSpecialCharsWithSpaces.= ' ';
                        $sSpecialCharsWithSpaces.= $sSpecialChars[$iCounter];
                }

                //the rules
                $arrRules = array();
                $arrRules[] = transg('textbox_validator_strongpassword_rule_1uppercaseletter', 'At least 1 upper case letter (A-Z).');
                $arrRules[] = transg('textbox_validator_strongpassword_rule_1lowercaseletter', 'At least 1 lower case letter (a-z).');
                $arrRules[] = transg('textbox_validator_strongpassword_rule_1upperdigit', 'At least 1 digit (0-9).');
                $arrRules[] = transg('textbox_validator_strongpassword_rule_1specialcharacter', 'At least 1 of these special characters: [specialchar]', 'specialchar', $sSpecialCharsWithSpaces);
                $arrRules[] = transg('textbox_validator_strongpassword_rule_between8and255chars', 'Needs to be at least 8 characters long.');

                return $arrRules;
        }

}

?>