<?php
namespace dr\classes\dom\tag;

/* 
 * additional classes for generating a HTML table
 *
 * TODO: als geen inhoud in TD, dan &nbsp toevoegen
 *
 * 8 juli 2012: th toegevoegd voor tableheaders
 * 
 * @author Dennis Renirie
 */


/**
 * <table></table>
 *
 *  Example creating a table:

 *  $objTable = new table();
 *  $objTable->addRow(array(1,2,3,4,5));
 *  $objTable->addRow(array(6,7,8,9,10));
 *  echo $objTable->getAsText;
 *
 */
class Table extends HTMLTag
{
    public function __construct($objParentNode = null)
    {
        parent::__construct($objParentNode);
        $this->setTagName('table');

        //according to the w3c standards: every table tag needs to have a tbody,
        //so we create that automatically for you:
        $objTBODY = new tbody($objParentNode);
        $this->appendChild($objTBODY);
    }

    /**
     * returns the body object of the html tag
     *
     * @return tbody tbody object
     */
    public function getTbody()
    {
        return $this->getChildNode(0);
    }
    
    /**
     * adds a row to the table
     *
     * @param array $arrColumnValues array met waardes voor in de rij
     * @param string $sCSSClass css klasse
     */
    public function addRow($arrCols, $sCSSClass = '')
    {
        $objBody = $this->getTbody();
        $objBody->addRow($arrCols, $sCSSClass);
    }

}


?>
