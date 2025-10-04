<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');

/**
 * date not allowed after ...
 * 
 * checks also if the date is a valid date
 * 
 */
class DateMax extends ValidatorAbstract
{
    private $objDateMax = null; //default
    private $sPHPDateFormat = ''; //default
    private $bAllowEmpty = false;
        
    /**
     * @param TDateTime $objDateNotAllowedAfter
     * @param string $$sPHPDateFormat
     * @param string $bAllowEmpty
     */
    public function __construct($objDateNotAllowedAfter, $sPHPDateFormat = '', $bAllowEmpty = false)
	{   
        $sErrorMessage = '';

        $this->sPHPDateFormat = $sPHPDateFormat;
        $this->objDateMax = $objDateNotAllowedAfter;
        $this->bAllowEmpty = $bAllowEmpty;
		$sErrorMessage = transg('validator_datemax_error_datenotallowedafter', 'Date needs to be before [date]', 'date', $objDateNotAllowedAfter->getDateAsString($sPHPDateFormat));
            
        parent::__construct($sErrorMessage);
	}
        

	public function isValid($mFormInput)
	{
        if ($this->bAllowEmpty)
            if (($mFormInput === '') || ($mFormInput === null))
                return true;
                
        if (($mFormInput === '') || ($mFormInput === null))
                return false;
        
        if ($this->objDateMax)
        {
            if ($this->objDateMax instanceof TDateTime)
            {
                if (isValidDate($mFormInput, $this->sPHPDateFormat))
                {
                    $bResult = false;
                    $objDateFromInputField = new TDateTime();
                    $objDateFromInputField->setDateAsString($mFormInput, $this->sPHPDateFormat);                            
                    $bResult = $this->objDateMax->isLater($objDateFromInputField);
        
                    unset($objDateFromInputField);
                    
                    return $bResult;
                }
                else
                    return false;
                        
            }
            return false;
        }
        else
            return false;
            
                
		return isValidDate($mFormInput, $this->sPHPDateFormat);
	}

	public function filterValue($mFormInput)
	{
        $mFormInput = filterBadCharsWhiteList($mFormInput, '0123456789 amp/-');// american notation has am/pm and a space and slash (/). Human notation has -

        if (isValidDate($mFormInput, $this->sPHPDateFormat))
            return $mFormInput;
        else
		    return $this->objDateMax->getDateAsString($this->sPHPDateFormat);
	}

}

?>