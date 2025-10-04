<?php
namespace dr\classes\types;

use dr\classes\patterns\TObject;

/**
 * Description of TFloat
 * helper class for floats, for example to compare floats.
 * 
 * This class represents a float, double or real value.
 * 
 * The precision in this class is ONLY used for comparing floats
 *
 ***************************************************
 * WARNING!!!    WARNING!!!!    WARNING!!!!
 ***************************************************
 * TFloat is a float HELPER class for native PHP float!
 * TFloat HAS NOTHING TO DO WITH TDecimal !!!!!!!!!!!!!!!!
 * TDecimal represents a decimal number without problems such as
 * rounding errors in bit representation and therefore false comparisons.
 ***************************************************
 * 
 * 
 * 20 juli 2012: TFloat created
 * 20 juli 2012: TFloat heeft inhoud gekregen
 * 20 juli 2012: TFloat erft nu over van TFloatAbstract
 * 04 mei 2014: TFloatAbstract rename TFloat, TFloatAbstract verwijderd
 * 14 jun 2014: extends TObject removed
 *  
 *
 * @author drenirie
 */
class TFloat
{
    private $fInternalValue = 0.0;
    private $iDecimalPrecision = 2; //default
    
    
    /**
     *
     * @param int $fValue 
     */
    public function __construct($fValue = 0.0, $iDecimalPrecision = 2) 
    {
        $this->fInternalValue = $fValue;
        $this->iDecimalPrecision = $iDecimalPrecision;
    }    
    
    /**
     * get the precision in number-of-characters after the decimal separator
     * 
     * @return type 
     */
    public function getDecimalPrecision()
    {
        return $this->iDecimalPrecision;
    }
    
    /**
     * get the precision in number-of-characters after the decimal separator
     * 
     * @param type $iPrecision 
     */
    public function setDecimalPrecision($iPrecision)
    {
        $this->iDecimalPrecision = $iPrecision;
    }
    
    /**
     * set float value
     * 
     * this function accepts:
     * floats: of course ... like, d��h
     * integers: converts them to floats
     * 
     * @param mixed $mValue 
     */
    public function setValue($mValue)
    {       
        if (is_float($mValue))
            $this->fInternalValue = $mValue;
        if (is_int($mValue))
            $this->fInternalValue = (float)$mValue;
    }
    
    /**
     * mathematical: adds a float value to the internal float
     * 
     * @param type $mValue 
     */
    public function addValue($mValue)
    {
        if (is_float($mValue))
            $this->fInternalValue += $mValue;
        if (is_int($mValue))
            $this->fInternalValue += (float)$mValue;        
    }
    
    /**
     * mathematical: adds a TFloat object to this object
     * 
     * @param TFloat $objAdd 
     */
    public function add(TFloat $objAdd)
    {
        $this->fInternalValue += $objAdd->getValue();
    }


    /**
     * mathematical substracts (aftrekken) a float value from the internal float
     * 
     * @param mixed $mValue (int or float) 
     */
    public function subtractValue($mValue)
    {
        if (is_float($mValue))
            $this->fInternalValue -= $mValue;
        if (is_int($mValue))
            $this->fInternalValue -= (float)$mValue;        
    }    
    
    /**
     * mathematical substracts (aftrekken) a TFloatAbstract from this object
     * 
     * @param TFloatAbstract $objSub 
     */
    public function subtract(TFloatAbstract $objSub)
    {
        $this->fInternalValue -= $objSub->getValue();
    }
    
    /**
     * get integer value
     * 
     * @return float 
     */
    public function getValue()
    {
        return $this->fInternalValue;
    }    
    
    /**
     * tests is two floats are equal
     * 
     * @param TFloat $objEqualTo
     * @return bool (true = equal, false otherwise)
     */
    public function isEqual(TFloatAbstract $objEqualTo)
    {
        $iResult = compareFloat($this->getValue(), $objEqualTo->getValue(), $this->getDecimalPrecision());
        return ($iResult == 0);
    }
    
    /**
     * tests if this float is greater than $objOther
     * @param TFloat $objOther
     * @return bool 
     */
    public function isGreaterThan(TFloatAbstract $objOther)
    {
        $iResult = compareFloat($this->getValue(), $objOther->getValue(), $this->getDecimalPrecision());
        return ($iResult == 1);        
    }
    
    /**
     * tests if this float is less than $objOther
     * @param TFloat $objOther
     * @return bool 
     */
    public function isLessThan(TFloatAbstract $objOther)
    {
        $iResult = compareFloat($this->getValue(), $objOther->getValue(), $this->getDecimalPrecision());
        return ($iResult == -1);        
    }    
      
    /**
     * get the value as a locale formatted string
     * 
     * @param int $iNoOfDecimals
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
                $sDecimalSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_DECIMAL); 

                return number_format($this->getValue(), $this->getDecimalPrecision(), $sDecimalSeparator, $sThousandSeparator); 
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
                $sDecimalSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_DECIMAL);                 
                
                $sCleanString = str_replace($sThousandSeparator, '', $sValue); //de 'duizend'-scheidings-karakters filteren
                $sCleanString = str_replace($sDecimalSeparator, '.', $sCleanString); //decimaal karakter vervangen voor engelse punt(.)
                         
                try
                {
                    $this->setValue((float)$sCleanString);
                }
                catch (Exception $objException)
                {
                    $this->setValue(0.0);
                }
                
            }
        }
    }    
    
    
    
    
}

?>
