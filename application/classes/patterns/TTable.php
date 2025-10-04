<?php
namespace dr\classes\patterns;

use dr\classes\dom\tag\table;
/**
 * Description of TTable
 * 
 * Deze klasse is een generieke manier voor het opslaan van 2d data, zoals een tabel
 *
 * 8 juli 2012: TTable aangemaakt
 * 8 juli 2012: TTable getAsHTML() toegevoegd
 * 8 juli 2012: TTable header verplaatst van TCSV naar TTable
 * 21 jan 2014: TTable add() verwijdert omdat deze gelijk moet zijn aan de parent (php strictheidsregels), dus het afdwingen van  klasse kan niet
 * 4 apr 2015: TTable optimalisatie door count buiten loop te doen
 * 
 * @author drenirie
 */

class TTable extends TObjectList
{
    private $arrHeader = array();
    

    /**
     * returns an array with the fields of the CSV file
     * 
     * @return array of strings with header 
     */
    protected function getHeader()
    {
        return $this->arrHeader;
    }
    
    /**
     * get the name of the column
     * 
     * @param int $iColNr 
     */
    public function getColumnName($iColNr)
    {
        return $this->arrHeader[$iColNr];
    }
    
    /**
     * sets the header of the CSV file
     * 
     * @param array $arrHeader 1d array of strings
     */
    public function setHeader($arrHeader)
    {
        unset($this->arrHeader);
        $this->arrHeader = $arrHeader;
    }    
    
    /**
     * generate a html table object
     * 
     * @return table
     */
    public function getAsHTML()
    {
        $objHTMLTable = new table();
        $arrRowObjects = $this->getArrayObjects();
        $arrHeader = $this->getHeader();

       
        //als er een header is, dan deze ook weergeven
        if (count($arrHeader) > 0)
        {
            /* @var $objRow TTableRow */
            $objHTMLRow = new tr();                        
                   
            //kolommen nagaan
            $iCount = count($arrHeader);
            for ($iColumnCounter = 0; $iColumnCounter < $iCount; $iColumnCounter++)
            {
                $objHTMLCol = new th();
                               
                $objHTMLCol->appendChildTextNode($arrHeader[$iColumnCounter]); //waarde opvragen aan de hand van de kolomnaam
                    
                $objHTMLRow->appendChild($objHTMLCol);                
            }

            $objHTMLTable->getTbody()->appendChild($objHTMLRow);     
        }

        
        //rijen nagaan
        foreach ($arrRowObjects as $objRow)
        {
            /* @var $objRow TTableRow */
            $objHTMLRow = new tr();                        
                   
            //kolommen nagaan
            $iCount = count($objRow->count());
            for ($iColumnCounter = 0; $iColumnCounter < $iCount; $iColumnCounter++)
            {
                $objHTMLCol = new td();
                
                $objHTMLCol->appendChildTextNode($objRow->get($iColumnCounter)); //waarde opvragen aan de hand van de kolomnaam
                    
                $objHTMLRow->appendChild($objHTMLCol);                
            }

            $objHTMLTable->getTbody()->appendChild($objHTMLRow);            
        }        

        return $objHTMLTable;
    }
    
    
    /**
     * gets a tablerow
     * 
     * @param TTableRow $iIndex 
     */
    public function get($iIndex)
    {
        return parent::get($iIndex);
    }
    
}


?>
