<?php
namespace dr\classes\patterns;

use dr\classes\patterns\TKeyValuePair;

/**
 * Description of TKeyValueList
 *
 * holds a list of TKeyValuePair objects
 * 
 * 4 apr 2015: TKeyValueList klasse in aparte file (ivm performance van autoloader)
  * 22 jun 2015: TVariableValueList rename naar TKeyValueList
  * 
 * @author dennis renirie
 */

class TKeyValueList extends TObjectListHash
{

	public function __construct()
	{

	}

	public function __destruct()
	{

	}

	/**
	 * You can't call this function, it will raise an exception, use addVar() instead
	 *
	 */
	final public function add($objAdd, $sKey = null, $bNullAllowed = false)
	{
		throw new Exception('TKeyValuePair add() was called instead of addVar()');
		//     		parent::add($objAdd, $sVariable);
	}


	/**
	 * add variable with a value to the list
	 *
	 * LET OP: de function call verschilt van de parent: de parameters (volgorde) zijn omgedraaid!!!
	 *
	 * @param string $sVariable
	 * @param string $sValue
	 * @param int $tpType type as defined by lib_types
	 * @param string $sComparisonOperand comparison operator for WHERE clauses in SQL statements
	 */
	public function addVar($sVariable, $sValue, $tpType = TP_STRING, $sComparisonOperand = COMPARISON_OPERATOR_EQUAL_TO)
	{
		$objAdd = new TKeyValuePair();	
		$objAdd->setValue($sValue);
		$objAdd->setType($tpType);
		$objAdd->setComparisonOperator($sComparisonOperand);

		parent::add($objAdd, $sVariable);
	}

	/**
	 * bypass the limitation of not adding an object for child classes
	 * 
	 * @param object $objAdd
	 * @param string $sVariable
	 */
	protected function addFunctionFromParentObject($objAdd, $sVariable)
	{
		parent::add($objAdd, $sVariable);
	}
	
	/**
	 * adds the content of another TKeyValueList to this one
	 *
	 * @param TKeyValueList $objOtherList
	 */
	public function addList(TKeyValueList $objOtherList)
	{
		$arrVarsOtherList = $objOtherList->getVariables();

		//        vardump($arrVarsOtherList);
		//        tracepoint('jopie');

		foreach ($arrVarsOtherList as $sVarOtherList)
		{
			$objOtherVarVal = $objOtherList->get($sVarOtherList);			
			$objNewVarVal = clone $objOtherVarVal;
			parent::add($objNewVarVal, $sVarOtherList);
		}
	}

	/**
	 * get a value from a variable
	 *
	 * @param string $sVariabele
	 * @return string
	 */
	public function getValue($sVariabele, $bKeyDoesntExistError = true)
	{
		$objVarVal = parent::get($sVariabele, $bKeyDoesntExistError);
		return $objVarVal->getValue();
	}

	/**
	 * get the TKeyValuePair Object
	 *
	 * @param string $sVariabele
	 * @return TKeyValuePair
	 */
	public function get($sVariabele, $bKeyDoesntExistError = true)
	{
		$objVarVal = parent::get($sVariabele, $bKeyDoesntExistError);
		return $objVarVal;
	}


	/**
	 * get if the variable is changes since last load
	 *
	 * @param string $sVariabele
	 * @param bool $bKeyDoesntExistError
	 * @return bool
	 */
	public function getDirty($sVariabele, $bKeyDoesntExistError = true)
	{
		$objVarVal = parent::get($sVariabele, $bKeyDoesntExistError);
		return $objVarVal->getDirty();
	}

	/**
	 * set a value in a variable
	 *
	 * @param string $sVariabele
	 * @param string $sValue
	 */
	public function set($sVariabele, $sValue, $tpType = TP_STRING)
	{
		$objVarVal = parent::get($sVariabele, false);
		if ($objVarVal == null) //als niet bestaat: toevoegen
			$this->addVar($sVariabele, $sValue, $tpType);
		else //als wel bestaat: wijzigen
		{
			$objVarVal->setValue($sValue);
			$objVarVal->setType($tpType);
		}
	}


	/**
	 * return the variables in a array present in this list
	 *
	 * @return array
	 */
	public function getVariables()
	{
		return parent::getKeys();
	}
}
