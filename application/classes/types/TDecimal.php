<?php
namespace dr\classes\types;


/**
 * Description of TDecimal
 *
 * When you want speed, you need to use the php native float type, if you need precision or want to store floats in a database you need this class.
 * For setting values and calculations there is a lot of overhead. 
 * This class uses slow string manipulation, type juggling and converting a lot
 * in stead of using processor native calulations.
 * This class is accurate but not very efficient (in terms of speed)
 * 
 * TFloat is a float helper class and has nothing to do TDecimal or TCurrency.
 * 
 * The difference between TDecimal or TCurrency:
 * technically they are the same, but for input and display reasons it is useful to separate them.
 * i.e. an amount is represented by '3', while a currency is represented by '3.00' for readability and input sake
 * 
 * floats zijn onbetrouwbaar in verband met hun bit representatie
 * bijvoorbeeld voor geld is dit een groot probleem, omdat je rare afrondingsfouten krijgt. 
 * afrondingsfout op afrondingsfout is dan een groot probleem
 * Deze klasse heeft een accuraat kommagetal door de precisie op te slaan en de interne waarde is een 64 bits integer
 * 
 * om waardes goed op te kunnen slaan in de database moet je voor mysql het DECIMAL(13,4) type gebruiken (13 cijfers voor en 4 cijfers na de komma)
 * 
 * de beste manier om met deze klasse te werken is door waardes in string te setten en te getten.
 * 
 * GAAP
 * ====
 * If you want to meet Generally Accepted Accounting Principles (GAAP), 
 * you need to have at least FOUR decimal places. 
 * This ensures that rounding errors will not, on average, exceed $0.01 
 * with thousand transactions (because rounding errors tend to even out).
 * 
 * If you want to be compliant with Generally Accepted Accounting Principles (GAAP), 
 * then you should use DECIMAL(13,4) in MySQL.
 * 
 * 3 mei 2014: TDecimal created
 * 4 mei 2014: TDecimal renamed to TDecimal
 * 4 mei 2014: TDecimal omgebouwd naar met string oriented in en uitvoer van de klasse om programmeren te versnellen en bugs te voorkomen
 * 30 may 2025: TDecimal replaces TCurrency, 3 functions moved from TCurrency => TDecimal
 * 30 may 2025: TDecimal replaces bug in getValue() with 0.05, 0.005 and 0.0005 
 * 
 * 
 * @author drenirie
 */
class TDecimal
{
    const DECIMALPRECISIONDEFAULT = 4; //so it can be used by other classes
    
    private $iInternalValue = 0; //integer64 representation of the internal value --> maxvalue: PHP_INT_MAX
    private $iDecimalPrecision = TDecimal::DECIMALPRECISIONDEFAULT; //default 4 cijfers achter de komma
    
    
    /**
     * when you want to input 6 euro, you can use: __construct('6',0) or __construct('6.00',0)
     * when you want to input 6,23 euro, use: __construct ('6.23',2)
     * 
     * @param string $sValue the dot (.) is the decimal separator
     * @param string $iDecimalPrecision sets the internal precision for the whole object (not only the parameter)
     */
    public function __construct($sValue, $iDecimalPrecision = TDecimal::DECIMALPRECISIONDEFAULT) 
    {
        $this->setDecimalPrecision($iDecimalPrecision);
        $this->setValue($sValue);          
    }      
    
    
    /**
     * returns the maximum integer value that this object can hold 
     * based on the decimal precision
     * (digits after decimal symbol are not represented in this number)
     * 
     * @param int $iDecimalPrecision
     * @return int
     */
    public function getMaxValue()
    {
        return (int)round(PHP_INT_MAX / $this->getMultiplyFactor($this->getDecimalPrecision()));
    }
    
   /**
     * get the value as a locale formatted string
     * 
    *  er kunnen afrondingsverschillen in zitten omdat er een float berekend wordt die door number_format geformatteerd wordt
    * 
     * @global Application $objApplication
     * @return string 
     */            
    public function getValueFormatted()
    {
        global $objApplication;
        $sResultString = '';
       
        if ($objApplication)
        {
            if ($objApplication->getLocale())
            {
                $sThousandSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_THOUSAND);
                $sDecimalSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_DECIMAL);
                $fRealValue = $this->getValueAsInt() / $this->getMultiplyFactor($this->getDecimalPrecision());
                
                return number_format($fRealValue, $this->getDecimalPrecision(), $sDecimalSeparator, $sThousandSeparator);                 
            }
        }        
        
        return $sResultString;
    }      
    
    
    /**
     * set a value as a string
     * 
     * it uses the locale format settings to extract the correct value
     * 
     * this function determines what the decimal precision of $sValue and and adjusts it to internal precision
     * 
     * LET OP: afrondingsverschillen als de precisie van $sValue GROTER dan van de interne waarde
     * 
     * je kunt deze functie gebruiken om waardes uit een html editbox te interpreteren
     * deze functie is sql-injection safe, omdat alle fouten karakters gefilterd worden
     * 
     * @global Application $objApplication
     * @param string $sValue 
     */            
    public function setValueFormatted($sValue)
    {
        global $objApplication;
       
        if ($objApplication)
        {
            if ($objApplication->getLocale())
            {                
                $sThousandSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_THOUSAND);
                $sDecimalSeparator = $objApplication->getSettings()->getSetting(TLocalisation::SEPARATOR_DECIMAL); 
                
                $sValue = str_replace($sThousandSeparator, '', $sValue); //de 'duizend'-scheidings-karakters filteren
                $sValue = str_replace($sDecimalSeparator, '.', $sValue); //decimaal karakter vervangen voor engelse punt(.)
                
                $this->setValue($sValue);
            }
        }
    }  
    

    /**
     * returns the internal integer value
     * 
     * @return int
     */
    protected function getValueAsInt()
    {
        return $this->iInternalValue;
    }
    
    /**
     * translating the internal value into a string.
     * This does NOT anticipate on the locale settings, use getValueFormatted() for that.
     * 
     * This function ALWAYS reproduces values in the same (non locale) way, which setValue() can use.
     * In other words: setValue() and getValue() are each others' opposites
     * In other words: $obj->setValue($obj->getValue()) will set the internal value to the same value as it was before.
     * 
     * NOTICE: the stop/dot (.) is ALWAYS the decimal separator
     * 
     * @return string 
     */    
    public function getValue()
    {
        $sStringZonderKomma = '';
        $sVoorDeKomma = '';
        $sNaDeKomma = '';

        $sStringZonderKomma = (string)$this->getValueAsInt();              
        if ((strlen($sStringZonderKomma) - $this->iDecimalPrecision) >= 0) //i.e. length of 500 (=3) with decimal precision 4 gives negative, which makes substr() go beserk
        {
            $sVoorDeKomma = substr($sStringZonderKomma, 0, strlen($sStringZonderKomma) - $this->iDecimalPrecision);
            $sNaDeKomma = substr($sStringZonderKomma, strlen($sStringZonderKomma) - $this->iDecimalPrecision, $this->iDecimalPrecision);
        }
        else
        {
            $sNaDeKomma = str_pad($sStringZonderKomma, $this->iDecimalPrecision,  '0', STR_PAD_LEFT);
        }

        if ($sVoorDeKomma == '')
            $sVoorDeKomma = '0';

        if ($sNaDeKomma == '')
            $sNaDeKomma = '0';            
        
        return $sVoorDeKomma.'.'.$sNaDeKomma;
    }    
    
    /**
     * Set the internal value with a string.
     * This does NOT anticipate on the locale settings, use setValueFormatted() for that.
     * 
     * This function ALWAYS accepts values in the same (non locale) way, which getValue() produces
     * In other words: setValue() and getValue() are each others' opposites
     * In other words: $obj->setValue($obj->getValue()) will set the internal value to the same value as it was before.
     * 
     * NOTICE: the stop/dot (.) is ALWAYS the decimal separator
     * 
     * @param string $sValue
     */
    public function setValue($sValue)
    {       
        $iDecimalPrecision = 0;
        $iStringWithoutDecimalSep = 0;
        $sValue = filterValidFloat($sValue);
        
        //how many digits after separator?
        $iPosDecimalSep = strpos($sValue, '.'); //position of decimal separator

        if ($iPosDecimalSep === false) //decimal seperator not found
        {
            $this->setValueAsInt((int)$sValue, 0);
        }
        else
        {
            $iStringWithoutDecimalSep = (int)str_replace('.', '', $sValue);
            $iDecimalPrecision = strlen($sValue) - ($iPosDecimalSep + 1); //+1 because first index is 0
            $this->setValueAsInt($iStringWithoutDecimalSep, $iDecimalPrecision);
        }       
   }
    
    /**
     * this function is not recommended:
     * POSSIBLE LOSS OF PRECISION due to the bit representation of floats
     * 
     * @return float
     */
    public function getValueAsFloat()
    {
        return $this->getValueAsInt() / $this->getMultiplyFactor($this->getDecimalPrecision());
    }

    
    /**
     * setting the internal value
     * setValueAsInt($iValue = 10000, $iDecimalPrecision = 4) means 1,0000
     * 
     * WARNING!! will be rounded when $iDecimalPrecision is bigger than internal precision
     * 
     * @param int $iValue integer without decimal separator
     * @param int $iDecimalPrecision decimal precision for the function parameter. it doest NOT change the internal decimal precision of this class
     */
    protected function setValueAsInt($iValue, $iDecimalPrecision = TDecimal::DECIMALPRECISIONDEFAULT)
    {        
        if ($this->getDecimalPrecision() >= $iDecimalPrecision) //als de interne precisie groter is of gelijk aan die van de waarde dan vermenigvuldigen met precisieverschil
            $this->iInternalValue = $iValue * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $iDecimalPrecision);        
        else //als de interne precisie kleiner is dan delen met precisieverschil, LET OP!! afronding, er wordt afgerond naar de interne precisie
            $this->iInternalValue = (int)round($iValue / $this->getMultiplyFactorDifference($iDecimalPrecision, $this->getDecimalPrecision()));                    
    }
    
    /**
     * set value by supplying a float
     * afronding gebeurd op $iDecimalPrecision cijfers achter de komma
     * 
     * this function is not recommended:
     * POSSIBLE LOSS OF PRECISION due to the bit representation of floats
     * 
     * @param float $fValue
     */
    public function setValueAsFloat($fValue, $iDecimalPrecision = TDecimal::DECIMALPRECISIONDEFAULT)
    {
        $this->setValueAsInt((int)round($fValue * $this->getMultiplyFactor($iDecimalPrecision)), $iDecimalPrecision);
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
     * the mathematical function add:
     * for adding a value to the internal value
     * i.e. 2 + 3
     * 
     * LET OP!! afronding op precisie van de interne precisie van deze klasse
     * er wordt eerst opgeteld, daarna pas afgerond
     * 
     * @param TDecimal $objAdd
     */
    public function add(TDecimal $objAdd)
    {
        $iValueThis = $this->getValueAsInt();// default
        $iValueAdd = $objAdd->getValueAsInt();// default
        $iHighestPrecision = $this->getDecimalPrecision(); //default

        
        //eerst precisie gelijktrekken op niveau van de hoogste precisie
        if ($this->getDecimalPrecision() >= $objAdd->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueAdd = $objAdd->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objAdd->getDecimalPrecision()); //omrekenen laagste precisie
            $iHighestPrecision = $this->getDecimalPrecision();            
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objAdd->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueAdd = $objAdd->getValueAsInt(); //hoogste precisie
            $iHighestPrecision = $objAdd->getDecimalPrecision();            
        }
        
        //nu pas optellen
        $iSum = $iValueThis + $iValueAdd;
        
        //nu pas setten (en dus afronden)
        $this->setValueAsInt($iSum, $iHighestPrecision); 
         
    }

    /**
     * add an integer number to internal value
     * @param int $iValue
     */
    public function addInt($iValue) 
    {
        if (is_int($iValue))
        {
            $iSum = $this->getValueAsInt() + (int)($this->getMultiplyFactor($this->getDecimalPrecision()) * $iValue);
            $this->setValueAsInt($iSum, $this->getDecimalPrecision());
        }
    }

    /**
     * the mathematical function subtract:
     * for subtracting a value from the internal value
     * i.e. 2 - 3
     * 
     * LET OP!! afronding op precisie van de interne precisie van deze klasse
     * er wordt eerst afgetrokken, daarna pas afgerond
     * 
     * @param TDecimal $objSubtract
     */
    public function subtract(TDecimal $objSubtract)
    {
        $iValueThis = $this->getValueAsInt();// default
        $iValueSubtract = $objSubtract->getValueAsInt();// default
        $iHighestPrecision = $this->getDecimalPrecision(); //default
        
        //eerst precisie gelijktrekken op niveau van de hoogste precisie
        if ($this->getDecimalPrecision() >= $objSubtract->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueSubtract = $objSubtract->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objSubtract->getDecimalPrecision()); //omrekenen laagste precisie
            $iHighestPrecision = $this->getDecimalPrecision();
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objSubtract->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueSubtract = $objSubtract->getValueAsInt(); //hoogste precisie
            $iHighestPrecision = $objSubtract->getDecimalPrecision();            
        }
        
        //nu pas aftrekken
        $iSum = $iValueThis - $iValueSubtract;
        
        //nu pas setten (en dus afronden)
        $this->setValueAsInt($iSum, $iHighestPrecision); 
         
    }
    
    /**
     * the mathematical function multiply:
     * for multiplying the internal value by 
     * i.e. 2 * 3
     * 
     * LET OP!! afronding op precisie van de interne precisie van deze klasse
     * er wordt eerst vermenigvuldigd, daarna pas afgerond
     * 
     * @param TDecimal $objMultiplyBy
     */
    public function multiply(TDecimal $objMultiplyBy)
    {
        $iValueThis = $this->getValueAsInt();// default
        $iValueMultiply = $objMultiplyBy->getValueAsInt();// default
        $iHighestPrecision = $this->getDecimalPrecision(); //default
        
        //eerst precisie gelijktrekken op niveau van de hoogste precisie
        if ($this->getDecimalPrecision() >= $objMultiplyBy->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueMultiply = $objMultiplyBy->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objMultiplyBy->getDecimalPrecision()); //omrekenen laagste precisie
            $iHighestPrecision = $this->getDecimalPrecision();
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objMultiplyBy->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueMultiply = $objMultiplyBy->getValueAsInt(); //hoogste precisie
            $iHighestPrecision = $objMultiplyBy->getDecimalPrecision();
        }
        
        //nu pas vermenigvuldigen        
        $iSum = $iValueThis * $iValueMultiply;
  
        
        //nu pas setten (en dus afronden)
        $this->setValueAsInt($iSum, $iHighestPrecision * 2);  //2x de hoogste precisie omdat je 2 getallen met elkaar gaat vermenigvuldigen op de hoogste precisie
    }    

    /**
     * multiplies the internal value by the supplied INTEGER parameter
     * 
     * @param int $iMultiplyBy
     */
    public function multiplyInt($iMultiplyBy)
    {
        if (is_numeric($iMultiplyBy))
            $this->iInternalValue = ($this->iInternalValue * (int)$iMultiplyBy);
    }
        
    
    /**
     * the mathematical function divide:
     * for divide the internal value by 
     * i.e. 2 / 3
     * 
     * LET OP!! afronding op precisie van de interne precisie van deze klasse
     * er wordt eerst gedeeld, daarna pas afgerond
     * 
     * @param TDecimal $objDivideBy
     */
    public function divide(TDecimal $objDivideBy)
    {
        $iValueThis = $this->getValueAsInt();// default
        $iValueDivide = $objDivideBy->getValueAsInt();// default
        $iHighestPrecision = $this->getDecimalPrecision(); //default
        
        //eerst precisie gelijktrekken op niveau van de hoogste precisie
        if ($this->getDecimalPrecision() >= $objDivideBy->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueDivide = $objDivideBy->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objDivideBy->getDecimalPrecision()); //omrekenen laagste precisie
            $iHighestPrecision = $this->getDecimalPrecision();
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objDivideBy->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueDivide = $objDivideBy->getValueAsInt(); //hoogste precisie
            $iHighestPrecision = $objDivideBy->getDecimalPrecision();
        }
        
        //nu pas vermenigvuldigen        
        $fDivision = $iValueThis / $iValueDivide;
        
        //nu pas setten (en dus afronden)
        $this->setValueAsFloat($fDivision, $iHighestPrecision); 
    }    
    

    /**
     * divides the internal value by the supplied INTEGER parameter
     * 
     * @param int $iDivideBy
     */
    public function divideInt($iDivideBy)
    {
        if (is_numeric($iDivideBy))
            $this->iInternalValue = (int)round($this->iInternalValue / (int)$iDivideBy);
    }    
    
            
     /**
     * tests is two currency's are equal
     * 
     * the test is done on the precision level of the object with the highest precision level 
     * i.e. if $this (precision = 2) and $objEqualTo (precision = 4) then the test is done on precision 4 (not 2)
     * 
     * @param TDecimal $objEqualTo
     * @return bool (true = equal, false otherwise)
     */
    public function isEqual(TDecimal $objEqualTo)
    {
        //vergelijken met de grootste precisie van (bepalen welke klasse de grootste precisie heeft)
        if ($this->getDecimalPrecision() >= $objEqualTo->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueEqualTo = $objEqualTo->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objEqualTo->getDecimalPrecision()); //omrekenen laagste precisie
            return ($iValueThis == $iValueEqualTo);
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objEqualTo->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueEqualTo = $objEqualTo->getValueAsInt(); //hoogste precisie
            return ($iValueThis == $iValueEqualTo);
        }
        
        return false;//voor de zekerheid        
    }
    
    /**
     * tests if this currency has a higher value than $objOther
     * 
     * the test is done on the precision level of the object with the highest precision level 
     * i.e. if $this (precision = 2) and $objEqualTo (precision = 4) then the test is done on precision 4 (not 2)

     * @param TDecimal $objOther
     * @return bool 
     */    
    public function isGreaterThan(TDecimal $objOther)
    {
        //vergelijken met de grootste precisie van (bepalen welke klasse de grootste precisie heeft)
        if ($this->getDecimalPrecision() >= $objOther->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueOther = $objOther->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objOther->getDecimalPrecision()); //omrekenen laagste precisie
            return ($iValueThis > $iValueOther);
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objOther->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueOther = $objOther->getValueAsInt(); //hoogste precisie
            return ($iValueThis > $iValueOther);
        }
        
        return false;//voor de zekerheid              
    }
    
    
    /**
     * tests if this currency has a lower value than $objOther
     * 
     * the test is done on the precision level of the object with the highest precision level 
     * i.e. if $this (precision = 2) and $objEqualTo (precision = 4) then the test is done on precision 4 (not 2)

     * @param TDecimal $objOther
     * @return bool 
     */    
    public function isLessThan(TDecimal $objOther)
    {
        //vergelijken met de grootste precisie van (bepalen welke klasse de grootste precisie heeft)
        if ($this->getDecimalPrecision() >= $objOther->getDecimalPrecision()) //$this grootste precisie ?
        {
            $iValueThis = $this->getValueAsInt(); //hoogste precisie
            $iValueOther = $objOther->getValueAsInt() * $this->getMultiplyFactorDifference($this->getDecimalPrecision(), $objOther->getDecimalPrecision()); //omrekenen laagste precisie
            return ($iValueThis < $iValueOther);
        }
        else //$objEqualTo grootste precisie
        {
            $iValueThis = $this->getValueAsInt() * $this->getMultiplyFactorDifference($objOther->getDecimalPrecision(), $this->getDecimalPrecision()); //omrekenen laagste precisie
            $iValueOther = $objOther->getValueAsInt(); //hoogste precisie
            return ($iValueThis < $iValueOther);
        }
        
        return false;//voor de zekerheid              
    }
    
    
    /**
     * returns the multiply factor to balance out the difference for the second value
     * to match the basevalue's precision
     * 
     * for example, when
     * basevalue = 12345678 and $iBaseDecimalPrecision = 4 (means: 1234,5678)
     * value2 = 90123456 and precision2 = 2 (means: 901234,56)
     * 
     * this function returns 100, because you have to multiply value2 by 100 to equal the base precision
     * 
     * if $iDecimalPrecision2 > $iBaseDecimalPrecision this function returns 0
     * 
     * @param int $iBaseDecimalPrecision
     * @param int $iDecimalPrecision2
     * @return int
     */
    private function getMultiplyFactorDifference($iBaseDecimalPrecision, $iDecimalPrecision2)
    {
        
        $iDiffInDecimalPrecisionMultiplyFactor = (int)round($this->getMultiplyFactor($iBaseDecimalPrecision) / $this->getMultiplyFactor($iDecimalPrecision2));//verschil tussen decimale precisie van de interne precisie en die van deze functie
        return $iDiffInDecimalPrecisionMultiplyFactor;        
    }    
    
    /**
     * what is the multiply factor of the actual float value to get the  
     * internal integer
     * 
     * i.e. with 4 decimals ($iDecimalPrecision=4) the internal value 123456 means 12.3456
     * this function returns 10000
     * 
     * @param int $iDecimalPrecision
     */
    public function getMultiplyFactor($iDecimalPrecision)
    {
       return pow(10, $iDecimalPrecision);
    }

  
    /**
     * return new TCurrency object including VAT
     * (assuming current value is excluding VAT)
     * 
     * @param TDecimal $objVATPercentage
     * @return TDecimal 
     */
    function getIncludingVAT(TDecimal $objVATPercentage)
    {
        $objExclBTW = clone $this;
        $objVatPercentageCopy = clone $objVATPercentage;
        
        $objExclBTW->divideInt(100);
        $objVatPercentageCopy->addInt(100);
        
        $objExclBTW->multiply($objVatPercentageCopy);
                
        return $objExclBTW;
    }    
    

    /**
     * return new TCurrency object excluding VAT
     * (assuming current amount is including VAT)
     * 
     * @param TDecimal $objVATPercentage
     * @return TDecimal 
     */
    function getExcludingVAT(TDecimal $objVATPercentage)
    {
        $objVatPercentageCopy = clone $objVATPercentage;
        $objInclBTW = clone $this;
          
        $objVatPercentageCopy->addInt(100);
        $objInclBTW->divide($objVatPercentageCopy);
          
        $objInclBTW->multiplyInt(100);
          

        return $objInclBTW;
    }  
    
    
    /**
     * currency converter
     * convert this object to other currency object i.e.
     * convert Euro's to Dollars
     * For example:
     * 1 euro = 1,22 dollars, so the exchange rate is 1,22
     * 
     * @param TDecimal $objExchangeRate how many [Dollars] is 1 [Euro] ?
     * @return TDecimal 
     */
    function convertToOtherCurrency(TDecimal $objExchangeRate)
    {       
        $objOtherCurr = clone $this;
        $objOtherCurr->multiply($objExchangeRate);
        return $objOtherCurr;
    }    
}

?>
