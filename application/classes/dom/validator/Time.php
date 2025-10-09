<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');

/**
 * valid date
 */
class Time extends ValidatorAbstract
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

                $sErrorMessage = transg('validator_time_error_notvalidtime', 'This is not a valid time');                
                
		parent::__construct($sErrorMessage);
	}
        
	public function isValid($mFormInput)
	{
                if ($this->bAllowEmpty)
                        if ($mFormInput == '')
                                return true;
            
                if ($mFormInput == '')
                        return false;
                        
		return isValidTime($mFormInput, $this->sPHPDateFormat);
	}

	public function filterValue($mFormInput)
	{
                //by filtering hopefully we get a valid date
                $mFormInput = filterBadCharsWhiteList($mFormInput, '0123456789 amp:');// american notation has am/pm and a space and colon (:). Human notation has only colon (:)
                
                if (isValidTime($mFormInput, $this->sPHPDateFormat))
                {
                       //it could be that a date is recognized, but it doesn't have to be correct, like 74-12-2021
                       $objTempDate = new TDateTime(0);
                       $objTempDate->setTimeAsString($mFormInput, $this->sPHPDateFormat);
                       return $objTempDate->getTimeAsString($this->sPHPDateFormat);                        
                        // return $mFormInput;
                }
                else
                {
                        $objTempDate = new TDateTime(time());
                        return $objTempDate->getTimeAsString($this->sPHPDateFormat);
                }

                return $mFormInput;
	}

}

?>