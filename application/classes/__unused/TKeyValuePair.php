<?php
namespace dr\classes\patterns;


/**
 * Description of TKeyValuePair
 *
 * sometimes you need to have a variable and a value, like options in a database
 * or in an ini file
 *
 * sept/okt 2009
 * 
 * 
 * 8 juli 2012: TVariableValueList->add() aangepast aan de gewijzigde parameters van de parent. de parameters van deze add() functie blijven ongewijzigd!
 * 11 feb 2013: TKeyValuePair type toegevoegd
 * 11 feb 2013: TVariableValueList->get() rename naar TVariableValueList->getValue()
 * 26 mrt 2013: TKeyValuePair: ondersteuning voor vergelijkingsoperatoren
 * 26 mrt 2013: TVariableValueList: ondersteuning voor vergelijkingsoperatoren
 * 7 aug 2014: TVariableValueList: addList() toegevoegd
 * 7 aug 2014: TVariableValueList: had een loze interne objectlist in zich, deze is verwijderd
 * 21 jan 2015: TVariableValueList: addVar() vervangt add(), ivm php strictheidsregels. add() geeft nu een exception
 * 4 apr 2015: TVariableValueList klasse in aparte file (ivm performance van autoloader)
 * 22 jun 2015: TVariableValue rename naar TKeyValuePair
 * 22 jun 2015: TKeyValuePair extends niet meer TObject (snelheidswinst)
 *
 * @author dennis renirie
 */

class TKeyValuePair
{
    private $bDirty = false;
    private $bNew = true;
    private $sValue = '';
    private $tpType = TP_STRING;//default string
    private $sComparisonOperand = COMPARISON_OPERATOR_EQUAL_TO; //vergelijkings operator: dit kan zijn '=', '>', '<=' etc  voor WHERE clausules in SQL statements

    /**
     * when the value is an object, we have to clone that too...
     */
    public function __clone()
    {    	
    		if (is_object($this->sValue))    			
    			$this->sValue = clone $this->sValue;
    }
    
    
    public function getDirty()
    {
        return $this->bDirty;
    }

    public function setDirty($bDirty)
    {
        $this->bDirty = $bDirty;
    }

    public function getNew()
    {
        return $this->bNew;
    }

    public function setNew($bNew)
    {
        $this->bNew = $bNew;
    }

    public function getValue()
    {
        return $this->sValue;
    }

    public function setValue($sValue)
    {
        $this->sValue = $sValue;
        $this->setDirty(true);
    }
    
    /**
     * returns type of variable
     * 
     * @return int as defined in lib_types
     */
    public function getType()
    {
        return $this->tpType;
    }
    
    public function setType($tpType)
    {
        if (is_int($tpType))
        {
            $this->tpType = $tpType;
        }
    }

    /**
     * voor WHERE clausules in SQL statements
     * @param string $sOperand
     */
    public function setComparisonOperator($sOperand = COMPARISON_OPERATOR_EQUAL_TO)
    {
        $this->sComparisonOperand = $sOperand;
    }
    
    /**
     * voor WHERE clausules in SQL statements
     * @return string
     */
    public function getComparisonOperator()
    {
        return $this->sComparisonOperand;
    }
}

?>
