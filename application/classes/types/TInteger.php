<?php
namespace dr\classes\types;

use dr\classes\patterns\TObject;
/**
 * Description of TInteger
 * 
 * TInteger helpt je om integers locale aware weer te geven
 * 
 * 20 juli 2012: TInteger class created
  * 20 juli 2012: TInteger extends TObject
* 14 jun 2014: extends TObject removed
 * 
 * @author drenirie
 */
class TInteger
{
    private $iInternalValue = 0;
    
    /**
     *
     * @param int $iValue 
     */
    public function __construct($iValue = 0) 
    {
        if (is_int($iValue))
            $this->iInternalValue = $iValue;
    }
    
    /**
     * set integer value
     * 
     * this function accepts:
     * strings: it tries to convert it to int
     * floats: but is rounds them
     * integers: of course ... like, d��h
     * 
     * @param mixed $mValue 
     */
    public function setValue($mValue)
    {       
        if (is_string($mValue))
            $this->setAsString($mValue);
        if (is_float($mValue))
            $this->iInternalValue = (int)round($mValue);
        if (is_int($mValue))
            $this->iInternalValue = $mValue;
    }
    
    /**
     * get integer value
     * 
     * @return int 
     */
    public function getValue()
    {
        return $this->iInternalValue;
    }
    
    /**
     * get the value as a locale formatted string 
     * @global Application $objApplication
     * @return string 
     */
    public function getAsString()
    {
        global $objApplication;
        $sResultString = '';
       
        if ($objApplication)
        {
            if ($objApplication->getLocale())
            {
                $sThousandSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_THOUSAND);
                //$sDecimalSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_DECIMAL); --> overbodig, omdat het om integers gaat

                return number_format($this->getValue(), 0, ',', $sThousandSeparator); //de decimaal-scheider is niet belangrijk ivm integers
            }
        }
        
        return $sResultString;
    }
    
    /**
     * set a value as a string
     * 
     * @global Application $objApplication
     * @param string $sValue 
     */
    public function setAsString($sValue)
    {
        global $objApplication;
       
        if ($objApplication)
        {
            if ($objApplication->getLocale())
            {
                $sThousandSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_THOUSAND);
                
                $sCleanString = str_replace($sThousandSeparator, '', $sValue); //de 'duizend'-scheidings karakters filteren
                             
                try
                {
                    $this->setValue((int)$sCleanString);
                }
                catch (Exception $objException)
                {
                    $this->setValue(0);
                }
                
            }
        }
    }
}

?>
