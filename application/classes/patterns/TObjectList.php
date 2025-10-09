<?php
namespace dr\classes\patterns;

/**
 * Description of TObjectList - object list driven by integer indexes
 *
 * Deze klasse stelt een lijst met objecten voor, die je kunt benaderen door ze
 * bij index aan te roepen middels de get() functie.
 * De intentie was om een gelinkte lijst te programmeren.
 * Omdat delete en insert acties vanwege de webscripting achtergrond waarschijnlijk
 * maar sporadisch voorkomen en index aanroepen vermoedelijk des te meer
 * is gekozen om voor de interne opslagstructuur voor arrays te kiezen.
 *
 * Het eerste element is aanwezig op indexnr 0, tweede op indexnr 1 enz.
 *
 * TODO: ownsObjects
 *
 * 16 mei 2010: contructor met init array 
 * 8 juli 2012: TObjectList: unset($this->arrObjects) toegevoegd aan setArrayObjects
 * 26 juli 2012: TObjectList: bug in insert() de index van de offset van de lower array was 1 te hoog
 * 17 juni 2013: TObjectlist: test op klasse setArrayObjects
 * 31 juli 2014: TObjectlist: indexOf() sneller doordat deze niet meer verder zoekt nadat er iets is gevonden
 * 31 juli 2014: TObjectlist: indexOfClass() toegevoegd
 * 4 apr 2015: TObjectlist: optimalisaties door count buiten loop te zetten 
 *
 * @author Dennis Renirie
 */
class TObjectList
{
    private $arrObjects = array();

    /**
     * constructor
     *
     * @param array $arrData initial array of data added to the objectlist, parameter can be null, then it will start with an empty list
     */
    public function __construct($arrObjects = null)
    {
        if ($arrObjects != null)
            $this->setArrayObjects($arrObjects);
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
     * add an object to the list
     * @param object $objAdd
     * @param bool $bNullAllowed
     */
    public function add($objAdd, $bNullAllowed = false)
    {
        try
        {
            if ($bNullAllowed)
            {
                $this->arrObjects[] = $objAdd;
            }
            else
            {
                if ($objAdd != null)
                    $this->arrObjects[] = $objAdd;
                else
                    throw new \Exception('add(): met meegegeven object om toe te voegen is null');
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
     * @param int $iIndex
     * @return bool removing succesfull ?
     */
    public function remove($iIndex)
    {
        try
        {
            if ($this->checkIfIndexExceedsBounds($iIndex, true))
            {
                //alles eronder kopieren
                $arrBegin = array();
                if ($iIndex > 0)
                    $arrBegin = array_slice($this->arrObjects, 0, $iIndex);

                //alles erboven kopieren
                $arrEnd = array();
                if ($iIndex < $this->count() -1)
                    $arrEnd = array_slice($this->arrObjects, $iIndex + 1, ($this->count()-1- $iIndex + 1));

                //alles weer bij elkaar plakken
                $this->arrObjects = array_merge($arrBegin, $arrEnd);

                return true;
            }
            else
                return false;


        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }
    }
    
    /**
     * bogus functie tbv duidelijkheid. roept remove() aan.
     * @param int $iIndex 
     */
    public function delete($iIndex)
    {
        return $this->remove($iIndex);
    }

    /**
     * checken of index binnen de grenzen ligt van de object list
     *
     * @param int $iIndex index in object list
     * @return bool true = ok, false = buiten grenzen
     */
    public function checkIfIndexExceedsBounds($iIndex, $bShowMessage = false)
    {
        $bResult = false; //default

        try
        {
            if (is_numeric($iIndex)) // extra check ivm insertion
            {
                if ($iIndex >= 0)
                {
                    if ($iIndex < $this->count())
                    {
                        $bResult = true;
                    }
                }
            }

            if (($bShowMessage) && ($bResult == false))
                throw new \Exception('De opgegeven index "'.$iIndex.'" valt buiten het bereik van de objectenlijst. Dit bereik is 0-'.$this->count());
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return false;
        }

        return $bResult;
    }

    /**
     * verwijdert het laatste item uit de object list en geeft deze terug
     *
     * @return object last object in element list
     */
    public function pop()
    {
        return array_pop($this->arrObjects);
    }

    /**
     * Voegt een item toe aan de objectenlijst
     */
    public function push($objObject)
    {
        array_push($this->arrObjects, $objObject);
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
                throw new \Exception('Niet alle objecten in objectlijst zijn van het type '.$sClassName);
        }
        catch (\Exception $objException)
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
     * @param int $iIndex - index van het object dat je wilt hebben
     * @return object op de index
     */
    public function get($iIndex)
    {
        $objResult = null;

        try
        {
            if ($this->checkIfIndexExceedsBounds($iIndex, true))
            {
                $objResult = $this->arrObjects[$iIndex];
            }
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
        }

        return $objResult;
    }

    
    /**
     * verkrijgen van laatste object in object list
     *
     * @return object - laatste object in object list
     */
    public function peek()
    {
        try
        {
            return $this->get($this->count() -1);
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return null;
        }
    }
    /**
     * invoegen van een object op een bepaalde plaats in de object list
     * de items erboven worden 1 plaats omhoog geschoven
     *
     * @param int $iIndex
     * @param object $objObject
     */
    public function insert($iIndex, $objObject)
    {
        try
        {
            //array opdelen in 2 delen: het gedeelte eronder en het gedeelte erboven
            $arrUpper = array();//default
            if ($iIndex > 0)
                $arrUpper = array_slice($this->arrObjects, 0, $iIndex);

            $arrLower = array();
            if ($iIndex < $this->count() -1)
                $arrLower = array_slice($this->arrObjects, $iIndex , ($this->count()-1- $iIndex + 1));

            $arrMiddle = array($objObject);

            //nu alles weer aan elkaar plakken
            $this->arrObjects = array_merge($arrUpper, $arrMiddle);
            $this->arrObjects = array_merge($this->arrObjects, $arrLower);
        }
        catch (\Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
        }
    }

    /**
     * object aan het begin van de objectlist toevoegen
     * door deze functie kun je de objectlist ook als queue (algoritme) gebruiken
     * op basis van FIFO (first in, first out)
     *
     * @param object $objObject
     */
    public function pushAsQueue($objObject)
    {
        $this->insert(0, $objObject);
    }

    /**
     * returns the first index of the supplied parameter object
     * (it looks for the exact same instance of class, not if its the same class)
     * 
     * @param object $objObject object to search fot
     * @return int index of the found object; returns -1 if not found
     */
    public function indexOf($objObject)
    {
        //alle objecten langslopen
        $iCount = $this->count();
        for ($iTeller = 0; $iTeller < $iCount; $iTeller++)
        {
            $objCurrObject = $this->get($iTeller);

            if ($objCurrObject == $objObject)
                return $iTeller;
        }

        return -1;
    }
    
    /**
     * returns the first index of the supplied parameter object
     * (it looks for the same class as the parameter object, not same object)
     * 
     * @param object $objClass
     * @return int index of the found object; returns -1 if not found
     */
    public function indexOfClass($objClass)
    {
        //alle objecten langslopen
    		$iCount = $this->count();
        for ($iTeller = 0; $iTeller < $iCount; $iTeller++)
        {
            $objCurrObject = $this->get($iTeller);

            if (get_class($objCurrObject) == get_class($objClass))
                return $iTeller;
        }

        return -1;
    }    

    /**
     * remove the parameter object from the object list
     *
     * @param object $objObject
     * @return bool is there at least 1 object removed ?
     */
    public function removeByObject($objObject)
    {
        $bSuccess = false;

        //eerst uitvingen op welke index dit object uit hangt
        $iIndex = $this->indexOf($objObject);

        //werke
        if ($iIndex >= 0)
        {
            $bSuccess = $this->remove($iIndex);            
            $this->removeByObject($objObject); //search further recursively
        }

        return $bSuccess;
    }

    /**
     * set the contents of the object list with an array
     *
     * @param array $arrObjects
     */
    public function setArrayObjects($arrObjects)
    {
        if (is_array($arrObjects))
        {
            unset($this->arrObjects);
            $this->arrObjects = $arrObjects;
        }
        
    }

    /**
     * return array with objects
     * @return array
     */
    public function getArrayObjects()
    {
        return $this->arrObjects;
    }
    
}

?>
