<?php
namespace dr\classes\patterns;

/**
 * Description of TStringList
 *
 * an object list for handeling strings
 * 
 * changes:
 * 29-06-2012   getString()
 * 8 juli 2012 TStringList:saveToFile() heeft niet '' als lineendingchar default maar PHP_EOL
 * 
 *
 * @author drenirie
 */
class TStringList extends TObjectList
{
    /**
     * set array of strings
     * @param  $arrStrings
     */
    public function setArrayStrings($arrStrings)
    {
        $this->setArrayObjects($arrStrings);
    }

    /**
     * return object list as array of strings
     * @return array
     */
    public function getArrayStrings()
    {
        return $this->getArrayObjects();
    }
    
    /**
     * return stringlist as 1 string, lines separated with line separator-character
     * 
     * @return string 
     */
    public function getString()
    {
        return implode(PHP_EOL, $this->getArrayStrings());
    }

    /**
     * remove string $sString from the list
     *
     * @param string $sString
     */
    public function removeByString($sString)
    {
        return $this->removeByObject($sString);
    }

    /**
     * remove function 'removeByObject' by making it private
     *
     * @param object $objObject
     */
/*    private function removeByObject($objObject)
    {
        return parent::removeByObject($objObject);
    }
*/
    /**
     * remove function 'getArrayObjects' by making it private
     *
     * @return boolean
     */
/*    private function getArrayObjects()
    {
        return parent::getArrayObjects();
    }
*/
    /**
     * remove function 'checkInstanceOfAllObjects' by making it private
     *
     * @param <type> $sClassName
     * @param <type> $bShowMessage
     * @return <type>
     */
    /*private function checkInstanceOfAllObjects($sClassName, $bShowMessage = false)
    {
        return parent::checkInstanceOfAllObjects($sClassName, $bShowMessage);
    }
*/

   /**
     * load stringlist (the items of the stringlist, NOT the serialized object itself) from textfile
     *
     * @param string $sFileName
     * @return boolean
     */
    public function loadFromFile($sFileName)
    {
        $arrContents = loadFromFile($sFileName);

        if ($arrContents == false)
        {
            return false;
        }
        else
        {
            $this->setArrayStrings($arrContents);
            return true;
        }

    }

    /**
     * save stringlist (the items of the stringlist, NOT the object serialized itself) to textfile
     *
     * @param string $sFileName
     * @param string $sLineEndingCharacter - if '' then OS default will be assumed
     * @param int $iChmod if -1, nog chmod action is performed
     * @return boolean
     */
    public function saveToFile($sFileName, $sLineEndingCharacter = PHP_EOL, $iChmod = -1)
    {
        $arrContents = $this->getArrayStrings();

        if ($sLineEndingCharacter == '')
            $sLineEndingCharacter = PHP_EOL;

        return saveToFile($arrContents, $sFileName, $sLineEndingCharacter, $iChmod);
    }

    /**
     * add a string to the list
     * 
     * @param string $sString 
     */
//    public function add($sString)
//    {
//        parent::add($sString, true);
//    }
}
?>
