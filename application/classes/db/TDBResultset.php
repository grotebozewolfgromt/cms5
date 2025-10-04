<?php
namespace dr\classes\db;

use dr\classes\patterns\TObject;
use dr\classes\dom\tag\table;

use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TCurrency;
use dr\classes\types\TFloat;
use dr\classes\types\TInteger;

/**
 * Description of TDBResultset
 *
 * @author Dennis Renirie
 * 
 * voorbeeld gebruik resultset: 
 *     
 * $objQuery = new TDBPreparedStatementMySQL($objApplication->getDatabaseConnectionObject());
   $objQuery->prepare('SELECT * FROM tblCMSUsers WHERE i_id < ?');  
   $objQuery->bind(5);
   $objRst = $objQuery->executeQuery();
   while ($objRst->next())
        echo $objRst->get('s_username').'<br>';
 * 
 *
 * Als een query op een database resultaten terug geeft worden deze aangeboden
 * in een TDBResultset
 * Door de resultset kun je heen lopen, importeren van verschillende
 * (data/bestands) formaten en ook weer exporteren naar verschillende bestandformaten
 * 
 * 27 sept 2012: TDBResultset functienamen gerenamed getAsInt->getInt getAsBool->getBool etc.
 * 17 okt 2012: TDBResultset: getDateTime() aanpassing, werkte met de oude datum conversie functie
 * 17 okt 2012: TDBResultset: getParentQueryObject() verwijderd (gaat straks via $objOwnerObject van TObject)
 * 17 okt 2012: TDBResultset: constructor verwijderd waardoor deze van TObject gebruikt wordt
 * 12 feb 2013: TDBResultset: expirimenteren met 'while (next) { }' ipv (!eof) { movenext() }
 * 12 feb 2013: TDBResultset: oude recordpointer verdwenen,  moveprevious() movenext() verwijderd --> dit voorkomt bugs (omdat je iets kan vergeten) en programmeert sneller
 * 12 feb 2013: TDBResultset: numberofrows functies en variabele verwijderd. ik had geen idee wat het nut van deze fucnties was
 * 28 jul 2014: TDBResultset: get() expanded with columntypes
 * 28 jul 2014: TDBResultset: getColumnNames() toegevoegd
 * 28 jul 2014: TDBResultset: getDecimal()
 * 28 jul 2014: TDBResultset: get() uitgebreid met CT_DECIMAL en CT_CURRENCY
 * 
 * 
 */
abstract class TDBResultset
{
    protected $arrData = null; //array met data --> $arrData[irecordpointer][databasetabel-veld]; $arrData[0] is het eerste element!
    protected $iRecordPointer = -1; 
    protected $arrColumnNames = null;//array met namen van kolommen (strings) - index is hetzelfde als die van arrayData
    protected $arrColumnTypes = null;//array met typen kolommen integers die bijvoorbeeld CT_VARCHAR voorstellen  - index is hetzelfde als die van arrayData
    //private $iNumberOfRows = 0;//12 feb 2013 verwijderd. het was mij niet duidelijk wat deze variabele doen

    public function __clone()
    {
    		$this->arrData = array_clone($this->arrData);
    }
    
    /**
     * add a fieldname to the internal array of fieldnames.
     * @param type $sName
     */
    public function addColumnName($sName)
    {
        $this->arrColumnNames[] = $sName;
    }
    
    /**
     * add a type to the internal array of fieldtypes
     * @param int $iType (like CT_VARCHAR etc)
     */
    public function addColumnType($iType)
    {
        $this->arrColumnTypes[] = $iType;
    }
    
    /**
     * move recordpointer and checks eof
     * 
     * new way with while (next) { }
     * 
     * @return bool false when eof, else true
     */
    public function next()
    {
        $this->iRecordPointer++;
        return ($this->iRecordPointer < $this->getRowCount());
    }
    
    /**
     * het aantal records geretourneerd in resultset
     *
     * @return int number of rows
     */     
    //12 feb 2013 verwijderd. het was mij niet duidelijk wat deze functies doen
//     public function setNumberOfRows($iRows)
//     {
//         $this->iNumberOfRows = $iRows;
//     }
//     
//     public function getNumberOfRows()
//     {
//         return $this->iNumberOfRows;
//     }
     
    /**
     * set internal data
     * 
     * @param array $arrNewData
     */
    public function setData($arrNewData)
    {
        $this->arrData = $arrNewData;
    }

    /**
     * get internal data
     * 
     * @return array (internal data format)
     */
    public function getData()
    {
        return $this->arrData;
    }
    
    /**
     * fetch all internal data
     * 
     * @return array(internal data format)
     */
    public function fetchAll()
    {
        return $this->arrData;
    }

    /**
     * remove all data from resultset
     */
    public function clear()
    {
        unset($this->arrData);
        $this->arrData = null;
    }                      
        
    /**
     * count number of rows in resultset
     */
    public function count()
    {
        if ($this->arrData)
            return count($this->arrData);
        else
            return 0;
    }
    
    /**
     * counts the columns in the resultset
     * 
     * @return int
     */
    public function countCols()
    {
        if (count($this->arrData) > 0)
            return count($this->arrData[0]);
        else
            return 0;
    }
    
    /**
     * add an associated array to the resultset
     * 
     * @param array $arrAssocArray associated array
     */
    public function addAssoc($arrAssocArray)
    {
        $this->arrData[] = $arrAssocArray;
    }
    
    /**
     * add a Zend resultset to this resultset
     * 
     * @param string $arrAssociativeZendDBArray - de associatieve array die uit de zend adapter komt rollen
     */
    // public function addZendDbFETCH_ASSOC($arrAssociativeZendDBArray)
    // {
    //     try
    //     {
    //         $this->arrData[] = array_slice($arrAssociativeZendDBArray, 0); //de eerste record verwijderen (die is namelijk null), met dank aan Zend
    //     }
    //     catch (Exception $objException)
    //     {
    //         logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
    //     }
    // }

    /**
     * het (lomp) outputten van data uit de interne array
     */
    public function dump()
    {
        var_dump($this->arrData);
    }

    /**
     * Het weergegeven van de data in een HTML tabel
     *
     * LET OP: Heeft table klasse nodig
     *
     * @param boolean $bShowTableHeader - tabelkop boven de tabel?
     *
     * @return string HTML code met tabelcode
     */
    public function getAsHTMLTable($bShowTableHeader = true)
    {
        try
        {
            $sHTML = '';
            
            if ($this->getRowCount() > 0)
            {
                $objTable = new table();

                //inhoud tabel
                $bFirstRow = true;
                foreach($this->arrData as $arrRow)
                {
                    //tabelkop maken
                    if ($bFirstRow && $bShowTableHeader)
                    {
                        $objTable->addRow(array_keys($arrRow));
                        $bFirstRow = false;
                    }

                    //cellen vullen
                    $objTable->addRow($arrRow);
                }
                
                $sHTML = $objTable->renderHTMLNode();
            }

        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
            return '';
        }
        
        return $sHTML;
    }

    /**
     *
     * @param boolean $bFirstLineIsHeader - is de eerste regel de kop ? (de veldnamen worden in eerste regel weergegeven)
     * @param string $sValueDelimiter - door welke string worden de waardes gescheiden -> let op deze worden gefilterd uit de velden om problemen te voorkomen
     */
    public function getAsCSV($bFirstLineIsHeader = true, $sValueDelimiter = ';')
    {
        $sCSV = '';

        try
        {
            if ($this->getRowCount() > 0)
            {
                //inhoud tabel
                $bFirstRow = true;
                foreach($this->arrData as $arrRow)
                {
                    //tabelkop maken
                    if ($bFirstRow && $bFirstLineIsHeader)
                    {
                        $bFirstCell = true;
                        foreach(array_keys($arrRow) as $sKey)
                        {
                            if (!$bFirstCell) //bij eerste cell geen komma ervoor
                                $sCSV.= $sValueDelimiter;

                            $sCSV.= str_replace($sValueDelimiter, '', $sKey);

                            $bFirstCell = false;
                        }
                        $bFirstRow = false;
                        $sCSV.= "\n";
                    }

                    //cellen vullen
                    $bFirstCell = true;
                    foreach($arrRow as $sCell)
                    {
                        if (!$bFirstCell) //bij eerste cell geen komma ervoor
                            $sCSV.= $sValueDelimiter;

                        $sCSV.= str_replace($sValueDelimiter, '', $sCell);

                        $bFirstCell = false;
                    }
                    $sCSV.= "\n";
                }//einde foreach row
            }//einde if rowcount > 0
        }
        catch (Exception $objException)
        {
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, $objException, $this);
        }
        
        return $sCSV;

    }

    
    /**
     *
     * @return int hoeveelheid records er in de tabel staan
     */
    public function getRowCount()
    {
        if ($this->arrData)
            return count($this->arrData);
        else 
            return 0;
    }

    
    /**
     * determines whether the recordpointer points at the last record
     */
    public function eof()
    {
        return ( $this->getRecordPointer() >= ($this->getRowCount()-1) );
    }

    /**
     * set recordpointer to $iRecord
     * @return boolean - is het setten gelukt ?
     * @param int $iRecord - recordnummer waar pointer naar moet verwijzen
     */
     public function setRecordPointer($iRecord)
     {
         if (($iRecord <= $this->getRowCount()) && ($iRecord > -2))
         {
            $this->iRecordPointer = $iRecord;
            return true;
         }
         else
            return false;
     }

     /**
      * get recordpointer
      * @return int recordpointer wich points to current record
      */
     protected function getRecordPointer()
     {
         return $this->iRecordPointer;
     }

     /**
      * resets the record pointer
      */
     public function resetRecordPointer()
     {
         $this->setRecordPointer(-1);
     }

     /**
      * het verkrijgen van een veld in de huidige record
      * 
      * 
      * @param string $sFieldName
      * @param int $iColumnType columntype constants CT_VARCHAR, CT_INT etc
      * @return mixed (null if recordpointer is invalid)
      */
     public function get($sFieldName, $iColumnType = CT_VARCHAR)
     {
        if ($this->iRecordPointer >= 0)
        {
            //type aware get
            switch ($iColumnType)
            {
                case CT_BOOL:
                        return $this->getBool($sFieldName);
                    break;
                case CT_DATETIME:
                        return $this->getDateTime($sFieldName);
                    break;     
                case CT_DOUBLE:
                        return $this->getFloat($sFieldName);
                    break;  
                case CT_FLOAT:
                        return $this->getFloat($sFieldName);                   
                    break;  
                case CT_INTEGER32:
                        return $this->getInt($sFieldName);
                    break;                         
                case CT_INTEGER64:
                        return $this->getInt($sFieldName);
                    break;                         
                case CT_DECIMAL:
                        return $this->getDecimal($sFieldName);
                    break;                         
                case CT_CURRENCY:
                        return $this->getDecimal($sFieldName);
                    break;                         
                case CT_IPADDRESS:
                        return inet_ntop($this->arrData[$this->getRecordPointer()][$sFieldName]);
                    break;                         
                case CT_HEX:
                case CT_COLOR:
                        return bin2hex($this->arrData[$this->getRecordPointer()][$sFieldName]);
                    break;                         
                default: //CT_VARCHAR, CT_BLOB, CT_BINARY etc
                    return $this->arrData[$this->getRecordPointer()][$sFieldName];

            }

            
        }
        else
            return null; 
     }

     public function getInt($sFieldName)
     {
         if ($this->iRecordPointer >= 0)
            return (int)$this->arrData[$this->getRecordPointer()][$sFieldName];
         else
            return null;
     }

     public function getFloat($sFieldName)
     {
         if ($this->iRecordPointer >= 0)
            return (double)$this->arrData[$this->getRecordPointer()][$sFieldName];
         else
            return null;
     }

    
     /**
      * return a Binairy Large Object
      * 
      * @param type $sFieldName
      * @return string
      */
     public function getBLOB($sFieldName)
     {
        if ($this->iRecordPointer >= 0)         
            return $this->get($sFieldName);
        else
            return null;
     }

     /**
      * return a IPv4 or IPv6 ip address in human readable form
      * IP addresses are internally stored in database as 16 byte binary data
      * 
      * @param type $sFieldName
      * @return string
      */     
    //  public function getIPAddress($sFieldName)
    //  {
    //     if ($this->iRecordPointer >= 0)         
    //         return inet_ntop($this->get($sFieldName));
    //     else
    //         return null;
    //  }

     /**
      * return a TDecimal object
      * 
      * @param type $sFieldName
      * @return TDecimal
      */        
     public function getDecimal($sFieldName)
     {
        if ($this->iRecordPointer >= 0)  
        {
            $sValue = $this->arrData[$this->getRecordPointer()][$sFieldName];
            return new TDecimal($sValue);
        }
        else
            return null;         
     }

    
     
     /**
      * returns the field names of the resultset
      * @return array/null (null if resultset is empty/recordpointer invalid)
      */
     public function getColumnNames()
     {
         return $this->arrColumnNames;
     }
     
     public function getColumnTypes()
     {
         return $this->arrColumnTypes;
     }
     
     abstract public function getBool($sFieldName);
     abstract public function getDateTimestamp($sFieldName);
     abstract public function getDateTime($sFieldName);

     /**
      * return result as currency object 
      * @param string $sFieldName
      * @return TCurrency|null
      */
    //  abstract public function getCurrency($sFieldName);

}
?>
