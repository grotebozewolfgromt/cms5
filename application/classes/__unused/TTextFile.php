<?php
namespace dr\classes\files;

use dr\classes\patterns\TObject;
use dr\classes\patterns\TStringList;

/**
 * Description of TTextFile
 *
 *
 * @author drenirie
 */
class TTextFile
{
    private $objStringList = null;

    public function __construct()
    {
        $this->objStringList = new TStringList();
    }

    /**
     * get contents from file
     * 
     * @return TStringlist
     */
    public function getContents()
    {
        return $this->objStringList;
    }

    /**
     * load contents from textfile
     *
     * @param string $sFileName
     * @return boolean load successfull ?
     */
    public function loadFromFile($sFileName)
    {
        return $this->objStringList->loadFromFile($sFileName);
    }

    /**
     * save contents to textfile
     *
     * @param string $sFileName
     * @param string $sLineEndingCharacter
     * @param $iChmod unix style change access mode code: 777 all access, if -1 cmod will not be performed
     * @return boolean save sucessfull ?
     */
    public function saveToFile($sFileName, $sLineEndingCharacter = '', $iChmod = -1)
    {
        return $this->objStringList->saveToFile($sFileName, $sLineEndingCharacter, $iChmod);
    }
}
?>
