<?php
namespace dr\classes\dom\validator;



use dr\classes\dom\tag\form\FormInputAbstract;
use dr\classes\locale\TLocalisation;
use dr\classes\types\TDateTime;

include_once(APP_PATH_LIBRARIES.DIRECTORY_SEPARATOR.'lib_date.php');

/**
 * date not allowed before ...
 * 
 * checks also if the date is a valid date
 * 
 */
class DateMin extends ValidatorAbstract
{
    private $objDateMin = null; //default
    private $sPHPDateFormat = ''; //default
    private $bAllowEmpty = false;
    
    /**
     * constructor
     * 
     * @param TDateTime $objDateNotAllowedBefore
     * @param string $sPHPDateFormat
     * @param string $bAllowEmpty
     */
    public function __construct($objDateNotAllowedBefore, $sPHPDateFormat = '', $bAllowEmpty = false)
	{   
		$sErrorMessage = '';

        $this->sPHPDateFormat = $sPHPDateFormat;
        $this->objDateMin = $objDateNotAllowedBefore;
        $this->bAllowEmpty = $bAllowEmpty;
		$sErrorMessage = transg('validator_datemin_error_datenotallowedbefore', 'Date needs to be after [date]', 'date',  $objDateNotAllowedBefore->getDateAsString($sPHPDateFormat));
                
	    parent::__construct($sErrorMessage);
	}
        
	public function isValid($mFormInput)
	{
        if ($this->bAllowEmpty)
            if (($mFormInput === '') || ($mFormInput === null))
                return true;
                
        if (($mFormInput === '') || ($mFormInput === null))
                return false;
        
        if ($this->objDateMin)
        {
            if ($this->objDateMin instanceof TDateTime)
            {
                if (isValidDate($mFormInput, $this->sPHPDateFormat))
                {
                    $bResult = false;
                    $objDateFromInputField = new TDateTime();
                    $objDateFromInputField->setDateAsString($mFormInput, $this->sPHPDateFormat);                            
                    $bResult = $this->objDateMin->isEarlier($objDateFromInputField);
        
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
		    return $this->objDateMin->getDateAsString($this->sPHPDateFormat);
	}

}

?>