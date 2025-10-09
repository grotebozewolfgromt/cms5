<?php
namespace dr\classes\files;

use dr\classes\patterns\TTable;
use dr\classes\patterns\TTableRow;
/**
 * Description of TCSV
 * 
 * Deze klasse wordt gebruikt voor het lezen en schrijven en CSV files
 * 
 * @todo er zijn CSV bestanden waar strings  wel een enclosed character, en getallen niet
 * 
 * 8 juli 2012: created
 * 8 juli 2012: TTable header verplaatst van TCSV naar TTable
 * 17 juli 2012: TCSV->save() filtering delimiter characters from string
 * 
 * @author drenirie
 */


class TCSV extends TTable
{
    private $sValueDelimiter = ','; //values separator by
    private $sValuesEnclosedBy = '"'; //values enclosed by
    private $bFirstLineIsHeader = false;

    const MSEXCELSETTINGS = 1;
    const APPLENUMBERSSETTINGS = 2;
    const INGSETTINGSKOMMAGESCHEIDEN = 3;
    const RABOBANKKOMMAGESCHEIDEN = 4; //Let op: strings hebben wel een enclosed character, getallen niet
    
    /**
     * set some default settings of applications
     * The constants to set it with are declared as 'const' in this class
     * 
     * @param type $iSettings 
     */
    public function setSettings($iSettings)
    {
        switch ($iSettings) 
        {
            case TCSV::MSEXCELSETTINGS:
                $this->setDelimiter(';');
                $this->setEncloseChars('');
                $this->setFirstLineIsHeader(true);
                break;
            case TCSV::APPLENUMBERSSETTINGS:
                $this->setDelimiter(';');
                $this->setEncloseChars('');
                $this->setFirstLineIsHeader(true);
                break;
            case TCSV::INGSETTINGSKOMMAGESCHEIDEN:
                $this->setDelimiter(',');
                $this->setEncloseChars('"');
                $this->setFirstLineIsHeader(true);
                break;
            case TCSV::RABOBANKKOMMAGESCHEIDEN:
                $this->setDelimiter(',');
                $this->setEncloseChars('');
                $this->setFirstLineIsHeader(true);
                break;            
        }
    }
    
    /**
     * get field separator, for example ,
     * in line: value1, value2, value3
     * 
     * @return string
     */
    public function getDelimiter()
    {
        return $this->sValueDelimiter;
    }
    
    /**
     * set field separator, for example ,
     * in line: value1, value2, value3
     * 
     * @param type $sSeparator 
     */
    public function setDelimiter($sSeparator)
    {
        $this->sValueDelimiter = $sSeparator;
    }
    
    
    /**
     * checks of Enclosed characters are present
     * @return boolean 
     */
    private function getIsEnclosedCharacterPresent()
    {
        $bPresent = true;
        
        if ($this->getEncloseChars() == null)
            $bPresent = false;
        
        if (strlen($this->getEncloseChars()) == 0)
            $bPresent = false;            
        
        return $bPresent;
    }
    
    /**
     * set string of wich values are enclosed by, such as "
     * in line: "value1","value2","value3"
     * 
     * @return string 
     */
    public function getEncloseChars()
    {
        return $this->sValuesEnclosedBy;
    }
    
    /**
     * set string of wich values are enclosed by, such as "
     * in line: "value1","value2","value3"
     * 
     * @param string $sEncloseValuesBy 
     */
    public function setEncloseChars($sEncloseValuesBy)
    {
        $this->sValuesEnclosedBy = $sEncloseValuesBy;
    }
    
    /**
     * returns if the first line of the CSV file is the header
     * 
     * @return boolean
     */
    public function getFirstLineIsHeader()
    {
        return $this->bFirstLineIsHeader;
    }
    
    /**
     * sets if the first line of the CSV file is the header
     * 
     * @param boolean $bHeader 
     */
    public function setFirstLineIsHeader($bHeader)
    {
        if (is_bool($bHeader))
            $this->bFirstLineIsHeader = $bHeader;
        else
            logError(__CLASS__.': '.__FUNCTION__.': '.__LINE__, 'setFirstLineIsHeader(): bHeader is not a boolean', $this);
    }
    
    /**
     * load contents from csv file
     *
     * 
     * @param string $sFileName
     * @return boolean load successfull ?
     */
    public function loadFromFile($sFileName)
    {
        if (is_file($sFileName))
        {
            $sFileContents = loadFromFileString($sFileName);
            return $this->loadFromString($sFileContents);
        }
        else
            false;
    }
    
    
    /** 
     * internal function for scanning a line from a CSV file and return the values as an array 
     */
    
    private function lineToArray($sLine)
    {
        $arrResult = array();
        $bRecord = false;
        $sCurrValue = '';
  
        //als naar omsluit karakters moet zoeken
        if ($this->getIsEnclosedCharacterPresent())
        { 
        
            //alle karakters langslopen
            for ($iTeller = 0; $iTeller < strlen($sLine); $iTeller ++)
            {
                $sCurrChar = substr($sLine, $iTeller, 1);

                
                //als een 'omsluit' karakter tegen gekomen dan waarde 'opnemen' (of juist niet)
                if ($sCurrChar == $this->getEncloseChars())
                    $bRecord = !$bRecord;
                
                //soms hebben alleen strings een enclosed-character en getallen niet, daarom een record forceren
                if (($sCurrChar != $this->getEncloseChars()) && ($bRecord == false) && ($sCurrChar != $this->getDelimiter()))
                {
                    if ($sCurrChar == $this->getDelimiter())
                    {
                        $arrResult[] = $sCurrValue;    
                        $sCurrValue = '';
                    }
                    else //geforceerd 'opnemen'
                        $sCurrValue.=$sCurrChar;    
                }

                
                //als moet opnemen dan aan huidige waarde toevoegen
                if (($bRecord) && ($sCurrChar != $this->getEncloseChars())) //test op de enclosed character om te voorkomen dat ie ook het enclosed-character 'opneemt'
                    $sCurrValue.=$sCurrChar;
                
                
                
                //als delimiter tegen gekomen, dan moet het volgende waarde 'opgenomen' worden
                // je mag alleen maar een delimiter tegenkomen als er NIET opgenomen wordt
                if (($sCurrChar == $this->getDelimiter()) && (!$bRecord)) 
                {
                    $arrResult[] = $sCurrValue;    
                    $sCurrValue = '';
                }      

            }
            
            $arrResult[] = $sCurrValue; //de laatste opgenomen waarde nog even toevoegen, anders missen we de laatste kolom
            
        }
        else //als geen 'omsluit' karakters, dan volstaat een gewone simpele explode()
            $arrResult = explode($this->getDelimiter(), $sLine);
        
        
        return $arrResult;
    }
    
    /**
     * load contents from csv file
     *
     * @todo bij enclosed-characters heeft de eerste waarde de enclosed character aan het begin van zijn string, en de laatste waarde de enclose character aan het einde
     * 
     * @param string $sStringToRead
     * @return boolean load successfull ?
     */
    public function loadFromString($sStringToRead)
    {
        $arrHeader = array();
        $this->clear(); //leegmaken        
        $arrFile = strToArr($sStringToRead);//laden        
       
        
        $iLineCounter = 0;
        foreach ($arrFile as $sLine)
        {
            //eerste regel header? EN het is de eerste regel ?
            if ($this->getFirstLineIsHeader() && ($iLineCounter == 0))
            {    
                $arrHeader = $this->lineToArray($sLine);
                $this->setHeader($arrHeader);
            }
            else //eerste regel niet header?
            {
                //vardump('blaat');
                if (strlen(trim($sLine)) > 0) //lege regels filteren
                {
                
                    $objRow = new TTableRow();                

                    //uit elkaar trekken van de regel en in de rij stoppen
                    $arrValues = $this->lineToArray($sLine);   
                    
                    for($iColumnCounter = 0; $iColumnCounter < count($arrValues); $iColumnCounter++)
                    {
                        $sValue = $arrValues[$iColumnCounter];
                        
                        if (count($arrHeader) > 0)
                            $objRow->add($sValue, $arrHeader[$iColumnCounter], true);
                        else
                            $objRow->add($sValue, null, true);
                    }


                    $this->add($objRow);
                    
                }

            }

        
            $iLineCounter++;
        }
        
        
        return true;
        
    }

    
    
    
    
    /**
     * save contents to textfile
     *
     * @param string $sFileName
     * @param string $sLineEndingCharacter
     * @return boolean save sucessfull ?
     */
    public function saveToFile($sFileName, $sLineEndingCharacter = PHP_EOL)
    {
        $sContents = $this->saveToString($sLineEndingCharacter);
        
        return saveToFileString($sContents, $sFileName);
    }

    
    /**
     * returns the CSV contents of this object as a string 
     */
    public function saveToString($sLineEndingCharacter = PHP_EOL)
    {
        $arrHeader = $this->getHeader();      
        $sResult = '';
        
        
        //header maken
        if ($this->getFirstLineIsHeader())
        {
            for($iColCounter = 0; $iColCounter < count($arrHeader); $iColCounter++)
            {
                if ($iColCounter == 0)
                    $sResult .= $this->getEncloseChars().$arrHeader[$iColCounter].$this->getEncloseChars();
                else
                    $sResult .= $this->getDelimiter().$this->getEncloseChars().$arrHeader[$iColCounter].$this->getEncloseChars();
            }
            
            $sResult.=$sLineEndingCharacter;
        }
        
        
        //inhoud maken
        for ($iLineCounter = 0; $iLineCounter < $this->count(); $iLineCounter++)
        {
            /* @var objRow TTableRow */
            $objRow = $this->get($iLineCounter);
           
            
            for($iColCounter = 0; $iColCounter < $objRow->count(); $iColCounter++)
            {
             
                $sValue = $objRow->get($iColCounter,false);
                
                //filtering delimiter characters from string
                $sValue = str_replace($this->getDelimiter(), '', $sValue);
                
                
                if ($iColCounter == 0)
                    $sResult .= $this->getEncloseChars().$sValue.$this->getEncloseChars();
                else
                    $sResult .= $this->getDelimiter().$this->getEncloseChars().$sValue.$this->getEncloseChars();
            }
            $sResult.=$sLineEndingCharacter;
        }     
        
        return $sResult;
    }
            
}

?>
