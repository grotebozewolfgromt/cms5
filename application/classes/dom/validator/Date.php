<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');

/**
 * valid date
 */
class Date extends ValidatorAbstract
{
        private $sPHPDateFormat = ''; //default
        private $bAllowEmpty = false;
        
        /**
         * @param string $sPHPDateFormat like 'd-m-Y', if '' then TLocalisation default date is assumed
         * @param string $bAllowEmpty
         */
    	public function __construct($sPHPDateFormat = '', $bAllowEmpty = false)
	{   
		$sErrorMessage = '';                

                $this->sPHPDateFormat = $sPHPDateFormat;
                $this->bAllowEmpty = $bAllowEmpty;
                
		$sErrorMessage = transg('validator_date_error_notvaliddate', 'This is not a valid date');

                parent::__construct($sErrorMessage);
	}
        
	public function isValid($mFormInput)
	{
                if ($this->bAllowEmpty)
                        if (($mFormInput === '') || ($mFormInput === null))
                                return true;
                        
                if ($mFormInput == '')
                        return false;
                        
		return isValidDate($mFormInput, $this->sPHPDateFormat);
	}

	public function filterValue($mFormInput)
	{
                //by filtering hopefully we get a valid date
                $mFormInput = filterBadCharsWhiteListLiteral($mFormInput, '0123456789/- ');// american notation has a slash (/) and can have space ( ). Human notation has dash (-)
                
                if (isValidDate($mFormInput, $this->sPHPDateFormat))
                {
                        //it could be that a date is recognized, but it doesn't have to be correct, like 74-12-2021
                        $objTempDate = new TDateTime(0);
                        $objTempDate->setDateAsString($mFormInput, $this->sPHPDateFormat);
                        return $objTempDate->getDateAsString($this->sPHPDateFormat);
                }
                else
                {
                        $objTempDate = new TDateTime(time());
                        return $objTempDate->getDateAsString($this->sPHPDateFormat);
                }

                return $mFormInput;
	}

}

?>