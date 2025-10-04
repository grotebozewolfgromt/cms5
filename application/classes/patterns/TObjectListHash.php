<?php
namespace dr\classes\patterns;

/**
 * Description of TObjectListHash - object list driven by string indexes using the php hash algorithm for arrays
 *
 * Deze klasse stelt een lijst met objecten voor, die je kunt benaderen door ze
 * bij string-index aan te roepen middels de get() functie.
 * De intentie was om een gelinkte lijst te programmeren.
 * Omdat delete en insert acties vanwege de webscripting achtergrond waarschijnlijk
 * maar sporadisch voorkomen en index aanroepen vermoedelijk des te meer
 * is gekozen om voor de interne opslagstructuur voor arrays te kiezen.
 *
 * 8 juli 2012: TObjectListHash->add() parameters swapped, zodat ik de key een default waarde kan geven
 * 8 juli 2012: TObjectListHash: toegevoegd: setArrayObjects() en getArrayObjects
 * 8 juli 2012: TObjectListHash: unset($this->arrObjects); toegevoegd aan setArrayObjects();
 *
 * @TODO: ownsObjects
 *
 * @author Dennis Renirie
 */
class TObjectListHash
{
    private $arrObjects = array();

    public function TObjectlist()
    {
       //constructor
    }

    public function  __destruct()
    {   	
        unset($this->arrObjects);
    }
    
    public function __clone()
    {    	
    		$this->arrObjects = array_clone($this->arrObjects);
    }

    /**
     * empty the object list
     */
    public function clear()
    {
        unset($this->arrObjects);
        $this->arrObjects = array();
    }

    /**
     * returns the number of elements in the object list
     *
     * @return int Count
     */
    public function count()
    {
        return count($this->arrObjects);
    }

    /**
     * add object to object list by using the key
     * @param string $sKey
     * @param object $objAdd
     */
    public function add($objAdd, $sKey = null, $bNullAllowed = false)
    {
        try
        {
            if (($objAdd == null) && (!$bNullAllowed))
            {
                throw new \Exception('add(): met meegegeven object om toe te voegen is null');
            }
            else
            {
                if ($sKey != null)
                    $this->arrObjects[$sKey] = $objAdd;
                else
                    $this->arrObjects[] = $objAdd;
                
            }
                
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
        }
    }

    /**
     * Verwijder een element uit de objectenlijst
     *
     * @param string $sKey
     * @return bool removing succesfull ?
     */
    public function remove($sKey)
    {
        try
        {
            if ($this->checkIfIndexExceedsBounds($sKey, true))
            {
                unset($this->arrObjects[$sKey]);

                return true;
            }
            else
                return false;


        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }
    }

    /**
     * bogus functie tbv duidelijkheid. roept remove() aan.
     * @param string $sKey
     */
    public function delete($sKey)
    {
        return $this->remove($sKey);
    }

    /**
     * checken of index binnen de grenzen ligt van de object list
     *
     * @param int $iIndex index in object list
     * @return bool true = ok, false = buiten grenzen
     */
    public function checkIfIndexExceedsBounds($sKey, $bShowMessage = false)
    {
        $bResult = false; //default

        try
        {
            $bResult = array_key_exists($sKey, $this->arrObjects);

            if (($bShowMessage) && ($bResult == false))
                throw new \Exception('De opgegeven key "'.$sKey.'" valt buiten het bereik van de objectenlijst.');
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }

        return $bResult;
    }



    /**
     * controleer in een keer of alle klassen in de objecten van het type $sClassName zijn
     *
     *
     *
     * @param string $sClassName
     * @return bool alle klassen van de instantie ? ja = true, nee = false
     */
    public function checkInstanceOfAllObjects($sClassName, $bShowMessage = false)
    {
        $bResult = true; //default -> optimistisch blijven

        try
        {
            foreach($this->arrObjects as $objCurr)
            {
                if (($objCurr instanceof $sClassName) == false)
                    $bResult = false;
            }

            if (($bResult == false) && ($bShowMessage))
                throw new Exception('Niet alle objecten in objectlijst zijn van het type '.$sClassName);
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }

        return $bResult;

    }

    /**
     * dumps all elements in list to the screen
     */
    public function dump()
    {
        vardump($this->arrObjects);
    }


    /**
     * het verkrijgen van een item uit de objectlist
     *
     * @param string $sKey - index van het object dat je wilt hebben
     * @param bool $bKeyDoesntExistError - als de key niet bestaat een error geven ?
     * @return object op de keyindex
     */
    public function get($sKey, $bKeyDoesntExistError = true)
    {
        $objResult = null;

        try
        {
            if ($this->checkIfIndexExceedsBounds($sKey, $bKeyDoesntExistError))
            {
                $objResult = $this->arrObjects[$sKey];
            }
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
        }

        return $objResult;
    }

    /**
     * returns an array with the keys of the object list
     * 
     * @return array
     */
    public function getKeys()
    {
        return array_keys($this->arrObjects);
    }
    
    /**
     * returns if key exists
     * 
     * @param string $sKey
     * @return bool
     */
    public function getKeyExists($sKey)
    {
    		$arrKeys = array_keys($this->arrObjects);
    		return array_key_exist($sKey, $arrKeys);
    }
    
    /**
     * set the contents of the object list with an array
     *
     * @param array $arrObjects
     */
    public function setArrayObjects($arrObjects)
    {
        unset($this->arrObjects);
        $this->arrObjects = $arrObjects;
    }

    /**
     * return array with objects
     * @return array
     */
    public function getArrayObjects()
    {
        return $this->arrObjects;
    }    
    
    
    
//
//
//    /**
//     * remove the parameter object from the object list
//     *
//     * @param object $objObject
//     * @return bool is there at least 1 object removed ?
//     */
//    public function removeByObject($objObject)
//    {
//        $bSuccess = false;
//
//        //eerst uitvingen op welke index dit object uit hangt
//        $iIndex = indexOf($objObject);
//
//        //werke
//        if ($iIndex >= 0)
//        {
//            $bSuccess = $this->remove($iIndex);
//            $this->removeByObject($objObject); //search further recursively
//        }
//
//        return $bSuccess;
//    }
}
?>
