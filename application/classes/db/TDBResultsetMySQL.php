<?php
namespace dr\classes\db;

use dr\classes\types\TDateTime;
use dr\classes\types\TDecimal;
use dr\classes\types\TCurrency;
use dr\classes\types\TFloat;
use dr\classes\types\TInteger;

/**
 * Description of TDBResultsetMySQL
 * 
 * specifieke vertaling voor MySQL (bijvoorbeeld floats, datums een booleans)
 * 
 * 27 sept 2012: TDBResultsetMySQL aangemaakt
 * 8 mrt 2014: TDBResultsetMySQL bugopgelost met tijd laden uit database met ongeldige datum
 * 38 jul 2014: TDBResultsetMySQL getCurrency toegeveogd
 * 6 jul 2015: TDBResultsetMySQL getDateTimeStamp() bug geplet dat altijd de huidige tijdstip werd teruggegeven
 *
 * @author drenirie
 */
class TDBResultsetMySQL extends TDBResultset
{
    //deze klasse is extreem leeg
    //dat komt omdat de gedefinieerde defaults in TDBResulset dezelfde zijn als die van MySQL
    
     /**
      * return result als boolean
      *
      * @param string $sFieldName
      * @return bool
      */
     public function getBool($sFieldName)
     {
         if ($this->iRecordPointer >= 0)
            return strToBool($this->arrData[$this->getRecordPointer()][$sFieldName]);
         else
             return null;
     }

     /**
      * return result as unix timestamp
      *
      * @param string $sFieldName
      * @return int date unix timestamp
      */
     public function getDateTimestamp($sFieldName)
     {
        if ($this->iRecordPointer >= 0)
        {
            $arrDT = date_parse_from_format('Y-m-d H:i:s' , $this->get($sFieldName));
            if ($arrDT['warning_count'] == 0)
                return mktime($arrDT['hour'], $arrDT['minute'], $arrDT['second'], $arrDT['month'], $arrDT['day'], $arrDT['year']);                                       
            else //invalid timestamp
                return 0;            
        }
        else
            return null;
     }    
     
     /**
      * return result as unix timestamp
      *
      * @param string $sFieldName
      * @return TDateTime datetime object
      */
     public function getDateTime($sFieldName)
     {
        $iTimestamp = $this->getDateTimestamp($sFieldName);
        return new TDateTime($iTimestamp);
     }    
     
     /**
      * return result as currency object 
      * @param string $sFieldName
      * @return TCurrency|null
      */
    //  public function getCurrency($sFieldName)
    //  {
    //     if ($this->iRecordPointer >= 0)  
    //     {
    //         $sValue = $this->arrData[$this->getRecordPointer()][$sFieldName];
    //         return new TCurrency($sValue);
    //     }
    //     else
    //         return null;         
    //  }     
    
}



?>
